<?php

namespace App\Http\Controllers;

use App\Jobs\DecreaseVideoSize;
use App\Jobs\MoveYVideoToFtp;
use App\Models\Admin;
use App\Models\Affilate;
use App\Models\CategoryFeatureItem;
use App\Models\MongoCategory;
use App\Models\MongoItem;
use App\Models\MongoQuestion;
use App\Models\MongoUser;
use App\Models\MongoVideo;
use App\Models\SiteCategory;
use App\Models\User;
use App\Models\Video;
use App\Models\VideoFeatureValue;
use App\Models\VideoYoutubeFormat;
use App\Notifications\SiteEvent;
use App\Services\Admin\AdminNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Throwable;

class VideoController extends Controller
{

    public function show($category_slug, $video_slug = null, $random_id = null)
    {
        if (!isset($category_slug) || !isset($video_slug) || !isset($random_id)) {
            return redirect()->route('home')->with('success', 'آدرس صفحه تغییر کرده است، از منو سایت دوباره جستجو کنید');
        }
        $slug2 = $category_slug . '/' . $video_slug . '/' . $random_id;
        $video = MongoVideo::where('slug2', $slug2)->first();
        if (!isset($video)) {
            return redirect()->route('home')->with('success', 'آدرس صفحه تغییر کرده است، از منو سایت دوباره جستجو کنید');
        }
        $category = $video->category;
        if (!isset($category)) {
            return redirect()->route('home')->with('success', 'آدرس صفحه تغییر کرده است، از منو سایت دوباره جستجو کنید');
        }
        if (isset($video->youtube_link)) {
            return redirect(route('question.index') . '?s=1');
        }

        $videos = MongoVideo::where('status', 1)->whereNotIn('id', [$video->id])->orderBy('created_at', 'desc')->take(20)->get();
        if (!isset($_COOKIE['page_seen'])) {
            $video->seen_count += 1;
            $video->update();
        }

        $compactVars = [
            'video',
            'videos',
        ];
        if (isset($video->affilate_id)) {
            $affilate = Affilate::find($video->affilate_id);
            $compactVars[] = 'affilate';
        }
        if (isset($video->question_id)) {
            $question = MongoQuestion::find($video->question_id);
            $compactVars[] = 'question';
        }
        return view('video.show', compact(...$compactVars));
    }

    public function showEmbedb($category_slug, $video_slug = null, $random_id = null)
    {
        if (!isset($category_slug) || !isset($video_slug) || !isset($random_id)) {
            return redirect()->route('home')->with('success', 'آدرس صفحه تغییر کرده است، از منو سایت دوباره جستجو کنید');
        }
        $slug2 = $category_slug . '/' . $video_slug . '/' . $random_id;
        $video = MongoVideo::where('slug2', $slug2)->first();
        if (!isset($video)) {
            return redirect()->route('home')->with('success', 'آدرس صفحه تغییر کرده است، از منو سایت دوباره جستجو کنید');
        }
        return view('video.embedb', compact('video'));
    }

