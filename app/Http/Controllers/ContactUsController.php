<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\ContactUs;
use App\Models\MongoContactUs;
use App\Models\MongoUser;
use App\Notifications\SiteEvent;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{

    public function create()
    {
        $user = auth('user')->user();
        return view('contactus.create', compact('user'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'body' => 'required'
        ], [
            'body.required' => 'پیام خود را وارد کنید'
        ]);

        $name = auth('user')->check() ? auth('user')->user()->username : $request->name;

        $contact = new MongoContactUs();
        if (auth('user')->check()) {
            $contact->user_id = auth('user')->id();
        } else {
            $contact->name = $request->name;
            $contact->email = $request->email;
        }
        $contact->title = $request->title;
        $contact->body = $request->body;

        $contact->save();

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => $name . ' تماس با ما با عنوان ' . $contact->title . ' را ارسال کرد',
                'route' => route('contact.list'),
            ]));
        }

        return back()->with('success', 'پیام شما با موفقیت به پشتیبانی بچرخ ارسال شد');
    }


    public function lists()
    {
        $contacts = MongoContactUs::orderBy('created_at', 'desc')->get();
        return view('contactus.list', compact('contacts'));
    }
}
