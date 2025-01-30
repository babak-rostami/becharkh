<?php

namespace App\Http\Controllers;

use App\Models\MongoQuestion;
use App\Models\MongoUser;
use App\Models\MongoVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class QuestionVideoController extends Controller
{

    public function addVideoQuestion(Request $request)
    {
        $user = auth('user')->user();
        $question = MongoQuestion::find($request->id);
        if (!isset($question)) {
            return response()->json(['status' => 0, 'message' => 'سوال پیدا نشد!'], 404);
        }
        $question_video = $question->videos ?? null;
        $question_nacvideo = $question->nac_videos ?? null;
        if ($question_video == null && $question_nacvideo == null) {
            $is_free = 0;
            if (!$user->canAddVideo()) {
                return response()->json(['status' => 0, 'message' => 'موجودی کافی نمی باشد'], 404);
            }
        } else {
            $is_free = 1;
        }
        $cache_key = 'adding_question_video_' . $question->id;
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
            if ($question_nacvideo != $video_id) {
                if ($video_id == $question_video) {
                    $question->unset('nac_videos');
                    Cache::forget($cache_key);
                    return response()->json(['status' => 0, 'message' => 'ویدیو تایید شده است!'], 403);
                } else {
                    $question->nac_videos = $video_id;
                    $question->update();
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
        $question = MongoQuestion::find($id);
        $nac_video = $question->nac_videos ?? null;
        if (!$nac_video) {
            return back()->with('success', 'ویدیو ثبت نشده!');
        }
        $video = MongoVideo::find($nac_video);
        if (!isset($video)) {
            return back()->with('success', 'ویدیو پیدا نشد!');
        }
        $question->videos = $video->id;
        $question->update();
        $question->unset('nac_videos');
        return back()->with('success', 'ویدیو تایید شد');
    }
    public function rejectVideo($id)
    {
        $question = MongoQuestion::find($id);
        $video = $question->videos ?? null;
        if (!$video) {
            $user = MongoUser::find($question->user_id);
            $user->money += 15000;
            $user->update();
        }
        $question->unset('nac_videos');
        return back()->with('success', 'ویدیو رد شد');
    }
}
