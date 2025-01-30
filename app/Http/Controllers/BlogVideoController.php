<?php

namespace App\Http\Controllers;

use App\Models\MongoBlog;
use App\Models\MongoUser;
use App\Models\MongoVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class BlogVideoController extends Controller
{

    public function addVideoPost(Request $request)
    {
        $user = auth('user')->user();
        $blog = MongoBlog::find($request->id);
        if (!isset($blog)) {
            return response()->json(['status' => 0, 'message' => 'مقاله پیدا نشد!'], 404);
        }
        $blog_video = $blog->videos ?? null;
        $blog_nacvideo = $blog->nac_videos ?? null;
        if ($blog_video == null && $blog_nacvideo == null) {
            $is_free = 0;
            if (!$user->canAddVideo()) {
                return response()->json(['status' => 0, 'message' => 'موجودی کافی نمی باشد'], 404);
            }
        } else {
            $is_free = 1;
        }
        $cache_key = 'adding_blog_video_' . $blog->id;
        if (Cache::has($cache_key)) {
            return response()->json(['status' => 0, 'message' => 'در حال پردازش ویدیو...'], 404);
        } else {
            Cache::put($cache_key, 1, now()->addMinutes(1));
        }
        $video = MongoVideo::where('youtube_link', $request->youtube_link)->first();
        if (!$video) {
            $result = app(VideoController::class)->createYoutubeVideo($request->youtube_link);
            $status = $result['status'];
            if ($status == 1) {
                $video_id = $result['video_id'];
            } elseif ($status == 0) {
                return response()->json(['status' => 0, 'message' => $result['message']], 404);
            }
        } else {
            $video_id = $video->id;
        }
        if (isset($video_id)) {
            if (!$is_free) {
                $user->decreaseMoneyFor('add-video');
            }
            if ($blog_nacvideo != $video_id) {
                if ($video_id == $blog_video) {
                    $blog->unset('nac_videos');
                    Cache::forget($cache_key);
                    return response()->json(['status' => 0, 'message' => 'ویدیو تایید شده است!'], 403);
                } else {
                    $blog->nac_videos = $video_id;
                    $blog->update();
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
        $blog = MongoBlog::find($id);
        $nac_video = $blog->nac_videos ?? null;
        if (!$nac_video) {
            return back()->with('success', 'ویدیو ثبت نشده!');
        }
        $video = MongoVideo::find($nac_video);
        if (!isset($video)) {
            return back()->with('success', 'ویدیو پیدا نشد!');
        }
        $blog->videos = $video->id;
        $blog->update();
        $blog->unset('nac_videos');
        return back()->with('success', 'ویدیو تایید شد');
    }
    public function rejectVideo($id)
    {
        $blog = MongoBlog::find($id);
        $video = $blog->videos ?? null;
        if (!$video) {
            $user = MongoUser::find($blog->user_id);
            $user->money += 15000;
            $user->update();
        }
        $blog->unset('nac_videos');
        return back()->with('success', 'ویدیو رد شد');
    }
}
