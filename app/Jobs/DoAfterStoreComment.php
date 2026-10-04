<?php

namespace App\Jobs;

use App\Jobs\pages\UpdateHotPages;
use App\Jobs\User\UpdateUserFollowItem;
use App\Models\Admin;
use App\Models\ItemForTopUser;
use App\Models\MongoCategory;
use App\Models\MongoCategoryComment;
use App\Models\MongoItem;
use App\Models\MongoQuestion;
use App\Models\MongoUser;
use App\Notifications\SiteEvent;
use App\Services\Admin\AdminNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DoAfterStoreComment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $page, $user_id, $comment_id, $object_id, $item_id, $requestData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($page, $user_id, $comment_id, $object_id, $item_id, $requestData)
    {
        $this->page = $page;
        $this->user_id = $user_id;
        $this->comment_id = $comment_id;
        $this->object_id = $object_id;
        $this->item_id = $item_id;
        $this->requestData = $requestData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $page = $this->page;
        $user = MongoUser::find($this->user_id);
        if (!$user) {
            return;
        }
        $comment = MongoCategoryComment::find($this->comment_id);
        if (!$comment) {
            return;
        }
        if ($page == 'show_question') {
            $object = MongoQuestion::find($this->object_id);
        } elseif ($page == 'comment') {
            $object = MongoCategory::find($this->object_id);
        }
        if (!$object) {
            return;
        }
        $item = MongoItem::find($this->item_id);

        $requestData = $this->requestData;

        $admin = Admin::first();

        if ($this->checkCommentContent($comment->body)) {
            $comment->status = 0;
            $comment->update();
            $route = 'https://becharkh.com' . Str::replace('http://localhost', '', route('admin.category.comment.index'));
            AdminNotificationService::send($user->username . ' یک نظر تایید نشده دارد!', $route);
        }

        if ($page == 'show_question') {
            dispatch(new SendUserNotification('question_answer', $user, $comment))->onQueue('becharkhsite');
            $route = 'https://becharkh.com' . Str::replace('http://localhost', '', route('question.show', $object->slug2));

            AdminNotificationService::send($user->username . ' یک پاسخ برای پرسش با عنوان ' . $object->title . ' منتشر کرد', $route);

            // if (isset($object->items)) {
            //     dispatch(new UpdateUserFollowItem('question_answer', $comment->id))->onQueue('becharkhsite');
            // }
        } elseif ($page == 'comment') {
            $this->updateHotItems();
            if (isset($comment->parent_id)) {
                dispatch(new SendEmailCategoryComment($comment->parent_id, $requestData['reply_id'], $user))->onQueue('becharkhsite');
                $commentPage = route('question.index', $object->slug) . "?s=1";
                $route = 'https://becharkh.com' . Str::replace('http://localhost', '', $commentPage);
                $this->sendUserNotification('ccomment', $user, $comment);

                AdminNotificationService::send($user->username . ' یک ریپلای ارسال کرد', $route);
            } else {
                if (isset($requestData['item_id'])) {
                    $commentPage = $item->withParentsCommentUrl();
                    $commentPageTitle = $item->full_title ?? $item->title;
                } else {
                    $commentPage = 'https://becharkh.com' . Str::replace('http://localhost', '', route('question.index', $object->slug) . "?s=1");
                    $commentPageTitle = $object->full_title ?? $object->title;
                }
                $route = $commentPage;

                AdminNotificationService::send($user->username . ' نظری در صفحه ' . $commentPageTitle . ' ارسال کرد', $route);
            }

            if (isset($item)) {
                $item_for_top_user = ItemForTopUser::where('item_id', $item->id)->first();
                if (!isset($item_for_top_user)) {
                    $item_for_top_user = new ItemForTopUser();
                    $item_for_top_user->item_id = $item->id;
                    $item_for_top_user->save();
                }
            }
        }
    }
    private function updateHotItems()
    {
        $update_hot_pages_cache_key = 'is_update_hpages';
        if (!Cache::has($update_hot_pages_cache_key)) {
            Cache::put($update_hot_pages_cache_key, true, 1800);
            dispatch(new UpdateHotPages())->onQueue('becharkhsite');
        }
    }
    private function sendUserNotification($forr, $from_user, $new_object)
    {
        dispatch(new SendUserNotification($forr, $from_user, $new_object))->onQueue('becharkhsite');
    }
    private function checkCommentContent(string $body): bool
    {
        $text = mb_strtolower($body);

        if (preg_match('/\b((https?:\/\/|ftp:\/\/|www\.|mailto:)[^\s]+)/i', $text)) {
            return true;
        }

        if (preg_match('/\b[a-z0-9-]+\.(com|ir|net|org|info|me|io|co|biz|xyz)\b/i', $text)) {
            return true;
        }

        $socialKeywords = [
            'instagram.com',
            'insta.me',
            't.me',
            'telegram.me',
            'wa.me',
            'whatsapp.com',
            'facebook.com',
            'fb.com',
            'twitter.com',
            'x.com',
            'linkedin.com',
            'aparat.com',
            'rubika.ir',
            'eitaa.ir',
            'bale.ai',
            'soroush.ir'
        ];

        foreach ($socialKeywords as $social) {
            if (Str::contains($text, $social)) {
                return true;
            }
        }

        $badWords = [
            'کثافت',
            'عوضی',
            'حرومزاده',
            'دیوث',
            'جاکش',
            'گوساله',
            'بی‌ناموس',
            'بیناموس',
            'بی ناموس',
            'احمق',
            'گاو',
            'مادر جنده',
            'مادرجنده',
            'ولد زنا',
            'کس کش',
            'کوسکش',
            'کوس کش',
            'جاکش',
            'خواهر کسه',
            'خواهرکسه',
            'خوارکسه',
            'خارکسه',
            'خارکسده',
            'حروم زاده',
            'حرومزاده',
            'کونی',
            'کونده',
            'پدر سگ',
            'پدرسگ',
            'توله سگ',
            'تخم سگ',
            'fuck',
            'گوه',
        ];

        foreach ($badWords as $bad) {
            if (Str::contains($text, mb_strtolower($bad))) {
                return true;
            }
        }

        return false;
    }
}
