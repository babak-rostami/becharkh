<?php

namespace App\Http\Controllers;

use App\Mail\ReplyToCommentMail;
use App\Models\Admin;
use App\Models\MongoBlog;
use App\Models\MongoBlogComment;
use App\Notifications\SiteEvent;
use App\Services\Admin\AdminNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BlogCommentController extends Controller
{

    public function store(Request $request)
    {
        $this->validate($request, [
            'body' => 'required',
        ], [
            'body.required' => 'نظر خود را بنویسید',
        ]);
        $user = auth('user')->user();
        if (!isset($user)) {
            abort(403);
        }
        $comment = new MongoBlogComment();
        $comment->blog_id = $request->blog_id;
        $comment->body = $request->body;
        $comment->user_id = $user->id;
        $blog = MongoBlog::find($request->blog_id);

        $blogUser = $blog->user;

        //if comment is not main comment 
        if (isset($request->parent_id)) {
            $comment->parent_id = $request->parent_id;
            //if comment was reply to reply
            if (isset($request->reply_id)) {
                $comment->reply_id = $request->reply_id;
                $replyComment = MongoBlogComment::find($request->reply_id);
                $rUser = $replyComment->user;
                //send NE to replyUser if reply user is not $user
                if (isset($rUser) && $user != $rUser) {
                    $this->NE($user, $rUser, $blog);
                }
            }
            //if comment was reply to comment
            else {
                $parentComment = MongoBlogComment::find($request->parent_id);
                $pCUser = $parentComment->user;
                //send Ne to ParentUser if parent user is not user
                if (isset($user) && $user != $pCUser) {
                    $this->NE($user, $pCUser, $blog);
                }
            }
        }
        //main comment
        else {
            $comment_count = $blog->comment_count ?? 0;
            $comment_count += 1;
            $blog->comment_count = $comment_count;
            $blog->update();
        }

        AdminNotificationService::send(
            $user->username . ' نظری در مقاله ' . $blog->title . ' ارسال کرد',
            route('blog.show', ['category_slug' => $blog->category->slug, 'slug' => $blog->slug, 'random_id' => $blog->random_id])
        );

        $comment->save();

        // $user->getPoint(2, $blog->category_id);

        return back()->with('success', 'نظر شما با موفقیت ثبت شد');
    }

    public function storeAdmin(Request $request)
    {
        $this->validate($request, [
            'body' => 'required',
            'username' => 'required'
        ], [
            'body.required' => 'نظر خود را بنویسید',
        ]);
        $user_id = app(UserController::class)->fakeRegisterSend($request->name, $request->username);

        $comment = new MongoBlogComment();
        $comment->blog_id = $request->blog_id;
        $comment->body = $request->body;
        $comment->user_id = $user_id;
        $blog = MongoBlog::find($request->blog_id);

        $comment_count = $blog->comment_count ?? 0;
        $comment_count += 1;
        $blog->comment_count = $comment_count;
        $blog->update();

        $comment->save();

        return back()->with('success', 'نظر شما با موفقیت ثبت شد');
    }

    private function NE($fromUser, $toUser, $blog)
    {
        if (isset($toUser) && (!isset($toUser->email_actived) || $toUser->email_actived != 0)) {
            Mail::to($toUser->email)->send(new ReplyToCommentMail($blog->title, $fromUser->username, route('blog.show', ['category_slug' => $blog->category->slug, 'slug' => $blog->slug, 'random_id' => $blog->random_id])));
        }
    }
}
