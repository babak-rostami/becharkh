<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\DiamondPackage;
use App\Models\MongoUserOrder;
use App\Models\UserMoney;
use App\Models\UserOrder;
use App\Notifications\SiteEvent;
use Illuminate\Http\Request;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

class UserOrderController extends Controller
{

    public function addShowPlace()
    {
        $user = auth('user')->user();

        $amount = 10000;

        return Payment::callbackUrl(route('callback'))->purchase(
            (new Invoice)->amount($amount),
            function ($driver, $transactionId) use ($amount, $user) {
                $order = new UserOrder();
                $order->transaction_number = $transactionId;
                $order->amount = $amount;
                $order->status = 0;
                $order->user_id = $user->id;
                $order->save();
            }
        )->pay()->render();
    }

    public function buyPackage($pack_id)
    {
        $user = auth('user')->user();
        $pack = DiamondPackage::find($pack_id);
        $amount = $pack->price;
        return Payment::callbackUrl(route('callback'))->purchase(
            (new Invoice)->amount($amount),
            function ($driver, $transactionId) use ($amount, $user, $pack) {
                $order = new UserOrder();
                $order->transaction_number = $transactionId;
                $order->amount = $amount;
                $order->status = 0;
                $order->user_id = $user->id;
                $order->order_type_id = $pack->id;
                $order->order_type_class = 'plan';
                $order->save();
            }
        )->pay()->render();
    }


    public function chargeAccount(Request $request)
    {
        $user = auth('user')->user();
        $amount = intval(str_replace(",", "", $request->ammount));
        if ($amount < 10000 || $amount > 99999999) {
            return back()->with('success', 'لطفا رقمی بین ده هزار تا صد میلیون تومان انتخاب کنید');
        }
        $order = new MongoUserOrder();
        $order->amount = $amount;
        $order->status = 0;
        $order->user_id = $user->id;
        $order->order_type_class = 'charge';
        $order->save();
        return Payment::callbackUrl(route('charge.account.callback', $order->id))->purchase(
            (new Invoice)->amount($amount),
            function ($driver, $transactionId) use ($order) {
                $order->transaction_number = $transactionId;
                $order->update();
            }
        )->pay()->render();
    }

    public function chargeAccountCallback(Request $request, $order_id)
    {
        if ($request->input('Status')) {
            $order = MongoUserOrder::find($order_id);
            $user = $order->user;
            $transaction_id = $order->transaction_number;
            $amount = intval($order->amount);
            try {
                $receipt = Payment::amount($amount)->transactionId($transaction_id)->verify();
                $order->payment_reference_id = $receipt->getReferenceId();
                $order->status = 1;
                $order->save();

                if ($order->order_type_class == "charge") {

                    $money = $user->money;
                    $user->money = intval($money) + intval($order->amount);
                    $user->update();

                    $admins = Admin::all();
                    foreach ($admins as $admin) {
                        $admin->notify(new SiteEvent([
                            'action' => $user->username . ' حساب خود را به میزان ' . $amount . ' تومان شارژ کرد ',
                            'route' => route('user.dashboard', $user->username),
                        ]));
                    }
                }

                $m = "تبریک حساب شما با موفقیت شارژ شد";
                return redirect()->route('user.dashboard.edit', $user->username)->with('success', $m);
            } catch (InvalidPaymentException $exception) {
                $error = $exception->getMessage();
                return view('user.callback', compact('error'));
            }
        }
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
                $order->save();

                if ($order->order_type_class == "plan") {
                    $pack = DiamondPackage::find($order->order_type_id);

                    $userDiamond = $user->diamond;

                    if (isset($userDiamond)) {
                        $userDiamondPack = $userDiamond;
                        $userDiamondPack->amount += $pack->diamond_count;
                    } else {
                        $userDiamondPack = new UserMoney();
                        $userDiamondPack->amount = $pack->diamond_count;
                    }
                    $userDiamondPack->user_id = $user->id;

                    if (isset($userDiamond)) {
                        $userDiamondPack->update();
                    } else {
                        $userDiamondPack->save();
                    }

                    $admins = Admin::all();
                    foreach ($admins as $admin) {
                        $admin->notify(new SiteEvent([
                            'action' => $user->name . ' پلن جدیدی را فعال کرد ',
                            'route' => route('user.dashboard', $user->username),
                        ]));
                    }
                }

                $m = "تعداد " . $pack->diamond_count . " الماس با موفقیت به حساب شما واریز شد";
                return redirect()->route('user.dashboard.edit', $user->username)->with('success', $m);
            } catch (InvalidPaymentException $exception) {
                $error = $exception->getMessage();
                return view('user.callback', compact('error'));
            }
        }
        return view('user.callback');
    }

    public function orders()
    {
        $orders = UserOrder::OrderBy('id', 'desc')->get();
        return view('admin.user-orders', compact('orders'));
    }
}
