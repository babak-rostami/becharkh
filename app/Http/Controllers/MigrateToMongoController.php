<?php

namespace App\Http\Controllers;

use App\Jobs\Item\ChangeItemPageCount;
use App\Jobs\pages\UpdateHotPages;
use App\Models\Advertise;
use App\Models\AdvertiseVideo;
use App\Models\Affilate;
use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\BlogVideo2;
use App\Models\CategoryComment;
use App\Models\CategoryCommentEditorImage;
use App\Models\CategoryFeature;
use App\Models\CategoryFeatureItem;
use App\Models\Chat;
use App\Models\ItemForTopUser;
use App\Models\MongoAdvertise;
use App\Models\MongoBlog;
use App\Models\MongoBlogComment;
use App\Models\MongoBlogCommentLike;
use App\Models\MongoBlogLike;
use App\Models\MongoCategory;
use App\Models\MongoCategoryComment;
use App\Models\MongoCategoryCommentLike;
use App\Models\MongoChat;
use App\Models\MongoConversation;
use App\Models\MongoFeature;
use App\Models\MongoFollowItem;
use App\Models\MongoItem;
use App\Models\MongoQuestion;
use App\Models\MongoQuestionAnswer;
use App\Models\MongoQuestionAnswerLike;
use App\Models\MongoQuestionLike;
use App\Models\MongoUser;
use App\Models\MongoUserFollow;
use App\Models\MongoUserMedal;
use App\Models\MongoUserOrder;
use App\Models\MongoVideo;
use App\Models\MongoVideoComment;
use App\Models\MongoVideoLike;
use App\Models\MongoWork;
use App\Models\Ostan;
use App\Models\ProductComment;
use App\Models\Question;
use App\Models\QuestionAnswer;
use App\Models\QuestionAnswerEditorImage;
use App\Models\Shahr;
use App\Models\SiteCategory;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\UserOrder;
use App\Models\Video;
use App\Models\Work;
use App\Services\Elasticsearch;
use DOMDocument;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use stdClass;
use Symfony\Component\Process\Process;

class MigrateToMongoController extends Controller
{

