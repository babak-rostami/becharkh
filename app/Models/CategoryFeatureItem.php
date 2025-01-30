<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CategoryFeatureItem extends Model
{
    use HasFactory;
    protected $connection = 'mysql';

    public function parent()
    {
        return $this->belongsTo(CategoryFeatureItem::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(CategoryFeatureItem::class, 'parent_id');
    }

    public function follows()
    {
        return $this->hasMany(FollowFeatureItem::class, 'item_id');
    }

    public function feature()
    {
        return $this->belongsTo(CategoryFeature::class, 'feature_id');
    }

    public function withParentsNameEn()
    {
        return $this->getparentNameEn($this);
    }

    public function withParentsTitle()
    {
        return $this->getparentTitle($this);
    }

    private function getparentTitle($item)
    {
        $title = null;
        $features = CategoryFeature::where('status', 1)->get();
        $items = CategoryFeatureItem::where('status', 1)->get();

        $feature = $features->where('id', $item->feature_id)->first();
        $parentItem = $items->where('id', $item->parent_id)->first();
        if (isset($parentItem)) {
            $parentItemFeature = $features->where('id', $parentItem->feature_id)->first();
        }
        if (isset($parentItem) && isset($parentItemFeature) && $parentItemFeature->item_is_in_title && !$parentItemFeature->item_is_in_title_if_not_parent) {
            if ($feature->is_feature_in_title) {
                $title .= $this->getparentTitle($parentItem) . " " . $feature->title . " " . $item->title;
            } else {
                $title .= $this->getparentTitle($parentItem) . " " . $item->title;
            }
        } else {
            if ($feature->is_feature_in_title) {
                $title = $feature->title . " " . $item->title;
            } else {
                $title = $item->title;
            }
        }
        return $title;
    }

    private function getparentNameEn($item)
    {
        $nameEn = null;
        if ($item->parent_id != null) {
            $nameEn .= $this->getparentTitle($item->parent) . " " . $item->title_en;
        } else {
            $nameEn = $item->title_en;
        }
        return $nameEn;
    }

    public function withParentsRtableUrl()
    {
        $feature = $this->feature;
        if (isset($feature)) {
            $category = $feature->category;
            if (isset($category)) {
                $url = route('question.index', $category->slug) . "?" . $this->getparentUrl($this);
                return $url;
            }
        }
    }
    public function withParentsCommentUrl()
    {
        $feature = $this->feature;
        if (isset($feature)) {
            $category = $feature->category;
            if (isset($category)) {
                $url = route('question.index', $category->slug) . "?s=1&" . $this->getparentUrl($this);
                return $url;
            }
        }
    }
    public function withParentsAdvertiseUrl()
    {
        $feature = $this->feature;
        if (isset($feature)) {
            $category = $feature->category;
            if (isset($category)) {
                $url = route('ads.index', $category->slug) . "?" . $this->getparentUrl($this);
                return $url;
            }
        }
    }


    private function getparentUrl($item)
    {
        $url = null;
        $feature = $item->feature;
        if (isset($feature)) {
            $category = $feature->category;
            if (isset($category)) {
                if ($item->parent_id != null) {
                    $url .= $this->getparentUrl($item->parent) . "&" . $feature->slug . "=" . $item->slug;
                } else {
                    $url = $feature->slug . "=" . $item->slug;
                }
                return $url;
            }
        }
    }


    public function advertiseFeatureValues()
    {
        return $this->hasMany(AdvertiseFeatureValue::class, 'value')->where('feature_id', $this->feature->id);
    }

    public function advertises()
    {
        $ads = collect();
        foreach ($this->advertiseFeatureValues as $afv) {
            $ad = $afv->advertise;
            $ads->add($ad);
        }
        $ads = $ads->unique();
        return $ads;
    }


    public function commentFeatureValues()
    {
        return $this->hasMany(CategoryCommentFeatureValue::class, 'item_id')->where('feature_id', $this->feature->id);
    }
    public function questionFeatureValues()
    {
        return $this->hasMany(QuestionFeatureValue::class, 'item_id')->where('feature_id', $this->feature->id);
    }
    public function blogFeatureValues()
    {
        return $this->hasMany(BlogFeatureValue::class, 'item_id');
    }

    public function comments()
    {
        $cms = collect();
        foreach ($this->commentFeatureValues as $afv) {
            $cm = $afv->comment;
            $cms->add($cm);
        }
        $cms = $cms->unique();
        return $cms->sortByDesc('likes1')->take(3);
    }
    public function questions()
    {
        $cms = collect();
        foreach ($this->questionFeatureValues as $afv) {
            $cm = $afv->question;
            $cms->add($cm);
        }
        $cms = $cms->unique();
        return $cms->sortByDesc('likes1')->take(3);
    }
    public function blogs()
    {
        $cms = collect();
        foreach ($this->blogFeatureValues as $bfv) {
            $cm = $bfv->blog;
            $cms->add($cm);
        }
        return $cms->unique();
    }

    public function videos()
    {
        return $this->belongsToMany(Video::class, 'video_feature_values', 'item_id', 'video_id')->withTimestamps();
    }

    public function images()
    {
        return $this->hasMany(ItemImage::class, 'item_id');
    }

    public function hasIMage()
    {
        if ($this->images->isEmpty()) {
            return false;
        } else {
            return true;
        }
    }


    public function image1()
    {
        $item = $this;
        $category = $item->feature->category;
        if (!$item->images->isEmpty()) {
            $image = $item->images->first();
            if (isset($image)) {
                $path = "https://dl.becharkh.com/user_files/";
                return $path . $image->thum;
            } else {
                return $category->thumb();
            }
        } else {
            return $category->thumb();
        }
    }

    public function thumb()
    {
        $category = $this->feature->category;
        if (!$this->images->isEmpty()) {
            $image = $this->images->first();
            if (isset($image)) {
                $path = "https://dl.becharkh.com/user_files/";
                return $path . $image->thum;
            } else {
                return $category->thumb();
            }
        } else {
            return $category->thumb();
        }
    }

    public function suggestItems()
    {
        $item = $this;
        $categories = Cache::rememberForever('categories', function () {
            return SiteCategory::where('status', 1)->get();
        });
        $features = Cache::rememberForever('features', function () {
            return CategoryFeature::where('status', 1)->get();
        });
        $allItems = Cache::rememberForever('allItems', function () {
            return CategoryFeatureItem::where('status', 1)->get();
        });
        $feature = $features->where('id', $item->feature_id)->first();
        $category = $categories->where('id', $feature->category_id)->first();
        $items = collect();
        $parentItem = $allItems->where('id', $item->parent_id)->first();

        $itemChildren = $allItems->where('parent_id', $item->id);
        // if item selected hasn't children : s5 model hasn't children byt ford brand has children
        if (count($itemChildren) == 0) {
            if (isset($parentItem)) {
                foreach ($feature->itemsByParentItemSlug($parentItem->slug) as $i) {
                    if ($item->id != $i->id) {
                        $items->add($i);
                    }
                }
            } else {
                $sitems = $allItems->where('feature_id', $category->featureForRtable->first()->id);
                foreach ($sitems as $i) {
                    if ($item->id != $i->id) {
                        $items->add($i);
                    }
                }
            }
        } else {
            foreach ($itemChildren as $i) {
                if ($item->id != $i->id) {
                    $items->add($i);
                }
            }
        }

        if ($items->count() < 10) {
            $remain = 15 - $items->count();
            if (isset($parentItem)) {
                $sugSib = collect();
                foreach ($this->sibling($parentItem) as $parentSibling) {
                    $parentSiblingChildren = $allItems->where('parent_id', $parentSibling->id);
                    $count = 0;
                    foreach ($parentSiblingChildren as $si) {
                        $count += 1;
                        if ($count > 4) {
                            break;
                        }
                        $sugSib->add($si);
                    }
                }
                foreach ($sugSib as $sb) {
                    $remain = $remain - 1;
                    if ($remain == 0) {
                        break;
                    }
                    $items->add($sb);
                }
            } else {
                $sugSib = collect();
                foreach ($this->sibling($item) as $parentSibling) {
                    $parentSiblingChildren = $allItems->where('parent_id', $parentSibling->id);
                    $count = 0;
                    foreach ($parentSiblingChildren as $si) {
                        $count += 1;
                        if ($count > 4) {
                            break;
                        }
                        $sugSib->add($si);
                    }
                }
                foreach ($sugSib as $sb) {
                    $remain = $remain - 1;
                    if ($remain == 0) {
                        break;
                    }
                    $items->add($sb);
                }
            }
        }

        return $items;
    }

    // $features = Cache::rememberForever('features', function () {
    //     return CategoryFeature::where('status', 1)->get();
    // });
    // $feature = $features->where('id', $this->feature_id)->first();
    // $popularItems = $feature->popularItems();
    // $suggestItems = $this->suggestItems();
    // foreach ($suggestItems as $key1 => $item) {
    //     if (!$popularItems->isEmpty()) {
    //         foreach ($popularItems as $key2 => $pi) {
    //             if ($pi->id == $item->id) {
    //                 $suggestItems->forget($key1);
    //                 $popularItems->forget($key2);
    //             }
    //         }
    //     } else {
    //         break;
    //     }
    // }
    // return $suggestItems;
    public function suggestItemsWithotBest()
    {
        $features = Cache::rememberForever('features', function () {
            return CategoryFeature::where('status', 1)->get();
        });
        $feature = $features->where('id', $this->feature_id)->first();
        $popularItems = $feature->popularItems();
        $suggestItems = $this->suggestItems();

        if ($popularItems->isNotEmpty()) {
            $popularItemIds = $popularItems->pluck('id')->toArray();
            $suggestItems = $suggestItems->reject(function ($item) use ($popularItemIds) {
                return in_array($item->id, $popularItemIds);
            });
        }

        return $suggestItems;
    }

    public function itemRankInPopular()
    {
        $items = $this->feature->items->sortByDesc('follows');
        $count = 1;
        foreach ($items as $item) {
            if ($item->id == $this->id) {
                return $count;
            } else {
                $count += 1;
            }
        }
    }

    public function sibling($item)
    {
        $allItems = CategoryFeatureItem::where('status', 1)->get();

        if ($item->parent_id != null) {
            $pItemChildren = $allItems->where('parent_id', $item->parent_id);
            return $pItemChildren;
        } else {
            $sitems = $allItems->where('feature_id', $item->feature_id)->where('id', '!=', $item->id);
            return $sitems;
        }
    }
}
