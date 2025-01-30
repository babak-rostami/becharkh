<?php

namespace App\Http\Controllers;

use App\Models\CarAdvocate;
use Illuminate\Http\Request;

class CarAdvocateController extends Controller
{


    public function store(Request $request)
    {
        $follow = true;

        foreach (auth('user')->user()->carAdvocates as $car) {
            if ($car->model_id == $request->model_id) {
                $car->delete();
                $follow = false;
                return back()->with('success', 'شما دیگه طرفدار نیستید');
            }
        }

        if ($follow) {
            $advocate = new CarAdvocate();
            $advocate->model_id = $request->model_id;
            $advocate->user_id = auth('user')->id();
            $advocate->save();
        }

        return back()->with('success', 'شما با موفقیت طرفدار شدید');
    }


}
