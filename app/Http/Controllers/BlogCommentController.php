<?php

namespace App\Http\Controllers;

use App\Mail\ReplyToCommentMail;
use App\Models\Admin;
use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\MongoBlog;
use App\Models\MongoBlogComment;
use App\Notifications\SiteEvent;
use App\Notifications\UserNotif;
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
            $parentComment = MongoBlogComment::find($request->parent_id);
            $pCUser = $parentComment->user;
            //if comment was reply to reply
            if (isset($request->reply_to_id)) {
                $comment->reply_to_id = $request->reply_to_id;
                $replyComment = MongoBlogComment::find($request->reply_to_id);
                $rUser = $replyComment->user;
                //send NE to replyUser if reply user is not $user
                if (isset($rUser) && $user != $rUser) {
                    $this->NE($user, $rUser, $blog);
                }
                //send NE to ParentUser if parent user is not $user
                if (isset($pUser) && $user != $pCUser && $pCUser != $rUser) {
                    $this->NE($user, $pCUser, $blog);
                }
                //send NE to BlogUser if blog user is not $user
                if ($user != $blogUser && $blogUser != $pCUser && $blogUser != $rUser) {
                    $this->NE($user, $blogUser, $blog);
                }
            }
            //if comment was reply to comment
            else {
                //send Ne to ParentUser if parent user is not user
                if (isset($user) && $user != $pCUser) {
                    $this->NE($user, $pCUser, $blog);
                }
                //sent Ne to BlogUser if blog user is not user
                if ($user != $blogUser && $blogUser != $pCUser) {
                    $this->NE($user, $blogUser, $blog);
                }
            }
        }
        //main comment
        else {
            if ($user != $blogUser) {
                $this->NE($user, $blogUser, $blog);
            }

            $comment_count = $blog->comment_count ?? 0;
            $comment_count += 1;
            $blog->comment_count = $comment_count;
            $blog->update();
        }

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => $user->username . ' نظری در مقاله ' . $blog->title . ' ارسال کرد',
                'route' => route('blog.show', ['category_slug' => $blog->category->slug, 'slug' => $blog->slug, 'random_id' => $blog->random_id]),
            ]));
        }

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
        if (isset($toUser)) {
            Mail::to($toUser->email)->send(new ReplyToCommentMail($blog->title, $fromUser->username, route('blog.show', ['category_slug' => $blog->category->slug, 'slug' => $blog->slug, 'random_id' => $blog->random_id])));
            //     $toUser->notify(new UserNotif([
            //         'action' => ' یک نظر جدید از ' . $fromUser->username . ' دریافت کرده اید ',
            //         'route' => route('blog.show', ['category_slug' => $blog->category->slug, 'slug' => $blog->slug, 'random_id' => $blog->random_id]),
            //         'userImage' => asset($fromUser->image()),
            //         'pageImage' => asset('files/blog/images/' . $blog->image),
            //         'pageType' => 'blog',
            //         'notifType' => 'comment',
            //         'important' => 0,
            //         'pageId' => $blog->id,
            //         'userId' => $fromUser->id,
            //     ]));
        }
    }

    public function all()
    {
        $comments = BlogComment::orderBy('id', 'desc')->get();
        return view('blog.comments', compact('comments'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:250',
            'email' => 'required|max:250',
            'body' => 'required',
        ], [
            'name.required' => 'نام الزامی می باشد.',
            'email.required' => 'ایمیل الزامی می باشد.',
            'body.required' => 'متن پیام الزامی می باشد.',
        ]);

        $comment = BlogComment::find($id);
        $comment->name = $request->name;
        $comment->email = $request->email;
        $comment->body = $request->body;
        $comment->update();

        return back()->with('success', 'نظر شما با موفقیت ویرایش شد');
    }


    public function destroy($id)
    {
        $comment = BlogComment::find($id);
        $comment->delete();
        return back()->with('success', 'نظر با موفقیت حذف شد');
    }
}
