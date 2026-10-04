<?php

namespace App\Jobs\Item;

use App\Models\MongoCategoryComment;
use App\Models\MongoItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class UpdateHotItems implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Cache::forget('hot_items');
        Cache::rememberForever('hot_items', function () {
            $item_ids = MongoCategoryComment::orderBy('created_at', 'desc')
                ->where('category_id', '!=', null)
                ->where('items', '!=', null)
                ->take(60)
                ->pluck('items')
                ->flatten()
                ->unique()
                ->take(40);
            $items = MongoItem::whereIn('_id', $item_ids)
                ->where('status', 1)
                ->select('category_id', 'title', 'full_title', 'with_parent_url', 'images')
                ->with(['category' => function ($query) {
                    $query->select('title', 'full_title', 'image');
                }])->get();
            return $items;
        });
    }
}