    public function start()
    {
        set_time_limit(3600);
        // $this->user();
        // $this->userOrders();
        // $this->category();
        // $this->feature();
        // $this->item();
        // $this->addItemWithParentUrl();
        // $this->categoryComment();
        // $this->categoryCommentItemsTitle();
        // $this->question();
        // $this->questionAnswer();
        // $this->questionAnswerCount();
        // $this->video();
        // $this->youtubeFormats();
        // $this->blog();
        // $this->blogComment();
        // $this->blogCommentCount();
        // $this->advertise();
        // $this->advertiseFeatureValues();
        // $this->advertiseLocation();
        // $this->advertiseImage();
        // $this->blogVideo();
        // $this->advertiseVideo();
        // $this->videoAdvertise();
        // $this->works();
        // $this->userWorks();
        // $this->chat();
        // $this->userMoney();
        // $this->userFollow();
        // $this->commentImageToArray();
        // $this->videoFiles();
        // $this->commentFixBugs();

        // $this->videoFixFormat();
        // $this->testYoutube();
        // $this->videoThumb();
        // $this->emptyFilePathVideo();
        // $this->createIndexes();

        // $this->followItems();

        // $this->test();

        // dd('d');

        // $this->blogImage();

        // $this->questionHotAnswer();

        // $this->itemPageCount();

        // $this->updateItemPriority();

        // $this->updateUserFollowItem();

        // $this->convertAnswerToCcomment();

        // $this->addCategoryQuestions();


        // ۱) همه عکس‌ها رو بر اساس comment_id گروه‌بندی کن
        // $commentImagesGrouped = CategoryCommentEditorImage::whereNotNull('comment_id')
        //     ->get()
        //     ->groupBy('comment_id');

        // foreach ($commentImagesGrouped as $comment_id => $images) {
        //     $comment = MongoCategoryComment::find($comment_id);

        //     if (!$comment) {
        //         continue; // اگر کامنت حذف شده بود
        //     }

        //     // آرایه iimages اگه وجود نداره ایجاد کن
        //     $iimages = $comment->iimages ?? [];

        //     // پیدا کردن بزرگترین کلید موجود در iimages
        //     $existingKeys = array_keys($iimages);
        //     $maxKey = empty($existingKeys) ? -1 : max($existingKeys);

        //     foreach ($images as $image) {
        //         $maxKey++;
        //         $iimages[$maxKey] = [
        //             'path' => $image->path,
        //             'id'   => $maxKey,
        //         ];
        //     }

        //     // به‌روزرسانی کامنت و ذخیره
        //     $comment->iimages = $iimages;
        //     $comment->save();
        // }

        // $comments = MongoCategoryComment::where('images', '!=', null)->get();
        // foreach ($comments as $comment) {
        //     $iimages = [];
        //     $iimages[0] = [
        //         'path' => $comment->images[0],
        //         'id'   => 0,
        //     ];
        //     $comment->iimages = $iimages;
        //     $comment->update();
        // }

        // $comments = MongoCategoryComment::where('iimages', '!=', null)->get();
        // foreach ($comments as $comment) {
        //     if (strpos($comment->editor, '<figure') !== false) {
        //         $cleanedEditor = preg_replace('/<figure[^>]*>.*?<\/figure>/si', '', $comment->editor);

        //         if ($cleanedEditor !== $comment->editor) {
        //             $comment->editor = $cleanedEditor;
        //             $comment->update();
        //         }
        //     }
        // }

        // $affilates = Affilate::all();

        // foreach ($affilates as $affilate) {
        //     $doc = new DOMDocument();
        //     @$doc->loadHTML('<?xml encoding="UTF-8">' . $affilate->body);

        //     $imgs = $doc->getElementsByTagName('img');
        //     $images = [];
        //     $i = 0;

        //     foreach ($imgs as $img) {
        //         $base = 'https://dl.becharkh.com/user_files/';

        //         $data_src_full = $img->getAttribute('data-src');
        //         $src_full = $img->getAttribute('src');

        //         $final_full = $data_src_full ?: $src_full;

        //         if (str_starts_with($final_full, $base)) {
        //             $path = substr($final_full, strlen($base));

        //             if (!empty($path)) {
        //                 $images[] = [
        //                     'id' => $i++,
        //                     'path' => $path,
        //                 ];
        //             }
        //         }
        //     }

        //     $affilate->iimages = $images;
        //     $affilate->update();
        // }

        // $affilates = Affilate::all();
        // foreach ($affilates as $affilate) {
        //     if (strpos($affilate->body, '<figure') !== false) {
        //         $cleanedEditor = preg_replace('/<figure[^>]*>.*?<\/figure>/si', '', $affilate->body);

        //         if ($cleanedEditor !== $affilate->body) {
        //             $affilate->body = $cleanedEditor;
        //             $affilate->update();
        //         }
        //     }
        // }


        // $items = MongoItem::where('top_users', '!=', null)->get();
        // foreach ($items as $item) {
        //     $item->unset('top_users');
        // }

        // $item_for_top_user = new ItemForTopUser();
        // $item_for_top_user->item_id = '66821a5910cf783aeb0f0c2f';
        // $item_for_top_user->save();

        dd("done");
    }
    private function addCategoryQuestions()
    {
        $data = [
            [
                'title' => 'ارزش خرید داره؟',
                'desc' => 'توی این رنج قیمتی به نظرتون ارزش خرید داره؟'
            ],
            [
                'title' => 'چیزایی که تجربه کردین',
                'desc' => 'تجربه ای داشتین که ممکنه به درد بقیه هم بخوره؟'
            ],
            [
                'title' => 'مشکلی پیش اومده؟',
                'desc' => 'بپرس، شاید برای بقیه هم پیش اومده باشه و کمکت کنن'
            ],
            [
                'title' => 'خدمات پس از فروش',
                'desc' => 'قطعات راحت پیدا میشه؟ نمایندگی خدماتش خوبه؟'
            ],
            [
                'title' => 'هزینه نگهداری',
                'desc' => 'هزینه نگهداری و تعمیراتش چطوره؟'
            ],
            [
                'title' => 'عملکرد موتور',
                'desc' => 'شتاب و کشش موتور راضی‌کننده‌ست؟'
            ],
            [
                'title' => 'مصرف سوخت',
                'desc' => 'مصرف سوخت توی شهر و جاده چطوره؟'
            ],
            [
                'title' => 'داخل کابین',
                'desc' => 'صندلی‌ها راحتن؟ فضا برای سرنشین‌ها خوبه؟'
            ],
            [
                'title' => 'مقایسه با رقبا',
                'desc' => 'اگه برگردی عقب، باز همینو برمی‌داری؟'
            ]
        ];

        $category = MongoCategory::where('slug', 'car')->first();

        if ($category) {
            $category->tips = $data;
            $category->update();
        }
    }

