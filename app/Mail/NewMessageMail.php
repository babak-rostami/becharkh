<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name, $title, $route;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $title, $route)
    {
        $this->name = $name;
        $this->title = $title;
        $this->route = $route;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('tondiran@tondiran.ir')->view('email.message')->subject($this->title)->with([
            'name' => $this->name,
            'route' => $this->route,
        ]);
    }
}
