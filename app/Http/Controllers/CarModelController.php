<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class CarModelController extends Controller
{


    public function index($id)
    {
        $brands = Brand::all();

        $br = Brand::find($id);
        $models = $br->models;
        return view('carModel.index', compact('br', 'brands', 'models'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'brand_id' => 'required'
        ], [
            'title.required' => 'لطفا نام مدل را وارد کنید',
            'brand_id.required' => 'لطفا نام مدل را وارد کنید'
        ]);

        $model = new CarModel();
        $model->title = $request->title;
        $model->slug = preg_replace('/\s+/', '-', $request->title);
        $model->brand_id = $request->brand_id;

        $model->save();

        return back()->with('success', 'مدل با موفقیت افروده شد');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\CarModel $carModel
     * @return \Illuminate\Http\Response
     */
    public function show(CarModel $carModel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\CarModel $carModel
     * @return \Illuminate\Http\Response
     */
    public function edit(CarModel $carModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\CarModel $carModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'brand_id' => 'required',
        ], [
            'title.required' => 'لطفا نام مدل را وارد کنید',
            'brand_id.required' => 'لطفا نام مدل را وارد کنید'
        ]);

        $model = CarModel::find($id);
        $model->title = $request->title;
        $model->slug = preg_replace('/\s+/', '-', $request->title);
        $model->brand_id = $request->brand_id;

        $model->update();

        return back()->with('success', 'مدل با موفقیت ویرایش شد');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\CarModel $carModel
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = CarModel::find($id);
        $model->delete();

        return back()->with('success', 'مدل با موفقیت حذف شد');
    }
}
