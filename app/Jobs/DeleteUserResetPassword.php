<?php

namespace App\Jobs;

use App\Models\MongoUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteUserResetPassword implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = MongoUser::find($this->user_id);
        if (isset($user)) {
            $reset_password = $user->reset_password ?? null;
            $reset_password_time = $user->reset_password_time ?? null;
            if ($reset_password && $reset_password_time) {
                $time_diff = now()->diffInMinutes($reset_password_time);
                if ($time_diff > 29) {
                    $user->unset('reset_password');
                    $user->unset('reset_password_time');
                } else {
                    dispatch(new DeleteUserResetPassword($user->id))->onQueue('becharkhsite')->delay(now()->addMinutes(30));
                }
            }
        }
    }
}
