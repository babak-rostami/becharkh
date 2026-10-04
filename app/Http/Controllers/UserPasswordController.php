<?php

namespace App\Http\Controllers;

use App\Jobs\DeleteUserResetPassword;
use App\Mail\ForgetPasswordEmail;
use App\Models\MongoUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserPasswordController extends Controller
{

    public function forgetPassword(Request $request)
    {
        $user = MongoUser::where('email', $request->email)->first();
        if (!isset($user)) {
            return response()->json(['error' => 'این ایمیل در بچرخ ثبت نام نکرده است!'], 404);
        }
        if (!isset($user->reset_password_time) || !isset($user->reset_password)) {
            $token = Str::lower(Str::random(54)) . $user->username . time();
            $user->reset_password = $token;
            $user->reset_password_time = now()->format('Y-m-d H:i:s');
            $user->update();
            dispatch(new DeleteUserResetPassword($user->id))->onQueue('becharkhsite')->delay(now()->addMinutes(30));
        } else {
            $time_diff = now()->diffInSeconds($user->reset_password_time);
        }

        if (isset($time_diff) && $time_diff < 90) {
            return response()->json(['error' => 'برای ارسال مجدد باید 90 ثانیه از درخواست قبلی گذشته باشد!'], 403);
        } else {
            $token = Str::lower(Str::random(54)) . $user->username . time();
            $user->reset_password = $token;
            $user->reset_password_time = now()->format('Y-m-d H:i:s');
            $user->update();
            $routeResetPass = route('reset.password.get', $token);
            Mail::to($user->email)->send(new ForgetPasswordEmail($routeResetPass));
            return response()->json(['message' => 'لینک تغییر رمز عبور به ایمیل شما ارسال شد و تا 30 دقیقه معتبر است'], 200);
        }
    }

    public function showResetPasswordForm($token)
    {
        $user = MongoUser::where('reset_password', $token)->first();
        if (isset($user)) {
            return view('user.forgetPasswordLink', ['token' => $token]);
        } else {
            return redirect()->route('home')->with('success', 'لینک معتبر نمی باشد!');
        }
    }

    public function submitResetPasswordForm(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ], [
            'email.required' => 'ایمیل خود را وارد کنید',
            'password.required' => 'پسورد جدید را وارد کنید'
        ]);
        $user = MongoUser::where('email', $request->email)->first();
        $reset_password = $user->reset_password ?? null;

        if ($reset_password != $request->token) {
            return back()->with('success', 'درخواست تغییر رمز عبور برای این ایمیل ثبت نشده است');
        }

        $user->password = bcrypt($request->password);
        $user->update();
        $user->unset('reset_password');
        $user->unset('reset_password_time');

        return redirect()->route('home')->with('success', 'رمز عبور با موفقیت تغییر کرد');
    }
}
