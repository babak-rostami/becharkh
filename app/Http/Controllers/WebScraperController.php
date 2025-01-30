<?php

namespace App\Http\Controllers;

use App\Jobs\Item\UpdateItemPrice;
use App\Models\CategoryFeatureItem;
use App\Models\ItemImage;
use App\Models\MongoCategory;
use App\Models\MongoCity;
use App\Models\MongoDistrict;
use App\Models\MongoFeature;
use App\Models\MongoItem;
use App\Models\MongoProvince;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class WebScraperController extends Controller
{

    public function updateCarPrice($item)
    {
        $cache_key = 'update-item-price-' . $item->id;
        Cache::get($cache_key);
        if ($cache_key) {
            return;
        }
        set_time_limit(120);
        if (!isset($item->crl_price_url)) {
            return;
        }
        $prices = $item->prices ?? [];
        if (isset($prices['last_update'])) {
            $last_update = $prices['last_update'];
            if (now()->timestamp - $last_update < 43200) {
                return;
            }
        }
        Cache::put($cache_key, 1, 120);
        dispatch(new UpdateItemPrice($item))->onQueue('becharkhsite')->delay(now()->addMinutes(1));
    }

    public function mobiles()
    {
        set_time_limit(3600);

        dd('done');

        $disk = Storage::disk('ftp');
        $client = HttpClient::create();
        $browser = new HttpBrowser($client);

        $urls = array(
            'https://www.gsmarena.com/samsung-phones-9.php',
            'https://www.gsmarena.com/apple-phones-48.php',
            'https://www.gsmarena.com/google-phones-107.php',
            'https://www.gsmarena.com/huawei-phones-58.php',
            'https://www.gsmarena.com/oneplus-phones-95.php',
            'https://www.gsmarena.com/xiaomi-phones-80.php',
            'https://www.gsmarena.com/oppo-phones-82.php',
            'https://www.gsmarena.com/vivo-phones-98.php',
            'https://www.gsmarena.com/sony-phones-7.php',
            'https://www.gsmarena.com/asus-phones-46.php',
            'https://www.gsmarena.com/nokia-phones-1.php',
            'https://www.gsmarena.com/lg-phones-20.php',
            'https://gsmarena.com/lenovo-phones-73.php',
            'https://www.gsmarena.com/zte-phones-62.php',
            'https://www.gsmarena.com/htc-phones-45.php',
            'https://www.gsmarena.com/panasonic-phones-6.php',
            'https://www.gsmarena.com/motorola-phones-4.php',
            'https://www.gsmarena.com/honor-phones-121.php',
            'https://www.gsmarena.com/tecno-phones-120.php',
            'https://www.gsmarena.com/vertu-phones-39.php'
        );

        foreach ($urls as $url) {
            $crawler = $browser->request('GET', $url);
            if (str_contains($url, 'samsung')) {
                $mobile_brand = MongoItem::find('6692937deffe6ba4c90747e9');
            } elseif (str_contains($url, 'apple')) {
                $mobile_brand = MongoItem::find('669294bcb3638100140e6006');
            } elseif (str_contains($url, 'google')) {
                $mobile_brand = MongoItem::find('669295bc30cb94841c01d5b4');
            } elseif (str_contains($url, 'huawei')) {
                $mobile_brand = MongoItem::find('66929615d406b885f8083272');
            } elseif (str_contains($url, 'oneplus')) {
                $mobile_brand = MongoItem::find('6692963bd406b885f8083273');
            } elseif (str_contains($url, 'xiaomi')) {
                $mobile_brand = MongoItem::find('6692964fb3638100140e6007');
            } elseif (str_contains($url, 'oppo')) {
                $mobile_brand = MongoItem::find('66929667598c7e291405aa42');
            } elseif (str_contains($url, 'vivo')) {
                $mobile_brand = MongoItem::find('6692968cadbcf0128c00a792');
            } elseif (str_contains($url, 'sony-phones')) {
                $mobile_brand = MongoItem::find('669296a1adbcf0128c00a793');
            } elseif (str_contains($url, 'asus')) {
                $mobile_brand = MongoItem::find('669296ad598c7e291405aa43');
            } elseif (str_contains($url, 'nokia')) {
                $mobile_brand = MongoItem::find('669296bb598c7e291405aa44');
            } elseif (str_contains($url, 'lg')) {
                $mobile_brand = MongoItem::find('669296d9adbcf0128c00a794');
            } elseif (str_contains($url, 'lenovo')) {
                $mobile_brand = MongoItem::find('669296fb598c7e291405aa45');
            } elseif (str_contains($url, 'zte')) {
                $mobile_brand = MongoItem::find('6692974fadbcf0128c00a795');
            } elseif (str_contains($url, 'htc')) {
                $mobile_brand = MongoItem::find('66929760adbcf0128c00a796');
            } elseif (str_contains($url, 'panasonic')) {
                $mobile_brand = MongoItem::find('6692976fadbcf0128c00a797');
            } elseif (str_contains($url, 'motorola')) {
                $mobile_brand = MongoItem::find('66929796d406b885f8083274');
            } elseif (str_contains($url, 'honor')) {
                $mobile_brand = MongoItem::find('669297bf598c7e291405aa46');
            } elseif (str_contains($url, 'tecno')) {
                $mobile_brand = MongoItem::find('669297ddadbcf0128c00a798');
            } elseif (str_contains($url, 'vertu')) {
                $mobile_brand = MongoItem::find('669298edadbcf0128c00a799');
            }

            if (!isset($mobile_brand)) {
                return 'not found';
            }
            $next_page_as = $crawler->filter('.nav-pages')->filter('a');
            $site_route = 'https://www.gsmarena.com/';
            if ($next_page_as->count() > 0) {
                $next_page_link = $next_page_as->eq($next_page_as->count() - 1)->attr('href');
            } else {
                $next_page_link = null;
            }
            $do = 1;
            $page = 1;
            while ($do) {
                $mobile_items = $crawler->filter('#review-body')->filter('.makers')->filter('ul')->filter('li');
                if ($mobile_items->count() > 0) {
                    $mobile_items->each(function ($mobile_box) use ($mobile_brand, $disk) {
                        $mobile_box_a = $mobile_box->filter('a');
                        $mobile_model_title = $mobile_box_a->text();
                        if (
                            !Str::startsWith($mobile_model_title, 'iPad') &&
                            !Str::startsWith($mobile_model_title, 'Pad') &&
                            !Str::startsWith($mobile_model_title, 'MagicWatch') &&
                            !Str::startsWith($mobile_model_title, 'Fit') &&
                            !Str::startsWith($mobile_model_title, 'Nord') &&
                            !Str::startsWith($mobile_model_title, 'Zenwatch') &&
                            !Str::startsWith($mobile_model_title, 'Transformer') &&
                            !str_contains($mobile_model_title, 'Tablet') &&
                            !str_contains($mobile_model_title, 'Watch') &&
                            !Str::startsWith($mobile_model_title, 'SmartWatch') &&
                            !Str::startsWith($mobile_model_title, 'Quartz') &&
                            !Str::startsWith($mobile_model_title, 'Moto') &&
                            !Str::startsWith($mobile_model_title, 'Choice')
                        ) {
                            $slug = $mobile_brand->slug . '-' . Str::lower(preg_replace('~[^\pL\d]+~u', '-', $mobile_model_title));
                            $exist = MongoItem::where('slug', $slug)->first();
                            if (!isset($exist)) {
                                dump($mobile_model_title);
                            }
                            // if (!isset($exist)) {
                            //     $model_image_src = $mobile_box_a->filter('img')->attr('src');
                            //     $response = Http::get($model_image_src);
                            //     $cover = $response->body();

                            //     $mobile_model = new MongoItem();
                            //     $mobile_model->title = $mobile_model_title;
                            //     $mobile_model->full_title = $mobile_brand->title . " " . $mobile_model_title;
                            //     $mobile_model->title_en = $mobile_model_title;
                            //     $mobile_model->slug = $slug;
                            //     $mobile_model->status = 1;
                            //     $mobile_model->parent_id = $mobile_brand->id;
                            //     $mobile_model->category_id = '6682148310cf783aeb0ef696';
                            //     $mobile_model->feature_id = '66929298effe6ba4c90747e8';
                            //     $mobile_model->with_parent_url = '/mobile?s=1&phone-brand=' . $mobile_brand->slug . '&phone-model=' . $slug;
                            //     $images = [];
                            //     $path = 'itemImg/images/mobile';
                            //     $baseFilname = $slug . time();
                            //     //main image
                            //     $filename1 =  $baseFilname . '.webp';
                            //     $this->uploadAndResizeImage($cover, $path, $filename1, 90, 0, $disk);
                            //     //thumb image
                            //     $filename2 =  $baseFilname . '2.webp';
                            //     $this->uploadAndResizeImage($cover, $path, $filename2, 90, 1, $disk);

                            //     $images[] = $path . $filename1;
                            //     $mobile_model->images = $images;
                            //     $mobile_model->save();
                            // }
                        }
                    });
                }
                $page += 1;
                if ($next_page_link == '#' || $next_page_link == null || $page == 3) {
                    $do = 0;
                } else {
                    $next_page_url = $site_route . $next_page_link;
                    $crawler = $browser->request('GET', $next_page_url);
                    $next_page_as = $crawler->filter('.nav-pages')->filter('a');
                    if ($next_page_as->count() > 0) {
                        $next_page_link = $next_page_as->eq($next_page_as->count() - 1)->attr('href');
                    } else {
                        $do = 0;
                    }
                }
            }
        }
    }

    public function test()
    {
        set_time_limit(3600);

        $client = HttpClient::create();
        $browser = new HttpBrowser($client);

        $until_page = 1;

        $new_movies_count = 0;
        for ($fromPage = 1; $fromPage <= $until_page; $fromPage++) {
            if ($fromPage == 1) {
                $crawler = $browser->request('GET', 'https://digimoviez.com');
            } else {
                $crawler = $browser->request('GET', 'https://digimoviez.com/page/' . $fromPage);
            }
            $movie_boxes = $crawler->filter('.title_h');
            $allItems = Cache::rememberForever('allItems', function () {
                return CategoryFeatureItem::where('status', 1)->get();
            });
            if ($movie_boxes->count() > 0) {
                $movie_boxes->each(function ($movie_box) use (&$new_movies_count, $allItems) {
                    $movie_box_h2 = $movie_box->filter('h2');
                    $movie_a = $movie_box_h2->filter('a');
                    $movie_title = $movie_a->text();
                    $movie_link = $movie_a->attr('href');

                    $isset_title = 0;
                    if (strpos($movie_title, 'دانلود فیلم') === 0) {
                        $movie_title = explode('دانلود فیلم ', $movie_title)[1];
                        $isset_title = 1;
                    } elseif (strpos($movie_title, 'دانلود انیمیشن') === 0) {
                        $movie_title = explode('دانلود انیمیشن ', $movie_title)[1];
                        $isset_title = 1;
                    } elseif (strpos($movie_title, 'دانلود انیمه') === 0) {
                        $movie_title = explode('دانلود انیمه ', $movie_title)[1];
                        $isset_title = 1;
                    } elseif (strpos($movie_title, 'دانلود مستند') === 0) {
                        $movie_title = explode('دانلود مستند ', $movie_title)[1];
                        $isset_title = 1;
                    }
                    if ($isset_title == 1) {
                        $findMovie = $allItems->SortByDesc('id')->where('title', $movie_title)->first();
                        if (!isset($findMovie)) {
                            $new_movies_count++;
                            $movie_client = HttpClient::create();
                            $movie_browser = new HttpBrowser($movie_client);
                            $movie_page = $movie_browser->request('GET', $movie_link);

                            if (count($movie_page->filter('.num_holder')->filter('.greencol')) > 0) {
                                if ((floatval($movie_page->filter('.num_holder')->filter('.greencol')->text()) >= 9)) {
                                    $movie_desc = $movie_page->filter('.plot_text')->text() . '
                                        ';
                                    $movie_desc .= 'IMDb: ' . $movie_page->filter('.num_holder')->filter('.greencol')->text() . '/10' . '
                                         ';
                                    $movie_genres = $movie_page->filter('.res_item')->filter('a');
                                    if ($movie_genres->count() > 0) {
                                        $genre_count = 0;
                                        $movie_genres->each(function ($mg) use (&$movie_desc, &$genre_count) {
                                            $g = $this->findGenreItem($mg->text());
                                            if (isset($g)) {
                                                if ($genre_count == 0) {
                                                    $genre_count = 1;
                                                    $movie_desc .= "ژانر : " . $mg->text();
                                                } else {
                                                    $genre_count = 1;
                                                    $movie_desc .= " , " . $mg->text();
                                                }
                                            }
                                        });
                                    }
                                    $movie_desc .= '
                                        ';
                                    $movie_countries = $movie_page->filter('.res_item')->filter('a');
                                    if ($movie_genres->count() > 0) {
                                        $county_count = 0;
                                        $movie_countries->each(function ($mc) use (&$movie_desc, &$county_count) {
                                            if (strpos($mc->attr('href'), 'https://digimoviez.com/country/') === 0) {
                                                if ($county_count == 0) {
                                                    $county_count = 1;
                                                    $movie_desc .= "محصول کشور : " . $mc->text();
                                                } else {
                                                    $county_count = 1;
                                                    $movie_desc .= " , " . $mc->text();
                                                }
                                            }
                                        });
                                    }

                                    $movie_ganre = $movie_page->filter('.res_item')->filter('a')->text();
                                    $movie_img_src = $movie_page->filter('.inner_cover')->filter('a')->filter('img')->attr('src');

                                    $genre = $this->findGenreItem($movie_ganre);

                                    if (isset($genre)) {
                                        $this->createItem($movie_title, $movie_desc, $genre->id, $movie_img_src);
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }
        Cache::forget('allItems');
        $allItems = Cache::rememberForever('allItems', function () {
            return CategoryFeatureItem::where('status', 1)->get();
        });
        dd($new_movies_count);
    }

    private function uploadAndResizeImage($image, $path, $filename, $quality, $thumb, $disk)
    {
        if ($thumb == 1) {
            $resizedImage = Image::make($image)->resize(128, null, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('webp', $quality);
        } else {
            $resizedImage = Image::make($image)->encode('webp', $quality);
        }
        $disk->put($path . $filename, (string) $resizedImage);
    }

    public function createItem($title, $desc, $ganre_fitem_id, $movie_img_src)
    {
        $item = new CategoryFeatureItem();
        $item->title = $title;
        $item->feature_id = 12; //for movie feature
        $item->title_en = $title;
        $item->slug = strtolower(preg_replace('~[^\pL\d]+~u', '-', $title));
        $item->similar_search = $desc;
        $item->parent_id = $ganre_fitem_id;
        $item->status = 1;

        $item->save();

        $response = Http::get($movie_img_src);
        $cover = $response->body();
        $path = 'itemImg/images/' . $item->category->slug . '/';

        $image = new ItemImage();
        $image->item_id = $item->id;
        $baseFilname = $item->slug . '-' . time();
        //main image
        $filename =  $baseFilname . '.webp';
        $image->image = $path . $filename;
        $this->uploadAndResizeImage($cover, $path, $filename, 90, 0);
        //small image
        $filename1 = $baseFilname . '1.webp';
        $image->s_image = $path . $filename1;
        $this->uploadAndResizeImage($cover, $path, $filename1, 90, 1);
        //thum image
        $filename2 = $baseFilname . '2.webp';
        $image->thum = $path . $filename2;
        $this->uploadAndResizeImage($cover, $path, $filename2, 90, 1);
        $image->save();
    }

    private function findGenreItem($genre_title)
    {
        $allItems = Cache::rememberForever('allItems', function () {
            return CategoryFeatureItem::where('status', 1)->get();
        });
        $genre = $allItems->where('feature_id', 9)->where('title', $genre_title)->first();
        return $genre;
    }

    public function getCities()
    {
        $cityObject = [];
        $provinceData = json_decode($cityObject, true);
        foreach ($provinceData as $p) {
            $province = new MongoProvince();
            $province->name = $p['name'];
            $province->slug = $p['slug'];
            $province->save();
            foreach ($p['cities'] as $c) {
                $city = new MongoCity();
                $city->province_id = $province->id;
                $city->name = $c['name'];
                $city->slug = $c['slug'];
                $city->lat = $c['lat'];
                $city->lon = $c['lon'];
                $city->save();
                foreach ($c['districts'] as $d) {
                    $district = new MongoDistrict();
                    $district->city_id = $city->id;
                    $district->name = $d['name'];
                    $district->lat = $c['lat'];
                    $district->lon = $c['lon'];
                    $district->save();
                }
            }
        }
        dd('done');
    }
}
