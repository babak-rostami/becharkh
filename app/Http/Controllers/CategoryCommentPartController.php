<?php

namespace App\Http\Controllers;

use App\Models\MongoCategoryComment;
use Illuminate\Http\Request;

class CategoryCommentPartController extends Controller
{
    public function getReplies($comment_id)
    {
        $comment = MongoCategoryComment::findOrFail($comment_id);
        $replies = $comment->replies()->where('status', '!=', 0)->with([
            'user:id,username,name,image,body'
        ])->get([
            'id',
            'parent_id',
            'body',
            'user_id',
            'created_at',
            'reply_name',
            'like_count',
            'unlike_count'
        ]);

        $html = view('category.comment.parts.replies', [
            'comment' => $comment,
            'replies' => $replies,
            'is_admin' => auth('admin')->check() ? 1 : 0,
            'ftp_path' => 'https://dl.becharkh.com/user_files/'
        ])->render();

        return response()->json([
            'html' => $html
        ]);
    }
}
