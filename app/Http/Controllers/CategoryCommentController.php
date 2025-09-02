<?php

namespace App\Http\Controllers;

use App\Jobs\DoAfterStoreComment;
use App\Jobs\Item\ChangeItemPageCount;
use App\Jobs\pages\UpdateHotPages;
use App\Jobs\SendEmailCategoryComment;
use App\Jobs\SendUserNotification;
use App\Jobs\User\UpdateUserFollowItem;
use App\Models\Admin;
use App\Models\CategoryCommentEditorImage;
use App\Models\MongoCategory;
use App\Models\MongoCategoryComment;
use App\Models\MongoCategoryCommentLike;
use App\Models\MongoItem;
use App\Models\MongoQuestion;
use App\Models\MongoUser;
use App\Models\MongoVideo;
use App\Models\RtablePageData;
use App\Models\SurveyOption;
use App\Notifications\SiteEvent;
use App\Repositories\Category\Mongodb\CategoryRepository;
use App\Repositories\CategoryComment\Mongodb\CategoryCommentRepository;
use App\Repositories\Feature\Mongodb\FeatureRepository;
use App\Services\Affilate\AffilateService;
use App\Services\Comment\CommentEditorService;
use App\Services\Item\AdditemsService;
use App\Services\Survey\SurveyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CategoryCommentController extends Controller
{

    public function index($request, $suggestionService, $category_slug)
    {
        $category_comment_repository = new CategoryCommentRepository();

        $title = "";
        $selectedFeatures = collect();
        $followFeature = null;
        $item = null;
        $data = new RtablePageData();

        $user = null;
        if (auth('user')->check()) {
            $user = auth('user')->user();
        }

        $is_admin = null;
        if (auth('admin')->check()) {
            $is_admin = 1;
        }

        $meta_title = null;
        $meta_desc = null;
        $meta_desc_editor = null;

        $page_intro_title = null;
        $page_intro_desc = null;

        $hasComments = 1;

        if ($category_slug != null) {
            $category = MongoCategory::where('slug', $category_slug)->first();
            if (!isset($category)) {
                return redirect()->route('home')->with('success', 'آدرس صفحه تغییر کرده است، از منو سایت دوباره جستجو کنید');
            }

            app(SiteCategoryController::class)->redirectIfPageNotExist($request, $category, 'comments');

            $categoryFeatures = $category->features()->where('is_in_filter_rtable', 1);
            $childFeature = $categoryFeatures->sortByDesc('level')->first();
            $featuresInUrl = collect($data->getFeaturesInUrl($request))
                ->map(function ($fiu) {
                    return explode('=', $fiu)[0];
                });
            if ($featuresInUrl->isNotEmpty()) {
                $featuresBySlug = $categoryFeatures->keyBy('slug');
                // گرفتن ویژگی هایی که در ادرس صفحه هستن
                $featuresIsInUrl = $featuresInUrl
                    ->map(fn($slug) => $featuresBySlug->get($slug))
                    ->filter();

                if ($featuresIsInUrl->isNotEmpty()) {
                    if (!$featuresInUrl->contains($childFeature->slug)) {
                        $childFeature = $featuresIsInUrl->sortByDesc('level')->first();
                    }
                }
            }

            $comments = collect();

            if (isset($childFeature)) {
                $item = MongoItem::where('feature_id', $childFeature->id)->where('slug', $request[$childFeature->slug])->first();
                if ($childFeature->has_follow) {
                    $followFeature = $childFeature;
                }
            }

            $comments = app(IndexController::class)->getMainComments($category->id, isset($item) ? $item->id : 'null', 'null');
            $hasNextPage = count($comments) > 40 ? 1 : 0;

            $get_top_comments_data = app(IndexController::class)->selectTopComments($comments, 1);
            $comments = $get_top_comments_data['comments'];
            $acceptedAnswer = $get_top_comments_data['acceptedAnswer'];

            if ($hasNextPage) {
                $nextPageUrl = app(IndexController::class)->getCommentsNextPageUrl($comments, isset($category) ? $category->id : 'null', isset($item) ? $item->id : 'null', 'null', 1, $request->cri);
            } else {
                $nextPageUrl = null;
            }

            if ($comments->isEmpty()) {
                $comments = MongoCategoryComment::orderBy('created_at', 'desc')
                    ->whereNull('parent_id')
                    ->take(20)
                    ->with('user')
                    ->get();
                $hasComments = 0;
            }

            $pin_questions = collect();

            if (isset($followFeature) && isset($item)) {
                $metaData = app(IndexController::class)->generateMetaDataForItem($item, $category, $followFeature);
                $title = $metaData['title'] ?? null;
                $meta_title = $metaData['meta_title'] ?? null;
                $meta_desc = $metaData['meta_desc'] ?? null;
                $meta_desc_editor = $metaData['meta_desc_editor'] ?? null;
                $page_intro_title = $metaData['page_intro_title'] ?? null;
                $page_intro_desc = $metaData['page_intro_desc'] ?? null;

                $pageLinksData = app(IndexController::class)->generatePageLinksForItem($category, $item);
                $forum_page = $pageLinksData['forum_page'] ?? null;
                $blog_page = $pageLinksData['blog_page'] ?? null;
                $advertise_page = $pageLinksData['advertise_page'] ?? null;

                if (isset($item->has_rcats)) {
                    $ircats = MongoCategory::select('_id', 'title', 'slug', 'image')
                        ->whereIn('_id', $item->has_rcats)
                        ->get()
                        ->shuffle();
                }

                if (isset($item->pin_question_ids)) {
                    $pin_questions = MongoQuestion::select('_id', 'title', 'sug_title', 'slug2', 'answer', 'image')
                        ->whereIn('_id', $item->pin_question_ids)
                        ->get()
                        ->shuffle();
                }
                // if (isset($item->videos)) {
                //     $ivids = MongoVideo::find($item->videos)->shuffle()->first();
                //     if (isset($ivids)) {
                //         $item_video = $ivids;
                //     }
                // }
            } else {
                $metaData = app(IndexController::class)->generateMetaDataForCat($category);
                $title = $metaData['title'] ?? null;
                $meta_title = $metaData['meta_title'] ?? null;
                $meta_desc = $metaData['meta_desc'] ?? null;

                $pageLinksData = app(IndexController::class)->generatePageLinksForCat($category, $item);
                $forum_page = $pageLinksData['forum_page'] ?? null;
                $blog_page = $pageLinksData['blog_page'] ?? null;
                $advertise_page = $pageLinksData['advertise_page'] ?? null;
            }

            $suggests = $suggestionService->suggest($category, $item);
            if (isset($suggests['items'])) {
                $suggetItems = $suggests['items'];
            } else {
                $suggestCats = $suggests['cats'];
            }

            // if ($pin_questions->count() < 3) {
            //     $other_pin_questions = MongoQuestion::select('_id', 'title', 'sug_title', 'slug2', 'answer', 'image')
            //         ->where('just_this_page', 0)
            //         ->take(20)
            //         ->get();
            //     $other_pin_questions = $other_pin_questions->shuffle();
            //     $pin_questions = $pin_questions->merge($other_pin_questions)->unique('_id')->take(3);
            // }
            $affilateService = new AffilateService();
            $affilates = $affilateService->suggestsForPages($category, $item, 3);

            $currentQueryParams = $request->query();

            $comments = $this->sendCommentRefferIdToTop($request, $comments);

            // $hot_pages = Cache::get('hot_pages');

            $compactVars = [
                'is_admin',
                'page_intro_title',
                'page_intro_desc',
                'acceptedAnswer',
                'hasComments',
                'nextPageUrl',
                'meta_title',
                'meta_desc',
                'meta_desc_editor',
                'item',
                'comments',
                'category',
                'title',
                'currentQueryParams'
            ];
            if (isset($ircats)) {
                $compactVars[] = 'ircats';
            }
            // if (isset($item_video)) {
            //     $compactVars[] = 'item_video';
            // }
            if (isset($affilates)) {
                $compactVars[] = 'affilates';
            }
            if (isset($pin_questions) && !$pin_questions->isEmpty()) {
                $compactVars[] = 'pin_questions';
            }
            if (isset($forum_page)) {
                $compactVars[] = 'forum_page';
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
            return view('category.comment.index', compact(...$compactVars));
        } else {
            $suggests = $suggestionService->suggest();
            $suggestCats = $suggests['cats'];

            $comments = MongoCategoryComment::orderBy('created_at', 'desc')
                ->whereNull('parent_id')
                ->take(100)
                ->with('user')
                ->get();
            $lastComments = $comments->take(30);
            $firstComs = $lastComments->take(3);
            $topUnLikes = $lastComments->sortByDesc('unlike_count')->take(3);
            $topLikes = $lastComments->sortByDesc('like_count')->take(3);
            $comments = $firstComs->merge($topUnLikes)->merge($topLikes)->merge($comments)->unique()->take(20);

            $nextPageUrl = app(IndexController::class)->getCommentsNextPageUrl($comments, 'null', 'null', 'null', 1, $request->cri);

            $comments = $this->sendCommentRefferIdToTop($request, $comments);

            $forum_page = route('question.index');
            $advertise_page = route('ads.index');
            $blog_page = route('blog.index');

            $compactVars = [
                'is_admin',
                'suggestCats',
                'forum_page',
                'blog_page',
                'hasComments',
                'advertise_page',
                'nextPageUrl',
                'comments',
                'title'
            ];

            return view('category.comment.index', compact(...$compactVars));
        }
    }

    public function sendCommentRefferIdToTop($request, $comments)
    {
        $reffer_comment_id = $request->cri;

        if (isset($reffer_comment_id)) {
            $reffer_comment = $comments->find($reffer_comment_id);

            if ($reffer_comment) {
                $comments = $comments->reject(function ($comment) use ($reffer_comment_id) {
                    return $comment->id == $reffer_comment_id;
                });

                $comments->prepend($reffer_comment);
            } else {
                $mongoCategoryComment = MongoCategoryComment::find($reffer_comment_id);

                if ($mongoCategoryComment) {
                    $comments->prepend($mongoCategoryComment);
                }
            }
        }
        return $comments;
    }

    public function getCommentsPaginatePage($query, $category_id, $page, $lastId, $cri = null)
    {
        $category_repository = new CategoryRepository();
        $category_comment_repository = new CategoryCommentRepository();

        $category = $category_repository->getCategoryByIds($category_id);
        $featuresInUrl = explode("---", $query);
        $allComments = collect();
        if ($category_id != 'null') {
            if ($query != 'null') {
                foreach ($featuresInUrl as $f) {
                    $fea = explode("=", $f);
                    if (isset($fea[1])) {
                        $item_id = $fea[1];
                        $comments = $category_comment_repository->getParentCommentsByItemId($category->id, $item_id, 120);
                        if (count($allComments) > 0) {
                            $allComments = $allComments->intersect($comments);
                        } else {
                            $allComments = $comments;
                        }
                    }
                }
            } else {
                $allComments = $category_comment_repository->getParentComments(100);
            }
        } else {
            $allComments = $category_comment_repository->getParentComments(100);
            $lastComments = $allComments->take(30);
            $firstComs = $lastComments->take(3);
            $topUnLikes = $lastComments->sortByDesc('unlike_count')->take(3);
            $topLikes = $lastComments->sortByDesc('like_count')->take(3);
            $allComments = $firstComs->merge($topUnLikes)->merge($topLikes)->merge($allComments)->unique();
        }
        if (count($allComments) > 0) {
            if ($category_id != 'null') {
                $lastComments = $allComments->take(30);
                $firstComs = $lastComments->take(3);
                $topUnLikes = $lastComments->sortByDesc('unlike_count')->take(3);
                $topLikes = $lastComments->sortByDesc('like_count')->take(3);
                $allComments = $firstComs->merge($topUnLikes)->merge($topLikes)->merge($allComments)->unique();
            }

            foreach ($allComments as $key => $c) {
                if ($c->id != $lastId) {
                    $allComments->forget($key);
                } else {
                    $allComments->forget($key);
                    break;
                }
            }
            foreach ($allComments as $key => $c) {
                if ($c->id == $cri) {
                    $allComments->forget($key);
                }
            }

            $allComments = $allComments->take(20);

            foreach ($allComments as $newComment) {
                $editor_service = new CommentEditorService();
                $editor_service->changeTempEditorLazyImg($newComment);
            }

            if (count($allComments) > 0) {
                if ($page == 4) {
                    $nextPageUrl = null;
                } else {
                    if ($category_id != 'null') {
                        $nextPageUrl = route('api.get.comments.page', ['query' => $query, 'category_id' => $category, 'page' => $page + 1, 'lastId' => $allComments->last()->id, 'cri' => isset($cri) ? $cri : null]);
                    } else {
                        $nextPageUrl = route('api.get.comments.page', ['query' => $query, 'category_id' => 'null', 'page' => $page + 1, 'lastId' => $allComments->last()->id, 'cri' => isset($cri) ? $cri : null]);
                    }
                }
                return response()->json([
                    'comments' => $allComments,
                    'nextPageUrl' => $nextPageUrl,
                    'html' => view('question.comment-items', ['firstItems' => 0, 'comments' => $allComments])->render(),
                    'scrollTo' => 'comment-box-' . $allComments->first()->id
                ], 200);
            } else {
                return response()->json([
                    'comments' => null,
                    'nextPageUrl' => null,
                ], 200);
            }
        } else {
            return response()->json([
                'comments' => null,
                'nextPageUrl' => null,
            ], 200);
        }
    }

    public function adminIndex()
    {
        $comments = MongoCategoryComment::orderBy('created_at', 'desc')->paginate(100);
        return view('category.comment.admin-index', compact('comments'));
    }

    public function createAdmin(Request $request)
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
        return view('category.comment.admin-create', compact('categories'));
    }

    public function storeAdmin(Request $request)
    {
        $this->validate($request, [
            'body' => 'required',
        ], [
            'body.required' => 'نظر نمیتواند خالی باشد',
        ]);
        $user_id = app(UserController::class)->fakeRegisterSend($request->name, $request->username);
        $comment = new MongoCategoryComment();
        $comment->user_id = $user_id;

        if (isset($request->parent_id)) {
            $comment->parent_id = $request->parent_id;
            $parent_comment = MongoCategoryComment::find($request->parent_id);
            $this->setCommentRepliesCount($parent_comment, 1);
            $comment->category_id = $parent_comment->category_id;
            if (isset($request->reply_id)) {
                $reply_comment = MongoCategoryComment::find($request->reply_id);
                if (isset($reply_comment)) {
                    $comment->reply_id = $request->reply_id;
                    $comment->reply_name = $reply_comment->user->username;
                }
            }
            $comment->body = $request->body;
        } else {
            $category = MongoCategory::find($request->category_id);
            if (!isset($category)) {
                return back()->with('success', 'دسته بندی وجود ندارد');
            }

            if ($request->filled('images')) {
                $address = $category->slug;
                $images = app(InputImagesController::class)->setImagesArrayForStore($request->images, 'ccomment', $address);
                $comment->iimages = $images; // فیلد json
            }


            $comment->category_id = $category->id;

            $addItemService = new AdditemsService();
            $add_item_result = $addItemService->addForCreate($category, $request);
            $items = $add_item_result['items'];
            $items_title = $add_item_result['items_title'];
            $changeStatus = $add_item_result['changeStatus'];

            if (count($items) > 0) {
                $comment->items = $items;
                dispatch(new ChangeItemPageCount($items, 'comment', 1))->onQueue('becharkhsite')->delay(now()->addMinutes(5));
            }
            if (count($items_title) > 0) {
                $comment->items_title = $items_title;
            }

            if ($changeStatus) {
                $comment->status = 0;
            } else {
                $comment->status = 1;
            }
            $editor_service = new CommentEditorService();
            $editor_images = $editor_service->store('admin_create_comment', $request->body, $comment);
        }

        $survey_service = new SurveyService();
        $survey_service->addSurveyTo($comment, $request);

        $comment->save();

        if (!isset($request->parent_id)) {
            $editor_service->updateImageCommentId($editor_images, $comment->id);
        }

        $this->updateHotItems();

        if (isset($comment->parent_id)) {
            $user = MongoUser::find($user_id);
            dispatch(new SendEmailCategoryComment($comment->parent_id, $request->reply_id, $user))->onQueue('becharkhsite')->delay(now()->addMinutes(1));
            $this->sendUserNotification('ccomment', $user, $comment);
        }

        if (isset($comment->items)) {
            dispatch(new UpdateUserFollowItem('ccomment', $comment->id))->onQueue('becharkhsite')->delay(now()->addMinutes(1));
        }

        return redirect()->route('admin.category.comment.index')->with('success', 'نظر با موفقیت ثبت شد');
    }


    // action 1 = increase and 0 = decrease
    private function setCommentRepliesCount($comment, $action)
    {
        $replies_count = $comment->replies_count ?? 0;
        if ($action) {
            $comment->replies_count = $replies_count + 1;
            $comment->update();
        } else {
            $new_replies_count = $replies_count - 1;
            if ($new_replies_count <= 0) {
                $comment->unset('replies_count');
            } else {
                $comment->replies_count = $new_replies_count;
                $comment->update();
            }
        }
    }

    public function editAdmin(Request $request, $comment_id)
    {
        $comment = MongoCategoryComment::find($comment_id);
        if (!isset($comment->parent_id)) {
            $editor_service = new CommentEditorService();
            $editor_service->changeTempEditorLazyImg($comment);
            $categories = MongoCategory::where('status', 1)->get();

            $category = null;
            $cfeatures = collect();
            $citems = collect();
            if (isset($comment->category_id)) {
                $category = $categories->find($comment->category_id);
                if ($category) {
                    $cfeatures = $category->features()->where('is_in_filter_rtable', 1);
                    $citems = MongoItem::where('category_id', $category->id)->where('status', 1)->get();
                }
            }

            //for fix order object items
            $commentFeatueItems = $comment->getItems();
            $itemIds = $comment->items;
            if ($itemIds) {
                $itemIdPositionMap = array_flip($itemIds);
                $orderedItems = $commentFeatueItems->sortBy(function ($item) use ($itemIdPositionMap) {
                    return $itemIdPositionMap[$item->_id];
                });
                $commentFeatueItems = $orderedItems->values()->reverse();
            }
            //end for fix order object items

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
            $commentFeatueItems = $commentFeatueItems->map(function ($fi) {
                return [
                    'f_id' => $fi->feature_id,
                    'i_id' => $fi->id,
                ];
            })->values();

            $compactVars = [
                'comment',
                'category',
                'categories',
                'cfeatures',
                'citems',
                'commentFeatueItems'
            ];

            if (isset($comment->question_id)) {
                $question_ids = []; // Initialize the array
                $question_ids[] = $comment->question_id;
                $questionIds = $comment->question_id;
                $questionSelects = MongoQuestion::whereIn('_id', $question_ids)->select('title')->get();
                $compactVars[] = 'questionIds';
                $compactVars[] = 'questionSelects';
            }


            return view('category.comment.admin-edit', compact(...$compactVars));
        } else {
            $parent = $comment->parent;
            if (isset($parent->category_id)) {
                $category = $parent->category;
            } else {
                $category = null;
            }
            $categories = null;
            $citems = null;
            $cfeatures = null;
            $commentFeatueItems = null;
            $parent_item = $parent->getItems();
            $parent_url = null;
            if ($parent_item->isNotEmpty()) {
                $parent_url = $parent->getItems()->last()->withParentsCommentUrl();
            }
            return view('category.comment.admin-edit', compact('parent_url', 'parent', 'comment', 'category', 'categories', 'citems', 'cfeatures', 'commentFeatueItems'));
        }
    }

    public function updateAdmin(Request $request, $comment_id)
    {
        $this->validate($request, [
            'body' => 'required'
        ], [
            'body.required' => 'نظر الزامی می باشد',
        ]);

        $comment = MongoCategoryComment::find($comment_id);

        if (!isset($comment->parent_id)) {
            if ($request->remove_from_ccom != 1) {
                if (isset($request->category_id)) {
                    if ($comment->category_id != $request->category_id) {
                        $comment->category_id = $request->category_id;
                    }
                    $category = MongoCategory::find($request->category_id);
                    $addItemService = new AdditemsService();
                    $last_items = $comment->items ?? [];
                    $add_item_result = $addItemService->addForUpdate($category, $last_items, $request);
                    $items = $add_item_result['items'];
                    $items_title = $add_item_result['items_title'];
                    if (count($items) > 0) {
                        $comment->items = $items;
                    }
                    if (count($items_title) > 0) {
                        $comment->items_title = $items_title;
                    }
                }
            }
            $editor_service = new CommentEditorService();
            $editor_service->update('admin_edit_comment', $request->body, $comment);

            $comment->editor2 = $request->editor2;

            $questions = array_filter(explode(',', $request->questions));
            $unset_ques = 0;
            if (count($questions) > 0) {
                $comment->question_id = $questions[0];
            } else {
                $unset_ques = 1;
            }

            $unset_best_answ = 0;
            if (isset($request->best_answer) && $request->best_answer == 1) {
                $comment->best_answer = 1;
            } else {
                $unset_best_answ = 1;
            }

            if ($request->filled('images')) {
                $address = $category->slug;
                $images = app(InputImagesController::class)->setImagesArrayForUpdate($request->images, 'ccomment', $comment, $address);
                $comment->iimages = $images; // فیلد json
            }
        } else {
            $comment->body = $request->body;
        }

        if ($comment->status == 0) {
            $comment->status = 1;
        }

        $comment->update();

        if ($request->remove_from_ccom == 1) {
            $comment->unset('category_id');
            $comment->unset('items');
            $comment->unset('items_title');
        }
        if (isset($unset_ques) && $unset_ques == 1) {
            $comment->unset('question_id');
        }
        if (isset($unset_best_answ) && $unset_best_answ == 1) {
            $comment->unset('best_answer');
        }

        $this->updateHotItems();

        return redirect()->route('admin.category.comment.index')->with('success', 'تغییرات ثبت شد');
    }

    public function deleteAdmin(Request $request)
    {
        $comment = MongoCategoryComment::find($request->comment_id);
        $comment_images = CategoryCommentEditorImage::where('comment_id', $comment->id)->get();
        $disk = Storage::disk('ftp');
        if (!$comment_images->isEmpty()) {
            foreach ($comment_images as $ci) {
                $disk->delete($ci->path);
                $ci->delete();
            }
        }
        if (isset($comment->surop1)) {
            $sur_options = SurveyOption::where('page', 'comment')->where('obj_id', $comment->id)->get();
            foreach ($sur_options as $so) {
                $so->delete();
            }
        }
        if (isset($comment->items) && count($comment->items) > 0) {
            dispatch(new ChangeItemPageCount($comment->items, 'comment', 0))->onQueue('becharkhsite')->delay(now()->addMinutes(5));
        }
        if (isset($comment->parent_id) || isset($comment->reply_id)) {
            $parent_comment = MongoCategoryComment::find($comment->parent_id);
            if (isset($parent_comment)) {
                $this->setCommentRepliesCount($parent_comment, 0);
                if (isset($parent_comment->question_id)) {
                    app(UserNotificationController::class)->deleteNotification('question_answer', $comment->id);
                }
                if (isset($parent_comment->category_id)) {
                    app(UserNotificationController::class)->deleteNotification('ccomment', $comment->id);
                }
            }
        }
        $likes = MongoCategoryCommentLike::where('comment_id', $comment->id)->get();
        foreach ($likes as $like) {
            $like->delete();
        }

        $images = $comment->iimages ?? [];
        foreach ($images as $image) {
            if (isset($image['path']) && $disk->exists($image['path'])) {
                $disk->delete($image['path']);
            }
        }

        $comment->delete();
        return back()->with('success', 'با موفقیت حذف شد');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'body' => 'required',
        ], [
            'body.required' => 'نظر خود را بنویسید',
        ]);
        $user = auth('user')->user();
        if (!$user) {
            abort(403);
        }

        $page = $request->page;
        if ($page == 'show_question') {
            $object = MongoQuestion::find($request->object_id);
            if (!isset($object)) {
                return back()->with('success', 'صفحه وجود ندارد');
            }
        } elseif ($page == 'comment') {
            $object = MongoCategory::find($request->object_id);
            if (!isset($object)) {
                return back()->with('success', 'دسته بندی وجود ندارد');
            }
        }

        $this->storeCommentSection($request, $user, $page, $object);

        return back()->with('success', 'نظر شما با موفقیت ثبت شد');
    }

    public function storeWithoutRefresh(Request $request)
    {
        if (!isset($request->object_id) || !isset($request->parent_id)) {
            return response()->json(['error' => 'خطایی رخ داد'], 404);
        }
        if (!isset($request->body) || trim($request->body) === '') {
            return response()->json(['error' => 'دیدگاه خود را بنویسید.'], 404);
        }
        $user = auth('user')->user();
        if (!$user) {
            return response()->json(['error' => 'وارد حساب کاربری خود شوید.'], 401);
        }

        $page = $request->page;
        if ($page == 'show_question') {
            $object = MongoQuestion::find($request->object_id);
            if (!isset($object)) {
                return back()->with('success', 'صفحه وجود ندارد');
            }
        } elseif ($page == 'comment') {
            $object = MongoCategory::find($request->object_id);
            if (!isset($object)) {
                return back()->with('success', 'دسته بندی وجود ندارد');
            }
        }

        $comment = $this->storeCommentSection($request, $user, $page, $object);

        return response()->json([
            'success' => 'نظر شما با موفقیت ثبت شد',
            'comment' => [
                'id' => $comment->id,
                'body' => $request->body,
                'username' => $user->username,
                'user_image' => $user->thumb(),
                'like_count' => 0,
                'unlike_count' => 0,
                'object_id' => $request->object_id,
                'parent_id' => $request->parent_id,
                'reply_id' => $request->reply_id,
                'reply_name' => $comment->reply_name,
            ]
        ], 201);
    }

    private function storeCommentSection($request, $user, $page, $object)
    {
        $comment = new MongoCategoryComment();
        $comment->body = $request->body;
        $comment->user_id = $user->id;
        $item = null;

        if ($page == 'show_question') {
            $comment->question_id = $object->id;
        } elseif ($page == 'comment') {
            $comment->category_id = $object->id;
        }

        //if comment is not main comment 
        if (isset($request->parent_id)) {
            $parent_comment = MongoCategoryComment::find($request->parent_id);
            if (isset($parent_comment)) {
                $comment->parent_id = $parent_comment->id;
                $this->setCommentRepliesCount($parent_comment, 1);
                //if comment was reply to reply
                if (isset($request->reply_id)) {
                    $reply_comment = MongoCategoryComment::find($request->reply_id);
                    if (isset($reply_comment)) {
                        $comment->reply_id = $request->reply_id;
                        $comment->reply_name = $reply_comment->user->username;
                    }
                }
            }
        }
        if ($page == 'comment') {
            if (isset($request->item_id)) {
                $item = MongoItem::find($request->item_id);
                $fv = [];
                $items_title = [];
                $fv[] = $item->id;
                $items_title[] = $item->full_title ?? $item->title;
                foreach ($item->parents() as $i) {
                    $fv[] = $i->id;
                    $items_title[] = $i->full_title ?? $i->title;
                }
                dispatch(new ChangeItemPageCount($fv, 'comment', 1))->onQueue('becharkhsite')->delay(now()->addMinutes(5));
                $comment->items = $fv;
                $comment->items_title = $items_title;
            }
            $survey_service = new SurveyService();
            $survey_service->addSurveyTo($comment, $request);
        }

        $comment->save();

        $item_id = 'null';
        if (isset($item)) {
            $item_id = $item->id;
        }
        if (isset($object)) {
            $object_id = $object->id;
        }

        dispatch(new DoAfterStoreComment(
            $page ?? 'null',
            $user->id ?? 'null',
            $comment->id ?? 'null',
            $object_id,
            $item_id,
            [
                'reply_id' => $request->reply_id ?? null,
                'item_id'  => $request->item_id ?? null,
            ]
        ))->onQueue('becharkhsite')->delay(now()->addSeconds(10));

        return $comment;
    }

    private function sendUserNotification($forr, $from_user, $new_object)
    {
        dispatch(new SendUserNotification($forr, $from_user, $new_object))->onQueue('becharkhsite')->delay(now()->addMinutes(1));
    }

    private function updateHotItems()
    {
        // $update_hot_items_cache_key = 'is_update_hitems';
        // if (!Cache::has($update_hot_items_cache_key)) {
        //     Cache::put($update_hot_items_cache_key, true, 3600);
        //     dispatch(new UpdateHotItems())->onQueue('becharkhsite')->delay(now()->addHours(1));
        // }
        $update_hot_pages_cache_key = 'is_update_hpages';
        if (!Cache::has($update_hot_pages_cache_key)) {
            Cache::put($update_hot_pages_cache_key, true, 1800);
            dispatch(new UpdateHotPages())->onQueue('becharkhsite')->delay(now()->addMinutes(30));
        }
    }

    // private function NE($fromUser, $toUser, $route, $title)
    // {
    //     if (isset($toUser)) {
    //         Mail::to($toUser->email)->send(new ReplyToCommentMail($title, $fromUser->username, $route));
    //         // $toUser->notify(new UserNotif([
    //         //     'action' => ' یک نظر جدید از ' . $fromUser->username . ' در صفحه ' . $title . ' دریافت کرده اید ',
    //         //     'route' => $route,
    //         //     'userImage' => asset($fromUser->image()),
    //         //     'pageImage' => null,
    //         //     'pageType' => 'blog',
    //         //     'notifType' => 'comment',
    //         //     'important' => 0,
    //         //     'pageId' => null,
    //         //     'userId' => $fromUser->id,
    //         // ]));
    //     }
    // }
}
