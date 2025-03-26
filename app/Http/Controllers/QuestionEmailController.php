<?php

namespace App\Http\Controllers;

use App\Jobs\Question\SendNotificationToUsers;
use App\Models\MongoCategoryComment;
use App\Models\MongoFollowItem;
use App\Models\MongoItem;
use App\Models\MongoQuestion;
use App\Models\MongoQuestionEmail;
use App\Models\MongoUser;
use App\Repositories\CategoryComment\Mongodb\CategoryCommentRepository;
use App\Repositories\Item\Mongodb\ItemRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class QuestionEmailController extends Controller
{

    public function index($question_id)
    {
        $question = MongoQuestion::select(['id', 'title'])->find($question_id);
        $emails = MongoQuestionEmail::where('question_id', $question_id)->get();
        return view('question.admin.send-email', compact('question'));
    }

    public function getCategoryUsers(Request $request)
    {
        $item_repository = new ItemRepository();
        $comment_repository = new CategoryCommentRepository();
        $item_ids = $item_repository->getItemsByCategoryId($request->category_id, ['id'])->pluck('id');
        $item_user_ids = MongoFollowItem::whereIn('item_id', $item_ids)->pluck('user_id');
        $comment_user_ids = $comment_repository->getParentCommentsByCategoryId($request->category_id, 300)->pluck('user_id');
        $all_user_ids = $item_user_ids->merge($comment_user_ids)->unique();

        $email_user_ids = MongoQuestionEmail::where('question_id', $request->question_id)->pluck('user_id');
        $user_ids = $all_user_ids->except($email_user_ids);

        $users = MongoUser::select('id', 'username', 'email', 'image')->find($user_ids);

        $users = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'image' => $user->thumb(),
            ];
        });

        return response()->json(['users' => $users], 200);
    }

    public function getItemegoryUsers(Request $request)
    {
        $comment_repository = new CategoryCommentRepository();
        $category_id = MongoItem::find($request->item_id)->category_id;

        $item_user_ids = MongoFollowItem::where('item_id', $request->item_id)->pluck('user_id');
        $comment_user_ids = $comment_repository->getParentCommentsByItemId($category_id, $request->item_id, 300)->pluck('user_id');
        $all_user_ids = $item_user_ids->merge($comment_user_ids)->unique();

        $email_user_ids = MongoQuestionEmail::where('question_id', $request->question_id)->pluck('user_id');
        $user_ids = $all_user_ids->except($email_user_ids);

        $users = MongoUser::select('id', 'username', 'email', 'image')->find($user_ids);
        $users = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'image' => $user->thumb(),
            ];
        });

        return response()->json(['users' => $users], 200);
    }

    public function sendQuestionEmail(Request $request)
    {
        $question = MongoQuestion::where('_id', $request->question_id)
            ->select(['title', 'body', 'items_title', 'slug2', 'user_id'])
            ->with(['user' => function ($query) {
                $query->select(['name']);
            }])
            ->with(['category' => function ($query) {
                $query->select(['slug']);
            }])->first();
        $user_ids = [];
        foreach ($request->users as $userId) {
            $user_ids[] = $userId['id'];
        }
        $email_user_ids = MongoQuestionEmail::where('question_id', $question->id)->pluck('user_id');
        $new_user_ids = array_filter($user_ids, function ($id) use ($email_user_ids) {
            return !in_array($id, $email_user_ids->all());
        });
        if (empty($new_user_ids)) {
            return response()->json(['message' => 'حداقل یک کاربر انتخاب کنید'], 404);
        }
        $users = MongoUser::whereIn('_id', $new_user_ids)->select(['email', 'name'])->get();
        $route = route('question.show', $question->slug2);

        $delay = 5;
        foreach ($users as $user) {
            $mqemail = new MongoQuestionEmail();
            $mqemail->question_id = $question->id;
            $mqemail->user_id = $user->id;
            $mqemail->save();
            dispatch(new SendNotificationToUsers($user, $question, $request->title, $route))->onQueue('becharkhsite')
                ->delay(now()->addSeconds($delay));
            $delay += 65;
        }
        return response()->json(['status' => 1], 200);
    }
}
