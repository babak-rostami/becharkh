<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailActiveEmail;
use App\Mail\ActiveCodeEmail;
use App\Models\Admin;
use App\Models\MongoFollowItem;
use App\Models\MongoItem;
use App\Models\MongoUser;
use App\Models\MongoWork;
use App\Models\Question;
use App\Models\User;
use App\Models\UserActivation;
use App\Models\UserMoney;
use App\Notifications\SiteEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{

    public function login()
    {
        return view('user.login');
    }

    public function loginSendAjax(Request $request)
    {
        auth('user')->attempt(
            $this->credentials($request)
        );

        $user = auth('user')->user();
        if (!isset($user)) {
            return response()->json(['message' => 'رمز عبور اشتباه است'], 403);
        }

        //******************لگر کاربر غیرفعال بود وارد نشود************************
        if ($user->status && $user->status == 0) {
            auth('user')->logout();
            return response()->json(['message' => 'اکانت شما غیر فعال شده است'], 403);
        } else {
            return response()->json(['status' => 1], 200);
        }
    }

    public function loginSend(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ], [
            'email.required' => 'لطفا ایمیل خود را وارد کنید',
            'password.required' => 'لطفا پسورد را وارد کنید',
        ]);

        auth('user')->attempt(
            $this->credentials($request)
        );

        $user = auth('user')->user();

        if (isset($user)) {
            //******************لگر کاربر غیرفعال بود وارد نشود************************
            if ($user->status == 0) {
                auth('user')->logout();
                return redirect()->route('user.login')->with('success', 'اکانت کاربری شما غیر فعال است');
            } elseif ($user->email_actived == 0) {
                return redirect()->back()->with('success', 'خوش آمدید');
            } else {
                return redirect()->back();
            }
        } else {
            return back()->with('success', 'ایمیل یا رمز عبور خود را اشتباه وارد کرده اید');
        }
    }

    protected function credentials(Request $request)
    {
        return $request->only('email', 'password');
    }

    public function register()
    {
        return view('user.register');
    }

    public function registerSendAjax(Request $request)
    {
        $checkUser = MongoUser::where('username', $request->username)->first();
        if (isset($checkUser)) {
            return response()->json(['message' => 'این نام کاربری قبلا انتخاب شده است'], 403);
        }
        $checkUser = MongoUser::where('email', $request->email)->first();
        if (isset($checkUser)) {
            return response()->json(['message' => 'این ایمیل قبلا ثبت نام کرده است'], 403);
        }

        $user = new MongoUser();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->money = 30000;
        $user->password = bcrypt($request->password);
        $user->save();

        auth('user')->attempt(
            $this->credentials($request)
        );
        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => ' کاربری جدید با نام  ' . $request->username . ' ثبت نام کرد',
                'route' => route('user.dashboard', $request->username),
            ]));
        }
        return response()->json(['success' => 1], 200);
    }

    public function registerSend(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:25',
            'username' => 'required|max:25|unique:users',
            'email' => 'required|max:50|unique:users',
            'password' => 'required|max:50',
        ], [
            'name.required' => 'لطفا نام خود را وارد کنید',
            'username.required' => 'لطفا نام کاربری خود را وارد کنید',
            'username.unique' => 'نام کاربری وارد شده قبلا انتخاب شده است',
            'email.required' => 'لطفا ایمیل خود را وارد کنید',
            'email.unique' => 'ایمیل وارد شده قبلا ثبت نام کرده است',
            'password.required' => 'لطفا پسورد را وارد کنید',
        ]);

        $user = new MongoUser();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->money = 30000;
        $user->password = bcrypt($request->password);
        $user->save();

        auth('user')->attempt(
            $this->credentials($request)
        );

        $admin = Admin::first();
        $admin->notify(new SiteEvent([
            'action' => ' کاربری جدید با نام  ' . $request->username . ' ثبت نام کرد',
            'route' => route('user.dashboard', $request->username),
        ]));

        return redirect()->back()->with('success', 'ثبت نام با موفقیت انجام شد');
    }

    public function fakeRegisterSend($name, $username)
    {
        $user = MongoUser::where('username', $username)->first();
        if (isset($user)) {
            return $user->id;
        } else {
            $user = new MongoUser();
            $user->name = $name;
            $user->username = $username;
            $user->email = $username . strtolower(Str::random(10)) . "@gmail.com";
            $user->password = bcrypt(strtolower(Str::random(16)));
            $user->email_actived = 0;
            $user->save();
            return $user->id;
        }
    }

    public function checkIfEmailExist(Request $request)
    {
        $email = $request->email;
        $user = MongoUser::where('email', $email)->first();
        if (isset($user)) {
            return response()->json(['status' => 1, 'email' => $email, 'name' => $user->name]);
        } else {
            return response()->json(['status' => 0, 'email' => $email]);
        }
    }

    public function dashboard($useranme, $tab = null)
    {
        $dashuser = MongoUser::where('username', $useranme)->first();
        if (isset($dashuser)) {
            $showPostForDefault = 1;
            $advertises = $dashuser->advertises;
            $blogs = $dashuser->blogs;
            if (count($blogs) == 0) {
                if (count($advertises) > 0) {
                    $showPostForDefault = 0;
                }
            }
            return view('user.dashboard', compact('dashuser', 'tab', 'advertises', 'blogs', 'showPostForDefault'));
        } else {
            abort(404);
        }
    }

    public function dashboardEdit($tab = null)
    {
        $user = auth('user')->user();
        if (isset($user)) {
            $user_money = (int)$user->money ?? 0;

            $compact = [
                'user_money',
                'tab',
            ];
            switch ($tab) {
                case 'ad':
                    $uadvertises = $user->advertises;
                    $compact[] = 'uadvertises';
                    break;
                case 'job':
                    $ujobs = $user->works ?? [];
                    $jobs = MongoWork::all();
                    $compact = array_merge($compact, ['ujobs', 'jobs']);
                    break;
                case 'post':
                    $uBlogs = $user->blogs;
                    $compact[] = 'uBlogs';
                    break;
                case 'forum':
                    $uquestions = $user->questions;
                    $compact[] = 'uquestions';
                    break;
            }
            return view('user.dashboard-edit', compact($compact));
        } else {
            abort(404);
        }
    }

    public function uploadUserImage(Request $request)
    {
        $user = auth('user')->user();

        if ($request->hasFile('image')) {
            $cover = $request->file('image');
            $path = 'user/profile/';

            if (isset($user->attributes['image'])) {
                $image_name = explode($path, $user->attributes['image'])[1];
                $basefilename = explode('.webp', $image_name)[0];
            } else {
                $basefilename = $user->username . rand(1000, 9999) . time();
            }

            //main image
            $filename = $basefilename . '.webp';
            $user->image = $path . $filename;
            $this->uploadAndResizeImage($cover, $path, $filename, 90, 0);

            //thum image
            $filename2 = $basefilename . '2.webp';
            $this->uploadAndResizeImage($cover, $path, $filename2, 90, 1);
        }

        $user->update();

        $npath = asset($user->image());
        return response()->json([
            'success' => 1,
            'filePath' => $npath,
        ], 200);
    }

    private function uploadAndResizeImage($image, $path, $filename, $quality, $thumb)
    {
        $disk = Storage::disk('ftp');

        if ($thumb == 1) {
            $resizedImage = Image::make($image)->resize(256, null, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('webp', $quality);
        } else {
            $resizedImage = Image::make($image)->encode('webp', $quality);
        }
        $disk->put($path . $filename, (string) $resizedImage);
    }

    public function notifications()
    {
        $user = auth('user')->user();
        $notifications = $user->notifications;
        $user->unreadNotifications->markAsRead();
        return view('user.notification', compact('notifications'));
    }

    public function yourPackages()
    {
        return view('user.packages');
    }


    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ], [
            'name.required' => 'نام خود را وارد کنید'
        ]);

        $user = auth('user')->user();
        $user->name = $request->name;
        $user->body = $request->body;
        if ($request->phone != null) {
            $user->phone = $request->phone;
        }

        $user->update();

        return back()->with('success', 'تغییرات ذخیره شد');
    }

    public function updateAdmin(Request $request, $user_id)
    {
        $user = MongoUser::find($user_id);
        if (isset($request->name)) {
            $user->name = $request->name;
        }
        if (isset($request->username)) {
            $user->username = $request->username;
        }
        if (isset($request->email)) {
            $user->email = $request->email;
        }
        if (isset($request->money)) {
            $user->money = $request->money;
        }
        if (isset($request->body)) {
            $user->body = $request->body;
        }

        if ($request->hasFile('image')) {
            $cover = $request->file('image');
            $path = 'user/profile/';

            if (isset($user->attributes['image'])) {
                $image_name = explode($path, $user->attributes['image'])[1];
                $basefilename = explode('.webp', $image_name)[0];
            } else {
                $basefilename = $user->username . rand(1000, 9999) . time();
            }

            //main image
            $filename = $basefilename . '.webp';
            $user->image = $path . $filename;
            $this->uploadAndResizeImage($cover, $path, $filename, 90, 0);

            //thum image
            $filename2 = $basefilename . '2.webp';
            $this->uploadAndResizeImage($cover, $path, $filename2, 90, 1);
        }


        $user->update();

        return back()->with('success', 'تغییرات ذخیره شد');
    }

    public function activeEmail()
    {
        $user = auth('user')->user();
        if ($user->email_actived == 1) {
            return response()->json(['message' => 'ایمیل شما تایید شده است', 'success' => 0], 200);
        }

        $message = "یک پیام برای فعالسازی حساب به ایمیل شما ارسال شد " . $user->email;

        if (isset($user->active_code)) {
            if (!isset($user->last_active_code)) {
                $time_diff = 91;
            } else {
                $time_diff = now()->diffInSeconds($user->last_active_code);
            }
            if ($time_diff < 90) {
                $remaining_time = 90 - $time_diff;
                return response()->json(['message' => "برای ارسال مجدد $remaining_time ثانیه صبر کنید.", 'success' => 0], 200);
            } else {
                $active_code = Str::lower(Str::random(16));
                $user->active_code = $active_code;
                $user->last_active_code = now()->format('Y-m-d H:i:s');
                $user->update();
                $activeRoute = route('active.user', ['username' => $user->username, 'code' => $active_code]);
                dispatch(new SendEmailActiveEmail($activeRoute, $user->email))->onQueue('becharkhsite');
            }
        } else {
            $active_code = Str::lower(Str::random(16));
            $user->active_code = $active_code;
            $user->last_active_code = now()->format('Y-m-d H:i:s');
            $user->update();
            $activeRoute = route('active.user', ['username' => $user->username, 'code' => $active_code]);
            dispatch(new SendEmailActiveEmail($activeRoute, $user->email))->onQueue('becharkhsite');
        }
        return response()->json(['message' => $message, 'success' => 1], 200);
    }

    public function userChangeEmail(Request $request)
    {
        $check_email = MongoUser::where('email', $request->email)->first();
        if (isset($check_email)) {
            return response()->json(['message' => 'ایمیل وارد شده قبلا ثبت شده است', 'error' => 1], 200);
        }
        $user = auth('user')->user();
        $user->email = $request->email;
        $user->update();
        $user->unset('last_active_code');
        $user->unset('email_actived');
        return response()->json(['message' => 'ایمیل با موفقیت تغییر کرد'], 200);
    }

    public function activeUser($username, $code)
    {
        $user = MongoUser::where('username', $username)->first();
        $active_code = $user->active_code ?? null;
        if ($active_code != null && $active_code == $code) {
            $user->email_actived = 1;
            $user->update();
            $user->unset('active_code');
            $user->unset('last_active_code');

            app(AdvertiseController::class)->activeAdsAfterActiveEmail($user->id);

            $admin = Admin::first();
            $admin->notify(new SiteEvent([
                'action' => ' کاربر با نام کاربری ' . $user->username . ' ایمیل خود را تایید کرد',
                'route' => route('user.dashboard', $user->username),
            ]));

            return redirect()->route('home')->with('success', 'تبریک حساب کاربری شما با موفقیت فعال شد');
        } else {
            return redirect()->route('home')->with('success', 'اعتبار این لینک فعال سازی به پایان رسیده است');
        }
    }

    public function myList()
    {
        $advertises = auth('user')->user()->advertises();
        return view('user.mylist', compact('advertises'));
    }

    public function logout()
    {
        auth('user')->logout();

        $cookie_post = cookie()->forget('posts_count');
        $cookie_ad = cookie()->forget('ads_count');
        $cookie_question = cookie()->forget('question_count');
        $cookie_video = cookie()->forget('videos_count');

        return redirect()->back()
            ->withCookie($cookie_post)
            ->withCookie($cookie_ad)
            ->withCookie($cookie_question)
            ->withCookie($cookie_video);
    }

    public function getuserapi()
    {
        if (auth('user')->check()) {
            return auth('user')->user();
        } else {
            return;
        }
    }

    public function islikeapi($question_id)
    {
        if (auth('user')->check()) {
            return auth('user')->user()->isLike($question_id);
        } else {
            return;
        }
    }

    public function getuserImageApi($question_id)
    {
        $question = Question::find($question_id);
        return $question->user->image();
    }

    public function destroyNotification($notif)
    {
        DB::table('notifications')
            ->where('id', $notif)->delete();
        return back()->with('success', 'پیام با موفقیت حذف شد');
    }

    public function favoriteIndex()
    {
        $user = auth('user')->user();
        $item_ids = MongoFollowItem::where('user_id', $user->id)->pluck('item_id');
        $followItems = MongoItem::whereIn('_id', $item_ids)->paginate(24);

        return view('user.favorite', compact('followItems'));
    }
}
