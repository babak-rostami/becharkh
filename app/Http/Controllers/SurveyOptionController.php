<?php

namespace App\Http\Controllers;

use App\Models\MongoCategoryComment;
use App\Models\MongoQuestion;
use App\Models\SurveyOption;
use Illuminate\Http\Request;

class SurveyOptionController extends Controller
{

    public function choose(Request $request)
    {
        $ip = $request->ip();
        $page = $request->page;
        $obj_id = $request->obj_id;
        $option_number = $request->option_number;
        $type = 1;

        // Find the user's current survey option
        $sur_opt = SurveyOption::where('page', $page)
            ->where('obj_id', $obj_id)
            ->where('ip', $ip)
            ->first();

        $object = null;
        if ($page == 'comment') {
            $object = MongoCategoryComment::find($obj_id);
        } elseif ($page == 'show_question') {
            $object = MongoQuestion::find($obj_id);
        }

        $delete_option = null;
        if (isset($sur_opt)) {
            // User has already voted
            if ($sur_opt->option_number == $option_number) {
                // User is removing their vote
                $type = 0;
                $sur_opt->delete();
            } else {
                // User is changing their vote
                $delete_option = $sur_opt->option_number; // Store the old option
                $sur_opt->option_number = $option_number; // Update to the new option
                $sur_opt->save();
            }
        } else {
            // New vote
            $sur_opt = new SurveyOption();
            $sur_opt->page = $page;
            $sur_opt->obj_id = $obj_id;
            $sur_opt->ip = $ip;
            $sur_opt->option_number = $option_number;
            $sur_opt->save();
        }

        if ($object) {
            // Calculate percentages and update counts
            $this->calCulatePercent($object, $option_number, $type, $delete_option);
            return response()->json([
                'surop1_count' => $object->surop1_count,
                'surop2_count' => $object->surop2_count,
                'surop3_count' => $object->surop3_count,
                'surop4_count' => $object->surop4_count,
            ], 200);
        }

        return response()->json(['error' => 'Object not found'], 404);
    }

    private function calCulatePercent($object, $option_number, $type, $delete_option = null)
    {
        $option_counts = [];
        $total_count = 0;

        // Gather current counts
        for ($i = 1; $i <= 4; $i++) {
            $count_property = 'surop' . $i . '_count';
            if (isset($object->$count_property)) {
                $option_data = explode('-', $object->$count_property);
                $option_counts[$i] = (int)$option_data[0];
                $total_count += $option_counts[$i];
            }
        }

        // Update the count for the new option
        $option_counts[$option_number] += ($type == 1) ? 1 : -1;

        // If there was a previous option selected, decrease its count
        if ($delete_option !== null) {
            $option_counts[$delete_option] -= 1; // Decrease the count for the deleted option
        }

        // Calculate the total count again
        $total_count = array_sum($option_counts);

        // Update the object with new counts and percentages
        for ($i = 1; $i <= 4; $i++) {
            if (isset($option_counts[$i])) {
                $percent = ($total_count > 0) ? (100 * $option_counts[$i]) / $total_count : 0;
                $object->{'surop' . $i . '_count'} = $option_counts[$i] . '-' . round($percent, 2);
            }
        }

        // Save the updated object
        $object->update();
    }
}
