<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvertiseFeatureValue extends Model
{
    use HasFactory;

    public function feature()
    {
        return $this->hasOne(CategoryFeature::class, 'id', 'feature_id');
    }

    public function advertise()
    {
        return $this->belongsTo(Advertise::class, 'advertise_id');
    }

    public function item()
    {
        return $this->belongsTo(CategoryFeatureItem::class, 'value');
    }

    public function queryUrl()
    {
        $fea = $this->feature;
        $req = route('ads.index', $fea->category->slug);
        $query = null;
        if (isset($this->feature) && isset($this->item)) {
            $query = $this->addUrl($this->feature, $this->item);
            $req .= "?" . $query;
        }

        if ($query == null) {
            return null;
        } else {
            return $req;
        }
    }

    private function addUrl($feature, $item)
    {
        if ($feature->is_important_in_ad) {
            $req = null;
            if (isset($feature->parent) && isset($item->parent)) {
                $req =  $this->addUrl($feature->parent, $item->parent);
            }
            if ($req) {
                return $req . "&" . $feature->slug . "=" . $item->slug;
            } else {
                return $req . $feature->slug . "=" . $item->slug;
            }
        }
    }


    public function titleWithParents()
    {
        if (isset($this->advertise->category)) {
            $title = $this->advertise->category->title . " " . $this->addParentTitle($this->item);
        } else {
            $title = $this->addParentTitle($this->item);
        }
        return $title;
    }



    private function addParentTitle($item)
    {
        if (isset($item)) {
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
}
