<?php

namespace App\Jobs;

use App\Models\MongoItem;
use App\Models\MongoUser;
use App\Models\MongoUserMedal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class AddUserCategoryCommentMedal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $item_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($item_id)
    {
        $this->item_id = $item_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $item_id = $this->item_id;
        $item = MongoItem::find($item_id);
        $ccom_mdl_cache_key = 'add_ccom_medal' . $item_id;
        $cache_ccom_medal_value = Cache::get($ccom_mdl_cache_key);
        if ($cache_ccom_medal_value) {
            $cvalue_explode = explode('-', $cache_ccom_medal_value);
            $user_id = $cvalue_explode[0];
            $like_count = (int)$cvalue_explode[1];
            $top_medals = MongoUserMedal::where('type', 'ccomment')->where('item_id', $item_id)->orderBy('count', 'desc')->get();
            if ($top_medals->isEmpty()) {
                $new_user = MongoUser::find($user_id);
                $new_user_medals = $new_user->medals ?? [];
                $new_top_medal = new MongoUserMedal();
                $new_top_medal->type = 'ccomment';
                $new_top_medal->item_id = $item_id;
                $new_top_medal->user_id = $user_id;
                $new_top_medal->count = $like_count;
                $new_top_medal->save();
                $item_title = $item->full_title ?? $item->title;
                $new_user_medals[] = 'ccomment-' . $item->id . '---' . $item_title;
                $new_user->medals = $new_user_medals;
                $new_user->update();
            } else {
                $last_top_medal = $top_medals->last();
                $user_medal = $top_medals->where('user_id', $user_id)->first();

                if ($user_medal) {
                    if ($like_count > $user_medal->count) {
                        $user_medal->count = $like_count;
                        $user_medal->update();
                        if (count($top_medals) == 3 && $last_top_medal->id == $user_medal->id) {
                            $item->top3_like_count = $like_count;
                            $item->update();
                        }
                    }
                } else {
                    $new_user = MongoUser::find($user_id);
                    $new_user_medals = $new_user->medals ?? [];
                    if (count($top_medals) < 3) {
                        $new_top_medal = new MongoUserMedal();
                        $new_top_medal->type = 'ccomment';
                        $new_top_medal->item_id = $item_id;
                        $new_top_medal->user_id = $user_id;
                        $new_top_medal->count = $like_count;
                        $new_top_medal->save();
                        $item_title = $item->full_title ?? $item->title;
                        $new_user_medals[] = 'ccomment-' . $item->id . '---' . $item_title;
                        $new_user->medals = $new_user_medals;
                        $new_user->update();
                    } else {
                        $new_top_medal = $top_medals->last();
                        $last_user = MongoUser::find($new_top_medal->user_id);
                        $last_user_medals = $last_user->medals ?? [];
                        // توی مدالای کاربر مدالی که مربوط به این آیتمه پیدا میکنه
                        $medals_to_remove = array_filter($last_user_medals, function ($medal) use ($item_id) {
                            return strpos($medal, 'ccomment-' . $item_id) === 0;
                        });
                        // توی مدالای کاربر مدال این آیتم رو پاک میکنه
                        $last_user_medals = array_diff($last_user_medals, $medals_to_remove);
                        if (count($last_user_medals) > 0) {
                            $last_user->unset('medals');
                        } else {
                            $last_user->medals = $last_user_medals;
                            $last_user->update();
                        }

                        if (isset($medals_to_remove[0])) {
                            $new_user_medals[] = $medals_to_remove[0];
                        } else {
                            $item_title = $item->full_title ?? $item->title;
                            $new_user_medals[] = 'ccomment-' . $item->id . '---' . $item_title;;
                        }
                        $new_user->medals = $new_user_medals;
                        $new_user->update();

                        $new_top_medal->count = $like_count;
                        $new_top_medal->user_id = $user_id;
                        $new_top_medal->update();

                        $item->top3_like_count = $like_count;
                        $item->update();
                    }
                }
            }
            Cache::forget($ccom_mdl_cache_key);
        }
    }
}
