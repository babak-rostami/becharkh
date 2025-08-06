<?php

namespace App\Services\Affilate;

use App\Models\Affilate;

class AffilateService
{

    public function suggestsForPages($category = null, $item = null, $take = null)
    {
        $affiliates = collect();
        $aff_count = 0;

        if ($item) {
            $affiliates = Affilate::where('items', $item->id)
                ->where('status', 1)
                ->with('video')
                ->take(20)
                ->get()
                ->shuffle()
                ->take($take);

            if (isset($category)) {
                $partitioned = $affiliates->partition(function ($affiliate) use ($category) {
                    return isset($affiliate->categories) && in_array($category->id, $affiliate->categories);
                });
                $allowed = $partitioned[0];
                $disallowed = $partitioned[1];
                if ($disallowed->count() > 1) {
                    $keepOne = $disallowed->random(1);
                    $disallowed = $keepOne;
                }
                $affiliates = $disallowed->merge($allowed);
            }
            $aff_count = $affiliates->count();
        }
        // if ($aff_count < 3 && $category) {
        //     $cat_affiliates = Affilate::where('categories', $category->id)->where('status', 1)->take(20)->get()->shuffle()->take($take - $aff_count);
        //     $affiliates = $affiliates->merge($cat_affiliates)->unique();
        //     $aff_count = $affiliates->count();
        // }

        if ($aff_count < 3) {
            $other_affiliates = Affilate::where('just_this_page', 0)
                ->with('video')
                ->where('status', 1)->take(20)->get()->shuffle()->take($take - $aff_count);
            $affiliates = $affiliates->merge($other_affiliates)->unique();
            $aff_count = $affiliates->count();
        }

        foreach ($affiliates as  $affiliate) {
            $affiliate->body = $this->modifyAffiliateBody($affiliate);
        }

        return $affiliates;
    }

    private function modifyAffiliateBody($affiliate)
    {
        $body = $affiliate->body;

        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $body, $matches);
        $affiliate->image_urls = $matches[1] ?? [];

        $body = preg_replace('/<figure[^>]*>.*?<\/figure>/is', '', $body);

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


    public function suggestsForPagesApi($category_id = null, $item_id = null, $take = null)
    {
        $affiliates = collect();
        $aff_count = 0;

        if ($item_id) {
            $affiliates = Affilate::where('items', $item_id)
                ->where('status', 1)
                ->select(['id', 'title', 'slug', 'link', 'image', 'body', 'video_id'])
                ->take(20)
                ->with(['video' => function ($query) {
                    $query->select(['id', 'image']);
                }])
                ->get()
                ->shuffle()
                ->take($take);

            if (isset($category_id)) {
                $partitioned = $affiliates->partition(function ($affiliate) use ($category_id) {
                    return isset($affiliate->categories) && in_array($category_id, $affiliate->categories);
                });
                $allowed = $partitioned[0];
                $disallowed = $partitioned[1];
                if ($disallowed->count() > 1) {
                    $keepOne = $disallowed->random(1);
                    $disallowed = $keepOne;
                }
                $affiliates = $disallowed->merge($allowed);
            }
            $aff_count = $affiliates->count();
        }
        if ($aff_count < 3) {
            $other_affiliates = Affilate::where('just_this_page', 0)
                ->where('status', 1)
                ->select(['id', 'title', 'slug', 'link', 'image', 'body', 'video_id'])
                ->take(20)
                ->with(['video' => function ($query) {
                    $query->select(['id', 'image']);
                }])
                ->get()->shuffle()->take($take - $aff_count);
            $affiliates = $affiliates->merge($other_affiliates)->unique();
            $aff_count = $affiliates->count();
        }

        foreach ($affiliates as  $affiliate) {
            $affiliate->body = $this->modifyAffiliateBody($affiliate);
        }

        return $affiliates;
    }

    public function suggestForPages($category = null, $item = null)
    {
        if ($item) {
            $affiliate = Affilate::where('items', $item->id)->where('status', 1)->with('video')->take(20)->get()->shuffle()->first();
        }

        if (!isset($affiliate) && $category) {
            $affiliate = Affilate::where('categories', $category->id)->where('status', 1)->with('video')->take(20)->get()->shuffle()->first();
        }

        if (!isset($affiliate)) {
            $affiliate = Affilate::where('just_this_page', 0)->where('status', 1)->with('video')->take(20)->get()->shuffle()->first();
        }

        $affiliate->body = $this->modifyAffiliateBody($affiliate);

        return $affiliate;
    }

    public function suggestForAds($category = null, $item = null)
    {
        $affiliates = collect();
        if ($item) {
            $affiliates = Affilate::where('items', $item->id)->where('status', 1)->with('video')->take(20)->get();
            if ($category) {
                $affiliates = $affiliates->sortByDesc(function ($affiliate) use ($category) {
                    return in_array($category->id, (array)$affiliate->categories) ? 1 : 0;
                })->values();
            }
        }
        if ($affiliates->count() < 10) {
            $cat_affilates = Affilate::where('just_this_page', 0)->where('status', 1)->with('video')->take(20)->get()->shuffle();
            $affiliates = $affiliates->merge($cat_affilates);
        }
        $affiliates = $affiliates->unique('id')->take(10);
        foreach ($affiliates as $affiliate) {
            $affiliate->body = $this->modifyAffiliateBody($affiliate);
        }
        return $affiliates;
    }

    public function suggestForQuestion($question_id, $category = null, $item = null)
    {
        $affiliates = Affilate::where('questions', $question_id)->where('status', 1)->with('video')->take(3)->get()->shuffle();
        foreach ($affiliates as $affiliate) {
            $affiliate->body = $this->modifyAffiliateBody($affiliate);
        }

        $aff_count = $affiliates->count();
        if ($aff_count < 3) {
            $new_affiliates = $this->suggestsForPages($category, $item, 5);
            $affiliates = $affiliates->merge($new_affiliates)->unique()->take(3);
        }
        return $affiliates;
    }
}
