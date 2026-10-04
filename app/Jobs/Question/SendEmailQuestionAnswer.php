<?php

namespace App\Jobs\Question;

use App\Mail\ReplyToCommentMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailQuestionAnswer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $toUser_email, $question_title, $fromUser_username, $route;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($toUser_email, $question_title, $fromUser_username, $route)
    {
        $this->toUser_email = $toUser_email;
        $this->question_title = $question_title;
        $this->fromUser_username = $fromUser_username;
        $this->route = $route;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->toUser_email)->send(new ReplyToCommentMail($this->question_title, $this->fromUser_username, $this->route));
    }
}
