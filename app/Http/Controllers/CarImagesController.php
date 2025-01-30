<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\CarImages;
use App\Notifications\SiteEvent;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class CarImagesController extends Controller
{

    public function CarImagesAdmin()
    {
        $images = CarImages::orderBy('id', 'desc')->get();
        return view('car.images-admin', compact('images'));
    }

    public function CarImageAccept($id)
    {
        $image = CarImages::find($id);
        $image->status = true;
        $image->update();

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => auth('admin')->user()->name . ' تصویر خودرو ' . $image->model->brand->title . ' ' . $image->model->title . ' را تایید کرد',
                'route' => route('car.page', ['brand_slug' => $image->model->brand->slug, 'model_slug' => $image->model->slug]),
            ]));
        }

        return back()->with('success', 'عکس تایید شد');
    }


    public function CarImageReject($id)
    {
        $image = CarImages::find($id);
        $image->status = false;
        $image->update();

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => auth('admin')->user()->name . ' تصویر خودرو ' . $image->model->brand->title . ' ' . $image->model->title . ' را رد کرد',
                'route' => route('car.page', ['brand_slug' => $image->model->brand->slug, 'model_slug' => $image->model->slug]),
            ]));
        }

        return back()->with('success', 'عکس رد شد');
    }


    public function uploadImage(Request $request)
    {
        $modelImage = new CarImages();
        $modelImage->model_id = $request->model_id;
        if (auth('user')->check()) {
            $modelImage->user_id = auth('user')->id();
        }
        if ($request->hasFile('image')) {
            $cover = $request->file('image');
            $filename = time() . '.' . $cover->getClientOriginalName();
            $modelImage->image = $filename;
            Image::make($request->file('image'))->fit(600, 400)
                ->save("files/carImages/images/" . $filename, '70');
        }
        $modelImage->save();
        return back()->with('success', 'تصویر با موفقیت آپلود شد و بعد از تایید نمایش داده می شود');
    }

}
