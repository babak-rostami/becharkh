<?php

namespace App\Mail;

use App\Models\CarReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendRemindMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reminder;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(CarReminder $reminder)
    {
        $this->reminder = $reminder;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('tondiran@tondiran.ir')->view('email.car-reminder')->subject("آگهی درخواستی شما موجود شد")->with([
            'brand' => $this->reminder->brand->title,
            'model' => $this->reminder->model->title,
            'price1' => $this->reminder->price1,
            'price2' => $this->reminder->price2,
        ]);
    }
}
