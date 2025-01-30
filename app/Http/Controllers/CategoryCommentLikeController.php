<?php

namespace App\Http\Controllers;

use App\Jobs\AddUserCategoryCommentMedal;
use App\Models\CategoryCommentLike;
use App\Models\MongoCategoryComment;
use App\Models\MongoCategoryCommentLike;
use App\Models\MongoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryCommentLikeController extends Controller
{

    public function store(Request $request)
    {
        $comment = MongoCategoryComment::find($request->category_comment_id);
        $like_count = $comment->like_count ?? 0;
        $unlike_count = $comment->unlike_count ?? 0;

        if (!$comment) {
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
        $comment->like_count = $like_count;
        $comment->unlike_count = $unlike_count;
        $comment->update();

        return response()->json([
            'likecount' => $like_count,
            'unlikecount' => $unlike_count,
        ]);
    }

    private function addLikeOrUnlike($request, $status)
    {
        $like = MongoCategoryCommentLike::where('comment_id', $request->category_comment_id)->where('ip', $request->ip())->first();
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
            $like = new MongoCategoryCommentLike();
            $like->comment_id = $request->category_comment_id;
            $like->ip = $request->ip();
            $like->like_or_unlike = $status;
            $like->save();
            return 1;
        }
    }
}