    private function convertAnswerToCcomment()
    {
        $qca = MongoCategoryComment::where('question_id', '!=', null)->where('newq', '!=', 1)->get();
        foreach ($qca as $qa) {
            foreach ($qa->likes as $like) {
                $like->delete();
            }
            foreach ($qa->unlikes as $unlike) {
                $unlike->delete();
            }
            $edimgs = CategoryCommentEditorImage::where('comment_id', $qa->id)->get();
            foreach ($edimgs as $eimg) {
                $eimg->delete();
            }
            $qa->delete();
        }

        $answers = MongoQuestionAnswer::all();
        foreach ($answers as $answer) {
            $ccomment = new MongoCategoryComment();
            if (isset($answer->body)) {
                $ccomment->body = $answer->body;
            }
            if (isset($answer->editor)) {
                $ccomment->editor = $answer->editor;
            }
            if (isset($answer->parent_id)) {
                $ccomment->last_parent_id = $answer->parent_id;
            }
            $ccomment->question_id = $answer->question_id;
            $ccomment->user_id = $answer->user_id;
            if (isset($answer->like_count)) {
                $ccomment->like_count = $answer->like_count;
            }
            if (isset($answer->unlike_count)) {
                $ccomment->unlike_count = $answer->unlike_count;
            }
            $ccomment->created_at = $answer->created_at;
            $ccomment->updated_at = $answer->updated_at;
            $ccomment->last_id = $answer->id;
            $ccomment->save();

            $likes = MongoQuestionAnswerLike::where('answer_id', $answer->id)->get();
            foreach ($likes as $like) {
                $new_like = new MongoCategoryCommentLike();
                $new_like->comment_id = $ccomment->id;
                $new_like->ip = $like->ip;
                $new_like->like_or_unlike = $like->like_or_unlike;
                $new_like->save();
            }

            $editor_images = QuestionAnswerEditorImage::where('comment_id', $answer->id)->get();
            foreach ($editor_images as $ei) {
                $new_ed = new CategoryCommentEditorImage();
                $new_ed->path = $ei->path;
                $new_ed->comment_id = $ccomment->id;
                $new_ed->save();
            }
        }

        // update parent_id
        $new_replies = MongoCategoryComment::where('last_parent_id', '!=', null)->get();
        foreach ($new_replies as $reply) {
            $new_parent = MongoCategoryComment::where('last_id', $reply->last_parent_id)->first();
            if (isset($new_parent)) {
                $reply->parent_id = $new_parent->id;
                $reply->update();
            }
        }
    }

    private function updateUserFollowItem()
    {
        $comments = MongoCategoryComment::orderBy('created_at', 'desc')
            ->where('items', '!=', null)
            ->where('user_id', '!=', null)
            ->get();
        foreach ($comments as $cm) {
            $items = $cm->items ?? [];
            if (!empty($items)) {
                $item_id = $items[0];
                if (!MongoFollowItem::where('user_id', $cm->user_id)->where('item_id', $item_id)->exists()) {
                    $ufi = new MongoFollowItem();
                    $ufi->user_id = $cm->user_id;
                    $ufi->item_id = $item_id;
                    $ufi->save();
                }
            }
        }
        $answers = MongoCategoryComment::where('parent_id', null)->get();
        foreach ($answers as $ans) {
            $question = $ans->question;
            if (!$question) {
                return;
            }
            $items = $question->items ?? [];
            if (!empty($items)) {
                $item_id = $items[0];
                if (!MongoFollowItem::where('user_id', $ans->user_id)->where('item_id', $item_id)->exists()) {
                    $ufi = new MongoFollowItem();
                    $ufi->user_id = $ans->user_id;
                    $ufi->item_id = $item_id;
                    $ufi->save();
                }
            }
        }
    }

