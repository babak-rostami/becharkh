<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoFeature extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'features';

    protected $fillable = [
        'category_id',
        'title',
        'title_en',
        'slug',
        'parent_id',
        'status',
        'is_in_filter_rtable',
        'is_important_in_ad',
        'input_type',
        'is_in_filter_ad',
        'is_in_page_title',
        'has_follow',
        'has_items',
        'is_feature_in_title',
        'item_is_in_title',
        'item_is_in_title_if_not_parent',
        'is_in_child_cats',
        'be_indexed',
        'select_items_count'
    ];

    public function categories()
    {
        return $this->hasMany(MongoCategory::class, 'feature_ids');
    }

    public function category()
    {
        return $this->belongsTo(MongoCategory::class, 'category_id');
    }

    public function children()
    {
        return $this->hasMany(MongoFeature::class, 'parent_id');
    }

    public function allChildren()
    {
        $children = collect();
        $this->load('children');
        foreach ($this->children as $child) {
            $children->push($child);
            $children = $children->merge($child->allChildren());
        }
        return $children;
    }

    public function items()
    {
        return $this->hasMany(MongoItem::class, 'feature_id')->where('status', 1);
    }

    public function allItems()
    {
        return $this->hasMany(MongoItem::class, 'feature_id');
    }


    public function parent()
    {
        return $this->belongsTo(MongoFeature::class, 'parent_id');
    }

    public function parents()
    {
        $feature = $this;
        $parentfeatures = collect();
        while (isset($feature->parent_id)) {
            $parent = $feature->parent;
            $parentfeatures->push($parent);
            $feature = $parent;
        }
        return $parentfeatures;
    }
}
