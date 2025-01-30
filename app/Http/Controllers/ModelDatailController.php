<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\ModelDatail;
use App\Models\ShortLink;
use App\Notifications\SiteEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ModelDatailController extends Controller
{

    public function all($model_id)
    {
        $model = CarModel::find($model_id);
        $details = $model->details;
        return view('carDetail.all', compact('details', 'model'));
    }

    public function create($model_id)
    {
        $model = CarModel::find($model_id);
        return view('carDetail.create', compact('model'));
    }

    public function store(Request $request, $id)
    {
        $this->validate($request, [
            'production_year' => 'required',
            'cylinder' => 'required',
            'image' => 'required',
            'engine_volume' => 'max:250',
            'acceleration' => 'max:250',
            'engine_power' => 'max:250',
            'torque' => 'max:250',
            'max_speed' => 'max:250',
            'gearbox' => 'max:250',
            'differential' => 'max:250',
            'body_class' => 'max:250',
            'car_weight' => 'max:250',
            'fuel_consumption' => 'max:250',
            'buck_volume' => 'max:250',
            'country' => 'max:250',
            'brake_system' => 'max:250',
            'steering_system' => 'max:250',
            'media_system' => 'max:500',
            'mirror_glass_system' => 'max:250',
            'lighting' => 'max:250',
            'welfare' => 'max:550',
            'power_points' => 'max:250',
            'weak_points' => 'max:250',
        ]);

        $model = CarModel::find($id);
        $carDetail = new ModelDatail();
        $carDetail->model_id = $model->id;
        $carDetail->brand_id = $model->brand->id;


        $carDetail->production_year = $request->production_year;
        $carDetail->cylinder = $request->cylinder;
        $carDetail->engine_volume = $request->engine_volume;
        $carDetail->acceleration = $request->acceleration;
        $carDetail->engine_power = $request->engine_power;
        $carDetail->torque = $request->torque;
        $carDetail->max_speed = $request->max_speed;
        $carDetail->gearbox = $request->gearbox;
        $carDetail->differential = $request->differential;
        $carDetail->body_class = $request->body_class;
        $carDetail->car_weight = $request->car_weight;
        $carDetail->fuel_consumption = $request->fuel_consumption;
        $carDetail->buck_volume = $request->buck_volume;
        $carDetail->country = $request->country;
        $carDetail->brake_system = $request->brake_system;
        $carDetail->steering_system = $request->steering_system;
        $carDetail->media_system = $request->media_system;
        $carDetail->mirror_glass_system = $request->mirror_glass_system;
        $carDetail->lighting = $request->lighting;
        $carDetail->welfare = $request->welfare;
        $carDetail->power_points = $request->power_points;
        $carDetail->weak_points = $request->weak_points;
        $carDetail->description = $request->description;
        $carDetail->others = $request->others;

        if ($request->hasFile('image')) {
            $cover = $request->file('image');
            $filename = time() . '.' . $cover->getClientOriginalName();
            $carDetail->image = $filename;
            Image::make($request->file('image'))->fit(600, 400)
                ->save("files/carmodel/images/" . $filename, '70');
        }

        $carDetail->save();

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => auth('admin')->user()->name . ' مشخصات فنی خودرو ' . $model->title . ' ' . $model->brand->title . ' ' . $request->production_year . ' ' . $request->cylinder . ' را ثبت کرد',
                'route' => route('car.detail.show', ['brand_slug' => $model->brand->slug, 'model_slug' => $model->slug]),
            ]));
        }

        return redirect()->route('car.detail.all', $model->id)->with('success', 'مشخصات با موفقیت ثبت شد');
    }

    public function edit($model_id, $detail_id)
    {
        $model = CarModel::find($model_id);
        $detail = ModelDatail::find($detail_id);
        return view('carDetail.edit', compact('model', 'detail'));
    }

    public function update(Request $request, $model_id, $detail_id)
    {
        $this->validate($request, [
            'production_year' => 'required',
            'cylinder' => 'required',
            'engine_volume' => 'max:250',
            'acceleration' => 'max:250',
            'engine_power' => 'max:250',
            'torque' => 'max:250',
            'max_speed' => 'max:250',
            'gearbox' => 'max:250',
            'differential' => 'max:250',
            'body_class' => 'max:250',
            'car_weight' => 'max:250',
            'fuel_consumption' => 'max:250',
            'buck_volume' => 'max:250',
            'country' => 'max:250',
            'brake_system' => 'max:250',
            'steering_system' => 'max:250',
            'media_system' => 'max:500',
            'mirror_glass_system' => 'max:250',
            'lighting' => 'max:250',
            'welfare' => 'max:550',
            'power_points' => 'max:250',
            'weak_points' => 'max:250',
        ]);

        $model = CarModel::find($model_id);
        $carDetail = ModelDatail::find($detail_id);

        $carDetail->production_year = $request->production_year;
        $carDetail->cylinder = $request->cylinder;
        $carDetail->engine_volume = $request->engine_volume;
        $carDetail->acceleration = $request->acceleration;
        $carDetail->engine_power = $request->engine_power;
        $carDetail->torque = $request->torque;
        $carDetail->max_speed = $request->max_speed;
        $carDetail->gearbox = $request->gearbox;
        $carDetail->differential = $request->differential;
        $carDetail->body_class = $request->body_class;
        $carDetail->car_weight = $request->car_weight;
        $carDetail->fuel_consumption = $request->fuel_consumption;
        $carDetail->buck_volume = $request->buck_volume;
        $carDetail->country = $request->country;
        $carDetail->brake_system = $request->brake_system;
        $carDetail->steering_system = $request->steering_system;
        $carDetail->media_system = $request->media_system;
        $carDetail->mirror_glass_system = $request->mirror_glass_system;
        $carDetail->lighting = $request->lighting;
        $carDetail->welfare = $request->welfare;
        $carDetail->power_points = $request->power_points;
        $carDetail->weak_points = $request->weak_points;
        $carDetail->description = $request->description;
        $carDetail->others = $request->others;

        if ($request->hasFile('image')) {
            $path = public_path('/files/carmodel/images/' . $carDetail->image);
            if (File::exists($path)) {
                File::delete($path);
            }
            $cover = $request->file('image');
            $filename = time() . '.' . $cover->getClientOriginalName();
            $carDetail->image = $filename;
            Image::make($request->file('image'))->fit(600, 400)
                ->save("files/carmodel/images/" . $filename, '70');
        }

        $carDetail->update();

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => auth('admin')->user()->name . ' مشخصات فنی خودرو ' . $model->title . ' ' . $model->brand->title . ' ' . $request->production_year . ' ' . $request->cylinder . ' را ویرایش کرد',
                'route' => route('car.detail.show', ['brand_slug' => $model->brand->slug, 'model_slug' => $model->slug]),
            ]));
        }

        return redirect()->route('car.detail.all', $model->id)->with('success', 'مشخصات با موفقیت ویرایش شد');
    }

    public function delete($id)
    {
        $detail = ModelDatail::find($id);
        $path = public_path('/files/carmodel/images/' . $detail->image);
        if (File::exists($path)) {
            File::delete($path);
        }
        $detail->delete();
        return back()->with('success', 'مشخصات با موفقیت حذف شد');
    }

    public function show(Request $request, $brand_slug, $model_slug = null)
    {

        if ($brand_slug != null) {
            if ($model_slug != null) {
                $route = route('question.index', "car") . "?brand=" . $brand_slug . "&model=" . $model_slug;
                return redirect($route);
            } else {
                $route = route('question.index', "car") . "?brand=" . $brand_slug;
                return redirect($route);
            }
        } else {
            return route('question.index');
        }


        $brand = Brand::where('nameEn', $brand_slug)->first();
        if (isset($brand)) {
            foreach ($brand->models as $m) {
                if ($m->nameEn == $model_slug) {
                    $model = $m;
                }
            }
        } else {
            abort(404);
        }

        if (isset($model)) {
            $details = $model->details;
        } else {
            abort(404);
        }

        if (isset($request->s)) {
            $detail = $details->get($request->s);
        } else {
            $detail = $details->first();
        }

        if ($detail->shortlink == null) {
            $this->createShortLink('detail', $detail->id);
        }

        if (isset($detail)) {
            if (!isset($_COOKIE['page_seen'])) {
                $detail->seen_count += 1;
                $detail->update();
            }
            return view('carDetail.show', compact('detail'));
        } else {
            abort(404);
        }
    }


    private function createShortLink($link_class, $link_id)
    {
        $random = Str::random(12);
        if ($this->isShortLinkUnique($random)) {
            $shortlink = new ShortLink();
            $shortlink->short_link = $random;
            $shortlink->link_id = $link_id;
            $shortlink->link_class = $link_class;
            $shortlink->save();
        } else {
            $this->createShortLink($link_class, $link_id);
        }
    }

    private function isShortLinkUnique($short_link)
    {
        $shortLinks = ShortLink::all();
        foreach ($shortLinks as $sh) {
            if ($sh->short_link == $short_link) {
                return false;
            }
        }
        return true;
    }

    public function priority(Request $request)
    {
        $detail = ModelDatail::find($request->detail_id);
        $detail->priority = $request->priority;

        $detail->update();
        return back()->with('success', 'اولویت با موفقیت ثبت شد');
    }
}
