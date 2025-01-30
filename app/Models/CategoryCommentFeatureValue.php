<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryCommentFeatureValue extends Model
{
    use HasFactory;

    public function comment()
    {
        return $this->belongsTo(CategoryComment::class, 'comment_id');
    }


    public function feature()
    {
        return $this->belongsTo(CategoryFeature::class, 'feature_id');
    }

    public function item()
    {
        return $this->belongsTo(CategoryFeatureItem::class, 'item_id');
    }

    public function queryUrl()
    {
        $fea = $this->feature;
        $req = route('question.index', $fea->category->slug);


        $req .= "?s=1&" . $this->addUrl($this->feature, $this->item);
        return $req;
    }

    private function addUrl($feature, $item)
    {
        $req = null;
        if (isset($feature->parent)) {
            $req =  $this->addUrl($feature->parent, $item->parent);
        }
        if ($req) {
            return $req . "&" . $feature->slug . "=" . $item->slug;
        } else {
            return $req . $feature->slug . "=" . $item->slug;
        }
    }


    public function titleWithParents()
    {
        $title = $this->addParentTitle($this->item);
        return $title;
    }


    private function addParentTitle($item)
    {
        $title = null;
        if (isset($item->parent)) {
            $title =  $this->addParentTitle($item->parent);
        }
        if ($title) {
            return $title . " " . $item->title;
        } else {
            return $item->title;
        }
    }

}
