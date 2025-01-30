<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{


    public function store(Request $request, $advertise_id)
    {
        $this->validate($request, [
            'body' => 'required'
        ], [
            'body.required' => 'نظر خود را بنویسید'
        ]);

        $comment = new Comment();
        $comment->user_id = auth('user')->id();
        $comment->advertise_id = $advertise_id;
        $comment->body = $request->body;
        $comment->save();

        return back()->with('success', 'نظر با موفقیت ثبت شد');
    }

    public function delete($id)
    {
        $comment = Comment::find($id);
        $comment->delete();
        return back();
    }

    public function deleteForAdmin($id)
    {
        $comment = Comment::find($id);
        $comment->delete();
        return back();
    }


    public function update(Request $request)
    {
        $comment = Comment::find($request->comment_id);
        $comment->body = $request->body;
        $comment->update();
        return back()->with('success', 'نظر با موفقیت ویرایش شد');

    }


    public function replyStore(Request $request, $advertise_id)
    {
        $this->validate($request, [
            'body' => 'required'
        ], [
            'body.required' => 'پاسخ شما خالی می باشد'
        ]);

        $comment = new Comment();
        $comment->user_id = auth('user')->id();
        $comment->advertise_id = $advertise_id;
        $comment->parent_id = $request->parent_id;
        $comment->reply_id = $request->reply_id;
        $comment->body = $request->body;
        $comment->save();

        return back()->with('success', 'پاسخ با موفقیت ثبت شد');
    }


    public function all()
    {
        $comments = Comment::all();
        return view('comment.all', compact('comments'));
    }

}
