<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Race;
use App\Models\RaceOption;
use App\Models\Tag;
use App\Models\TagPage;
use App\Notifications\SiteEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Morilog\Jalali\Jalalian;

class RaceController extends Controller
{

    public function index()
    {
        $races = Race::all();
        return view('race.index', compact('races'));
    }

    public function show($slug)
    {
        $race = Race::where('slug', $slug)->first();
        if (isset($race)) {
            if (!isset($_COOKIE['page_seen'])) {
                $race->seen_count += 1;
                $race->update();
            }
            return view('race.show', compact('race'));
        } else {
            abort(404);
        }
    }

    public function indexAdmin()
    {
        $races = Race::all();
        return view('race.index-admin', compact('races'));
    }

    public function createAdmin()
    {
        return view('race.create-admin');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'date' => 'required',
            'image' => 'required',
        ], [
            'title.required' => 'عنوان مسابقه را وارد کنید',
            'date.required' => 'تعداد روز مسابقه را وارد کنید',
            'image.required' => 'تصویر مسابقه را وارد کنید',
        ]);

        $race = new Race();

        if (auth('admin')->check()) {
            $race->creator_id = auth('admin')->id();
            $race->creator_class = 'admin';
        } elseif (auth('user')->check()) {
            $race->creator_id = auth('user')->id();
            $race->creator_class = 'user';
        }

        $race->title = $request->title;
        $race->slug = preg_replace('/\s+/', '-', $request->title);;
        $race->body = $request->body;

        $race->end_time = Jalalian::now()->addDays($request->date)->toCarbon()->toDateTimeString();

        if ($request->hasFile('image')) {
            $cover = $request->file('image');
            $filename = time() . '.' . $cover->getClientOriginalName();
            $race->image = $filename;
            Image::make($request->file('image'))->fit(600, 400)
                ->save("files/race/images/" . $filename, '90');
        }
        $race->save();


        if (isset($request->tags)) {
            $tags = collect();
            foreach (explode('#', $request->tags) as $t) {
                if ($t != null) {
                    $tag = Tag::where('title', $t)->first();
                    if (!isset($tag)) {
                        $createTag = new Tag();
                        $createTag->title = $t;
                        $createTag->slug = preg_replace('/\s+/', '-', $t);
                        $createTag->save();
                        $tags->add($createTag);
                    } else {
                        $tags->add($tag);
                    }
                }
            }
        }

        if (isset($tags)) {
            foreach ($tags as $tt) {
                $tagPage = new TagPage();
                $tagPage->tag_id = $tt->id;
                $tagPage->page_id = $race->id;
                $tagPage->page_class = 'race';
                $tagPage->save();
            }
        }


        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => 'مسابقه جدید با عنوان ' . $request->title . ' ایجاد شد',
                'route' => route('race.show', $race->slug),
            ]));
        }

        return back()->with('success', 'مسابقه با موفقیت ایجاد شد بعد از ثبت گزینه ها برای نمایش در سایت روی گزینه انتشار کلیک کنید.');
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
        ], [
            'title.required' => 'عنوان مسابقه را وارد کنید',
        ]);

        $race = Race::find($id);

        $race->title = $request->title;
        $race->slug = preg_replace('/\s+/', '-', $request->title);;
        $race->body = $request->body;

        if (isset($request->date)) {
            $race->end_time = Jalalian::now()->addDays($request->date)->toCarbon()->toDateTimeString();
        }

        if ($request->hasFile('image')) {
            $path = public_path('/files/rece/images/' . $race->image);
            if (File::exists($path)) {
                File::delete($path);
            }
            $cover = $request->file('image');
            $filename = time() . '.' . $cover->getClientOriginalName();
            $race->image = $filename;
            Image::make($request->file('image'))->fit(600, 400)
                ->save("files/race/images/" . $filename, '90');
        }
        $race->update();

        return back()->with('success', 'مسابقه با موفقیت ویرایش شد بعد از ثبت گزینه ها برای نمایش در سایت روی گزینه انتشار کلیک کنید.');
    }


    public function destroy($id)
    {
        $race = Race::find($id);
        $path = public_path('/files/rece/images/' . $race->image);
        if (File::exists($path)) {
            File::delete($path);
        }
        $race->delete();
        return back()->with('success', 'مسابقه با موفقیت حذف شد');
    }

    public function raceOptionsAdmin($id)
    {
        $race = Race::find($id);
        $options = $race->options;
        return view('race.option-index-admin', compact('options', 'race'));
    }

    public function optionStore(Request $request)
    {
        $this->validate($request, [
            'title' => 'required'
        ], [
            'title.required' => 'عنوان گزینه را وارد کنید'
        ]);
        $option = new RaceOption();
        $option->title = $request->title;
        $option->race_id = $request->race_id;
        $option->save();
        return back()->with('success', 'گزینه با موفقیت ایجاد شد');
    }

    public function optionUpdate(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required'
        ], [
            'title.required' => 'عنوان گزینه را وارد کنید'
        ]);
        $option = RaceOption::find($id);
        $option->title = $request->title;
        $option->update();
        return back()->with('success', 'گزینه با موفقیت ویرایش شد');
    }

    public function optionDestroy($id)
    {
        $option = RaceOption::find($id);
        $option->delete();
        return back()->with('success', 'گزینه با موفقیت حذف شد');
    }

    public function raceTrue($id)
    {
        $race = Race::find($id);
        $race->status = 1;
        $race->update();
        return back()->with('success', 'نظر سنجی با موفقیت منتشر شد');
    }

    public function raceFalse($id)
    {
        $race = Race::find($id);
        $race->status = 0;
        $race->update();
        return back()->with('success', 'نظر سنجی با موفقیت غیرفعال شد');
    }

}
