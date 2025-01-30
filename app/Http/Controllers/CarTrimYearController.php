<?php

namespace App\Http\Controllers;

use App\Models\CarModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CarTrimYearController extends Controller
{


    public function getTrims(Request $request)
    {
        $model = CarModel::find($request->model);
        $year = $request->year;
        $trims_count = 0;
        foreach ($model->trims as $t) {
            foreach ($t->years as $y) {
                if ($year >= $y->from && $year <= $y->to) {
                    $trims_count++;
                    echo '<option value=' . $t->id . '>' . $t->title . '</option>';
                }
            }
        }
        if ($trims_count == 0) {
            echo '<option value=' . null . '>' . "تریم وجود ندارد" . '</option>';
        }
    }
}
