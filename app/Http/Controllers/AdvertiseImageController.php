<?php

namespace App\Http\Controllers;

use App\Models\Advertise;
use App\Models\AdvertiseImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class AdvertiseImageController extends Controller
{


    public function store(Request $request, $slug, $random_id)
    {
        $ad = Advertise::where('slug', $slug)->where('random_id', $random_id)->first();
        if (auth('user')->user()->cannot('addImage', $ad)) {
            abort(403);
        }
        if (isset($ad)) {
            $adImage = new AdvertiseImage();
            $adImage->advertise_id = $ad->id;
            if ($request->hasFile('file')) {
                $cover = $request->file('file');
                $filename = time() . '.' . $cover->getClientOriginalName();
                $adImage->image = $filename;

                Image::make($request->file('file'))->fit(500, 500)
                    ->save("files/advertise/images/" . $filename, '60');

            }
            $adImage->save();
        }

    }


    public function delete($id)
    {
        $adImage = AdvertiseImage::find($id);

        if (isset($adImage)) {

            $advertise = Advertise::find($adImage->advertise_id);
            if (auth('user')->user()->cannot('deleteImage', $advertise)) {
                abort(403);
            }

            $path = public_path('/files/advertise/images/' . $adImage->image);
            if (File::exists($path)) {
                File::delete($path);
            }
            $adImage->delete();
            return back()->with('success', 'تصویر با موفقیت حذف شد');
        } else {
            return back();
        }


    }

}
