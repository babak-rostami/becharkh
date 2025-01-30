<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\MongoUser;
use App\Models\MongoUserFollow;
use App\Models\User;
use App\Notifications\UserNotif;
use Illuminate\Http\Request;

class FollowController extends Controller
{

    public function follow(Request $request)
    {
        if (!auth('user')->check()) {
            return back()->with('success', 'ابتدا وارد حساب کاربری خود شوید');
        }
        $user = auth('user')->user();
        $user_1 = $user->id;
        $user2 = MongoUser::find($request->user_2);
        if (isset($user2)) {
            $follow = new MongoUserFollow();
            $follow->user_1 = $user_1;
            $follow->user_2 = $user2->id;
            $follow->save();
            return back()->with('success', $user2->username . ' با موفقیت دنبال شد');
        }
        return back()->with('success', 'کاربر پیدا نشد');
    }

    public function unfollow(Request $request)
    {
        if (!auth('user')->check()) {
            return back()->with('success', 'ابتدا وارد حساب کاربری خود شوید');
        }
        $user_1 = auth('user')->user();
        $isFollow = $user_1->followings->where('user_2', $request->user_2)->first();
        if (isset($isFollow)) {
            $isFollow->delete();
        }
        return back()->with('success', 'کاربر از لیست دوستان حذف شد');
    }
}
