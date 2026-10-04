<?php

namespace App\Jobs\User;

use App\Models\MongoCategoryComment;
use App\Models\MongoFollowItem;
use App\Models\MongoItem;
use App\Models\MongoQuestion;
use App\Models\MongoQuestionAnswer;
use App\Models\MongoUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class UpdateUserFollowItem implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $object_type, $object_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($object_type, $object_id)
    {
        $this->object_type = $object_type;
        $this->object_id = $object_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $object_type = $this->object_type;
        $object_id = $this->object_id;
        $user_id = null;

        if ($object_type == 'ccomment') {
            $comment = MongoCategoryComment::find($object_id);
            if (!isset($comment)) {
                return;
            }
            $items = $comment->items ?? [];
            $user_id = $comment->user_id;
        } elseif ($object_type == 'question_answer') {
            $answer = MongoCategoryComment::find($object_id);
            if (!$answer) {
                return;
            }
            $question = $answer->question;
            if (!$question) {
                return;
            }
            $items = $question->items ?? [];
            $user_id = $answer->user_id;
        }

        $user = MongoUser::find($user_id);
        if (!$user || $user->is_fake == 1) {
            return;
        }

        if (!empty($items)) {
            $item_id = $items[0];
            $item = MongoItem::find($item_id);
            if ($item && !MongoFollowItem::where('user_id', $user_id)->where('item_id', $item->id)->exists()) {
                $ufi = new MongoFollowItem();
                $ufi->user_id = $user_id;
                $ufi->item_id = $item->id;
                $ufi->save();
            }
        }

        $user_follows = MongoFollowItem::where('user_id', $user->id)->get();
        if ($user_follows->count() > 30) {
            $excess_follows = $user_follows->slice(30);
            foreach ($excess_follows as $follow) {
                $follow->delete();
            }
        }
    }
}
