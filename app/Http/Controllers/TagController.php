<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\TagPage;
use Illuminate\Http\Request;

class TagController extends Controller
{

    public function show($slug)
    {
        abort(404);
        $tag = Tag::where('slug', $slug)->first();
        if (isset($tag)) {
            if (!isset($_COOKIE['page_seen'])) {
                $tag->seen_count += 1;
                $tag->update();
            }
            return view('tag.show', compact('tag'));
        } else {
            abort(404);
        }
    }

    public function destroy($tid, $pid, $class)
    {
        $tagPage = TagPage::where('tag_id', $tid)->where('page_id', $pid)->where('page_class', $class)->first();
        $tagPage->delete();
        return back();
    }

    public function getTagsPost(Request $request)
    {
        $data = [];
        if ($request->has('q')) {
            $search = $request->q;
            $data = Tag::where('title', 'LIKE', "%$search%")
                ->get();
        }
        if ($data->isEmpty()) {
            $data->add(['id' => $request->q, 'title' => $request->q]);
        }
        return response()->json($data);
    }

}
