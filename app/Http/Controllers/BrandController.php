<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\CarModel;
use Illuminate\Http\Request;
use Psy\Util\Str;

class BrandController extends Controller
{

    public function index()
    {
        $brands = Brand::all();
        return view('brand.index', compact('brands'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required'
        ], [
            'title.required' => 'لطفا نام برند را وارد کنید'
        ]);

        $brand = new Brand();
        $brand->title = $request->title;
        $brand->slug = preg_replace('/\s+/', '-', $request->title);

        $brand->save();

        return back()->with('success', 'برند با موفقیت افروده شد');

    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'slug' => 'unique:brands,slug,' . $id
        ], [
            'title.required' => 'لطفا نام برند را وارد کنید'
        ]);

        $brand = Brand::find($id);
        $brand->title = $request->title;
        $brand->slug = preg_replace('/\s+/', '-', $request->title);

        $brand->update();

        return back()->with('success', 'برند با موفقیت ویرایش شد');
    }


    public function destroy($id)
    {
        $brand = Brand::find($id);
        $brand->delete();

        return back()->with('success', 'برند با موفقیت حذف شد');
    }

}
