<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AllViewComposer
{
    public function compose(View $view)
    {
        $ip = request()->ip();
        $user_online_cache_key = "online-users";

        $online_users = Cache::remember($user_online_cache_key, 3600, function () {
            return [];
        });

        $online_users = collect($online_users)->filter(function ($user) {
            return now()->timestamp - $user['added_at'] < 3600;
        });

        if (!$online_users->contains('ip', $ip)) {
            $online_users->push([
                'ip' => $ip,
                'added_at' => now()->timestamp,
            ]);
            Cache::put($user_online_cache_key, $online_users->all(), 3600);
        }

        $online_user_count = $online_users->count();

        $user = auth('user')->user();
        $ftp_path = 'https://dl.becharkh.com/user_files/';
        $ad_price = 20000;
        $ad_rocket = 10000;
        $view->with([
            'ftp_path' => $ftp_path,
            'user' => $user,
            'ad_price' => $ad_price,
            'ad_rocket' => $ad_rocket,
            'online_user_count' => $online_user_count
        ]);
    }
}
