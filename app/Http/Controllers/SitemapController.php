<?php

namespace App\Http\Controllers;

use App\Models\Affilate;
use App\Models\MongoAdvertise;
use App\Models\MongoBlog;
use App\Models\MongoCategoryComment;
use App\Models\MongoItem;
use App\Models\MongoQuestion;
use App\Models\MongoVideo;
use DOMDocument;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{


    public function sitemap()
    {
        return response()->view('sitemap.sitemap')->header('Content-Type', 'text/xml');
    }

    public function sitemap2()
    {
        return response()->view('sitemap.sitemap2')->header('Content-Type', 'text/xml');
    }

    public function sitemap3()
    {
        return response()->view('sitemap.sitemap3')->header('Content-Type', 'text/xml');
    }

    public function sitemap4()
    {
        return response()->view('sitemap.sitemap4')->header('Content-Type', 'text/xml');
    }


    public function sitemap5()
    {
        return response()->view('sitemap.sitemap5')->header('Content-Type', 'text/xml');
    }

    public function sitemap6()
    {
        return response()->view('sitemap.sitemap6')->header('Content-Type', 'text/xml');
    }

    public function sitemap7()
    {
        return response()->view('sitemap.sitemap7')->header('Content-Type', 'text/xml');
    }


    public function statics()
    {
        return response()->view('sitemap.static')->header('Content-Type', 'text/xml');
    }

    public function rTableCategories()
    {
        $uniqueItemIds = Cache::remember('question_items', 3600, function () {
            $allItems = MongoQuestion::pluck('items')->flatten()->unique()->values()->toArray();
            return $allItems;
        });

        $items = MongoItem::with('category')
            ->whereIn('_id', $uniqueItemIds)
            ->get(['title', 'full_title', 'images', 'with_parent_url', 'category_id']);

        $items = $items->filter(function ($item) {
            return $item->category && $item->category->has_formus == 1;
        });
        return response()->view('sitemap.rcategories', compact('items'))->header('Content-Type', 'text/xml');
    }

    public function commentCategories()
    {
        $uniqueItemIds = Cache::remember('comment_items', 3600, function () {
            $allItems = MongoCategoryComment::pluck('items')->flatten()->unique()->values()->toArray();
            return $allItems;
        });

        $items = MongoItem::with('category')
            ->whereIn('_id', $uniqueItemIds)
            ->get(['title', 'full_title', 'images', 'with_parent_url', 'category_id']);

        $items = $items->filter(function ($item) {
            return $item->category && $item->category->has_comments == 1;
        });

        return response()->view('sitemap.ccategories', compact('items'))->header('Content-Type', 'text/xml');
    }

    public function adPages()
    {
        $uniqueItemIds = Cache::remember('advertise_items', 3600, function () {
            $allItems = MongoAdvertise::pluck('items')->flatten()->unique()->values()->toArray();
            return $allItems;
        });

        $items = MongoItem::with('category')
            ->whereIn('_id', $uniqueItemIds)
            ->get(['title', 'full_title', 'images', 'with_parent_url', 'category_id']);

        $items = $items->filter(function ($item) {
            return $item->category && $item->category->has_ads == 1;
        });

        return response()->view('sitemap.advertise', compact('items'))->header('Content-Type', 'text/xml');
    }

    public function blogs()
    {
        $blogs = MongoBlog::orderBy('created_at', 'desc')->where('google_index', 1)->get();
        return response()->view('sitemap.blog', compact('blogs'))->header('Content-Type', 'text/xml');
    }

    public function questions()
    {
        $questions = MongoQuestion::orderBy('created_at', 'desc')->where('google_index', 1)->get();
        return response()->view('sitemap.question', compact('questions'))->header('Content-Type', 'text/xml');
    }

    public function videos()
    {
        $videos = MongoVideo::where('status', 1)->get();
        return response()->view('sitemap.videos', compact('videos'))->header('Content-Type', 'text/xml');
    }

    public function products()
    {
        $products = Affilate::orderBy('created_at', 'desc')->where('google_index', 1)->where('status', 1)->get();
        foreach ($products as $product) {
            $product->first_p = $this->getFirstParagraph($product->body);
        }
        return response()->view('sitemap.products', compact('products'))->header('Content-Type', 'text/xml');
    }

    private function getFirstParagraph($html)
    {
        $dom = new DOMDocument();
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        $paragraphs = $dom->getElementsByTagName('p');
        if ($paragraphs->length > 0) {
            return trim($paragraphs->item(0)->textContent);
        }
        return '';
    }
}
