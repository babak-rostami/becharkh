<?php

namespace App\Jobs\Question;

use App\Models\MongoQuestion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ChangeHotAnswer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $question_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($question_id)
    {
        $this->question_id = $question_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $question = MongoQuestion::find($this->question_id);
        $answers = $question->answers;
        if (count($answers) > 0) {
            $like_answer = $answers->sortByDesc('like_count')->first();
            $unlike_answer = $answers->sortByDesc('unlike_count')->first();
            if ($like_answer->like_count > $unlike_answer->unlike_count) {
                $question->answer = str_limit($like_answer->body, 65, '...');
                $question->update();
            } else {
                $question->answer = str_limit($unlike_answer->body, 65, '...');
                $question->update();
            }
        }
    }
}
