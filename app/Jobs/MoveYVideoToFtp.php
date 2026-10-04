<?php

namespace App\Jobs;

use App\Models\MongoVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class MoveYVideoToFtp implements ShouldQueue
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
        $format = $video->defaultYVFormat();

        $lastpath = $format['file_path'];
        $filename = $format['filename'];
        $ftpdir = 'video/yfiles/' . $video->category->slug . '/';

        if (file_exists(public_path($lastpath))) {
            $disk = Storage::disk('ftp');
            $disk->put($ftpdir . $filename, file_get_contents(public_path($lastpath)));

            $video->updateFormat($format['format_id'], null, null, $ftpdir . $filename, null);

            dispatch(new RemoveVideoFromServer($lastpath))->onQueue('becharkhsite')->delay(now()->addMinutes(45));

            dispatch(new RemoveVideoFromFtp($video->id))->onQueue('becharkhsite')->delay(now()->addHours(48));
        }
    }
}
