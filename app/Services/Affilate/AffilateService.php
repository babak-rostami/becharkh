<?php

namespace App\Services\Affilate;

use App\Models\Affilate;

class AffilateService
{
    public function suggestForPages($category = null, $item = null)
    {
        if ($item) {
            $affiliate = Affilate::where('items', $item->id)->take(20)->get()->shuffle()->first();
        }

        if (!isset($affiliate) && $category) {
            $affiliate = Affilate::where('categories', $category->id)->take(20)->get()->shuffle()->first();
        }

        if (!isset($affiliate)) {
            $affiliate = Affilate::where('just_this_page', 0)->take(20)->get()->shuffle()->first();
        }

        $affiliate->body = $this->modifyAffiliateBody($affiliate);

        return $affiliate;
    }

    public function suggestForAds($category = null, $item = null)
    {
        $affiliates = collect();
        if ($item) {
            $affiliates = Affilate::where('items', $item->id)->take(20)->get();
        }
        if ($affiliates->count() < 10 && $category) {
            $cat_affilates = Affilate::where('categories', $category->id)->take(20)->get()->shuffle();
            $affiliates = $affiliates->merge($cat_affilates);
        }
        if ($affiliates->count() < 10) {
            $cat_affilates = Affilate::where('just_this_page', 0)->take(20)->get()->shuffle();
            $affiliates = $affiliates->merge($cat_affilates);
        }
        $affiliates = $affiliates->unique('id')->take(10);
        foreach ($affiliates as $affiliate) {
            $affiliate->body = $this->modifyAffiliateBody($affiliate);
        }
        return $affiliates;
    }

    private function modifyAffiliateBody($affiliate)
    {
        $body = $affiliate->body;
        return preg_replace_callback(
            '/<h2>(.*?)<\/h2>/i',
            function ($matches) use ($affiliate) {
                $a_id = "affilb-route-" . $affiliate->id;
                if ($affiliate->google_index) {
                    return '<a target="_blank" class="mb-2" id="' . $a_id . '" href="' . route('product.show', $affiliate->slug) . '">' . $matches[1] . '</a>';
                } else {
                    return '<h2 class="cur-p mb-2" id="' . $a_id . '" onclick="jslink(\'' . route('product.show', $affiliate->slug) . '\', 1)">' . $matches[1] . '</h2>';
                }
            },
            $body,
            1
        );
    }

    public function suggestForQuestion($question_id, $category = null, $item = null)
    {
        $affiliate = Affilate::where('questions', $question_id)->take(20)->get()->shuffle()->first();
        if (isset($affiliate)) {
            $affiliate->body = $this->modifyAffiliateBody($affiliate);
        } else {
            $affiliate = $this->suggestForPages($category, $item);
        }
        return $affiliate;
    }
}
