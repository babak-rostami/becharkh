<?php

namespace App\Http\Controllers;

use App\Models\MongoVideo;
use App\Models\MongoVideoLike;
use App\Models\Video;
use App\Models\VideoLike;
use Illuminate\Http\Request;

class VideoLikeController extends Controller
{

    public function store(Request $request)
    {
        $video = MongoVideo::find($request->video_id);
        if (!$video) {
            return response()->json(['error' => 'not found!'], 404);
        }
        $like_count = $video->like_count ?? 0;
        $unlike_count = $video->unlike_count ?? 0;
        if ($request->like_or_unlike) {
            $status = $this->addLikeOrUnlike($request, true);
            if ($status == 0) {
                $new_status = 0;
                $like_count -= 1;
            } elseif ($status == 1) {
                $new_status = 1;
                $like_count += 1;
            } elseif ($status == 2) {
                $new_status = 1;
                $like_count += 1;
                $unlike_count -= 1;
            }
        } else {
            $status = $this->addLikeOrUnlike($request, false);
            if ($status == 0) {
                $new_status = 2;
                $unlike_count -= 1;
            } elseif ($status == 1) {
                $new_status = 3;
                $unlike_count += 1;
            } elseif ($status == 2) {
                $new_status = 3;
                $unlike_count += 1;
                $like_count -= 1;
            }
        }
        $video->like_count = $like_count;
        $video->unlike_count = $unlike_count;
        $video->update();
        return response()->json([
            'like_count' => $like_count,
            'unlike_count' => $unlike_count,
            'status' => $new_status
        ]);
    }

    private function addLikeOrUnlike($request, $status)
    {
        $user = auth('user')->user();
        $like = MongoVideoLike::where('video_id', $request->video_id)->where('user_id', $user->id)->first();
        if (isset($like)) {
            if ($like->like_or_unlike == $status) {
                $like->delete();
                return 0;
            } else {
                $like->like_or_unlike = $status;
                $like->update();
                return 2;
            }
        } else {
            $like = new MongoVideoLike();
            $like->video_id = $request->video_id;
            $like->user_id = $user->id;
            $like->like_or_unlike = $status;
            $like->save();
            return 1;
        }
    }
}
