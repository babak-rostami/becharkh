<?php

namespace App\Http\Controllers;

use App\Jobs\Item\ChangeItemPageCount;
use App\Models\Admin;
use App\Models\MongoCategory;
use App\Models\MongoCategoryComment;
use App\Models\MongoFollowItem;
use App\Models\MongoItem;
use App\Models\MongoQuestion;
use App\Models\MongoQuestionAnswer;
use App\Models\MongoVideo;
use App\Models\Question;
use App\Models\QuestionAnswerEditorImage;
use App\Models\QuestionCategory;
use App\Models\QuestionEditorImage;
use App\Models\QuestionLike;
use App\Models\RtablePageData;
use App\Models\SurveyOption;
use App\Models\Tag;
use Illuminate\Support\Str;
use App\Notifications\SiteEvent;
use App\Repositories\Feature\Mongodb\FeatureRepository;
use App\Repositories\Question\Mongodb\QuestionRepository;
use App\Services\Affilate\AffilateService;
use App\Services\Comment\CommentEditorService;
use App\Services\Item\AdditemsService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Services\Suggestion\SuggestionService;
use App\Services\Survey\SurveyService;
use Illuminate\Support\Facades\Cache;

class QuestionController extends Controller
{

    public function forumIndex($request, $suggestionService, $category_slug)
    {
        $question_repository = new QuestionRepository();

        $selectedFeatures = collect();
        $followFeature = null;
        $item = null;
        $data = new RtablePageData();

        $user = null;
        if (auth('user')->check()) {
            $user = auth('user')->user();
        }

        $meta_title = null;
        $meta_desc = null;
        $meta_desc_editor = null;

        $page_intro_title = null;
        $page_intro_desc = null;

        if ($category_slug != null) {
            $feature_repository = new FeatureRepository();
            $category = MongoCategory::where('slug', $category_slug)->first();
            if (!isset($category)) {
                return redirect()->route('home')->with('success', 'آدرس صفحه تغییر کرده است، از منو سایت دوباره جستجو کنید');
            }

            app(SiteCategoryController::class)->redirectIfPageNotExist($request, $category, 'forums');

            $categoryFeatures = $feature_repository->getFeaturesByCategoryIdForForum($category->id);

            $featuresInUrl = $data->getFeaturesInUrl($request);
            foreach ($featuresInUrl as $fiu) {
                $fs = explode('=', $fiu)[0];
                $f = $categoryFeatures->where('slug', $fs)->first();
                if (isset($f)) {
                    $selectedFeatures->add($f);
                }
            }
            $fiu_parentIds = $selectedFeatures->filter->parent_id->pluck('parent_id');
            $fiu_Ids = $selectedFeatures->filter->id->pluck('id');
            $childFeaturesInUrl = $categoryFeatures->whereIn('id', $fiu_Ids)->whereNotIn('id', $fiu_parentIds);

            $questions = collect();

            $selected_items = collect();
            foreach ($selectedFeatures as $sf) {
                if (isset($sf)) {
                    $sitem = MongoItem::where('feature_id', $sf->id)->where('slug', $request[$sf->slug])->first();
                    if (isset($sitem)) {
                        $selected_items->add($sitem);
                    }
                }
            }
            foreach ($childFeaturesInUrl as $f) {
                if (isset($f)) {
                    $item = $selected_items->where('feature_id', $f->id)->where('slug', $request[$f->slug])->first();
                    if (isset($item)) {
                        $tempQuestions = $question_repository->getQuestionsByItemId($category->id, $item->id);
                        if (count($questions) > 0) {
                            $questions = $questions->intersect($tempQuestions);
                        } else {
                            $questions = $tempQuestions;
                        }
                    }
                    //follow button
                    if ($f->has_follow) {
                        $followFeature = $f;
                    }
                }
            }

            $title = "";
            if (isset($followFeature) && isset($item)) {
                if ($followFeature->item_is_in_title) {
                    if ($category->is_cat_in_title == 1) {
                        $ct = $category->full_title ?? $category->title;
                        $ctitle = trim($ct) == "" ? "" : $ct . " ";
                    } else {
                        $ctitle = "";
                    }
                    $title = $ctitle . ($followFeature->is_feature_in_title == 1 ? ' ' . $followFeature->title : '') . ($item->full_title  ?? $item->title);
                }
                if (isset($item->title_in_rtable)) {
                    $meta_title = str_replace("*", $title, $item->title_in_rtable);
                } else {
                    if ($category->title_in_rtable) {
                        $meta_title = str_replace("*", $title, $category->title_in_rtable);
                    } else {
                        $meta_title = "انجمن " . $title;
                    }
                }
                if (isset($item->desc_in_rtable)) {
                    $meta_desc = str_replace("*", $title, $item->desc_in_rtable);
                } else {
                    if ($category->desc_in_rtable) {
                        $meta_desc = str_replace("*", $title, $category->desc_in_rtable);
                    } else {
                        $meta_desc = "هر سوالی داری توی انجمن " . $title . " به جواب میرسی";
                    }
                }
                if (isset($item->desc_in_rtable_editor)) {
                    $meta_desc_editor = str_replace("*", $title, $item->desc_in_rtable_editor);
                } else {
                    if ($category->desc_in_rtable_editor) {
                        $meta_desc_editor = str_replace("*", $title, $category->desc_in_rtable_editor);
                    }
                }
                if (isset($followFeature->page_intro_title) && isset($followFeature->page_intro_desc)) {
                    $page_intro_title = str_replace("*", $title, $followFeature->page_intro_title);
                    $page_intro_desc = str_replace("*", $title, $followFeature->page_intro_desc);
                }
                // if ($user) {
                //     $follow = MongoFollowItem::where('item_id', $item->id)->where('user_id', $user->id)->first();
                //     if (isset($follow)) {
                //         $is_follow = 1;
                //     } else {
                //         $is_follow = 0;
                //     }
                // }
            } else {
                $cat_title = $category->full_title ?? $category->title;
                if ($category->title_in_rtable) {
                    $meta_title = str_replace("*", $cat_title, $category->title_in_rtable);
                } else {
                    $meta_title = "انجمن " . $cat_title;
                }
                if ($category->desc_in_rtable) {
                    $meta_desc = str_replace("*", $cat_title, $category->desc_in_rtable);
                } else {
                    $meta_desc = "هر سوالی داری توی انجمن " . $cat_title . " به جواب میرسی";
                }
                $questions = $question_repository->getQuestionsByCategoryId($category->id);
            }

            $suggests = $suggestionService->suggest($category, $item);
            if (isset($suggests['items'])) {
                $suggetItems = $suggests['items'];
            } else {
                $suggestCats = $suggests['cats'];
            }

            // $hotVideos = MongoVideo::random(15, ['pr_link' => 'notnull']);
            if (isset($item) && isset($followFeature)) {
                $hotQuestions = $this->getHotQuestions($category, $item->id);
            } else {
                $hotQuestions = $this->getHotQuestions($category, null);
            }
            //clean query for paginate url
            $reqs = $data->getCurrentUrlWithoutPage($request);
            $questions = $this->paginateC($questions, 20, null, $reqs)->onEachSide(1);

            $advertise_page = null;
            if (isset($item)) {
                if ($category->has_comments) {
                    $comment_page = $item->withParentsCommentUrl();
                }
                if ($category->has_blogs) {
                    $blog_page = $item->withParentsBlogUrl();
                }
                if ($category->has_ads) {
                    $advertise_page = $item->withParentsAdvertiseUrl();
                }
                if (isset($item->videos)) {
                    $ivids = MongoVideo::find($item->videos)->shuffle()->first();
                    if (isset($ivids)) {
                        $item_video = $ivids;
                    }
                }
            } else {
                if ($category->has_comments) {
                    $comment_page = route('question.index', $category->slug) . '?s=1';
                }
                if ($category->has_blogs) {
                    $blog_page = route('blog.index', $category->slug);
                }
                if ($category->has_ads) {
                    $advertise_page = route('ads.index', $category->slug);
                }
            }

            $affilateService = new AffilateService();
            $affilate = $affilateService->suggestForPages($category, $item);

            $features = $category->features();
            $currentQueryParams = $request->query();

            $hot_pages = Cache::get('hot_pages');

            $compactVars = [
                'page_intro_title',
                'page_intro_desc',
                'hot_pages',
                'hotQuestions',
                'item',
                'meta_title',
                'meta_desc',
                'meta_desc_editor',
                'data',
                'questions',
                'category',
                'title',
                'currentQueryParams',
                // 'selected_items',
            ];
            // if (isset($features)) {
            //     $compactVars[] = 'features';
            // }
            if (isset($item_video)) {
                $compactVars[] = 'item_video';
            }
            if (isset($affilate)) {
                $compactVars[] = 'affilate';
            }
            if (isset($comment_page)) {
                $compactVars[] = 'comment_page';
            }
            if (isset($advertise_page)) {
                $compactVars[] = 'advertise_page';
            }
            if (isset($blog_page)) {
                $compactVars[] = 'blog_page';
            }
            if (isset($suggetItems)) {
                $compactVars[] = 'suggetItems';
            } elseif (isset($suggestCats)) {
                $compactVars[] = 'suggestCats';
            }
            return view('question.index', compact(...$compactVars));
        } else {
            $suggests = $suggestionService->suggest();
            $suggestCats = $suggests['cats'];

            // $hotVideos = MongoVideo::random(15, ['pr_link' => 'notnull']);
            $hotQuestions = $this->getHotQuestions(null, null);

            $questions = $question_repository->getlastQuestions();
            $questions = $this->paginateC($questions, 20, null, $request->url())->onEachSide(1);

            $comment_page = route('question.index') . '?s=1';
            $advertise_page = route('ads.index');
            $blog_page = route('blog.index');

            return view('question.index', compact('suggestCats', 'comment_page', 'blog_page', 'advertise_page', 'hotQuestions', 'data', 'questions'));
        }
    }

