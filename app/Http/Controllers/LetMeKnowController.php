<?php

namespace App\Http\Controllers;

use App\Models\LetMeKnow;
use Illuminate\Http\Request;

class LetMeKnowController extends Controller
{

    public function store(Request $request)
    {
        $let_me_know = LetMeKnow::where('category_id', $request->category_id)->where('item_id', $request->item_id)->where('ip', $request->ip())->first();
        if (isset($let_me_know)) {
            return response()->json(['message' => 'درخواست شما در حال پیگیری است'], 200);
        } else {
            $let_me_know = new LetMeKnow();
            $let_me_know->category_id = $request->category_id;
            $let_me_know->item_id = $request->item_id;
            $let_me_know->ip = $request->ip();
            $let_me_know->save();
            return response()->json(['message' => 'درخواست شما با موفقیت ثبت شد'], 200);
        }
    }

    public function index()
    {
        $lmks = LetMeKnow::orderBy('created_at', 'desc')->with('category', 'item')->get();
        return view('admin.letmeknow.index', compact('lmks'));
    }
}
