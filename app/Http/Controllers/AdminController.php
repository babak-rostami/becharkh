<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Advertise;
use App\Models\Blog;
use App\Models\CategoryFeatureItem;
use App\Models\ChangeUsername;
use App\Models\LetMeKnow;
use App\Models\MongoAdvertise;
use App\Models\MongoBlog;
use App\Models\MongoCategory;
use App\Models\MongoItem;
use App\Models\MongoQuestion;
use App\Models\MongoUser;
use App\Models\MongoVideo;
use App\Models\Question;
use App\Models\SiteCategory;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{

    public function dashboard()
    {
        $blogs_count = MongoBlog::count();
        $user_count = MongoUser::count();
        $ad_count = MongoAdvertise::count();
        $videoNotAcceptedCount = MongoVideo::where('status', 0)->orWhere('status', 2)->count();
        $itemNotAccepted = MongoItem::where('status', 0)->get();
        $notAcceptedQuestions = MongoQuestion::where('status', 0)->count();
        $cat_waiting_count = MongoCategory::where('status', 0)->count();
        $cun_count = ChangeUsername::count();
        return view(
            'admin.dashboard',
            compact('cun_count', 'blogs_count', 'user_count', 'ad_count', 'cat_waiting_count', 'videoNotAcceptedCount', 'itemNotAccepted', 'notAcceptedQuestions')
        );
    }

    public function events()
    {
        $events = auth('admin')->user()->unreadNotifications;
        return view('admin.event', compact('events'));
    }

    public function deleteNotification($notif)
    {
        DB::table('notifications')
            ->where('id', $notif)->delete();
        return back()->with('success', 'رویداد با موفقیت حذف شد');
    }

    public function register()
    {
        return view('admin.register');
    }


    public function registerSend(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'username' => 'required|unique:admins',
            'email' => 'required|unique:admins',
            'password' => 'required',
        ], [
            'name.required' => 'لطفا نام خود را وارد کنید',
            'username.required' => 'لطفا نام کاربری خود را وارد کنید',
            'username.unique' => 'نام کاربری مورد نظر قبلا انتخاب شده است',
            'email.required' => 'لطفا ایمیل خود را وارد کنید',
            'email.unique' => 'ایمیل وارد شده قبلا استفاده شده است',
            'password.required' => 'لطفا پسورد را وارد کنید',
        ]);

        $admin = new Admin();
        $admin->name = $request->name;
        $admin->username = $request->username;
        $admin->email = $request->email;
        $admin->password = bcrypt($request->password);

        $admin->save();


        return redirect()->route('admin.register')->with('success', 'ثبت نام با موفقیت انجام شد');
    }


    public function login()
    {
        return view('admin.login');
    }


    public function loginSend(Request $request)
    {
        auth('admin')->attempt(
            $this->credentials($request)
        );

        $admin = auth('admin')->user();

        if (isset($admin)) {
            //******************لگر ادمین غیرفعال بود وارد نشود************************
            if ($admin->status == 0) {
                auth('admin')->logout();
                return redirect()->route('admin.login')->with('success', 'درخواست ادمینی شما تایید نشده است');
            } else {
                return redirect()->route('admin.dashboard');
            }
        } else {
            return back()->with('success', 'اطلاعات وارد شده اشتباه می باشد');
        }
    }

    public function edit()
    {
        $admin = auth('admin')->user();
        return view('admin.edit', compact('admin'));
    }


    public function update(Request $request)
    {
        $admin = auth('admin')->user();

        $this->validate($request, [
            'name' => 'required',
            'username' => 'required|unique:admins,username,' . $admin->id,
            'email' => 'required|unique:admins,email,' . $admin->id,
        ], [
            'name.required' => 'لطفا نام خود را وارد کنید',
            'username.required' => 'لطفا نام کاربری خود را وارد کنید',
            'username.unique' => 'نام کاربری مورد نظر قبلا انتخاب شده است',
            'email.required' => 'لطفا ایمیل خود را وارد کنید',
            'email.unique' => 'ایمیل وارد شده قبلا استفاده شده است',
        ]);

        $admin->name = $request->name;
        $admin->username = $request->username;
        $admin->email = $request->email;
        $admin->phone = $request->phone;

        if ($request->password != null) {
            $admin->password = bcrypt($request->password);
        }


        if ($request->hasFile('image')) {
            $path = public_path('/files/admin/images/' . $admin->image);
            if (File::exists($path)) {
                File::delete($path);
            }
            $cover = $request->file('image');
            $filename = time() . '.' . $cover->getClientOriginalName();
            $admin->image = $filename;
            $cover->move(public_path('/files/admin/images'), $filename);
        }

        $admin->update();

        return back()->with('success', 'اطلاعات شما با موفقیت ثبت شد');
    }

    public function logout()
    {
        auth('admin')->logout();
        return redirect()->route('home');
    }

    protected function credentials(Request $request)
    {
        return $request->only('email', 'password');
    }


    public function users()
    {
        $users = MongoUser::orderBy('created_at', 'desc')->paginate(500);
        return view('admin.users', compact('users'));
    }
}