    public function index(Request $request, SuggestionService $suggestionService, $category_slug = null)
    {
        if (isset($request->query()['s'])) {
            return app(CategoryCommentController::class)->index($request, $suggestionService, $category_slug);
        } else {
            return $this->forumIndex($request, $suggestionService, $category_slug);
        }
    }

    private function getHotQuestions($category = null, $item_id = null, $take = 5)
    {
        $hotRelated = collect();
        if (isset($category)) {
            if ($item_id) {
                $hotRelatedByItem = MongoQuestion::orderBy('created_at', 'desc')->where('category_id', $category->id)->where('items', $item_id)->where('status', 1)->take($take)->get();
                $hotRelated = $hotRelated->merge($hotRelatedByItem);
            }
            if (count($hotRelated) < $take) {
                $hotRelatedByCategory = MongoQuestion::orderBy('created_at', 'desc')->where('category_id', $category->id)->where('status', 1)->take($take)->get();
                $hotRelated = $hotRelated->merge($hotRelatedByCategory)->unique();
            }
            if (count($hotRelated) < $take) {
                $hotRelatedByCreateAt = MongoQuestion::orderBy('created_at', 'desc')->where('status', 1)->take($take)->get();
                $hotRelated = $hotRelated->merge($hotRelatedByCreateAt)->unique();
            }
        } else {
            $hotRelatedByCreateAt = MongoQuestion::orderBy('created_at', 'desc')->where('status', 1)->take($take)->get();
            $hotRelated = $hotRelated->merge($hotRelatedByCreateAt)->unique();
        }

        return $hotRelated;
    }

