<?php

namespace App\Http\Controllers;

use App\Models\MongoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ItemImageController extends Controller
{

    public function index($item_id)
    {
        $item = MongoItem::find($item_id);
        $images = $item->images ?? [];
        return view('admin.featureItems.images', compact('item', 'images'));
    }

    private function uploadAndResizeImage($image, $path, $filename, $quality, $thumb)
    {
        $disk = Storage::disk('ftp');

        if ($thumb == 1) {
            $resizedImage = Image::make($image)->resize(128, null, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('webp', $quality);
        } else {
            $resizedImage = Image::make($image)->encode('webp', $quality);
        }
        $disk->put($path . $filename, (string) $resizedImage);
    }

    public function store(Request $request, $item_id)
    {
        $item = MongoItem::find($item_id);
        $images = $item->images ?? [];

        if ($request->hasFile('image')) {
            $cover = $request->file('image');
            $path = 'itemImg/images/' . $item->category->slug . '/';

            $baseFileName = $item->slug . time() . rand(100, 999);
            //main image
            $filename = $baseFileName . '.webp';
            $this->uploadAndResizeImage($cover, $path, $filename, 95, 0);
            //thum image
            $filename2 = $baseFileName . '2.webp';
            $this->uploadAndResizeImage($cover, $path, $filename2, 90, 1);
        }
        $images[] = $path . $filename;
        $item->images = $images;
        $item->update();

        return back()->with('success', 'با موفقیت انجام شد');
    }

    public function update(Request $request)
    {
        if ($request->hasFile('image')) {
            $image_key = $request->image_key;
            $item_id = $request->item_id;
            $disk = Storage::disk('ftp');
            $item = MongoItem::find($item_id);

            $cover = $request->file('image');

            $images = $item->images ?? [];
            if (isset($images[$image_key])) {
                $filename = $images[$image_key];
                $filename2 = explode('.webp', $filename)[0] . '2.webp';
                $change = 0;
            } else {
                $path = 'itemImg/images/' . $item->category->slug . '/';
                $baseFileName = $item->slug . time() . rand(100, 999);
                $filename = $path . $baseFileName . '.webp';
                $filename2 = $path . $baseFileName . '2.webp';
                $change = 1;
            }
            //main image
            $resizedImage = Image::make($cover)->encode('webp', 95);
            $disk->put($filename, (string) $resizedImage);

            //thum image
            $resizedImage = Image::make($cover)->resize(128, null, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('webp', 90);
            $disk->put($filename2, (string) $resizedImage);

            if ($change) {
                $images[$image_key] = $filename;
                $item->images = $images;
                $item->update();
            }
        }
        return back()->with('success', 'با موفقیت انجام شد');
    }
}
