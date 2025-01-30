<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\DiamondPackage;
use App\Models\UserMoney;
use App\Notifications\SiteEvent;
use Illuminate\Http\Request;

class DiamondPackageController extends Controller
{
    public function adminAll()
    {
        $packages = DiamondPackage::all();
        return view('diamond.packages', compact('packages'));
    }

    public function plans()
    {
        $user = auth('user')->user();
        $plans = DiamondPackage::all();
        return view('diamond.plans', compact('plans', 'user'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'diamond_count' => 'required',
            'price' => 'required'
        ], [
            'name.required' => 'لطفا نام بسته را وارد کنید',
            'diamond_count.required' => 'لطفا تعداد آگهی بسته را وارد کنید',
            'price.required' => 'لطفا قیمت بسته را وارد کنید (تومان)',
        ]);

        $package = new DiamondPackage();
        $package->name = $request->name;
        $package->body = $request->body;
        $package->diamond_count = $request->diamond_count;
        $package->org_price = $request->org_price;
        $package->price = $request->price;

        $package->save();

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => auth('admin')->user()->name . ' بسته الماس با نام ' . $package->name . ' را ایجاد کرد',
                'route' => route('admin.diamond.packages'),
            ]));
        }

        return back()->with('success', 'بسته با موفقیت ایجاد شد');
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'diamond_count' => 'required',
            'price' => 'required'
        ], [
            'name.required' => 'لطفا نام بسته را وارد کنید',
            'diamond_count.required' => 'لطفا تعداد آگهی بسته را وارد کنید',
            'price.required' => 'لطفا قیمت بسته را وارد کنید (تومان)',
        ]);

        $package = DiamondPackage::find($id);
        $package->name = $request->name;
        $package->body = $request->body;
        $package->diamond_count = $request->diamond_count;
        $package->org_price = $request->org_price;
        $package->price = $request->price;

        $package->update();

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => auth('admin')->user()->name . ' بسته الماس با نام ' . $package->name . ' را ویرایش کرد',
                'route' => route('admin.diamond.packages'),
            ]));
        }

        return back()->with('success', 'تغییرات بسته با موفقیت ثبت شد');
    }


    public function destroy($id)
    {
        $package = DiamondPackage::find($id);

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => auth('admin')->user()->name . ' بسته الماس با نام ' . $package->name . ' را حذف کرد',
                'route' => route('admin.diamond.packages'),
            ]));
        }

        $package->delete();
        return back()->with('success', 'بسته با موفقیت حذف شد');
    }

    public function freePack()
    {
        $user = auth('user')->user();
        $userDiamond = $user->diamond;
        if ($user->free_pack != 0) {
            return back()->with('success', 'بسته هدیه استفاده شده است');
        } else {
            if (isset($userDiamond)) {
                $userDiamondPack = $userDiamond;
                $userDiamondPack->amount += 30;
            } else {
                $userDiamondPack = new UserMoney();
                $userDiamondPack->amount = 30;
            }
            $userDiamondPack->user_id = $user->id;

            if (isset($userDiamond)) {
                $userDiamondPack->update();
            } else {
                $userDiamondPack->save();
            }
            $user->free_pack = 1;
            $user->update();
            return redirect()->route('user.dashboard.edit')->with('success', 'تبریک 30 الماس هدیه خوش آمد گویی به حساب شما واریز شد');
        }
    }
}
