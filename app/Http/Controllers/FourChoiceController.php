<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\FourChoice;
use App\Models\SiteCategory;
use App\Notifications\SiteEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FourChoiceController extends Controller
{


    public function store(Request $request)
    {
        if (auth('user')->check()) {
            $user = auth('user')->user();
        } else {
            return response()->json([
                'error' => 1
            ], 401);
        }

        $fchoice = new FourChoice();
        $fchoice->user_id = $user->id;
        $fchoice->title = $request->title;
        $fchoice->true_answer = $request->true_answer;
        $fchoice->wrong_answer1 = $request->wrong_answer1;
        $fchoice->wrong_answer2 = $request->wrong_answer2;
        $fchoice->wrong_answer3 = $request->wrong_answer3;
        $fchoice->status = 0;
        $fchoice->category_id = $request->category_id;

        $fchoice->save();

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => $user->username . ' یک تست هوش با عنوان ' . $request->title . ' ایجاد کرد',
                'route' => route('user.dashboard', $user->username),
            ]));
        }

        return response()->json([
            'success' => 1
        ], 200);
    }

    public function randomRace()
    {
        $item = FourChoice::inRandomOrder()->first();
        return response()->json([
            'raceid' => $item->id,
            'user' => $item->user->username,
            'user_dashboard' => route('user.dashboard', $item->user->username),
            'user_image' => asset($item->user->thumb()),
            'title' => $item->title,
            'true_answer' => '<span class="random-span" style="cursor: pointer" onclick="allresult(' . $item->id . ',(this))">' . $item->true_answer . '</span>',
            'wrong_answer1' => '<span class="random-span" style="cursor: pointer" onclick="allresult(' . $item->id . ',(this))">' . $item->wrong_answer1 . '</span>',
            'wrong_answer2' => '<span class="random-span" style="cursor: pointer" onclick="allresult(' . $item->id . ',(this))">' . $item->wrong_answer2 . '</span>',
            'wrong_answer3' => '<span class="random-span" style="cursor: pointer" onclick="allresult(' . $item->id . ',(this))">' . $item->wrong_answer3 . '</span>',
        ]);
    }

    public function getAnswer(Request $request)
    {
        $item = FourChoice::find($request->f_id);

        if (isset($item)) {
            if ($item->true_answer == $request->selected) {
                if ($request->this_el == $request->selected) {
                    return response()->json([
                        'status' => true,
                        'message' => $this->getWinRandomMessage()
                    ]);
                } else {
                    return response()->json([
                        'status' => true,
                        'message' => $this->getLoseRandomMessage()
                    ]);
                }
            }

            if ($request->selected == $request->this_el) {
                if ($item->true_answer == $request->this_el) {
                    $item->true_click += 1;
                    $item->save();
                } elseif ($item->wrong_answer1 == $request->this_el) {
                    $item->wrong_click1 += 1;
                    $item->save();
                } elseif ($item->wrong_answer2 == $request->this_el) {
                    $item->wrong_click2 += 1;
                    $item->save();
                } elseif ($item->wrong_answer3 == $request->this_el) {
                    $item->wrong_click3 += 1;
                    $item->save();
                }
            }
        } else {
            abort(404);
        }
    }

    private function getWinRandomMessage()
    {
        $sents = array("ایول", "آفرین درست بود", "کارت درسته", "معرکه ای", "نابغه ای", "داری پیشرفت میکنی", "تو بهترینی", "خودتو دست کم نگیر", "اطلاعاتت عالیه");
        return $sents[rand(0, count($sents) - 1)];
    }

    private function getLoseRandomMessage()
    {
        $sents = array("نا امید نشو", "دوباره تلاش کن", "شکست نباید نا امیدت کنه (:", "انسان جایز الخطاست (:", "سخت بود؟", "پیشرفت یه شبه اتفاق نمیفته (;", "یه بار دیگه تلاش کن");
        return $sents[rand(0, count($sents) - 1)];
    }


    public function listAdmin()
    {
        $list = FourChoice::orderBy('id', 'desc')->get();
        return view('fourchoice.index-admin', compact('list'));
    }

    public function searchCategory($value = null)
    {
        $value = trim($value);
        if ($value != "") {
            $categories = SiteCategory::where('status', 1)->get();
            foreach ($categories as $key => $category) {
                if (!Str::contains($category->withParentsTitle(), Str::lower($value)) && !Str::contains($category->similar_search, Str::lower($value))) {
                    $categories->forget($key);
                }
            }
        } else {
            $categories = SiteCategory::where('status', 1)->get();
            $categories = $categories->take(15);
        }


        echo '<div class="list-group">';
        if ($categories->count() > 0) {
            foreach ($categories as $c) {
                echo '<li class="px-3 pt-2 pb-3 fgame-cat-result-a radius-10 cur-p" onClick="selectCategoryFGame(this,' . $c->id . ')">
                    ' . $c->withParentsTitle() . '
                    </li>';
            }
        }
        if ($categories->count() == 0) {
            echo '<p class="p-3 text-center">نتیجه ای پیدا نشد</p>';
        }
        echo '</div>';
    }
}
