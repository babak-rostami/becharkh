<?php

namespace App\Services\Item;

use App\Models\RtablePageData;

class FeatureService
{
    /**
     * گرفتن ویژگی فرزند بر اساس آدرس URL
     */
    public function getChildFeature($category, $request)
    {
        $data = new RtablePageData();
        // ویژگی‌هایی که باید در فیلتر جدول باشن
        $categoryFeatures = $category->features()->where('is_in_filter_rtable', 1);

        // پیش‌فرض: بالاترین level
        $childFeature = $categoryFeatures->sortByDesc('level')->first();

        // استخراج ویژگی‌ها از URL
        $featuresInUrl = collect($data->getFeaturesInUrl($request))
            ->map(fn($fiu) => explode('=', $fiu)[0]);

        if ($featuresInUrl->isNotEmpty()) {
            $featuresBySlug = $categoryFeatures->keyBy('slug');

            // ویژگی‌هایی که در آدرس هستن
            $featuresIsInUrl = $featuresInUrl
                ->map(fn($slug) => $featuresBySlug->get($slug))
                ->filter();

            if ($featuresIsInUrl->isNotEmpty() && !$featuresInUrl->contains($childFeature->slug)) {
                $childFeature = $featuresIsInUrl->sortByDesc('level')->first();
            }
        }

        return $childFeature;
    }
}
