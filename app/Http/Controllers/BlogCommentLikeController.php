<?php

namespace App\Http\Controllers;

use App\Models\MongoBlogComment;
use App\Models\MongoBlogCommentLike;
use Illuminate\Http\Request;

class BlogCommentLikeController extends Controller
{

    public function store(Request $request)
    {
        $blogComment = MongoBlogComment::find($request->blog_comment_id);
        $like_count = $blogComment->like_count ?? 0;
        $unlike_count = $blogComment->unlike_count ?? 0;
        if (!$blogComment) {
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
        $blogComment->like_count = $like_count;
        $blogComment->unlike_count = $unlike_count;
        $blogComment->update();
        return response()->json([
            'likecount' => $like_count,
            'unlikecount' => $unlike_count,
        ]);
    }

    private function addLikeOrUnlike($request, $status)
    {
        $like = MongoBlogCommentLike::where('comment_id', $request->blog_comment_id)->where('ip', $request->ip())->first();
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
            $like = new MongoBlogCommentLike();
            $like->comment_id = $request->blog_comment_id;
            $like->ip = $request->ip();
            $like->like_or_unlike = $status;
            $like->save();
            return 1;
        }
    }
}
