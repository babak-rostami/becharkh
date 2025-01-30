<?php

namespace App\Http\Controllers;

use App\Models\RaceOption;
use App\Models\RaceOptionPercent;
use Illuminate\Http\Request;

class RaceOptionPercentController extends Controller
{

    public function voteStore(Request $request, $id)
    {
        $option = RaceOption::find($id);
        $race = $option->race;
        foreach ($race->options as $op) {
            foreach ($op->votes as $v) {
                if ($v->ip == $request->ip()) {
                    $v->delete();
                    break;
                }
            }
        }
        $vote = new RaceOptionPercent();
        $vote->option_id = $id;
        $vote->ip = $request->ip();
        $vote->save();
        return back();
    }

}
