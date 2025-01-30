<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ActiveCodeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $activeRoute;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($activeRoute)
    {
        $this->activeRoute = $activeRoute;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('becharkh@becharkh.com')->view('email.active-user')->subject('فعالسازی حساب کاربری')->with([
            'route' => $this->activeRoute,
        ]);
    }
}
