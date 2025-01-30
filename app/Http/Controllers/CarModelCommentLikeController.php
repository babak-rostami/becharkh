<?php

namespace App\Http\Controllers;

use App\Models\CarModelComment;
use App\Models\CarModelCommentLike;
use Illuminate\Http\Request;

class CarModelCommentLikeController extends Controller
{

    public function store(Request $request)
    {
        $modelComment = CarModelComment::find($request->model_comment_id);
        if ($request->like_or_unlike == "true") {
            $this->addLikeOrUnlike($request, true);
        } else {
            $this->addLikeOrUnlike($request, false);
        }
        return response()->json([
            'likecount' => $modelComment->likes()->count(),
            'unlikecount' => $modelComment->unlikes()->count(),
        ]);
    }

    private function addLikeOrUnlike($request, $status)
    {
        $like = CarModelCommentLike::where('ip', $request->ip())->where('model_comment_id', $request->model_comment_id)->where('like_or_unlike', $status)->first();
        if (isset($like)) {
            $like->delete();
        } else {
            $t = !$status;
            $other = CarModelCommentLike::where('ip', $request->ip())->where('model_comment_id', $request->model_comment_id)->where('like_or_unlike', $t)->first();
            if (isset($other)) {
                $other->delete();
            }
            $like = new CarModelCommentLike();
            $like->model_comment_id = $request->model_comment_id;
            $like->ip = $request->ip();
            $like->like_or_unlike = $status;
            $like->save();
        }
    }

}
