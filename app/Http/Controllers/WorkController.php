<?php

namespace App\Http\Controllers;

use App\Models\MongoWork;
use App\Models\Work;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;


class WorkController extends Controller
{

    public function adminIndex()
    {
        $jobs = MongoWork::all();
        return view('job.admin-index', compact('jobs'));
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'title' => 'max:255|required',
            'title_en' => 'max:255|required',
            'slug' => 'max:255|required|unique:works',
        ], [
            'title.max' => 'نام شغل نباید از 255 کلمه بیشتر باشد',
            'title_en.max' => 'نام انگلیسی شغل نباید از 255 کلمه بیشتر باشد',
            'slug.max' => 'اسلاگ نباید از 255 کلمه بیشتر باشد',
            'slug.unique' => 'اسلاگ قبلا استفاده شده است',
        ]);

        $work = new MongoWork();
        $work->title = $request->title;
        $work->title_en = $request->title_en;
        $work->slug = $request->slug;
        $work->similar_search = $request->similar_search;
        $work->body = $request->body;

        if ($request->hasFile('image')) {
            $disk = Storage::disk('ftp');
            $file = $request->file('image');
            $path = 'job/images/';

            $basefilename = $work->slug . time();
            //org image
            $filename = $basefilename . '.webp';
            $work->image = $path . $filename;
            $resizedImage = Image::make($file)->encode('webp', 90);
            $disk->put($path . $filename, (string) $resizedImage);

            //thumb image
            $filename2 = $basefilename . '2.webp';
            $resizedImage2 = Image::make($file)->resize(128, null, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('webp', 90);
            $disk->put($path . $filename2, (string) $resizedImage2);
        }
        $work->save();

        return back()->with('success', 'شغل با موفقیت ایجاد شد');
    }

    public function updateAdmin(Request $request, $job_id)
    {
        $work = MongoWork::find($job_id);
        $work->title = $request->title;
        $work->title_en = $request->title_en;
        $work->similar_search = $request->similar_search;
        $work->body = $request->body;

        if ($request->hasFile('image')) {
            $disk = Storage::disk('ftp');

            if ($work->getImage()) {
                $filename = explode("job/images/", $work->getImage())[1];
                $basefilename = explode('.webp', $filename)[0];
                $filename2 = $basefilename . '2.webp';
            } else {
                $basefilename = $work->slug . time();
                $filename = $basefilename . '.webp';
                $filename2 = $basefilename . '2.webp';
            }
            $file = $request->file('image');
            $path = 'job/images/';

            //org image
            $work->image = $path . $filename;
            $resizedImage = Image::make($file)->encode('webp', 90);
            $disk->put($path . $filename, (string) $resizedImage);

            //thumb image
            $resizedImage2 = Image::make($file)->resize(128, null, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('webp', 90);
            $disk->put($path . $filename2, (string) $resizedImage2);
        }

        $work->update();

        return back()->with('success', 'شغل با موفقیت ویرایش شد');
    }
}
