<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InformationDoneMail extends Mailable
{
    use Queueable, SerializesModels;

    public $route;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($route)
    {
        $this->route = $route;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('tondiran@tondiran.ir')->view('email.info-done')->subject('اطلاعات ویرایش شد')->with([
            'route' => $this->route,
        ]);
    }
}
