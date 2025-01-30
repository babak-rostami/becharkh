<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use App\Models\MissionReward;
use Carbon\Carbon;

class MissionController extends Controller
{

    public function index()
    {
        $misssions = Mission::whereDate('expire_time', '>', now())->get();
        if ($misssions->isEmpty()) {
            $this->createMissions();
            $misssions = Mission::whereDate('expire_time', '>', now())->get();
        }
        $mission = $misssions->last();
        $user = auth('user')->user();

        return view('mission.index', compact('misssions', 'mission'));
    }

    private function createMissions()
    {
        $rtable_comment_mission = new Mission();
        $c_comment_mission = new Mission();

        $rtable_comment_mission->type = 1;
        $rtable_comment_mission->priority = 1;
        $rtable_comment_mission->expire_time = Carbon::now()->addDays(7);
        $rtable_comment_mission->save();
        $r1 = new MissionReward();
        $r1->title = "m"; //score
        $r1->amount = 5;
        $r1->mission_id = $rtable_comment_mission->id;
        $r1->save();

        $c_comment_mission->type = 2;
        $c_comment_mission->priority = 2;
        $c_comment_mission->expire_time = Carbon::now()->addDays(7);
        $c_comment_mission->save();
        $r2 = new MissionReward();
        $r2->title = "m"; //money
        $r2->amount = 5;
        $r2->mission_id = $c_comment_mission->id;
        $r2->save();
    }

    // $ad_comment_mission = new Mission();
    // $blog_comment_mission = new Mission();
    // $follow_mission = new Mission();
    // $race_mission = new Mission();
    // $be_fan_mission = new Mission();


    // $rtable = Question::whereHas('featureValuesHasItem')->withCount('wganswers')->orderBy('wganswers_count', 'asc')->take(10)->get()->random();
    // $rFV = $rtable->featureValuesHasItem->last();
    // $rtable_comment_mission->category_id = $rFV->feature->category_id;
    // $rtable_comment_mission->feature_id = $rFV->feature_id;
    // $rtable_comment_mission->item_id = $rFV->item_id;


    // $ccomment = CategoryComment::whereHas('featureValuesHasItem')->withCount('replies')->orderBy('replies_count', 'asc')->take(10)->get()->random();
    // $cFV = $ccomment->featureValuesHasItem->last();
    // $c_comment_mission->category_id = $cFV->feature->category_id;
    // $c_comment_mission->feature_id = $cFV->feature_id;
    // $c_comment_mission->item_id = $cFV->item_id;

    // $follow_mission->type = 5;
    // $follow_mission->priority = 5;
    // $follow_mission->done_count = 5;
    // $follow_mission->expire_time = Carbon::now()->addDays(7);
    // $follow_mission->save();
    // $r5 = new MissionReward();
    // $r5->title = "s"; //score
    // $r5->amount = 1;
    // $r5->mission_id = $follow_mission->id;
    // $r5->save();

    // $race_mission->type = 6;
    // $race_mission->priority = 6;
    // $race_mission->done_count = 3;
    // $race_mission->expire_time = Carbon::now()->addDays(7);
    // $race_mission->save();
    // $r6 = new MissionReward();
    // $r6->title = "s"; //score
    // $r6->amount = 1;
    // $r6->mission_id = $race_mission->id;
    // $r6->save();

    // $be_fan_mission->type = 7;
    // $be_fan_mission->priority = 7;
    // $be_fan_mission->expire_time = Carbon::now()->addDays(7);
    // $be_fan_mission->save();
    // $r7 = new MissionReward();
    // $r7->title = "s"; //score
    // $r7->amount = 2;
    // $r7->mission_id = $be_fan_mission->id;
    // $r7->save();
    // $r8 = new MissionReward();
    // $r8->title = "m"; //money
    // $r8->amount = 2;
    // $r8->mission_id = $be_fan_mission->id;
    // $r8->save();
}
