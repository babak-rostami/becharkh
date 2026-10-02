<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class RtablePageData extends Model
{

    public function getFeatureItemFromUrl($feature_slug)
    {
        $feature = MongoFeature::where('slug', $feature_slug)->first();
        $req = Request::query();
        if (!isset($req[$feature->slug])) {
            return null;
        }
        $items = $feature->items->where('slug', $req[$feature->slug]);
        if (count($items) > 0) {
            if (count($items) == 1) {
                foreach ($items as $item) {
                    return $item;
                }
            } elseif (count($items) > 1) {
                $parentFeature = $feature->parent;
                foreach ($items as $item) {
                    if (isset($req[$parentFeature->slug])) {
                        if ($item->parent->slug == $req[$parentFeature->slug]) {
                            return $item;
                        }
                    } else {
                        foreach ($items as $item) {
                            return $item;
                        }
                    }
                }
                foreach ($items as $item) {
                    return $item;
                }
            }
        } else {
            return null;
        }
    }
    public function getFeatureItemFromUrlTitle($feature_slug)
    {
        $fifu = $this->getFeatureItemFromUrl($feature_slug);
        if ($fifu == null) {
            return null;
        } else {
            return $fifu->title;
        }
    }
    public function getCategoryFeatureItemFromUrl($f)
    {
        $feature = $f;
        $req = Request::query();
        $features = Cache::rememberForever('features', function () {
            return MongoFeature::where('status', 1)->get();
        });
        $allItems = Cache::rememberForever('items', function () {
            return MongoItem::where('status', 1)->get();
        });
        $items = $allItems->where('feature_id', $feature->id)->where('slug', $req[$feature->slug]);
        if (count($items) > 0) {
            if (count($items) == 1) {
                foreach ($items as $item) {
                    return $item;
                }
            } elseif (count($items) > 1) {
                $parentFeature = $features->where('id', $feature->parent_id)->first();
                if (isset($parentFeature) && isset($req[$parentFeature->slug])) {
                    foreach ($items as $item) {
                        $parentItem = $allItems->where('parent_id', $item->parent_id)->first();
                        if ($parentItem->slug == $req[$parentFeature->slug]) {
                            return $item;
                        }
                    }
                } else {
                    foreach ($items as $item) {
                        return $item;
                    }
                }
                foreach ($items as $item) {
                    return $item;
                }
            }
        } else {
            return null;
        }
    }

    public function AdvertiseCanonUrl($category, $item)
    {
        if ($item) {
            $url = $item->withParentsAdvertiseUrl();
            return $url;
        } else {
            if ($category) {
                return route('ads.index', $category->slug);
            } else {
                return route('ads.index');
            }
        }
    }

    public function removeFeatureFromUrl($feature)
    {
        $req = Request::getRequestUri();
        $reqs = Request::url();
        $count = 0;
        $query = explode("&", explode('?', $req)[1]);
        foreach ($query as $q) {
            $qr = explode("=", $q);
            if ($qr[0] == $feature->slug) {
                break;
            } else {
                if ($count == 0) {
                    $reqs .= "?" . $q;
                    $count++;
                } else {
                    $reqs .= "&" . $q;
                }
            }
        }
        return $reqs;
    }

    public function isRTable()
    {
        $req = Request::query();
        if (isset($req['s'])) {
            return false;
        } else {
            return true;
        }
    }


    public function rTablePageUrl()
    {
        $req = Request::getRequestUri();
        $reqs = Request::url();
        $allQuery = explode('?', $req);
        if (isset($allQuery[1])) {
            $query = explode("&", $allQuery[1]);
            $count = 0;
            foreach ($query as $q) {
                $qr = explode("=", $q);
                if ($qr[0] != 's') {
                    if ($count == 0) {
                        $reqs .= "?";
                        $reqs .= $q;
                        $count = 1;
                    } else {
                        $reqs .= "&" . $q;
                    }
                }
            }
        }
        return $reqs;
    }


    public function requestWithotPage($feature, $item)
    {
        $req = Request::getRequestUri();
        $reqs = Request::url();
        $allQuery = explode('?', $req);
        $count = 0;
        if (isset($allQuery[1])) {
            if (count($allQuery) > 2) {
                foreach ($allQuery as $key => $aq) {
                    if ($key > 1) {
                        $allQuery[1] .= $aq;
                    }
                }
            }
            while (substr($allQuery[1], 0, 1) === "?" || substr($allQuery[1], 0, 1) === "&") {
                $allQuery[1] = substr($allQuery[1], 1);
            }
            while (strpos($allQuery[1], '&&') !== false) {
                $allQuery[1] = Str::replace('&&', '&', $allQuery[1]);
            }
            $query = explode("&", $allQuery[1]);
            $f = MongoFeature::where('slug', $feature)->first();
            $childFeature = $f->children->first();
            foreach ($query as $q) {
                $qr = explode("=", $q);
                if (isset($childFeature)) {
                    if ($qr[0] != 'page' && $qr[0] != $feature && $qr[0] != $childFeature->slug) {
                        if ($count == 0) {
                            $reqs .= "?";
                            $reqs .= $q;
                            $count = 1;
                        } else {
                            $reqs .= "&" . $q;
                        }
                    }
                } else {
                    if ($qr[0] != 'page' && $qr[0] != $feature) {
                        if ($count == 0) {
                            $reqs .= "?";
                            $reqs .= $q;
                            $count = 1;
                        } else {
                            $reqs .= "&" . $q;
                        }
                    }
                }
            }
        }
        if ($count == 0) {
            $reqs .= "?" . $feature . "=" . $item;
        } else {
            $reqs .= "&" . $feature . "=" . $item;
        }
        return $reqs;
    }


    public function commentPageUrl()
    {
        if (count(Request::query()) == 0) {
            return Request::fullUrl() . "?s=1";
        } else {
            if (isset(Request::query()['s'])) {
                return Request::fullUrl();
            } else {
                return Request::fullUrl() . "&s=1";
            }
        }
    }


    public function isRTablePage()
    {
        if (count(Request::query()) == 0) {
            return true;
        } else {
            if (isset(Request::query()['s'])) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function newQuestionUrl($category_slug = null)
    {
        $req = Request::getRequestUri();
        $allQueryarr = explode('?', $req);

        if (isset($allQueryarr[1])) {

            if (count($allQueryarr) > 2) {
                foreach ($allQueryarr as $key => $aq) {
                    if ($key > 1) {
                        $allQueryarr[1] .= $aq;
                    }
                }
            }
            while (substr($allQueryarr[1], 0, 1) === "?" || substr($allQueryarr[1], 0, 1) === "&") {
                $allQueryarr[1] = substr($allQueryarr[1], 1);
            }
            while (
                strpos($allQueryarr[1], '&&') !== false || strpos($allQueryarr[1], '?&') !== false
                || strpos($allQueryarr[1], '&?') !== false || strpos($allQueryarr[1], '??') !== false
            ) {
                $allQueryarr[1] = Str::replace('??', '?', $allQueryarr[1]);
                $allQueryarr[1] = Str::replace('&&', '&', $allQueryarr[1]);
                $allQueryarr[1] = Str::replace('?&', '&', $allQueryarr[1]);
                $allQueryarr[1] = Str::replace('&?', '&', $allQueryarr[1]);
            }

            $allQuery = $allQueryarr[1];
        }
        if (isset($allQuery)) {
            return route('question.create', $category_slug) . "?" . $allQuery;
        } else {
            return route('question.create', $category_slug);
        }
    }
    public function newAdvertiseUrl($category_slug = null)
    {
        $req = Request::getRequestUri();
        $allQueryarr = explode('?', $req);
        if (isset($allQueryarr[1])) {

            if (count($allQueryarr) > 2) {
                foreach ($allQueryarr as $key => $aq) {
                    if ($key > 1) {
                        $allQueryarr[1] .= $aq;
                    }
                }
            }
            while (substr($allQueryarr[1], 0, 1) === "?" || substr($allQueryarr[1], 0, 1) === "&") {
                $allQueryarr[1] = substr($allQueryarr[1], 1);
            }
            while (
                strpos($allQueryarr[1], '&&') !== false || strpos($allQueryarr[1], '?&') !== false
                || strpos($allQueryarr[1], '&?') !== false || strpos($allQueryarr[1], '??') !== false
            ) {
                $allQueryarr[1] = Str::replace('??', '?', $allQueryarr[1]);
                $allQueryarr[1] = Str::replace('&&', '&', $allQueryarr[1]);
                $allQueryarr[1] = Str::replace('?&', '&', $allQueryarr[1]);
                $allQueryarr[1] = Str::replace('&?', '&', $allQueryarr[1]);
            }

            $allQuery = $allQueryarr[1];
        }
        if (isset($allQuery)) {
            return route('new.ad', $category_slug) . "?" . $allQuery;
        } else {
            return route('new.ad', $category_slug);
        }
    }

    private function getReqs($category_slug = null)
    {
        $req = Request::fullUrl();
        $req = explode('?', $req)[0];
        $tokens = explode('/', $req);
        $category_slug = $tokens[sizeof($tokens) - 1];
        $category = MongoCategory::where('slug', $category_slug)->first();

        $req = Request::getRequestUri();
        $reqs = "";
        $allQuery = explode('?', $req);
        if (isset($allQuery[1])) {
            $query = explode("&", $allQuery[1]);
            $count = 0;
            foreach ($query as $q) {
                $qr = explode("=", $q);
                if ($qr[0] != 's' && $qr[0] != 'page') {
                    $feature = $category->features()->where('slug', $qr[0])->first();
                    if (isset($feature) && $feature->is_in_filter_rtable == 1) {
                        if ($count == 0) {
                            $reqs .= "?";
                            $reqs .= $q;
                            $count = 1;
                        } else {
                            $reqs .= "&" . $q;
                        }
                    }
                }
            }
        }
        return $reqs;
    }
    private function getReqsComment($category_slug = null)
    {
        $req = Request::fullUrl();
        $req = explode('?', $req)[0];
        $tokens = explode('/', $req);
        $category_slug = $tokens[sizeof($tokens) - 1];
        $category = MongoCategory::where('slug', $category_slug)->first();

        $req = Request::getRequestUri();
        $reqs = "";
        $allQuery = explode('?', $req);
        if (isset($allQuery[1])) {
            $query = explode("&", $allQuery[1]);
            $count = 0;
            foreach ($query as $q) {
                $qr = explode("=", $q);
                if ($qr[0] != 's' && $qr[0] != 'page') {
                    $feature = $category->features()->where('slug', $qr[0])->first();
                    if (isset($feature) && $feature->is_in_filter_rtable == 1) {
                        if ($count == 0) {
                            $reqs .= $q;
                            $count = 1;
                        } else {
                            $reqs .= "&" . $q;
                        }
                    }
                }
            }
        }
        return $reqs;
    }

    public function questiIndexonUrl($category_slug = null)
    {
        $reqs = $this->getReqs($category_slug);
        if (isset($reqs) && strpos($reqs, "%20") === false) {
            return route('question.index', $category_slug)  . $reqs;
        } else {
            return route('question.index', $category_slug);
        }
    }

    public function CommentIndexUrl($category_slug = null)
    {
        $reqs = $this->getReqsComment($category_slug);
        if (isset($reqs) && strpos($reqs, "%20") === false) {
            return route('question.index', $category_slug) . "?s=1&"  . $reqs;
        } else {
            return route('question.index', $category_slug);
        }
    }

    public function adsIndexonUrl($category_slug = null)
    {

        $req = Request::fullUrl();
        $req = explode('?', $req)[0];
        $tokens = explode('/', $req);
        $category_slug = $tokens[sizeof($tokens) - 1];
        $category = MongoCategory::where('slug', $category_slug)->first();

        $req = Request::getRequestUri();
        $reqs = "";
        $allQuery = explode('?', $req);
        if (isset($category) && isset($allQuery[1])) {
            $query = explode("&", $allQuery[1]);
            $count = 0;
            foreach ($query as $q) {
                $qr = explode("=", $q);
                if ($qr[0] != 's' && $qr[0] != 'page') {
                    $feature = $category->features()->where('slug', $qr[0])->first();
                    if ($feature->is_important_in_ad == 1) {
                        if ($count == 0) {
                            $reqs .= "?";
                            $reqs .= $q;
                            $count = 1;
                        } else {
                            $reqs .= "&" . $q;
                        }
                    }
                }
            }
        }

        if (isset($reqs)) {
            return route('ads.index', $category->slug)  . $reqs;
        } else {
            if (isset($category)) {
                return route('ads.index', $category->slug);
            } else {
                return route('ads.index');
            }
        }
    }

    public function followTitle($followFeature)
    {
        $query = Request::query();
        if (isset($query[$followFeature->slug])) {
            $item = $followFeature->items->where('slug', $query[$followFeature->slug])->first();
            if (isset($item)) {
                return $item->title;
            }
        }
        return "";
    }

    public function followItem($followFeature)
    {
        if (!$followFeature) {
            return null;
        }

        $query = Request::query();
        $allItems = Cache::rememberForever('items', function () {
            return MongoItem::where('status', 1)->get();
        });

        $items = $allItems->where('feature_id', $followFeature->id)
            ->where('slug', $query[$followFeature->slug]);

        if ($items->isEmpty()) {
            return null;
        }

        if ($items->count() === 1) {
            return $items->first();
        }

        $features = Cache::rememberForever('features', function () {
            return MongoFeature::where('status', 1)->get();
        });

        $parentFeature = $features->where('id', $followFeature->parent_id)->first();

        if ($parentFeature && isset($query[$parentFeature->slug])) {
            foreach ($items as $item) {
                $parentItem = $allItems->where('id', $item->parent_id)->first();
                if ($parentItem && $parentItem->slug == $query[$parentFeature->slug]) {
                    return $item;
                }
            }
        }

        return $items->first();
    }



    public static function staticSuggestRtables()
    {
        $questions = MongoQuestion::where('status', 1)->orderBy('created_at', 'desc')->take(5)->get();
        return $questions;
    }
    public function suggestRtables()
    {
        $questions = MongoQuestion::where('status', 1)->orderBy('created_at', 'desc')->take(5)->get();
        return $questions;
    }

    public function suggestAdvertises()
    {
        $advertises = Advertise::where('status', 1)->orderBy('id', 'desc')->get()->take(6);
        return $advertises;
    }
    public static function staticSuggestAdvertises()
    {
        $advertises = MongoAdvertise::where('status', 1)->orderBy('created_at', 'desc')->get()->take(5);
        return $advertises;
    }

    public function getFeaturesInUrl($request)
    {
        $qr = $request->getQueryString();
        $featuresInUrl = explode("&", $qr);
        foreach ($featuresInUrl as $key => $fiu) {
            $ft = explode('=', $fiu)[0];
            if (!isset($ft) || $ft == 's' || $ft == 'page') {
                unset($featuresInUrl[$key]);
            }
        }
        return $featuresInUrl;
    }

    public function getChildFeaturesInUrl($request)
    {
        $allFeatures = Cache::rememberForever('features', function () {
            return MongoFeature::where('status', 1)->get();
        });
        $features = collect();
        foreach ($this->getFeaturesInUrl($request) as $feature) {
            $fs = explode('=', $feature)[0];
            $fea = $allFeatures->where('slug', $fs)->first();
            if (isset($fea)) {
                $features->add($fea);
            }
        }
        $childFeatures = $features;
        if (count($childFeatures) > 1) {
            foreach ($features as $feature) {
                if (isset($feature->parent_id)) {
                    $this->removeParentFeatures($childFeatures, $feature);
                }
            }
        }
        return $childFeatures;
    }

    private function removeParentFeatures($childFeatures, $feature)
    {
        foreach ($childFeatures as $key => $f1) {
            foreach ($feature->parents() as $f2) {
                if ($f1->id == $f2->id) {
                    $childFeatures->forget($key);
                }
            }
        }
    }
    public function getCurrentUrlWithoutPage($request)
    {
        $reqs = $request->url();
        $req = $request->getRequestUri();
        $allQueryarr = explode('?', $req);
        if (isset($allQueryarr[1])) {
            $query = explode("&", $allQueryarr[1]);
            $count = 0;
            foreach ($query as $q) {
                $qr = explode("=", $q);
                if ($qr[0] != 'page') {
                    if ($count == 0) {
                        $reqs .= "?";
                        $reqs .= $q;
                        $count = 1;
                    } else {
                        $reqs .= "&" . $q;
                    }
                }
            }
        }
        return $reqs;
    }
}
