<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionFeatureValue extends Model
{
    use HasFactory;

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
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


        $req .= "?" . $this->addUrl($this->feature, $this->item);
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
        if (isset($this->item)) {
            return $this->item->withParentsTitle();
        }
    }

    // public function titleWithParents()
    // {
    //     if (isset($this->question->category)) {
    //         $title = $this->question->category->title . " " . $this->addParentTitle($this->item);
    //     } else {
    //         $title = $this->addParentTitle($this->item);
    //     }
    //     return $title;
    // }



    // private function addParentTitle($item)
    // {
    //     if (isset($item)) {
    //         $title = null;
    //         if (isset($item->parent)) {
    //             $title =  $this->addParentTitle($item->parent);
    //         }
    //         if ($title) {
    //             return $title . " " . $item->title;
    //         } else {
    //             return $item->title;
    //         }
    //     }
    // }
}
