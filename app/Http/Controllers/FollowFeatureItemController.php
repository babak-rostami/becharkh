<?php

namespace App\Http\Controllers;

use App\Models\MongoFollowItem;
use App\Models\MongoItem;
use Illuminate\Http\Request;

class FollowFeatureItemController extends Controller
{


    public function follow(Request $request)
    {
        $item_id = $request->item_id;
        $user = auth('user')->user();
        $item = MongoItem::find($item_id);
        if (isset($item)) {
            $follow = MongoFollowItem::where('item_id', $item->id)->where('user_id', $user->id)->first();
            $item_follow_count = $item->follow_count ?? 0;
            if (isset($follow)) {
                $follow->delete();
                $item->follow_count = $item_follow_count - 1;
                $item->update();
                return response()->json(['follow' => 0], 200);
            } else {
                $ffi = new MongoFollowItem();
                $ffi->item_id = $item->id;
                $ffi->user_id = $user->id;
                $ffi->save();
                $item->follow_count = $item_follow_count + 1;
                $item->update();
                return response()->json(['follow' => 1], 200);
            }
        } else {
            return response()->json(['error' => 1], 404);
        }
    }
}
