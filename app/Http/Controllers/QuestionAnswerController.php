<?php

namespace App\Http\Controllers;

use App\Jobs\MissionComplete;
use App\Jobs\Question\ChangeHotAnswer;
use App\Jobs\Question\SendEmailQuestionAnswer;
use App\Mail\ReplyToCommentMail;
use App\Models\Admin;
use App\Models\MongoQuestion;
use App\Models\MongoQuestionAnswer;
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

        $answer = new MongoQuestionAnswer();
        $question = MongoQuestion::find($request->question_id);
        $questionUser = $question->user;

        $user = auth('user')->user();

        $answer->question_id = $request->question_id;
        if (isset($request->parent_id)) {
            $answer->parent_id = $request->parent_id;
            if (isset($request->reply_id)) {
                $answer->reply_id = $request->reply_id;
            }
            $reply = MongoQuestionAnswer::find($request->parent_id);
            //NE to reply user if reply user is not $user
            if (isset($reply->user) && $reply->user != $user && $reply->user != $questionUser) {
                $this->NE($user, $reply->user, $question);
            }
            //NE to question user if Questionuser is not $user
            if ($user != $questionUser) {
                $this->NE($user, $questionUser, $question);
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

        if (!isset($request->parent_id)) {
            $editor_service->updateImageCommentId($editor_images, $answer->id);
        }

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => $user->username . ' یک پاسخ برای پرسش با عنوان ' . $question->title . ' منتشر کرد',
                'route' => route('question.show', ['category' => $question->category->slug, 'slug' => $question->slug, 'random' => $question->random_id])
            ]));
        }

        //mission complete
        // MissionComplete::dispatchSync($user, 1);

        // if (!$question->featureValuesHasItem->isEmpty()) {
        //     $user->getPoint(3, $question->category_id, $question->featureValuesHasItem->last()->item_id);
        // }

        return back()->with('success', 'پاسخ شما با موفقیت ثبت شد');
    }

    public function storeAdmin(Request $request)
    {
        $this->validate($request, [
            'body' => 'required'
        ], [
            'body.required' => 'پاسخ خود را وارد کنید'
        ]);

        $answer = new MongoQuestionAnswer();
        $question = MongoQuestion::find($request->question_id);
        $questionUser = $question->user;

        $user_id = app(UserController::class)->fakeRegisterSend($request->name, $request->username);
        $user = MongoUser::find($user_id);

        $answer->question_id = $request->question_id;
        if (isset($request->parent_id)) {
            $answer->parent_id = $request->parent_id;
            if (isset($request->reply_id)) {
                $answer->reply_id = $request->reply_id;
            }
            $reply = MongoQuestionAnswer::find($request->parent_id);
            //NE to reply user if reply user is not $user
            if (isset($reply->user) && $reply->user != $user && $reply->user != $questionUser) {
                $this->NE($user, $reply->user, $question);
            }
            //NE to question user if Questionuser is not $user
            if ($user != $questionUser) {
                $this->NE($user, $questionUser, $question);
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

        return back()->with('success', 'پاسخ با موفقیت ثبت شد');
    }

    public function questionAnswersAdmin($question_id)
    {
        $question = MongoQuestion::find($question_id);
        $answers = MongoQuestionAnswer::where('question_id', $question_id)->orderBy('created_at', 'desc')->get();
        return view('question.admin.answers', compact('answers', 'question'));
    }

    public function questionAnswerEditAdmin($answer_id)
    {
        $answer = MongoQuestionAnswer::find($answer_id);
        $editor_service = new CommentEditorService();
        $editor_service->changeTempEditorLazyImg($answer);
        return view('question.admin.answer_edit', compact('answer'));
    }

    public function questionAnswerUpdateAdmin(Request $request, $answer_id)
    {
        $answer = MongoQuestionAnswer::find($answer_id);
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
        $answer = MongoQuestionAnswer::find($answer_id);
        $question = $answer->question;
        $answer_images = QuestionAnswerEditorImage::where('comment_id', $answer_id)->get();
        if (!$answer_images->isEmpty()) {
            $disk = Storage::disk('ftp');
            foreach ($answer_images as $ci) {
                $disk->delete($ci->path);
                $ci->delete();
            }
        }
        $answer->delete();
        if ($question->answer_count > 0) {
            $question->answer_count -= 1;
            $question->update();
        }
        return back()->with('success', 'با موفقیت حذف شد');
    }

    private function NE($fromUser, $toUser, $question)
    {
        if (isset($toUser) && (!isset($toUser->email_actived) || $toUser->email_actived != 0)) {
            $route = route('question.show', ['category' => $question->category->slug, 'slug' => $question->slug, 'random' => $question->random_id]);
            dispatch(new SendEmailQuestionAnswer($toUser->email, $question->title, $fromUser->username, $route))->onQueue('becharkhsite');
        }
    }
}
