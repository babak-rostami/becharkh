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
}
