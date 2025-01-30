<?php

namespace App\Jobs\Item;

use App\Models\MongoItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ChangeItemPageCount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $item_ids, $page, $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($item_ids, $page, $type)
    {
        $this->item_ids = $item_ids;
        $this->page = $page;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $items = MongoItem::whereIn('_id', $this->item_ids)->get();
        foreach ($items as $item) {
            if ($this->page == 'question') {
                $last_count = $item->question_count ?? 0;
                if ($this->type == 1) {
                    $item->question_count = $last_count + 1;
                } else {
                    $item->question_count = $last_count - 1;
                }
                $item->update();
            } elseif ($this->page == 'advertise') {
                $last_count = $item->advertise_count ?? 0;
                if ($this->type == 1) {
                    $item->advertise_count = $last_count + 1;
                } else {
                    $item->advertise_count = $last_count - 1;
                }
                $item->update();
            } elseif ($this->page == 'comment') {
                $last_count = $item->comment_count ?? 0;
                if ($this->type == 1) {
                    $item->comment_count = $last_count + 1;
                } else {
                    $item->comment_count = $last_count - 1;
                }
                $item->update();
            } elseif ($this->page == 'blog') {
                $last_count = $item->blog_count ?? 0;
                if ($this->type == 1) {
                    $item->blog_count = $last_count + 1;
                } else {
                    $item->blog_count = $last_count - 1;
                }
                $item->update();
            }
        }
    }
}
