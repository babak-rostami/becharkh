<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\CategoryFeature;
use App\Models\CategoryFeatureItem;
use App\Models\MongoCategory;
use App\Models\MongoFeature;
use App\Models\MongoItem;
use App\Models\Ostan;
use App\Models\SiteCategory;
use App\Notifications\SiteEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class SiteCategoryController extends Controller
{

    public function catsItemsIndex()
    {
        $categories = MongoCategory::select(['id', 'title'])->get();
        return view('category.admin.index', compact('categories'));
    }

    public function indexAdmin($id = null)
    {
        if (isset($id)) {

            $selectedCat = MongoCategory::find($id);
            $allCats = MongoCategory::where('_id', '!=', $selectedCat->id)->get();
            $categories = $selectedCat->children;
            $children_cat_ids = $selectedCat->allChildren()->pluck('id')->toArray();
            $parent_cats = $allCats->whereNotIn('_id', $children_cat_ids)->where('_id', '!=', $selectedCat->id);
            $compactVars = [
                'categories',
                'allCats',
                'parent_cats',
                'selectedCat'
            ];
            if (isset($selectedCat->related_cats)) {
                $categories = $selectedCat->related_cats;
                $categoryIds = implode(',', $categories);
                $categorySelects = MongoCategory::whereIn('_id', $categories)->select('title')->get();
                $compactVars[] = 'categoryIds';
                $compactVars[] = 'categorySelects';
            }
            if (isset($selectedCat->has_items)) {
                $items = $selectedCat->has_items;
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
            return view('admin.siteCategory.index', compact(...$compactVars));
        } else {
            $categories = MongoCategory::where('parent_id', null)->get();
            $allCats = $categories;
            return view('admin.siteCategory.index', compact('categories', 'allCats'));
        }
    }

    public function storeAdmin(Request $request)
    {
        $findslug = MongoCategory::where('slug', $request->slug)->first();
        if (isset($findslug)) {
            return back()->with('success', 'اسلاگ قبلا استفاده شده است');
        }

        $category = new MongoCategory();
        $category->title = $request->title;
        $category->title_en = $request->title_en;
        $category->slug = $request->slug;

        $category->parent_id = $request->parent_id;
        $category->status = (int)$request->status;
        $category->has_ads = (int)$request->has_ads;
        $category->has_forums = (int)$request->has_forums;
        $category->has_comments = (int)$request->has_comments;
        $category->has_blogs = (int)$request->has_blogs;
        $category->is_cat_in_title = (int)$request->is_cat_in_title;


        if ($request->cost_description) {
            $category->cost_description = $request->cost_description;
        }

        if ($request->title_in_rtable) {
            $category->title_in_rtable = $request->title_in_rtable;
        }
        if ($request->desc_in_rtable) {
            $category->desc_in_rtable = $request->desc_in_rtable;
        }
        if ($request->desc_in_rtable_editor) {
            $category->desc_in_rtable_editor = $request->desc_in_rtable_editor;
        }

        if ($request->title_in_comment) {
            $category->title_in_comment = $request->title_in_comment;
        }
        if ($request->desc_in_comment) {
            $category->desc_in_comment = $request->desc_in_comment;
        }
        if ($request->desc_in_comment_editor) {
            $category->desc_in_comment_editor = $request->desc_in_comment_editor;
        }

        if ($request->title_in_ads) {
            $category->title_in_ads = $request->title_in_ads;
        }
        if ($request->desc_in_ads) {
            $category->desc_in_ads = $request->desc_in_ads;
        }
        if ($request->desc_in_ads_editor) {
            $category->desc_in_ads_editor = $request->desc_in_ads_editor;
        }


        if ($request->hasFile('image')) {
            $disk = Storage::disk('ftp');
            $file = $request->file('image');
            $path = 'catImage/images/';

            $baseFilename = $category->slug . time();

            //org image
            $filename = $baseFilename . '.webp';
            $category->image = $path . $filename;
            $resizedImage = Image::make($file)->encode('webp', 90);
            $disk->put($path . $filename, (string) $resizedImage);

            //thumb image
            $filename2 = $baseFilename . '2.webp';
            $resizedImage2 = Image::make($file)->resize(255, null, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('webp', 90);
            $disk->put($path . $filename2, (string) $resizedImage2);
        }

        $category->save();

        Cache::forget('categories');

        return back()->with('success', 'دسته بندی با موفقیت ایجاد شد');
    }

    public function updateCategoryAdmin(Request $request, $id)
    {
        $category = MongoCategory::find($id);

        if ($request->slug != $category->slug) {
            $findslug = MongoCategory::where('slug', $request->slug)->first();
            if (isset($findslug)) {
                return back()->with('success', 'اسلاگ قبلا استفاده شده است');
            }
        }

        $category->title = $request->title;
        $category->title_en = $request->title_en;
        $category->slug = $request->slug;
        $category->parent_id = $request->parent_id;
        $category->status = (int)$request->status;
        $category->is_active = (int)$request->is_active;
        $category->has_ads = (int)$request->has_ads;
        $category->has_forums = (int)$request->has_forums;
        $category->has_comments = (int)$request->has_comments;
        $category->has_blogs = (int)$request->has_blogs;
        $category->is_cat_in_title = (int)$request->is_cat_in_title;

        if ($request->cost_description) {
            $category->cost_description = $request->cost_description;
        }

        if ($request->title_in_rtable) {
            $category->title_in_rtable = $request->title_in_rtable;
        }
        if ($request->desc_in_rtable) {
            $category->desc_in_rtable = $request->desc_in_rtable;
        }
        if ($request->desc_in_rtable_editor) {
            $category->desc_in_rtable_editor = $request->desc_in_rtable_editor;
        }

        if ($request->title_in_comment) {
            $category->title_in_comment = $request->title_in_comment;
        }
        if ($request->desc_in_comment) {
            $category->desc_in_comment = $request->desc_in_comment;
        }
        if ($request->desc_in_comment_editor) {
            $category->desc_in_comment_editor = $request->desc_in_comment_editor;
        }

        if ($request->title_in_ads) {
            $category->title_in_ads = $request->title_in_ads;
        }
        if ($request->title_in_ads_noi) {
            $category->title_in_ads_noi = $request->title_in_ads_noi;
        }
        if ($request->desc_in_ads) {
            $category->desc_in_ads = $request->desc_in_ads;
        }
        if ($request->desc_in_ads_noi) {
            $category->desc_in_ads_noi = $request->desc_in_ads_noi;
        }
        if ($request->desc_in_ads_editor) {
            $category->desc_in_ads_editor = $request->desc_in_ads_editor;
        }

        if ($request->hasFile('image')) {
            $disk = Storage::disk('ftp');
            $file = $request->file('image');

            $path = 'catImage/images/';

            if ($category->getImage() !== null) {
                $image = $category->getImage();
                $imagename = explode($path, $image)[1];
                $baseFilename = explode('.webp', $imagename)[0];
            } else {
                $baseFilename = $category->slug . time();
            }

            //org image
            $filename = $baseFilename . '.webp';
            $resizedImage = Image::make($file)->encode('webp', 90);
            $disk->put($path . $filename, (string) $resizedImage);

            //thumb image
            $filename2 = $baseFilename . '2.webp';
            $resizedImage2 = Image::make($file)->resize(255, null, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('webp', 90);
            $disk->put($path . $filename2, (string) $resizedImage2);
        }

        $categories = array_filter(explode(',', $request->categories));
        $items = array_filter(explode(',', $request->items));
        $unset_cats = 0;
        $unset_items = 0;
        if (count($categories) > 0) {
            $category->related_cats = $categories;
        } else {
            $unset_cats = 1;
        }
        if (count($items) > 0) {
            $category->has_items = $items;
        } else {
            $unset_items = 1;
        }

        $category->update();

        if ($unset_cats) {
            $category->unset('related_cats');
        }
        if ($unset_items) {
            $category->unset('has_items');
        }

        $this->updateItemsHRCats($items, $category);

        return back()->with('success', 'تغییرات ثبت شد');
    }

    private function updateItemsHRCats($items, $category)
    {
        $allItemIds = collect($items)->merge(
            MongoItem::where('has_rcats', $category->_id)->pluck('_id')->toArray()
        )->unique();
        foreach ($allItemIds as $itemId) {
            $item = MongoItem::find($itemId);
            if (!$item) {
                continue;
            }
            $hasRcats = $item->has_rcats ?? [];
            // چک کنیم که این آیتم جزو آیتم‌های انتخاب‌شده هست یا خیر
            if (in_array($itemId, $items)) {
                // باید این دسته‌بندی را اضافه کنیم اگر نبود
                if (!in_array($category->_id, $hasRcats)) {
                    $hasRcats[] = $category->_id;
                }
            } else {
                // این آیتم دیگر نباید این دسته‌بندی را داشته باشد
                $hasRcats = array_values(array_diff($hasRcats, [$category->_id]));
            }

            if (empty($hasRcats)) {
                $item->unset('has_rcats');
            } else {
                $item->has_rcats = $hasRcats;
                $item->save();
            }
        }
    }

    public function destroy($id)
    {
        $category = SiteCategory::find($id);
        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => 'ادمین ' . auth('admin')->user()->username . ' دسته بندی ' . $category->title . ' را حذف کرد',
                'route' => route('site.category.admin'),
            ]));
        }
        $category->delete();

        Cache::forget('categories');
        Cache::forget('parentCategories');
        $categories = Cache::rememberForever('categories', function () {
            return SiteCategory::where('status', 1)->get();
        });
        $sliderCategories = Cache::rememberForever('parentCategories', function () {
            return SiteCategory::where('status', 1)->doesntHave('children')->get();
        });

        return back()->with('success', 'دسته بندی با موفقیت حذف شد');
    }


    public function getChildren($category_id, $cat_edit_id = null)
    {
        $categories = Cache::rememberForever('categories', function () {
            return SiteCategory::where('status', 1)->get();
        });
        $children = $categories->where('parent_id', $category_id);

        if (isset($cat_edit_id)) {
            foreach ($children as $child) {
                $child_id = "'" . $child->id . "'";
                $child_title = "'" . $child->title . "'";
                $edit_id = "'" . $cat_edit_id . "'";
                echo '<div class="row my-2 p-2 ml-2" style="background-color: #f1f1f1">
        <div style="cursor: pointer" onclick="categoryDrop(' . $child_id . ',' . $edit_id . ')"
        id="category-drop-' . $child->id . '-' . $cat_edit_id . '" class="col-9">
        ' . $child->title . '
        </div>
        <div class="col-3"
        onclick="selectCategory(' . $child_id . ',' . $child_title . ',' . $edit_id . ')"
        style="background-color: #2c2c2c ; cursor: pointer">
        انتخاب
        </div>
        </div>
        <div class="ml-2" id="cat-children-' . $child->id . '-' . $cat_edit_id . '"></div>';
            }
        } else {
            foreach ($children as $child) {
                $child_id = "'" . $child->id . "'";
                $child_title = "'" . $child->title . "'";
                echo '<div class="row my-2 p-2 ml-2" style="background-color: #f1f1f1">
        <div style="cursor: pointer" onclick="categoryDrop(' . $child_id . ')"
        id="category-drop-' . $child->id . '" class="col-9">
        ' . $child->title . '
        </div>
        <div class="col-3"
        onclick="selectCategory(' . $child_id . ',' . $child_title . ')"
        style="background-color: #2c2c2c ; cursor: pointer">
        انتخاب
        </div>
        </div>
        <div class="ml-2" id="cat-children-' . $child->id . '"></div>';
            }
        }
    }

    public function getCategoryFeaturesItems(Request $request)
    {
        if (!isset($request->category_id)) {
            return response()->json([
                'message' => 'دسته بندی پیدا نشد!'
            ], 404);
        }
        $category = MongoCategory::find($request->category_id);
        $cfeatures = $category->features()->where('is_in_filter_rtable', 1);

        $item_cat_ids = [];
        foreach ($cfeatures as $f) {
            $item_cat_ids[] = $f->category_id;
        }
        $cat_ids = array_merge($item_cat_ids, [$category->id]);
        $cat_ids = array_unique($cat_ids);
        $citems = MongoItem::whereIn('category_id', $cat_ids)->where('status', 1)->get();

        $cfeatures = $cfeatures->map(function ($f) {
            return [
                'id' => $f->id,
                'p_id' => $f->parent_id,
                'title' => $f->title,
                'slug' => $f->slug,
                'i_count' => $f->select_items_count ?? 1
            ];
        })->values();
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
        return response()->json([
            'cfeatures' => $cfeatures,
            'citems' => $citems,
        ], 200);
    }

    public function getCategoryFeaturesItemsForAd(Request $request)
    {
        if (!isset($request->category_id)) {
            return response()->json([
                'message' => 'دسته بندی پیدا نشد!'
            ], 404);
        }
        // $cfeatures = get all citems feature_id and unique
        // $cfeatures = $category->features()->where('is_in_filter_ad', 1);
        // $item_cat_ids = [];
        // foreach ($cfeatures as $f) {
        //     $item_cat_ids[] = $f->category_id;
        // }
        // $cat_ids = array_merge($item_cat_ids, [$category->id]);
        // $cat_ids = array_unique($cat_ids);
        $category = MongoCategory::find($request->category_id);
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }
        $citems = MongoItem::where('category_id', $category->id)->get();
        if ($citems->isEmpty()) {
            return response()->json(['cfeatures' => [], 'citems' => []], 200);
        }
        $cfeatures = MongoFeature::whereIn('_id', $citems->pluck('feature_id')->unique()->values())->get();
        if ($cfeatures->isEmpty()) {
            $cfeatures = collect();
        }
        $cfeatures = $cfeatures->map(function ($f) {
            return [
                'id' => $f->id,
                'p_id' => $f->parent_id,
                'title' => $f->title,
                'slug' => $f->slug,
                'type' => $f->input_type,
                'require' => $f->is_important_in_ad,
            ];
        })->values();
        $citems = $citems->map(function ($i) {
            return [
                'id' => $i->id,
                'f_id' => $i->feature_id,
                'p_id' => $i->parent_id,
                'title' => $i->title,
                'e_title' => $i->title_en,
                'slug' => $i->slug,
            ];
        })->values();
        return response()->json([
            'cfeatures' => $cfeatures,
            'citems' => $citems,
        ], 200);
    }

    public function redirectIfPageNotExist($request, $category, $page)
    {
        $check_has_comments = 0;
        $check_has_ads = 0;
        $check_has_forums = 0;
        $check_has_blogs = 0;
        if ($page == 'comments') {
            if (!$category->has_comments) {
                $page_title = 'forum';
                $check_has_ads = 1;
                $check_has_forums = 1;
                $check_has_blogs = 1;
            } else {
                return;
            }
        }
        if ($page == 'forums') {
            if (!$category->has_forums) {
                $page_title = 'forum';
                $check_has_ads = 1;
                $check_has_comments = 1;
                $check_has_blogs = 1;
            } else {
                return;
            }
        }
        if ($page == 'ads') {
            if (!$category->has_ads) {
                $page_title = 'ads';
                $check_has_comments = 1;
                $check_has_forums = 1;
                $check_has_blogs = 1;
            } else {
                return;
            }
        }
        if ($page == 'blogs') {
            if (!$category->has_blogs) {
                $page_title = 'blogs';
                $check_has_comments = 1;
                $check_has_forums = 1;
                $check_has_ads = 1;
            } else {
                return;
            }
        }
        $new_url = $request->root() . $request->getRequestUri();
        if ($check_has_comments && $category->has_comments) {
            if (strpos($new_url, '?') === false) {
                $new_url = str_replace($category->slug, $category->slug . '?s=1', $new_url);
            } else {
                $new_url = str_replace($category->slug . '?', $category->slug . '?s=1&', $new_url);
            }
            $new_url = str_replace($page_title, 'forum', $new_url);
            Redirect::to($new_url, 302)->send();
        }
        $new_url = str_replace('s=1&', '', $new_url);
        $new_url = str_replace('?s=1', '', $new_url);
        if ($check_has_ads && $category->has_ads) {
            $new_url = str_replace($page_title, 'ads', $new_url);
            Redirect::to($new_url, 302)->send();
        }
        if ($check_has_forums && $category->has_forums) {
            $new_url = str_replace($page_title, 'forum', $new_url);
            Redirect::to($new_url, 302)->send();
        }
        if ($check_has_blogs && $category->has_blogs) {
            $new_url = str_replace($page_title, 'blogs', $new_url);
            Redirect::to($new_url, 302)->send();
        }
    }
}
