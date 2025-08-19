<?php

namespace App\Http\Controllers;

use App\Jobs\MissionComplete;
use App\Jobs\Question\ChangeHotAnswer;
use App\Jobs\Question\SendEmailQuestionAnswer;
use App\Jobs\SendUserNotification;
use App\Jobs\User\UpdateUserFollowItem;
use App\Mail\ReplyToCommentMail;
use App\Models\Admin;
use App\Models\MongoCategoryComment;
use App\Models\MongoCategoryCommentLike;
use App\Models\MongoQuestion;
use App\Models\MongoQuestionAnswer;
use App\Models\MongoQuestionAnswerLike;
use App\Models\MongoUser;
use App\Models\QuestionAnswerEditorImage;
use App\Notifications\SiteEvent;
use App\Services\Comment\CommentEditorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class QuestionAnswerController extends Controller
{

    public function store(Request $request)
    {
        $this->validate($request, [
            'body' => 'required'
        ], [
            'body.required' => 'پاسخ خود را وارد کنید'
        ]);
        if (!auth('user')->check()) {
            abort(403);
        }

        // $answer = new MongoCategoryComment();
        $answer = new MongoCategoryComment();
        $question = MongoQuestion::find($request->question_id);
        $questionUser = $question->user;

        $user = auth('user')->user();

        $answer->question_id = $request->question_id;
        if (isset($request->parent_id)) {
            $parent_comment = MongoCategoryComment::find($request->parent_id);
            if (isset($parent_comment)) {
                $answer->parent_id = $parent_comment->id;
                $this->setCommentRepliesCount($parent_comment, 1);
                if (isset($request->reply_id)) {
                    $reply = MongoCategoryComment::find($request->reply_id);
                    if (isset($reply)) {
                        $answer->reply_id = $request->reply_id;
                        $reply_user = $reply->user;
                        if (isset($reply_user)) {
                            $answer->reply_name = $reply_user->username;
                            if ($reply_user != $user && $reply_user != $questionUser) {
                                $this->NE($user, $reply_user, $question);
                            }
                        }
                    } else {
                        return response()->json(['error' => 'این نظر حذف شده است.'], 404);
                    }
                }
            }
            $answer->body = $request->body;
        } else {
            //NE to question user if Questionuser is not $user
            if ($user != $questionUser) {
                $this->NE($user, $questionUser, $question);
            }

            $answer_count = $question->answer_count ?? 0;
            $answer_count += 1;
            $question->answer_count = $answer_count;
            $question->update();

            $editor_service = new CommentEditorService();
            $editor_images = $editor_service->store('show_question', $request->body, $answer);
        }
        $answer->user_id = $user->id;
        $answer->save();

        dispatch(new SendUserNotification('question_answer', $user, $answer))->onQueue('becharkhsite')->delay(now()->addMinutes(1));

        $admin = Admin::first();
        $admin->notify(new SiteEvent([
            'action' => $user->username . ' یک پاسخ برای پرسش با عنوان ' . $question->title . ' منتشر کرد',
            'route' => route('question.show', $question->slug2)
        ]));

        if (isset($question->items)) {
            dispatch(new UpdateUserFollowItem('question_answer', $answer->id))->onQueue('becharkhsite')->delay(now()->addMinutes(1));
        }

        return back()->with('success', 'پاسخ شما با موفقیت ثبت شد');
    }

    public function storeWithoutRefresh(Request $request)
    {
        if (!isset($request->question_id) || !isset($request->body) || !isset($request->parent_id)) {
            return response()->json(['error' => 'خطایی رخ داد'], 404);
        }
        if ($request->body == '') {
            return response()->json(['error' => 'دیدگاه خود را بنویسید.'], 404);
        }
        if (!auth('user')->check()) {
            return response()->json(['error' => 'وارد حساب کاربری خود شوید.'], 401);
        }

        $question = MongoQuestion::find($request->question_id);
        if (!isset($question)) {
            return response()->json(['error' => 'سوال پیدا نشد.'], 404);
        }
        $answer = new MongoCategoryComment();
        $questionUser = $question->user;

        $user = auth('user')->user();
        $answer->question_id = $request->question_id;
        if (isset($request->parent_id)) {
            $parent_comment = MongoCategoryComment::find($request->parent_id);
            if (isset($parent_comment)) {
                $answer->parent_id = $parent_comment->id;
                $this->setCommentRepliesCount($parent_comment, 1);
                if (isset($request->reply_id)) {
                    $reply = MongoCategoryComment::find($request->reply_id);
                    if (isset($reply)) {
                        $answer->reply_id = $request->reply_id;
                        $reply_user = $reply->user;
                        if (isset($reply_user)) {
                            $answer->reply_name = $reply_user->username;
                            if ($reply_user != $user && $reply_user != $questionUser) {
                                $this->NE($user, $reply_user, $question);
                            }
                        }
                    } else {
                        return response()->json(['error' => 'این نظر حذف شده است.'], 404);
                    }
                }
            }
            $answer->body = $request->body;
        } else {
            if ($user != $questionUser) {
                $this->NE($user, $questionUser, $question);
            }

            $answer_count = $question->answer_count ?? 0;
            $answer_count += 1;
            $question->answer_count = $answer_count;
            $question->update();

            $editor_service = new CommentEditorService();
            $editor_images = $editor_service->store('show_question', $request->body, $answer);
        }
        $answer->user_id = $user->id;
        $answer->save();

        dispatch(new SendUserNotification('question_answer', $user, $answer))->onQueue('becharkhsite')->delay(now()->addMinutes(1));

        if (!isset($request->parent_id)) {
            $editor_service->updateImageCommentId($editor_images, $answer->id);
        }

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => $user->username . ' یک پاسخ برای پرسش با عنوان ' . $question->title . ' منتشر کرد',
                'route' => route('question.show', $question->slug2)
            ]));
        }

        return response()->json([
            'success' => 'نظر شما با موفقیت ثبت شد',
            'comment' => [
                'id' => $answer->id,
                'body' => $request->body,
                'username' => $user->username,
                'user_image' => $user->thumb(),
                'like_count' => 0,
                'unlike_count' => 0,
                'question_id' => $request->question_id,
                'parent_id' => $request->parent_id,
                'reply_id' => $request->reply_id,
                'reply_name' => $answer->reply_name
            ]
        ], 201);
    }

    private function NE($fromUser, $toUser, $question)
    {
        $user_send_email_key = 'user_email_send_' . $toUser->id;
        if (Cache::add($user_send_email_key, 1, 3600)) {
            if (isset($toUser) && (!isset($toUser->email_actived) || $toUser->email_actived != 0)) {
                $route = route('question.show', $question->slug2);
                dispatch(new SendEmailQuestionAnswer($toUser->email, $question->title, $fromUser->username, $route))->onQueue('becharkhsite');
            }
        }
    }

    public function storeAdmin(Request $request)
    {
        $this->validate($request, [
            'body' => 'required'
        ], [
            'body.required' => 'پاسخ خود را وارد کنید'
        ]);

        $answer = new MongoCategoryComment();
        $question = MongoQuestion::find($request->question_id);
        $questionUser = $question->user;

        $user_id = app(UserController::class)->fakeRegisterSend($request->name, $request->username);
        $user = MongoUser::find($user_id);

        $answer->question_id = $request->question_id;
        if (isset($request->parent_id)) {
            $parent_comment = MongoCategoryComment::find($request->parent_id);
            if (isset(($parent_comment))) {
                $answer->parent_id = $parent_comment->id;
                $this->setCommentRepliesCount($parent_comment, 1);
                if (isset($request->reply_id)) {
                    $reply = MongoCategoryComment::find($request->reply_id);
                    if (isset($reply)) {
                        $answer->reply_id = $request->reply_id;
                        $reply_user = $reply->user;
                        if (isset($reply_user)) {
                            $answer->reply_name = $reply_user->username;
                            if ($reply_user != $user && $reply_user != $questionUser) {
                                $this->NE($user, $reply_user, $question);
                            }
                        }
                    } else {
                        return response()->json(['error' => 'این نظر حذف شده است.'], 404);
                    }
                }
            }

            $answer->body = $request->body;
        } else {
            //NE to question user if Questionuser is not $user
            if ($user != $questionUser) {
                $this->NE($user, $questionUser, $question);
            }

            $answer_count = $question->answer_count ?? 0;
            $answer_count += 1;
            $question->answer_count = $answer_count;
            $question->update();

            $editor_service = new CommentEditorService();
            $editor_images = $editor_service->store('admin_qanswers', $request->body, $answer);
        }
        $answer->user_id = $user->id;
        $answer->save();

        dispatch(new SendUserNotification('question_answer', $user, $answer))->onQueue('becharkhsite')->delay(now()->addMinutes(1));

        if (!isset($request->parent_id)) {
            $editor_service->updateImageCommentId($editor_images, $answer->id);
        }

        if ($question->answer_count == 1) {
            $store_answer_cache_key = 'q_hot_ans' . $question->id;
            if (!Cache::has($store_answer_cache_key)) {
                Cache::put($store_answer_cache_key, true, 1800); // lock for 30 minutes
                dispatch(new ChangeHotAnswer($question->id))->onQueue('becharkhsite')->delay(now()->addMinutes(30));
            }
        }

        if (isset($question->items)) {
            dispatch(new UpdateUserFollowItem('question_answer', $answer->id))->onQueue('becharkhsite')->delay(now()->addMinutes(1));
        }

        return back()->with('success', 'پاسخ با موفقیت ثبت شد');
    }

    // action 1 = increase and 0 = decrease
    private function setCommentRepliesCount($comment, $action)
    {
        $replies_count = $comment->replies_count ?? 0;
        if ($action) {
            $comment->replies_count = $replies_count + 1;
            $comment->update();
        } else {
            $new_replies_count = $replies_count - 1;
            if ($new_replies_count <= 0) {
                $comment->unset('replies_count');
            } else {
                $comment->replies_count = $new_replies_count;
                $comment->update();
            }
        }
    }

    public function questionAnswersAdmin($question_id)
    {
        $question = MongoQuestion::find($question_id);
        $answers = MongoCategoryComment::where('question_id', $question_id)->orderBy('created_at', 'desc')->get();
        return view('question.admin.answers', compact('answers', 'question'));
    }

    public function questionAnswerEditAdmin($answer_id)
    {
        $answer = MongoCategoryComment::find($answer_id);
        $editor_service = new CommentEditorService();
        $editor_service->changeTempEditorLazyImg($answer);
        return view('question.admin.answer_edit', compact('answer'));
    }

    public function questionAnswerUpdateAdmin(Request $request, $answer_id)
    {
        $answer = MongoCategoryComment::find($answer_id);
        if (isset($answer->parent_id)) {
            $answer->body = $request->body;
        } else {
            $editor_service = new CommentEditorService();
            $editor_service->update('admin_edit_qanswer', $request->body, $answer);
        }
        $answer->update();

        return redirect()->route('question.answers.admin', $answer->question_id)->with('success', 'تغییرات ثبت شد');
    }

    public function questionAnswerDestroyAdmin($answer_id)
    {
        $answer = MongoCategoryComment::find($answer_id);
        $question = $answer->question;
        $answer_images = QuestionAnswerEditorImage::where('comment_id', $answer_id)->get();
        if (!$answer_images->isEmpty()) {
            $disk = Storage::disk('ftp');
            foreach ($answer_images as $ci) {
                $disk->delete($ci->path);
                $ci->delete();
            }
        }
        $likes = MongoCategoryCommentLike::where('comment_id', $answer->id)->get();
        foreach ($likes as $like) {
            $like->delete();
        }
        if (isset($answer->parent_id)) {
            $parent_comment = MongoCategoryComment::find($answer->parent_id);
            if (isset($parent_comment)) {
                $this->setCommentRepliesCount($parent_comment, 0);
            }
        }
        $answer->delete();
        if ($question->answer_count > 0) {
            $question->answer_count -= 1;
            $question->update();
        }

        app(UserNotificationController::class)->deleteNotification('question_answer', $answer->id);

        return back()->with('success', 'با موفقیت حذف شد');
    }
}
