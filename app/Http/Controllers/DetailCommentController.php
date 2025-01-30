<?php

namespace App\Http\Controllers;

use App\Mail\ReplyToCommentMail;
use App\Models\Admin;
use App\Models\DetailComment;
use App\Models\ModelDatail;
use App\Notifications\SiteEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class DetailCommentController extends Controller
{

    public function store(Request $request)
    {
        $this->validate($request, [
            'body' => 'required',
            'name' => 'required',
            'email' => 'required'
        ], [
            'body.required' => 'نظر خود را وارد کنید',
            'name.required' => 'نام خود را وارد کنید',
            'email.required' => 'ایمیل خود را وارد کنید'
        ]);

        $comment = new DetailComment();

        $comment->name = $request->name;
        $comment->email = $request->email;

        $comment->body = $request->body;
        $comment->detail_id = $request->detail_id;

        if (isset($request->parent_id)) {
            $comment->parent_id = $request->parent_id;
            if (isset($request->reply_id)) {
                $comment->reply_id = $request->reply_id;

                $reply = DetailComment::find($request->reply_id);
                $detail = ModelDatail::find($request->detail_id);
                $title = " مشخصات فنی " . $detail->brand->title . " " . $detail->model->title . " ";
                Mail::to($reply->email)->send(new ReplyToCommentMail($title, $request->name, route('car.detail.show', ['brand_slug' => $detail->brand->slug, 'model_slug' => $detail->model->slug])));
            } else {
                $reply = DetailComment::find($request->parent_id);
                $detail = ModelDatail::find($request->detail_id);
                $title = " مشخصات فنی " . $detail->brand->title . " " . $detail->model->title . " ";
                Mail::to($reply->email)->send(new ReplyToCommentMail($title, $request->name, route('car.detail.show', ['brand_slug' => $detail->brand->slug, 'model_slug' => $detail->model->slug])));
            }
        }
        $comment->save();


        $detail = ModelDatail::find($request->detail_id);
        $title = " مشخصات فنی " . $detail->brand->title . " " . $detail->model->title . " ";
        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => $request->name . ' نظری در ' . $title . ' منتشر کرد',
                'route' => route('car.detail.show', ['brand_slug' => $detail->brand->slug, 'model_slug' => $detail->model->slug]),
            ]));
        }

        return back()->with('success', 'نظر شما با موفقیت ثبت شد');
    }

}
