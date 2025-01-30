<?php

namespace App\Jobs;

use App\Mail\ReplyToCommentMail;
use App\Models\Admin;
use App\Models\MongoCategoryComment;
use App\Models\MongoItem;
use App\Notifications\SiteEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailCategoryComment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $comment_parent_id;
    private $reply_to_id;
    private $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($comment_parent_id, $reply_to_id = null, $user)
    {
        $this->comment_parent_id = $comment_parent_id;
        $this->reply_to_id = $reply_to_id;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $parentComment = MongoCategoryComment::find($this->comment_parent_id);
        if (!isset($parentComment)) {
            return;
        }
        $parentItems = $parentComment->items ?? null;
        $commentPage = null;
        $commentPageTitle = null;
        if ($parentItems) {
            $item = MongoItem::find($parentItems[0]);
            if (isset($item)) {
                $commentPage = $item->withParentsCommentUrl();
                $commentPageTitle = $item->full_title ?? $item->title;
            }
        } else {
            $category = $parentComment->category;
            $commentPage = route('question.index', $category->slug) . "?s=1";
            $commentPageTitle = $category->full_title ?? $category->title;
        }
        if ($commentPage == null || $commentPageTitle == null) {
            return;
        }
        $pCUser = $parentComment->user;
        //if comment was reply to reply
        if ($this->reply_to_id != null) {
            $replyComment = MongoCategoryComment::find($this->reply_to_id);
            $rUser = $replyComment->user;
            //send NE to replyUser if reply user is not $user
            if (isset($rUser) && (!isset($rUser->email_actived) || $rUser->email_actived != 0) && $this->user != $rUser) {
                Mail::to($rUser->email)->send(new ReplyToCommentMail($commentPageTitle, $this->user->username, $commentPage));
            }
            //send NE to ParentUser if parent user is not $this->user
            if (isset($pCUser) && (!isset($pCUser->email_actived) || $pCUser->email_actived != 0) && $this->user != $pCUser && $pCUser != $rUser) {
                Mail::to($pCUser->email)->send(new ReplyToCommentMail($commentPageTitle, $this->user->username, $commentPage));
            }
        }
        //if comment was reply to comment
        else {
            //send Ne to ParentUser if parent user is not user
            if (isset($pCUser) && (!isset($pCUser->email_actived) || $pCUser->email_actived != 0) && $this->user != $pCUser) {
                Mail::to($pCUser->email)->send(new ReplyToCommentMail($commentPageTitle, $this->user->username, $commentPage));
            }
        }
    }
}
