<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteCategory extends Model
{
    use HasFactory;
    protected $connection = 'mysql';

    public function children()
    {
        return $this->hasMany(SiteCategory::class, 'parent_id');
    }


    public function parent()
    {
        return $this->belongsTo(SiteCategory::class, 'parent_id');
    }


    public function withParentsTitle()
    {
        if (trim($this->getparentTitle($this), " ") == "") {
            return $this->title;
        } else {
            return $this->getparentTitle($this);
        }
    }

    private function getparentTitle($category)
    {
        $title = null;
        if ($category->parent_id != null) {
            $categoryParent = $category->parent;
            $title .= $this->getparentTitle($categoryParent) . " " . ($category->is_cat_in_title ? $category->title : "");
        } else {
            $title = ($category->is_cat_in_title ? $category->title : "");
        }
        return $title;
    }

    public function allParentTree()
    {
        $hasParent = 1;
        $category = $this;
        $parents = collect();
        while ($hasParent) {
            $parent = $category->parent;
            if (isset($parent)) {
                $parents->add($parent);
                if ($parent->parent_id == null) {
                    $hasParent = 0;
                } else {
                    $category = $parent;
                }
            } else {
                $hasParent = 0;
            }
        }
        return $parents;
    }

    public function isInParents($cat_id, $id)
    {
        $category = SiteCategory::find($cat_id);
        if ($category->id == $id) {
            return true;
        } else {
            if (isset($category->parent)) {
                return $this->isInParents($category->parent->id, $id);
            } else {
                return false;
            }
        }
    }

    public function allFeaturesWithParentsFeaturesForRtable()
    {
        $features = collect();
        foreach ($this->features->SortBy('id', true)->where('is_in_filter_rtable', 1) as $f) {
            $features->add($f);
        }
        if (isset($this->parent)) {
            foreach ($this->parent->parentsFeaturesForRtable() as $ff) {
                $features->add($ff);
            }
        }
        return $features;
    }

    public function allFeaturesWithParentsFeaturesForAds()
    {
        $features = collect();
        foreach ($this->features->SortBy('id', true)->where('is_in_filter_ad', 1) as $f) {
            $features->add($f);
        }
        if (isset($this->parent)) {
            foreach ($this->parent->parentsFeaturesForAds() as $ff) {
                $features->add($ff);
            }
        }
        return $features;
    }

    //it's just return parent feature doesn't have parent feature
    public function featuresWithParentsFeaturesForAds()
    {
        $features = collect();
        foreach ($this->features->SortBy('id', true)->where('is_in_filter_ad', 1)->where('parent', null) as $f) {
            $features->add($f);
        }
        if (isset($this->parent)) {
            foreach ($this->parent->parentsFeaturesForAds() as $ff) {
                $features->add($ff);
            }
        }
        return $features;
    }

    public function featuresWithParentsFeaturesForRtable()
    {
        $features = collect();
        foreach ($this->features->SortBy('id', true)->where('is_in_filter_rtable', 1)->where('parent', null) as $f) {
            $features->add($f);
        }
        if (isset($this->parent)) {
            foreach ($this->parent->parentsFeaturesForRtable() as $ff) {
                $features->add($ff);
            }
        }
        return $features;
    }

    private function parentsFeaturesForRtable()
    {
        $features = collect();
        foreach ($this->featuresThatIsInChildCats->SortBy('id', true)->where('is_in_filter_rtable', 1)->where('parent', null) as $f) {
            $features->add($f);
        }
        if (isset($this->parent)) {
            foreach ($this->parent->featuresWithParentsFeaturesForRtable() as $ff) {
                $features->add($ff);
            }
        }
        return $features;
    }

    private function parentsFeaturesForAds()
    {
        $features = collect();
        foreach ($this->featuresThatIsInChildCats->SortBy('id', true)->where('is_in_filter_ad', 1)->where('parent', null) as $f) {
            $features->add($f);
        }
        if (isset($this->parent)) {
            foreach ($this->parent->featuresWithParentsFeaturesForAds() as $ff) {
                $features->add($ff);
            }
        }
        return $features;
    }

    public function featuresThatIsInChildCats()
    {
        return $this->hasMany(CategoryFeature::class, 'category_id')->where('is_in_child_cats', 1);
    }

    public function features()
    {
        return $this->hasMany(CategoryFeature::class, 'category_id');
    }

    public function featureswhereHasRFV()
    {
        return $this->hasMany(CategoryFeature::class, 'category_id')->whereHas('rTableFeatureValues');
    }


    public function features1()
    {
        return $this->hasMany(CategoryFeature::class, 'category_id')->where('parent_id', null);
    }
    public function features1WithItems()
    {
        return $this->hasMany(CategoryFeature::class, 'category_id')->with('itemsPluck')->where('parent_id', null)->where('status', 1)->select(['id', 'title', 'slug', 'parent_id', 'is_important_in_ad', 'input_type', 'is_in_filter_rtable']);
    }
    public function featuresWithItemsRtableCreate()
    {
        return $this->hasMany(CategoryFeature::class, 'category_id')->with('itemsPluck')->where('parent_id', null)->where('status', 1)->where('is_in_filter_rtable', 1)->select(['id', 'title', 'slug', 'parent_id', 'input_type', 'is_in_filter_rtable']);
    }
    public function featureForRtable()
    {
        return $this->hasMany(CategoryFeature::class, 'category_id')->where('status', 1)->where('is_in_filter_rtable', 1);
    }

    public function featureForAdvertise()
    {
        return $this->hasMany(CategoryFeature::class, 'category_id')->where('status', 1)->where('is_important_in_ad', 1);
    }

    public function featuresWithItemsForAdsFilter()
    {
        return $this->hasMany(CategoryFeature::class, 'category_id')->with('itemsPluck')->where('parent_id', null)->where('status', 1)->where('is_in_filter_ad', 1)->select(['id', 'title', 'slug', 'parent_id', 'input_type', 'is_in_filter_ad']);
    }


    public function questions()
    {
        return $this->hasMany(Question::class, 'category_id')->orderBy('id', 'desc');
    }

    public function comments()
    {
        return $this->hasMany(CategoryComment::class, 'category_id')->where('parent_id', null)->orderBy('id', 'desc');
    }

    public function image()
    {
        if ($this->image != null) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $this->image;
        } else {
            return 'files/other/images/default.jpg';
        }
    }

    public function thumb()
    {
        if ($this->image != null) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $this->thum;
        } else {
            return 'files/other/images/default.jpg';
        }
    }


    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category_id')->orderBy('id', 'desc');
    }

    public function withChildrenBlogs()
    {
        $blogs = $this->blogs;
        foreach ($this->children as $child) {
            $blogs = $blogs->merge($child->withChildrenBlogs());
        }
        return $blogs;
    }

    public function blogs2()
    {
        return $this->hasMany(Blog::class, 'category_id')->orderBy('id', 'desc')->paginate(15);
    }

    public function advertises()
    {
        return $this->hasMany(Advertise::class, 'category_id')->orderBy('id', 'desc');
    }

    public function withChildrenAdvertises()
    {
        $advertises = $this->advertises;
        foreach ($this->children as $child) {
            $advertises = $advertises->merge($child->withChildrenAdvertises());
        }
        return $advertises;
    }

    public function canDelete()
    {
        if (
            $this->status == 0 &&
            $this->advertises->isEmpty() &&
            $this->questions->isEmpty() &&
            $this->children->isEmpty() &&
            $this->comments->isEmpty() &&
            $this->blogs->isEmpty()
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function hasAdButChildrenDont()
    {
        if ($this->has_ads) {
            foreach ($this->children as $ch) {
                if ($ch->has_ads) {
                    return false;
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public function childrenHaveAds()
    {
        $categories = Cache::rememberForever('categories', function () {
            return SiteCategory::where('status', 1)->get();
        });
        $children = $categories->where('parent_id', $this->id);
        foreach ($children as $child) {
            if ($child->has_ads) {
                return true;
            }
        }
        return false;
    }
}
