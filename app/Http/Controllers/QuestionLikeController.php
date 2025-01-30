<?php

namespace App\Http\Controllers;

use App\Models\MongoQuestion;
use App\Models\MongoQuestionLike;
use App\Models\Question;
use App\Models\QuestionLike;
use App\Notifications\UserNotif;
use Illuminate\Http\Request;

class QuestionLikeController extends Controller
{

    public function store(Request $request)
    {
        $question = MongoQuestion::find($request->question_id);
        $questionUser = $question->user;
        $user = auth('user')->user();
        $like_count = $question->like_count ?? 0;

        if ($request->like_or_unlike == "لایک") {
            $status = $this->addLikeOrUnlike($request, true);
            if ($status) {
                $like_count += 1;
                $question->like_count = $like_count;
                $question->update();
            }
        } else {
            $status = $this->addLikeOrUnlike($request, false);
            if ($status) {
                $like_count -= 1;
                $question->like_count = $like_count;
                $question->update();
            }
        }
        return response()->json([
            'likes_count' => $like_count,
            'is_like' => $request->like_or_unlike
        ]);
    }

    private function addLikeOrUnlike($request, $status)
    {
        $user = auth('user')->user();
        $last_like = MongoQuestionLike::where('question_id', $request->question_id)->where('user_id', $user->id)->first();
        if ($status) {
            if (!isset($last_like)) {
                $like = new MongoQuestionLike();
                $like->question_id = $request->question_id;
                $like->user_id = $user->id;
                $like->like_or_unlike = 1;
                $like->save();
                return 1;
            }
            return 0;
        } else {
            if (isset($last_like)) {
                $last_like->delete();
                return 1;
            }
            return 0;
        }
    }
}
