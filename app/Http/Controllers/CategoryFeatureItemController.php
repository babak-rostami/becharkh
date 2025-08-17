<?php

namespace App\Http\Controllers;

use App\Models\CategoryFeature;
use App\Models\CategoryFeatureItem;
use App\Models\MongoCategory;
use App\Models\MongoFeature;
use App\Models\MongoFollowItem;
use App\Models\MongoItem;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryFeatureItemController extends Controller
{

    public function itemEditAdmin($item_id)
    {
        $item = MongoItem::find($item_id);
        $feature = MongoFeature::find($item->feature);
        $parent_itmes = null;
        if (isset($feature->parent_id)) {
            $parent_itmes = $feature->parent->allItems;
        }

        //اینو گذاشتم تگ هایی که نظرات صفحه آخر گذاشتم رو بفهمم
        $comments = app(IndexController::class)->getMainComments($item->category_id, $item->id, 'null');
        $get_top_comments_data = app(IndexController::class)->selectTopComments($comments, 1);
        $comments = $get_top_comments_data['comments'];
        $links_from_editor = [];
        foreach ($comments as $comment) {
            // استخراج لینک‌ها از editor
            if (!empty($comment->editor)) {
                $dom = new DOMDocument();
                @$dom->loadHTML(mb_convert_encoding($comment->editor, 'HTML-ENTITIES', 'UTF-8'));
                foreach ($dom->getElementsByTagName('a') as $aTag) {
                    $links_from_editor[] = trim($aTag->textContent);
                }
            }
        }

        return view('item.admin.edit', compact('item', 'parent_itmes', 'links_from_editor'));
    }

    public function getItemsAdmin($id)
    {
        $feature = MongoFeature::find($id);
        $parent_itmes = null;
        if (isset($feature->parent_id)) {
            $parent_itmes = $feature->parent->allItems;
        }
        $items =  MongoItem::where('feature_id', $id)->orderBy('created_at', 'desc')->paginate(100);
        return view('admin.featureItems.index', compact('feature', 'items', 'parent_itmes'));
    }


    public function storeItemAdmin(Request $request)
    {
        $request->validate([
            'title' => 'max:255|required',
            'title_en' => 'max:255|required',
            'slug' => 'max:255|required',
        ], [
            'title.max' => 'نام آیتم نباید از 255 کلمه بیشتر باشد',
            'title_en.max' => 'نام انگلیسی آیتم نباید از 255 کلمه بیشتر باشد',
            'slug.max' => 'اسلاگ نباید از 255 کلمه بیشتر باشد',
        ]);

        $item = new MongoItem();
        $item->title = $request->title;

        $feature = MongoFeature::find($request->feature_id);
        $category = MongoCategory::find($request->category_id);

        if (isset($feature) && isset($category)) {
            $item->feature_id = $feature->id;
            $item->category_id = $category->id;
        }

        $item->title_en = $request->title_en;
        $item->slug = $request->slug;
        if ($request->parent_id) {
            $item->parent_id = $request->parent_id;
        }

        if ($request->title_in_rtable) {
            $item->title_in_rtable = $request->title_in_rtable;
        }
        if ($request->desc_in_rtable) {
            $item->desc_in_rtable = $request->desc_in_rtable;
        }
        if ($request->desc_in_rtable_editor) {
            $item->desc_in_rtable_editor = $request->desc_in_rtable_editor;
        }

        if ($request->title_in_comment) {
            $item->title_in_comment = $request->title_in_comment;
        }
        if ($request->desc_in_comment) {
            $item->desc_in_comment = $request->desc_in_comment;
        }
        if ($request->desc_in_comment_editor) {
            $item->desc_in_comment_editor = $request->desc_in_comment_editor;
        }

        if ($request->title_in_ads) {
            $item->title_in_ads = $request->title_in_ads;
        }
        if ($request->desc_in_ads) {
            $item->desc_in_ads = $request->desc_in_ads;
        }
        if ($request->desc_in_ads_editor) {
            $item->desc_in_ads_editor = $request->desc_in_ads_editor;
        }

        $item->status = (int)$request->status;


        $similar_search = $request->title . ' ' . $request->title_en;
        $item->similar_search = $similar_search;

        $item->save();

        $update_again = 0;
        $full_title = $item->withParentsTitle();
        if ($item->title != $full_title) {
            $item->full_title = $full_title;
            $similar_search .= $full_title;
            $update_again = 1;
        }
        $parent_url = $this->getparentUrl($item);
        if ($parent_url != null) {
            $wpu = '/' . $category->slug . '?s=1&' . $parent_url;
            $item->with_parent_url = $wpu;
            $update_again = 1;
        }
        if ($update_again) {
            $item->update();
        }

        return back()->with('success', 'آیتم با موفقیت ایجاد شد');
    }

    private function getparentUrl($item)
    {
        $url = null;
        $feature = $item->feature;
        if (isset($feature) && $feature->is_in_filter_rtable) {
            if ($item->parent_id != null) {
                $url .= $this->getparentUrl($item->parent) . "&" . $feature->slug . "=" . $item->slug;
            } else {
                $url = $feature->slug . "=" . $item->slug;
            }
        }
        return $url;
    }

    public function updateItemAdmin(Request $request, $item_id)
    {
        $request->validate([
            'title' => 'max:255|required',
            'title_en' => 'max:255|required',
        ], [
            'title.max' => 'نام آیتم نباید از 255 کلمه بیشتر باشد',
            'title_en.max' => 'نام انگلیسی آیتم نباید از 255 کلمه بیشتر باشد',
        ]);

        $item = MongoItem::find($item_id);
        $item->title = $request->title;
        $item->title_en = $request->title_en;
        $item->similar_search = $request->similar_search;

        if ($request->slug) {
            $item->slug = $request->slug;
        }
        if ($request->parent_id) {
            $item->parent_id = $request->parent_id;
        }
        if ($request->crl_price_url) {
            $item->crl_price_url = $request->crl_price_url;
        }

        if ($request->title_in_rtable) {
            $item->title_in_rtable = $request->title_in_rtable;
        }
        if ($request->desc_in_rtable) {
            $item->desc_in_rtable = $request->desc_in_rtable;
        }
        if ($request->desc_in_rtable_editor) {
            $item->desc_in_rtable_editor = $request->desc_in_rtable_editor;
        }

        if ($request->title_in_comment) {
            $item->title_in_comment = $request->title_in_comment;
        }
        if ($request->desc_in_comment) {
            $item->desc_in_comment = $request->desc_in_comment;
        }
        if ($request->desc_in_comment_editor) {
            $item->desc_in_comment_editor = $request->desc_in_comment_editor;
        }

        if ($request->title_in_ads) {
            $item->title_in_ads = $request->title_in_ads;
        }
        if ($request->desc_in_ads) {
            $item->desc_in_ads = $request->desc_in_ads;
        }
        if ($request->desc_in_ads_editor) {
            $item->desc_in_ads_editor = $request->desc_in_ads_editor;
        }

        $item->status = (int)$request->status == 1 ? 1 : 0;

        $item->update();

        $update_again = 0;
        $full_title = $item->withParentsTitle();
        if ($item->title != $full_title) {
            $item->full_title = $full_title;
            $update_again = 1;
        }
        $parent_url = $this->getparentUrl($item);
        if ($parent_url != null) {
            $category = $item->category;
            $wpu = '/' . $category->slug . '?s=1&' . $parent_url;
            $item->with_parent_url = $wpu;
            $update_again = 1;
        }
        if ($update_again) {
            $item->update();
        }

        return back()->with('success', 'آیتم با موفقیت ویرایش شد');
    }

    public function destroyItemAdmin($item_id)
    {
        $item = MongoItem::find($item_id);
        $item->delete();
        return back()->with('success', 'آیتم حذف شد!');
    }

    public function itemResetSuggests(Request $request)
    {
        $item = MongoItem::find($request->item_id);
        $item->unset('suggest_items');
        $items = MongoItem::where('parent_id', $item->id)->get();
        foreach ($items as $i) {
            $i->unset('suggest_items');
        }
        return back()->with('success', 'آیتم های پیشنهادی ریست شد');
    }

    public function fChildrenByItem($feature_id, $item_id = null)
    {
        $feature = CategoryFeature::find($feature_id);
        $child = $feature->children->first();
        if (!isset($child)) {
            return;
        }
        $items = $child->items->where('parent_id', $item_id);

        $child_slug = "#" . $child->slug;

        $allchildren = collect();

        $allchildren = $this->addChild($allchildren, $feature);

        return response()->json(['child_items' => $items, 'child_slug' => $child_slug, 'all_child' => $allchildren], 200);
    }
    private function addChild($allchildren, $feature)
    {
        if ($feature->children->first() != null) {
            $allchildren->add($feature->children->first());
            $this->addChild($allchildren, $feature->children->first());
        }
        return $allchildren;
    }


    //for 1 = ads
    //for 2 = group
    //for 3 = comment
    public function searchFeatureItem($for, $feature_id, $value = null)
    {
        if (!isset($value)) {
            return '<li class="px-3 pt-2 pb-3 fea-item-result-a radius-10 text-center">جستجو کنید ...</li>';
        }
        $feature = CategoryFeature::find($feature_id);

        if ($for == 1 && $feature->is_in_filter_ad) {
            $items = $feature->items->where('status', 1);
        } elseif ($for == 2 || $for == 3) {
            $items = $feature->items->where('status', 1);
        }
        foreach ($items as $key => $item) {
            if (!Str::contains($item->withParentsTitle(), Str::lower($value)) && !Str::contains($item->similar_search, Str::lower($value)) && !Str::contains($item->title_en, Str::lower($value))) {
                $items->forget($key);
            }
        }

        $items = $items->take(15);

        echo '<div class="list-group">';
        if ($items->count() > 0) {
            foreach ($items as $c) {
                if ($for == 1) {
                    echo '<a class="decor-none" href="' . $c->withParentsAdvertiseUrl() . '">
                    <li class="px-3 pt-2 pb-3 fea-item-result-a radius-10">
                    ' . $c->withParentsTitle() . '
                    </li>
                    </a>';
                } elseif ($for == 2) {
                    echo '<a class="decor-none" href="' . $c->withParentsRtableUrl() . '">
                    <li class="px-3 pt-2 pb-3 fea-item-result-a radius-10">
                    ' . $c->withParentsTitle() . '
                    </li>
                    </a>';
                } elseif ($for == 3) {
                    echo '<a class="decor-none" href="' . $c->withParentsCommentUrl() . '">
                    <li class="px-3 pt-2 pb-3 fea-item-result-a radius-10">
                    ' . $c->withParentsTitle() . '
                    </li>
                    </a>';
                }
            }
        }
        if ($items->count() == 0) {
            echo '<p class="p-3 text-center">نتیجه ای پیدا نشد</p>';
        }
        echo '</div>';
    }

    public function hotItemsAdmin()
    {
        $notUsers = [
            'ali_zamani',
            'tondiran',
            'saipa.savaran',
            'bahrami',
            'benz',
            'mohammad',
            'becharkh',
            'camper',
            'safarland',
            'lamari_club',
            'carin',
            'baghban',
            'karyab',
            'atighe',
            'amirh',
            'sara',
            'moeinzare',
            'ehsanam',
            'alii',
            'moein',
            'b',
            'car_expert',
            'declon',
            'iman_i',
            'miladr',
            '3stare',
            'arman',
            'rezaiii'
        ];

        $followItems = MongoFollowItem::orderBy('created_at', 'desc')
            ->whereDoesntHave('user', function ($query) use ($notUsers) {
                $query->whereIn('username', $notUsers);
            })
            ->take(100)
            ->with(['user', 'item'])
            ->get();

        return view('item.admin.hot-items', compact('followItems'));
    }
}
