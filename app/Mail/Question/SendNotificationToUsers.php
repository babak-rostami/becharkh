<?php

namespace App\Mail\Question;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendNotificationToUsers extends Mailable
{
    use Queueable, SerializesModels;

    private $user, $question, $title, $route;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $question, $title, $route)
    {
        $this->user = $user;
        $this->question = $question;
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
        return $this->from('becharkh@becharkh.com')->view('email.question.notif-to-users')->subject($this->title)->with([
            'user' => $this->user,
            'question' => $this->question,
            'route' => $this->route,
        ]);
    }
}
