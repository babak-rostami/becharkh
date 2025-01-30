<?php

namespace App\Jobs;

use App\Models\Mission;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MissionComplete implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;
    private $mission_type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $mission_type)
    {
        $this->user = $user;
        $this->mission_type = $mission_type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */

    public function handle()
    {
        $user = $this->user;
        $activeMissions = Mission::whereDate('expire_time', '>', Carbon::now())->get();
        if (count($activeMissions)) {
            $mission = $activeMissions->where('type', $this->mission_type)->first();
            if ($user->checkMission($mission->id)) {
                return;
            }
            $userMissions = $user->missions ?? [];
            $userMissionsDone = $user->missions_done ?? [];
            if (isset($mission)) {
                $userMissions = array_filter($userMissions, function ($missionId) use ($activeMissions) {
                    return $activeMissions->contains('id', $missionId);
                });
                $userMissionsDone = array_filter($userMissionsDone, function ($doneId) use ($activeMissions) {
                    return $activeMissions->contains('id', $doneId);
                });
                if (!in_array($mission->id, $userMissions)) {
                    $userMissions[] = $mission->id;
                }
                if (!empty($userMissions)) {
                    $user->missions = array_values($userMissions);
                } else {
                    $user->missions = [];
                }
                if (!empty($userMissionsDone)) {
                    $user->missions_done = array_values($userMissionsDone);
                } else {
                    $user->missions_done = [];
                }
                $user->update();
            }
        }
    }
}
