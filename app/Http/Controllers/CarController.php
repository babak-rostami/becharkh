<?php

namespace App\Http\Controllers;

use App\Jobs\Item\ChangeItemPageCount;
use App\Models\Advertise;
use App\Models\MongoAdvertise;
use App\Models\ShortLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CarController extends Controller
{

    public function carPage(Request $request, $brandEn, $modelEn = null)
    {
        if (isset($modelEn)) {
            $req = route('question.index', 'car') . "?s=1&" . "brand=" . $brandEn . "&model=" . $modelEn;
        } else {
            $req = route('question.index', 'car') . "?s=1&" . "brand=" . $brandEn;
        }
        return redirect()->to($req);
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
