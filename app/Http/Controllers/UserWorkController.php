<?php

namespace App\Http\Controllers;

use App\Models\MongoWork;
use App\Models\UserWork;
use App\Models\Work;
use Illuminate\Http\Request;

class UserWorkController extends Controller
{

    public function store(Request $request)
    {
        $user = auth('user')->user();
        $job = MongoWork::find($request->job_id);

        if (!isset($user) || !isset($job)) {
            return response()->json(['error' => 'خطا'], 404);
        }

        $user_jobs = $user->works ?? [];
        if (count($user_jobs) >= 5) {
            return response()->json(['error' => 'شما حداکثر 5 تخصص میتوانید انتخاب کنید'], 403);
        }

        if (!in_array($request->job_id, $user_jobs)) {
            $user_jobs[] = $request->job_id;
            $user->works = $user_jobs;
            $user->save();
            return response()->json(['status' => 1], 200);
        } else {
            return response()->json(['error' => $job->title . ' قبلا انتخاب شده است'], 403);
        }
    }

    public function getUserJobs()
    {
        $user = auth('user')->user();
        if (!isset($user)) {
            return response()->json(['error' => 'لاگین نیستید!'], 403);
        }
        $jobs = $user->jobs();
        $user_job_cookie = null;
        foreach ($jobs as $key => $j) {
            if ($user_job_cookie != null) {
                $user_job_cookie .= "-";
            }
            $user_job_cookie .= $key + 1 . "=" . $j->id;
        }
        $jobs = $jobs->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->title
            ];
        });
        return response()->json(['jobs' => $jobs, 'user_jobs_cookie' => $user_job_cookie], 200);
    }

    public function destroy(Request $request)
    {
        $user = auth('user')->user();
        $user_jobs = $user->works ?? [];
        if (in_array($request->job_id, $user_jobs)) {
            $user_jobs = array_diff($user_jobs, [$request->job_id]);
            if (empty($user_jobs)) {
                $user->unset('works');
            } else {
                $user->works = $user_jobs;
                $user->save();
            }
            return response()->json(['status' => 1], 200);
        } else {
            return response()->json(['error' => ' حذف شده است'], 403);
        }
    }
}