    private function generateIphoneSimilarSearch($model)
    {
        $similarSearches = [];

        $base = Str::replace("iPhone", "آیفون", $model);
        $variations = [$base];

        if (stripos($model, 'Pro Max') !== false) {
            $variations[] = Str::replace("Pro Max", "پرومکس", $base);
            $variations[] = Str::replace("Pro Max", "پرو مکس", $base);
        } elseif (stripos($model, 'Pro') !== false) {
            $variations[] = Str::replace("Pro", "پرو", $base);
        }

        if (stripos($model, 'Plus') !== false) {
            $variations[] = Str::replace("Plus", "پلاس", $base);
        }

        if (stripos($model, 'Mini') !== false) {
            $variations[] = Str::replace("Mini", "مینی", $base);
        }

        if (stripos($model, 'XS') !== false) {
            $variations[] = Str::replace("XS", "ایکس اس", $base);
        }

        if (stripos($model, 'SE') !== false) {
            $variations[] = Str::replace("SE", "اس ای", $base);
        }

        if (stripos($model, 'XR') !== false) {
            $variations[] = Str::replace("XR", "ایکس ار", $base);
            $variations[] = Str::replace("XR", "ایکس آر", $base);
        }

        if (preg_match('/\bX\b/', $model)) {
            $variations[] = Str::replace("X", "ایکس", $base);
        }

        $similarSearches = implode(" ", array_unique($variations));

        return $similarSearches;
    }

    // $this->clearHotItems();

    public function updateItemPriority()
    {
        $items = MongoItem::all();
        foreach ($items as $item) {
            $comment_count = $item->comment_count ?? 0;
            $question_count = $item->question_count ?? 0;
            $item_priority  = $comment_count  + $question_count;
            if ($item_priority > 0) {
                $item->priority = $item_priority;
                $item->update();
            }
        }
    }

    public function itemPageCount()
    {
        $comments = MongoCategoryComment::all();
        foreach ($comments as $comment) {
            if (!isset($comment->parent_id) && isset($comment->items) && count($comment->items) > 0) {
                foreach ($comment->items as $item_id) {
                    $item = MongoItem::find($item_id);
                    if ($item) {
                        $item->comment_count = ($item->comment_count ?? 0) + 1;
                        $item->update();
                    }
                }
            }
        }
        $questions = MongoQuestion::all();
        foreach ($questions as $question) {
            if (isset($question->items) && count($question->items) > 0) {
                foreach ($question->items as $item_id) {
                    $item = MongoItem::find($item_id);
                    if ($item) {
                        $item->question_count = ($item->question_count ?? 0) + 1;
                        $item->update();
                    }
                }
            }
        }
        $advertises = MongoAdvertise::all();
        foreach ($advertises as $advertise) {
            if (isset($advertise->items) && count($advertise->items) > 0) {
                foreach ($advertise->items as $item_id) {
                    $item = MongoItem::find($item_id);
                    if ($item) {
                        $item->advertise_count = ($item->advertise_count ?? 0) + 1;
                        $item->update();
                    }
                }
            }
        }
    }

    public function clearHotItems()
    {
        $this->updateItemPriority();
        $items = MongoItem::where('suggest_items', '!=', null)->get();
        foreach ($items as $item) {
            $item->unset('suggest_items');
        }
    }

    public function hotItems()
    {
        Cache::forget('hot_items');
        Cache::rememberForever('hot_items', function () {
            $item_ids = MongoCategoryComment::orderBy('created_at', 'desc')
                ->where('category_id', '!=', null)
                ->where('items', '!=', null)
                ->take(20)
                ->pluck('items')
                ->flatten()
                ->unique();
            $items = MongoItem::whereIn('_id', $item_ids)
                ->where('status', 1)
                ->select('category_id', 'title', 'full_title', 'with_parent_url', 'images')
                ->with(['category' => function ($query) {
                    $query->select('title', 'full_title', 'image');
                }])
                ->get();
            return $items;
        });
    }

    public function questionHotAnswer()
    {
        $questions = MongoQuestion::all();
        foreach ($questions as $question) {
            if (count($question->answers) > 0) {
                $like_answer = $question->answers->sortByDesc('like_count')->first();
                $unlike_answer = $question->answers->sortByDesc('unlike_count')->first();
                if ($like_answer->like_count > $unlike_answer->unlike_count) {
                    $question->answer = Str::limit($like_answer->body, 65, '...');
                    $question->update();
                } else {
                    $question->answer = Str::limit($unlike_answer->body, 65, '...');
                    $question->update();
                }
            }
        }
    }

