<?php

namespace App\Jobs;

use App\Models\MongoVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class RemoveVideoFromFtp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $video_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($video_id)
    {
        $this->video_id = $video_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $video = MongoVideo::find($this->video_id);
        if (isset($video)) {
            $format = $video->defaultYVFormat();
            $filepath = $format['file_path'];

            $cache_watching_format = $video->id . "-watching";
            if (Cache::has($cache_watching_format)) {
                dispatch(new RemoveVideoFromFtp($video->id))->onQueue('becharkhsite')->delay(now()->addHours(24));
            } else {
                Storage::disk('ftp')->delete($filepath);
                $video->updateFormat($format['format_id'], null, null, -1, null);
            }
        }
    }
}