    public function downloadFormat(Request $request)
    {
        //if video is downloading and user refresh the page dont  allow to download again
        $cache_key = $request->video_id . "downloading";
        while (Cache::has($cache_key)) {
            sleep(3);
        }
        $video_id = explode('-', $request->video_id)[0];
        $video = MongoVideo::find($video_id);
        $format = $video->defaultYVFormat();
        if (!isset($format)) {
            return response()->json([
                "error" => "Video Not Found!",
            ], 404);
        }
        if ($format['file_path'] != null) {
            return response()->json([
                "url" => $video->yformatVideoPath(),
            ], 200);
        }
        if ($format['filename'] != null) {
            $fileName = $format['filename'];
        } else {
            $fileName = Str::limit($video->slug, 15, '-') . rand(1000, 9999) . time() . '.mp4';
        }

        $video_format_id = $format['format_id']; // video only
        $audio_format_id = '140'; // audio only
        $path = 'files/yfiles/';
        $outputDir = public_path($path);

        // for main video
        $filePath = $outputDir . $fileName;

        Cache::put($cache_key, 1, now()->addMinutes(1));
        $download_merge_process = new Process([
            'yt-dlp',
            '-f',
            $video_format_id . '+' . $audio_format_id,
            '-o',
            $filePath,
            $video->youtube_link
        ]);
        try {
            // Download and merge video and audio
            $download_merge_process->start();
            $download_merge_process->wait();

            Cache::forget($cache_key);
            if ($download_merge_process->isSuccessful()) {
                $video->updateFormat($video_format_id, null, null, $path . $fileName, $fileName);
                dispatch(new MoveYVideoToFtp($video->id))->onQueue('becharkhsite')->delay(now()->addSeconds(30));
                return response()->json([
                    "url" => $video->yformatVideoPath(),
                ], 200);
            } else {
                return response()->json([
                    "error" => 'خطا در دریافت ویدیو',
                ], 503);
            }
        } catch (ProcessFailedException $exception) {
            Cache::forget($cache_key);
            return response()->json([
                "error" => "خطا در دریافت فایل!",
            ], 404);
        }

        return response()->json(['success' => $request->video_id . $request->format_id], 200);
    }

