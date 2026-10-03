<?php

namespace App\Http\Controllers;

use App\Models\MongoAdvertise;
use App\Models\MongoUser;
use App\Models\MongoVideo;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Intervention\Image\Facades\Image;
use Symfony\Component\Process\Exception\ProcessFailedException;

class AdvertiseVideoController extends Controller
{

    public function addVideoAdvertise(Request $request)
    {
        $user = auth('user')->user();
        $advertise = MongoAdvertise::find($request->id);
        if (!isset($advertise)) {
            return response()->json(['status' => 0, 'message' => 'آگهی پیدا نشد!'], 404);
        }
        $advertise_video = $advertise->videos ?? null;
        $advertise_nacvideo = $advertise->nac_videos ?? null;
        if ($advertise_video == null && $advertise_nacvideo == null) {
            $is_free = 0;
            if (!$user->canAddVideo()) {
                return response()->json(['status' => 0, 'message' => 'موجودی کافی نمی باشد'], 404);
            }
        } else {
            $is_free = 1;
        }
        $cache_key = 'adding_advertise_video_' . $advertise->id;
        if (Cache::has($cache_key)) {
            return response()->json(['status' => 0, 'message' => 'در حال پردازش ویدیو...'], 404);
        } else {
            Cache::put($cache_key, 1, now()->addMinutes(1));
        }
        $video = MongoVideo::where('youtube_link', $request->youtube_link)->first();
        $video_id = $video->id;
        if (isset($video_id)) {
            if (!$is_free) {
                $user->decreaseMoneyFor('add-video');
            }
            if ($advertise_nacvideo != $video_id) {
                if ($video_id == $advertise_video) {
                    $advertise->unset('nac_videos');
                    Cache::forget($cache_key);
                    return response()->json(['status' => 0, 'message' => 'ویدیو تایید شده است!'], 403);
                } else {
                    $advertise->nac_videos = $video_id;
                    $advertise->update();
                }
            }
            Cache::forget($cache_key);
            return response()->json([
                'status' => 1
            ], 200);
        } else {
            Cache::forget($cache_key);
            return response()->json(['status' => 0, 'message' => 'خطا در دریافت ویدیو!'], 404);
        }
    }

    public function acceptVideo($id)
    {
        $advertise = MongoAdvertise::find($id);
        $nac_video = $advertise->nac_videos ?? null;
        if (!$nac_video) {
            return back()->with('success', 'ویدیو ثبت نشده!');
        }
        $video = MongoVideo::find($nac_video);
        if (!isset($video)) {
            return back()->with('success', 'ویدیو پیدا نشد!');
        }
        $advertise->videos = $video->id;
        $advertise->update();
        $advertise->unset('nac_videos');
        return back()->with('success', 'ویدیو تایید شد');
    }
    public function rejectVideo($id)
    {
        $advertise = MongoAdvertise::find($id);
        $video = $advertise->videos ?? null;
        if (!$video) {
            $user = MongoUser::find($advertise->user_id);
            $user->money += 15000;
            $user->update();
        }
        $advertise->unset('nac_videos');
        return back()->with('success', 'ویدیو رد شد');
    }
}
