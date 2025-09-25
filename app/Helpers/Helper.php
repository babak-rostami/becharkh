<?php

if (!function_exists('admin_notification_count')) {
    function admin_notification_count()
    {
        if (auth('admin')->check()) {
            $notifications = auth('admin')->user()->notifications;
            return $notifications->count();
        } else {
            return 0;
        }
    }
}

if (!function_exists('urlForSuggest')) {
    function urlForSuggest($page, $suggestCat, $category, $features, $currentQueryParams)
    {
        $url = null;
        if ($page == 'advertise') {
            $url = route('ads.index', $suggestCat->slug);
        } elseif ($page == 'comment') {
            $url = route('question.index', $suggestCat->slug) . '?s=1';
        } elseif ($page == 'forum') {
            $url = route('question.index', $suggestCat->slug);
        } elseif ($page == 'blog-index') {
            $url = route('blog.index', $suggestCat->slug);
        } elseif ($page == 'show_blog') {
            $url = route('question.index', $suggestCat->slug) . '?s=1';
        } elseif ($page == 'show_question') {
            $url = route('question.index', $suggestCat->slug);
        } elseif ($page == 'show_advertise') {
            $url = route('ads.index', $suggestCat->slug);
        }
        if (isset($category)) {
            $queryParams = [];
            if (!$features) {
                $features = [];
            }
            foreach ($features as $feature)
                if (isset($suggestCat->feature_ids) && in_array($feature->id, $suggestCat->feature_ids)) {
                    if (array_key_exists($feature->slug, $currentQueryParams)) {
                        $queryParams[$feature->slug] = $currentQueryParams[$feature->slug];
                    }
                }
            $queryString = http_build_query($queryParams);
            $url = $url . ($queryString ? '?' . $queryString : '');
        }
        return $url;
    }
}
