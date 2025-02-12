<?php

namespace App\Http\Controllers;

use App\Models\UserSearch;
use Illuminate\Http\Request;

class UserSearchController extends Controller
{
    public function index()
    {
        $searches = UserSearch::orderBy('created_at', 'desc')->get();
        return view('admin.user.search-index', compact('searches'));
    }

    public function delete(Request $request, $id)
    {
        $search = UserSearch::find($id);
        $search->delete();
        return back()->with('success', 'حذف شد');
    }
}
