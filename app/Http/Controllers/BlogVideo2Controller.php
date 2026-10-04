<?php

namespace App\Http\Controllers;

use App\Models\MongoBlog;
use App\Models\MongoVideo;
use Illuminate\Http\Request;

class BlogVideo2Controller extends Controller
{

    public function addVideoPost(Request $request)
    {
        $user = auth('user')->user();
        $blog = MongoBlog::find($request->blog_id);
        $video = MongoVideo::find($request->video_id);
        if (isset($blog) && isset($video) && isset($user)) {
            $blogVideo = $blog->videos ?? null;
            if ($blogVideo != null) {
                if ($blogVideo != $video->id) {
                    $blog->videos = $video->id;
                    $blog->update();
                }
            } else {
                $blog->videos = $video->id;
                $blog->update();
            }
        }
        return response()->json([
            'success' => 1
        ], 200);
    }
}
