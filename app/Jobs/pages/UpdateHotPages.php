<?php

namespace App\Jobs\pages;

use App\Models\Affilate;
use App\Models\MongoCategoryComment;
use App\Models\MongoItem;
use App\Models\MongoQuestion;
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
            //category comments take 30
            $category_comments = MongoCategoryComment::orderBy('created_at', 'desc')
                ->whereNull('parent_id')
                ->where('items', '!=', null)
                ->whereNull('question_id')
                ->take(150)
                ->get();
            $processed_item_ids = [];
            $fccom_count = 0;
            foreach ($category_comments as $cc) {
                if (isset($cc->items_title) && !empty($cc->items_title)) {
                    $item = MongoItem::find($cc->items[0]);
                    if ($item && !in_array($item->id, $processed_item_ids)) {
                        $new_page = new stdClass();
                        $category = $item->category;
                        if (isset($category) && $category->is_cat_in_title == 1) {
                            $title = $category->title . ' ' . $item->withParentsTitle();
                        } else {
                            $title = $item->withParentsTitle();
                        }
                        $title .= ' | نظرات + تجربیات + مشکلات';
                        $new_page->title = $title;
                        $new_page->body = str_limit($cc->body, 100, '...');
                        $new_page->url = $item->withParentsCommentUrl();
                        $new_page->image = $item->image();
                        $new_page->time = $cc->created_at->format('Y-m-d H:i:s');
                        $hot_pages->add($new_page);
                        $processed_item_ids[] = $item->id;
                        $fccom_count += 1;
                        if ($fccom_count >= 25) {
                            break;
                        }
                    }
                }
            }
            //questions take 25
            $processed_question_ids = [];
            $question_commetns = MongoCategoryComment::orderBy('created_at', 'desc')
                ->whereNull('parent_id')
                ->where('question_id', '!=', null)
                ->take(250)
                ->get();
            $unique_question_ids = [];
            $filtered_qcomments = collect();
            foreach ($question_commetns as $qcom) {
                if (!in_array($qcom->question_id, $unique_question_ids)) {
                    $unique_question_ids[] = $qcom->question_id;
                    $filtered_qcomments->add($qcom);
                }
                if (count($filtered_qcomments) >= 10) {
                    break;
                }
            }
            foreach ($filtered_qcomments as $qc) {
                $question = MongoQuestion::find($qc->question_id);
                if ($question &&  $question->status == 1 && $question->google_index == 1 && !in_array($question->id, $processed_question_ids)) {
                    $new_page = new stdClass();
                    $new_page->title = $question->sug_title ?? $question->title;
                    $new_page->body = str_limit($qc->body, 100, '...');
                    $question_route = route('question.show', $question->slug2);
                    $new_page->url = 'https://becharkh.com' . str_replace('http://localhost', '', $question_route);
                    if ($question->getImage()) {
                        $new_page->image = $question->image();
                    } else {
                        if (!$question->getItems()->isEmpty()) {
                            $new_page->image = $question->getItems()->last()->image();
                        } else {
                            $new_page->image = $question->category->image();
                        }
                    }
                    $new_page->time = $qc->created_at->format('Y-m-d H:i:s');
                    $hot_pages->add($new_page);
                    $processed_question_ids[] = $question->id;
                }
            }
            //products take 30
            // $processed_product_ids = [];
            // $product_commetns = ProductComment::orderBy('created_at', 'desc')
            //     ->whereNull('parent_id')
            //     ->take(30)
            //     ->get();
            // foreach ($product_commetns as $pc) {
            //     $product = Affilate::find($pc->product_id);
            //     if ($product && !in_array($product->id, $processed_product_ids)) {
            //         $new_page = new stdClass();
            //         $new_page->title = $product->title;
            //         $new_page->body = str_limit($pc->body, 100, '...');
            //         $product_route = route('product.show', $product->slug);
            //         $new_page->url = 'https://becharkh.com' . str_replace('http://localhost', '', $product_route);
            //         $new_page->image = $product->image;
            //         $new_page->time = $pc->created_at->format('Y-m-d H:i:s');
            //         $hot_pages->add($new_page);
            //         $processed_product_ids[] = $product->id;
            //     }
            // }
            $hot_pages = $hot_pages->sortByDesc(function ($page) {
                return $page->time;
            });
            $hot_pages = $hot_pages->values()->take(35);
            return $hot_pages;
        });
    }
}
