<?php

namespace App\Http\Controllers;

use App\Models\Affilate;
use App\Models\AffilateEditorImage;
use App\Models\AffilatePublicLink;
use App\Models\MongoCategory;
use App\Models\MongoFollowItem;
use App\Models\MongoItem;
use App\Models\MongoQuestion;
use App\Models\MongoVideo;
use App\Services\Affilate\AffilateService;
use App\Services\Comment\CommentEditorService;
use App\Services\Suggestion\SuggestionService;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class AffilateController extends Controller
{

    public function indexAdmin()
    {
        $affilates = Affilate::orderBy('created_at', 'desc')->paginate(100);
        return view('affilate.index-admin', compact('affilates'));
    }

    public function create()
    {
        $plinks = AffilatePublicLink::all();
        return view('affilate.create', compact('plinks'));
    }

    function createUniqueSlug($title)
    {
        $slug = preg_replace('~[^\pL\d]+~u', '-', str_limit($title, '45', '-'));
        $originalSlug = $slug;
        $counter = 1;
        while (Affilate::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        return $slug;
    }

    private function modifyAffiliateBody($affiliate, $sug = 0)
    {
        $body = $affiliate->body;
        if ($sug == 1) {
            return preg_replace_callback(
                '/<h2>(.*?)<\/h2>/i',
                function ($matches) use ($affiliate) {
                    $a_id = "affilb-route-" . $affiliate->id;
                    if ($affiliate->google_index) {
                        return '<a target="_blank" class="mb-2" id="' . $a_id . '" href="' . route('product.show', $affiliate->slug) . '">' . $matches[1] . '</a>';
                    } else {
                        return '<h2 class="cur-p mb-2" id="' . $a_id . '" onclick="jslink(\'' . route('product.show', $affiliate->slug) . '\', 1)">' . $matches[1] . '</h2>';
                    }
                },
                $body,
                1
            );
        } else {
            return preg_replace_callback(
                '/<h2>(.*?)<\/h2>/i',
                function ($matches) use ($affiliate) {
                    $a_id = "affilb-route-" . $affiliate->id;
                    return '<h2 class="cur-p mb-2" id="' . $a_id . '">' . $matches[1] . '</h2>';
                },
                $body,
                1
            );
        }
    }

    public function show($slug, SuggestionService $suggestionService)
    {
        $product = Affilate::where('slug', $slug)->first();
        if (!isset($product)) {
            abort(404);
        }
        if (!isset($_COOKIE['page_seen'])) {
            if (isset($product->seen_count)) {
                $product->seen_count += 1;
            } else {
                $product->seen_count = 1;
            }
            $product->update();
        }
        $product->body  = $this->modifyAffiliateBody($product, 0);

        $comments = $product->comments;

        $page = 'show_product';
        $item = null;
        $category = null;
        $user = null;
        if (auth('user')->check()) {
            $user = auth('user')->user();
        }

        if (isset($product->items[0])) {
            $item = MongoItem::find($product->items[0]);
            $category = $item->category;
            $features = $category->features();
            $currentQueryParams = [];
            $suggests = $suggestionService->suggest($category, $item);
            if (isset($suggests['items'])) {
                $suggetItems = $suggests['items'];
            } else {
                $suggestCats = $suggests['cats'];
            }
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
            if ($category->has_ads) {
                $advertise_page = $item->withParentsAdvertiseUrl();
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
            if (isset($product->categories[0])) {
                $category = MongoCategory::find($product->categories[0]);
                $features = $category->features();
                $currentQueryParams = [];
                $suggests = $suggestionService->suggest($category, null);
                if (isset($suggests['items'])) {
                    $suggetItems = $suggests['items'];
                } else {
                    $suggestCats = $suggests['cats'];
                }
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
        }

        $affilateService = new AffilateService();
        $affilates = $affilateService->suggestForAds($category, $item);
        $affilates = $affilates->reject(function ($affilate) use ($product) {
            return $affilate->id === $product->id;
        });

        $affilates = $affilates->values();

        $compactVars = [
            'user',
            'product',
            'page',
            'comments',
        ];
        if ($category) {
            $compactVars[] = 'category';
        }
        if ($item) {
            $compactVars[] = 'item';
        }
        if (isset($affilates)) {
            $compactVars[] = 'affilates';
        }
        if (isset($features)) {
            $compactVars[] = 'features';
        }
        if (isset($currentQueryParams)) {
            $compactVars[] = 'currentQueryParams';
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

        return view('affilate.show', compact(...$compactVars));
    }

    public function store(Request $request)
    {
        $affilate = new Affilate();
        $affilate->title = $request->title;
        $affilate->slug = $this->createUniqueSlug($request->title);

        if (isset($request->link)) {
            $affilate->link = $request->link;
        } else {
            $affilate->product_link = $request->product_link;
            $affilate->public_link = $request->public_link;
        }

        $affilate->page_link = $request->page_link;
        $affilate->just_this_page = (int)$request->just_this_page;
        $affilate->google_index = (int)$request->google_index;
        $affilate->status = (int)$request->status;

        $editor_service = new CommentEditorService();
        $editor_images = $editor_service->store('create_affilate', $request->body, $affilate);
        if ($request->body2) {
            $editor_images_2 = $editor_service->store('create_affilate_body_2', $request->body2, $affilate);
            $affilate_images = $editor_images->merge($editor_images_2)->unique();
        } else {
            $affilate_images = $editor_images;
        }

        $categories = array_filter(explode(',', $request->categories));
        $items = array_filter(explode(',', $request->items));
        $questions = array_filter(explode(',', $request->questions));

        if (count($categories) > 0) {
            $cat_ids = [];
            foreach ($categories as $category_id) {
                $cat_ids[] = $category_id;
                $category = MongoCategory::find($category_id);
                $cat_children = $category->allChildren();
                foreach ($cat_children as $child) {
                    $cat_ids[] = $child->id;
                }
            }
            $cat_ids = array_unique($cat_ids);
            $cat_ids = array_values($cat_ids);
            $affilate->categories = $cat_ids;
        }

        if (count($items) > 0) {
            $affilate->items = $items;
        }
        if (count($questions) > 0) {
            $affilate->questions = $questions;
        }
        $affilate->save();

        $this->setAffilateMainImage($affilate);

        $editor_service->updateImageCommentId($affilate_images, $affilate->id);

        return redirect()->route('affilate.index.admin')->with('success', 'با موفقیت اضافه شد');
    }


    private function setAffilateMainImage($affilate)
    {
        if (isset($affilate->video)) {
            $imageSrc = $affilate->video->image();
        } else {
            $dom = new DOMDocument();
            @$dom->loadHTML($affilate->body);
            $images = $dom->getElementsByTagName('img');
            if ($images->length > 0) {
                $imageSrc = $images->item(0)->getAttribute('data-src');
            } else {
                $imageSrc = null;
            }
        }
        $affilate->image = $imageSrc;
        $affilate->update();
    }

    public function edit(Request $request, $id)
    {
        $plinks = AffilatePublicLink::all();
        $affilate = Affilate::find($id);

        $compactVars = [
            'plinks',
            'affilate'
        ];

        $editor_service = new CommentEditorService();
        $editor_service->changeTempEditorLazyImgAffilate($affilate);

        if (isset($affilate->categories)) {
            $categories = $affilate->categories;
            $categoryIds = implode(',', $categories);
            $categorySelects = MongoCategory::whereIn('_id', $categories)->select('title')->get();
            $compactVars[] = 'categoryIds';
            $compactVars[] = 'categorySelects';
        }
        if (isset($affilate->items)) {
            $items = $affilate->items;
            $itemIds = implode(',', $items);
            $itemSelects = MongoItem::whereIn('_id', $items)
                ->select('title')
                ->get()
                ->sortBy(function ($item) use ($items) {
                    return array_search($item->_id, $items);
                });
            $compactVars[] = 'itemIds';
            $compactVars[] = 'itemSelects';
        }
        if (isset($affilate->questions)) {
            $questions = $affilate->questions;
            $questionIds = implode(',', $questions);
            $questionSelects = MongoQuestion::whereIn('_id', $questions)->select('title')->get();
            $compactVars[] = 'questionIds';
            $compactVars[] = 'questionSelects';
        }
        if (isset($affilate->video_id)) {
            $videoSelect = MongoVideo::select('title')->find($affilate->video_id);
            $videoId = $videoSelect->id;
            $compactVars[] = 'videoId';
            $compactVars[] = 'videoSelect';
        }

        return view('affilate.edit', compact(...$compactVars));
    }

    public function update(Request $request, $id)
    {
        $affilate = Affilate::find($id);
        $affilate->title = $request->title;
        $affilate->page_link = $request->page_link;
        $affilate->just_this_page = (int) $request->just_this_page;
        $affilate->google_index = (int)$request->google_index;
        $affilate->status = (int)$request->status;

        $unset_link = 0;
        $unset_plink = 0;
        if (isset($request->link)) {
            $affilate->link = $request->link;
            if (isset($affilate->product_link)) {
                $unset_plink = 1;
            }
        } else {
            $affilate->product_link = $request->product_link;
            $affilate->public_link = $request->public_link;
            if (isset($affilate->link)) {
                $unset_link = 1;
            }
        }

        $editor_service = new CommentEditorService();
        $body_images = $editor_service->update('edit_affilate', $request->body, $affilate);
        if ($request->body2) {
            $body2_images = $editor_service->update('edit_affilate_body_2', $request->body2, $affilate);
            $affilate_images = $body_images->merge($body2_images)->unique();
        } else {
            $affilate_images = $body_images;
        }
        $editor_service->updateImageAffilate($affilate_images, $affilate);

        $categories = array_filter(explode(',', $request->categories));
        $items = array_filter(explode(',', $request->items));
        $questions = array_filter(explode(',', $request->questions));
        $unset_cats = 0;
        $unset_items = 0;
        $unset_ques = 0;
        $unset_vid = 0;
        if (count($categories) > 0) {
            $cat_ids = [];
            foreach ($categories as $category_id) {
                $cat_ids[] = $category_id;
                $category = MongoCategory::find($category_id);
                $cat_children = $category->allChildren();
                foreach ($cat_children as $child) {
                    $cat_ids[] = $child->id;
                }
            }
            $cat_ids = array_unique($cat_ids);
            $cat_ids = array_values($cat_ids);
            $affilate->categories = $cat_ids;
        } else {
            $unset_cats = 1;
        }
        if (count($items) > 0) {
            $affilate->items = $items;
        } else {
            $unset_items = 1;
        }
        if (count($questions) > 0) {
            $affilate->questions = $questions;
        } else {
            $unset_ques = 1;
        }
        if (isset($request->video)) {
            if ($affilate->video_id != $request->video) {
                $video = MongoVideo::find($request->video);
                $affilate->video_id = $video->id;
                $affilate->video_path = $video->video_path;
                $video->affilate_id = $affilate->id;
                $video->update();
            }
        } else {
            $unset_vid = 1;
        }
        $affilate->update();

        $this->setAffilateMainImage($affilate);

        if ($unset_cats) {
            $affilate->unset('categories');
        }
        if ($unset_items) {
            $affilate->unset('items');
        }
        if ($unset_ques) {
            $affilate->unset('questions');
        }
        if ($unset_vid) {
            $affilate->unset('video_id');
            $affilate->unset('video_path');
        }
        if ($unset_plink) {
            $affilate->unset('product_link');
            $affilate->unset('public_link');
        }
        if ($unset_link) {
            $affilate->unset('link');
        }
        return redirect()->route('affilate.index.admin')->with('success', 'با موفقیت ویرایش شد');
    }

    public function destroy(Request $request, $id)
    {
        $affilate = Affilate::find($id);
        if (isset($affilate->video_id)) {
            $video = MongoVideo::find($affilate->video_id);
            $video->unset('affilate_id');
        }
        $disk = Storage::disk('ftp');
        $disk->delete($affilate->getImage());
        $comment_images = AffilateEditorImage::where('comment_id', $affilate->id)->get();
        if (!$comment_images->isEmpty()) {
            foreach ($comment_images as $ci) {
                $disk->delete($ci->path);
                $ci->delete();
            }
        }
        $affilate->delete();
        return back()->with('success', 'با موفقیت حذف شد');
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

    public function slink($affilate_id)
    {
        $affilate = Affilate::find($affilate_id);
        if (isset($affilate->link)) {
            $link = $affilate->link;
        } else if (isset($affilate->public_link)) {
            $base64EncodedUrl = base64_encode($affilate->product_link);
            $link = str_replace('{YOUR_BASE64_ENCODED_URL}', $base64EncodedUrl, $affilate->public_link);
        }
        return redirect()->away($link);
    }

    public function plinks()
    {
        $plinks = AffilatePublicLink::all();
        return view('affilate.plinks', compact('plinks'));
    }

    public function plinkStore(Request $request)
    {
        $plink = AffilatePublicLink::where('link', $request->link)->first();
        if (isset($plink)) {
            return redirect()->back()->with('success', 'لینک قبلا ایجاد شده است');
        } else {
            $plink = new AffilatePublicLink();
            $plink->title = $request->title;
            $plink->link = $request->link;
            $plink->save();
            return redirect()->back()->with('success', 'لینک با موفقیت ایجاد شد');
        }
    }

    public function plinkUpdate(Request $request, $id)
    {
        $plink = AffilatePublicLink::find($id);
        $plink->title = $request->title;
        $plink->link = $request->link;
        $plink->update();
        return redirect()->back()->with('success', 'لینک با موفقیت ایجاد شد');
    }

    public function plinkDelete(Request $request)
    {
        $plink = AffilatePublicLink::find($request->plink_id);
        $plink->delete();
        return back()->with('success', 'با موفقیت حذف شد');
    }
}
