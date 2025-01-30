<?php

namespace App\Http\Controllers;

use App\Models\ImageCompressor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageCompressorController extends Controller
{
    public function adminIndex()
    {
        $images = ImageCompressor::orderBy('id', 'desc')->paginate(100);
        return view('imageCompressor.admin-index', compact('images'));
    }

    public function adminDestroy($id)
    {
        $image = ImageCompressor::find($id);
        $disk = Storage::disk('ftp');
        $disk->delete($image->image);
        $disk->delete($image->compress_image);
        $image->delete();
        return back()->with('success', 'تصویر با موفقیت حذف شد');
    }
    public function index()
    {
        return view('imageCompressor.index');
    }

    public function upload(Request $request)
    {
        // compress_image
        $imageC = new ImageCompressor();

        if ($request->hasFile('image')) {
            $cover = $request->file('image');
            $fileName = str_replace('/', '-', $cover->getClientOriginalName());
            $ext = $cover->getClientOriginalExtension();
            $path = 'imageCompress/org-images/';
            //main image          
            $filename = time() . '.becharkh.com.' . $fileName;
            $imageC->image = $path . $filename;
            $imageC->image_ext = $ext;
            $this->uploadAndResizeImage($cover, $path, $filename, $ext, 90, 0);
        }
        $imageC->save();

        return response()->json(
            [
                'status' => 1,
                'filePath' => $imageC->image(),
                'image_id' => $imageC->id
            ],
            200
        );
    }

    public function compress(Request $request)
    {
        $image = ImageCompressor::find($request->image_id);
        if (!isset($image)) {
            return response()->json(['status' => 0], 404);
        }
        $disk = Storage::disk('ftp');
        if (isset($image->compress_image)) {
            $disk->delete($image->compress_image);
        }

        $image_quality = $request->image_quality;
        $image_format = $request->image_format;
        $image_width = $request->image_width;
        $image_height = $request->image_height;

        $path = 'imageCompress/comp-images/';
        $compressName = time() . explode("imageCompress/org-images/", $image->image)[1];
        if ($image_format != 0) {
            $compressNameWithoutExt = explode($image->image_ext, $compressName)[0];
            $compressName = $compressNameWithoutExt . $image_format;
        } else {
            $image_format = $image->image_ext;
        }

        $image->compress_image = $path . $compressName;
        $image->update();

        $newImage = $disk->get($image->image);
        $this->uploadAndResizeImage($newImage, $path, $compressName, $image_format, $image_quality, 1, $image_width, $image_height);

        return response()->json(
            [
                'new_image' => $image->newImage(),
                'new_image_download' => route('image.compressor.download', $image->id),
                'new_image_size' => $disk->size($image->compress_image)
            ],
            200
        );
    }

    private function uploadAndResizeImage($image, $path, $filename, $ext, $quality, $thumb, $w = null, $h = null)
    {
        $disk = Storage::disk('ftp');

        if ($thumb == 1) {
            $intImage = Image::make($image);
            if ($w != null && $h != null) {
                $height = $w;
                $width = $h;
            } else {
                $height = $intImage->height();
                $width = $intImage->width();
            }
            $resizedImage = $intImage->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            })->encode($ext, $quality);
            $disk->put($path . $filename, (string) $resizedImage);
        } else {
            $disk->put($path . $filename, fopen($image, 'r+'));
        }
    }

    public function downloadImage($id)
    {
        $disk = Storage::disk('ftp');
        $ic = ImageCompressor::find($id);
        return  $disk->download($ic->compress_image);
    }
}