    public function create(Request $request, $category_slug = null)
    {
        $categories = MongoCategory::where('status', 1)->get();

        $categories = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'title' => $category->title,
                'slug' => $category->slug,
                'p_id' => $category->parent_id,
                'ss' => $category->similar_search
            ];
        });
        return view('video.create', compact('categories'));
    }

    public function edit($id)
    {
        $video = MongoVideo::find($id);
        $user = auth('user')->user();
        if ($user->id != $video->user_id) {
            return back()->with('success', 'دسترسی ندارید');
        }
        $categories = Cache::rememberForever('categories', function () {
            return MongoCategory::where('status', 1)->get();
        });
        $category = $categories->find($video->category_id);
        $cfeatures = $category->features()->where('is_in_filter_rtable', 1);
        $allItems = MongoItem::where('status', 1)->get();
        $videoFeatueItems = $video->getItems();
        $citems = collect();
        foreach ($cfeatures as $ffr) {
            $citems = $citems->merge($allItems->where('feature_id', $ffr->id));
        }
        $categories = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'title' => $category->title,
                'slug' => $category->slug,
                'p_id' => $category->parent_id
            ];
        });
        $cfeatures = $cfeatures->map(function ($f) {
            return [
                'id' => $f->id,
                'p_id' => $f->parent_id,
                'title' => $f->title,
                'slug' => $f->slug,
                'i_count' => $f->select_items_count
            ];
        });
        $citems = $citems->map(function ($i) {
            return [
                'id' => $i->id,
                'f_id' => $i->feature_id,
                'p_id' => $i->parent_id,
                'title' => $i->title,
                'e_title' => $i->title_en,
                'slug' => $i->slug,
            ];
        });
        $videoFeatueItems = $videoFeatueItems->map(function ($fi) {
            return [
                'f_id' => $fi->feature_id,
                'i_id' => $fi->id,
            ];
        })->values();

        return view('video.edit', compact('user', 'categories', 'video', 'category', 'cfeatures', 'citems', 'videoFeatueItems'));
    }

    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'title' => 'required',
                'video_id' => 'required',
                'description' => 'required',
            ],
            [
                'title.required' => 'عنوان ویدیو را وارد کنید',
                'video_id.required' => 'ویدیو آپلود نشده است',
                'description.required' => 'توضیحات کوتاه درباره ویدیو را تکمیل کنید',
            ]
        );

        $user = auth('user')->user();

        if (!isset($user)) {
            return back()->with('success', 'وارد حساب کاربری خود شوید');
        }

        $category = MongoCategory::find($request->category_id);
        $video = MongoVideo::find($request->video_id);
        if (!isset($video) || !isset($category)) {
            return back()->with('success', 'خطایی رخ داد');
        }

        $video->status = 1;
        $video->title = $request->title;
        $video->description = $request->description;

        if (!isset($video->slug2) || $video->slug2 == null) {
            $slug = preg_replace('~[^\pL\d]+~u', '-', $request->title);
            $slug2 = $this->createVideoSlug($category->slug, $slug);
            $video->slug2 = $slug2;
        } else {
            $slug = $video->slug2;
        }

        $videoImageUser = $user;
        if ($request->hasFile('image')) {
            $cover = $request->file('image');
            $path = 'video/images/' . $category->slug . '/' . $videoImageUser->username . '/';

            if (isset($video->attributes['image'])) {
                $baseFilename = explode('.webp', $video->attributes['image'])[0];
                $baseFilenamearr = explode($path, $baseFilename);
                if (isset($baseFilenamearr[1])) {
                    $baseFilename = $baseFilenamearr[1];
                } else {
                    $baseFilename = Str::limit($slug, 10, '-') . time();
                }
            } else {
                $baseFilename = Str::limit($slug, 10, '-') . time();
            }

            //main image
            $filename = $baseFilename . '.webp';
            $this->uploadAndResizeImage($cover, $path, $filename, 100, 0);
            //thum image
            $filename2 = $baseFilename . '2.webp';
            $this->uploadAndResizeImage($cover, $path, $filename2, 90, 1);

            $video->image = $path . $filename;
        }

        $changeStatus = 0;

        if ($category->status) {
            $cfeatures = $category->features()->where('is_in_filter_rtable', 1);
            $allItems = MongoItem::where('status', 1)->get();
            $citems = collect();
            foreach ($cfeatures as $ffr) {
                $citems = $citems->merge($allItems->where('feature_id', $ffr->id));
            }

            $items = [];
            $items_title = [];
            $last_items = $video->items ?? [];

            $cfeatures = $cfeatures->where('parent_id', null);
            while (count($cfeatures) > 0) {
                foreach ($cfeatures as $key => $fea) {
                    //new items for this fea
                    $new_i_inp_name = $fea->id . '-';
                    $nfiInputs = collect($request->all())->filter(function ($value, $key) use ($new_i_inp_name) {
                        return Str::startsWith($key, $new_i_inp_name);
                    });
                    if ($fea->select_items_count == 1) {
                        $newItemAdded = 0;
                        // if new item added handle it here
                        if (isset($nfiInputs)) {
                            foreach ($nfiInputs as $nfi) {
                                $nifid = explode('-', $nfi)[1];
                                $nifTitle = explode('-', $nfi)[0];
                                if ($request[$fea->slug] == $nifid) {
                                    $newItem = new MongoItem();
                                    $newItem->title = $nifTitle;
                                    $newItem->feature_id = $fea->id;
                                    $newItem->slug = $nifTitle . rand(100000, 999999);
                                    $newItem->status = 0;
                                    $newItem->save();
                                    $changeStatus = 1;

                                    $items[] = $newItem->id;

                                    $newItemAdded = 1;
                                }
                            }
                        }
                        if ($newItemAdded == 0) {
                            $it = $citems->where('id', $request[$fea->slug])->first();
                            if (!in_array($request[$fea->slug], $last_items)) {
                                if (isset($it)) {
                                    $iparent = $it->parent_id ?? null;
                                    $addItem = 0;
                                    if ($iparent != null) {
                                        if (in_array($iparent, $items)) {
                                            $addItem = 1;
                                        }
                                    } else {
                                        $addItem = 1;
                                    }
                                    if ($addItem) {
                                        $items[] = $it->id;
                                        $items_title[] = $it->full_title ?? $it->title;
                                    }
                                }
                            } else {
                                $items[] = $it->id;
                                $items_title[] = $it->full_title ?? $it->title;
                            }
                        }
                    } else {
                        $fisArray = $request[$fea->slug];
                        if ($fisArray) {
                            $fisArrays = json_decode("[$fisArray]")[0];
                            if (count($fisArrays) > $fea->select_items_count) {
                                break;
                            }

                            if (isset($nfiInputs)) {
                                foreach ($nfiInputs as $nfi) {
                                    $nifid = explode('-', $nfi)[1];
                                    $nifTitle = explode('-', $nfi)[0];
                                    $nikey = array_search($nifid, $fisArrays);
                                    if ($nikey !== false) {
                                        $newItem = new MongoItem();
                                        $newItem->title = $nifTitle;
                                        $newItem->feature_id = $fea->id;
                                        $newItem->slug = $nifTitle . rand(100000, 999999);
                                        $newItem->status = 0;
                                        $newItem->save();
                                        $changeStatus = 1;

                                        $items[] = $newItem->id;
                                    }
                                }
                            }

                            foreach ($fisArrays as $fi_id) {
                                $it = $citems->where('id', $fi_id)->first();
                                if (!in_array($fi_id, $last_items)) {
                                    if (isset($it)) {
                                        $iparent = $it->parent_id ?? null;
                                        $addItem = 0;
                                        if ($iparent != null) {
                                            if (in_array($iparent, $items)) {
                                                $addItem = 1;
                                            }
                                        } else {
                                            $addItem = 1;
                                        }
                                        if ($addItem) {
                                            $items[] = $it->id;
                                            $items_title[] = $it->full_title ?? $it->title;
                                        }
                                    }
                                } else {
                                    $items[] = $it->id;
                                    $items_title[] = $it->full_title ?? $it->title;
                                }
                            }
                        }
                    }
                    //now do it for children features
                    $cfeatures->forget($key);
                    $fchildren = $fea->children;
                    if (count($fchildren) > 0) {
                        foreach ($fchildren as $key => $chf) {
                            $cfeatures->add($chf);
                        }
                    }
                }
            }
        }

        if (count($items) > 0) {
            $video->items = $items;
        }
        if (count($items_title) > 0) {
            $video->items_title = $items_title;
        }

        if ($changeStatus) {
            if ($video->status == 1) {
                $video->status = 0;
            } elseif ($video->status == 3) {
                $video->status = 2;
            }
        }

        $video->update();

        AdminNotificationService::send($user->username . ' ویدیو ' . $video->title . ' را منتشر کرد', route('video.show', $video->slug2));

        $cookieName = 'videos_count';
        $cookie = cookie()->forget($cookieName);
        return redirect()->route('user.dashboard.edit', 'video')->with('success', 'ویدیو با موفقیت منتشر شد')->withCookie($cookie);
    }

    public function createAdmin(Request $request, $category_slug = null)
    {
        $categories = MongoCategory::where('status', 1)->get();
        $categories = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'title' => $category->title,
                'slug' => $category->slug,
                'p_id' => $category->parent_id,
                'ss' => $category->similar_search
            ];
        });
        return view('video.admin.create', compact('categories'));
    }

    public function storeAdmin(Request $request)
    {
        $this->validate(
            $request,
            [
                'title' => 'required',
                'video_id' => 'required',
                'description' => 'required',
            ],
            [
                'title.required' => 'عنوان ویدیو را وارد کنید',
                'video_id.required' => 'ویدیو آپلود نشده است',
                'description.required' => 'توضیحات کوتاه درباره ویدیو را تکمیل کنید',
            ]
        );

        $category = MongoCategory::find($request->category_id);
        $video = MongoVideo::find($request->video_id);
        if (!isset($video) || !isset($category)) {
            return back()->with('success', 'خطایی رخ داد');
        }

        $video->status = 1;
        $video->title = $request->title;
        $video->description = $request->description;
        $video->google_index = 1;

        if (!isset($video->slug2) || $video->slug2 == null) {
            $slug = preg_replace('~[^\pL\d]+~u', '-', $request->title);
            $slug2 = $this->createVideoSlug($category->slug, $slug);
            $video->slug2 = $slug2;
        } else {
            $slug = $video->slug2;
        }

        if ($request->hasFile('image')) {
            $cover = $request->file('image');
            $path = 'video/images/' . $category->slug . '/';

            if (isset($video->attributes['image'])) {
                $baseFilename = explode('.webp', $video->attributes['image'])[0];
                $baseFilenamearr = explode($path, $baseFilename);
                if (isset($baseFilenamearr[1])) {
                    $baseFilename = $baseFilenamearr[1];
                } else {
                    $baseFilename = Str::limit($slug, 10, '-') . time();
                }
            } else {
                $baseFilename = Str::limit($slug, 10, '-') . time();
            }

            //main image
            $filename = $baseFilename . '.webp';
            $this->uploadAndResizeImage($cover, $path, $filename, 90, 0);
            //thum image
            $filename2 = $baseFilename . '2.webp';
            $this->uploadAndResizeImage($cover, $path, $filename2, 90, 1);

            $video->image = $path . $filename;
        }

        $items = array_filter(explode(',', $request->items));
        if (count($items) > 0) {
            $video->items = $items;
        }

        $video->update();

        return redirect()->route('admin.video.index')->with('success', 'ویدیو با موفقیت منتشر شد');
    }

    public function fileStoreAdmin(Request $request)
    {
        set_time_limit(36000);
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'فایل پیدا نشد'], 404);
        }
        $requestVideo = $request->file('file');
        $disk = Storage::disk('ftp');

        $category = MongoCategory::find($request->category_id);
        if (isset($request->video_id)) {
            $video = MongoVideo::find($request->video_id);
        } else {
            $video = new MongoVideo();
            $video->status = 0;
            $video->category_id = $category->id;
        }

        $fileName = null;
        $videoPath = 'video/files/' . $category->slug . '/';

        if ($video->video_path != null) {
            $fileName = explode($videoPath, $video->video_path)[1];
            Storage::disk('ftp')->delete($video->video_path);
        } else {
            $fileName = $category->slug . '.'  . time() . '.' . $requestVideo->getClientOriginalExtension();
        }

        // It's better to use streaming (laravel 5.4+)
        $disk->putFileAs($videoPath, $requestVideo, $fileName);

        $video->video_path = $videoPath . $fileName;
        if (isset($request->video_id)) {
            $video->update();
        } else {
            $video->save();
        }

        return response()->json([
            'video_id' => $video->id,
        ]);
    }

    public function editAdmin($id)
    {
        $video = MongoVideo::find($id);
        $categories = MongoCategory::where('status', 1)->get();
        $category = $categories->find($video->category_id);
        $categories = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'title' => $category->title,
                'slug' => $category->slug,
                'p_id' => $category->parent_id
            ];
        });

        $compactVars = [
            'categories',
            'video',
            'category'
        ];

        if (isset($video->items)) {
            $items = $video->items;
            $itemIds = implode(',', $items);
            $itemSelects = MongoItem::whereIn('_id', $items)
                ->select('title')
                ->get()
                ->sortBy(function ($item) use ($items) {
                    return array_search($item->_id, $items);
                });
            $compactVars[] = 'itemIds';
            $compactVars[] = 'itemSelects';
        }

        return view('video.admin.edit', compact(...$compactVars));
    }

    public function updateAdmin(Request $request)
    {
        $this->validate(
            $request,
            [
                'title' => 'required',
                'video_id' => 'required',
                'description' => 'required',
            ],
            [
                'title.required' => 'عنوان ویدیو را وارد کنید',
                'video_id.required' => 'ویدیو آپلود نشده است',
                'description.required' => 'توضیحات کوتاه درباره ویدیو را تکمیل کنید',
            ]
        );

        $category = MongoCategory::find($request->category_id);
        $video = MongoVideo::find($request->video_id);
        if (!isset($video) || !isset($category)) {
            return back()->with('success', 'خطایی رخ داد');
        }

        if (isset($video->youtube_link)) {
            $video->status = 3;
        } else {
            $video->status = 1;
        }

        $video->category_id = $category->id;
        $video->title = $request->title;
        $video->description = $request->description;
        $video->google_index = 1;
        $video->show_in_item = (int)$request->show_in_item;

        if ($request->hasFile('image')) {
            $cover = $request->file('image');
            if ($video->imageAttr() != null) {
                $video_image = $video->imageAttr();
                $image_arr = explode('/', $video_image);
                $image_name = $image_arr[count($image_arr) - 1];

                $baseFilename = explode('.webp', $image_name)[0];

                $last_path = explode($image_name, $video_image)[0];
                if ($last_path == "video/yimages/") {
                    $path = 'video/images/' . $category->slug . '/';
                } else {
                    $path = $last_path;
                }
            } else {
                $path = 'video/images/' . $category->slug . '/';
                $baseFilename = Str::limit($video->slug, 10, '-') . time() . rand(10000, 99999);
            }

            //main image
            $filename = $baseFilename . '.webp';
            $this->uploadAndResizeImage($cover, $path, $filename, 100, 0);
            //thum image
            $filename2 = $baseFilename . '2.webp';
            $this->uploadAndResizeImage($cover, $path, $filename2, 90, 1);

            $video->image = $path . $filename;
        } else {
            $video_image = $video->imageAttr();
            $image_arr = explode('/', $video_image);
            $image_name = $image_arr[count($image_arr) - 1];
            $last_path = explode($image_name, $video_image)[0];
            if ($last_path == "video/yimages/") {
                $baseFilename = explode('.webp', $image_name)[0];
                $path = 'video/images/' . $category->slug . '/';

                $thumb_full_path_last = $last_path . $baseFilename . '2.webp';
                $thumb_full_path_new = $path . $baseFilename . '2.webp';
                $image_full_path_last = $video_image;
                $image_full_path_new = $path . $baseFilename . '.webp';

                $this->moveFtpFile($thumb_full_path_last, $thumb_full_path_new);
                $this->moveFtpFile($image_full_path_last, $image_full_path_new);

                $video->image = $image_full_path_new;
            }
        }

        $last_items2 = $video->items ?? [];
        $items = array_filter(explode(',', $request->items));
        $deleted_items = array_diff($last_items2, $items);
        $unset_items = 0;
        if (count($items) > 0) {
            $video->items = $items;
        } else {
            $unset_items = 1;
        }

        $video->update();

        if ($unset_items) {
            $video->unset('items');
        }

        if (count($items) > 0) {
            $items_model = MongoItem::find($items);
            foreach ($items_model as $itm) {
                if ($request->show_in_item == 1) {
                    $i_vids = $itm->videos ?? [];
                    if (!in_array($video->id, $i_vids)) {
                        $i_vids[] = $video->id;
                        $itm->videos = $i_vids;
                        $itm->update();
                    }
                } else {
                    $i_vids = $itm->videos ?? [];
                    if (($key = array_search($video->id, $i_vids)) !== false) {
                        unset($i_vids[$key]);
                        $itm->videos = array_values($i_vids);
                        $itm->update();
                    }
                    if (empty($i_vids)) {
                        $itm->unset('videos');
                    }
                }
            }
        }
        $d_items_model = MongoItem::find($deleted_items);
        foreach ($d_items_model as $di) {
            $di_vids = $di->videos ?? [];
            if (($key = array_search($video->id, $di_vids)) !== false) {
                unset($di_vids[$key]);
                $di->videos = array_values($di_vids);
                $di->update();
            }
            if (empty($di_vids)) {
                $di->unset('videos');
            }
        }


        if ($video->compress != 1 && $request->compress == 1) {
            dispatch(new DecreaseVideoSize($video))->onQueue('becharkhsite');
        }

        return redirect()->route('admin.video.index')->with('success', 'ویدیو با موفقیت ویرایش شد');
    }

    private function createVideoSlug($cat_slug, $slug, $random = 1)
    {
        $slug2 = $cat_slug . '/' . $slug . '/' . $random;
        $is_exist = MongoVideo::where('slug2', $slug2)->first();
        if ($is_exist) {
            return $this->createVideoSlug($cat_slug, $slug, $random + 1);
        } else {
            return $slug2;
        }
    }

    function moveFtpFile($oldPath, $newFullPath)
    {
        $disk = Storage::disk('ftp');
        $disk->copy($oldPath, $newFullPath);
        $disk->delete($oldPath);
    }

    public function fileStore(Request $request)
    {
        set_time_limit(3600);
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'فایل پیدا نشد'], 404);
        }
        $requestVideo = $request->file('file');
        $videoSize = $requestVideo->getSize() / 1024 / 1024;
        if ($videoSize > 500) {
            return response()->json(['error' => 'حداکثر حجم مجاز ویدیو 500 مگابایت می باشد'], 403);
        }
        $newVideo = json_decode($this->howManyDiamondNeedForSize($videoSize), true);
        $moneyNeedNew = $newVideo["d_count"];

        $disk = Storage::disk('ftp');
        $user = auth('user')->user();

        $category = MongoCategory::find($request->category_id);
        if (isset($request->video_id)) {
            $video = MongoVideo::find($request->video_id);
            $lastVideo = json_decode($this->howManyDiamondNeedForSize($video->max_size - 5), true);

            $moneyNeedLast = $lastVideo["d_count"];
            if ($moneyNeedNew > $moneyNeedLast) {
                $moneyNeedCount = $moneyNeedNew - $moneyNeedLast;
                if ($user->money < $moneyNeedCount) {
                    return response()->json(['error' => 'موجودی کافی ندارید'], 403);
                }
                $video->max_size = $newVideo["max_size"];
            }
        } else {
            $video = new MongoVideo();
            $video->random_id = Str::random(10);
            $video->status = 0;
            $video->category_id = $category->id;
            $video->user_id = $user->id;

            if ($moneyNeedNew > 0) {
                $moneyNeedCount = $moneyNeedNew;
                if ($user->money < $moneyNeedCount) {
                    return response()->json(['error' => 'موجودی کافی ندارید'], 403);
                }
            }
            $video->max_size = $newVideo["max_size"];
        }

        if (isset($request->video_id) && $video->user_id != $user->id) {
            return response()->json(['error' => 'دسترسی ندارید'], 403);
        }

        $fileName = null;
        $videoPath = 'video/files/' . $category->slug . '/' . $user->username . '/';

        if ($video->video_path != null) {
            $fileName = explode($videoPath, $video->video_path)[1];
            Storage::disk('ftp')->delete($video->video_path);
        } else {
            $fileName = $category->slug . '.' . $user->username . time() . '.' . $requestVideo->getClientOriginalExtension();
        }

        // It's better to use streaming (laravel 5.4+)
        $disk->putFileAs($videoPath, $requestVideo, $fileName);

        $video->video_path = $videoPath . $fileName;
        if (isset($request->video_id)) {
            $video->update();
        } else {
            $video->save();
        }
        if (isset($moneyNeedCount)) {
            $user->decreaseMoneyAmount($moneyNeedCount);
        }

        dispatch(new DecreaseVideoSize($video))->onQueue('becharkhsite');

        return response()->json([
            'video_id' => $video->id,
        ]);
    }

    private function howManyDiamondNeedForSize($size)
    {
        if ($size < 11) {
            $data = array(
                'd_count' => 0,
                'max_size' => 11
            );
            return json_encode($data);
        } else if ($size >= 11 && $size < 26) {
            $data = array(
                'd_count' => 8000,
                'max_size' => 26
            );
            return json_encode($data);
        } else if ($size >= 26 && $size < 51) {
            $data = array(
                'd_count' => 12000,
                'max_size' => 51
            );
            return json_encode($data);
        } else if ($size >= 51 && $size < 101) {
            $data = array(
                'd_count' => 18000,
                'max_size' => 101
            );
            return json_encode($data);
        } else if ($size >= 101 && $size < 176) {
            $data = array(
                'd_count' => 26000,
                'max_size' => 176
            );
            return json_encode($data);
        } else if ($size >= 176 && $size < 251) {
            $data = array(
                'd_count' => 38000,
                'max_size' => 251
            );
            return json_encode($data);
        } else if ($size >= 251 && $size < 351) {
            $data = array(
                'd_count' => 48000,
                'max_size' => 351
            );
            return json_encode($data);
        } else if ($size >= 351 && $size < 451) {
            $data = array(
                'd_count' => 56000,
                'max_size' => 451
            );
            return json_encode($data);
        } else if ($size >= 451 && $size < 501) {
            $data = array(
                'd_count' => 64000,
                'max_size' => 501
            );
            return json_encode($data);
        }
    }

    private function uploadAndResizeImage($image, $path, $filename, $quality, $thumb)
    {
        $disk = Storage::disk('ftp');

        if ($thumb == 1) {
            $resizedImage = Image::make($image)->resize(256, null, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('webp', $quality);
        } else {
            $resizedImage = Image::make($image)->encode('webp', $quality);
        }
        $disk->put($path . $filename, (string) $resizedImage);
    }

    public function getUserVideos($user_id, $id = null, $forr = null)
    {
        $user = MongoUser::find($user_id);
        $videos = $user->videos;
        $videos = $videos->map(function ($v) {
            return [
                'id' => $v->id,
                'title' => $v->title,
                'thumb' => $v->thumb(),
            ];
        });
        if ($forr == "blog") {
            $selecedList = $user->blogVideos();
            $selecedList = $selecedList->map(function ($s) {
                return [
                    'blog_id' => $s->id,
                    'video_id' => $s->videos,
                ];
            });
        } elseif ($forr == "ad") {
            $selecedList = $user->adVideos();
            $selecedList = $selecedList->map(function ($s) {
                return [
                    'ad_id' => $s->id,
                    'video_id' => $s->videos,
                ];
            });
        }
        return response()->json([
            'videos' => $videos,
            'selected_video' => $selecedList
        ], 200);
    }


    public function adminIndex()
    {
        $videos = MongoVideo::where('status', 0)->orWhere('status', 1)->orderBy('created_at', 'desc')->paginate(100);
        return view('video.admin.index', compact('videos'));
    }

    public function adminDestroy($id)
    {
        $video = MongoVideo::find($id);
        $disk = Storage::disk('ftp');
        if ($video->isVideoFromYoutue()) {
            $format = $video->defaultYVFormat();
            if ($format['file_path'] != null) {
                $disk->delete($format['file_path']);
            }
        } else {
            $disk->delete($video->video_path);
        }

        $video_image = $video->imageAttr();
        $image_arr = explode('/', $video_image);
        $image_name = $image_arr[count($image_arr) - 1];
        $baseFilename = explode('.webp', $image_name)[0];
        $last_path = explode($image_name, $video_image)[0];
        $thumb_path = $last_path . $baseFilename . '2.webp';

        $disk->delete($video_image);
        $disk->delete($thumb_path);

        $affilates = Affilate::where('video_id', $video->id)->get();
        foreach ($affilates as $affilate) {
            $affilate->unset('video_id');
            $affilate->unset('video_path');
        }

        $video->delete();

        return redirect()->back()->with('success', 'ویدیو حذف شد');
    }
}
