<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CategoryFeature extends Model
{
    use HasFactory;

    public function category()
    {
        return $this->belongsTo(SiteCategory::class, 'category_id');
    }
    public function parent()
    {
        return $this->belongsTo(CategoryFeature::class, 'parent_id');
    }

    public function allParents()
    {
        $features = Cache::rememberForever('features', function () {
            return CategoryFeature::where('status', 1)->get();
        });
        $parents = collect();
        $parent = $features->where('id', $this->parent_id)->first();
        if (isset($parent)) {
            $this->getparents($parents, $parent);
        }
        return $parents;
    }

    private function getparents($parents, CategoryFeature $parent)
    {
        $features = Cache::rememberForever('features', function () {
            return CategoryFeature::where('status', 1)->get();
        });
        $parents->add($parent);
        $parent2 = $features->where('id', $parent->parent_id)->first();
        if (isset($parent2)) {
            $this->getparents($parents, $parent2);
        }
    }

    public function children()
    {
        return $this->hasMany(CategoryFeature::class, 'parent_id');
    }


    public function items()
    {
        return $this->hasMany(CategoryFeatureItem::class, 'feature_id');
    }

    public function itemsByParentItemSlug($piSlug)
    {
        $allItems = Cache::rememberForever('allItems', function () {
            return CategoryFeatureItem::where('status', 1)->get();
        });
        $pItem = $allItems->where('feature_id', $this->parent_id)->where('slug', $piSlug)->first();
        if (isset($pItem)) {
            $items = $allItems->where('feature_id', $this->id)->where('parent_id', $pItem->id);
            return $items;
        } else {
            return collect();
        }
    }

    public function itemsPluck()
    {
        return $this->hasMany(CategoryFeatureItem::class, 'feature_id')->where('status', 1)->select(['id', 'title', 'slug', 'feature_id', 'parent_id']);
    }

    public function rTableFeatureValues()
    {
        return $this->hasMany(QuestionFeatureValue::class, 'feature_id');
    }

    public function popularItems()
    {
        return $this->items->sortByDesc('follows')->take(5);
    }
}
