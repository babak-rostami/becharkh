<?php

namespace App\Http\Controllers;

use App\Models\PageError;
use Illuminate\Http\Request;

class PageErrorController extends Controller
{
    public function index()
    {
        $site_errors = PageError::orderBy('created_at', 'desc')->get();
        return view('admin.site-errors', compact('site_errors'));
    }

    public function delete(Request $request, $id)
    {
        $error = PageError::find($id);
        $error->delete();
        return back()->with('success', 'حذف شد');
    }
    public function deleteAll(Request $request)
    {
        $errors = PageError::all();
        foreach ($errors as $error) {
            $error->delete();
        }
        return back()->with('success', 'همه پیام ها حذف شد!');
    }
}
