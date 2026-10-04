<?php

namespace App\Http\Controllers;

use App\Models\MongoCategory;
use App\Models\MongoFeature;
use App\Repositories\Item\Mongodb\ItemRepository;
use Illuminate\Http\Request;

class CategoryFeatureController extends Controller
{

    public function categoryFeaturesAdmin($cat_id, $feature_id = null)
    {
        if ($cat_id) {
            $category = MongoCategory::find($cat_id);
            if (isset($feature_id)) {
                $feature = MongoFeature::find($feature_id);
                $allFeatures = $category->features()->where('parent_id', null);
                $features = $feature->children;
            } else {
                $features = $category->features()->where('parent_id', null);
                $allFeatures = $features;
            }
            return view('admin.categoryFeature.index', compact('features', 'allFeatures', 'category'));
        } else {
            $allFeatures = MongoFeature::orderBy('created_at', 'desc')->get();
            $features = $allFeatures->where('parent_id', null);
            return view('admin.categoryFeature.index', compact('features', 'allFeatures'));
        }
    }

    public function create()
    {
        $features = MongoFeature::all();
        $categories = MongoCategory::orderBy('created_at', 'desc')->get();
        return view('admin.categoryFeature.create', compact('categories', 'features'));
    }

    public function storeFeatureAdmin(Request $request)
    {
        $is_slug_unique = MongoFeature::where('slug', $request->slug)->first();
        if ($is_slug_unique) {
            return redirect()->back()
                ->withErrors(['slug' => 'اسلاگ قبلا استفاده شده'])
                ->withInput();
        }
        if (!$request->parent_id && !isset($request->categories)) {
            return redirect()->back()
                ->withErrors(['categories' => 'دسته بندی را انتخاب کنید'])
                ->withInput();
        }

        $feature = new MongoFeature();
        $feature->title = $request->title;
        $feature->title_en = $request->title_en;
        $feature->slug = $request->slug;
        $feature->status = (int)$request->status;
        $feature->be_indexed = (int)$request->be_indexed;
        $feature->is_in_filter_rtable = (int)$request->is_in_filter_rtable;
        $feature->is_in_filter_ad = (int)$request->is_in_filter_ad;
        $feature->has_follow = (int)$request->has_follow;
        $feature->has_items = (int)$request->has_items;
        $feature->is_important_in_ad = (int)$request->is_important_in_ad;
        $feature->input_type = (int)$request->input_type;
        $feature->is_feature_in_title = (int)$request->is_feature_in_title;
        $feature->item_is_in_title = (int)$request->item_is_in_title;
        $feature->item_is_in_title_if_not_parent = (int)$request->item_is_in_title_if_not_parent;
        $feature->select_items_count = $request->select_items_count;
        if ($request->parent_id) {
            $feature->parent_id = $request->parent_id;
            $parent_feature = MongoFeature::find($request->parent_id);
            $cat_ids = $parent_feature->categories->pluck('id')->toArray();
        } else {
            $cat_ids = explode(',', $request->categories);
        }
        $feature->save();

        foreach ($cat_ids as $cat_id) {
            $category = MongoCategory::find($cat_id);
            $fids =  $category->feature_ids ?? [];
            $fids[] = $feature->id;
            $category->feature_ids = $fids;
            $category->update();
        }

        $this->assignLevels();

        return back()->with('success', 'ویژگی با موفقیت ایجاد شد');
    }

    public function edit($feature_id)
    {
        $feature = MongoFeature::find($feature_id);
        $all_children_id = $feature->allChildren()->pluck('id')->toArray();
        $features = MongoFeature::whereNotIn('_id', $all_children_id)->where('_id', '!=', $feature_id)->get();
        $categories = MongoCategory::orderBy('created_at', 'desc')->get();
        return view('admin.categoryFeature.edit', compact('feature', 'categories', 'features'));
    }

