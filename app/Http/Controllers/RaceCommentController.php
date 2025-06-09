<?php

namespace App\Http\Controllers;

use App\Mail\ReplyToCommentMail;
use App\Models\Admin;
use App\Models\Race;
use App\Models\RaceComment;
use App\Notifications\SiteEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class RaceCommentController extends Controller
{

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:250',
            'email' => 'required|max:250',
            'body' => 'required',
        ], [
            'name.required' => 'نام الزامی می باشد.',
            'email.required' => 'ایمیل الزامی می باشد.',
            'body.required' => 'متن پیام الزامی می باشد.',
        ]);

        $comment = new RaceComment();
        $comment->race_id = $request->race_id;
        $comment->name = $request->name;
        $comment->email = $request->email;
        $comment->body = $request->body;

        $race = Race::find($request->race_id);

        if (isset($request->parent_id)) {
            $comment->parent_id = $request->parent_id;

            if (isset($request->reply_id)) {
                $comment->reply_id = $request->reply_id;
                $reply = RaceComment::find($request->reply_id);
                Mail::to($reply->email)->send(new ReplyToCommentMail($race->title, $request->name, route('race.show', $race->slug)));
            } else {
                $reply = RaceComment::find($request->parent_id);
                Mail::to($reply->email)->send(new ReplyToCommentMail($race->title, $request->name, route('race.show', $race->slug)));
            }
        }

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => $request->name . ' نظری در مسابقه ' . $race->title . ' ارسال کرد',
                'route' => route('race.show', $race->slug),
            ]));
        }

        $comment->save();

        return back()->with('success', 'نظر شما با موفقیت ثبت شد');
    }

}
