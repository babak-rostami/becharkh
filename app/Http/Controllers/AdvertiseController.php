<?php

namespace App\Http\Controllers;

use App\Jobs\Item\ChangeItemPageCount;
use App\Mail\EmailToUser;
use App\Models\Admin;
use App\Models\Advertise;
use App\Models\MongoAdvertise;
use App\Models\MongoAdvertiseFeatureValue;
use App\Models\MongoCategory;
use App\Models\MongoCity;
use App\Models\MongoDistrict;
use App\Models\MongoFeature;
use App\Models\MongoFollowItem;
use App\Models\MongoItem;
use App\Models\MongoProvince;
use App\Models\MongoQuestion;
use App\Models\MongoVideo;
use App\Models\RtablePageData;
use App\Models\SaveList;
use App\Models\SiteCategory;
use App\Notifications\SiteEvent;
use App\Repositories\Advertise\Mongodb\AdvertiseRepository;
use App\Repositories\Feature\Mongodb\FeatureRepository;
use App\Services\Affilate\AffilateService;
use App\Services\Item\AdditemsService;
use App\Services\Suggestion\SuggestionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class AdvertiseController extends Controller
{

    public function getAds(Request $request, SuggestionService $suggestionService, $category_slug = null)
    {
        $advertise_repository = new AdvertiseRepository();

        $title = "";
        $selectedFeatures = collect();
        $followFeature = null;
        $item = null;
        $data = new RtablePageData();

        $user = null;
        if (auth('user')->check()) {
            $user = auth('user')->user();
        }

        $meta_title = null;
        $meta_desc = null;
        $meta_desc_editor = null;

        //for filtet -- cat category and features for filter
        $category = MongoCategory::where('slug', $category_slug)->first();
        if (isset($category)) {

            app(SiteCategoryController::class)->redirectIfPageNotExist($request, $category, 'ads');

            $feature_repository = new FeatureRepository();
            $categoryFeatures = $feature_repository->getFeaturesByCategoryIdForForum($category->id);

            $featuresInUrl = $data->getFeaturesInUrl($request);
            foreach ($featuresInUrl as $fiu) {
                $fs = explode('=', $fiu)[0];
                $f = $categoryFeatures->where('slug', $fs)->first();
                if (isset($f)) {
                    $selectedFeatures->add($f);
                }
            }
            $fiu_parentIds = $selectedFeatures->filter->parent_id->pluck('parent_id');
            $fiu_Ids = $selectedFeatures->filter->id->pluck('id');
            $childFeaturesInUrl = $categoryFeatures->whereIn('id', $fiu_Ids)->whereNotIn('id', $fiu_parentIds);

            $isset_ads = 1;
            $fiuforp = null;
            $comments = collect();
            $questions = collect();
            $advertises = collect();

            $selected_items = collect();
            foreach ($selectedFeatures as $sf) {
                if (isset($sf)) {
                    $sitem = MongoItem::where('feature_id', $sf->id)->where('slug', $request[$sf->slug])->first();
                    if (isset($sitem)) {
                        $selected_items->add($sitem);
                    }
                }
            }
            foreach ($childFeaturesInUrl as $f) {
                if (isset($f)) {
                    $item = $selected_items->where('feature_id', $f->id)->where('slug', $request[$f->slug])->first();
                    if (isset($item)) {
                        $tempAds = $advertise_repository->getAdvertiseByItemIdByPaginate($category->id, $item->id, 20);
                        if (count($advertises) > 0) {
                            $ids = $advertises->pluck('id');
                            $advertises = $tempAds->whereIn('id', $ids);
                        } else {
                            $advertises = $tempAds;
                        }
                        //follow button
                        if ($f->has_follow) {
                            $followFeature = $f;
                        }
                    }
                }
            }

            if (isset($followFeature) && isset($item)) {
                if ($followFeature->item_is_in_title) {
                    if ($category->is_cat_in_title) {
                        $ct = $category->full_title ?? $category->title;
                        $ctitle = trim($ct) == "" ? "" : $ct . " ";
                    } else {
                        $ctitle = "";
                    }
                    $title = $ctitle . ($item->full_title  ?? $item->title);
                }
                if ($item->title_in_ads) {
                    $meta_title = str_replace("*", $title, $item->title_in_ads);
                } else {
                    if ($category->title_in_ads) {
                        $meta_title = str_replace("*", $title, $category->title_in_ads);
                    } else {
                        $meta_title = "آگهی های " . $title;
                    }
                }
                if ($item->desc_in_ads) {
                    $meta_desc = str_replace("*", $title, $item->desc_in_ads);
                } else {
                    if ($category->desc_in_ads) {
                        $meta_desc = str_replace("*", $title, $category->desc_in_ads);
                    } else {
                        $meta_desc = "آکهی های با موضوع " . $title;
                    }
                }
                if ($item->desc_in_ads_editor) {
                    $meta_desc_editor = str_replace("*", $title, $item->desc_in_ads_editor);
                } else {
                    if ($category->desc_in_ads_editor) {
                        $meta_desc_editor = str_replace("*", $title, $category->desc_in_ads_editor);
                    }
                }
                // if ($user) {
                //     $follow = MongoFollowItem::where('item_id', $item->id)->where('user_id', $user->id)->first();
                //     if (isset($follow)) {
                //         $is_follow = 1;
                //     } else {
                //         $is_follow = 0;
                //     }
                // }
                $advertises->appends(request()->query());
            } else {
                $cat_title = $category->full_title ?? $category->title;
                if (isset($category->title_in_ads_noi)) {
                    $meta_title = str_replace("*", $cat_title, $category->title_in_ads_noi);
                } else {
                    if ($category->title_in_ads) {
                        $meta_title = str_replace("*", $cat_title, $category->title_in_ads);
                    } else {
                        $meta_title = "آگهی های " . $cat_title;
                    }
                }
                if ($category->desc_in_ads_noi) {
                    $meta_desc = str_replace("*", $cat_title, $category->desc_in_ads_noi);
                } else {
                    if ($category->desc_in_ads) {
                        $meta_desc = str_replace("*", $cat_title, $category->desc_in_ads);
                    } else {
                        $meta_desc = "آکهی های با موضوع " . $cat_title;
                    }
                }

                $advertises = $advertise_repository->getAdvertiseByCategoryIdByPaginate($category->id, 20);
            }

            $suggests = $suggestionService->suggest($category, $item);
            if (isset($suggests['items'])) {
                $suggetItems = $suggests['items'];
            } else {
                $suggestCats = $suggests['cats'];
            }

            if (isset($item)) {
                $hotQuestions = $this->getHotQuestions($category, $item);
            } else {
                $hotQuestions = $this->getHotQuestions($category, null);
            }

            if ($advertises->isEmpty()) {
                $isset_ads = 0;
            }

            if (isset($item)) {
                $tab_category = $item->category;
                if ($tab_category->has_comments) {
                    $comment_page = $item->withParentsCommentUrl();
                }
                if ($tab_category->has_forums) {
                    $forum_page = $item->withParentsForumUrl();
                }
                if ($tab_category->has_blogs) {
                    $blog_page = $item->withParentsBlogUrl();
                }
                // if (isset($item->videos)) {
                //     $ivids = MongoVideo::find($item->videos)->shuffle()->first();
                //     if (isset($ivids)) {
                //         $item_video = $ivids;
                //     }
                // }
            } else {
                if ($category->has_comments) {
                    $comment_page = route('question.index', $category->slug) . '?s=1';
                }
                if ($category->has_forums) {
                    $forum_page = route('question.index', $category->slug);
                }
                if ($category->has_blogs) {
                    $blog_page = route('blog.index', $category->slug);
                }
            }

            $affilateService = new AffilateService();
            $affilates = $affilateService->suggestForAds($category, $item);

            $features = $category->features();
            $currentQueryParams = $request->query();

            // $hot_pages = Cache::get('hot_pages');

            $compactVars = [
                'item',
                'meta_title',
                'meta_desc',
                'meta_desc_editor',
                'data',
                'isset_ads',
                'advertises',
                'title',
                'category',
                'currentQueryParams',
                'selected_items',
            ];
            // if (isset($item_video)) {
            //     $compactVars[] = 'item_video';
            // }
            if (isset($features)) {
                $compactVars[] = 'features';
            }
            if (isset($affilates)) {
                $compactVars[] = 'affilates';
            }
            if (isset($forum_page)) {
                $compactVars[] = 'forum_page';
            }
            if (isset($comment_page)) {
                $compactVars[] = 'comment_page';
            }
            if (isset($blog_page)) {
                $compactVars[] = 'blog_page';
            }
            if (isset($suggetItems)) {
                $compactVars[] = 'suggetItems';
            } elseif (isset($suggestCats)) {
                $compactVars[] = 'suggestCats';
            }
            return view('advertise.index', compact(...$compactVars));
        } else {
            $suggests = $suggestionService->suggest();
            $suggestCats = $suggests['cats'];

            $advertises = $advertise_repository->getlastAdvertiseByPaginate(20);

            $reqs = route('ads.index');

            $forum_page = route('question.index');
            $comment_page = route('question.index') . '?s=1';
            $blog_page = route('blog.index');
            $isset_ads = 1;

            return view('advertise.index', compact('suggestCats', 'forum_page', 'blog_page', 'comment_page', 'data', 'isset_ads', 'advertises', 'title'));
        }
    }

    private function getHotQuestions($category = null, $item = null)
    {
        if (isset($category)) {
            if (isset($item)) {
                $hotRelated = MongoQuestion::where('items', $item->id)->take(5)->get();
            } else {
                $hotRelated = MongoQuestion::where('category_id', $category->id)->take(3)->get();
            }
        } else {
            $hotRelated = MongoQuestion::random(3);
        }
        $hotOther = MongoQuestion::random(6);

        $merged = $hotRelated->merge($hotOther)->unique('id')->take(5);
        return $merged;
    }

    private function getHotVideos($category = null, $item = null)
    {
        if (isset($category)) {
            if (isset($item)) {
                $hotRelated = MongoVideo::where('items', $item->id)->take(5)->get();
            } else {
                $hotRelated = MongoVideo::where('category_id', $category->id)->take(3)->get();
            }
        } else {
            $hotRelated = MongoVideo::random(3);
        }
        $hotads = MongoVideo::random(10, ['advertises' => 'notnull']);
        $hotOther = MongoVideo::random(20);

        $merged = $hotRelated->merge($hotads)->merge($hotOther)->unique('id')->take(25);
        return $merged;
    }

    public function paginateC(
        $items,
        $perPage = 15,
        $page = null,
        $baseUrl = null,
        $options = []
    ) {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ?
            $items : Collection::make($items);

        $lap = new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            $options
        );

        if ($baseUrl) {
            $lap->setPath($baseUrl);
        }

        return $lap;
    }

    public function adSave($id)
    {
        $saveList = new SaveList();
        $saveList->user_id = auth('user')->id();
        $saveList->advertise_id = $id;

        $saveList->save();

        return back()->with('success', 'آگهی با موفقیت ذخیره شد');
    }

    public function adRemoveFromSave($advertise_id)
    {
        $save_item = SaveList::where('user_id', auth('user')->id())->where('advertise_id', $advertise_id)->first();
        $save_item->delete();

        return back()->with('success', 'آگهی از لیست علاقه مندی ها حذف شد');
    }

    public function savelist()
    {
        $saveItems = auth('user')->user()->saveAdvertises();
        return view('user.savelist', compact('saveItems'));
    }


    public function adminAll($cat_slug = null)
    {
        if ($cat_slug != null) {
            $category = MongoCategory::where('slug', $cat_slug)->first();
            $advertises = $category->advertises->take(250);
            return view('advertise.all-for-admin', compact('advertises', 'category'));
        } else {
            $advertises = MongoAdvertise::orderBy('created_at', 'desc')->paginate(50);
            return view('advertise.all-for-admin', compact('advertises'));
        }
    }


    public function create(Request $request)
    {
        $user = auth('user')->user();
        if ($user->email_actived != 1) {
            return redirect()->route('user.dashboard.edit')->with('success', 'برای ثبت آگهی ایمیل خود را تایید کنید');
        }
        $categories = MongoCategory::where('status', 1)->where('is_active', 1)->get();
        $categories = $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'title' => $category->title,
                'slug' => $category->slug,
                'p_id' => $category->parent_id,
                'ss' => $category->similar_search,
                'price_tag' => $category->cost_description
            ];
        });

        $provinces = MongoProvince::select(['name'])->get();
        $cities = MongoCity::select(['name', 'province_id'])->get();
        $districts = MongoDistrict::select(['name', 'city_id'])->get();
        $provinces = $provinces->map(function ($province) {
            return [
                'id' => $province->id,
                'name' => $province->name,
            ];
        });
        $cities = $cities->map(function ($city) {
            return [
                'id' => $city->id,
                'name' => $city->name,
                'p_id' => $city->province_id,
            ];
        });
        $districts = $districts->map(function ($district) {
            return [
                'id' => $district->id,
                'name' => $district->name,
                'c_id' => $district->city_id,
            ];
        });

        return view('advertise.create', compact('categories', 'provinces', 'cities', 'districts'));
    }

    public function payAdFromAcc($advertise_id)
    {
        $advertise = MongoAdvertise::find($advertise_id);
        $user = auth('user')->user();
        if (!isset($advertise)) {
            return back()->with('success', 'آگهی پیدا نشد!');
        }
        if ($user->id != $advertise->user_id) {
            return back()->with('success', 'دسترسی ندارید!');
        }
        if ($advertise->not_paid == 1) {
            if ($user->canCreateAd()) {
                $user->decreaseMoneyFor("advertise");
                if ($advertise->not_cat != 1 && $advertise->not_item != 1) {
                    $advertise->status = 1;
                    $advertise->update();
                }
                $advertise->unset('not_paid');
                return back()->with('success', 'آگهی با موفقیت تایید شد');
            } else {
                return back()->with('success', 'ابتدا موجودی حساب خود را افزایش دهید');
            }
        }
    }

    public function checkAdStatus($advertise_id)
    {
        $advertise = MongoAdvertise::find($advertise_id);
        $user = auth('user')->user();

        if (!isset($advertise)) {
            return back()->with('success', 'آگهی پیدا نشد!');
        }
        if ($advertise->status == 1) {
            return back()->with('success', 'آگهی تایید شده است!');
        } elseif ($advertise->status == 2) {
            if ($user->canCreateAd()) {
                $user->decreaseMoneyFor("advertise");
                $advertise->status = 1;
                $advertise->update();
                return back()->with('success', 'آگهی با موفقیت تایید شد');
            } else {
                return back()->with('success', 'در انتظار افزایش موجودی');
            }
        } elseif ($advertise->status == 3) {
            if ($user->email_actived == 1) {
                $advertise->status = 1;
                $advertise->update();
                return back()->with('success', 'آگهی با موفقیت تایید شد');
            } else {
                return back()->with('success', 'هنوز ایمیل خود را تایید نکرده اید');
            }
        } elseif ($advertise->status == 4) {
            $isMoneyEnough = 0;
            $isEmailActive = 0;
            if ($user->email_actived == 1) {
                $isEmailActive = 1;
            }
            if ($user->canCreateAd()) {
                $user->decreaseMoneyFor("advertise");
                $isMoneyEnough = 1;
            }

            if ($isMoneyEnough && $isEmailActive) {
                $advertise->status = 1;
                $advertise->update();
                return back()->with('success', 'تبریک آگهی تایید شد');
            }
            if (!$isMoneyEnough && $isEmailActive) {
                $advertise->status = 2;
                $advertise->update();
                return back()->with('success', 'در انتظار افزایش موجودی');
            }
            if ($isMoneyEnough && !$isEmailActive) {
                $advertise->status = 3;
                $advertise->update();
                return back()->with('success', 'در انتظار تایید ایمیل');
            }
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

    public function show(Request $request, $category_slug, $slug, $random, SuggestionService $suggestionService)
    {
        $category = MongoCategory::where('slug', $category_slug)->first();
        if (!isset($category)) {
            return redirect()->route('home')->with('success', 'آدرس صفحه تغییر کرده است');
        }
        $advertise = MongoAdvertise::where('category_id', $category->id)->where('slug', $slug)->where('random_id', $random)->first();
        if (!isset($advertise)) {
            return redirect()->route('home')->with('success', 'آدرس صفحه تغییر کرده است، از منو سایت دوباره جستجو کنید');
        }
        if ($advertise->status == 0) {
            return redirect()->route('ads.index')->with('success', 'آگهی در انتظار تایید می باشد');
        } elseif ($advertise->status == 2) {
            return redirect()->back()->with('success', 'آگهی در انتظار پرداخت می باشد');
        }

        $items = $advertise->items;
        $item = null;
        $user = null;
        if (auth('user')->check()) {
            $user = auth('user')->user();
        }
        if (isset($items) && count($items) > 0) {
            $item_id = $items[0];
            $item = MongoItem::find($item_id);
        }
        if ($item) {
            $tab_title = $item->full_title ?? $item->title;
            // if ($user) {
            //     $follow = MongoFollowItem::where('item_id', $item->id)->where('user_id', $user->id)->first();
            //     if (isset($follow)) {
            //         $is_follow = 1;
            //     } else {
            //         $is_follow = 0;
            //     }
            // }
        } else {
            $tab_title = $category->full_title ?? $category->title;
        }
        $suggests = $suggestionService->suggest($category, $item);
        if (isset($suggests['items'])) {
            $suggetItems = $suggests['items'];
        } else {
            $suggestCats = $suggests['cats'];
        }
        $advertises = $advertise->reletadAds();

        if ($user) {
            if ($user->id == $advertise->user_id) {
                $isAdForThisUser = 1;
            } else {
                $isAdForThisUser = 0;
            }
        } else {
            $isAdForThisUser = 0;
        }
        $adImages = $advertise->getImages();
        $adVideo = $advertise->video();
        $advertiseUser = $advertise->user;

        if (isset($item)) {
            if ($category->has_comments) {
                $comment_page = $item->withParentsCommentUrl();
            }
            if ($category->has_forums) {
                $forum_page = $item->withParentsForumUrl();
            }
            if ($category->has_blogs) {
                $blog_page = $item->withParentsBlogUrl();
            }
            if ($category->has_ads) {
                $advertise_page = $item->withParentsAdvertiseUrl();
            }
            if ($category->has_ads) {
                $advertise_page = $item->withParentsAdvertiseUrl();
            }
        } else {
            if ($category->has_comments) {
                $comment_page = route('question.index', $category->slug) . '?s=1';
            }
            if ($category->has_forums) {
                $forum_page = route('question.index', $category->slug);
            }
            if ($category->has_blogs) {
                $blog_page = route('blog.index', $category->slug);
            }
            if ($category->has_ads) {
                $advertise_page = route('ads.index', $category->slug);
            }
        }

        if (!isset($_COOKIE['page_seen'])) {
            $advertise->seen_count += 1;
            $advertise->update();
        }

        $compactVars = [
            'user',
            'category',
            'item',
            'tab_title',
            'adVideo',
            'advertises',
            'user',
            'adImages',
            'advertise',
            'isAdForThisUser',
            'advertiseUser'
        ];
        if (isset($forum_page)) {
            $compactVars[] = 'forum_page';
        }
        if (isset($comment_page)) {
            $compactVars[] = 'comment_page';
        }
        if (isset($blog_page)) {
            $compactVars[] = 'blog_page';
        }
        if (isset($advertise_page)) {
            $compactVars[] = 'advertise_page';
        }
        if (isset($suggetItems)) {
            $compactVars[] = 'suggetItems';
        } elseif (isset($suggestCats)) {
            $compactVars[] = 'suggestCats';
        }
        return view('advertise.show', compact(...$compactVars));
    }

    public function edit($id)
    {
        $advertise = MongoAdvertise::find($id);
        if (!auth('admin')->check()) {
            $user = auth('user')->user();
            if ($user->id !=  $advertise->user_id) {
                return back()->with('success', 'دسترسی ندارید');
            }
        }
        $category = MongoCategory::find($advertise->category_id);
        $cfeatures = $category->features()->where('is_in_filter_ad', 1);

        $item_cat_ids = [];
        foreach ($cfeatures as $f) {
            $item_cat_ids[] = $f->category_id;
        }
        $cat_ids = array_merge($item_cat_ids, [$category->id]);
        $cat_ids = array_unique($cat_ids);

        $citems = MongoItem::whereIn('category_id', $cat_ids)->get();
        $advertiseFeatueItems = $advertise->getItems();
        $cfeatures = $cfeatures->map(function ($f) {
            return [
                'id' => $f->id,
                'p_id' => $f->parent_id,
                'title' => $f->title,
                'slug' => $f->slug,
                'type' => $f->input_type,
                'require' => $f->is_important_in_ad,
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
        $advertiseFeatueValues = $advertise->featureValues;
        $advertiseFeatueItems = $advertiseFeatueItems->map(function ($fi) {
            return [
                'f_id' => $fi->feature_id,
                'i_id' => $fi->id,
                'type' => 0,
            ];
        })->toArray();
        $featureValuesArray = $advertiseFeatueValues->map(function ($fv) {
            return [
                'f_id' => $fv->feature_id,
                'value' => $fv->value,
                'type' => 1,
            ];
        })->toArray();
        $advertiseFeatueItems = array_merge($advertiseFeatueItems, $featureValuesArray);

        $provinces = MongoProvince::select(['name'])->get();
        $cities = MongoCity::select(['name', 'province_id'])->get();
        $districts = MongoDistrict::select(['name', 'city_id'])->get();
        $provinces = $provinces->map(function ($province) {
            return [
                'id' => $province->id,
                'name' => $province->name,
            ];
        });
        $cities = $cities->map(function ($city) {
            return [
                'id' => $city->id,
                'name' => $city->name,
                'p_id' => $city->province_id,
            ];
        });
        $districts = $districts->map(function ($district) {
            return [
                'id' => $district->id,
                'name' => $district->name,
                'c_id' => $district->city_id,
            ];
        });
        return view('advertise.edit', compact('advertise', 'category', 'provinces', 'cities', 'districts', 'advertiseFeatueItems', 'cfeatures', 'citems'));
    }

    public function updateAdmin(Request $request, $id)
    {
        $advertise = MongoAdvertise::find($id);

        $category = SiteCategory::find($request->category_id);
        if ($request->status == 1) {
            if ($category->status == 0) {
                return back()->with('success', 'برای تایید آگهی ابتدا دسته بندی را تایید کنید');
            } else {
                $advertise->status = $request->status;
            }
        } else {
            $advertise->status = $request->status;
        }

        $advertise->category_id = $category->id;
        $advertise->body = $request->body;

        $advertise->update();

        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new SiteEvent([
                'action' => auth('admin')->user()->username . '  آگهی با عنوان ' . $advertise->title . ' را ویرایش کرد (admin)',
                'route' => route('ad.show', ['category_slug' => $advertise->category->slug, 'slug' => $advertise->slug, 'random' => $advertise->random_id]),
            ]));
            if ($advertise->status == 0) {
                Mail::to($admin->email)->send(new EmailToUser('آگهی تایید نشده', 'یک آگهی با عنوان ' . $advertise->title . ' در انتظار تایید می باشد در اسرع وقت نسبت به ویرایش آن اقدام کنید'));
            }
        }

        return back()->with('success', 'آگهی با موفقیت ویرایش شد');
    }

    public function update(Request $request, $id)
    {
        set_time_limit(360);
        $advertise = MongoAdvertise::find($id);
        $is_admin = 0;
        if (!auth('admin')->check()) {
            $user = auth('user')->user();
            if ($user->id != $advertise->user_id) {
                return back()->with('success', 'دسترسی ندارید');
            }
        } else {
            $user = $advertise->user;
            $is_admin = 1;
        }

        $advertise->title = $request->title;
        $advertise->body = $request->advertise_body;
        $unset_price = 0;
        $unset_phone = 0;
        $unset_dist = 0;
        if (isset($request->price)) {
            $advertise->price = $request->price;
        } else {
            if (isset($advertise->price)) {
                $unset_price = 1;
            }
        }
        if (isset($request->phone)) {
            $advertise->phone = $request->phone;
        } else {
            if (isset($advertise->phone)) {
                $unset_phone = 1;
            }
        }

        $advertise->province_id = $request->province_id;
        if ($advertise->city_id != $request->city_id || $advertise->district_id != $request->district_id) {
            $advertise->city_id = $request->city_id;
            if (isset($request->district_id)) {
                $advertise->district_id = $request->district_id;
                $district = MongoDistrict::find($request->district_id);
            } else {
                if (isset($advertise->district_id)) {
                    $unset_dist = 1;
                }
            }
            $city = MongoCity::find($request->city_id);
            if (isset($district)) {
                $advertise->location = $city->name . ' - ' . $district->name;
            } else {
                $advertise->location = $city->name;
            }
        }

        $category = $advertise->category;

        $adImages = $advertise->images ?? [];
        $images = [];
        for ($i = 0; $i < 9; $i++) {
            $fn = 'img-' . $i + 1;
            if ($request->hasFile($fn)) {
                $path = 'advertise/images/' . $category->slug . '/' . $user->username . '/';
                $cover = $request->file($fn);
                if (isset($adImages[$i])) {
                    $baseFilename = explode('.webp', $adImages[$i])[0];
                    $baseFilenamearr = explode($path, $baseFilename);
                    if (isset($baseFilenamearr[1])) {
                        $baseFilename = $baseFilenamearr[1];
                    } else {
                        $baseFilename = Str::limit($advertise->slug, 10, '-') . time() . $i;
                    }
                } else {
                    $baseFilename = Str::limit($advertise->slug, 10, '-') . time() . $i;
                }
                if ($i == 0) {
                    $filename2 = $baseFilename . '2.webp';
                    $this->uploadAndResizeImage($cover, $path, $filename2, 90, 1);
                }
                $filename = $baseFilename . '.webp';
                $this->uploadAndResizeImage($cover, $path, $filename, 90, 0);
                $images[] = $path . $filename;
            } else {
                if (isset($adImages[$i])) {
                    $images[] = $adImages[$i];
                }
            }
        }

        if (count($images) > 0) {
            $advertise->images = $images;
        }

        $addItemService = new AdditemsService();
        $last_items = $advertise->items ?? [];
        $add_item_result = $addItemService->addForUpdateAd($category, $last_items, $request);
        $items = $add_item_result['items'];
        $items_title = $add_item_result['items_title'];
        $changeStatus = $add_item_result['changeStatus'];
        $typeTextFeatures = $add_item_result['typeTextFeatures'];

        $returnText = 'تغییرات با موفقیت ثبت شد';
        if ($changeStatus) {
            $advertise->status = 0;
            $advertise->not_item = 1;
            $returnText = "تغییرات ثبت شد و بعد از تایید نمایش داده میشود";
        }

        if (count($items) > 0) {
            $advertise->items = $items;
        }
        if (count($items_title) > 0) {
            $advertise->items_title = $items_title;
        }

        $advertise->update();

        if ($unset_price) {
            $advertise->unset('price');
        }
        if ($unset_phone) {
            $advertise->unset('phone');
        }
        if ($unset_dist) {
            $advertise->unset('district_id');
        }

        $afvs = $advertise->featureValues;
        if (isset($typeTextFeatures)) {
            foreach ($typeTextFeatures as $feature) {
                $fn = $feature->slug;
                if (isset($request->$fn)) {
                    $afv = $afvs->where('feature_id', $feature->id)->first();
                    if (isset($afv)) {
                        $afv->value = $request->$fn;
                        $afv->update();
                    } else {
                        $adFeatureValue = new MongoAdvertiseFeatureValue();
                        $adFeatureValue->advertise_id = $advertise->id;
                        $adFeatureValue->feature_id = $feature->id;
                        $adFeatureValue->value = $request->$fn;
                        $adFeatureValue->save();
                    }
                }
            }
        }

        if ($is_admin) {
            return redirect()->route('admin.advertise.all')->with('success', $returnText);
        } else {
            $admins = Admin::all();
            foreach ($admins as $admin) {
                $admin->notify(new SiteEvent([
                    'action' => $user->username . '  آگهی با عنوان ' . $advertise->title . ' را ویرایش کرد',
                    'route' => route('ad.show', ['category_slug' => $advertise->category->slug, 'slug' => $advertise->slug, 'random' => $advertise->random_id]),
                ]));
                if ($advertise->status == 0) {
                    Mail::to($admin->email)->send(new EmailToUser('آگهی تایید نشده', 'یک آگهی با عنوان ' . $advertise->title . ' در انتظار تایید می باشد در اسرع وقت نسبت به ویرایش آن اقدام کنید'));
                }
            }
            return redirect()->route('user.dashboard.edit', "ad")->with('success', $returnText);
        }
    }

    public function activeAdsAfterActiveEmail($user_id)
    {
        $advertises = MongoAdvertise::where('user_id', $user_id)->get();
        foreach ($advertises as $advertise) {
            if ($advertise->not_paid != 1 && $advertise->not_cat != 1) {
                $advertise->status = 1;
                $advertise->update();
            }
        }
    }

    public function store(Request $request)
    {
        $category = MongoCategory::find($request->category_id);
        if (!isset($category)) {
            return back()->with('success', 'دسته بندی آگهی را انتخاب کنید');
        }
        set_time_limit(360);
        $user = auth('user')->user();
        $advertise = new MongoAdvertise();

        $returnText = "آگهی با موفقیت ثبت شد";
        $advertise->status = 1;
        if ($user->email_actived != 1) {
            $advertise->status = 0;
            $returnText = "آگهی ثبت شد ، برای نمایش آگهی باید ایمیل خود را تایید کنید";
        }
        if (!$user->canCreateAd()) {
            $advertise->status = 0;
            $advertise->not_paid = 1;
            $returnText = "آگهی با موفقیت ثبت شد و در انتظار پرداخت می باشد";
        }
        if ($category->status != 1) {
            $advertise->status = 0;
            $advertise->not_cat = 1;
            $returnText = "آگهی با موفقیت ثبت شد و بعد از تایید نمایش داده میشود";
        }

        $advertise->category_id = $category->id;
        $advertise->user_id = $user->id;

        if (isset($request->price)) {
            $advertise->price = $request->price;
        }
        if (isset($request->phone)) {
            $advertise->phone = $request->phone;
        }
        $advertise->title = $request->title;
        $advertise->body = $request->advertise_body;
        $slug = preg_replace('~[^\pL\d]+~u', '-', $request->title);
        $advertise->slug = $slug;
        $advertise->random_id = strtolower(str_random(4)) . time();

        $advertise->province_id = $request->province_id;
        $advertise->city_id = $request->city_id;
        if (isset($request->district_id)) {
            $advertise->district_id = $request->district_id;
            $district = MongoDistrict::find($request->district_id);
        }
        $city = MongoCity::find($request->city_id);
        if (isset($district)) {
            $advertise->location = $city->name . ' - ' . $district->name;
        } else {
            $advertise->location = $city->name;
        }

        $addItemService = new AdditemsService();
        $add_item_result = $addItemService->addForCreateAd($category, $request);
        $items = $add_item_result['items'];
        $items_title = $add_item_result['items_title'];
        $changeStatus = $add_item_result['changeStatus'];
        $typeTextFeatures = $add_item_result['typeTextFeatures'];

        if ($changeStatus) {
            $advertise->status = 0;
            $advertise->not_item = 1;
            $returnText = "تغییرات ثبت شد و بعد از تایید نمایش داده میشود";
        }

        if (count($items) > 0) {
            $advertise->items = $items;
            dispatch(new ChangeItemPageCount($items, 'advertise', 1))->onQueue('becharkhsite')->delay(now()->addMinutes(30));
        }
        if (count($items_title) > 0) {
            $advertise->items_title = $items_title;
        }

        $images = [];
        for ($i = 1; $i <= 8; $i++) {
            $fn = 'img-' . $i;
            if ($request->hasFile($fn)) {
                $cover = $request->file($fn);
                $baseFilename = Str::limit($slug, 10, '-') . time() . $i;
                $path = 'advertise/images/' . $category->slug . '/' . $user->username . '/';
                if ($i == 1) {
                    $filename2 = $baseFilename . '2.webp';
                    $this->uploadAndResizeImage($cover, $path, $filename2, 90, 1);
                }
                $filename = $baseFilename . '.webp';
                $this->uploadAndResizeImage($cover, $path, $filename, 90, 0);
                $images[] = $path . $filename;
            } else {
                break;
            }
        }

        if (count($images) > 0) {
            $advertise->images = $images;
        }

        $advertise->save();

        if ($typeTextFeatures != null) {
            foreach ($typeTextFeatures as $feature) {
                $fn = $feature->slug;
                if (isset($request->$fn)) {
                    $adFeatureValue = new MongoAdvertiseFeatureValue();
                    $adFeatureValue->advertise_id = $advertise->id;
                    $adFeatureValue->feature_id = $feature->id;
                    $adFeatureValue->value = $request->$fn;
                    $adFeatureValue->save();
                }
            }
        }

        if ($user->canCreateAd()) {
            $user->decreaseMoneyFor("advertise");
        }

        $admin = Admin::first();
        $admin->notify(new SiteEvent([
            'action' => $user->username . ' یک آگهی با عنوان ' . $request->title . ' ثبت کرد',
            'route' => route('ad.show', ['category_slug' => $category->slug, 'slug' => $advertise->slug, 'random' => $advertise->random_id]),
        ]));
        if ($advertise->status == 0) {
            Mail::to($admin->email)->send(new EmailToUser('آگهی تایید نشده', 'یک آگهی با عنوان ' . $advertise->title . ' در انتظار تایید می باشد در اسرع وقت نسبت به ویرایش آن اقدام کنید'));
        }

        return redirect()->route('user.dashboard.edit', "ad")->with('success', $returnText);
    }

    private function startsWithHttp($url)
    {
        return (stripos($url, "http://") === 0 || stripos($url, "https://") === 0);
    }

    public function rocket($id)
    {
        $user = auth('user')->user();
        if ($user->canRocketAd()) {
            $ad = MongoAdvertise::find($id);
            $ad->created_at = Carbon::now();
            $ad->update();
            $user->decreaseMoneyFor("rocket-advertise");
            return back()->with('success', 'آگهی به ابتدای لیست بازگشت');
        } else {
            return back()->with('success', 'موجودی شما کافی نمیباشد');
        }
    }
}
