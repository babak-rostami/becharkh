<?php

namespace App\Http\Controllers;

use App\Models\MongoCategory;
use App\Models\MongoItem;
use App\Models\SuggestPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class SuggestPageController extends Controller
{
    public function indexAdmin()
    {
        $suggests = SuggestPage::orderBy('created_at', 'desc')->paginate(100);
        return view('suggestp.admin-index', compact('suggests'));
    }

    public function create()
    {
        return view('suggestp.create');
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

    public function store(Request $request)
    {
        $sugp = new SuggestPage();
        $sugp->title = $request->title;
        $sugp->body = $request->body;
        $sugp->link = $request->link;
        $sugp->just_this_page = (int)$request->just_this_page;

        if ($request->hasFile('image')) {
            $cover = $request->file('image');
            $basefilename = Str::limit(Str::slug($request->title, '-'), 10, '') . time();
            $path = 'sugp/images/';

            $filename = $basefilename . '.webp';
            $sugp->image = $path . $filename;
            $this->uploadAndResizeImage($cover, $path, $filename, 90, 0);
        }

        $categories = array_filter(explode(',', $request->categories));
        $items = array_filter(explode(',', $request->items));

        if (count($categories) > 0) {
            $cat_ids = [];
            foreach ($categories as $category_id) {
                $cat_ids[] = $category_id;
                $category = MongoCategory::find($category_id);
                $cat_children = $category->allChildren();
                foreach ($cat_children as $child) {
                    $cat_ids[] = $child->id;
                }
            }
            $cat_ids = array_unique($cat_ids);
            $cat_ids = array_values($cat_ids);
            $sugp->categories = $cat_ids;
        }

        if (count($items) > 0) {
            $sugp->items = $items;
        }
        $sugp->save();

        return redirect()->route('admin.suggest.pages')->with('success', 'با موفقیت اضافه شد');
    }

    public function edit(Request $request, $id)
    {
        $sugp = SuggestPage::find($id);

        $compactVars = [
            'sugp'
        ];

        if (isset($sugp->categories)) {
            $categories = $sugp->categories;
            $categoryIds = implode(',', $categories);
            $categorySelects = MongoCategory::whereIn('_id', $categories)->select('title')->get();
            $compactVars[] = 'categoryIds';
            $compactVars[] = 'categorySelects';
        }
        if (isset($sugp->items)) {
            $items = $sugp->items;
            $itemIds = implode(',', $items);
            $itemSelects = MongoItem::whereIn('_id', $items)
                ->select('title')
                ->get()
                ->sortBy(function ($item) use ($items) {
                    return array_search($item->_id, $items);
                });
            $compactVars[] = 'itemIds';
            $compactVars[] = 'itemSelects';
        }

        return view('suggestp.edit', compact(...$compactVars));
    }

    public function update(Request $request, $id)
    {
        $sugp = SuggestPage::find($id);
        $sugp->title = $request->title;
        $sugp->body = $request->body;
        $sugp->link = $request->link;
        $sugp->just_this_page = (int) $request->just_this_page;

        if ($request->hasFile('image')) {
            $cover = $request->file('image');
            $path = 'sugp/images/';
            if ($sugp->getImage()) {
                $image_name = explode($path, $sugp->getImage())[1];
                $basefilename = explode('.webp', $image_name)[0];
            } else {
                $basefilename = Str::limit(Str::slug($request->title, '-'), 10, '') . time();
            }
            $filename = $basefilename . '.webp';
            $sugp->image = $path . $filename;
            $this->uploadAndResizeImage($cover, $path, $filename, 90, 0);
        }

        $categories = array_filter(explode(',', $request->categories));
        $items = array_filter(explode(',', $request->items));
        $unset_cats = 0;
        $unset_items = 0;
        if (count($categories) > 0) {
            $cat_ids = [];
            foreach ($categories as $category_id) {
                $cat_ids[] = $category_id;
                $category = MongoCategory::find($category_id);
                $cat_children = $category->allChildren();
                foreach ($cat_children as $child) {
                    $cat_ids[] = $child->id;
                }
            }
            $cat_ids = array_unique($cat_ids);
            $cat_ids = array_values($cat_ids);
            $sugp->categories = $cat_ids;
        } else {
            $unset_cats = 1;
        }
        if (count($items) > 0) {
            $sugp->items = $items;
        } else {
            $unset_items = 1;
        }
        $sugp->update();

        if ($unset_cats) {
            $sugp->unset('categories');
        }
        if ($unset_items) {
            $sugp->unset('items');
        }
        return redirect()->route('admin.suggest.pages')->with('success', 'با موفقیت ویرایش شد');
    }

    public function destroy(Request $request, $id)
    {
        $sugp = SuggestPage::find($id);
        $disk = Storage::disk('ftp');
        $image = $sugp->getImage();
        $disk->delete($image);
        $sugp->delete();
        return redirect()->back()->with('success', 'با موفقیت حذف شد');
    }
}
