<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class BlogCategoryController extends Controller
{

    public function index()
    {
        $categories = BlogCategory::all();
        return view('blog.category', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'slug' => 'unique:blog_categories'
        ], [
            'slug.unique' => 'اسلاگ تکراری می باشد'
        ]);
        $category = new BlogCategory();
        $category->title = $request->title;
        $category->slug = $request->slug;
        $category->meta_title = $request->meta_title;
        $category->description = $request->description;
        $category->meta_description = $request->meta_description;

        if ($request->hasFile('image')) {
            $cover = $request->file('image');
            $filename = time() . '.' . $cover->getClientOriginalName();
            $category->image = $filename;
            Image::make($request->file('image'))->fit(600, 400)
                ->save("files/blogcategory/images/" . $filename, '90');
        }

        $category->save();

        return back()->with('success', 'دسته بندی با موفقیت ایجاد شد');
    }


    public function update(Request $request, BlogCategory $category)
    {
        $category->title = $request->title;
        $category->slug = $request->slug;
        $category->meta_title = $request->meta_title;
        $category->description = $request->description;
        $category->meta_description = $request->meta_description;

        if ($request->hasFile('image')) {
            $path = public_path('/files/blogcategory/images/' . $category->image);
            if (File::exists($path)) {
                File::delete($path);
            }
            $cover = $request->file('image');
            $filename = time() . '.' . $cover->getClientOriginalName();
            $category->image = $filename;
            Image::make($request->file('image'))->fit(600, 400)
                ->save("files/blogcategory/images/" . $filename, '90');
        }

        $category->update();

        return back()->with('success', 'دسته بندی با موفقیت ویرایش شد');
    }


}
