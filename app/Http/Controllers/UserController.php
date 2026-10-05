<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailActiveEmail;
use App\Mail\ActiveCodeEmail;
use App\Models\Admin;
use App\Models\ChangeUsername;
use App\Models\MongoCategoryComment;
use App\Models\MongoCategoryCommentLike;
use App\Models\MongoFollowItem;
use App\Models\MongoItem;
use App\Models\MongoQuestion;
use App\Models\MongoUser;
use App\Models\MongoWork;
use App\Models\Question;
use App\Models\User;
use App\Models\UserActivation;
use App\Models\UserMoney;
use App\Notifications\SiteEvent;
use App\Services\Admin\AdminNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use stdClass;

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
            return response()->json(['status' => 1, 'csrf_token' => csrf_token()], 200);
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

        AdminNotificationService::send(' کاربری جدید با نام  ' . $request->username . ' ثبت نام کرد', route('user.dashboard', $request->username));

        return response()->json(['success' => 1, 'csrf_token' => csrf_token()], 200);
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

        AdminNotificationService::send(' کاربری جدید با نام  ' . $request->username . ' ثبت نام کرد', route('user.dashboard', $request->username));

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
            $user->is_fake = 1;
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
        if (!isset($dashuser)) {
            abort(404);
        }
        $user_contents = collect();
        $comments = MongoCategoryComment::orderBy('created_at', 'desc')
            ->whereNull('parent_id')
            ->where('user_id', $dashuser->id)
            ->with('question')
            ->get();

        foreach ($comments as $comment) {
            if (isset($comment->question_id)) {
                $comment_question = $comment->question;
                if (isset($comment_question)) {
                    $comment->page_url = route('question.show', $comment_question->slug2) . '?cri=' . $comment->id;
                    $comment->page_img = $comment_question->image();
                }
            } else {
                if (isset($comment->items)) {
                    $comment_item = $comment->getItems()->last();
                    if (isset($comment_item)) {
                        $comment->page_url = $comment_item->withParentsCommentUrl() . '&cri=' . $comment->id;
                        $comment->page_img = $comment_item->image();
                    } else {
                        $comment_category = $comment->category;
                        if (isset($comment_category)) {
                            $comment->page_url = route('question.index', $comment_category->slug) . '?s=1' . '&cri=' . $comment->id;
                            $comment->page_img = $comment_category->image();
                        }
                    }
                } else {
                    $comment_category = $comment->category;
                    if (isset($comment_category)) {
                        $comment->page_url = route('question.index', $comment_category->slug) . '?s=1' . '&cri=' . $comment->id;
                        $comment->page_img = $comment_category->image();
                    }
                }
            }
            $user_content = new stdClass();
            $user_content->type = 'comment';
            $user_content->id = $comment->id;
            $user_content->editor = $comment->editor;
            $user_content->editor2 = $comment->editor2;
            $user_content->body = $comment->body;
            $user_content->iimages = $comment->iimages;
            $user_content->replies_count = $comment->replies_count;
            $user_content->page_url = $comment->page_url;
            $user_content->page_img = $comment->page_img;
            $user_content->created_at = $comment->created_at;
            $user_contents->add($user_content);
        }
        $questions = MongoQuestion::orderBy('created_at', 'desc')->where('user_id', $dashuser->id)->get();

        foreach ($questions as $question) {
            $user_content = new stdClass();
            $user_content->type = 'question';
            $user_content->id = $question->id;
            $user_content->status = $question->status;
            $user_content->title = $question->title;
            $user_content->body = $question->body;
            $user_content->answer_count = $question->answer_count;
            $user_content->page_url = $question->status == 1 ? route('question.show', $question->slug2) : null;
            $user_content->page_img = $question->image();
            $user_content->created_at = $question->created_at;
            $user_contents->add($user_content);
        }

        $user_contents = $user_contents->sortByDesc('created_at')->values();


        return view('user.dashboard', compact('dashuser', 'user_contents'));
    }

    public function dashboardEdit($tab = null)
    {
        $user = auth('user')->user();

        if (!isset($user)) {
            abort(404);
        }

        if ($tab == 'edit') {
            return view('user.dashboard-edit', compact('user'));
        } elseif ($tab == 'ads') {
            $advertises = $user->advertises;
            return view('user.dashboard-ads', compact('user', 'advertises'));
        }

        if (
            !$user->likes_count_updated_at ||
            now()->diffInHours($user->likes_count_updated_at) >= 1
        ) {
            $user_comments = MongoCategoryComment::where('user_id', $user->id)->get();
            $user_likes = $user_comments->sum('like_count');

            if ($user_likes > 0) {
                $user->likes_count = $user_likes;
                $user->likes_count_updated_at = now();
                $user->update();
            }
        } else {
            $user_likes = $user->likes_count;
        }

        return view('user.dashboard-main', compact('user', 'user_likes'));
    }

    public function uploadUserImage(Request $request)
    {
        if ($request->hasFile('image')) {
            $user = auth('user')->user();
            $cover = $request->file('image');
            $path = 'user/profile/';
            $user_img = $user->getImage();
            if ($user_img !== null) {
                $image_name = explode($path, $user_img)[1];
                $basefilename = explode('.webp', $image_name)[0];
            } else {
                $basefilename = $user->username . rand(1000, 9999) . time();
            }

            // نوع پسوند فایل
            $ext = strtolower($cover->getClientOriginalExtension()); // اضافه شد
            // لیست فرمت‌های قابل پشتیبانی توسط Intervention
            $supported = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']; // اضافه شد

            if (in_array($ext, $supported)) {
                //main image
                $filename = $basefilename . '.webp';
                $user->image = $path . $filename;
                $this->uploadAndResizeImage($cover, $path, $filename, 90, 0);

                //thumb image
                $filename2 = $basefilename . '2.webp';
                $this->uploadAndResizeImage($cover, $path, $filename2, 90, 1);
            } else {
                return response()->json([
                    'success' => 0,
                    'message' => 'عکس با فرمت HEIC پشتیبانی نمی‌شود',
                ], 415); // Unsupported Media Type
            }

            $user->update = 4;
            $user->update();

            $npath = asset($user->image());
            return response()->json([
                'success' => 1,
                'filePath' => $npath,
            ], 200);
        } else {
            return response()->json([
                'success' => 0,
            ], 404);
        }
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
        $notifications = $user->myNotifications;
        if ($notifications) {
            $notifications = $notifications->sortByDesc('created_at');
        } else {
            $notifications = collect();
        }
        foreach ($notifications as $notification) {
            if ($notification->unread) {
                $notification->seen = 1;
                $notification->unset('unread');
            }
        }
        $user->unset('notif_count');
        return view('user.notification', compact('notifications'));
    }

    public function yourPackages()
    {
        return view('user.packages');
    }


    public function update(Request $request)
    {
        $user = auth('user')->user();

        if ($request->has('name')) {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:25'],
            ], [
                'name.required' => 'نام نمی‌تواند خالی باشد.',
                'name.max' => 'نام نباید بیشتر از 25 کاراکتر باشد.'
            ]);
            $user->update = 1;
            $user->name = $validated['name'];
        }

        if ($request->has('phone')) {
            $validated = $request->validate([
                'phone' => ['required', 'digits_between:1,15'],
            ], [
                'phone.required' => 'شماره تلفن نمی‌تواند خالی باشد.',
                'phone.digits_between' => 'شماره تلفن باید حداکثر 15 رقم باشد.',
            ]);
            $user->update = 2;
            $user->phone = $validated['phone'];
        }

        if ($request->has('body')) {
            $validated = $request->validate([
                'body' => ['required', 'string'],
            ], [
                'body.required' => 'بیوگرافی نمی‌تواند خالی باشد.',
            ]);
            $user->update = 3;
            $user->body = $validated['body'];
        }

        $user->update();

        return response()->json([
            'success' => true,
            'message' => 'اطلاعات با موفقیت به‌روزرسانی شد.',
        ]);
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
        if (isset($request->email_actived)) {
            $user->email_actived = $request->email_actived;
        }
        if (isset($request->is_fake)) {
            $user->is_fake = $request->is_fake;
        } else {
            if ($user->is_fake == 1) {
                $unset_is_fake = 1;
            }
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

        $user->unset('update');

        if (isset($unset_is_fake)) {
            $user->unset('is_fake');
        }

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

            AdminNotificationService::send(' کاربر با نام کاربری ' . $user->username . ' ایمیل خود را تایید کرد', route('user.dashboard', $user->username));

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


    public function favoriteIndex()
    {
        $user = auth('user')->user();
        $item_ids = MongoFollowItem::where('user_id', $user->id)->pluck('item_id');
        $followItems = MongoItem::whereIn('_id', $item_ids)->paginate(24);

        return view('user.favorite', compact('followItems'));
    }

    public function requestChangeUsername(Request $request)
    {
        $newUsername = trim($request->new_username);

        // بررسی تکراری بودن نام کاربری
        $checkUser = MongoUser::where('username', $newUsername)->first();
        if ($checkUser) {
            return response()->json(['message' => 'این نام کاربری قبلا انتخاب شده است'], 403);
        }

        if (preg_match('/^\d/', $newUsername)) {
            return response()->json(['message' => 'نام کاربری نمی‌تواند با عدد شروع شود'], 403);
        }

        if (!preg_match('/^[a-zA-Z0-9_.]+$/', $newUsername)) {
            return response()->json(['message' => 'نام کاربری فقط می‌تواند شامل حروف انگلیسی، اعداد، نقطه و _ باشد'], 403);
        }

        $user = auth('user')->user();
        $change_user_name = new ChangeUsername();
        $change_user_name->user_id = $user->id;
        $change_user_name->username = $newUsername;
        $change_user_name->save();

        return response()->json(['success' => 1], 200);
    }

    public function changeUserNameReqs(Request $request)
    {
        $reqs = ChangeUsername::with('user')->get();
        foreach ($reqs as $req) {
            if (MongoUser::where('username', $req->username)->first()) {
                $req->exist = 1;
            } else {
                $req->exist = 0;
            }
        }
        return view('user.change-username-reqs', compact('reqs'));
    }
    public function DestroyChunReqs(Request $request)
    {
        $req = ChangeUsername::find($request->req_id);
        if (isset($req)) {
            $req->delete();
        }
        return back()->with('success', 'درخواست با موفقیت حذف شد');
    }

    public function isFakeuserExist(Request $request)
    {
        $user = MongoUser::where('username', $request->username)->first();
        if ($user) {
            return response()->json(['status' => 1], 200);
        } else {
            return response()->json(['status' => 0], 200);
        }
    }
}
