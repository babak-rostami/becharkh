<?php

namespace App\Http\Controllers;

use App\Models\AdvertiseComment;
use App\Models\AdvertiseCommentLike;
use Illuminate\Http\Request;

class AdvertiseCommentLikeController extends Controller
{

    public function store(Request $request)
    {
        $advertiseComment = AdvertiseComment::find($request->advertise_comment_id);
        if ($request->like_or_unlike == "true") {
            $this->addLikeOrUnlike($request, true);
        } else {
            $this->addLikeOrUnlike($request, false);
        }
        return response()->json([
            'likecount' => $advertiseComment->likes()->count(),
            'unlikecount' => $advertiseComment->unlikes()->count(),
        ]);
    }

    private function addLikeOrUnlike($request, $status)
    {
        $like = AdvertiseCommentLike::where('ip', $request->ip())->where('advertise_comment_id', $request->advertise_comment_id)->where('like_or_unlike', $status)->first();
        if (isset($like)) {
            $like->delete();
        } else {
            $t = !$status;
            $other = AdvertiseCommentLike::where('ip', $request->ip())->where('advertise_comment_id', $request->advertise_comment_id)->where('like_or_unlike', $t)->first();
            if (isset($other)) {
                $other->delete();
            }
            $like = new AdvertiseCommentLike();
            $like->advertise_comment_id = $request->advertise_comment_id;
            $like->ip = $request->ip();
            $like->like_or_unlike = $status;
            $like->save();
        }
    }
}