    public function blogImage()
    {
        $disk = Storage::disk('ftp');
        $client = new Client();
        $blogs = MongoBlog::skip(150)->take(20)->get();
        foreach ($blogs as $blog) {
            $imageUrl = $blog->image();
            try {
                $client->head($imageUrl);
            } catch (\Throwable $th) {
                $thumb = $blog->thumb();
                try {
                    $client->head($thumb);
                    $image_path = $blog->getImage();
                    $thumb_path = explode('.webp', $image_path)[0] . '2.webp';
                    $disk->copy($thumb_path, $image_path);
                } catch (\Throwable $th) {
                    dump($blog->thumb());
                    dump($blog->title . 'not exist!');
                }
            }
        }
    }
    public function followItems()
    {
        //////second
        // $result = MongoFollowItem::groupBy('item_id')->pluck('item_id');
        // foreach ($result as $item_id) {
        //     $item_follow_count = MongoFollowItem::where('item_id', $item_id)->count();
        //     $item = MongoItem::find($item_id);
        //     $item->follow_count = $item_follow_count;
        //     $item->update();
        // }

        /////first
        // $lastFollowItems = FollowFeatureItem::all();
        // foreach ($lastFollowItems as $lfi) {
        //     $last_item = MongoItem::where('last_id', $lfi->item_id)->first();
        //     $last_user = MongoUser::where('last_id', $lfi->user_id)->first();
        //     if (isset($last_item) && isset($last_user)) {
        //         $ffi = new MongoFollowItem();
        //         $ffi->item_id = $last_item->id;
        //         $ffi->user_id = $last_user->id;
        //         $ffi->save();
        //     }
        // }
    }

    public function test()
    {
        // dd(Cache::forget('6696b3c475711e085b0c83a5' . "downloading"));
    }

