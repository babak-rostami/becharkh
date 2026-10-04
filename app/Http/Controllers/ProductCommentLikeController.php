<?php

namespace App\Http\Controllers;

use App\Models\ProductComment;
use App\Models\ProductCommentLike;
use Illuminate\Http\Request;

class ProductCommentLikeController extends Controller
{
    public function store(Request $request)
    {
        $productComment = ProductComment::find($request->product_comment_id);
        $like_count = $productComment->like_count ?? 0;
        $unlike_count = $productComment->unlike_count ?? 0;
        if (!$productComment) {
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
        $productComment->like_count = $like_count;
        $productComment->unlike_count = $unlike_count;
        $productComment->update();
        return response()->json([
            'likecount' => $like_count,
            'unlikecount' => $unlike_count,
        ]);
    }

    private function addLikeOrUnlike($request, $status)
    {
        $like = ProductCommentLike::where('comment_id', $request->product_comment_id)->where('ip', $request->ip())->first();
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
            $like = new ProductCommentLike();
            $like->comment_id = $request->product_comment_id;
            $like->ip = $request->ip();
            $like->like_or_unlike = $status;
            $like->save();
            return 1;
        }
    }
}
