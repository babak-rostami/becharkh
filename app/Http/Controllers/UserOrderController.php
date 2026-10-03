<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\MongoUserOrder;
use App\Notifications\SiteEvent;
use App\Services\Admin\AdminNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Shetabit\Multipay\Exceptions\InvalidPaymentException;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

class UserOrderController extends Controller
{

    public function chargeAccount(Request $request)
    {
        $user = auth('user')->user();
        $amount = intval(Str::replace(",", "", $request->ammount));
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

                    AdminNotificationService::send($user->username . ' حساب خود را به میزان ' . $amount . ' تومان شارژ کرد ', route('user.dashboard', $user->username));
                }

                $m = "حساب با موفقیت شارژ شد";
                return redirect()->route('user.dashboard.edit')->with('success', $m);
            } catch (InvalidPaymentException $exception) {
                $error = $exception->getMessage();
                return view('user.callback', compact('error'));
            }
        }
    }
}
