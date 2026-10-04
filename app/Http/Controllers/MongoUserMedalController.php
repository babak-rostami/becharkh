<?php

namespace App\Http\Controllers;

use App\Models\MongoUser;
use App\Models\MongoUserMedal;
use Illuminate\Http\Request;

class MongoUserMedalController extends Controller
{

    public function getItemTopUsers($item_id)
    {
        $user_ids = MongoUserMedal::where('type', 'ccomment')->where('item_id', $item_id)->orderBy('count', 'desc')->pluck('user_id');
        return MongoUser::whereIn('_id', $user_ids)->select('username', 'image')->get();
    }
}
