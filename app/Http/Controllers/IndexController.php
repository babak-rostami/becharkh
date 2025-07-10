<?php

namespace App\Http\Controllers;

use App\Models\Affilate;
use App\Models\Blog;
use App\Models\Brand;
use App\Models\CategoryFeatureItem;
use App\Models\MongoBlog;
use App\Models\MongoCategory;
use App\Models\MongoItem;
use App\Models\MongoQuestion;
use App\Models\MongoVideo;
use App\Models\Ostan;
use App\Models\Question;
use App\Models\SiteCategory;
use App\Models\User;
use App\Models\UserSearch;
use App\Services\Suggestion\SuggestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class IndexController extends Controller
{

    public function home(SuggestionService $suggestionService)
    {
        $products = Affilate::orderBy('created_at', 'desc')->where('google_index', 1)->where('status', 1)->take(15)->get();

        $suggests = $suggestionService->suggest();
        $categories = $suggests['cats'];

        $questions = MongoQuestion::where('status', 1)->orderBy('created_at', 'desc')->take(20)->with('user')->get();

        $hot_pages = Cache::get('hot_pages');

        return view('home', compact('questions', 'products', 'categories', 'hot_pages'));
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
        return str_replace($persianNumerals, $englishNumerals, $input);
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
                    'a_url' => $item->category->has_ads ? $item->withParentsAdvertiseUrl() : null,
                    'img' => $item->thumb()
                ];
            });

            $categories = $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'title' => $category->full_title ?? $category->title,
                    'url' => $category->has_ads
                        ? route('ads.index', $category->slug)
                        : ($category->has_comments
                            ? route('question.index', $category->slug) . "?s=1"
                            : route('question.index', $category->slug)),
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
}
