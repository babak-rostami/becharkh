<?php

namespace App\Http\Controllers;

use App\Jobs\Item\ChangeItemPageCount;
use App\Jobs\pages\UpdateHotPages;
use App\Jobs\SendEmailCategoryComment;
use App\Jobs\SendUserNotification;
use App\Models\Admin;
use App\Models\CategoryCommentEditorImage;
use App\Models\MongoCategory;
use App\Models\MongoCategoryComment;
use App\Models\MongoItem;
use App\Models\MongoQuestion;
use App\Models\MongoUser;
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

        $meta_title = null;
        $meta_desc = null;
        $meta_desc_editor = null;

        $hasComments = 1;

        if ($category_slug != null) {
            $feature_repository = new FeatureRepository();

            $category = MongoCategory::where('slug', $category_slug)->first();
            if (!isset($category)) {
                return redirect()->route('home')->with('success', 'آدرس صفحه تغییر کرده است، از منو سایت دوباره جستجو کنید');
            }

            app(SiteCategoryController::class)->redirectIfPageNotExist($request, $category, 'comments');


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

            $fiuforp = null;
            $comments = collect();

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
                        if ($fiuforp == null) {
                            $fiuforp = $f->id . "=" . $item->id;
                        } else {
                            $fiuforp .= "---" . $f->id . "=" . $item->id;
                        }
                        $tempComments = $category_comment_repository->getParentCommentsByItemId($category->id, $item->id, 120);
                        if (count($comments) > 0) {
                            $comments = $comments->intersect($tempComments);
                        } else {
                            $comments = $tempComments;
                        }
                        //follow button
                        if ($f->has_follow) {
                            $followFeature = $f;
                        }
                    }
                }
            }

            if (isset($followFeature) && isset($item)) {
                if ($followFeature->item_is_in_title) {
                    if ($category->is_cat_in_title == 1) {
                        $ctitle = $category->full_title ?? $category->title;
                        $ctitle .=  " ";
                    } else {
                        $ctitle = "";
                    }
                    $title = $ctitle . ($followFeature->is_feature_in_title == 1 ? ' ' . $followFeature->title : '')  . ($item->full_title  ?? $item->title);
                }
                if (isset($item->title_in_comment)) {
                    $meta_title = str_replace("*", $title, $item->title_in_comment);
                } else {
                    if ($category->title_in_comment) {
                        $meta_title = str_replace("*", $title, $category->title_in_comment);
                    } else {
                        $meta_title = "نظرات کاربران درباره " . $title;
                    }
                }
                if (isset($item->desc_in_comment)) {
                    $meta_desc = str_replace("*", $title, $item->desc_in_comment);
                } else {
                    if ($category->desc_in_comment) {
                        $meta_desc = str_replace("*", $title, $category->desc_in_comment);
                    } else {
                        $meta_desc = "بحث و گفتگو با موضوع  " . $title;
                    }
                }
                if (isset($item->desc_in_comment_editor)) {
                    $meta_desc_editor = str_replace("*", $title, $item->desc_in_comment_editor);
                } else {
                    if ($category->desc_in_comment_editor) {
                        $meta_desc_editor = str_replace("*", $title, $category->desc_in_comment_editor);
                    }
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
                if ($category->title_in_comment) {
                    $meta_title = str_replace("*", $cat_title, $category->title_in_comment);
                } else {
                    $meta_title = "نظرات کاربران درباره " . $cat_title;
                }
                if ($category->desc_in_comment) {
                    $meta_desc = str_replace("*", $cat_title, $category->desc_in_comment);
                } else {
                    $meta_desc = "بحث و گفتگو با موضوع  " . $cat_title;
                }
                $comments = $category_comment_repository->getParentCommentsByCategoryId($category->id, 50);
            }

            $suggests = $suggestionService->suggest($category, $item);
            if (isset($suggests['items'])) {
                $suggetItems = $suggests['items'];
            } else {
                $suggestCats = $suggests['cats'];
            }

            $hasNextPage = count($comments) > 40 ? 1 : 0;
            $lastComments = $comments->take(30);
            $firstComs = $lastComments->take(3);
            $topUnLikes = $lastComments->sortByDesc('unlike_count')->take(3);
            $topLikes = $lastComments->sortByDesc('like_count')->take(3);
            $comments = $firstComs->merge($topUnLikes)->merge($topLikes)->merge($comments)->unique()->take(40);

            if (count($topUnLikes) > 0) {
                $acceptedAnswer = $topUnLikes->first();
            } else {
                if (count($topLikes) > 0) {
                    $acceptedAnswer = $topLikes->first();
                } else {
                    $acceptedAnswer = $comments->first();
                }
            }
            if ($hasNextPage) {
                if (isset($fiuforp)) {
                    $nextPageUrl = route('api.get.comments.page', ['query' => $fiuforp, 'category_id' => $category->id, 'page' => 2, 'lastId' => $comments->last()->id, 'cri' => $request->cri]);
                } else {
                    $nextPageUrl = route('api.get.comments.page', ['query' => 'null', 'category_id' => $category->id, 'page' => 2, 'lastId' => $comments->last()->id, 'cri' => $request->cri]);
                }
            } else {
                $nextPageUrl = null;
            }

            if ($comments->isEmpty()) {
                $comments = MongoCategoryComment::orderBy('created_at', 'desc')
                    ->whereNull('parent_id')
                    ->where('category_id', $category->id)
                    ->take(20)
                    ->with('user')
                    ->get();
                if ($comments->isEmpty()) {
                    $comments = MongoCategoryComment::orderBy('created_at', 'desc')
                        ->whereNull('parent_id')
                        ->take(20)
                        ->with('user')
                        ->get();
                }
                $hasComments = 0;
            }

            if (isset($item)) {
                if ($category->has_forums) {
                    $forum_page = $item->withParentsForumUrl();
                }
                if ($category->has_blogs) {
                    $blog_page = $item->withParentsBlogUrl();
                }
                if ($category->has_ads) {
                    $advertise_page = $item->withParentsAdvertiseUrl();
                }
                if (isset($item->pin_question_ids)) {
                    $pin_questions = MongoQuestion::select('_id', 'title', 'slug2', 'image')
                        ->whereIn('_id', $item->pin_question_ids)
                        ->get();
                }
            } else {
                if ($category->has_forums) {
                    $forum_page = route('question.index', $category->slug);
                }
                if ($category->has_blogs) {
                    $blog_page = route('blog.index', $category->slug);
                }
                if ($category->has_ads) {
                    $advertise_page = route('ads.index', $category->slug);
                }
                if (isset($category->pin_question_ids)) {
                    $pin_questions = MongoQuestion::select('_id', 'title', 'slug2', 'image')
                        ->whereIn('_id', $category->pin_question_ids)
                        ->get();
                }
            }
            $affilateService = new AffilateService();
            $affilate = $affilateService->suggestForPages($category, $item);

            // $features = $category->features();
            $currentQueryParams = $request->query();

            $comments = $this->sendCommentRefferIdToTop($request, $comments);

            $hot_pages = Cache::get('hot_pages');

            $compactVars = [
                'hot_pages',
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
                'currentQueryParams',
                'selected_items',
            ];
            if (isset($pin_questions) && !$pin_questions->isEmpty()) {
                $compactVars[] = 'pin_questions';
            }
            // if (isset($features)) {
            //     $compactVars[] = 'features';
            // }
            if (isset($affilate)) {
                $compactVars[] = 'affilate';
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

            // $hotVideos = MongoVideo::random(15, ['pr_link' => 'notnull']);

            $comments = $category_comment_repository->getParentComments(100);
            $lastComments = $comments->take(30);
            $firstComs = $lastComments->take(3);
            $topUnLikes = $lastComments->sortByDesc('unlike_count')->take(3);
            $topLikes = $lastComments->sortByDesc('like_count')->take(3);
            $comments = $firstComs->merge($topUnLikes)->merge($topLikes)->merge($comments)->unique()->take(20);

            $nextPageUrl = route('api.get.comments.page', ['query' => 'null', 'category_id' => 'null', 'page' => 2, 'lastId' => $comments->last()->id, 'cri' => $request->cri]);

            $comments = $this->sendCommentRefferIdToTop($request, $comments);

            $forum_page = route('question.index');
            $advertise_page = route('ads.index');
            $blog_page = route('blog.index');

            return view('category.comment.index', compact('suggestCats', 'forum_page', 'blog_page', 'hasComments', 'advertise_page', 'nextPageUrl', 'comments', 'title'));
        }
    }

    private function sendCommentRefferIdToTop($request, $comments)
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
            if (isset($request->reply_id)) {
                $comment->reply_id = $request->reply_id;
            }
            $comment->body = $request->body;
        } else {
            $category = MongoCategory::find($request->category_id);
            if (!isset($category)) {
                return back()->with('success', 'دسته بندی وجود ندارد');
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
            dispatch(new SendEmailCategoryComment($comment->parent_id, $request->reply_to_id, $user))->onQueue('becharkhsite');
            $this->sendUserNotification('ccomment', $user, $comment);
        }

        return redirect()->route('admin.category.comment.index')->with('success', 'نظر با موفقیت ثبت شد');
    }

    public function editAdmin(Request $request, $comment_id)
    {
        $comment = MongoCategoryComment::find($comment_id);
        if (!isset($comment->parent_id)) {
            $editor_service = new CommentEditorService();
            $editor_service->changeTempEditorLazyImg($comment);
            $categories = MongoCategory::where('status', 1)->get();
            $category = $categories->find($comment->category_id);
            $cfeatures = $category->features()->where('is_in_filter_rtable', 1);
            $citems = MongoItem::where('category_id', $category->id)->where('status', 1)->get();

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
            return view('category.comment.admin-edit', compact('comment', 'category', 'categories', 'cfeatures', 'citems', 'commentFeatueItems'));
        } else {
            $category = $comment->parent->category;
            $categories = null;
            $citems = null;
            $cfeatures = null;
            $commentFeatueItems = null;
            return view('category.comment.admin-edit', compact('comment', 'category', 'categories', 'citems', 'cfeatures', 'commentFeatueItems'));
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
            if (isset($request->category_id)) {
                if ($comment->category_id != $request->category_id) {
                    $comment->category_id = $request->category_id;
                }
            }
            $category = MongoCategory::find($request->category_id);

            $addItemService = new AdditemsService();
            $last_items = $comment->items ?? [];
            $add_item_result = $addItemService->addForUpdate($category, $last_items, $request);
            $items = $add_item_result['items'];
            $items_title = $add_item_result['items_title'];
            $changeStatus = $add_item_result['changeStatus'];

            if (count($items) > 0) {
                $comment->items = $items;
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
            $editor_service->update('admin_edit_comment', $request->body, $comment);
        } else {
            $comment->body = $request->body;
        }

        $comment->update();

        $this->updateHotItems();

        return redirect()->route('admin.category.comment.index')->with('success', 'تغییرات ثبت شد');
    }

    public function deleteAdmin(Request $request)
    {
        $comment = MongoCategoryComment::find($request->comment_id);
        $comment_images = CategoryCommentEditorImage::where('comment_id', $comment->id)->get();
        if (!$comment_images->isEmpty()) {
            $disk = Storage::disk('ftp');
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
        if (isset($comment->parent_id) || isset($comment->reply_to_id)) {
            app(UserNotificationController::class)->deleteNotification('ccomment', $comment->id);
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
        if (!auth('user')->check()) {
            abort(403);
        }

        $category = MongoCategory::find($request->category_id);
        if (!isset($category)) {
            return back()->with('success', 'دسته بندی وجود ندارد');
        }
        $comment = new MongoCategoryComment();

        // if (!isset($request->parent_id)) {
        //     $editor_service = new CommentEditorService();
        //     $editor_images = $editor_service->store('comment', $request->body, $comment);
        // } else {
        $comment->body = $request->body;
        // }

        $user = auth('user')->user();
        $comment->category_id = $category->id;
        $comment->user_id = $user->id;
        //if comment is not main comment 
        if (isset($request->parent_id)) {
            $comment->parent_id = $request->parent_id;
            //if comment was reply to reply
            if (isset($request->reply_to_id)) {
                $comment->reply_id = $request->reply_to_id;
            }
        }
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

        $comment->save();

        // if (!isset($request->parent_id)) {
        //     $editor_service->updateImageCommentId($editor_images, $comment->id);
        // }

        $this->updateHotItems();

        $admin = Admin::first();
        if (isset($comment->parent_id)) {
            dispatch(new SendEmailCategoryComment($comment->parent_id, $request->reply_to_id, $user))->onQueue('becharkhsite');
            $commentPage = route('question.index', $category->slug) . "?s=1";
            $this->sendUserNotification('ccomment', $user, $comment);
            $admin->notify(new SiteEvent([
                'action' => $user->username . ' یک ریپلای ارسال کرد',
                'route' => $commentPage,
            ]));
        } else {
            if (isset($request->item_id)) {
                $commentPage = $item->withParentsCommentUrl();
                $commentPageTitle = $item->full_title ?? $item->title;
            } else {
                $commentPage = route('question.index', $category->slug) . "?s=1";
                $commentPageTitle = $category->full_title ?? $category->title;
            }
            $admin->notify(new SiteEvent([
                'action' => $user->username . ' نظری در صفحه ' . $commentPageTitle . ' ارسال کرد',
                'route' => $commentPage,
            ]));
        }

        return back()->with('success', 'نظر شما با موفقیت ثبت شد');
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
