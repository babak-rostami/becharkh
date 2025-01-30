<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class RemoveVideoFromServer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $lastpath;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($lastpath)
    {
        $this->lastpath = $lastpath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (File::exists(public_path($this->lastpath))) {
            unlink(public_path($this->lastpath));
        }
    }
}
