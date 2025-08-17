<?php

namespace App\Http\Controllers;

use App\Models\MongoCategory;
use App\Models\MongoItem;
use App\Models\MongoUser;
use App\Models\UserNotification;

class UserNotificationController extends Controller
{
    public function sendNotification($forr, $from_user, $new_object)
    {
        if ($forr == 'ccomment') {
            $notif = new UserNotification();
            if (isset($new_object->reply_id)) {
                $parent_comment = $new_object->parentReply;
            } else {
                $parent_comment = $new_object->parent;
            }
            $main_parent = $new_object->parent;
            $parent_comment_user = $parent_comment->user;
            if ($parent_comment->user_id != $from_user->id && (!isset($parent_comment_user->is_fake) || $parent_comment_user->is_fake != 1)) {
                $notif_text = $from_user->username . ' نظری برای شما در صفحه ';
                if (isset($main_parent->items)) {
                    $item = MongoItem::find($main_parent->items[0]);
                    if (!isset($item)) {
                        return;
                    }
                    $notif_text = $notif_text . $item->withParentsTitle() . ' ارسال کرد';
                    $notif_route = $item->withParentsCommentUrl();
                } else {
                    $category = MongoCategory::find($main_parent->category_id);
                    if (!isset($category)) {
                        return;
                    }
                    $notif_text = $notif_text . $category->title . ' ارسال کرد';
                    $notif_route = route('question.index', $category->slug) . "?s=1";
                }
                $this->updateUserNotifs($parent_comment->user_id);
                $notif->user_id = $parent_comment->user_id;
                $notif->msg = $notif_text;
                $notif->body = str_limit($new_object->body, 100, '...');
                $notif->route = $notif_route;
                $notif->unread = 1;
                $notif->type = 'ccomment';
                $notif->type_id = $new_object->id;
                $notif->save();
            }
        } elseif ($forr == 'question_answer') {
            if (isset($new_object->reply_id)) {
                $parent_comment = $new_object->parentReply;
                $parent_comment_user = $parent_comment->user;
            } else {
                if (isset($new_object->parent_id)) {
                    $parent_comment = $new_object->parent;
                    $parent_comment_user = $parent_comment->user;
                }
            }
            $notif_text = $from_user->username . ' نظری برای شما در صفحه ';
            $question = $new_object->question;
            $notif_text = $notif_text . $question->title . ' ارسال کرد';

            $notid_r = route('question.show',  $question->slug2);
            if (strpos($notid_r, "http://localhost") === 0) {
                $notid_r = str_replace("http://localhost", "https://becharkh.com", $notid_r);
            }
            $notif_route = $notid_r;

            if (isset($parent_comment) && $parent_comment->user_id != $from_user->id && (!isset($parent_comment_user->is_fake) || $parent_comment_user->is_fake != 1)) {
                $notif_to_anw_user = new UserNotification();
                $this->updateUserNotifs($parent_comment->user_id);
                $notif_to_anw_user->user_id = $parent_comment->user_id;
                $notif_to_anw_user->msg = $notif_text;
                $notif_to_anw_user->body = str_limit($new_object->body, 100, '...');
                $notif_to_anw_user->route = $notif_route;
                $notif_to_anw_user->unread = 1;
                $notif_to_anw_user->type = 'question_answer';
                $notif_to_anw_user->type_id = $new_object->id;
                $notif_to_anw_user->save();
            }
            $question_user = $question->user;
            if ($question_user->id != $from_user->id) {
                if ((!isset($question_user->is_fake) || $question_user->is_fake != 1) && (!isset($parent_comment) || (isset($parent_comment) && $parent_comment->user_id != $question_user->id))) {
                    $notif_to_q_user = new UserNotification();
                    $this->updateUserNotifs($question_user->id);
                    $notif_to_q_user->user_id = $question_user->id;
                    $notif_to_q_user->msg = $notif_text;
                    $notif_to_q_user->body = str_limit($new_object->body, 100, '...');
                    $notif_to_q_user->route = $notif_route;
                    $notif_to_q_user->unread = 1;
                    $notif_to_q_user->type = 'question_answer';
                    $notif_to_q_user->type_id = $new_object->id;
                    $notif_to_q_user->save();
                }
            }
        }
    }

    private function updateUserNotifs($user_id)
    {
        $max_allow_notif = 19;
        $user = MongoUser::find($user_id);
        $parent_user_notif_count = $user->notif_count ?? 0;
        $parent_user_notif_count += 1;

        if ($parent_user_notif_count > $max_allow_notif) {
            $user_notifications = $user->myNotifications()->orderBy('created_at', 'asc')->get();
            if ($user_notifications->count() > $max_allow_notif) {
                $old_notifications = $user_notifications->take($user_notifications->count() - $max_allow_notif);
                foreach ($old_notifications as $notif) {
                    $notif->delete();
                }
            }
            $user->notif_count = $max_allow_notif;
        } else {
            $user->notif_count = $parent_user_notif_count;
        }

        $user->update();
    }

    public function deleteNotification($forr, $for_id)
    {
        $notifs = UserNotification::where('type', $forr)->where('type_id', $for_id)->get();
        foreach ($notifs as $notif) {
            if (isset($notif)) {
                if ($notif->unread) {
                    $notif_user = MongoUser::find($notif->user_id);
                    if ($notif_user->notif_count == 1) {
                        $notif_user->unset('notif_count');
                    } else {
                        $notif_user->notif_count -= 1;
                        $notif_user->update();
                    }
                }
                $notif->delete();
            }
        }
    }

    public function adminUserNotifs()
    {
        $notifs = UserNotification::orderBy('created_at', 'desc')->get();
        return view('admin.user.notifs', compact('notifs'));
    }

    public function adminUserNotifDestroy($id)
    {
        $notif = UserNotification::find($id);
        $notif->delete();
        return back()->with('success', 'اعلان با موفقیت حذف شد');
    }
}
