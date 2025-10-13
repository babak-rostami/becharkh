<?php

namespace App\Jobs;

use App\Models\ItemForTopUser;
use App\Models\MongoCategoryComment;
use App\Models\MongoItem;
use App\Models\MongoUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ItemTopUsersJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $item_for_top_users = ItemForTopUser::all();

        foreach ($item_for_top_users as $item_for_top_user) {
            $item = MongoItem::find($item_for_top_user->item_id);

            if (!$item) {
                continue;
            }

            $comments = MongoCategoryComment::orderBy('created_at', 'desc')
                ->where('items', $item->id)
                ->take(100)
                ->get();

            $userScores = [];

            foreach ($comments as $comment) {
                $userId = $comment->user_id;
                $likeCount = $comment->like_count ?? 0;

                if (!isset($userScores[$userId])) {
                    $userScores[$userId] = [
                        'user_id' => $userId,
                        'comments' => 0,
                        'likes' => 0,
                        'score' => 0,
                    ];
                }

                $userScores[$userId]['comments']++;
                $userScores[$userId]['likes'] += $likeCount;
                $userScores[$userId]['score'] = ($userScores[$userId]['comments'] * 10) + $userScores[$userId]['likes'];
            }

            $sortedUsers = collect($userScores)
                ->sortByDesc('score')
                ->values()
                ->all();

            $topUsers = array_filter(array_slice($sortedUsers, 0, 10), function ($user) {
                return $user['score'] > 0;
            });


            $top_users = [];

            foreach ($topUsers as $topUser) {
                // if ($topUser['comments'] > 5) {
                $user = MongoUser::find($topUser['user_id']);
                if (!$user) {
                    continue;
                }
                $user_img = $user->thumb();
                if ($user_img == 'https://dl.becharkh.com/user_files/files/other/images/profile.png') {
                    $user_has_img = 0;
                } else {
                    $user_has_img = 1;
                }
                $object = [
                    'username' => $user->username,
                    'user_img' => $user_img,
                    'user_has_img' => $user_has_img,
                    'user_dash' => str_replace('http://localhost', 'https://becharkh.com', route('user.dashboard', $user->username)),
                    'score' => $topUser['score'],
                ];
                $top_users[] = $object;
                // }
            }
            $item->top_users = $top_users;
            $item->update();

            $item_for_top_user->delete();
        }
    }
}
