<?php

namespace App\Jobs;

use App\Http\Controllers\UserNotificationController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendUserNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $forr, $from_user, $new_object;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($forr, $from_user, $new_object)
    {
        $this->forr = $forr;
        $this->from_user = $from_user;
        $this->new_object = $new_object;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        app(UserNotificationController::class)->sendNotification($this->forr, $this->from_user, $this->new_object);
    }
}
