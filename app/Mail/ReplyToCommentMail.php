<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReplyToCommentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $title, $reply_name, $route;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pagetitle, $reply_name, $route)
    {
        $this->title = $pagetitle;
        $this->reply_name = $reply_name;
        $this->route = $route;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('becharkh@becharkh.com')->view('email.reply-comment')->subject("پاسخی دریافت کرده اید")->with([
            'title' => $this->title,
            'reply_name' => $this->reply_name,
            'route' => $this->route,
        ]);
    }
}
