<?php

namespace App\Jobs\Question;

use App\Mail\Question\SendNotificationToUsers as QuestionSendNotificationToUsers;
use App\Models\MongoQuestion;
use App\Models\MongoUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNotificationToUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user, $question, $title, $route;

    /**
     * Create a new job instance.
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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->user->email)->send(new QuestionSendNotificationToUsers($this->user, $this->question, $this->title, $this->route));
    }
}
