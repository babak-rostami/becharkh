<?php

namespace App\Http\Controllers;

use App\Models\Affilate;
use App\Models\InputImage;
use App\Models\MongoCategoryComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class InputImagesController extends Controller
{
    public function store(Request $request)
    {
        if ($request->hasFile('image')) {
            $new_input_image = new InputImage();

            $cover = $request->file('image');
            $path = 'inputImages/';

            $baseFileName = time() . rand(100, 999);
            $filename = $baseFileName . '.webp';

            $this->uploadAndResizeImage($cover, $path, $filename, 90, 0);

            $new_input_image->image = $path . $filename;
            $new_input_image->save();

            $ftp_path = 'https://dl.becharkh.com/user_files/';

            return response()->json([
                'status' => 'success',
                'image_url' => $ftp_path . $path . $filename,
                'id' => $new_input_image->id,
            ]);
        }

        return response()->json(['status' => 'error'], 422);
    }

    public function update(Request $request)
    {
        $object = null;

        if ($request->is_for == 'ccomment') {
            $object = MongoCategoryComment::find($request->object_id);
        } elseif ($request->is_for == 'affilate') {
            $object = Affilate::find($request->object_id);
        }

        if (!$object || !is_array($object->iimages)) {
            return response()->json(['success' => false, 'message' => 'خطا در آپلود تصویر']);
        }

        $targetImage = collect($object->iimages)->firstWhere('id', $request->image_id);

        if (!$targetImage) {
            return response()->json(['success' => false, 'message' => 'تصویر مورد نظر یافت نشد']);
        }

        $fullPath = $targetImage['path']; // مثلاً: images/comments/2025/08/xyz.webp
        $pathInfo = pathinfo($fullPath);
        $path = $pathInfo['dirname'] . '/'; // => images/comments/2025/08/
        $filename = $pathInfo['basename'];  // => xyz.webp

        $this->uploadAndResizeImage($request->file('image'), $path, $filename, 90, 0);

        $ftp_path = 'https://dl.becharkh.com/user_files/';
        $url = $ftp_path . $fullPath;

        return response()->json([
            'success' => true,
            'new_image_url' => $url
        ]);
    }

    public function destroy(Request $request)
    {
        $object = null;

        if ($request->is_for === 'ccomment') {
            $object = MongoCategoryComment::find($request->object_id);
        } elseif ($request->is_for === 'affilate') {
            $object = Affilate::find($request->object_id);
        }

        if (!$object || !is_array($object->iimages)) {
            return response()->json(['success' => false, 'message' => 'کامنت یا تصاویر یافت نشدند']);
        }

        $images = collect($object->iimages);

        $targetImage = $images->firstWhere('id', $request->image_id);

        if (!$targetImage) {
            return response()->json(['success' => false, 'message' => 'تصویر مورد نظر یافت نشد']);
        }

        $disk = Storage::disk('ftp');
        $filePath = $targetImage['path'];

        if ($disk->exists($filePath)) {
            $disk->delete($filePath);
        }

        $filteredImages = $images->filter(function ($img) use ($request) {
            return (int)$img['id'] !== (int)$request->image_id;
        });

        if ($filteredImages->isEmpty()) {
            $object->unset('iimages');
        } else {
            $object->iimages = $filteredImages->toArray();
            $object->update();
        }


        return response()->json(['success' => true]);
    }


    private function uploadAndResizeImage($image, $path, $filename, $quality, $thumb)
    {
        $disk = Storage::disk('ftp');

        if ($thumb == 1) {
            $resizedImage = Image::make($image)->resize(256, null, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('webp', $quality);
        } else {
            $resizedImage = Image::make($image)->encode('webp', $quality);
        }
        $disk->put($path . $filename, (string) $resizedImage);
    }

    public function setImagesArrayForStore($images, $is_for, $address = null)
    {
        $images = explode(',', $images);
        $input_images = InputImage::find($images);
        $disk = Storage::disk('ftp');

        if ($is_for == 'ccomment') {
            $new_path_dir = 'ccomment/images';
        } elseif ($is_for == 'affilate') {
            $new_path_dir = 'product/images';
        }
        if ($address) {
            $new_path_dir =  $new_path_dir . '/' . $address;
        }

        $result_images = [];

        $i = 0;
        foreach ($input_images as $img) {
            $old_path = $img->image;

            $extension = pathinfo($old_path, PATHINFO_EXTENSION);

            if ($address) {
                $new_file_name = $address . '-' . time() . '-' . rand(1000, 9999) . '-' . $i . '.' . $extension;
            } else {
                $new_file_name = time() . '-' . rand(1000, 9999) . '-' . $i . '.' . $extension;
            }
            $new_path = $new_path_dir . '/' . $new_file_name;


            if ($disk->exists($old_path)) {
                $file_content = $disk->get($old_path);
                $disk->put($new_path, $file_content);

                $disk->delete($old_path);
            }

            $result_images[] = [
                'id' => $i,
                'path' => $new_path,
            ];

            $img->delete();

            $i++;
        }

        return $result_images;
    }

    public function setImagesArrayForUpdate($images, $is_for, $object, $address)
    {
        $last_images = $object->iimages ?? [];

        $images = explode(',', $images);
        $input_images = InputImage::find($images);
        $disk = Storage::disk('ftp');

        if ($is_for == 'ccomment') {
            $new_path_dir = 'ccomment/images';
        } elseif ($is_for == 'affilate') {
            $new_path_dir = 'product/images';
        }
        if ($address) {
            $new_path_dir =  $new_path_dir . '/' . $address;
        }

        $new_images = [];

        foreach ($input_images as $img) {
            $old_path = $img->image;
            $extension = pathinfo($old_path, PATHINFO_EXTENSION);

            if ($address) {
                $new_file_name = $address . '-' . time() . '-' . rand(1000, 9999) . '.' . $extension;
            } else {
                $new_file_name = time() . '-' . rand(1000, 9999) . '.' . $extension;
            }

            $new_path = $new_path_dir . '/' . $new_file_name;

            if ($disk->exists($old_path)) {
                $file_content = $disk->get($old_path);
                $disk->put($new_path, $file_content);
                $disk->delete($old_path);
            }

            $new_images[] = [
                'id' => null, // بعداً با merge مقداردهی می‌کنیم
                'path' => $new_path,
            ];

            $img->delete();
        }

        $merged_images = array_merge($last_images, $new_images);

        foreach ($merged_images as $index => &$img) {
            $img['id'] = $index;
        }

        return $merged_images;
    }
}
