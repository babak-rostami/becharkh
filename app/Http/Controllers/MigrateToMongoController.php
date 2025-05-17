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
use App\Models\CategoryFeature;
use App\Models\CategoryFeatureItem;
use App\Models\Chat;
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
use App\Models\Shahr;
use App\Models\SiteCategory;
use App\Models\User;
use App\Models\UserNotification;
use App\Models\UserOrder;
use App\Models\Video;
use App\Models\Work;
use App\Notifications\UserNotif;
use App\Services\Elasticsearch;
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

        // $this->createElasticIndexes();
        // $this->createCategoryElasticDocuments();
        // $this->createItemElasticDocuments();
        // $this->createQuestionElasticDocuments();
        // $this->createBlogElasticDocuments();

        // dd('d');

        // $this->blogImage();

        // $this->questionHotAnswer();

        // $this->itemPageCount();

        // $this->updateItemPriority();

        dd("done");
    }

    private function generateIphoneSimilarSearch($model)
    {
        $similarSearches = [];

        $base = str_replace("iPhone", "آیفون", $model);
        $variations = [$base];

        if (stripos($model, 'Pro Max') !== false) {
            $variations[] = str_replace("Pro Max", "پرومکس", $base);
            $variations[] = str_replace("Pro Max", "پرو مکس", $base);
        } elseif (stripos($model, 'Pro') !== false) {
            $variations[] = str_replace("Pro", "پرو", $base);
        }

        if (stripos($model, 'Plus') !== false) {
            $variations[] = str_replace("Plus", "پلاس", $base);
        }

        if (stripos($model, 'Mini') !== false) {
            $variations[] = str_replace("Mini", "مینی", $base);
        }

        if (stripos($model, 'XS') !== false) {
            $variations[] = str_replace("XS", "ایکس اس", $base);
        }

        if (stripos($model, 'SE') !== false) {
            $variations[] = str_replace("SE", "اس ای", $base);
        }

        if (stripos($model, 'XR') !== false) {
            $variations[] = str_replace("XR", "ایکس ار", $base);
            $variations[] = str_replace("XR", "ایکس آر", $base);
        }

        if (preg_match('/\bX\b/', $model)) {
            $variations[] = str_replace("X", "ایکس", $base);
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
                    $question->answer = str_limit($like_answer->body, 65, '...');
                    $question->update();
                } else {
                    $question->answer = str_limit($unlike_answer->body, 65, '...');
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

    public function createQuestionElasticDocuments()
    {
        $client = new Elasticsearch();
        MongoQuestion::chunk(200, function ($questions) use ($client) {
            foreach ($questions as $question) {
                $client->createDocument('questions', $question->id, [
                    'title' => $question->title,
                ]);
            }
        });
    }

    public function createBlogElasticDocuments()
    {
        $client = new Elasticsearch();
        MongoBlog::chunk(200, function ($blogs) use ($client) {
            foreach ($blogs as $blog) {
                $client->createDocument('blogs', $blog->id, [
                    'title' => $blog->title,
                ]);
            }
        });
    }

    public function createItemElasticDocuments()
    {
        $client = new Elasticsearch();
        MongoItem::chunk(200, function ($items) use ($client) {
            foreach ($items as $item) {
                if ($item->feature->is_in_filter_rtable == 1) {
                    $client->createDocument('items', $item->id, [
                        'similar_search' => $item->similar_search,
                    ]);
                }
            }
        });
    }

    public function createCategoryElasticDocuments()
    {
        $client = new Elasticsearch();
        MongoCategory::chunk(200, function ($categories) use ($client) {
            foreach ($categories as $category) {
                $client->createDocument('categories', $category->id, [
                    'similar_search' => $category->similar_search
                ]);
            }
        });
    }

    public function createElasticIndexes()
    {
        $elastic = new Elasticsearch();

        $category_index_name = MongoCategory::$elasticIndexName;
        $category_field = MongoCategory::$elasticField;
        $elastic->createIndex($category_index_name, $category_field);

        $item_index_name = MongoItem::$elasticIndexName;
        $item_field = MongoItem::$elasticField;
        $elastic->createIndex($item_index_name, $item_field);

        $blog_index_name = MongoBlog::$elasticIndexName;
        $blog_field = MongoBlog::$elasticField;
        $elastic->createIndex($blog_index_name, $blog_field);

        $question_index_name = MongoQuestion::$elasticIndexName;
        $question_field = MongoQuestion::$elasticField;
        $elastic->createIndex($question_index_name, $question_field);
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
        MongoQuestionAnswer::raw(function ($collection) {
            $collection->createIndex([
                'question_id' => 1,
                'reply_to_id' => 1,
                'created_at' => -1
            ]);
        });
        //////////////////////question likes
        MongoQuestionLike::raw(function ($collection) {
            $collection->createIndex([
                'question_id' => 1,
                'user_id' => 1
            ]);
        });
        //////////////////////question answer likes
        MongoQuestionAnswerLike::raw(function ($collection) {
            $collection->createIndex([
                'answer_id' => 1,
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

    public function videoThumb()
    {
        $vimages = MongoVideo::where('thum', '!=', null)->get();
        dd($vimages);
        foreach ($vimages as $video) {
            // dd($video);
            $image_w2 = explode('.webp', $video->imageAttr())[0] . '2.webp';
            $this->moveFtpFile($video->imageAttr(), $image_w2);
            // $video->image = $video->thum;
            // $video->update();
            $video->unset('thum');
        }
    }

    public function emptyFilePathVideo()
    {
        $videos = MongoVideo::where('youtube_link', '!=', null)->get();
        foreach ($videos as $video) {
            $formats = $video->formats;
            foreach ($formats as $format) {
                if ($format['file_path'] != null) {
                    $video->updateFormat($format['format_id'], null, null, -1, null);
                }
            }
        }
    }

    public function testYoutube()
    {
        // $video = MongoVideo::find('6688f1ca288df6228e00d7b5');
        // $path = 'files/yfiles/';
        // $outputDir = public_path($path);
        // $filePath = $outputDir . 'test.mp4';
        // $download_merge_process = new Process([
        //     'yt-dlp',
        //     '-f', 136 . '+' . 140,
        //     '-o', $filePath,
        //     $video->youtube_link
        // ]);

        // $download_merge_process->start();
        // $download_merge_process->wait();
        $format_proccess = new Process(['yt-dlp', '--list-formats', 'https://youtu.be/0s-mh_rLe1I?si=XTM5fRZrJi1HrNwd']);
        $format_proccess->start();
        $format_proccess->wait();

        dd(trim($format_proccess->getOutput()));
    }

    public function videoFixFormat()
    {
        $videos = MongoVideo::where('youtube_link', '!=', null)->get();
        foreach ($videos as $video) {
            $def_for = null;
            $def_filename = null;
            $formats = $video->formats;
            foreach ($formats as $format) {
                if (Str::lower($format['video_format']) === "720p") {
                    $def_for = 136;
                    $def_filename = $format['filename'];
                    break;
                }
            }
            if ($def_for == null) {
                foreach ($formats as $format) {
                    if (Str::lower($format['video_format']) === "360p") {
                        $def_for = 134;
                        $def_filename = $format['filename'];
                    }
                }
            }
            if ($def_for == null) {
                foreach ($formats as $format) {
                    if (Str::lower($format['video_format']) === "240p") {
                        $def_for = 133;
                        $def_filename = $format['filename'];
                    }
                }
            }
            $formats = [];
            $new_format = [
                'format_id' => $def_for,
                'file_path' => null,
                'filename' => $def_filename,
            ];
            $formats[] = $new_format;
            $video->formats = $formats;
            $video->update();
        }
    }

    public function commentFixBugs()
    {
        // $comments = CategoryComment::all();
        // foreach ($comments as $comment) {
        //     if (count($comment->featureValues) == 0) {
        //         $new_com = MongoCategoryComment::where('last_id', $comment->id)->first();
        //         $new_com->unset('items');
        //     }
        // }
        $new_coms = MongoCategoryComment::where('items', '!=', null)->get();
        foreach ($new_coms as $nc) {
            $items_title = [];
            foreach ($nc->getItems() as $i) {
                $items_title[] = $i->full_title ?? $i->title;
            }
            $nc->items_title = $items_title;
            $nc->update();
        }
    }

    function moveFtpFile($oldPath, $newFullPath)
    {
        $disk = Storage::disk('ftp');
        $disk->copy($oldPath, $newFullPath);
        $disk->delete($oldPath);
    }

    public function commentImageToArray()
    {
        $comments = MongoCategoryComment::where('images', '!=', null)->get();
        foreach ($comments as $comment) {
            $images = [];
            $images[] = $comment->images;
            $comment->images = $images;
            $comment->update();
        }
    }


    public function videoFiles()
    {
        $videos = MongoVideo::where('youtube_link', '!=', null)->get();
        foreach ($videos as $video) {
            foreach ($video->formats as $format) {
                // dump($format);
                $video->updateFormat($format['format_id'], null, null, -1, null);
            }
        }
    }

    public function userOrders()
    {
        $orders = UserOrder::all();
        foreach ($orders as $order) {
            $user = MongoUser::where('last_id', $order->user_id)->first();
            $newOrder = new MongoUserOrder();
            $newOrder->user_id = $user->id;
            $newOrder->transaction_number = $order->transaction_number;
            $newOrder->payment_reference_id = $order->payment_reference_id;
            $newOrder->amount = $order->amount;
            $newOrder->status = $order->status;
            $newOrder->order_type_id = $order->order_type_id;
            $newOrder->order_type_class = $order->order_type_class;
            $newOrder->created_at = $order->created_at;
            $newOrder->updated_at = $order->updated_at;
            $newOrder->save();
        }
    }

    public function userFollow()
    {
        $users = User::all();
        foreach ($users as $user) {
            $followers = $user->followers;
            $followings = $user->followings;
            $muser = MongoUser::where('last_id', $user->id)->first();
            if (count($followers) > 0) {
                foreach ($user->followers as $follow) {
                    $otherUser = MongoUser::where('last_id', $follow->user_1)->first();
                    $newUserFollow = new MongoUserFollow();
                    $newUserFollow->user_1 = $otherUser->id;
                    $newUserFollow->user_2 = $muser->id;
                    $newUserFollow->save();
                }
                $muser->follower_count = count($followers);
            }
            if (count($followings) > 0) {
                $muser->following_count = count($followings);
            }
            if (count($followers) > 0 || count($followings) > 0) {
                $muser->update();
            }
        }
    }

    public function advertiseLocation()
    {
        $advertises = MongoAdvertise::all();
        foreach ($advertises as $advertise) {
            $ostan = Ostan::find($advertise->ostan);
            $city = Shahr::find($advertise->city);
            $advertise->location = $ostan->title . ' - ' . $city->title;
            $advertise->update();
        }
    }

    public function userMoney()
    {
        $users = User::all();
        foreach ($users as $user) {
            $nuser = MongoUser::where('last_id', $user->id)->first();
            if ($user->userMoney() != null) {
                $nuser->money = $user->userMoney();
            } else {
                $nuser->money = 0;
            }
            $nuser->update();
        }
    }
    public function chat()
    {
        $chats = Chat::all();
        foreach ($chats as $chat) {
            $newConversation = new MongoConversation();
            $u1 = MongoUser::where('last_id', $chat->user_1)->first();
            $u2 = MongoUser::where('last_id', $chat->user_2)->first();
            $newConversation->user_1 = $u1->id;
            $newConversation->user_2 = $u2->id;
            $newConversation->created_at = $chat->created_at;
            $newConversation->updated_at = $chat->updated_at;
            $newConversation->save();
            foreach ($chat->messages as $cm) {
                $cmu = MongoUser::where('last_id', $cm->sender_id)->first();
                $newChat = new MongoChat();
                $newChat->message = $cm->message;
                $newChat->conversation_id = $newConversation->id;
                $newChat->sender_id = $cmu->id;
                $newChat->created_at = $cm->created_at;
                $newChat->updated_at = $cm->updated_at;
                $newChat->save();

                $lastMessages = $newConversation->last_messages ?: [];
                $lastMessages[] = $newChat->toArray();
                if (count($lastMessages) > 25) {
                    $lastMessages = array_slice($lastMessages, -25);
                }
                $newConversation->last_messages = $lastMessages;
                $newConversation->update();
            }
        }
    }

    public function works()
    {
        $works = Work::all();
        foreach ($works as $work) {
            $newWork = new MongoWork();
            $newWork->last_id = $work->id;
            $newWork->title = $work->title;
            $newWork->title_en = $work->title_en;
            $newWork->slug = $work->slug;
            if ($work->image) {
                $newWork->image = $work->image;
            }
            if ($work->body) {
                $newWork->body = $work->body;
            }
            if ($work->similar_search) {
                $newWork->similar_search = $work->similar_search;
            }
            $newWork->save();
        }
    }

    public function userWorks()
    {
        $users = User::all();
        foreach ($users as $user) {
            if (count($user->userJobs) > 0) {
                $uw = [];
                foreach ($user->userJobs as $userjob) {
                    $uw[] = $userjob->work_id;
                }
            }
            if (isset($uw) && count($uw) > 0) {
                $u = MongoUser::where('last_id', $user->id)->first();
                $u->works = $uw;
                $u->update();
            }
        }
    }

    public function categoryCommentItemsTitle()
    {
        $comments = MongoCategoryComment::all();
        foreach ($comments as $comment) {
            if (isset($comment->items)) {
                $fv = [];
                foreach ($comment->getItems() as $item) {
                    if (isset($item->full_title)) {
                        $fv[] = $item->full_title;
                    } else {
                        $fv[] = $item->title;
                    }
                }
                if (count($fv) > 0) {
                    $comment->items_title = $fv;
                    $comment->update();
                }
            }
        }
    }

    public function questionAnswerCount()
    {
        $qs = MongoQuestion::all();
        foreach ($qs as $q) {
            $q->answer_count = count($q->answers);
            $q->update();
        }
    }

    public function addItemWithParentUrl()
    {
        $items = MongoItem::all();
        foreach ($items as $item) {
            $category = $item->category;
            $wpu = '/' . $category->slug . '?s=1&' . $this->getparentUrl($item);
            $item->with_parent_url = $wpu;
            $item->update();
        }
    }

    private function getparentUrl($item)
    {
        $url = null;
        $feature = $item->feature;
        if (isset($feature) && $feature->is_in_filter_rtable) {
            if ($item->parent_id != null) {
                $url .= $this->getparentUrl($item->parent) . "&" . $feature->slug . "=" . $item->slug;
            } else {
                $url = $feature->slug . "=" . $item->slug;
            }
            return $url;
        }
    }

    public function blogVideo()
    {
        $blogVideos = BlogVideo2::all();
        foreach ($blogVideos as $bv) {
            $blog = MongoBlog::where('last_id', $bv->blog_id)->first();
            $video = MongoVideo::where('last_id', $bv->video_id)->first();
            $blog->videos = $video->id;
            $blog->update();
        }
    }
    public function videoAdvertise()
    {
        $advertises = MongoAdvertise::where('videos', '!=', null)->get();
        foreach ($advertises as $advertise) {
            $video = MongoVideo::find($advertise->videos);
            $ads = [];
            $ads[] = $advertise->id;
            $video->advertises = $ads;
            $video->update();
        }
    }
    public function advertiseVideo()
    {
        $adVideos = AdvertiseVideo::all();
        foreach ($adVideos as $av) {
            $advertise = MongoAdvertise::where('last_id', $av->ad_id)->first();
            $video = MongoVideo::where('last_id', $av->video_id)->first();
            $advertise->videos = $video->id;
            $advertise->update();
        }
    }

    public function advertiseFeatureValues()
    {
        $advertises = Advertise::all();
        foreach ($advertises as $advertise) {
            $newAdvertise = MongoAdvertise::where('last_id', $advertise->id)->first();
            if (count($advertise->featureValues) > 0) {
                $fv = [];
                foreach ($advertise->featureValues as $fvalue) {
                    $feature = MongoFeature::where('last_id', $fvalue->feature_id)->first();
                    if ($feature->last_id == 1 || $feature->last_id == 2) {
                        $ii = MongoItem::where('last_id', intval($fvalue->value))->first();
                        if (isset($ii)) {
                            $fv[] = $ii->id;
                        }
                    }
                }
            }
            if (isset($fv) && count($fv) > 0) {
                $newAdvertise->items = $fv;
                $newAdvertise->update();
            }
        }
    }

    public function advertise()
    {
        $advertises = Advertise::all();
        foreach ($advertises as $advertise) {
            $ac = MongoCategory::where('last_id', $advertise->category_id)->first();
            $au = MongoUser::where('last_id', $advertise->user_id)->first();
            $newAdvertise = new MongoAdvertise();
            $newAdvertise->last_id = $advertise->id;
            $newAdvertise->title = $advertise->title;
            $newAdvertise->slug = $advertise->slug;
            $newAdvertise->random_id = $advertise->random_id;
            $newAdvertise->seen_count = $advertise->seen_count;
            $newAdvertise->body = $advertise->body;
            if ($advertise->site_link) {
                $newAdvertise->site_link = $advertise->site_link;
            }
            if ($advertise->price) {
                $newAdvertise->price = $advertise->price;
            }
            if ($advertise->phone) {
                $newAdvertise->phone = $advertise->phone;
            }
            $newAdvertise->ostan = $advertise->ostan;
            $newAdvertise->city = $advertise->city;
            $newAdvertise->status = $advertise->status;
            $newAdvertise->category_id = $ac->id;
            $newAdvertise->user_id = $au->id;
            $newAdvertise->created_at = $advertise->created_at;
            $newAdvertise->updated_at = $advertise->updated_at;

            $newAdvertise->save();
        }
    }

    public function advertiseImage()
    {
        $advertises = Advertise::all();
        foreach ($advertises as $advertise) {
            $adImages = $advertise->images;
            if (count($adImages) > 0) {
                $ad = MongoAdvertise::where('last_id', $advertise->id)->first();
                $aimages = [];
                foreach ($adImages as $image) {
                    $aimages[] = $image->image;
                }
                $ad->images = $aimages;
                $ad->update();
            }
        }
    }

    public function blog()
    {
        $blogs = Blog::all();
        foreach ($blogs as $blog) {
            $bc = MongoCategory::where('last_id', $blog->category_id)->first();
            $bu = MongoUser::where('last_id', $blog->user_id)->first();
            $newBlog = new MongoBlog();
            $newBlog->last_id = $blog->id;
            $newBlog->category_id = $bc->id;
            $newBlog->title = $blog->title;
            $newBlog->slug = $blog->slug;
            $newBlog->seen_count = $blog->seen_count;
            $newBlog->image = $blog->image;
            $newBlog->thum = $blog->thum;
            $newBlog->content = $blog->content;
            $newBlog->short_description = $blog->short_description;
            $newBlog->status = $blog->status;
            $newBlog->user_id = $bu->id;
            $newBlog->random_id = $blog->random_id;
            $newBlog->google_index = $blog->google_index;

            $likeCount = count($blog->likes);
            if ($likeCount > 0) {
                $newBlog->like_count = $likeCount;
            }
            $unlikeCount = count($blog->unlikes);
            if ($unlikeCount > 0) {
                $newBlog->unlike_count = $unlikeCount;
            }
            if (count($blog->featureValues) > 0) {
                $fv = [];
                foreach ($blog->featureValues as $value) {
                    $ii = MongoItem::where('last_id', $value->item_id)->first();
                    $fv[] = $ii->id;
                }
            }
            if (isset($fv) && count($fv) > 0) {
                $newBlog->items = $fv;
            }

            $newBlog->updated_at = $blog->updated_at;
            $newBlog->created_at = $blog->created_at;
            $newBlog->save();
            foreach ($blog->likeAndUnlikes as $vlu) {
                $llu = MongoUser::where('last_id', $vlu->user_id)->first();
                $lu = new MongoBlogLike();
                $lu->blog_id = $newBlog->id;
                $lu->user_id = $llu->id;
                $lu->like_or_unlike = $vlu->like_or_unlike;
                $lu->save();
            }
        }
    }

    public function blogCommentCount()
    {
        $blogs = MongoBlog::all();
        foreach ($blogs as $blog) {
            $comments_count = count($blog->comments);
            if ($comments_count > 0) {
                $blog->comment_count = $comments_count;
                $blog->update();
            }
        }
    }

    public function blogComment()
    {
        $allComments = BlogComment::all();
        foreach ($allComments as $comment) {
            $mb = MongoBlog::where('last_id', $comment->blog_id)->first();

            if ($comment->user_id == null) {
                $user = MongoUser::where('username', preg_replace('~[^\pL\d]+~u', '-', $comment->name))->first();
                if (!isset($user)) {
                    $user_id = app(UserController::class)->fakeRegisterSend($comment->name, preg_replace('~[^\pL\d]+~u', '-', $comment->name));
                } else {
                    $user_id = $user->id;
                }
            } else {
                $u = MongoUser::where('last_id', $comment->user_id)->first();
                $user_id = $u->id;
            }

            $newComment = new MongoBlogComment();
            $newComment->last_id = $comment->id;
            $newComment->blog_id = $mb->id;
            $newComment->user_id = $user_id;
            if ($comment->parent_id) {
                $pc = MongoBlogComment::where('last_id', $comment->parent_id)->first();
                $newComment->parent_id = $pc->id;
            }
            if ($comment->reply_to_id) {
                $pr = MongoBlogComment::where('last_id', $comment->reply_to_id)->first();
                $newComment->reply_to_id = $pr->id;
            }
            $likeCount = count($comment->likes);
            if ($likeCount > 0) {
                $newComment->like_count = $likeCount;
            }
            $unlikeCount = count($comment->unlikes);
            if ($unlikeCount > 0) {
                $newComment->unlike_count = $unlikeCount;
            }
            $newComment->body = $comment->body;
            $newComment->created_at = $comment->created_at;
            $newComment->updated_at = $comment->updated_at;
            $newComment->save();

            foreach ($comment->likeAndUnlikes as $vlu) {
                $llu = MongoUser::where('last_id', $vlu->user_id)->first();
                $lu = new MongoBlogCommentLike();
                $lu->comment_id = $newComment->id;
                $lu->user_id = $llu->id;
                $lu->like_or_unlike = $vlu->like_or_unlike;
                $lu->save();
            }
        }
    }

    public function video()
    {
        $videos = Video::all();
        foreach ($videos as $video) {
            $vc = MongoCategory::where('last_id', $video->category_id)->first();
            $vu = MongoUser::where('last_id', $video->user_id)->first();
            $newVideo = new MongoVideo();
            $newVideo->last_id = $video->id;
            $newVideo->title = $video->title;
            $newVideo->slug = $video->slug;
            $newVideo->description = $video->description;
            $newVideo->video_path = $video->video_path;
            $newVideo->random_id = $video->random_id;
            $newVideo->google_index = $video->google_index;
            $newVideo->status = $video->status;
            $newVideo->image = $video->image;
            $newVideo->thum = $video->thum;
            $newVideo->seen_count = $video->seen_count;
            $newVideo->category_id = $vc->id;
            $newVideo->user_id = $vu->id;
            if ($video->max_size) {
                $newVideo->max_size = $video->max_size;
            }
            if ($video->youtube_link) {
                $newVideo->youtube_link = $video->youtube_link;
            }
            $likeCount = count($video->likes);
            if ($likeCount > 0) {
                $newVideo->like_count = $likeCount;
            }
            $unlikeCount = count($video->unlikes);
            if ($unlikeCount > 0) {
                $newVideo->unlike_count = $unlikeCount;
            }
            if (count($video->featureValues) > 0) {
                $fv = [];
                foreach ($video->featureValues as $value) {
                    $ii = MongoItem::where('last_id', $value->item_id)->first();
                    $fv[] = $ii->id;
                }
            }
            if (isset($fv) && count($fv) > 0) {
                $newVideo->items = $fv;
            }
            $newVideo->created_at = $video->created_at;
            $newVideo->updated_at = $video->updated_at;
            $newVideo->save();
            foreach ($video->likeAndUnlikes as $vlu) {
                $llu = MongoUser::where('last_id', $vlu->user_id)->first();
                $lu = new MongoVideoLike();
                $lu->video_id = $newVideo->id;
                $lu->user_id = $llu->id;
                $lu->like_or_unlike = $vlu->like_or_unlike;
                $lu->save();
            }
            foreach ($video->allComments as $comment) {
                $cu = MongoUser::where('last_id', $comment->user_id)->first();
                $newComment = new MongoVideoComment();
                $newComment->last_id = $comment->id;
                $newComment->video_id = $newVideo->id;
                $newComment->user_id = $cu->id;
                if ($comment->parent_id) {
                    $pc = MongoVideoComment::where('last_id', $comment->parent_id)->first();
                    if (isset($pc)) {
                        $newComment->parent_id = $pc->id;
                    }
                }
                if ($comment->reply_to_id) {
                    $pr = MongoVideoComment::where('last_id', $comment->reply_to_id)->first();
                    if (isset($pr)) {
                        $newComment->reply_to_id = $pr->id;
                    }
                }
                $newComment->body = $comment->body;
                $newComment->save();
            }
        }
    }

    public function youtubeFormats()
    {
        $videos = MongoVideo::where('youtube_link', '!=', null)->get();
        foreach ($videos as $video) {
            $lastVideo = Video::find($video->last_id);
            $formats = $lastVideo->yformats;
            $formats = $formats->map(function ($category) {
                return [
                    'format_id' => $category->format_id,
                    'video_format' => $category->video_format,
                    'video_size' => $category->video_size,
                    'file_path' => $category->file_path,
                    'filename' => $category->filename
                ];
            })->toArray();
            $video->formats = $formats;
            $video->update();
        }
    }

    public function question()
    {
        $questinos = Question::all();
        foreach ($questinos as $lastQuestion) {
            $nu = MongoUser::where('last_id', $lastQuestion->user_id)->first();
            $nc = MongoCategory::where('last_id', $lastQuestion->category_id)->first();
            $newQuestion = new MongoQuestion();
            $newQuestion->last_id = $lastQuestion->id;
            $newQuestion->title = $lastQuestion->title;
            $newQuestion->slug = $lastQuestion->slug;
            $newQuestion->seen_count  = $lastQuestion->seen_count;
            $newQuestion->body = $lastQuestion->body;
            if ($lastQuestion->editor) {
                $newQuestion->editor = $lastQuestion->editor;
            }
            $newQuestion->random_id = $lastQuestion->random_id;
            $newQuestion->status = $lastQuestion->status;
            $newQuestion->google_index = $lastQuestion->google_index;
            $likeCount = count($lastQuestion->likes1);
            if ($likeCount > 0) {
                $newQuestion->like_count = $likeCount;
            }
            $newQuestion->category_id = $nc->id;
            $newQuestion->user_id = $nu->id;
            $fv = [];
            $fv_titles = [];
            if (count($lastQuestion->featureValues) > 0) {
                foreach ($lastQuestion->featureValues as $value) {
                    $ii = MongoItem::where('last_id', $value->item_id)->first();
                    $fv[] = $ii->id;
                    $fv_titles[] = $ii->full_title ?? $ii->title;
                }
            }
            if (count($fv) > 0) {
                $newQuestion->items = $fv;
                $newQuestion->items_title = $fv_titles;
            }
            $newQuestion->created_at = $lastQuestion->created_at;
            $newQuestion->updated_at = $lastQuestion->updated_at;
            $newQuestion->save();
            //for question likes
            foreach ($lastQuestion->likes1 as $like) {
                $u = MongoUser::where('last_id', $like->user_id)->first();
                $lu = new MongoQuestionLike();
                $lu->question_id = $newQuestion->id;
                $lu->user_id = $u->id;
                $lu->save();
            }
        }
    }

    public function questionAnswer()
    {
        $answers = QuestionAnswer::all();
        foreach ($answers as $answer) {
            if ($answer->user_id == null) {
                $user = MongoUser::where('username', preg_replace('~[^\pL\d]+~u', '-', $answer->name))->first();
                if (!isset($user)) {
                    $user_id = app(UserController::class)->fakeRegisterSend($answer->name, preg_replace('~[^\pL\d]+~u', '-', $answer->name));
                } else {
                    $user_id = $user->id;
                }
            } else {
                $u = MongoUser::where('last_id', $answer->user_id)->first();
                $user_id = $u->id;
            }
            $qId = MongoQuestion::where('last_id', $answer->question_id)->first();
            $newAnswer = new MongoQuestionAnswer();
            $newAnswer->last_id = $answer->id;
            $newAnswer->question_id = $qId->id;
            $newAnswer->user_id = $user_id;
            if ($answer->reply_to_id) {
                $reply = MongoQuestionAnswer::where('last_id', $answer->reply_to_id)->first();
                if (isset($reply)) {
                    $newAnswer->reply_to_id = $reply->id;
                }
            }
            $newAnswer->body = $answer->body;
            $likeCount = count($answer->likes1);
            if ($likeCount > 0) {
                $newAnswer->like_count = $likeCount;
            }
            $unlikeCount = count($answer->unLikes1);
            if ($unlikeCount > 0) {
                $newAnswer->unlike_count = $unlikeCount;
            }
            $newAnswer->created_at = $answer->created_at;
            $newAnswer->updated_at = $answer->updated_at;
            $newAnswer->save();
            foreach ($answer->lukes as $clu) {
                $lu = new MongoQuestionAnswerLike();
                $lu->answer_id = $newAnswer->id;
                if ($clu->ip) {
                    $lu->ip = $clu->ip;
                }
                if ($clu->user_id) {
                    $lu->user_id = $clu->user_id;
                }
                $lu->like_or_unlike = $clu->like_or_unlike;
                $lu->save();
            }
        }
    }

    public function user()
    {
        $users = User::all();
        foreach ($users as $user) {
            $newUser = new MongoUser();
            $newUser->last_id = $user->id;
            $newUser->name = $user->name;
            $newUser->username = $user->username;
            $newUser->email = $user->email;
            $newUser->password = $user->password;
            if ($user->phone) {
                $user->phone = $user->phone;
            }
            if ($user->image) {
                $newUser->image = $user->image;
            }
            if ($user->body) {
                $newUser->body = $user->body;
            }
            $newUser->email_actived = $user->email_actived;
            $newUser->save();
        }
    }

    public function category()
    {
        $lastCategories = SiteCategory::where('parent_id', null)->get();
        foreach ($lastCategories as $lcategory) {
            $nCategory = new MongoCategory();
            $nCategory->last_id = $lcategory->id;
            $nCategory->title = $lcategory->title;
            $nCategory->title_en = $lcategory->title_en;
            $nCategory->slug = $lcategory->slug;
            $nCategory->image = $lcategory->image;
            $nCategory->status = $lcategory->status;
            $nCategory->has_ads = $lcategory->has_ads;
            $nCategory->has_forums = $lcategory->has_forums;
            $nCategory->has_comments = $lcategory->has_comments;
            $nCategory->is_cat_in_title = $lcategory->is_cat_in_title;
            $nCategory->title_in_rtable = $lcategory->title_in_rtable;
            $nCategory->desc_in_rtable = $lcategory->desc_in_rtable;
            $nCategory->title_in_comment = $lcategory->title_in_comment;
            $nCategory->desc_in_comment = $lcategory->desc_in_comment;
            $nCategory->title_in_ads = $lcategory->title_in_ads;
            $nCategory->desc_in_ads = $lcategory->desc_in_ads;
            $nCategory->cost_description = $lcategory->cost_description;
            $nCategory->save();
            $this->catChildren($lcategory, $nCategory);
        }
    }
    private function catChildren($category, $parent)
    {
        foreach ($category->children as $ccategory) {
            $nCategory = new MongoCategory();
            $nCategory->parent_id = $parent->id;
            $nCategory->last_id = $ccategory->id;
            $nCategory->title = $ccategory->title;
            $nCategory->title_en = $ccategory->title_en;
            $nCategory->full_title = $ccategory->withParentsTitle();
            $nCategory->slug = $ccategory->slug;
            $nCategory->image = $ccategory->image;
            $nCategory->status = $ccategory->status;
            $nCategory->has_ads = $ccategory->has_ads;
            $nCategory->has_forums = $ccategory->has_forums;
            $nCategory->has_comments = $ccategory->has_comments;
            $nCategory->is_cat_in_title = $ccategory->is_cat_in_title;
            $nCategory->title_in_rtable = $ccategory->title_in_rtable;
            $nCategory->desc_in_rtable = $ccategory->desc_in_rtable;
            $nCategory->title_in_comment = $ccategory->title_in_comment;
            $nCategory->desc_in_comment = $ccategory->desc_in_comment;
            $nCategory->title_in_ads = $ccategory->title_in_ads;
            $nCategory->desc_in_ads = $ccategory->desc_in_ads;
            $nCategory->cost_description = $ccategory->cost_description;
            $nCategory->save();
            $this->catChildren($ccategory, $nCategory);
        }
    }

    public function feature()
    {
        $lastFeatures = CategoryFeature::where('parent_id', null)->get();

        foreach ($lastFeatures as $lfeature) {
            $newFeature = new MongoFeature();
            $category = MongoCategory::where('last_id', $lfeature->categories->pluck('id')->toArray()[0])->first();
            $newFeature->last_id = $lfeature->id;
            $cat_ids = [];
            $cat_ids[] = $category->id;
            $newFeature->cat_ids = $cat_ids;
            $newFeature->title = $lfeature->title;
            $newFeature->title_en = $lfeature->title_en;
            $newFeature->slug = $lfeature->slug;
            $newFeature->status = $lfeature->status;
            $newFeature->is_in_filter_rtable = $lfeature->is_in_filter_rtable;
            $newFeature->is_important_in_ad = $lfeature->is_important_in_ad;
            $newFeature->input_type = $lfeature->input_type;
            $newFeature->is_in_filter_ad = $lfeature->is_in_filter_ad;
            $newFeature->is_in_page_title = $lfeature->is_in_page_title;
            $newFeature->has_follow = $lfeature->has_follow;
            $newFeature->has_items = $lfeature->has_items;
            $newFeature->is_feature_in_title = $lfeature->is_feature_in_title;
            $newFeature->item_is_in_title = $lfeature->item_is_in_title;
            $newFeature->item_is_in_title_if_not_parent = $lfeature->item_is_in_title_if_not_parent;
            $newFeature->is_in_child_cats = $lfeature->is_in_child_cats;
            $newFeature->be_indexed = $lfeature->be_indexed;
            $newFeature->select_items_count = $lfeature->select_items_count;
            $newFeature->save();
            $this->featureChildren($lfeature, $newFeature);
        }
    }

    private function featureChildren($feature, $parent)
    {
        $category = MongoCategory::where('last_id', $feature->categories->pluck('id')->toArray()[0])->first();
        foreach ($feature->children as $lfeature) {
            $newFeature = new MongoFeature();
            $newFeature->last_id = $lfeature->id;
            $cat_ids = [];
            $cat_ids[] = $category->id;
            $newFeature->cat_ids = $cat_ids;
            $newFeature->parent_id = $parent->id;
            $newFeature->title = $lfeature->title;
            $newFeature->title_en = $lfeature->title_en;
            $newFeature->slug = $lfeature->slug;
            $newFeature->status = $lfeature->status;
            $newFeature->is_in_filter_rtable = $lfeature->is_in_filter_rtable;
            $newFeature->is_important_in_ad = $lfeature->is_important_in_ad;
            $newFeature->input_type = $lfeature->input_type;
            $newFeature->is_in_filter_ad = $lfeature->is_in_filter_ad;
            $newFeature->is_in_page_title = $lfeature->is_in_page_title;
            $newFeature->has_follow = $lfeature->has_follow;
            $newFeature->has_items = $lfeature->has_items;
            $newFeature->is_feature_in_title = $lfeature->is_feature_in_title;
            $newFeature->item_is_in_title = $lfeature->item_is_in_title;
            $newFeature->item_is_in_title_if_not_parent = $lfeature->item_is_in_title_if_not_parent;
            $newFeature->is_in_child_cats = $lfeature->is_in_child_cats;
            $newFeature->be_indexed = $lfeature->be_indexed;
            $newFeature->select_items_count = $lfeature->select_items_count;
            $newFeature->save();
            $this->featureChildren($lfeature, $newFeature);
        }
    }

    public function item()
    {
        $lastItems = CategoryFeatureItem::where('parent_id', null)->get();
        foreach ($lastItems as $litem) {
            $newItem = new MongoItem();
            $feature = MongoFeature::where('last_id', $litem->feature_id)->first();
            $category = MongoCategory::find($feature->categories->pluck('id')->toArray()[0]);
            $newItem->last_id = $litem->id;
            $newItem->category_id = $category->id;
            $newItem->feature_id = $feature->id;
            $newItem->title = $litem->title;
            $newItem->title_en = $litem->title_en;
            $newItem->slug = $litem->slug;
            $newItem->status = $litem->status;
            $newItem->similar_search = $litem->similar_search;
            if (count($litem->images) > 0) {
                $images = [];
                foreach ($litem->images as $image) {
                    array_push($images, $image->image);
                }
                $newItem->images = $images;
            }
            $newItem->save();
            $this->itemChildren($litem, $newItem);
        }
    }

    private function itemChildren($item, $parent)
    {
        foreach ($item->children as $litem) {
            $feature = MongoFeature::where('last_id', $litem->feature_id)->first();
            $newItem = new MongoItem();
            $newItem->last_id = $litem->id;
            $newItem->feature_id = $feature->id;
            $newItem->parent_id = $parent->id;
            $newItem->title = $litem->title;
            $newItem->title_en = $litem->title_en;
            $newItem->full_title = $litem->withParentsTitle();
            $newItem->slug = $litem->slug;
            $newItem->status = $litem->status;
            $newItem->similar_search = $litem->similar_search;
            if (count($litem->images) > 0) {
                $images = [];
                foreach ($litem->images as $image) {
                    array_push($images, $image->image);
                }
                $newItem->images = $images;
            }
            $newItem->save();
            $this->itemChildren($litem, $newItem);
        }
    }

    public function categoryComment()
    {
        $lastComments = CategoryComment::all();
        foreach ($lastComments as $c) {
            if ($c->parent_id == null) {
                if ($c->user_id == null) {
                    $user = MongoUser::where('username', preg_replace('~[^\pL\d]+~u', '-', $c->name))->first();
                    if (!isset($user)) {
                        $user_id = app(UserController::class)->fakeRegisterSend($c->name, preg_replace('~[^\pL\d]+~u', '-', $c->name));
                    } else {
                        $user_id = $user->id;
                    }
                } else {
                    $u = MongoUser::where('last_id', $c->user_id)->first();
                    $user_id = $u->id;
                }
                $fv = [];
                if (count($c->featureValues) > 0) {
                    foreach ($c->featureValues as $value) {
                        $ii = MongoItem::where('last_id', $value->item_id)->first();
                        $fv[] = $ii->id;
                    }
                }
                $ci = MongoCategory::where('last_id', $c->category_id)->first();
                $comment = new MongoCategoryComment();
                $comment->last_id = $c->id;
                $comment->category_id = $ci->id;
                $comment->user_id = $user_id;
                $comment->body = $c->body;
                if (isset($fv) && count($fv) > 0) {
                    $comment->items = $fv;
                }
                if (count($c->unLikes1) > 0) {
                    $comment->unlike_count = count($c->unLikes1);
                }
                if (count($c->likes1) > 0) {
                    $comment->like_count = count($c->likes1);
                }
                if ($c->has_img && $c->image) {
                    $comment->images = $c->image->image;
                }
                $comment->created_at = $c->created_at;
                $comment->updated_at = $c->updated_at;
                $comment->save();
                foreach ($c->lukes as $clu) {
                    $lu = new MongoCategoryCommentLike();
                    $lu->comment_id = $comment->id;
                    if ($clu->ip) {
                        $lu->ip = $clu->ip;
                    }
                    if ($clu->user_id) {
                        $lu->user_id = $clu->user_id;
                    }
                    $lu->like_or_unlike = $clu->like_or_unlike;
                    $lu->save();
                }
                foreach ($c->replies as $reply) {
                    if ($reply->user_id == null) {
                        $reply_user = MongoUser::where('username', preg_replace('~[^\pL\d]+~u', '-', $reply->name))->first();
                        if (!isset($reply_user)) {
                            $reply_user_id = app(UserController::class)->fakeRegisterSend($reply->name, preg_replace('~[^\pL\d]+~u', '-', $reply->name));
                        } else {
                            $reply_user_id = $reply_user->id;
                        }
                    } else {
                        $u = MongoUser::where('last_id', $reply->user_id)->first();
                        $reply_user_id = $u->id;
                    }
                    $commentReply = new MongoCategoryComment();
                    $commentReply->last_id = $reply->id;
                    $commentReply->user_id = $reply_user_id;
                    $commentReply->body = $reply->body;
                    $commentReply->parent_id = $comment->id;
                    $commentReply->created_at = $reply->created_at;
                    $commentReply->updated_at = $reply->updated_at;
                    if (count($reply->unLikes1) > 0) {
                        $commentReply->unlike_count = count($reply->unLikes1);
                    }
                    if (count($reply->likes1) > 0) {
                        $commentReply->like_count = count($reply->likes1);
                    }
                    $commentReply->save();
                    foreach ($reply->lukes as $rlu) {
                        $lu = new MongoCategoryCommentLike();
                        $lu->comment_id = $commentReply->id;
                        if ($rlu->ip) {
                            $lu->ip = $rlu->ip;
                        }
                        if ($rlu->user_id) {
                            $lu->user_id = $rlu->user_id;
                        }
                        $lu->like_or_unlike = $rlu->like_or_unlike;
                        $lu->save();
                    }
                }
            }
        }
    }
}
