<?php

namespace App\Jobs;

use App\Models\BlogVideo;
use FFMpeg\Filters\Video\VideoFilters;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class DecresePostVideoSize implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $video;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(BlogVideo $video)
    {
        $this->video = $video;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $video = $this->video;
        $disk = Storage::disk('ftp');

        $videoPath = 'blog/videos/' . $video->blog->user->username . '/';
        $videoName = explode($videoPath, $video->path)[1];
        $newFilePathAndName = $videoPath . '2' . $videoName;
        $disk->delete($newFilePathAndName);

        // $lowBitrateFormat = (new X264());
        $lowBitrateFormat = (new X264())->setKiloBitrate(1000);
        FFMpeg::fromDisk('ftp')
            ->open($video->path)
            ->export()
            ->toDisk('ftp')
            ->inFormat($lowBitrateFormat)
            ->save($newFilePathAndName);

        $lastPath = $video->path;
        $video->path = $newFilePathAndName;
        $video->update();
        $disk->delete($lastPath);
    }
}
