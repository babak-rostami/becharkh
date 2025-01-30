<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cookie;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $connection = 'mysql';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

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

    public function userJobs()
    {
        return $this->hasMany(UserWork::class, 'user_id');
    }
    public function jobs()
    {
        return $this->belongsToMany(Work::class, UserWork::class, 'user_id', 'work_id');
    }

    public function isLikeBlog($id)
    {
        $bl = BlogLike::where('blog_id', $id)->where('like_or_unlike', 1)->where('user_id', $this->id)->first();
        if (isset($bl)) {
            return true;
        } else {
            return false;
        }
    }
    public function isUnLikeBlog($id)
    {
        $bl = BlogLike::where('blog_id', $id)->where('like_or_unlike', 0)->where('user_id', $this->id)->first();
        if (isset($bl)) {
            return true;
        } else {
            return false;
        }
    }
    public function isLikeVideo($id)
    {
        $vl = VideoLike::where('video_id', $id)->where('like_or_unlike', 1)->where('user_id', $this->id)->first();
        if (isset($vl)) {
            return true;
        } else {
            return false;
        }
    }
    public function isUnLikeVideo($id)
    {
        $vl = VideoLike::where('video_id', $id)->where('like_or_unlike', 0)->where('user_id', $this->id)->first();
        if (isset($vl)) {
            return true;
        } else {
            return false;
        }
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'user_id')->orderBy('id', 'desc');
    }

    public function advertises()
    {
        return $this->hasMany(Advertise::class, 'user_id')->orderBy('created_at', 'desc');
    }


    public function image()
    {
        if ($this->image != null) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $this->image;
        } else {
            return 'files/other/images/profile.png';
        }
    }

    public function thumb()
    {
        if ($this->image != null) {
            $path = "https://dl.becharkh.com/user_files/";
            return $path . $this->thum;
        } else {
            return 'files/other/images/profile.png';
        }
    }

    public function userCkImages()
    {
        return $this->hasMany(UserCkImage::class, 'user_id');
    }
    public function userCkImagesDontSave()
    {
        return $this->hasMany(UserCkImage::class, 'user_id')->where('blog_id', null);
    }
    public function userBlogCkImages($blog)
    {
        return $this->hasMany(UserCkImage::class, 'user_id')->where('blog_id', $blog->id)->orWhere('blog_id', null)->get();
    }

    public function userQuestionCkImages()
    {
        return $this->hasMany(UserQuestionCkImage::class, 'user_id');
    }
    public function userQuestionCkImagesDontSave()
    {
        return $this->hasMany(UserQuestionCkImage::class, 'user_id')->where('question_id', null);
    }
    public function userFindQuestionCkImages($question)
    {
        return $this->hasMany(UserQuestionCkImage::class, 'user_id')->where('question_id', $question->id)->orWhere('question_id', null)->get();
    }

    public function likes()
    {
        return $this->belongsToMany(Question::class, 'question_likes', 'user_id', 'question_id');
    }

    public function isLike($question_id)
    {
        foreach ($this->likes as $like) {
            if ($like->id == $question_id) {
                return true;
            }
        }
        return false;
    }

    public function questionAnswers()
    {
        return $this->hasMany(QuestionAnswer::class, 'user_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'user_id')->orderBy('created_at', 'desc');
    }


    public function orders()
    {
        return $this->hasMany(UserOrder::class, 'user_id')->orderBy('updated_at', 'desc');
    }

    public function followings()
    {
        return $this->hasMany(Follow::class, 'user_1')->where('status', 1);
    }

    public function isFollow($user_id)
    {
        foreach ($this->followings() as $following) {
            if ($following->user_2 == $user_id) {
                return true;
            }
        }
        return false;
    }

    public function followers()
    {
        return $this->hasMany(Follow::class, 'user_2')->where('status', 1);
    }


    public function userMessageId(User $user)
    {
        foreach ($this->chats() as $chat) {
            if ($chat->user_1 == $user->id || $chat->user_2 == $user->id) {
                return $chat->id;
            }
        }
        $ch = new Chat();
        $ch->user_1 = auth('user')->id();
        $ch->user_2 = $user->id;
        $ch->save();
        return $ch->id;
    }

    public function chats()
    {
        $chats = Chat::where('user_1', $this->id)->orWhere('user_2', $this->id)->get();
        return $chats;
    }

    public function getMessages()
    {
        return $this->hasMany(UserMessage::class, 'to_user')->orderBy('id', 'desc');
    }
    public function sends()
    {
        return $this->hasMany(UserMessage::class, 'from_user')->orderBy('id', 'desc');
    }

    public function allMessage()
    {
        $chats = Chat::where('user_1', auth('user')->id())->orWhere('user_2', auth('user')->id())->orderby('updated_at', 'desc')->get();
        return $chats;
    }

    public function unReadMessages()
    {
        $count = 0;
        foreach ($this->allMessage() as $m) {
            if ($m->youHaveUnreadMessage()) {
                $count += 1;
            }
        }
        return $count;
    }


    public function adImageCount()
    {
        return 8;
    }

    public function decreaseDiamondAmount($amount)
    {
        $userDimond = $this->diamond;
        $userDimond->amount -= $amount;
        $userDimond->update();
    }

    public function decreaseDiamond($type)
    {
        $userDimond = $this->diamond;
        if ($type == "ad") {
            if (isset($userDimond)) {
                $userDimond->amount -= 10;
                $userDimond->update();
            }
        } elseif ($type == "rocket-ad") {
            if (isset($userDimond)) {
                $userDimond->amount -= 5;
                $userDimond->update();
            }
        }
    }

    public function featureItemFollows()
    {
        return $this->hasMany(FollowFeatureItem::class, 'user_id');
    }

    public function isFollowFeatureItem($feature_id, $item_id)
    {
        $follow = $this->featureItemFollows->where('feature_id', $feature_id)->where('item_id', $item_id)->first();
        if (isset($follow)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function itemInterests()
    {
        return $this->hasMany(UserInterestItem::class, 'user_id')->orderBy('seen_count', 'desc');
    }

    public function addItemInteres($category, $feature, $item, $page)
    {
        $user = $this;
        $interests = $this->itemInterests;
        $isupdateOrCreated = 0;

        if ($this->itemInterests->count() == 0) {
            $inter = new UserInterestItem();
            $inter->user_id = $user->id;
            $inter->category_id = $category->id;
            $inter->feature_id = $feature->id;
            $inter->item_id = $item->id;
            $inter->seen_count = 1;
            if ($page == "advertise") {
                $inter->ad_page_seen_count = 1;
            } elseif ($page == "rtable") {
                $inter->rtable_page_seen_count = 1;
            } elseif ($page == "comment") {
                $inter->ccomment_page_seen_count = 1;
            }
            $inter->save();
            $isupdateOrCreated = 1;
        } else {
            foreach ($interests as $key => $interest) {
                $count = $key + 1;
                if ($count < 15) {
                    if ($interest->item_id == $item->id && $interest->updated_at > Carbon::now()->subHours(1)) {
                        $interest->seen_count += 1;
                        if ($page == "advertise") {
                            $interest->ad_page_seen_count += 1;
                        } elseif ($page == "rtable") {
                            $interest->rtable_page_seen_count += 1;
                        } elseif ($page == "comment") {
                            $interest->ccomment_page_seen_count += 1;
                        }
                        $interest->update();
                        $isupdateOrCreated = 1;
                    }
                    //if item is not in your first 15 interested list
                } elseif ($count == 15) {
                    if ($interest->item_id == $item->id) {
                        $interest->seen_count += 1;
                        if ($page == "advertise") {
                            $interest->ad_page_seen_count += 1;
                        } elseif ($page == "rtable") {
                            $interest->rtable_page_seen_count += 1;
                        } elseif ($page == "comment") {
                            $interest->ccomment_page_seen_count += 1;
                        }
                        $interest->update();
                        $isupdateOrCreated = 1;
                    } else {
                        $interest->category_id = $category->id;
                        $interest->feature_id = $feature->id;
                        $interest->item_id = $item->id;
                        $interest->seen_count = 1;
                        if ($page == "advertise") {
                            $interest->ad_page_seen_count = 1;
                            $interest->rtable_page_seen_count = 0;
                            $interest->ccomment_page_seen_count = 0;
                        } elseif ($page == "rtable") {
                            $interest->ad_page_seen_count = 0;
                            $interest->rtable_page_seen_count = 1;
                            $interest->ccomment_page_seen_count = 0;
                        } elseif ($page == "comment") {
                            $interest->ccomment_page_seen_count = 1;
                            $interest->ad_page_seen_count = 0;
                            $interest->rtable_page_seen_count = 0;
                        }
                        $interest->update();
                        $isupdateOrCreated = 1;
                    }
                }
            }
        }
        if ($isupdateOrCreated == 0) {
            $inter = new UserInterestItem();
            $inter->user_id = $user->id;
            $inter->category_id = $category->id;
            $inter->feature_id = $feature->id;
            $inter->item_id = $item->id;
            $inter->seen_count = 1;
            if ($page == "advertise") {
                $inter->ad_page_seen_count = 1;
            } elseif ($page == "rtable") {
                $inter->rtable_page_seen_count = 1;
            } elseif ($page == "comment") {
                $inter->ccomment_page_seen_count = 1;
            }
            $inter->save();
        }
    }

    public function missions()
    {
        return $this->hasMany(UserMission::class, 'user_id');
    }


    public function checkMission($mission)
    {
        $userMission = $this->missions->where('mission_id', $mission->id)->first();
        if (isset($userMission)) {
            return $userMission->done_count;
        } else {
            return 0;
        }
    }
    public function missionRewardReceived($mission)
    {
        $userMission = $this->missions->where('mission_id', $mission->id)->first();
        if (isset($userMission) && $userMission->get_reward) {
            return true;
        } else {
            return false;
        }
    }

    public function missionsDone()
    {
        $count = 0;
        $missions = Mission::whereDate('expire_time', '>', now())->get();
        foreach ($missions as $mission) {
            if ($this->checkMission($mission) == $mission->done_count) {
                $count += 1;
            }
        }
        if ($count == $missions->count()) {
            return true;
        } else {
            return false;
        }
    }


    public function diamond()
    {
        return $this->hasOne(UserMoney::class, 'user_id');
    }

    public function diamondAmount()
    {
        $diamond = $this->diamond;
        if (isset($diamond)) {
            return $diamond->amount;
        } else {
            return 0;
        }
    }

    public function userMoney()
    {
        $dAmount = $this->diamondAmount();
        if ($dAmount == 0) {
            return 0;
        } else {
            return $dAmount * 1000;
        }
    }

    public function canCreateAd()
    {
        $userDim = $this->diamond;
        if (isset($userDim) && $userDim->amount >= 10) {
            return 1;
        }
        return 0;
    }
    public function canRocketAd()
    {
        $userDim = $this->diamond;
        if (isset($userDim) && $userDim->amount >= 5) {
            return 1;
        }
        return 0;
    }


    public function ranks()
    {
        return $this->hasMany(Ranking::class, 'user_id');
    }

    public function getPoint($point, $category_id = null, $item_id = null)
    {
        if ($item_id != null) {
            $this->getWithParentPoint($point, $item_id);
        }
        if ($category_id != null) {
            $itemRank = $this->ranks->where('category_id', $category_id)->first();
            if (isset($itemRank)) {
                $itemRank->score += $point;
                $itemRank->update();
            } else {
                $itemRank = new Ranking();
                $itemRank->category_id = $category_id;
                $itemRank->user_id = $this->id;
                $itemRank->score = $point;
                $itemRank->save();
            }
        }
    }

    private function getWithParentPoint($point, $item_id)
    {
        $item = CategoryFeatureItem::find($item_id);
        if ($item->parent_id != null) {
            $this->getWithParentPoint($point, $item->parent_id);
        }
        $itemRank = $this->ranks->where('item_id', $item_id)->first();
        if (isset($itemRank)) {
            $itemRank->score += $point;
            $itemRank->update();
        } else {
            $item = CategoryFeatureItem::find($item_id);
            $itemRank = new Ranking();
            $itemRank->category_id = $item->category_id;
            $itemRank->feature_id = $item->feature_id;
            $itemRank->item_id = $item_id;
            $itemRank->user_id = $this->id;
            $itemRank->score = $point;
            $itemRank->save();
        }
    }

    public function activation()
    {
        return $this->hasOne(UserActivation::class, 'user_id');
    }


    public function videos()
    {
        return $this->hasMany(Video::class, 'user_id')->orderBy('id', 'desc');
    }

    public function blogVideos()
    {
        return $this->hasMany(BlogVideo2::class, 'user_id')->orderBy('id', 'desc');
    }
    public function adVideos()
    {
        return $this->hasMany(AdvertiseVideo::class, 'user_id')->orderBy('id', 'desc');
    }
}
