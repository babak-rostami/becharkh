<?php

namespace App\Http\Controllers;

use App\Mail\EmailToUser;
use App\Mail\InformationDoneMail;
use App\Models\Admin;
use App\Models\EditInformation;
use App\Notifications\SiteEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EditInformationController extends Controller
{

    public function indexAdmin()
    {
        $edits = EditInformation::orderBy('id', 'desc')->get();
        return view('editInformation.admin-index', compact('edits'));
    }

    public function store(Request $request)
    {
        if (auth('user')->check()) {
            $this->validate($request, [
                'body' => 'required'
            ], [
                'body.required' => 'لطفا توضیحات خود را وارد کنید'
            ]);
        } else {
            $this->validate($request, [
                'body' => 'required',
                'email' => 'required'
            ], [
                'body.required' => 'لطفا توضیحات خود را وارد کنید',
                'email.required' => 'لطفا ایمیل خود را وارد کنید'
            ]);
        }

        $editInformation = new EditInformation();
        $editInformation->body = $request->body;
        $editInformation->url = $request->url;

        if (isset($request->email)) {
            $editInformation->email = $request->email;
        } else {
            $editInformation->user_id = auth('user')->id();
        }

        $editInformation->save();

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => 'درخواست ویرایش اطلاعات جدیدی ثبت شد',
                'route' => route('edit.informations.admin'),
            ]));
        }

        return back()->with('success', 'درخواست ویرایش اطلاعات شما با موفقیت ثبت شد');

    }


    public function editDone(Request $request)
    {
        $editInfo = EditInformation::find($request->id);
        $editInfo->status = 1;
        $editInfo->save();

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => 'ادمین ' . auth('admin')->user()->name . ' درخواست ویرایش اطلاعات را تکمیل کرد',
                'route' => route('edit.informations.admin'),
            ]));
        }
        if (isset($editInfo->email)) {
            Mail::to($editInfo->email)->send(new InformationDoneMail($editInfo->url));
        } else {
            Mail::to($editInfo->user->email)->send(new InformationDoneMail($editInfo->url));
        }

        return back()->with('success', 'تغییرات با موفقیت ثبت شد');
    }

    public function editDelete(Request $request)
    {
        $editInfo = EditInformation::find($request->id);
        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => 'ادمین ' . auth('admin')->user()->name . ' درخواست ویرایش اطلاعات را حذف کرد',
                'route' => route('edit.informations.admin'),
            ]));
        }
        $editInfo->delete();
        return back()->with('success', 'درخواست با موفقیت حذف شد');
    }

}