    public function createIndexes()
    {
        ////////////////////////user notifications
        UserNotification::raw(function ($collection) {
            $collection->createIndex([
                'user_id' => 1
            ]);
        });
        UserNotification::raw(function ($collection) {
            $collection->createIndex([
                'type' => 1,
                'type_id' => 1
            ]);
        });
        ////////////////////////user medals
        MongoUserMedal::raw(function ($collection) {
            $collection->createIndex([
                'type' => 1,
                'item_id' => 1,
            ]);
        });
        ////////////////////////user follow items
        MongoFollowItem::raw(function ($collection) {
            $collection->createIndex([
                'user_id' => 1,
                'item_id' => 1,
            ]);
        });
        MongoAdvertise::raw(function ($collection) {
            $collection->createIndex(['user_id' => 1]);
        });
        ////////////////////////advertise
        MongoAdvertise::raw(function ($collection) {
            $collection->createIndex([
                'created_at' => -1,
                'category_id' => 1,
                'items' => 1
            ]);
        });
        MongoAdvertise::raw(function ($collection) {
            $collection->createIndex([
                'category_id' => 1,
                'slug' => 1,
                'random_id' => 1
            ]);
        });
        MongoAdvertise::raw(function ($collection) {
            $collection->createIndex(['user_id' => 1]);
        });
        ////////////////////////blog
        MongoBlog::raw(function ($collection) {
            $collection->createIndex([
                'created_at' => -1,
                'category_id' => 1,
                'items' => 1
            ]);
        });
        MongoBlog::raw(function ($collection) {
            $collection->createIndex([
                'category_id' => 1,
                'slug' => 1,
                'random_id' => 1
            ]);
        });
        MongoBlog::raw(function ($collection) {
            $collection->createIndex(['title' => 1]);
        });
        MongoBlog::raw(function ($collection) {
            $collection->createIndex(['slug' => 1]);
        });
        MongoBlog::raw(function ($collection) {
            $collection->createIndex(['user_id' => 1]);
        });
        ////////////////////////blog like
        MongoBlogLike::raw(function ($collection) {
            $collection->createIndex([
                'blog_id' => 1,
                'user_id' => 1
            ]);
        });
        //////////////////////category
        MongoCategory::raw(function ($collection) {
            $collection->createIndex(['slug' => 1]);
        });

        //////////////////////category comments
        MongoCategoryComment::raw(function ($collection) {
            $collection->createIndex([
                'created_at' => -1,
                'parent_id' => 1,
                'category_id' => 1,
                'items' => 1
            ]);
        });
        MongoCategoryComment::raw(function ($collection) {
            $collection->createIndex([
                'created_at' => -1,
                'parent_id' => 1,
                'user_id' => 1
            ]);
        });
        //////////////////////category comment likes
        MongoCategoryCommentLike::raw(function ($collection) {
            $collection->createIndex([
                'comment_id' => 1,
                'ip' => 1
            ]);
        });
        //////////////////////features
        MongoFeature::raw(function ($collection) {
            $collection->createIndex(['category_id' => 1]);
        });
        MongoFeature::raw(function ($collection) {
            $collection->createIndex(['slug' => 1]);
        });
        //////////////////////items
        MongoItem::raw(function ($collection) {
            $collection->createIndex(['category_id' => 1]);
        });
        MongoItem::raw(function ($collection) {
            $collection->createIndex(['slug' => 1]);
        });
        MongoItem::raw(function ($collection) {
            $collection->createIndex([
                'feature_id' => 1,
                'slug' => 1,
            ]);
        });
        //////////////////////question answers
        MongoCategoryComment::raw(function ($collection) {
            $collection->createIndex([
                'created_at' => -1,
                'parent_id' => 1,
                'question_id' => 1,
            ]);
        });
        MongoCategoryComment::raw(function ($collection) {
            $collection->createIndex(['question_id' => 1]);
        });
        //////////////////////question likes
        MongoQuestionLike::raw(function ($collection) {
            $collection->createIndex([
                'question_id' => 1,
                'user_id' => 1
            ]);
        });
        //////////////////////question answer likes
        MongoCategoryCommentLike::raw(function ($collection) {
            $collection->createIndex([
                'comment_id' => 1,
                'ip' => 1
            ]);
        });
        //////////////////////questions
        MongoQuestion::raw(function ($collection) {
            $collection->createIndex([
                'created_at' => -1,
                'category_id' => 1,
                'items' => 1
            ]);
        });
        MongoQuestion::raw(function ($collection) {
            $collection->createIndex(['just_this_page' => 1]);
        });
        MongoQuestion::raw(function ($collection) {
            $collection->createIndex(['slug2' => 1]);
        });
        MongoQuestion::raw(function ($collection) {
            $collection->createIndex(['user_id' => 1]);
        });
        MongoQuestion::raw(function ($collection) {
            $collection->createIndex([
                'created_at' => -1,
                'user_id' => 1
            ]);
        });
        //////////////////////affilates
        Affilate::raw(function ($collection) {
            $collection->createIndex([
                'created_at' => -1,
                'google_index' => 1,
                'status' => 1
            ]);
        });
        Affilate::raw(function ($collection) {
            $collection->createIndex([
                'slug' => 1,
                'status' => 1
            ]);
        });
        Affilate::raw(function ($collection) {
            $collection->createIndex([
                'items' => 1,
                'status' => 1
            ]);
        });
        Affilate::raw(function ($collection) {
            $collection->createIndex([
                'categories' => 1,
                'status' => 1
            ]);
        });
        Affilate::raw(function ($collection) {
            $collection->createIndex([
                'questions' => 1,
                'status' => 1
            ]);
        });
        Affilate::raw(function ($collection) {
            $collection->createIndex([
                'video_id' => 1,
                'status' => 1
            ]);
        });
        //////////////////////users
        MongoUser::raw(function ($collection) {
            $collection->createIndex(['username' => 1]);
        });
        MongoUser::raw(function ($collection) {
            $collection->createIndex(['email' => 1]);
        });
        //////////////////////video comments
        MongoVideoComment::raw(function ($collection) {
            $collection->createIndex(['video_id' => 1]);
        });
        //////////////////////videos
        MongoVideo::raw(function ($collection) {
            $collection->createIndex(['title' => 1]);
        });
        MongoVideo::raw(function ($collection) {
            $collection->createIndex(['slug' => 1]);
        });
        MongoVideo::raw(function ($collection) {
            $collection->createIndex(['random_id' => 1]);
        });
        MongoVideo::raw(function ($collection) {
            $collection->createIndex(['category_id' => 1]);
        });
        MongoVideo::raw(function ($collection) {
            $collection->createIndex(['user_id' => 1]);
        });
        MongoVideo::raw(function ($collection) {
            $collection->createIndex(['items' => 1]);
        });
        MongoVideo::raw(function ($collection) {
            $collection->createIndex(['youtube_link' => 1]);
        });
        MongoVideo::raw(function ($collection) {
            $collection->createIndex(['created_at' => -1]);
        });
    }
}
