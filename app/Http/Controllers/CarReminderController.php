<?php

namespace App\Http\Controllers;

use App\Models\CarReminder;
use Illuminate\Http\Request;

class CarReminderController extends Controller
{


    public function all()
    {
        $reminders = CarReminder::orderBy('id', 'desc')->get();
        return view('reminder.car-index', compact('reminders'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'brand' => 'required',
            'model' => 'required',
            'price1' => 'required',
            'price2' => 'required',
            'email' => 'required',
        ], [
            'brand.required' => 'برند را انتخاب کنید.',
            'model.required' => 'مدل را انتخاب کنید.',
            'price1.required' => 'شروع قیمت را وارد کنید.',
            'price2.required' => 'قیمت پایانی را وارد کنید.',
            'email.required' => 'ایمیل خود را وارد کنید.',
        ]);
        $reminder = new CarReminder();
        $reminder->model_id = $request->model;
        $reminder->brand_id = $request->brand;
        $reminder->price1 = $request->price1;
        $reminder->price2 = $request->price2;
        $reminder->email = $request->email;
        $reminder->save();
        return back()->with('success', 'درخواست شما با موفقیت ثبت شد');
    }

    public function destroy($id)
    {
        $reminder = CarReminder::find($id);
        $reminder->delete();
        return back()->with('success','گزینه مورد نظر با موفقیت حذف شد');
    }

}
