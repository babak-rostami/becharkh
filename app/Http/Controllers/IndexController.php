<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Brand;
use App\Models\MongoBlog;
use App\Models\MongoCategory;
use App\Models\MongoItem;
use App\Models\MongoQuestion;
use App\Models\MongoUser;
use App\Models\MongoVideo;
use App\Models\Ostan;
use App\Models\Question;
use App\Models\SiteCategory;
use App\Models\User;
use App\Models\UserSearch;
use App\Repositories\CategoryComment\Mongodb\CategoryCommentRepository;
use App\Services\Affilate\AffilateService;
use App\Services\Suggestion\SuggestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class IndexController extends Controller
{

    public function home(SuggestionService $suggestionService)
    {
        // $products = Affilate::orderBy('created_at', 'desc')->where('google_index', 1)->where('status', 1)->take(15)->get();

        $suggests = $suggestionService->suggest();
        $categories = $suggests['cats'];

        // $questions = MongoQuestion::where('status', 1)->orderBy('created_at', 'desc')->take(20)->with('user')->get();

        $hot_pages = Cache::get('hot_pages');

        return view('home', compact('hot_pages', 'categories'));
        // return view('home', compact('questions', 'products', 'categories', 'hot_pages'));
    }

    public function getCities(Request $request)
    {
        $ostan = Ostan::find($request->id);
        if (isset($ostan)) {
            echo '<option value=' . "all" . '>' . "همه ی شهر ها" . '</option>';
            foreach ($ostan->cities as $city) {
                if ($city->advertises->count() > 0) {
                    echo '<option value=' . $city->id . '>' . $city->title . '</option>';
                }
            }
        } else {
            echo '<option value=' . "all" . '>' . "همه ی شهر ها" . '</option>';
        }
    }

    public function getModels(Request $request)
    {
        $brand = Brand::where('nameEn', $request->nameEn)->first();
        if (isset($brand)) {
            echo '<option value=' . "" . '>' . "همه ی مدل ها" . '</option>';
            foreach ($brand->models as $model) {
                if ($model->cars->count() > 0) {
                    echo '<option value=' . $model->nameEn . '>' . $model->title . '</option>';
                }
            }
        } else {
            echo '<option value=' . "" . '>' . "همه ی مدل ها" . '</option>';
        }
    }

    public function getModelsReminder(Request $request)
    {
        $brand = Brand::find($request->id);
        if (isset($brand)) {
            echo '<option value=' . "" . '>' . "همه ی مدل ها" . '</option>';
            foreach ($brand->models as $model) {
                echo '<option value=' . $model->id . '>' . $model->title . '</option>';
            }
        } else {
            echo '<option value=' . "" . '>' . "همه ی مدل ها" . '</option>';
        }
    }

    public function getCitiesCreate(Request $request)
    {
        $ostan = Ostan::find($request->id);
        if (isset($ostan)) {
            echo '<option value=' . "" . '>' . "شهر را انتخاب کنید" . '</option>';
            foreach ($ostan->cities as $city) {
                echo '<option value=' . $city->id . '>' . $city->title . '</option>';
            }
        } else {
            echo '<option value=' . "" . '>' . "شهر را انتخاب کنید" . '</option>';
        }
    }

    public function getModelsCreate(Request $request)
    {
        $brand = Brand::find($request->id);
        if (isset($brand)) {
            echo '<option value=' . "" . '>' . "مدل را انتخاب کنید" . '</option>';
            foreach ($brand->models as $model) {
                echo '<option value=' . $model['id'] . '>' . $model->title . '</option>';
            }
        } else {
            echo '<option value=' . "" . '>' . "مدل را انتخاب کنید" . '</option>';
        }
    }


    public function getMainModels(Request $request)
    {
        $brand = Brand::where('nameEn', $request->id)->first();
        if (isset($brand)) {
            echo '<option class="d-none" value=' . "" . '>' . "مدل را انتخاب کنید" . '</option>';
            foreach ($brand->models as $model) {
                if ($model->details->count() > 0) {
                    echo '<option value=' . $model->nameEn . '>' . $model->title . '</option>';
                }
            }
        } else {
            echo '<option class="d-none" value=' . "" . '>' . "مدل را انتخاب کنید" . '</option>';
        }
    }

    private function convertPersianToEnglishNumerals($input)
    {
        $persianNumerals = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $englishNumerals = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return Str::replace($persianNumerals, $englishNumerals, $input);
    }

    public function mainSearch($type, $value = null)
    {
        if (!$value) {
            return;
        }
        if (!auth('admin')->check()) {
            $user_search = new UserSearch();
            $user_search->text = $value;
            $user_search->save();
        }

        $value = Str::lower($value);
        $value = $this->convertPersianToEnglishNumerals($value);

        if ($type == 1) {
            $item_ids = MongoItem::elSearch($value);
            $items = MongoItem::whereIn('_id', $item_ids)->where('status', 1)->with('category')->get();

            $cat_ids = MongoCategory::elSearch($value);
            $categories = MongoCategory::whereIn('_id', $cat_ids)->where('status', 1)->where('is_active', 1)->get();

            $items = $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->full_title ?? $item->title,
                    'cat' => $item->category->full_title ?? $item->category->title,
                    'c_url' => $item->category->has_comments ? $item->withParentsCommentUrl() : null,
                    'f_url' => $item->category->has_forums ? $item->withParentsForumUrl() : null,
                    // 'a_url' => $item->category->has_ads ? $item->withParentsAdvertiseUrl() : null,
                    'img' => $item->thumb()
                ];
            });

            $categories = $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'title' => $category->full_title ?? $category->title,
                    'url' => $category->has_comments ?
                        route('question.index', $category->slug) . "?s=1"
                        :
                        route('question.index', $category->slug),
                    'img' => $category->thumb()
                ];
            });
            return response()->json(['items' => $items, 'categories' => $categories], 200);
        } elseif ($type == 2) {
            $question_ids = MongoQuestion::elSearch($value);
            $questions = MongoQuestion::whereIn('_id', $question_ids)->where('status', 1)->get();
            $questions = $questions->map(function ($question) {
                return [
                    'id' => $question->id,
                    'title' => $question->title,
                    'url' => route('question.show', $question->slug2),
                ];
            });
            return response()->json(['questions' => $questions], 200);
        } elseif ($type == 3) {
            $blog_ids = MongoBlog::elSearch($value);
            $blogs = MongoBlog::whereIn('_id', $blog_ids)->where('status', 1)->get();
            $blogs = $blogs->map(function ($blog) {
                return [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'url' => route('blog.show', ['category_slug' => $blog->category->slug, 'slug' => $blog->slug, 'random_id' => $blog->random_id]),
                    'img' => $blog->thumb(),
                ];
            });
            return response()->json(['blogs' => $blogs], 200);
        } elseif ($type == 4) {
            $videos = MongoVideo::where('title', 'like', '%' . $value . '%')->where('status', 1)->take(6)->get();
            $videos = $videos->map(function ($video) {
                return [
                    'id' => $video->id,
                    'title' => $video->title,
                ];
            });
            return response()->json(['videos' => $videos], 200);
        }
    }

    public function searchCategoryForCreate($for, $value = null)
    {
        if (!isset($value)) {
            return '<li class="px-3 pt-2 pb-3 cat-result-a radius-10 text-center">جستجوی دسته بندی در بچرخ</li>';
        }

        if ($for == 1) {
            $categories = SiteCategory::where('status', 1)->where('has_ads', 1)->doesntHave('children')->get();
        } elseif ($for == 2) {
            $categories = SiteCategory::where('status', 1)->doesntHave('children')->get();
        }
        foreach ($categories as $key => $category) {
            if (!Str::contains($category->withParentsTitle(), Str::lower($value)) && !Str::contains($category->similar_search, Str::lower($value))) {
                $categories->forget($key);
            }
        }

        echo '<div class="list-group">';
        if ($categories->count() > 0) {
            foreach ($categories as $c) {
                if ($for == 1) {
                    echo '<a class="decor-none" href="' . route('new.ad', $c->slug) . '">
                    <li class="px-3 pt-2 pb-3 cat-result-a radius-10">
                    ' . $c->withParentsTitle() . '
                    </li>
                    </a>';
                } elseif ($for == 2) {
                    echo '<a class="decor-none" href="' . route('question.create', $c->slug) . '">
                    <li class="px-3 pt-2 pb-3 cat-result-a radius-10">
                    ' . $c->withParentsTitle() . '
                    </li>
                    </a>';
                }
            }
        }
        if ($categories->count() == 0) {
            echo '<p class="p-3 text-center">نتیجه ای پیدا نشد</p>';
        }
        echo '</div>';
    }

    public function mainSearchBlog($value = null)
    {
        if (!isset($value)) {
            return;
        }
        $blogs = Blog::where('title', 'like', '%' . $value . '%')->where('status', 1)->take(6)->get();

        echo '<img class="mx-2" src="' . asset('files/other/images/blogs.png') . '"><span>مقالات</span><hr>';
        if ($blogs->count() > 0) {
            echo '<ul>';
            foreach ($blogs as $blog) {
                echo '<li class="my-2"> <a href="' . route('blog.show', ['category_slug' => $blog->category->slug, 'slug' => $blog->slug, 'random_id' => $blog->random_id]) . '">' . $blog->title . '</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<p>مقاله ای پیدا نشد</p>';
        }
    }

    public function mainSearchUser($value = null)
    {
        if (!isset($value)) {
            return;
        }
        $users = User::where('username', 'like', '%' . $value . '%')->take(6)->get();

        echo '<img class="mx-2" src="' . asset('files/other/images/account.png') . '"><span>کاربر</span><hr>';
        if ($users->count() > 0) {
            echo '<ul>';
            foreach ($users as $user) {
                echo '<li class="my-2"> <a href="' . route('user.dashboard', $user->username) . '">' . $user->username . '</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<p>کاربری پیدا نشد</p>';
        }
    }

    public function mainSearchQuestion($value = null)
    {
        if (!isset($value)) {
            return;
        }
        $questions = Question::where('title', 'like', '%' . $value . '%')->take(6)->get();

        echo '<img class="mx-2" src="' . asset('files/other/images/blogs.png') . '"><span>سوالات</span><hr>';
        if ($questions->count() > 0) {
            echo '<ul>';
            foreach ($questions as $question) {
                echo '<li class="my-2"> <a href="' . route('question.show', $question->slug2) . '">' . $question->title . '</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<p>سوال پیدا نشد میزگرد جدیدی ایجاد کنید</p>';
        }
    }

    public function terms()
    {
        return view('terms');
    }
    public function aboutus()
    {
        return view('aboutus');
    }

    public function generateMetaDataForItem($item, $category, $followFeature)
    {
        $result = [];

        if ($followFeature->item_is_in_title) {
            $ctitle = ($category->is_cat_in_title == 1)
                ? (($category->full_title ?? $category->title) . ' ')
                : '';
            $result['title'] = $ctitle
                . ($followFeature->is_feature_in_title == 1 ? ' ' . $followFeature->title : '')
                . ($item->full_title ?? $item->title);
        }

        $title = $result['title'] ?? ($item->full_title ?? $item->title);

        $result['meta_title'] = isset($item->title_in_comment)
            ? Str::replace("*", $title, $item->title_in_comment)
            : (isset($category->title_in_comment)
                ? Str::replace("*", $title, $category->title_in_comment)
                : "نظرات کاربران درباره " . $title);

        $result['meta_desc'] = isset($item->desc_in_comment)
            ? Str::replace("*", $title, $item->desc_in_comment)
            : (isset($category->desc_in_comment)
                ? Str::replace("*", $title, $category->desc_in_comment)
                : "بحث و گفتگو با موضوع  " . $title);

        if (isset($item->desc_in_comment_editor)) {
            $result['meta_desc_editor'] = Str::replace("*", $title, $item->desc_in_comment_editor);
        } elseif (isset($category->desc_in_comment_editor)) {
            $result['meta_desc_editor'] = Str::replace("*", $title, $category->desc_in_comment_editor);
        }

        if (isset($followFeature->page_intro_title) && isset($followFeature->page_intro_desc)) {
            $result['page_intro_title'] = Str::replace("*", $title, $followFeature->page_intro_title);
            $result['page_intro_desc'] = Str::replace("*", $title, $followFeature->page_intro_desc);
        }

        return $result;
    }
    public function generateMetaDataForCat($category)
    {
        $result = [];

        $cat_title = $category->full_title ?? $category->title;
        $result['title'] = $cat_title;

        if (isset($category->title_in_comment_noi)) {
            $result['meta_title'] = Str::replace("*", $cat_title, $category->title_in_comment_noi);
        } else {
            $result['meta_title'] = isset($category->title_in_comment)
                ? Str::replace("*", $cat_title, $category->title_in_comment)
                : "نظرات کاربران درباره " . $cat_title;
        }

        if ($category->desc_in_comment_noi) {
            $result['meta_desc'] = Str::replace("*", $cat_title, $category->desc_in_comment_noi);
        } else {
            $result['meta_desc'] = isset($category->desc_in_comment)
                ? Str::replace("*", $cat_title, $category->desc_in_comment)
                : "بحث و گفتگو با موضوع  " . $cat_title;
        }

        return $result;
    }

    public function generatePageLinksForItem($category, $item)
    {
        $result = [];
        if ($category->has_forums) {
            $result['forum_page'] = $item->withParentsForumUrl();
        }
        if ($category->has_blogs) {
            $result['blog_page'] = $item->withParentsBlogUrl();
        }
        if ($category->has_ads) {
            $result['advertise_page'] = $item->withParentsAdvertiseUrl();
        }
        return $result;
    }

    public function generatePageLinksForCat($category)
    {
        $result = [];
        if ($category->has_forums) {
            $result['forum_page'] = route('question.index', $category->slug);
        }
        if ($category->has_blogs) {
            $result['blog_page'] = route('blog.index', $category->slug);
        }
        // if ($category->has_ads) {
        //     $result['advertise_page'] = route('ads.index', $category->slug);
        // }
        return $result;
    }

    public function getCommentsPaginatePage($category_id, $item_id, $tag_id, $page, $lastId, $cri = null)
    {
        $allComments = $this->getMainComments($category_id, $item_id, $tag_id);

        $responseData = [];

        if ($allComments->isEmpty()) {
            $responseData['nextPageUrl'] = null;

            if ($tag_id != 'null' && $page == 1) {
                $responseData['no_cm'] = 1;
            }

            return response()->json($responseData, 200);
        }

        // انتخاب نظرات برجسته
        $allComments = $this->selectTopComments($allComments, 0);

        // حذف نظرها تا قبل از lastId
        if ($lastId != 'null') {
            $index = $allComments->search(fn($c) => $c->id == $lastId);
            if ($index !== false) {
                $allComments = $allComments->slice($index + 1)->values();
            }
        }
        $checkNextPage = 0;
        if ($allComments->count() > 20) {
            $checkNextPage = 1;
            $allComments = $allComments->take(20);
        }

        // حذف نظر با آیدی cri اگر موجود باشه
        //اگه ای دی نظری توی لینک صفحه باشه اون رو اول نشون میده بخاطر همین اگه دوباره نظر توی لیست بود میخوام پاک بشه
        if ($cri) {
            $allComments = $allComments->reject(fn($c) => $c->id == $cri)->values();
        }

        if ($allComments->isEmpty()) {
            $responseData['nextPageUrl'] = null;

            if ($tag_id != 'null') {
                $responseData['no_cm'] = 1;
            }

            return response()->json($responseData, 200);
        }

        if ($checkNextPage) {
            $nextPageUrl = $this->getCommentsNextPageUrl($allComments, $category_id, $item_id, $tag_id, $page, $cri);
        } else {
            $nextPageUrl = null;
        }

        if ($page == 1) {
            $affilateService = new AffilateService();
            $affilates = $affilateService->suggestsForPagesApi($category_id, $item_id, 3);

            if (isset($tag_id)) {
                $child_tags = [];
                $item = MongoItem::find($item_id);
                if ($item && isset($item->tags_array)) {
                    foreach ($item->tags_array as $tag_object) {
                        if (isset($tag_object['parent_id']) && $tag_object['parent_id'] == $tag_id) {
                            $child_tags[] = $tag_object;
                        }
                    }
                    usort($child_tags, function ($a, $b) {
                        return $a['priority'] <=> $b['priority'];
                    });
                }

                $responseData['child_tags'] = $child_tags;
                $responseData['category_id'] = $category_id;
                $responseData['item_id'] = $item_id;
            }

            $responseData['html'] = view('question.comment-items-api', [
                'comments' => $allComments,
                'affilates' => $affilates
            ])->render();
        } else {
            $responseData['html'] = view('question.comment-items-api', [
                'comments' => $allComments
            ])->render();
        }
        $responseData['scrollTo'] = 'comment-box-' . $allComments->first()->id;
        $responseData['nextPageUrl'] = $nextPageUrl;

        return response()->json($responseData, 200);
    }
    public function getMainComments($category_id, $item_id, $tag_id)
    {
        $category_comment_repository = new CategoryCommentRepository();
        if ($category_id != 'null') {
            if ($item_id != 'null') {
                if ($tag_id != 'null') {
                    $comments = $category_comment_repository->getParentCommentsByTagId($category_id, $item_id, $tag_id, 120);
                } else {
                    $comments = $category_comment_repository->getParentCommentsByItemId($category_id, $item_id, 120);
                }
            } else {
                $comments = $category_comment_repository->getParentCommentsByCategoryId($category_id, 120);
            }
        } else {
            $comments = $category_comment_repository->getParentComments(100);
        }
        return $comments;
    }

    //cri ای دی اون کامنتی هستش که با لینک اون صفحه رو باز کرده
    public function getCommentsNextPageUrl($comments, $category_id, $item_id, $tag_id, $page, $cri)
    {
        if ($comments->isEmpty()) {
            return null;
        }
        $nextPageUrl = ($page == 4) ? null : route('api.get.comments.page', [
            'category_id' => $category_id != 'null' ? $category_id : 'null',
            'item_id' => $item_id != 'null' ? $item_id : 'null',
            'tag_id' => $tag_id != 'null' ? $tag_id : 'null',
            'page' => $page + 1,
            'lastId' => $comments->last()->id,
            'cri' => isset($cri) ? $cri : 'null',
        ]);
        return $nextPageUrl;
    }

    public function selectTopComments($allComments, $getAccepted)
    {
        $lastComments = $allComments->take(30);
        $firstComs = $lastComments->take(3);
        $topUnLikes = $lastComments->sortByDesc('unlike_count')->take(3);
        $topLikes = $lastComments->sortByDesc('like_count')->take(3);

        $allComments = $firstComs->merge($topUnLikes)->merge($topLikes)->merge($allComments)->unique('id')->values();

        if ($getAccepted == 1) {
            $allComments = $allComments->take(40);
            if (count($topUnLikes) > 0) {
                $acceptedAnswer = $topUnLikes->first();
            } else {
                if (count($topLikes) > 0) {
                    $acceptedAnswer = $topLikes->first();
                } else {
                    $acceptedAnswer = $allComments->first();
                }
            }
            $data = [];
            $data['comments'] = $allComments;
            $data['acceptedAnswer'] = $acceptedAnswer;
            return $data;
        } else {
            return $allComments;
        }
    }
}
