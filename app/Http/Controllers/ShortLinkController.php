<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\CarModel;
use App\Models\ModelDatail;
use App\Models\Question;
use App\Models\ShortLink;
use Illuminate\Http\Request;

class ShortLinkController extends Controller
{

    public function show($short_link)
    {
        $shortlink = ShortLink::where('short_link', $short_link)->first();
        if (isset($shortlink)) {
            if ($shortlink->link_class == 'blog') {
                $blog = Blog::find($shortlink->link_id);
                return redirect()->route('blog.show', $blog->slug);
            } elseif ($shortlink->link_class == 'detail') {
                $detail = ModelDatail::find($shortlink->link_id);
                return redirect()->route('car.detail.show', ['brand_slug' => $detail->brand->slug, 'model_slug' => $detail->model->slug]);
            } elseif ($shortlink->link_class == 'car_page') {
                $model = CarModel::find($shortlink->link_id);
                return redirect()->route('car.page', ['brand_slug' => $model->brand->slug, 'model_slug' => $model->slug]);
            } elseif ($shortlink->link_class == 'question') {
                $question = Question::find($shortlink->link_id);
                return redirect()->route('question.show', $question->slug2);
            }
        } else {
            abort(404);
        }
    }
}
