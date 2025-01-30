<?php

namespace App\Http\Controllers;

use App\Mail\EmailToUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailToUserController extends Controller
{

    public function store(Request $request)
    {
        $this->validate($request,[
            'email' => 'required',
            'title' => 'required',
            'body' => 'required'
        ],[
            'email.required' => 'ایمیل کاربر را وارد کنید.',
            'title.required' => 'عنوان پیام را وارد کنید.',
            'body.required' => 'متن پیام را وارد کنید.'
        ]);
        $emailToUser = new \App\Models\EmailToUser();
        $emailToUser->admin_id = auth('admin')->id();
        $emailToUser->email = $request->email;
        $emailToUser->title = $request->title;
        $emailToUser->body = $request->body;
        $emailToUser->save();
        Mail::to($request->email)->send(new EmailToUser($request->title,$request->body));
        return back()->with('success', 'ایمیل با موفقیت ارسال شد');
    }

}
