<?php

namespace App\Http\Controllers;

use App\Mail\ReplyToCommentMail;
use App\Models\Admin;
use App\Models\MongoVideo;
use App\Models\MongoVideoComment;
use App\Models\Video;
use App\Models\VideoComment;
use App\Notifications\SiteEvent;
use App\Notifications\UserNotif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class VideoCommentController extends Controller
{

    public function store(Request $request)
    {
        $this->validate($request, [
            'body' => 'required',
        ], [
            'body.required' => 'نظر خود را بنویسید',
        ]);
        if (!auth('user')->check()) {
            abort(403);
        }
        $user = auth('user')->user();
        $comment = new MongoVideoComment();
        $comment->video_id = $request->video_id;
        $comment->body = $request->body;
        $comment->user_id = $user->id;

        $video = MongoVideo::find($request->video_id);
        $videoUser = $video->user;

        //if comment is not main comment 
        if (isset($request->parent_id)) {
            $comment->parent_id = $request->parent_id;
            $parentComment = MongoVideoComment::find($request->parent_id);
            $pCUser = $parentComment->user;
            //if comment was reply to reply
            if (isset($request->reply_id)) {
                $comment->reply_id = $request->reply_id;
                $replyComment = MongoVideoComment::find($request->reply_id);
                $rUser = $replyComment->user;
                //send NE to replyUser if reply user is not $user
                if (isset($rUser) && $user != $rUser) {
                    $this->NE($user, $rUser, $video);
                }
                //send NE to ParentUser if parent user is not $user
                if (isset($pUser) && $user != $pCUser && $pCUser != $rUser) {
                    $this->NE($user, $pCUser, $video);
                }
                //send NE to BlogUser if blog user is not $user
                if ($user != $videoUser && $videoUser != $pCUser && $videoUser != $rUser) {
                    $this->NE($user, $videoUser, $video);
                }
            }
            //if comment was reply to comment
            else {
                //send Ne to ParentUser if parent user is not user
                if (isset($user) && $user != $pCUser) {
                    $this->NE($user, $pCUser, $video);
                }
                //sent Ne to BlogUser if blog user is not user
                if ($user != $videoUser && $videoUser != $pCUser) {
                    $this->NE($user, $videoUser, $video);
                }
            }
        }
        //main comment
        else {
            if ($user != $videoUser) {
                $this->NE($user, $videoUser, $video);
            }

            $comment_count = $video->comment_count ?? 0;
            $comment_count += 1;
            $video->comment_count = $comment_count;
            $video->update();
        }

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => $user->username . ' نظری در ویدیو ' . $video->title . ' ارسال کرد',
                'route' => route('video.show', $video->slug2),
            ]));
        }

        $comment->save();

        return back()->with('success', 'نظر شما با موفقیت ثبت شد');
    }

    private function NE($fromUser, $toUser, $video)
    {
        if (isset($toUser)) {
            Mail::to($toUser->email)->send(new ReplyToCommentMail($video->title, $fromUser->username, route('video.show',  $video->slug2)));
            //     $toUser->notify(new UserNotif([
            //         'action' => ' یک نظر جدید از ' . $fromUser->username . ' دریافت کرده اید ',
            //         'route' => route('video.show',  $video->slug2),
            //         'userImage' => asset($fromUser->thumb()),
            //         'pageImage' => asset($video->thumb()),
            //         'pageType' => 'video',
            //         'notifType' => 'comment',
            //         'important' => 0,
            //         'pageId' => $video->id,
            //         'userId' => $fromUser->id,
            //     ]));
        }
    }
}
