<?php

namespace App\Http\Controllers;

use App\Models\MongoVideoComment;
use App\Models\MongoVideoCommentLike;
use App\Models\VideoComment;
use App\Models\VideoCommentLike;
use Illuminate\Http\Request;

class VideoCommentLikeController extends Controller
{


    public function store(Request $request)
    {
        $videoComment = MongoVideoComment::find($request->video_comment_id);
        $like_count = $videoComment->like_count ?? 0;
        $unlike_count = $videoComment->unlike_count ?? 0;
        if (!$videoComment) {
            return response()->json(['error' => 'Comment not found'], 404);
        }
        if ($request->like_or_unlike == "true") {
            $status = $this->addLikeOrUnlike($request, true);
            if ($status == 0) {
                $like_count -= 1;
            } elseif ($status == 1) {
                $like_count += 1;
            } elseif ($status == 2) {
                $like_count += 1;
                $unlike_count -= 1;
            }
        } else {
            $status = $this->addLikeOrUnlike($request, false);
            if ($status == 0) {
                $unlike_count -= 1;
            } elseif ($status == 1) {
                $unlike_count += 1;
            } elseif ($status == 2) {
                $unlike_count += 1;
                $like_count -= 1;
            }
        }
        $videoComment->like_count = $like_count;
        $videoComment->unlike_count = $unlike_count;
        $videoComment->update();
        return response()->json([
            'likecount' => $like_count,
            'unlikecount' => $unlike_count,
        ]);
    }

    private function addLikeOrUnlike($request, $status)
    {
        $like = MongoVideoCommentLike::where('comment_id', $request->video_comment_id)->where('ip', $request->ip())->first();
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
            $like = new MongoVideoCommentLike();
            $like->comment_id = $request->video_comment_id;
            $like->ip = $request->ip();
            $like->like_or_unlike = $status;
            $like->save();
            return 1;
        }
    }
}
