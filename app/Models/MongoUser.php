<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;

class MongoUser extends Model implements AuthenticatableContract
{

    // user update 1 = name    2 = phone    3 = body    4 = image

    use Authenticatable;

    protected $connection = 'mongodb';
    protected $table = 'users';
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'image',
        'body',
        'works',
        'unread_message_count',
        'active_code',
        'email_actived',
        'remember_token'
    ];
    protected $hidden = [
        'password'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'likes_count_updated_at',
    ];

    public function questions()
    {
        return $this->hasMany(MongoQuestion::class, 'user_id')->orderBy('created_at', 'desc');
    }

    public function blogs()
    {
        return $this->hasMany(MongoBlog::class, 'user_id')->orderBy('created_at', 'desc');
    }

    public function advertises()
    {
        return $this->hasMany(MongoAdvertise::class, 'user_id')->orderBy('created_at', 'desc');
    }

    public function canCreateAd()
    {
        if ($this->money >= Config::get('gvars.ad_price')) {
            return 1;
        }
        return 0;
    }

    public function canRocketAd()
    {
        if ($this->money >= Config::get('gvars.ad_rocket')) {
            return 1;
        }
        return 0;
    }

    public function decreaseMoneyFor($type)
    {
        $user = $this;
        $money = $user->money;
        if ($type == "advertise") {
            $user->money = $money - Config::get('gvars.ad_price');
            $user->update();
        } elseif ($type == "rocket-advertise") {
            $user->money = $money - Config::get('gvars.ad_rocket');
            $user->update();
        }
    }

    public function decreaseMoneyAmount($amount)
    {
        $user = $this;
        $user->money -= $amount;
        $user->update();
    }

    public function setUserCookies()
    {
        $cookies = [];
        if (!Cookie::has('posts_count')) {
            array_push($cookies, $this->setPostsCountCookie());
        }
        if (!Cookie::has('ads_count')) {
            array_push($cookies, $this->setAdsCountCookie());
        }
        if (!Cookie::has('question_count')) {
            array_push($cookies, $this->setQsCountCookie());
        }
        if (!Cookie::has('videos_count')) {
            array_push($cookies, $this->setVidsCountCookie());
        }
        return $cookies;
    }
    public function setPostsCountCookie()
    {
        $cookieName = 'posts_count';
        $postsCount = count($this->blogs);
        $cookie = cookie()->forever($cookieName, $postsCount, false);
        return $cookie;
    }
    public function setAdsCountCookie()
    {
        $cookieName = 'ads_count';
        $adsCount = count($this->advertises);
        $cookie = cookie()->forever($cookieName, $adsCount, false);
        return $cookie;
    }
    public function setQsCountCookie()
    {
        $cookieName = 'question_count';
        $questionsCount = count($this->questions);
        $cookie = cookie()->forever($cookieName, $questionsCount, false);
        return $cookie;
    }
    public function setVidsCountCookie()
    {
        $cookieName = 'videos_count';
        $videosCount = count($this->videos);
        $cookie = cookie()->forever($cookieName, $videosCount);
        return $cookie;
    }

    public function getPostsCount()
    {
        $this->setUserCookies();
        $cookieName = 'posts_count';
        $postsCount = 0;
        // Check if the cookie exists
        if (Cookie::has($cookieName)) {
            // Retrieve the posts count from the cookie
            $postsCount = Cookie::get($cookieName);
        }
        return $postsCount;
    }
    public function getAdsCount()
    {
        $cookieName = 'ads_count';
        $adsCount = 0;
        if (Cookie::has($cookieName)) {
            $adsCount = Cookie::get($cookieName);
        }
        return $adsCount;
    }
    public function getQsCount()
    {
        $cookieName = 'question_count';
        $questionsCount = 0;
        if (Cookie::has($cookieName)) {
            $questionsCount = Cookie::get($cookieName);
        }
        return $questionsCount;
    }
    public function getVidsCount()
    {
        $cookieName = 'videos_count';
        $videosCount = 0;
        if (Cookie::has($cookieName)) {
            $videosCount = Cookie::get($cookieName);
        }
        return $videosCount;
    }

    public function videos()
    {
        return $this->hasMany(MongoVideo::class, 'user_id')->orderBy('created_at', 'desc');
    }

    public function blogVideos()
    {
        $bvs = $this->videos->where('videos', '!=', null);
        return $bvs;
    }

    public function adVideos()
    {
        $bvs = $this->advertises->where('videos', '!=', null);
        return $bvs;
    }

    public function jobs()
    {
        if (!isset($this->works)) {
            return collect();
        }
        return MongoWork::whereIn('id', $this->works)->get();
    }

    public function getImage()
    {
        return $this->attributes['image'] ?? null;
    }

    public function image()
    {
        if (isset($this->attributes['image'])) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $this->attributes['image'];
        } else {
            return 'https://dl.becharkh.com/user_files/files/other/images/profile.png';
        }
    }

    public function thumb()
    {
        if (isset($this->attributes['image'])) {
            $thumb = explode('.webp', $this->attributes['image'])[0] . '2.webp';
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $thumb;
        } else {
            return 'https://dl.becharkh.com/user_files/files/other/images/profile.png';
        }
    }

    public function chats()
    {
        $chats = MongoConversation::where('user_1', $this->id)->orWhere('user_2', $this->id)->get();
        return $chats;
    }

    public function chatsWithUser()
    {
        $chats = MongoConversation::with('user2')->where('user_1', $this->id)->orWhere('user_2', $this->id)->get();
        return $chats;
    }

    public function chatIdWithUser($user_id_2)
    {
        $user = $this;
        $user_chats = $user->chats();
        $chat = $user_chats->where('user_1', $user_id_2)->where('user_2', $user_id_2)->first();
        if (isset($chat)) {
            return $chat->id;
        }
        return null;
    }

    public function followings()
    {
        return $this->hasMany(MongoUserFollow::class, 'user_1');
    }

    public function isFollow($user_id)
    {
        $isFollow = $this->followings->where('user_2', $user_id)->first();
        if (isset($isFollow)) {
            return true;
        }
        return false;
    }

    public function ccomMedals()
    {
        $medals = $this->medals ?? [];
        $titles = [];
        foreach ($medals as $medal) {
            $titles[] = explode('---', $medal)[1];
        }
        return $titles;
    }

    public function myNotifications()
    {
        return $this->hasMany(UserNotification::class, 'user_id');
    }
}
