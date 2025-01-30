<?php

namespace App\Jobs\pages;

use App\Models\Affilate;
use App\Models\MongoBlog;
use App\Models\MongoBlogComment;
use App\Models\MongoCategoryComment;
use App\Models\MongoItem;
use App\Models\MongoQuestion;
use App\Models\MongoQuestionAnswer;
use App\Models\ProductComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use stdClass;

class UpdateHotPages implements ShouldQueue
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
        Cache::forget('hot_pages');
        Cache::rememberForever('hot_pages', function () {
            $hot_pages = collect();
            $category_comments = MongoCategoryComment::orderBy('created_at', 'desc')
                ->whereNull('parent_id')
                ->where('items', '!=', null)
                ->take(30)
                ->get();
            $processed_item_ids = [];
            foreach ($category_comments as $cc) {
                if (isset($cc->items_title) && !empty($cc->items_title)) {
                    $item = MongoItem::find($cc->items[0]);
                    if ($item && !in_array($item->id, $processed_item_ids)) {
                        $new_page = new stdClass();
                        $new_page->title = "نظرات در مورد " . $item->withParentsTitle();
                        $new_page->body = $cc->body;
                        $new_page->url = $item->withParentsCommentUrl();
                        $new_page->time = $cc->created_at->format('Y-m-d H:i:s');
                        $hot_pages->add($new_page);
                        $processed_item_ids[] = $item->id;
                    }
                }
            }
            $processed_blog_ids = [];
            $blog_comments = MongoBlogComment::orderBy('created_at', 'desc')
                ->whereNull('parent_id')
                ->take(50)
                ->get();
            foreach ($blog_comments as $bc) {
                $blog = MongoBlog::find($bc->blog_id);
                if ($blog && !in_array($blog->id, $processed_blog_ids)) {
                    $new_page = new stdClass();
                    $new_page->title = $blog->title;
                    $new_page->body = str_limit($bc->body, 100, '...');
                    $blog_route = route('blog.show', [
                        'category_slug' => $blog->category->slug,
                        'slug' => $blog->slug,
                        'random_id' => $blog->random_id
                    ]);
                    $new_page->url = 'https://becharkh.com' . str_replace('http://localhost', '', $blog_route);
                    $new_page->time = $bc->created_at->format('Y-m-d H:i:s');
                    $hot_pages->add($new_page);
                    $processed_blog_ids[] = $blog->id;
                }
            }
            $processed_question_ids = [];
            $question_commetns = MongoQuestionAnswer::orderBy('created_at', 'desc')
                ->whereNull('parent_id')
                ->take(30)
                ->get();
            foreach ($question_commetns as $qc) {
                $question = MongoQuestion::find($qc->question_id);
                if ($question && !in_array($question->id, $processed_question_ids)) {
                    $new_page = new stdClass();
                    $new_page->title = $question->title;
                    $new_page->body = str_limit($qc->body, 100, '...');
                    $question_route = route('question.show', ['category' => $question->category->slug, 'slug' => $question->slug, 'random' => $question->random_id]);
                    $new_page->url = 'https://becharkh.com' . str_replace('http://localhost', '', $question_route);
                    $new_page->time = $qc->created_at->format('Y-m-d H:i:s');
                    $hot_pages->add($new_page);
                    $processed_question_ids[] = $question->id;
                }
            }
            $processed_product_ids = [];
            $product_commetns = ProductComment::orderBy('created_at', 'desc')
                ->whereNull('parent_id')
                ->take(30)
                ->get();
            foreach ($product_commetns as $pc) {
                $product = Affilate::find($pc->product_id);
                if ($product && !in_array($product->id, $processed_product_ids)) {
                    $new_page = new stdClass();
                    $new_page->title = $product->title;
                    $new_page->body = str_limit($pc->body, 100, '...');
                    $product_route = route('product.show', $product->slug);
                    $new_page->url = 'https://becharkh.com' . str_replace('http://localhost', '', $product_route);
                    $new_page->time = $pc->created_at->format('Y-m-d H:i:s');
                    $hot_pages->add($new_page);
                    $processed_product_ids[] = $product->id;
                }
            }
            $hot_pages = $hot_pages->sortByDesc(function ($page) {
                return $page->time;
            });
            $hot_pages = $hot_pages->values()->take(30);
            return $hot_pages;
        });
    }
}
