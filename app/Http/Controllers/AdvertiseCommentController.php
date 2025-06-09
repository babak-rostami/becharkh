<?php

namespace App\Http\Controllers;

use App\Mail\ReplyToCommentMail;
use App\Models\Admin;
use App\Models\Advertise;
use App\Models\AdvertiseComment;
use App\Notifications\SiteEvent;
use App\Notifications\UserNotif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdvertiseCommentController extends Controller
{

    public function store(Request $request)
    {
        $this->validate($request, [
            'body' => 'required',
        ], [
            'body.required' => 'نظر خود را وارد کنید',
        ]);
        if (!auth('user')->check()) {
            abort(403);
        }

        $advertise = Advertise::find($request->advertise_id);
        if (!isset($advertise)) {
            return back()->with('success', 'آگهی پیدا نشد');
        }

        $advertise = Advertise::find($request->advertise_id);

        $user = auth('user')->user();
        $comment = new AdvertiseComment();
        $comment->advertise_id = $advertise->id;
        $comment->body = $request->body;
        $comment->user_id = $user->id;

        //if comment is not main comment 
        if (isset($request->parent_id)) {
            $comment->parent_id = $request->parent_id;
            $parentComment = AdvertiseComment::find($request->parent_id);
            $pCUser = $parentComment->user;
            //if comment was reply to reply
            if (isset($request->reply_id)) {
                $comment->reply_id = $request->reply_id;
                $replyComment = AdvertiseComment::find($request->reply_id);
                $rUser = $replyComment->user;
                //send NE to replyUser if reply user is not $user
                if (isset($rUser) && $user != $rUser) {
                    $this->NE($user, $rUser, $advertise);
                }
                //send NE to ParentUser if parent user is not $user
                if (isset($pUser) && $user != $pCUser && $pCUser != $rUser) {
                    $this->NE($user, $pCUser, $advertise);
                }
            }
            //if comment was reply to comment
            else {
                //send Ne to ParentUser if parent user is not user
                if (isset($pUser) && $user != $pCUser) {
                    $this->NE($user, $pCUser, $advertise);
                }
            }
        }

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => $user->username . ' نظری در صفحه ' . $advertise->title . ' ارسال کرد',
                'route' => route('ad.show', ['category_slug' => $advertise->category->slug, 'slug' => $advertise->slug, 'random' => $advertise->random_id]),
            ]));
        }

        $comment->save();


        return back()->with('success', 'نظر شما با موفقیت ثبت شد');
    }

    private function NE($fromUser, $toUser, $advertise)
    {
        if (isset($toUser)) {
            Mail::to($toUser->email)->send(new ReplyToCommentMail($advertise->title, $fromUser->username, route('ad.show', ['category_slug' => $advertise->category->slug, 'slug' => $advertise->slug, 'random' => $advertise->random_id])));
            $toUser->notify(new UserNotif([
                'action' => ' یک نظر جدید از ' . $fromUser->username . ' دریافت کرده اید ',
                'route' => route('ad.show', ['category_slug' => $advertise->category->slug, 'slug' => $advertise->slug, 'random' => $advertise->random_id]),
                'userImage' => asset($fromUser->image()),
                'pageImage' => $advertise->image(),
                'pageType' => 'advertise',
                'notifType' => 'comment',
                'important' => 0,
                'pageId' => $advertise->id,
                'userId' => $fromUser->id,
            ]));
        }
    }
}
