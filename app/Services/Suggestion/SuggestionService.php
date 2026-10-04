<?php

namespace App\Services\Suggestion;

use App\Models\MongoAdvertise;
use App\Models\MongoCategory;
use App\Models\MongoCategoryComment;
use App\Models\MongoItem;
use App\Models\MongoQuestion;
use App\RepositoryInterface\Feature\FeatureRepositoryInterface;
use App\RepositoryInterface\Item\ItemRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class SuggestionService
{
    private $itemRepository;
    private $featureRepository;

    public function __construct(
        ItemRepositoryInterface $itemRepository,
        FeatureRepositoryInterface $featureRepository,
    ) {
        $this->itemRepository = $itemRepository;
        $this->featureRepository = $featureRepository;
    }

    public function suggest($category = null, $item = null)
    {
        if ($category) {
            if ($category->has_children == 1) {
                return $this->suggestCategories($category);
            } else {
                if ($item) {
                    return $this->suggestItems($item);
                } else {
                    return $this->suggestCategories($category);
                }
            }
        } else {
            return $this->getGlobalSuggestions();
        }
    }

    private function suggestItems($item)
    {
        $suggestItemIds = $item->suggest_items ?? null;
        if ($suggestItemIds) {
            return ['items' => $this->getSuggestedItemsByIds($suggestItemIds)];
        }
        $categoryId = $item->category_id;
        $allItems = $this->itemRepository->getItemsByCategoryAndStatus($categoryId, 1);
        $features = $this->featureRepository->getFeaturesByCategory($categoryId);
        $feature = $features->where('id', $item->feature_id)->first();
        $parentItem = $allItems->where('id', $item->parent_id)->first();
        $itemChildren = $allItems->where('parent_id', $item->id);

        if ($itemChildren->isEmpty()) {
            $items = $this->getItemsWithoutChildren($item, $feature, $parentItem, $allItems, $features, $categoryId);
        } else {
            $items = $this->getItemsWithChildren($item, $itemChildren, $allItems);
        }
        $item->suggest_items = $items->pluck('id')->toArray();
        $item->update();
        return ['items' => $items];
    }

    private function suggestCategories($category)
    {
        $itemsCacheKey = $category->id . 'scitems';
        $catsCacheKey = $category->id . 'scats';
        // Cache::forget($itemsCacheKey);
        // Cache::forget($catsCacheKey);
        // Cache::forget('suggest_cats');
        $keys = [$itemsCacheKey, $catsCacheKey];
        $values = Cache::many($keys);
        $items = $values[$itemsCacheKey];
        $cats = $values[$catsCacheKey];

        if ($items !== null) {
            return ['items' => $items];
        } elseif ($cats !== null) {
            return ['cats' => $cats];
        } else {
            $children = MongoCategory::where('parent_id', $category->id)->where('show_in_sug', 1)->get();
            if (count($children) > 0) {
                $cats = Cache::remember($catsCacheKey, 21600, function () use ($children) {
                    return $children;
                });
            } else {
                $items = Cache::remember($itemsCacheKey, 21600, function () use ($category) {
                    return MongoItem::where('category_id', $category->id)->where('parent_id', null)->orderBy('comment_count', 'desc')->take(15)->get();
                });
            }
            if (isset($cats)) {
                return ['cats' => $cats];
            } elseif (isset($items)) {
                return ['items' => $items];
            }
        }
    }

    private function getSuggestedItemsByIds($suggestItemIds)
    {
        $suggestItems = MongoItem::whereIn('_id', $suggestItemIds)->get();
        return $suggestItems->sortBy(function ($item) use ($suggestItemIds) {
            return array_search($item->_id, $suggestItemIds);
        });
    }

    private function getItemsWithoutChildren($item, $feature, $parentItem, $allItems, $features, $categoryId)
    {
        if (isset($parentItem)) {
            $filteredItems = $allItems->where('feature_id', $feature->id)
                ->where('parent_id', $item->parent_id)
                ->where('id', '!=', $item->id);
            $items = $filteredItems->sortByDesc('comment_count')->take(20);
            $new_items = $filteredItems->sortByDesc('created_at')->take(5);
            $items = $items->merge($new_items)->unique();
            // if (count($items) < 20) {
            //     $extraItems = $allItems->where('feature_id', $feature->id)
            //         ->where('id', '!=', $item->id)
            //         ->sortByDesc('comment_count')
            //         ->take(20)
            //         ->shuffle()
            //         ->take(20 - count($items));
            //     $items = $items->merge($extraItems)->unique();
            // }
        } else {
            $suggestFeature = MongoCategory::find($categoryId)->features()->where('is_in_filter_rtable', 1)->first();
            $filteredItems = $allItems->where('feature_id', $suggestFeature->id)->where('id', '!=', $item->id);
            $items = $filteredItems->sortByDesc('comment_count')->take(20);
            $new_items = $filteredItems->sortByDesc('created_at')->take(5);
            $items = $items->merge($new_items)->unique();
        }
        return $items;
    }

    private function getItemsWithChildren($item, $itemChildren, $allItems)
    {
        $items = $itemChildren->sortByDesc('comment_count')->take(40);
        // پنج تا تصادفی از بیست تای با اولویت پایین تر انتخاب میکنه
        $randomItems = $items->slice(20, 20)->shuffle()->take(5);
        $items = $items->take(20);
        $new_items = $itemChildren->sortByDesc('created_at')->take(5);
        $items = $items->merge($new_items)->merge($randomItems)->unique();
        // if ($items->count() < 20) {
        //     if ($items->isNotEmpty()) {
        //         $featureId = $items->first()->feature_id;
        //         $extraItems = $allItems->where('feature_id', $featureId)
        //             ->where('id', '!=', $item->id)
        //             ->sortByDesc('comment_count')
        //             ->take(20)
        //             ->shuffle()
        //             ->take(20 - $items->count());
        //         $items = $items->merge($extraItems)->unique('id');
        //     }
        // }
        return $items;
    }

    private function getGlobalSuggestions()
    {
        $suggestKey = 'suggest_cats';
        // Cache::forget($suggestKey);
        $suggestCats = Cache::remember($suggestKey, 21600, function () {
            $comment_categoryIds = MongoCategoryComment::raw(function ($collection) {
                return $collection->distinct('category_id');
            });
            $question_categoryIds = MongoQuestion::raw(function ($collection) {
                return $collection->distinct('category_id');
            });
            $advertise_categoryIds = MongoAdvertise::raw(function ($collection) {
                return $collection->distinct('category_id');
            });
            $categoryIds = array_unique(array_merge($comment_categoryIds, $question_categoryIds, $advertise_categoryIds));
            return MongoCategory::whereIn('_id', $categoryIds)->where('show_in_sug', 1)->get();
        });
        return ['cats' => $suggestCats];
    }
}
