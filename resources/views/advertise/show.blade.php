@extends('index')


@section('title')
    {{ $advertise->title }}
@endsection


@section('style')
    <link rel="stylesheet" href="{{ asset('assets/swiper/swiper-bundle.min.css') }}" />


    <link href="{{ asset('assets/css/open-image.css') . '?lm=' . filemtime('assets/css/open-image.css') }}" rel="stylesheet"
        type="text/css" />

    <link
        href="{{ asset('mixassets/css/advertise/show.min.css') . '?lm=' . filemtime('mixassets/css/advertise/show.min.css') }}"
        rel="stylesheet" type="text/css" />

    <meta name="title" content="{{ $advertise->title }}  - | بچرخ">

    <meta name="description" content="{{ $advertise->body }}">

    <meta name="robots" content="noindex, nofollow">
@endsection


@section('content')

    <div class="row justify-content-center bg-wht">

        <div class="col-12 text-center my-3">
            @include('mainPart.mainPage.cat-slider', [
                'page' => 'show_advertise',
                'suggetItems' => isset($suggetItems) ? $suggetItems : null,
                'suggestCats' => isset($suggestCats) ? $suggestCats : null,
            ])
        </div>

        <div class="col-12 col-md-8 bg-wht text-right order-md-1">

            @if (session('success'))
                <p class="alert alert-success text-center">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <p class="alert alert-danger text-center">{{ $error }}</p>
                @endforeach
            @endif

            @include('mainPart.mainPage.pages-tabs', [
                'page' => 'show_advertise',
                'item' => isset($item) ? $item : null,
                'user' => isset($user) ? $user : null,
                'is_follow' => isset($is_follow) ? $is_follow : null,
            ])

            <h1 class="font-20 mb-4 mt-5 text-center">{{ $advertise->title }}</h1>

            <?php $count = 0; ?>
            <?php $vcount = 0; ?>
            @if (count($adImages) > 0 || $adVideo != null)
                <div class="imgslider-container">
                    @if ($adVideo != null)
                        <?php $count = 1; ?>
                        <?php $vcount = 1; ?>
                        <div class="imgslider-ImageSlides imgslider-fade1 text-center">
                            <div class="imgslider-numbertext"> {{ $count }} / {{ $adImages->count() + $vcount }}
                            </div>
                            <video id="ad-video" controls poster="{{ asset($adVideo->image()) }}">
                                <source src="{{ $adVideo->videoPath() }}" type="video/mp4">
                            </video>
                        </div>
                    @endif
                    @foreach ($adImages as $image)
                        <?php $count++; ?>
                        <div class="imgslider-ImageSlides imgslider-fade1 text-center">
                            <div class="imgslider-numbertext">{{ $count }} / {{ count($adImages) + $vcount }}</div>
                            <img class="ad-image" onclick="clickImg({{ $count }})"
                                id="slideImg-{{ $count }}" src="{{ $image['url'] }}">
                        </div>
                    @endforeach
                    <div id="imageModal" class="imgslider-modal">
                        <span id="close-slide-img">&times;</span>
                        <img class="imgslider-modal-content" id="slide-img">
                        <a class="imgslider-prev" onclick="clickImg(prev_slide_image)">❮</a>
                        <a class="imgslider-next" onclick="clickImg(next_slide_image)">❯</a>
                    </div>


                    <a class="imgslider-prev" onclick="plusSlides(-1)">❮</a>
                    <a class="imgslider-next" onclick="plusSlides(1)">❯</a>
                </div>
                <br>
                <div class="text-center">
                    @for ($i = 0; $i < $count; $i++)
                        <span class="imgslider-dot" onclick="currentSlide({{ $i + 1 }})"></span>
                    @endfor
                </div>
            @else
                <img class="w-100" src="{{ asset($advertise->image()) }}">
            @endif

            <div class="row align-items-center">

                <div class="col-12 mb-3 text-right">
                    <hr>
                    <a href="{{ route('user.dashboard', $advertiseUser->username) }}" class="decor-none d-flex my-4">
                        <img id="user-image" class="mx-2" src="{{ asset($advertiseUser->image()) }}">
                        <div class="align-content-center">
                            <span class="font-weight-bold" style="color: #005cbf">{{ $advertiseUser->username }}@</span>
                            <br>
                            <span>{{ $advertiseUser->name }}</span>
                        </div>
                    </a>

                    @if ($user != null)
                        @if (!$isAdForThisUser)
                            <a class="btn mt-2" id="chat-btn" href="{{ route('user.chat.start', $advertiseUser->id) }}">
                                گفتگو با فروشنده
                                <img style="width: 24px;" src="{{ asset('files/other/images/ad-chat.gif') }}">
                            </a>
                        @endif
                    @else
                        <a class="btn mt-2" id="chat-btn" data-toggle="modal" data-target="#login_user" href="">
                            گفتگو با فروشنده
                            <img style="width: 24px;" src="{{ asset('files/other/images/ad-chat.gif') }}">
                        </a>
                    @endif


                    <div class="position-relative d-inline-block">
                        <button type="button" class="btn share-span mt-2">
                            اشتراک گذاری
                            <img class="lazy-load" data-src="{{ asset('files/other/images/share.png') }}">
                        </button>
                        <div class="share-box hide-share mt-2">
                            <div class="row">
                                <div class="col text-center cur-p" onclick="sentPageToTelegram()">
                                    <img class="lazy-load" data-src="{{ asset('files/other/images/telegram.png') }}">
                                    <br>
                                    <span>تلگرام</span>
                                </div>
                                <div class="col text-center cur-p" onclick="copyToClipboard()">
                                    <img class="lazy-load" data-src="{{ asset('files/other/images/chain.png') }}">
                                    <br>
                                    <span>کپی آدرس</span>
                                </div>
                                <div class="col text-center cur-p" onclick="sentPageToWhatsapp()">
                                    <img class="lazy-load" data-src="{{ asset('files/other/images/whatsapp.png') }}">
                                    <br>
                                    <span>واتساپ</span>
                                </div>
                                <div class="col-12 mt-3 text-center">
                                    <input class="form-control w-100" type="text" id="page-url-for-clipboard" readonly
                                        value="{{ Request::url() }}">
                                    <p id="copy-clipboard-done-span">آدرس صفحه کپی شد</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a class="btn btn-outline-danger mt-2 mr-1" href="" data-toggle="modal"
                        data-target="#advertise-report">
                        گزارش آگهی
                    </a>

                    @if ($isAdForThisUser)
                        <a class="btn btn-outline-primary mt-2" href="{{ route('ad.edit', $advertise->id) }}">ویرایش
                            آگهی</a>
                    @endif
                    @if ($advertise->site_link != null)
                        <a class="btn btn-primary mt-2" target="_blank" href="{{ $advertise->site_link }}"
                            rel="nofollow">سفارش محصول</a>
                    @endif
                    <hr>

                </div>

            </div>
            <div class="row">
                <div class="col-6 text-right">
                    زمان ثبت آگهی
                </div>
                <div class="col-6 text-right">
                    <span>{{ jdate($advertise->created_at)->ago() }}</span>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-6 text-right">
                    قیمت
                    <img style="width: 24px" src="{{ asset('files/other/images/price.png') }}">
                </div>
                <div class="col-6 text-right">
                    @if ($advertise->price != null)
                        <span>{{ number_format((int) $advertise->price) }}</span>
                    @else
                        <span>توافقی</span>
                    @endif
                </div>
            </div>
            @foreach ($advertise->getItems() as $item)
                <hr>
                <div class="row">
                    <div class="col-6 text-right">
                        {{ $item->feature->title }}
                    </div>
                    <div class="col-6 text-right">
                        {{ $item->title }}
                    </div>
                </div>
            @endforeach
            @foreach ($advertise->featureValues as $afv)
                <hr>
                <div class="row">
                    <div class="col-6 text-right">
                        {{ $afv->feature->title }}
                    </div>
                    <div class="col-6 text-right">
                        {{ $afv->value }}
                    </div>
                </div>
            @endforeach
            @if (isset($afv) || isset($item))
                <hr>
            @endif
            <div class="row">
                @if ($advertise->phone != null)
                    <div class="col-6 text-right">
                        تماس
                    </div>
                    <div class="col-6 text-right">
                        {{ $advertise->phone }}
                    </div>
                    <div class="col-12">
                        <hr>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-6 text-right">
                    محل آگهی
                    <img style="width: 24px" src="{{ asset('files/other/images/location.png') }}">
                </div>
                <div class="col-6 text-right">
                    {{ $advertise->location }}
                </div>
                <div class="col-12 text-right">
                    <hr>
                    <p class="textarea-preline mt-2">{{ $advertise->body }}</p>
                </div>
            </div>

            {{-- <div class="row">
                @if (isset($item) && isset($item->crl_price_url))
                    @include('item.price-box', [
                        'item' => $item,
                    ])
                @endif
            </div> --}}
        </div>


        <div class="col-12 col-md-4 px-2 mt-2 text-right order-md-0">
            @if (!$advertises->isEmpty())
                <div class="row mx-2">
                    <div class="col-12 mt-lg-5">
                        <h4 class="text-center my-4 pt-3 font-20">هنوز آگهی خود را ثبت نکرده اید؟</h4>
                        <a class="btn btn-danger mb-4 w-100" id="adsSection" rel="nofollow"
                            href="{{ route('new.ad') }}">
                            ثبت آگهی جدید
                        </a>
                    </div>

                    @foreach ($advertises as $hotad)
                        <div class="col-12 text-right mb-3">
                            <a class="text-decoration-none hotad-box radius-10 p-2 d-block"
                                href="{{ route('ad.show', ['category_slug' => $hotad->category->slug, 'slug' => $hotad->slug, 'random' => $hotad->random_id]) }}">
                                <div class="d-flex">
                                    <img class="hotad-image lazy-load" data-src="{{ asset($hotad->image()) }}"
                                        alt="{{ $hotad->title }}" />
                                    <div class="mr-2">
                                        <span class="hotad-title">{{ $hotad->title }}</span>
                                        <br>
                                        @if ($hotad->price != null)
                                            <span class="font-14 text-dark">{{ number_format((int) $hotad->price) }} تومان
                                            </span>
                                        @else
                                            <span class="font-14 text-dark">توافقی</span>
                                        @endif
                                        <br>
                                        <span class="font-14 text-dark">{{ jdate($hotad->created_at)->ago() }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Modal -->
        <div class="modal fade" id="advertise-report" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">گزارش آگهی</h5>
                        <button type="button" class="close float-left ml-0" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('report.advertise') }}" method="post" role="form">
                            @csrf
                            <div class="form-group">
                                <label for="">چرا این آگهی را گزارش می کنید؟</label>
                                <input type="hidden" name="advertise_id" value="{{ $advertise->id }}">
                                <textarea class="form-control" name="body"></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">ارسال</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection


@section('script')
    <script src="{{ asset('assets/swiper/swiper-bundle.min.js') }}"></script>

    <script>
        const page = 'show_advertise';
        const ad_show_csrf = "{{ csrf_token() }}";
        var open_image = null;
        var next_slide_image = null
        var prev_slide_image = null
        const has_video = "{{ $vcount }}"
        var slide_count = null;
        if (has_video != '0') {
            slide_count = "{{ count($adImages) + $vcount }}"
        } else {
            slide_count = "{{ count($adImages) }}"
        }

        const follow_item_route = '{{ route('follow.item') }}';
    </script>

    <script type="text/javascript"
        src="{{ asset('mixassets/js/advertise/show.min.js') . '?lm=' . filemtime('mixassets/js/advertise/show.min.js') }}">
    </script>
@endsection
