<?php

namespace App\Http\Controllers;

use App\Mail\ReplyToCommentMail;
use App\Models\Admin;
use App\Models\CarModel;
use App\Models\CarModelComment;
use App\Models\CarTrim;
use App\Notifications\SiteEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CarModelCommentController extends Controller
{

    public function store(Request $request)
    {

        if(!auth('user')->check()){
            abort(403);
        }

        $this->validate($request, [
            'body' => 'required',
        ], [
            'body.required' => 'متن پیام الزامی می باشد.',
        ]);


        $comment = new CarModelComment();
        $comment->model_id = $request->model_id;

        $comment->user_id = auth('user')->id();

        $comment->body = $request->body;


        if(isset($request->year_id)&& isset($request->trim_id)){
            $trim = CarTrim::find($request->trim_id);
            $comment->trim_id = $trim->id;
            $comment->year_id = $request->year_id;
        }


        if (!isset($request->parent_id) && !isset($request->reply_to_id)) {
            $comment->car_topic_category = $request->car_topic_category;
        }

        $model = CarModel::find($request->model_id);

        if (isset($request->parent_id)) {
            $comment->parent_id = $request->parent_id;

            if (isset($request->reply_to_id)) {
                $name = auth('user')->check() ? auth('user')->user()->name : $request->name;
                $comment->reply_to_id = $request->reply_to_id;
                $reply = CarModelComment::find($request->reply_to_id);
                Mail::to($reply->email())->send(new ReplyToCommentMail($model->brand->title . ' ' . $model->title, $name, route('car.page', ['brand_slug' => $model->brand->slug, 'model_slug' => $model->slug])));
            } else {
                $name = auth('user')->check() ? auth('user')->user()->name : $request->name;
                $reply = CarModelComment::find($request->parent_id);
                Mail::to($reply->email())->send(new ReplyToCommentMail($model->brand->title . ' ' . $model->title, $name, route('car.page', ['brand_slug' => $model->brand->slug, 'model_slug' => $model->slug])));
            }
        }

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $name = auth('user')->check() ? auth('user')->user()->name : $request->name;
            $admin->notify(new SiteEvent([
                'action' => $name . ' کامنتی در صفحه انجمن ' . $model->brand->title . ' ' . $model->title . ' ارسال کرد',
                'route' => route('car.page', ['brand_slug' => $model->brand->nameEn, 'model_slug' => $model->nameEn]),
            ]));
        }

        $comment->save();

        return back()->with('success', 'نظر شما با موفقیت ثبت شد');
    }
}