    public function commentsNextPage(Request $request, $comments)
    {
        $comments = $this->paginateC($comments, 20, 2, $request->url())->onEachSide(1);
        return response()->json(['comments' => $comments], 200);
    }

    public function paginateC(
        $items,
        $perPage = 15,
        $page = null,
        $baseUrl = null,
        $options = []
    ) {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ?
            $items : Collection::make($items);

        $lap = new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            $options
        );

        if ($baseUrl) {
            $lap->setPath($baseUrl);
        }

        return $lap;
    }

    public function updateCkeditor(Request $request, $id = null)
    {
        if ($request->hasFile('upload')) {
            $disk = Storage::disk('ftp');
            $user = auth('user')->user();
            $file = $request->file('upload');

            if ($id) {
                $question = MongoQuestion::find($id);
                $category = $question->category;
                $path = 'question/images/' . $category->slug . '/' . $user->username . '/';
                $filename = $category->slug . '-' . time() . rand(10000, 99999) . '.webp';
                $url = $path . $filename;
                $resizedImage = Image::make($file)->encode('webp', 90);
                $disk->put($path . $filename, (string) $resizedImage);

                $images = $question->images ?? [];
                $images[] = $url;

                $question->images = $images;
                $question->update();
            } else {
                $path = 'question/images/' . $user->username . '/';
                $filename = time() . '.' . $user->username . '.webp';
                $url = $path . $filename;
                $resizedImage = Image::make($file)->encode('webp', 90);
                $disk->put($path . $filename, (string) $resizedImage);

                $images = $user->temp_question_ck_images ?? [];
                $images[] = $url;

                $user->temp_question_ck_images = $images;
                $user->update();
            }

            return response()->json(['filename' => $filename, 'uploaded' => 1, 'url' => "https://dl.becharkh.com/user_files/" . $url]);
        }
    }

    public function category($category)
    {
        $categories = QuestionCategory::all();
        $category = QuestionCategory::where('slug', $category)->first();
        if (isset($category)) {
            $questions = $category->questions;
            $tags = Tag::all()->sortByDesc(function ($tag) {
                return $tag->pageCount();
            });
            return view('question.index', compact('categories', 'questions', 'category', 'tags'));
        } else {
            return redirect()->route('home')->with('success', 'آدرس صفحه تغییر کرده است، از منو سایت دوباره جستجو کنید');
        }
    }

    public function showShortLink($id)
    {
        $question = MongoQuestion::find($id);
        return redirect()->route('question.show', $question->slug2);
    }

    public function show(SuggestionService $suggestionService, $category, $slug = null, $random = null)
    {
        if (!isset($category) || !isset($slug) || !isset($random)) {
            return redirect()->route('home')->with('success', 'آدرس صفحه تغییر کرده است، از منو سایت دوباره جستجو کنید');
        }
        $slug2 = $category . '/' . $slug . '/' . $random;
        $question = MongoQuestion::where('slug2', $slug2)->with(['user', 'category'])->first();
        if (!isset($question)) {
            return redirect()->route('home')->with('success', 'آدرس صفحه تغییر کرده است، از منو سایت دوباره جستجو کنید');
        }
        if ($question->status != 1) {
            return redirect()->route('home')->with('success', 'سوال بعد از تایید در انجمن نمایش داده میشود');
        }
        $category = $question->category;

        $items = $question->items;
        $item = null;
        $user = null;
        if (auth('user')->check()) {
            $user = auth('user')->user();
        }
        if (isset($items) && count($items) > 0) {
            $item_id = $items[0];
            $item = MongoItem::find($item_id);
            $questions = $this->getHotQuestions($category, $item->id, 15)->where('id', '!=', $question->id);
        } else {
            $questions = $this->getHotQuestions($category, null, 15)->where('id', '!=', $question->id);
        }
        $features = $category->features();
        if (isset($item)) {
            $tab_title = $item->full_title ?? $item->title;
            if ($category->has_comments) {
                $comment_page = $item->withParentsCommentUrl();
            }
            if ($category->has_forums) {
                $forum_page = $item->withParentsForumUrl();
            }
            if ($category->has_blogs) {
                $blog_page = $item->withParentsBlogUrl();
            }
            if ($category->has_ads) {
                $advertise_page = $item->withParentsAdvertiseUrl();
            }
            $followFeature = $features->find($item->feature_id);
            if (isset($followFeature->page_intro_title) && isset($followFeature->page_intro_desc)) {
                $page_intro_title = str_replace("*", $tab_title, $followFeature->page_intro_title);
                $page_intro_desc = str_replace("*", $tab_title, $followFeature->page_intro_desc);
            }
        } else {
            $tab_title = $category->full_title ?? $category->title;
            if ($category->has_comments) {
                $comment_page = route('question.index', $category->slug) . '?s=1';
            }
            if ($category->has_forums) {
                $forum_page = route('question.index', $category->slug);
            }
            if ($category->has_blogs) {
                $blog_page = route('blog.index', $category->slug);
            }
            if ($category->has_ads) {
                $advertise_page = route('ads.index', $category->slug);
            }
        }
        $pin_questions = MongoQuestion::select('_id', 'title', 'sug_title', 'slug2', 'answer', 'image')
            ->where('just_this_page', 0)
            ->take(20)
            ->get();
        $pin_questions = $pin_questions->where('id', '!=', $question->id)->shuffle()->take(3);
        $pinQuestionIds = $pin_questions->pluck('_id');
        $questions = $questions->whereNotIn('_id', $pinQuestionIds);
        $questions->each(function ($hq) {
            $hq->load('user');
        });
        $suggests = $suggestionService->suggest($category, $item);
        if (isset($suggests['items'])) {
            $suggetItems = $suggests['items'];
        } else {
            $suggestCats = $suggests['cats'];
        }

        $childFeature = null;
        $childItem = null;

        $page_intro_title = null;
        $page_intro_desc = null;

        $lastAnswers = MongoCategoryComment::where('question_id', $question->id)->where('parent_id', null)->with('user')->orderBy('created_at', 'desc')->get();
        $firstComs = $lastAnswers->take(1);
        $topLikes = $lastAnswers->sortByDesc('like_count')->take(3);
        $acceptedAnswer = $topLikes->first();
        $answers = $firstComs->merge($topLikes)->merge($lastAnswers)->unique();

        if (!isset($_COOKIE['page_seen'])) {
            $question->seen_count += 1;
            $question->update();
        }

        if ($question->editor) {
            $question->editor = preg_replace('/<img(.*?)src=\"(.*?)\"/', '<img$1class="lazy-load" data-src="$2"', $question->editor);
        }

        $affilateService = new AffilateService();
        $affilates = $affilateService->suggestForQuestion($question->id, $category, $item);

        $currentQueryParams = [];

        $hot_pages = Cache::get('hot_pages');

        if ($question->video) {
            $video = $question->video;
        } else {
            $video = null;
        }

        $compactVars = [
            'video',
            'page_intro_title',
            'page_intro_desc',
            'hot_pages',
            'question',
            'answers',
            'acceptedAnswer',
            'questions',
            'user',
            'item',
            'category',
            'tab_title',
            'currentQueryParams',
        ];
        if (isset($pin_questions) && !$pin_questions->isEmpty()) {
            $compactVars[] = 'pin_questions';
        }
        if (isset($features)) {
            $compactVars[] = 'features';
        }
        if (isset($affilates)) {
            $compactVars[] = 'affilates';
        }
        if (isset($forum_page)) {
            $compactVars[] = 'forum_page';
        }
        if (isset($comment_page)) {
            $compactVars[] = 'comment_page';
        }
        if (isset($blog_page)) {
            $compactVars[] = 'blog_page';
        }
        if (isset($advertise_page)) {
            $compactVars[] = 'advertise_page';
        }
        if (isset($suggetItems)) {
            $compactVars[] = 'suggetItems';
        } elseif (isset($suggestCats)) {
            $compactVars[] = 'suggestCats';
        }
        return view('question.show', compact(...$compactVars));
    }

    public function showForVue($id)
    {
        $question = Question::with('user')->with('category')->where('id', $id)->first();
        return $question;
    }

    public function all()
    {
        $questions = Question::with('user')->with('category')->get();
        return $questions;
    }

    public function create(Request $request)
    {
        $categories = MongoCategory::where('status', 1)->where('is_active', 1)->get();
        $categories = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'title' => $category->title,
                'slug' => $category->slug,
                'p_id' => $category->parent_id,
                'ss' => $category->similar_search
            ];
        });

        return view('question.create', compact('categories'));
    }

    public function createForAdmin(Request $request)
    {
        $categories = MongoCategory::where('status', 1)->get();
        $categories = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'title' => $category->title,
                'slug' => $category->slug,
                'p_id' => $category->parent_id,
                'ss' => $category->similar_search
            ];
        });
        return view('question.admin.create', compact('categories', 'request'));
    }

    public function storeForAdmin(Request $request)
    {
        $category = MongoCategory::find($request->category_id);

        if (!isset($category)) {
            return back()->with('success', 'دسته بندی سوال را انتخاب کنید');
        }

        $question = new MongoQuestion();
        $question->status = 1;
        $question->google_index = 1;
        $user_id = app(UserController::class)->fakeRegisterSend($request->name, $request->username);
        $question->user_id = $user_id;

        $question->category_id = $category->id;
        $question->title = $request->title;
        $slug = preg_replace('~[^\pL\d]+~u', '-', $request->title);
        $slug2 = $this->createQuestionSlug($category->slug, $slug);
        $question->slug2 = $slug2;

        $editor_service = new CommentEditorService();
        $editor_images = $editor_service->store('create_question_admin', $request->body, $question);

        $addItemService = new AdditemsService();
        $add_item_result = $addItemService->addForCreate($category, $request);
        $items = $add_item_result['items'];
        $items_title = $add_item_result['items_title'];
        $changeStatus = $add_item_result['changeStatus'];

        if (count($items) > 0) {
            $question->items = $items;
            dispatch(new ChangeItemPageCount($items, 'question', 1))->onQueue('becharkhsite')->delay(now()->addMinutes(5));
        }
        if (count($items_title) > 0) {
            $question->items_title = $items_title;
        }

        if ($changeStatus) {
            $question->status = 0;
        } else {
            $question->status = 1;
        }

        $survey_service = new SurveyService();
        $survey_service->addSurveyTo($question, $request);

        $question->save();

        $editor_service->updateImageCommentId($editor_images, $question->id);

        return redirect()->route('question.index.admin')->with('success', 'سوال شما با موفقیت در انجمن ثبت شد');
    }

    private function createQuestionSlug($cat_slug, $slug, $random = 1)
    {
        $slug2 = $cat_slug . '/' . $slug . '/' . $random;
        $is_exist = MongoQuestion::where('slug2', $slug2)->first();
        if ($is_exist) {
            return $this->createQuestionSlug($cat_slug, $slug, $random + 1);
        } else {
            return $slug2;
        }
    }

    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'title' => 'required',
            ],
            [
                'title.required' => 'عنوان سوال را بنویسید',
            ]
        );

        $category = MongoCategory::find($request->category_id);

        if (!isset($category)) {
            return back()->with('success', 'دسته بندی سوال را انتخاب کنید');
        }

        $user = auth('user')->user();
        $question = new MongoQuestion();
        $question->category_id = $category->id;
        if ($category->status) {
            $question->status = 1;
        } else {
            $question->status = 0;
        }
        $question->title = $request->title;
        $slug = preg_replace('~[^\pL\d]+~u', '-', $request->title);
        $slug2 = $this->createQuestionSlug($category->slug, $slug);
        $question->slug2 = $slug2;

        $question->google_index = 0;

        $editor_service = new CommentEditorService();
        $editor_images = $editor_service->store('create_question', $request->body, $question);

        $question->user_id = $user->id;

        $addItemService = new AdditemsService();
        $add_item_result = $addItemService->addForCreate($category, $request);
        $items = $add_item_result['items'];
        $items_title = $add_item_result['items_title'];
        $changeStatus = $add_item_result['changeStatus'];

        if (count($items) > 0) {
            $question->items = $items;
            dispatch(new ChangeItemPageCount($items, 'question', 1))->onQueue('becharkhsite')->delay(now()->addMinutes(5));
        }
        if (count($items_title) > 0) {
            $question->items_title = $items_title;
        }

        $question->status = 0;

        $survey_service = new SurveyService();
        $survey_service->addSurveyTo($question, $request);

        $question->save();


        $editor_service->updateImageCommentId($editor_images, $question->id);

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => $user->username . ' یک پرسش با عنوان ' . $request->title . ' منتشر کرد',
                'route' => route('question.show', $question->slug2),
            ]));
        }

        return redirect()->route('user.dashboard.edit', "forum")->with('success', 'سوال شما با موفقیت در انجمن ثبت شد');
    }

    public function indexAdmin($cat_slug = null)
    {
        $categories = MongoCategory::all();
        if ($cat_slug != null) {
            $category = $categories->where('slug', $cat_slug)->first();
            $questions = $category->questions;
            return view('question.index_admin', compact('questions', 'categories', 'category'));
        } else {
            $questions = MongoQuestion::orderBy('created_at', 'desc')->get();
            return view('question.index_admin', compact('questions', 'categories'));
        }
    }

    public function questionDestroy(MongoQuestion $question)
    {
        $disk = Storage::disk('ftp');

        //delete img in question editor
        $q_images = QuestionEditorImage::where('comment_id', $question->id)->get();
        if (!$q_images->isEmpty()) {
            foreach ($q_images as $ci) {
                $disk->delete($ci->path);
                $ci->delete();
            }
        }
        //delete answers and img in answer editor
        foreach ($question->answers as $answer) {
            $answer_images = QuestionAnswerEditorImage::where('comment_id', $answer->id)->get();
            if (!$answer_images->isEmpty()) {
                foreach ($answer_images as $ci) {
                    $disk->delete($ci->path);
                    $ci->delete();
                }
            }
            $answer->delete();
        }
        foreach ($question->likes as  $like) {
            $like->delete();
        }

        if (isset($question->items) && count($question->items) > 0) {
            dispatch(new ChangeItemPageCount($question->items, 'question', 0))->onQueue('becharkhsite')->delay(now()->addMinutes(5));
        }
        if (isset($question->surop1)) {
            $sur_options = SurveyOption::where('page', 'show_question')->where('obj_id', $question->id)->get();
            foreach ($sur_options as $so) {
                $so->delete();
            }
        }

        $question->delete();

        return back()->with('success', 'سوال با موفقیت حذف شد');
    }

    public function edit(Request $request, $question_id)
    {
        $question = MongoQuestion::find($question_id);
        $editor_service = new CommentEditorService();
        $editor_service->changeTempEditorLazyImg($question);
        $user = auth('user')->user();
        if ($user->id != $question->user_id) {
            return back()->with('success', 'دسترسی ندارید');
        }
        $category = MongoCategory::find($question->category_id);
        $cfeatures = $category->features()->where('is_in_filter_rtable', 1);
        $questionFeatueItems = $question->getItems();
        $questionFeatueItems = $questionFeatueItems->reverse();
        $citems = MongoItem::where('category_id', $category->id)->get();
        $cfeatures = $cfeatures->map(function ($f) {
            return [
                'id' => $f->id,
                'p_id' => $f->parent_id,
                'title' => $f->title,
                'slug' => $f->slug,
                'i_count' => $f->select_items_count
            ];
        });
        $citems = $citems->map(function ($i) {
            return [
                'id' => $i->id,
                'f_id' => $i->feature_id,
                'p_id' => $i->parent_id,
                'title' => $i->title,
                'e_title' => $i->title_en,
                'slug' => $i->slug,
            ];
        });
        $questionFeatueItems = $questionFeatueItems->map(function ($fi) {
            return [
                'f_id' => $fi->feature_id,
                'i_id' => $fi->id,
            ];
        })->values();

        return view('question.edit', compact('question', 'category', 'cfeatures', 'citems', 'questionFeatueItems'));
    }

    public function editAdmin(Request $request, $question_id)
    {
        $question = MongoQuestion::find($question_id);
        $editor_service = new CommentEditorService();
        $editor_service->changeTempEditorLazyImg($question);
        $categories =  MongoCategory::where('status', 1)->get();
        $category = MongoCategory::find($question->category_id);
        $cfeatures = $category->features()->where('is_in_filter_rtable', 1);

        //for fix order object items
        $questionFeatueItems = $question->getItems();
        $itemIds = $question->items;
        if ($itemIds) {
            $itemIdPositionMap = array_flip($itemIds);
        } else {
            $itemIdPositionMap = [];
        }
        $orderedItems = $questionFeatueItems->sortBy(function ($item) use ($itemIdPositionMap) {
            return $itemIdPositionMap[$item->_id];
        });
        $questionFeatueItems = $orderedItems->values()->reverse();
        //end for fix order object items
        $citems = MongoItem::where('category_id', $category->id)->get();
        $categories = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'title' => $category->title,
                'slug' => $category->slug,
                'p_id' => $category->parent_id
            ];
        });
        $cfeatures = $cfeatures->map(function ($f) {
            return [
                'id' => $f->id,
                'p_id' => $f->parent_id,
                'title' => $f->title,
                'slug' => $f->slug,
                'i_count' => $f->select_items_count
            ];
        });
        $citems = $citems->map(function ($i) {
            return [
                'id' => $i->id,
                'f_id' => $i->feature_id,
                'p_id' => $i->parent_id,
                'title' => $i->title,
                'e_title' => $i->title_en,
                'slug' => $i->slug,
            ];
        });
        $questionFeatueItems = $questionFeatueItems->map(function ($fi) {
            return [
                'f_id' => $fi->feature_id,
                'i_id' => $fi->id,
            ];
        })->values();

        $compactVars = [
            'question',
            'category',
            'categories',
            'cfeatures',
            'citems',
            'questionFeatueItems'
        ];

        $sasf_categorySelects = MongoCategory::where('pin_question_ids', $question->id)
            ->select('id', 'title')
            ->get();
        if ($sasf_categorySelects->isNotEmpty()) {
            $sasf_categoryIds = $sasf_categorySelects->pluck('_id')->implode(',');
            $compactVars[] = 'sasf_categoryIds';
            $compactVars[] = 'sasf_categorySelects';
        }

        $sasf_itemSelects = MongoItem::where('pin_question_ids', $question->id)
            ->select('id', 'title')
            ->get();
        if ($sasf_itemSelects->isNotEmpty()) {
            $sasf_itemIds = $sasf_itemSelects->pluck('_id')->implode(',');
            $compactVars[] = 'sasf_itemIds';
            $compactVars[] = 'sasf_itemSelects';
        }
        if (isset($question->video_id)) {
            $videoSelect = MongoVideo::select('title')->find($question->video_id);
            $videoId = $videoSelect->id;
            $compactVars[] = 'videoId';
            $compactVars[] = 'videoSelect';
        }

        return view('question.admin.edit', compact(...$compactVars));
    }

    public function updateAdmin(Request $request, $question_id)
    {
        $question = MongoQuestion::find($question_id);

        $question->status = 1;
        $question->google_index = $request->google_index;
        $question->title = $request->title;

        $unset_vid = 0;
        $unset_sug_title = 0;
        $unset_just_this_page = 0;
        if (isset($request->sug_title)) {
            $question->sug_title = $request->sug_title;
        } else {
            $unset_sug_title = 1;
        }
        if ($request->just_this_page == 0) {
            $question->just_this_page = (int) $request->just_this_page;
        } else {
            $unset_just_this_page = 1;
        }

        $editor_service = new CommentEditorService();
        $editor_service->update('edit_question_admin', $request->body, $question);

        $category = MongoCategory::find($request->category_id);
        $question->category_id = $category->id;

        $addItemService = new AdditemsService();
        $last_items = $question->items ?? [];
        $add_item_result = $addItemService->addForUpdate($category, $last_items, $request);
        $items = $add_item_result['items'];
        $items_title = $add_item_result['items_title'];
        $changeStatus = $add_item_result['changeStatus'];

        if (isset($question->items)) {
            $question->items = $items;
            $question->items_title = $items_title;
        } else {
            if (count($items) > 0) {
                $question->items = $items;
            }
            if (count($items_title) > 0) {
                $question->items_title = $items_title;
            }
        }
        if (isset($request->video)) {
            if ($question->video_id != $request->video) {
                $video = MongoVideo::find($request->video);
                $question->video_id = $video->id;
                $question->video_path = $video->video_path;
                $video->question_id = $question->id;
                $video->update();
            }
        } else {
            $unset_vid = 1;
        }
        if ($changeStatus) {
            $question->status = 0;
        } else {
            $question->status = 1;
        }

        if ($request->hasFile('image')) {
            $cover = $request->file('image');
            $path = 'question/images/' . $category->slug . '/';
            if ($question->getImage()) {
                $image_name = explode($path, $question->getImage())[1];
                $basefilename = explode('.webp', $image_name)[0];
            } else {
                $basefilename = $category->slug . rand(1000, 9999) . time();
            }
            //main image
            $filename = $basefilename . '.webp';
            $question->image = $path . $filename;
            $this->uploadAndResizeImage($cover, $path, $filename, 90, 0);
            //thum image
            $filename2 = $basefilename . '2.webp';
            $this->uploadAndResizeImage($cover, $path, $filename2, 90, 1);
        }

        $question->update();

        if ($unset_sug_title) {
            $question->unset('sug_title');
        }
        if ($unset_just_this_page) {
            $question->unset('just_this_page');
        }
        if ($unset_vid) {
            if (isset($question->video_id)) {
                $video = MongoVideo::find($question->video_id);
                $video->unset('question_id');
            }
            $question->unset('video_id');
            $question->unset('video_path');
        }

        $this->updatePinQuestion($question, $request);

        return redirect()->route('question.index.admin')->with('success', 'تغییرات ثبت شد');
    }

    private function uploadAndResizeImage($image, $path, $filename, $quality, $thumb)
    {
        $disk = Storage::disk('ftp');

        if ($thumb == 1) {
            $resizedImage = Image::make($image)->resize(256, null, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('webp', $quality);
        } else {
            $resizedImage = Image::make($image)->encode('webp', $quality);
        }
        $disk->put($path . $filename, (string) $resizedImage);
    }

    private function updatePinQuestion($question, $request)
    {
        $last_pin_cat_ids = MongoCategory::where('pin_question_ids', $question->id)->pluck('_id')->toArray();
        $new_pin_cat_ids = array_filter(explode(',', $request->pin_cat_ids));
        $categories_to_remove = array_diff($last_pin_cat_ids, $new_pin_cat_ids);
        foreach ($categories_to_remove as $cat_id) {
            $category = MongoCategory::find($cat_id);
            if ($category) {
                $pin_question_ids = $category->pin_question_ids ?? [];
                $pin_question_ids = array_diff($pin_question_ids, [$question->id]);
                if (empty($pin_question_ids)) {
                    $category->unset('pin_question_ids');
                } else {
                    $category->pin_question_ids = array_values($pin_question_ids);
                }
                $category->save();
            }
        }
        foreach ($new_pin_cat_ids as $cat_id) {
            $category = MongoCategory::find($cat_id);
            if ($category) {
                $pin_question_ids = $category->pin_question_ids ?? [];
                if (!in_array($question->id, $pin_question_ids)) {
                    $pin_question_ids[] = $question->id;
                    $category->pin_question_ids = array_values($pin_question_ids);
                    $category->save();
                }
            }
        }
        $last_pin_item_ids = MongoItem::where('pin_question_ids', $question->id)->pluck('_id')->toArray();
        $new_pin_item_ids = array_filter(explode(',', $request->pin_item_ids));
        $items_to_remove = array_diff($last_pin_item_ids, $new_pin_item_ids);
        foreach ($items_to_remove as $item_id) {
            $item = MongoItem::find($item_id);
            if ($item) {
                $pin_question_ids = $item->pin_question_ids ?? [];
                $pin_question_ids = array_diff($pin_question_ids, [$question->id]);

                if (empty($pin_question_ids)) {
                    $item->unset('pin_question_ids');
                } else {
                    $item->pin_question_ids = array_values($pin_question_ids);
                }
                $item->save();
            }
        }
        foreach ($new_pin_item_ids as $item_id) {
            $item = MongoItem::find($item_id);
            if ($item) {
                $pin_question_ids = $item->pin_question_ids ?? [];
                if (!in_array($question->id, $pin_question_ids)) {
                    $pin_question_ids[] = $question->id;
                    $item->pin_question_ids = array_values($pin_question_ids);
                    $item->save();
                }
            }
        }
    }


    public function userQuestionUpdate(Request $request)
    {
        $question = MongoQuestion::find($request->question_id);
        $user = auth('user')->user();
        if (!isset($user) || $user->id != $question->user_id) {
            return back()->with('success', 'دسترسی ندارید');
        }
        $this->validate($request, [
            'title' => 'required',
        ], [
            'title.required' => 'عنوان سوال را بنویسید',
        ]);

        $question->title = $request->title;

        $editor_service = new CommentEditorService();
        $editor_service->update('edit_question', $request->body, $question);

        $category = MongoCategory::find($question->category_id);

        $addItemService = new AdditemsService();
        $last_items = $question->items ?? [];
        $add_item_result = $addItemService->addForUpdate($category, $last_items, $request);
        $items = $add_item_result['items'];
        $items_title = $add_item_result['items_title'];
        $changeStatus = $add_item_result['changeStatus'];

        if (count($items) > 0) {
            $question->items = $items;
        }
        if (count($items_title) > 0) {
            $question->items_title = $items_title;
        }

        if ($question->status == 1) {
            if ($changeStatus) {
                $question->status = 0;
            } else {
                $question->status = 1;
            }
        }

        $question->update();

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => $user->username . ' پرسش با عنوان ' . $question->title . ' را ویرایش کرد',
                'route' => route('question.show', $question->slug2),
            ]));
        }

        return redirect()->route('user.dashboard.edit', 'forum')->with('success', 'تغییرات ثبت شد');
    }

    public function like($question_id, $user_id)
    {
        $question_like = QuestionLike::where('question_id', $question_id)->where('user_id', $user_id)->first();
        if (isset($question_like)) {
            $question_like->delete();
        } else {
            $question_like = new QuestionLike();
            $question_like->question_id = $question_id;
            $question_like->user_id = $user_id;
            $question_like->save();
        }
    }
    public function randomQuestion(Request $request)
    {
        $question = Question::inRandomOrder()->first();
        return response()->json(
            [
                'username' => $question->user->username,
                'src' => asset($question->user->image()),
                'title' => $question->title,
                'body' => Str::limit($question->body, 100, '...'),
                'url' => route('question.show', $question->slug2),
            ],
            200
        );
    }
}
