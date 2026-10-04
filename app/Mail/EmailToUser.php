<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailToUser extends Mailable
{
    use Queueable, SerializesModels;

    public $title, $body;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($title, $body)
    {
        $this->title = $title;
        $this->body = $body;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('tondiran@tondiran.ir')->view('email.touser')->subject($this->title)->with([
            'body' => $this->body,
            'title' => $this->title,
        ]);
    }
}
