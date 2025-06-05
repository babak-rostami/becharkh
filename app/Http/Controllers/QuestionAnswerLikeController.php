<?php

namespace App\Http\Controllers;

use App\Jobs\Question\ChangeHotAnswer;
use App\Models\MongoCategoryComment;
use App\Models\MongoCategoryCommentLike;
use App\Models\MongoQuestionAnswer;
use App\Models\MongoQuestionAnswerLike;
use App\Models\QuestionAnswer;
use App\Models\QuestionAnswerLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class QuestionAnswerLikeController extends Controller
{

    public function store(Request $request)
    {
        $questionAnswer = MongoCategoryComment::find($request->question_answer_id);
        $like_count = $questionAnswer->like_count ?? 0;
        $unlike_count = $questionAnswer->unlike_count ?? 0;
        if (!$questionAnswer) {
            return response()->json(['error' => 'Comment not found'], 404);
        }
        if ($request->like_or_unlike == "true") {
            $status = $this->addLikeOrUnlike($request, true);
            if ($status == 0) {
                $like_count -= 1;
            } elseif ($status == 1) {
                $like_count += 1;
            } elseif ($status == 2) {
                $like_count += 1;
                $unlike_count -= 1;
            }
        } else {
            $status = $this->addLikeOrUnlike($request, false);
            if ($status == 0) {
                $unlike_count -= 1;
            } elseif ($status == 1) {
                $unlike_count += 1;
            } elseif ($status == 2) {
                $unlike_count += 1;
                $like_count -= 1;
            }
        }
        $questionAnswer->like_count = $like_count;
        $questionAnswer->unlike_count = $unlike_count;
        $questionAnswer->update();

        $store_answer_cache_key = 'q_hot_ans' . $questionAnswer->question_id;
        if (!Cache::has($store_answer_cache_key)) {
            Cache::put($store_answer_cache_key, true, 1800); // lock for 30 minutes
            dispatch(new ChangeHotAnswer($questionAnswer->question_id))->onQueue('becharkhsite')->delay(now()->addMinutes(30));
        }

        return response()->json([
            'likecount' => $like_count,
            'unlikecount' => $unlike_count,
        ]);
    }

    private function addLikeOrUnlike($request, $status)
    {
        $like = MongoCategoryCommentLike::where('comment_id', $request->question_answer_id)->where('ip', $request->ip())->first();
        if (isset($like)) {
            if ($like->like_or_unlike == $status) {
                $like->delete();
                return 0;
            } else {
                $like->like_or_unlike = $status;
                $like->update();
                return 2;
            }
        } else {
            $like = new MongoCategoryCommentLike();
            $like->comment_id = $request->question_answer_id;
            $like->ip = $request->ip();
            $like->like_or_unlike = $status;
            $like->save();
            return 1;
        }
    }
}
