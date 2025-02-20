<?php

namespace App\Http\Controllers;

use App\Jobs\Item\ChangeItemPageCount;
use App\Models\Admin;
use App\Models\AdvertiseVideo;
use App\Models\Affilate;
use App\Models\Blog;
use App\Models\BlogEditorImage;
use App\Models\BlogFeatureValue;
use App\Models\Brand;
use App\Models\CategoryFeatureItem;
use App\Models\MongoBlog;
use App\Models\MongoBlogLike;
use App\Models\MongoCategory;
use App\Models\MongoFollowItem;
use App\Models\MongoItem;
use App\Models\MongoQuestion;
use App\Models\MongoVideo;
use App\Models\Question;
use App\Models\QuestionFeatureValue;
use App\Models\RtablePageData;
use App\Models\ShortLink;
use App\Models\SiteCategory;
use App\Models\UserCkImage;
use App\Models\VideoFeatureValue;
use App\Notifications\SiteEvent;
use App\Repositories\Category\Mongodb\CategoryRepository;
use App\Repositories\Feature\Mongodb\FeatureRepository;
use App\Repositories\Item\Mongodb\ItemRepository;
use App\Services\Affilate\AffilateService;
use App\Services\Comment\CommentEditorService;
use App\Services\Item\AdditemsService;
use App\Services\Suggestion\SuggestionService;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class BlogController extends Controller
{
    public function index(Request $request, SuggestionService $suggestionService, $category_slug = null)
    {
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
        $is_follow = 0;

        if ($category_slug != null) {
            $feature_repository = new FeatureRepository();
            $category = MongoCategory::where('slug', $category_slug)->first();
            if (!isset($category)) {
                return redirect()->route('home')->with('success', 'آدرس صفحه تغییر کرده است، از منو سایت دوباره جستجو کنید');
            }

            app(SiteCategoryController::class)->redirectIfPageNotExist($request, $category, 'blogs');

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

            $blogs = collect();

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
                        $tempBlogs = MongoBlog::orderBy('created_at', 'desc')->where('category_id', $category->id)->where('items', $item->id)->where('status', 1)->get();
                        if (count($blogs) > 0) {
                            $blogs = $blogs->intersect($tempBlogs);
                        } else {
                            $blogs = $tempBlogs;
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
                    $title = $ctitle . ($followFeature->is_feature_in_title == 1 ? $followFeature->title : '') . ' ' . ($item->full_title  ?? $item->title);
                }
                if (isset($item->title_in_blogs)) {
                    $meta_title = str_replace("*", $title, $item->title_in_blogs);
                } else {
                    if ($category->title_in_blogs) {
                        $meta_title = str_replace("*", $title, $category->title_in_blogs);
                    } else {
                        $meta_title = "مجله - " . $title;
                    }
                }
                if (isset($item->desc_in_blogs)) {
                    $meta_desc = str_replace("*", $title, $item->desc_in_blogs);
                } else {
                    if ($category->desc_in_blogs) {
                        $meta_desc = str_replace("*", $title, $category->desc_in_blogs);
                    } else {
                        $meta_desc = "مطالب و پست های کاربران با موضوع " . $title;
                    }
                }
                if (isset($item->desc_in_blogs_editor)) {
                    $meta_desc_editor = str_replace("*", $title, $item->desc_in_blogs_editor);
                } else {
                    if ($category->desc_in_blogs_editor) {
                        $meta_desc_editor = str_replace("*", $title, $category->desc_in_blogs_editor);
                    }
                }
                if ($user) {
                    $follow = MongoFollowItem::where('item_id', $item->id)->where('user_id', $user->id)->first();
                    if (isset($follow)) {
                        $is_follow = 1;
                    } else {
                        $is_follow = 0;
                    }
                }
            } else {
                $cat_title = $category->full_title ?? $category->title;
                if ($category->title_in_blogs) {
                    $meta_title = str_replace("*", $cat_title, $category->title_in_blogs);
                } else {
                    $meta_title = "مجله - " . $cat_title;
                }
                if ($category->desc_in_blogs) {
                    $meta_desc = str_replace("*", $cat_title, $category->desc_in_blogs);
                } else {
                    $meta_desc = "مطالب و پست های کاربران با موضوع " . $cat_title;
                }
                $blogs = MongoBlog::orderBy('created_at', 'desc')->where('category_id', $category->id)->where('status', 1)->get();
            }

            $suggests = $suggestionService->suggest($category, $item);
            if (isset($suggests['items'])) {
                $suggetItems = $suggests['items'];
            } else {
                $suggestCats = $suggests['cats'];
            }

            //clean query for paginate url
            $reqs = $data->getCurrentUrlWithoutPage($request);
            $blogs = $this->paginateC($blogs, 20, null, $reqs)->onEachSide(1);

            if (isset($item)) {
                if ($category->has_comments) {
                    $comment_page = $item->withParentsCommentUrl();
                }
                if ($category->has_forums) {
                    $forum_page = $item->withParentsForumUrl();
                }
                if ($category->has_ads) {
                    $advertise_page = $item->withParentsAdvertiseUrl();
                }
            } else {
                if ($category->has_comments) {
                    $comment_page = route('question.index', $category->slug) . '?s=1';
                }
                if ($category->has_forums) {
                    $forum_page = route('question.index', $category->slug);
                }
                if ($category->has_ads) {
                    $advertise_page = route('ads.index', $category->slug);
                }
            }

            // $features = $category->features();
            $currentQueryParams = $request->query();

            $compactVars = [
                'is_follow',
                'item',
                'meta_title',
                'meta_desc',
                'meta_desc_editor',
                'data',
                'blogs',
                'category',
                'title',
                'currentQueryParams',
                'selected_items',
            ];
            // if (isset($features)) {
            //     $compactVars[] = 'features';
            // }
            if (isset($forum_page)) {
                $compactVars[] = 'forum_page';
            }
            if (isset($advertise_page)) {
                $compactVars[] = 'advertise_page';
            }
            if (isset($comment_page)) {
                $compactVars[] = 'comment_page';
            }
            if (isset($suggetItems)) {
                $compactVars[] = 'suggetItems';
            } elseif (isset($suggestCats)) {
                $compactVars[] = 'suggestCats';
            }
            return view('blog.front-index', compact(...$compactVars));
        } else {
            $suggests = $suggestionService->suggest();
            $suggestCats = $suggests['cats'];

            $blogs = MongoBlog::orderBy('created_at', 'desc')->where('status', 1)->get();
            $blogs = $this->paginateC($blogs, 20, null, $request->url())->onEachSide(1);

            $comment_page = route('question.index') . '?s=1';
            $forum_page = route('question.index');
            $advertise_page = route('ads.index');

            return view('blog.front-index', compact('is_follow', 'suggestCats', 'forum_page', 'comment_page', 'advertise_page', 'data', 'blogs'));
        }
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

    public function show(Request $request, SuggestionService $suggestionService, $category_slug, $slug, $random_id = null)
    {
        $category = MongoCategory::where('slug', $category_slug)->first();
        if (isset($category)) {
            if (!isset($random_id)) {
                $blog = MongoBlog::where('category_id', $category->id)->where('slug', $slug)->first();
                return redirect(route('blog.show', ['category_slug' => $category->slug, 'slug' => $blog->slug, 'random_id' => $blog->random_id]));
            }
            $blog = MongoBlog::where('category_id', $category->id)->where('slug', $slug)->where('random_id', $random_id)->first();
        } else {
            return redirect()->route('home')->with('success', 'آدرس صفحه تغییر کرده است، از منو سایت دوباره جستجو کنید');
        }

        if (!isset($blog)) {
            return redirect()->route('home')->with('success', 'آدرس صفحه تغییر کرده است، از منو سایت دوباره جستجو کنید');
        }

        $blog_items = $blog->items ?? collect();
        $item = null;
        $item_id = null;
        $is_follow = 0;
        $user = null;
        if (auth('user')->check()) {
            $user = auth('user')->user();
        }
        $blogs = collect();
        foreach ($blog_items as $key => $blog_item) {
            if ($key == 0) {
                $item_id = $blog_item;
            }
            $tblog = MongoBlog::orderBy('created_at', 'desc')->where('category_id', $category->id)->where('items', $blog_item)->take(15)->where('id', '!=', $blog->id)->get();
            $blogs = $blogs->merge($tblog);
        }
        $blogs = $blogs->unique()->take(15);
        if (count($blogs) < 15) {
            $tblogs = MongoBlog::orderBy('created_at', 'desc')->where('category_id', $category->id)->take(15)->where('id', '!=', $blog->id)->get();
            $blogs = $blogs->merge($tblogs)->unique()->take(15);
        }
        if (count($blogs) < 15) {
            $remain_blog = 25 - count($blogs);
            $tblogs = MongoBlog::orderBy('created_at', 'desc')->where('status', 1)->take($remain_blog)->where('id', '!=', $blog->id)->get();
            $blogs = $blogs->merge($tblogs)->unique()->take(15);
        }

        if ($item_id) {
            $item = MongoItem::find($item_id);
            if (isset($item)) {
                $tab_title = $item->full_title ?? $item->title;
                if ($user) {
                    $follow = MongoFollowItem::where('item_id', $item->id)->where('user_id', $user->id)->first();
                    if (isset($follow)) {
                        $is_follow = 1;
                    } else {
                        $is_follow = 0;
                    }
                }
            }
        } else {
            $tab_title = $category->full_title ?? $category->title;
        }
        $suggests = $suggestionService->suggest($category, $item);
        if (isset($suggests['items'])) {
            $suggetItems = $suggests['items'];
        } else {
            $suggestCats = $suggests['cats'];
        }

        if (isset($blog_item)) {
            $hotQuestions = $this->getHotQuestions($category, $blog_item);
        } else {
            $hotQuestions = $this->getHotQuestions($category, null);
        }

        if (!isset($_COOKIE['page_seen'])) {
            $blog->seen_count += 1;
            $blog->update();
        }
        $blogVideo = $blog->video();

        $blog->content = preg_replace('/<img(.*?)src=\"(.*?)\"/', '<img$1class="lazy-load" data-src="$2"', $blog->content);

        if (isset($item)) {
            if ($category->has_comments) {
                $comment_page = $item->withParentsCommentUrl();
            }
            if ($category->has_forums) {
                $forum_page = $item->withParentsForumUrl();
            }
            if ($category->has_ads) {
                $advertise_page = $item->withParentsAdvertiseUrl();
            }
            if ($category->has_blogs) {
                $blog_page = $item->withParentsBlogUrl();
            }
        } else {
            if ($category->has_comments) {
                $comment_page = route('question.index', $category->slug) . '?s=1';
            }
            if ($category->has_forums) {
                $forum_page = route('question.index', $category->slug);
            }
            if ($category->has_ads) {
                $advertise_page = route('ads.index', $category->slug);
            }
            if ($category->has_blogs) {
                $blog_page = route('blog.index', $category->slug);
            }
        }

        $affilateService = new AffilateService();
        $affilate = $affilateService->suggestForPages($category, $item);

        $features = $category->features();
        $currentQueryParams = $request->query();

        $hot_pages = Cache::get('hot_pages');

        $compactVars = [
            'hot_pages',
            'hotQuestions',
            'blog',
            'blogs',
            'blogVideo',
            'is_follow',
            'user',
            'item',
            'category',
            'tab_title',
            'currentQueryParams'
        ];
        if (isset($features)) {
            $compactVars[] = 'features';
        }
        if (isset($affilate)) {
            $compactVars[] = 'affilate';
        }
        if (isset($forum_page)) {
            $compactVars[] = 'forum_page';
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
        return view('blog.show', compact(...$compactVars));
    }

    // private function replaceAffilateWithHtml($blog_content)
    // {
    //     preg_match_all('/@##(.*?)##@/', $blog_content, $matches);

    //     foreach ($matches[1] as $affilate_id) {
    //         $affilate = Affilate::with('video')->find($affilate_id);

    //         if (isset($affilate)) {
    //             $html = '<div class="col-12 text-right py-2 my-4">';
    //             if (isset($affilate->video_path)) {
    //                 $video_embed_route = route('video.embedb.show', ['category_slug' => $affilate->video->category->slug, 'video_slug' => $affilate->video->slug, 'random_id' => $affilate->video->random_id]);
    //                 $html .= '<iframe class="shadow-sm p-0 m-0 mt-3 radius-10"';
    //                 $html .= 'src="' . $video_embed_route . '"';
    //                 $html .= 'style="border:none;" width="100%" height="292px" allowfullscreen></iframe>';
    //             } else {
    //                 $html .= '<img id="affilb-img" alt="' . $affilate->title . '" title="' . $affilate->title . '" src="' . $affilate->image() . '">';
    //             }
    //             $html .= '<h2 id="affilb-title">' . $affilate->title . '</h2>';
    //             $html .= '<span id="affilb-body">' . $affilate->body . '</span>';
    //             if (isset($affilate->link)) {
    //                 $html .= '<a id="affilb-link" rel="nofollow" target="_blank" class="text-decoration-none d-inline-block" href="' . $affilate->link . '">';
    //                 $html .= '<span class="font-600">مشاهده و خرید محصول</span>';
    //                 $html .= '<img class="lazy-load" data-src="https://dl.becharkh.com/user_files/files/other/images/next-light-w.png" alt="shop">';
    //                 $html .= '</a>';
    //             }
    //             $html .= '</div>';
    //             $blog_content = str_replace('@##' . $affilate_id . '##@', $html, $blog_content);
    //         } else {
    //             $blog_content = str_replace('@##' . $affilate_id . '##@', '', $blog_content);
    //         }
    //     }
    //     return $blog_content;
    // }

    private function getHotQuestions($category = null, $item_id = null)
    {
        $hotRelated = collect();
        if (isset($category)) {
            if (isset($item_id)) {
                $hotRelatedByItem = MongoQuestion::orderBy('created_at', 'desc')->where('category_id', $category->id)->where('items', $item_id)->take(5)->get();
                $hotRelated = $hotRelated->merge($hotRelatedByItem);
            }
            if (count($hotRelated) < 5) {
                $hotRelatedByCategory = MongoQuestion::orderBy('created_at', 'desc')->where('category_id', $category->id)->take(5)->get();
                $hotRelated = $hotRelated->merge($hotRelatedByCategory)->unique();
            }
            if (count($hotRelated) < 5) {
                $hotRelatedByCreateAt = MongoQuestion::orderBy('created_at', 'desc')->take(5)->get();
                $hotRelated = $hotRelated->merge($hotRelatedByCreateAt)->unique();
            }
        } else {
            $hotRelatedByCreateAt = MongoQuestion::orderBy('created_at', 'desc')->take(5)->get();
            $hotRelated = $hotRelated->merge($hotRelatedByCreateAt)->unique();
        }

        return $hotRelated;
    }

    private function getHotVideos($category = null, $item = null)
    {
        if (isset($category)) {
            if (isset($item)) {
                $hotRelated = MongoVideo::where('items', $item->id)->take(5)->get();
            } else {
                $hotRelated = MongoVideo::where('category_id', $category->id)->take(3)->get();
            }
        } else {
            $hotRelated = MongoVideo::random(3);
        }
        $hotads = MongoVideo::random(10, ['advertises' => 'notnull']);
        $hotOther = MongoVideo::random(20);

        $merged = $hotRelated->merge($hotads)->merge($hotOther)->unique('id')->take(25);
        return $merged;
    }

    public function all()
    {
        $blogs = MongoBlog::with('category')->orderBy('created_at', 'desc')->get();
        return view('blog.index', compact('blogs'));
    }


    public function EditorUploadImage(Request $request)
    {
        $file = $request->file('upload');
        $base_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $ext = $file->getClientOriginalExtension();
        $file_name = $base_name . '_' . time() . '.' . $ext;
        Image::make($file)
            ->save("files/BlogEditor/" . $file_name);
        $function = $request->CKEditorFuncNum;
        $url = asset('files/BlogEditor' . $file_name);

        return response("<script>window.parent.CKEDITOR.tools.callFunction({$function}, '{$url}', 'فایل به درستی آپلود شد')</script>");
    }

    public function UserNewPost($id = null)
    {
        $categories = MongoCategory::where('status', 1)->get();
        $categories = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'title' => $category->title,
                'slug' => $category->slug,
                'p_id' => $category->parent_id
            ];
        });

        if ($id) {
            $blog = MongoBlog::find($id);
            return view('blog.user-new-post', compact('categories', 'blog'));
        } else {
            return view('blog.user-new-post', compact('categories'));
        }
    }

    public function UserEditPost($id)
    {
        $blog = MongoBlog::find($id);
        $editor_service = new CommentEditorService();
        $editor_service->changeTempEditorLazyImgBlog($blog);
        // $user = auth('user')->user();
        // if ($user->id != $blog->user_id) {
        //     return back()->with('success', 'دسترسی ندارید');
        // }
        if ($blog->status == 2) {
            return redirect()->route('user.new.post', $id);
        }
        $category = MongoCategory::find($blog->category_id);
        $cfeatures = $category->features()->where('is_in_filter_rtable', 1);
        $blogFeatueItems = $blog->getItems();
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
        $blogFeatueItems = $blogFeatueItems->map(function ($fi) {
            return [
                'f_id' => $fi->feature_id,
                'i_id' => $fi->id,
            ];
        })->values();
        return view('blog.user-edit-post', compact('blog', 'category', 'cfeatures', 'citems', 'blogFeatueItems'));
    }

    public function UserdestroyPost(Request $request)
    {
        $blog = MongoBlog::find($request->post_id);
        if (isset($blog)) {
            $blog->delete();
        }
        return back();
    }

    public function UserStorePostTemprory(Request $request)
    {
        if ($request->post_id != 0) {
            $blog = MongoBlog::find($request->post_id);
        } else {
            $blog = new MongoBlog();
            $user = auth('user')->user();
            $blog->user_id = $user->id;
            $blog->status = 2;
        }

        $blog->title = $request->title;
        $blog->short_description = $request->short_description;
        $blog->content = $request->editor;

        if ($request->post_id != 0) {
            $blog->update();
        } else {
            $blog->save();
        }
        return response()->json(['status' => 1, 'post_id' => $blog->id], 200);
    }

    public function UserStorePost(Request $request)
    {
        $category = MongoCategory::find($request->category_id);

        if (!isset($category)) {
            return back()->with('success', 'دسته بندی را انتخاب کنید');
        }

        if (isset($request->post_id)) {
            $blog = MongoBlog::find($request->post_id);
        } else {
            $blog = new MongoBlog();
        }
        $user = auth('user')->user();

        $blog->title = $request->title;
        $blog->short_description = $request->short_description;
        $blog->user_id = $user->id;
        $blog->category_id = $category->id;
        $blog->random_id = str_random(10);
        $slug = preg_replace('~[^\pL\d]+~u', '-', $request->title);
        $blog->slug = $slug;
        $blog->google_index = 0;

        $editor_service = new CommentEditorService();
        $editor_images = $editor_service->store('create_blog', $request->body, $blog);

        if ($request->hasFile('image')) {
            $cover = $request->file('image');
            $basefilename = $category->slug . rand(1000, 9999) . time();
            $path = 'blog/images/' . $category->slug . '/' . $user->username . '/';

            //main image
            $filename = $basefilename . '.webp';
            $blog->image = $path . $filename;
            $this->uploadAndResizeImage($cover, $path, $filename, 90, 0);

            //thum image
            $filename2 = $basefilename . '2.webp';
            $this->uploadAndResizeImage($cover, $path, $filename2, 90, 1);
        }

        $addItemService = new AdditemsService();
        $add_item_result = $addItemService->addForCreate($category, $request);
        $items = $add_item_result['items'];
        $items_title = $add_item_result['items_title'];
        $changeStatus = $add_item_result['changeStatus'];

        if (count($items) > 0) {
            $blog->items = $items;
            dispatch(new ChangeItemPageCount($items, 'blog', 1))->onQueue('becharkhsite')->delay(now()->addMinutes(5));
        }
        if (count($items_title) > 0) {
            $blog->items_title = $items_title;
        }

        if ($changeStatus) {
            $blog->status = 0;
        } else {
            $blog->status = 1;
        }

        if (isset($request->post_id)) {
            $blog->update();
        } else {
            $blog->save();
        }

        $editor_service->updateImageCommentId($editor_images, $blog->id);

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => $user->username . ' مقاله ' . $blog->title . ' را منتشر کرد',
                'route' => route('blog.show', ['category_slug' => $category->slug, 'slug' => $blog->slug, 'random_id' => $blog->random_id])
            ]));
        }

        return redirect()->route('user.dashboard.edit', 'post')->with('success', 'مقاله با موفقیت منتشر شد');
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

    public function UserUpdatePost(Request $request, $id)
    {
        $blog = MongoBlog::find($id);
        $user = $blog->user;
        // if (!isset($user) || $user->id != $blog->user_id) {
        //     return back()->with('success', 'دسترسی ندارید');
        // }

        $category = MongoCategory::find($blog->category_id);

        $blog->short_description = $request->short_description;
        $blog->title = $request->title;

        $editor_service = new CommentEditorService();
        $editor_service->update('edit_blog', $request->body, $blog);

        if ($request->hasFile('image')) {
            $cover = $request->file('image');
            $path = 'blog/images/' . $category->slug . '/' . $user->username . '/';

            if ($blog->getImage()) {
                $image_name = explode($path, $blog->getImage())[1];
                $basefilename = explode('.webp', $image_name)[0];
            } else {
                $basefilename = $category->slug . rand(1000, 9999) . time();
            }

            //main image
            $filename = $basefilename . '.webp';
            $blog->image = $path . $filename;
            $this->uploadAndResizeImage($cover, $path, $filename, 90, 0);

            //thum image
            $filename2 = $basefilename . '2.webp';
            $this->uploadAndResizeImage($cover, $path, $filename2, 90, 1);
        }

        $addItemService = new AdditemsService();
        $last_items = $blog->items ?? [];
        $add_item_result = $addItemService->addForUpdate($category, $last_items, $request);
        $items = $add_item_result['items'];
        $items_title = $add_item_result['items_title'];
        $changeStatus = $add_item_result['changeStatus'];

        if (count($items) > 0) {
            $blog->items = $items;
        }
        if (count($items_title) > 0) {
            $blog->items_title = $items_title;
        }

        if ($changeStatus) {
            $blog->status = 0;
        } else {
            $blog->status = 1;
        }

        $blog->update();

        // $admins = Admin::all();
        // foreach ($admins as $admin) {
        //     $admin->notify(new SiteEvent([
        //         'action' => $user->name . ' مقاله ' . $blog->title . ' را ویرایش کرد',
        //         'route' => route('blog.show', ['category_slug' => $category->slug, 'slug' => $blog->slug, 'random_id' => $blog->random_id])
        //     ]));
        // }

        return redirect()->route('user.dashboard.edit', 'post')->with('success', 'تغییرات با موفقیت ثبت شد');
    }

    private function deleteBlogChildrenFeatureValue($blog, $category, $feature, $request)
    {
        $childfeatures = $category->features()->where('parent_id', $feature->id);
        if (count($childfeatures) == 0) {
            return;
        }
        $qFVs = $blog->featureValues;
        foreach ($childfeatures as $chf) {
            if ($request[$chf->slug] == null) {
                $bfv = $qFVs->where('feature_id', $chf->id)->first();
                if (isset($bfv)) {
                    $bfv->delete();
                    $this->deleteBlogChildrenFeatureValue($blog, $category, $chf, $request);
                }
            }
        }
    }

    public function updateCkeditor(Request $request, $id = null)
    {
        if ($request->hasFile('upload')) {
            $disk = Storage::disk('ftp');
            $user = auth('user')->user();
            $file = $request->file('upload');

            if ($id) {
                $blog = MongoBlog::find($id);
                $category = $blog->category;
                $path = 'blog/images/' . $category->slug . '/' . $user->username . '/';
                $filename = $category->slug . '-' . time() . rand(10000, 99999) . '.webp';
                $url = $path . $filename;
                $resizedImage = Image::make($file)->encode('webp', 90);
                $disk->put($path . $filename, (string) $resizedImage);

                $images = $blog->images ?? [];
                $images[] = $url;

                $blog->images = $images;
                $blog->update();
            } else {
                $path = 'blog/images/' . $user->username . '/';
                $filename = time() . '.' . $user->username . '.webp';
                $url = $path . $filename;
                $resizedImage = Image::make($file)->encode('webp', 90);
                $disk->put($path . $filename, (string) $resizedImage);

                $images = $user->temp_blog_ck_images ?? [];
                $images[] = $url;

                $user->temp_blog_ck_images = $images;
                $user->update();
            }

            return response()->json(['filename' => $filename, 'uploaded' => 1, 'url' => "https://dl.becharkh.com/user_files/" . $url]);
        }
    }


    private function createShortLink($link_class, $link_id)
    {
        $random = Str::random(12);
        if ($this->isShortLinkUnique($random)) {
            $shortlink = new ShortLink();
            $shortlink->short_link = $random;
            $shortlink->link_id = $link_id;
            $shortlink->link_class = $link_class;
            $shortlink->save();
        } else {
            $this->createShortLink($link_class, $link_id);
        }
    }

    private function isShortLinkUnique($short_link)
    {
        $shortLinks = ShortLink::all();
        foreach ($shortLinks as $sh) {
            if ($sh->short_link == $short_link) {
                return false;
            }
        }
        return true;
    }

    public function destroy($id)
    {
        $blog = MongoBlog::find($id);
        $disk = Storage::disk('ftp');

        $image = $blog->getImage();
        $thumb = explode('.webp', $image)[0] . '2.webp';
        $disk->delete($image);
        $disk->delete($thumb);

        $editor_images = BlogEditorImage::where('comment_id', $blog->id)->get();
        if (!$editor_images->isEmpty()) {
            foreach ($editor_images as $ci) {
                $disk->delete($ci->path);
                $ci->delete();
            }
        }
        if (isset($blog->items) && count($blog->items) > 0) {
            dispatch(new ChangeItemPageCount($blog->items, 'blog', 0))->onQueue('becharkhsite')->delay(now()->addMinutes(5));
        }

        $blog->delete();

        return back()->with('success', 'مقاله با موفقیت حذف شد');
    }
}
