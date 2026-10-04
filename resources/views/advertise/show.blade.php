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

        @include('mainPart.return-msg')

        <div class="col-12 text-center my-3">
            @include('mainPart.mainPage.cat-slider', [
                'page' => 'show_advertise',
                'suggetItems' => isset($suggetItems) ? $suggetItems : null,
                'suggestCats' => isset($suggestCats) ? $suggestCats : null,
            ])
        </div>

        <div class="col-12 col-md-8 bg-wht text-right">

            <?php $count = 0; ?>
            <?php $vcount = 0; ?>
            @if (count($adImages) > 0)
                <div class="imgslider-container">
                    @foreach ($adImages as $image)
                        <?php        $count++; ?>
                        <div class="imgslider-ImageSlides imgslider-fade1 text-center">
                            <div class="imgslider-numbertext">{{ $count }} / {{ count($adImages) + $vcount }}</div>
                            <img class="ad-image" onclick="clickImg({{ $count }})" id="slideImg-{{ $count }}"
                                src="{{ $image['url'] }}">
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

            <div class="row">

                <div class="col-12 text-right">
                    <h1 class="font-20 mb-4 mt-5">{{ $advertise->title }}</h1>
                    <p class="textarea-preline mt-2">{{ $advertise->body }}</p>
                    <hr>
                </div>

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
                <div class="col-12">
                    <hr>
                </div>
            </div>

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
            <div class="row align-items-center">
                <div class="col-12 mb-3 text-right">
                    <hr>
                    <div class="position-relative d-inline-block">
                        <button type="button" class="btn share-span mt-2">
                            اشتراک گذاری
                            <img loading="lazy" src="{{ asset('files/other/images/share.png') }}">
                        </button>
                        <div class="share-box hide-share mt-2">
                            <div class="row">
                                <div class="col text-center cur-p" onclick="sentPageToTelegram()">
                                    <img loading="lazy" src="{{ asset('files/other/images/telegram.png') }}">
                                    <br>
                                    <span>تلگرام</span>
                                </div>
                                <div class="col text-center cur-p" onclick="copyToClipboard()">
                                    <img loading="lazy" src="{{ asset('files/other/images/chain.png') }}">
                                    <br>
                                    <span>کپی آدرس</span>
                                </div>
                                <div class="col text-center cur-p" onclick="sentPageToWhatsapp()">
                                    <img loading="lazy" src="{{ asset('files/other/images/whatsapp.png') }}">
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

                    <a class="btn btn-outline-danger mt-2 mr-1" href="" data-toggle="modal" data-target="#advertise-report">
                        گزارش آگهی
                    </a>

                    @if ($isAdForThisUser)
                        <a class="btn btn-outline-primary mt-2" href="{{ route('ad.edit', $advertise->id) }}">ویرایش
                            آگهی</a>
                    @endif
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


        <div class="col-12 col-md-8 px-2 mt-2 text-right">

            <div class="row my-3">
                <div class="col-6 text-center pl-1">
                    <a class="btn btn-lg btn-primary w-100 radius-10" href="{{ route('new.ad') }}">ثبت آگهی جدید</a>
                </div>
                <div class="col-6 text-center pr-1">
                    @if (isset($item))
                        <a class="btn btn-lg btn-outline-primary w-100 radius-10"
                            href="{{ $item->withParentsCommentUrl() }}">نظرات کاربران</a>
                    @endif
                </div>
            </div>

            @if (!$advertises->isEmpty())
                <div class="row mx-2">

                    @foreach ($advertises as $hotad)
                        <div class="col-12 shadow-sm bg-wht text-right ad-box">
                            <a class="decor-none d-block" rel="nofollow" href="{{ route('ad.show', $hotad->slug) }}">
                                <img class="ad-box-image" loading="lazy" alt="{{ $hotad->title }}" title="{{ $hotad->title }}"
                                    src="{{ $hotad->thumbnail() }}">
                                <span class="ad-title">{{ $hotad->title }}</span>

                                @if (isset($hotad->price))
                                    <span class="ad-price-number">{{ number_format((int) $hotad->price) }}</span>
                                    <span class="ad-price-format">تومان</span>
                                @else
                                    <span class="ad-price-format">توافقی</span>
                                @endif

                                <div class="ad-f-div">
                                    <span class="ad-f-item">{{ $hotad->location }}</span>
                                    @if (isset($hotad->items_title))
                                        @foreach ($hotad->items_title as $item_title)
                                            <span class="ad-f-item">{{ $item_title }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Modal -->
        <div class="modal fade" id="advertise-report" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content text-right">
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

    </script>

    <script type="text/javascript"
        src="{{ asset('mixassets/js/advertise/show.min.js') . '?lm=' . filemtime('mixassets/js/advertise/show.min.js') }}">
        </script>
@endsection