    public function updateFeatureAdmin(Request $request, $id)
    {
        if (!$request->parent_id && !isset($request->categories)) {
            return redirect()->back()
                ->withErrors(['categories' => 'دسته بندی را انتخاب کنید'])
                ->withInput();
        }
        $feature = MongoFeature::find($id);

        $feature->title = $request->title;
        $feature->title_en = $request->title_en;
        $feature->status = (int)$request->status;
        $feature->be_indexed = (int)$request->be_indexed;
        $feature->is_in_filter_rtable = (int)$request->is_in_filter_rtable;
        $feature->is_in_filter_ad = (int)$request->is_in_filter_ad;
        $feature->is_important_in_ad = (int)$request->is_important_in_ad;
        $feature->has_follow = (int)$request->has_follow;
        $feature->has_items = (int)$request->has_items;
        $feature->input_type = (int)$request->input_type;
        $feature->is_feature_in_title = (int)$request->is_feature_in_title;
        $feature->item_is_in_title = (int)$request->item_is_in_title;
        $feature->item_is_in_title_if_not_parent = (int)$request->item_is_in_title_if_not_parent;
        $feature->select_items_count = $request->select_items_count;

        $unset_pititle = 0;
        $unset_pidesc = 0;
        if ($request->page_intro_title) {
            $feature->page_intro_title = $request->page_intro_title;
        } else {
            $unset_pititle = 1;
        }
        if ($request->page_intro_desc) {
            $feature->page_intro_desc = $request->page_intro_desc;
        } else {
            $unset_pidesc = 1;
        }

        // Step 1: Store the old cat_ids and parent_id to detect changes later
        $oldCatIds = $feature->categories->pluck('id')->toArray();
        $oldParentId = $feature->parent_id;
        $unset_parent_id = 0;

        // Step 2: Check feature parent_id
        if ($request->parent_id) {
            $newParentId = $request->parent_id;
            $parentFeature = MongoFeature::find($newParentId);
            if ($newParentId != $oldParentId) {
                if ($parentFeature) {
                    $feature->parent_id = $newParentId;
                } else {
                    return response()->json(['error' => 'Parent feature not found.'], 404);
                }
            }
            $newCatIds = $parentFeature->categories->pluck('id')->toArray();
        } elseif ($request->categories) {
            if (isset($feature->parent_id)) {
                $unset_parent_id = 1;
            }
            $newCatIds = explode(',', $request->categories);
        }
        $feature->save();
        if ($unset_parent_id) {
            $feature->unset('parent_id');
        }
        if ($unset_pititle) {
            $feature->unset('page_intro_title');
        }
        if ($unset_pidesc) {
            $feature->unset('page_intro_desc');
        }

        // Step 3: Check for changes in cat_ids
        $children = $feature->allChildren();
        if (count(array_diff($oldCatIds, $newCatIds)) > 0 || count(array_diff($newCatIds, $oldCatIds)) > 0) {
            // Step 4: Remove feature_id and children feature_ids from categories if old cat_ids do not exist in new cat_ids
            $categoriesToRemoveFrom = array_diff($oldCatIds, $newCatIds);
            foreach ($categoriesToRemoveFrom as $catId) {
                $category = MongoCategory::find($catId);
                if ($category) {
                    $category->feature_ids = array_diff($category->feature_ids, [$feature->id]);
                    foreach ($children as $childFeature) {
                        $category->feature_ids = array_diff($category->feature_ids, [$childFeature->id]);
                    }
                    $category->save();
                    if (empty($category->feature_ids)) {
                        $category->unset('feature_ids');
                    }
                }
            }
        }
        // Step 5: add feature id and children feature ids to categories
        if (isset($feature->parent_id)) {
            $parentFeature = MongoFeature::find($feature->parent_id);
            if ($parentFeature) {
                $catIds = $parentFeature->categories->pluck('id')->toArray();
                foreach ($catIds as $catId) {
                    $category = MongoCategory::find($catId);
                    if ($category) {
                        $category->feature_ids = array_unique(array_merge($category->feature_ids, [$feature->id], $children->pluck('id')->toArray()));
                        $category->save();
                    }
                }
            }
        } else {
            foreach ($newCatIds as $catId) {
                $category = MongoCategory::find($catId);
                if ($category) {
                    $category->feature_ids = $category->feature_ids ?? [];
                    $category->feature_ids = array_unique(array_merge($category->feature_ids, [$feature->id], $children->pluck('id')->toArray()));
                    $category->save();
                }
            }
        }

        $this->assignLevels();

        return back()->with('success', 'ویژگی با موفق آپدیت شد');
    }

    private function assignLevels()
    {
        // تمام ویژگی‌ها رو یکجا بگیر و با id ایندکس کن برای دسترسی سریع
        $features = MongoFeature::all()->keyBy('_id');

        $levels = [];

        // تابع بازگشتی برای پیدا کردن سطح هر ویژگی
        $getLevel = function ($feature) use (&$getLevel, &$features, &$levels) {
            if (isset($levels[$feature->_id])) {
                return $levels[$feature->_id];
            }

            if (!$feature->parent_id || !isset($features[$feature->parent_id])) {
                $levels[$feature->_id] = 1;
            } else {
                $parent = $features[$feature->parent_id];
                $levels[$feature->_id] = $getLevel($parent) + 1;
            }

            return $levels[$feature->_id];
        };

        foreach ($features as $feature) {
            $level = $getLevel($feature);
            if ($feature->level !== $level) {
                $feature->level = $level;
                $feature->update();
            }
        }
    }

    public function fifilLoadItems(Request $request)
    {
        $itemRepository = new ItemRepository();
        $items = $itemRepository->getItemsWithAllChildrenByFeatureId($request->f_id, [
            'id',
            'title',
            'title_en',
            'feature_id',
            'parent_id',
            'with_parent_url'
        ]);
        return response()->json([
            'items' => $items,
        ], 200);
    }

    public function fifilLoadChItems(Request $request)
    {
        $itemRepository = new ItemRepository();
        $items = $itemRepository->getItemsWithAllChildrenByFeatureId($request->f_id, [
            'id',
            'title',
            'title_en',
            'feature_id',
            'parent_id',
            'with_parent_url'
        ]);
        return response()->json([
            'items' => $items,
        ], 200);
    }
}
