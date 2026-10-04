<?php

namespace App\Http\Controllers;

use App\Models\AdvertiseReport;
use App\Models\MongoAdvertiseReport;
use Illuminate\Http\Request;

class AdvertiseReportController extends Controller
{

    public function store(Request $request)
    {
        $advReport = new MongoAdvertiseReport();

        if (auth('user')->check()) {
            $advReport->user_id = auth('user')->id();
        }
        $advReport->advertise_id = $request->advertise_id;
        $advReport->body = $request->body;

        $advReport->save();

        return back()->with('success', 'گزارش شما با موفقیت ثبت شد');
    }
}
