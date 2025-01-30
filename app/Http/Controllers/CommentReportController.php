<?php

namespace App\Http\Controllers;

use App\Models\CommentReport;
use Illuminate\Http\Request;

class CommentReportController extends Controller
{

    public function store(Request $request)
    {
        $advReport = new CommentReport();

        if (auth('user')->check()) {
            $advReport->user_id = auth('user')->id();
        }
        $advReport->comment_id = $request->comment_id;
        $advReport->body = $request->body;

        $advReport->save();

        return back()->with('success', 'با تشکر از گزارش شما');
    }


    public function all()
    {
        $reports = CommentReport::all();
        return view('report.comment', compact('reports'));
    }


    public function destroy($id)
    {
        $report = CommentReport::find($id);
        $report->delete();

        return back()->with('success', 'گزارش با موفقیت حذف شد');
    }

}
