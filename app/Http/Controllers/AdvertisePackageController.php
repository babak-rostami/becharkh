<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\AdvertisePackage;
use App\Notifications\SiteEvent;
use Illuminate\Http\Request;

class AdvertisePackageController extends Controller
{

    public function adminAll()
    {
        $packages = AdvertisePackage::all();
        return view('advertise.admin-packages', compact('packages'));
    }

    public function plans()
    {
        $user = auth('user')->user();
        $plans = AdvertisePackage::all();
        return view('advertise.plans', compact('plans', 'user'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'advertise_count' => 'required',
            'ad_to_top_count' => 'required',
            'image_count' => 'required',
            'price' => 'required',
            'date_number' => 'required',
        ], [
            'name.required' => 'لطفا نام بسته را وارد کنید',
            'advertise_count.required' => 'لطفا تعداد آگهی بسته را وارد کنید',
            'ad_to_top_count.required' => 'لطفا تعداد بالابر بسته را وارد کنید',
            'image_count.required' => 'لطفا تعداد تصویر بسته را وارد کنید',
            'price.required' => 'لطفا قیمت بسته را وارد کنید (تومان)',
            'date_number.required' => 'لطفا مدت زمان بسته را وارد کنید (تعداد روز)',
        ]);

        $package = new AdvertisePackage();
        $package->name = $request->name;
        $package->body = $request->body;
        $package->advertise_count = $request->advertise_count;
        $package->ad_to_top_count = $request->ad_to_top_count;
        $package->image_count = $request->image_count;
        $package->price = $request->price;
        $package->org_price = $request->org_price;
        $package->date_number = $request->date_number;

        $package->save();

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => auth('admin')->user()->name . ' بسته آگهی با نام ' . $package->name . ' را ایجاد کرد',
                'route' => route('admin.advertise.packages'),
            ]));
        }

        return back()->with('success', 'بسته با موفقیت ایجاد شد');
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'advertise_count' => 'required',
            'ad_to_top_count' => 'required',
            'image_count' => 'required',
            'price' => 'required',
            'date_number' => 'required',
        ], [
            'name.required' => 'لطفا نام بسته را وارد کنید',
            'advertise_count.required' => 'لطفا تعداد آگهی بسته را وارد کنید',
            'ad_to_top_count.required' => 'لطفا تعداد بالابر بسته را وارد کنید',
            'image_count.required' => 'لطفا تعداد تصویر بسته را وارد کنید',
            'price.required' => 'لطفا قیمت بسته را وارد کنید (تومان)',
            'date_number.required' => 'لطفا مدت زمان بسته را وارد کنید (تعداد روز)',
        ]);

        $package = AdvertisePackage::find($id);
        $package->name = $request->name;
        $package->body = $request->body;
        $package->advertise_count = $request->advertise_count;
        $package->ad_to_top_count = $request->ad_to_top_count;
        $package->image_count = $request->image_count;
        $package->price = $request->price;
        $package->org_price = $request->org_price;
        $package->date_number = $request->date_number;

        $package->update();

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => auth('admin')->user()->name . ' بسته آگهی با نام ' . $package->name . ' را ویرایش کرد',
                'route' => route('admin.advertise.packages'),
            ]));
        }

        return back()->with('success', 'تغییرات بسته با موفقیت ثبت شد');
    }


    public function destroy($id)
    {
        $package = AdvertisePackage::find($id);

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => auth('admin')->user()->name . ' بسته آگهی با نام ' . $package->name . ' را حذف کرد',
                'route' => route('admin.advertise.packages'),
            ]));
        }

        $package->delete();
        return back()->with('success', 'بسته با موفقیت حذف شد');
    }
}
