<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    use HasFactory;

    public function brandWithNameEn($nameEn)
    {
        $brand = Brand::where('nameEn', $nameEn)->first();
        return $brand;
    }

    public function getFeatureValue($feature_slug, $item_id)
    {
        $feature = CategoryFeature::where('slug',$feature_slug)->first();
        $item = $feature->items->where('id',$item_id)->first();
        return $item;
    }

    public function brand($slug)
    {
        $brand = Brand::where('nameEn', $slug)->first();
        return $brand;
    }

    public function carModel($brand, $model)
    {
        $brand = Brand::where('nameEn', $brand)->first();
        foreach ($brand->models as $m) {
            if ($m->nameEn == $model) {
                return $m;
            }
        }
        return $m;
    }

    public function brandModel($brand_slug, $model_slug)
    {
        $brand = $this->brand($brand_slug);
        foreach ($brand->models as $m) {
            if ($m->nameEn == $model_slug) {
                return $m;
            }
        }
    }

    public function city($id)
    {
        $city = Shahr::find($id);
        return $city;
    }

    public function ostan($id)
    {
        $ostan = Ostan::find($id);
        return $ostan;
    }

    public function brandsSortByCarCount()
    {
        $brands = Brand::with('cars')->get()->sortByDesc(function ($brand) {
            return $brand->cars->count();
        });
        return $brands;
    }

    public function modelsSortByCarCount()
    {
        $carModels = CarModel::with('cars')->get()->sortByDesc(function ($carModel) {
            return $carModel->cars->count();
        });
        return $carModels;
    }
}
