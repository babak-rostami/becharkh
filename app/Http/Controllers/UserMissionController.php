<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use App\Models\UserMission;
use App\Models\UserMoney;
use App\Models\UserScore;
use Illuminate\Http\Request;

class UserMissionController extends Controller
{

    public function missionCompleted($mission_id)
    {
        $mission = Mission::find($mission_id);
        $user = auth('user')->user();
        $userMissionsDone = $user->missions_done ?? [];
        if ($user->checkMission($mission_id) && !$user->missionRewardReceived($mission_id)) {
            $userMissionsDone[] = $mission->id;
            foreach ($mission->rewards as $r) {
                if ($r->title == "m") {
                    $user->money += $r->amount * 1000;
                    $user->missions_done = $userMissionsDone;
                    $user->update();
                }
            }
            return back()->with('success', 'تبریک جوایز با موفقیت دریافت شد');
        } else {
            return back()->with('success', 'جوایز قبلا دریافت شده است');
        }
    }
}
