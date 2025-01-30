<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Advertise;
use App\Models\AdvertisePackage;
use App\Models\UserAdvertisePackage;
use App\Models\UserOrder;
use App\Notifications\SiteEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

class UserAdvertisePackageController extends Controller
{

    public function buy($id)
    {
        $user = auth('user')->user();
        $pack = AdvertisePackage::find($id);

        return Payment::callbackUrl(route('advertise-callback'))->purchase(
            (new Invoice)->amount(intval($pack->price)),
            function ($driver, $transactionId) use ($user, $pack) {
                $order = new UserOrder();
                $order->transaction_number = $transactionId;
                $order->amount = $pack->price;
                $order->status = 0;
                $order->user_id = $user->id;
                $order->save();

                $userAdvertisePack = new UserAdvertisePackage();
                $userAdvertisePack->order_id = $order->id;
                $userAdvertisePack->user_id = $user->id;
                $userAdvertisePack->name = $pack->name;
                $userAdvertisePack->advertise_count = $pack->advertise_count;
                $userAdvertisePack->ad_to_top_count = $pack->ad_to_top_count;
                $userAdvertisePack->image_count = $pack->image_count;
                $userAdvertisePack->price = $pack->price;
                $userAdvertisePack->expire_date = Carbon::now()->addDays($pack->date_number)->toDateTimeString();
                $userAdvertisePack->save();
            }
        )->pay()->render();
    }


    public function callback(Request $request)
    {
        if ($request->input('Status')) {
            $user = auth('user')->user();
            $order = $user->orders->first();
            $transaction_id = $order->transaction_number;
            $amount = intval($order->amount);
            try {
                $receipt = Payment::amount($amount)->transactionId($transaction_id)->verify();
                $order->payment_reference_id = $receipt->getReferenceId();
                $order->status = 1;
                $order->update();
                $user->show_place = 1;
                $user->update();

                $userAdvertisePack = $order->userPackage;
                $userAdvertisePack->status = true;
                $userAdvertisePack->update();

                $success = 'yes';

                $admins = Admin::all();
                foreach ($admins as $admin) {
                    $admin->notify(new SiteEvent([
                        'action' => $user->name . ' بسته آگهی را با موفقیت فعال کرد ',
                        'route' => route('user.dashboard', $user->username),
                    ]));
                }

                return view('user.callback', compact('success'));
            } catch (InvalidPaymentException $exception) {
                $error = $exception->getMessage();
                return view('user.callback', compact('error'));
            }
        }
        return view('user.callback');
    }


    public function freePack()
    {
        $user = auth('user')->user();
        $userPackage = $user->package;
        if (isset($userPackage) && jdate($userPackage->expire_date)->greaterThanCarbon(\Illuminate\Support\Carbon::now())) {
            return back()->with('success', 'بسته هدیه ماهانه استفاده شده است');
        } else {
            $user->free_pack = true;
            $user->update();

            if (isset($userPackage)) {
                $userAdvertisePack = $userPackage;
            } else {
                $userAdvertisePack = new UserAdvertisePackage();
                $userAdvertisePack->advertise_count = 0;
                $userAdvertisePack->ad_to_top_count = 0;
            }
            $userAdvertisePack->user_id = auth('user')->id();
            $userAdvertisePack->advertise_count_free = 3;
            $userAdvertisePack->ad_to_top_count_free = 0;
            $userAdvertisePack->expire_date = Carbon::now()->addDays(30)->toDateTimeString();
            $userAdvertisePack->status = true;

            if (isset($userPackage)) {
                $userAdvertisePack->update();
            } else {
                $userAdvertisePack->save();
            }
            return redirect()->route('user.dashboard.edit')->with('success', 'پلن هدیه آگهی شخصی با موفقیت فعال شد');
        }
    }

    public function userPackAdmin($id)
    {
        $user = \App\Models\User::find($id);
        return view('admin.user-packages', compact('user'));
    }

    public function addToTop($id)
    {
        $advertise = Advertise::find($id);
        $advertise->adModel->created_at = Carbon::now()->toDateTimeString();
        $advertise->adModel->update();
        auth('user')->user()->decreaseAddToTop();
        return back()->with('success', 'آگهی با موفقیت به ابتدای لیست منتقل شد');
    }
}
