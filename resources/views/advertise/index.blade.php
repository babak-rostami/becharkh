@extends('index')

@section('title')
    @if (isset($meta_title) && $meta_title != null)
        {{ $meta_title }}
    @else
        آگهی خرید و فروش و استخدام و خدمات
    @endif
@endsection

@section('style')
    <link
        href="{{ asset('mixassets/css/advertise/index.min.css') . '?lm=' . filemtime('mixassets/css/advertise/index.min.css') }}"
        rel="stylesheet" type="text/css" />

    @if (isset($meta_title) && $meta_title != null)
        <meta name="title" content="{{ $meta_title }} | بچرخ">
        <meta name="description" content="{{ $meta_desc }}">
    @else
        <meta name="title" content="آگهی خرید و فروش و استخدام و خدمات | بچرخ">
        <meta name="description" content="ثبت آگهی رایگان خرید، فروش، اجاره، املاک، خودرو، استخدام و خدمات در ایران">
    @endif

    <link rel="canonical" href="{{ $data->AdvertiseCanonUrl($category ?? null, $item ?? null) }}">

    <meta name="robots" content="index, follow">
@endsection

@section('content')

    <div class="row bg-wht justify-content-center">

        @include('mainPart.return-msg')

        <div class="col-12 text-center">

            @include('mainPart.mainPage.cat-slider', [
                'page' => 'advertise',
                'suggetItems' => isset($suggetItems) ? $suggetItems : null,
                'suggestCats' => isset($suggestCats) ? $suggestCats : null,
            ])

            @if (isset($category))
                @if (isset($item->images))
                    <div id="item-gallery">
                        <img id="item-img-0" fetchpriority="high" class="my-3"
                            onclick="clickGalleryImg('item-img-0','item-gallery')" src="{{ asset($item->image(0)) }}"
                            title="{{ $item->full_title ?? $item->title }}"
                            alt="عکس {{ $item->full_title ?? $item->title }}">
                        {{-- @foreach ($item->images as $key => $img)
                            <img id="item-img-{{ $key }}" {!! $key == 0 ? 'fetchpriority="high"' : '' !!} class="my-3"
                                onclick="clickGalleryImg('item-img-{{ $key }}','item-gallery')"
                                src="{{ asset($item->image($key)) }}" title="{{ $item->full_title ?? $item->title }}"
                                alt="عکس {{ $item->full_title ?? $item->title }}">
                        @endforeach --}}
                    </div>
                @else
                    <img id="page-img" class="mb-3 mt-4" fetchpriority="high" src="{{ asset($category->image()) }}"
                        title="{{ $category->title }}" alt="{{ $category->title }}">
                @endif
                {{-- @endif --}}
            @else
                <img id="page-img" class="mb-3 mt-4" src="{{ $ftp_path . 'files/other/images/shop.jpg' }}"
                    title="خرید و فروش و استخدام و خدمات" alt="خرید و فروش و استخدام و خدمات">
            @endif
            @include('mainPart.gallery')
        </div>

    </div>


    <div class="row justify-content-center bg-wht">
        <div class="col-12 col-md-10">

            @include('item.top-users')


            <div class="text-right" id="page-title-box">
                @if (isset($category))
                    <h1 class="mt-4" id="page-title">{{ $meta_title }}</h1>
                    <p class="textarea-preline">{{ $meta_desc }}</p>
                @else
                    <h1 class="mt-4" id="page-title">آگهی خرید و فروش و استخدام</h1>
                    <p>انجمن بچرخ محلی برای ثبت آگهی و فروش محصول و خدمات شما.</p>
                @endif
            </div>

            <div class="row my-3">
                <div class="col-6 text-center pl-1">
                    <a class="btn btn-lg btn-primary w-100 radius-10" href="{{ route('new.ad') }}">ثبت آگهی جدید</a>
                </div>
                <div class="col-6 text-center pr-1">
                    @if (isset($item))
                        <a class="btn btn-lg btn-outline-primary w-100 radius-10"
                            href="{{ $item->withParentsCommentUrl() }}">نظرات کاربران</a>
                    @elseif(isset($category))
                        <a class="btn btn-lg btn-outline-primary w-100 radius-10"
                            href="{{ route('question.index', $category->slug) . '?s=1' }}">نظرات کاربران</a>
                    @else
                        <a class="btn btn-lg btn-outline-primary w-100 radius-10"
                            href="{{ route('question.index') . '?s=1' }}">نظرات کاربران</a>
                    @endif
                </div>
            </div>

            @if (isset($item) && !empty($item->top_users))
                @include('item.item_top_users', [
                    'top_users' => $item->top_users,
                ])
            @endif

            <div class="row mx-0 mt-4">
                @if (!$advertises->isEmpty())
                    @foreach ($advertises as $key => $advertise)
                        <div class="col-12 shadow-sm bg-wht text-right ad-box">
                            <a class="decor-none d-block" rel="nofollow" href="{{ route('ad.show', $advertise->slug) }}">
                                <img class="ad-image lazy-load" alt="{{ $advertise->title }}"
                                    title="{{ $advertise->title }}" data-src="{{ $advertise->thumbnail() }}">
                                <span class="ad-title">{{ $advertise->title }}</span>

                                @if (isset($advertise->price))
                                    <span class="ad-price-number">{{ number_format((int) $advertise->price) }}</span>
                                    <span class="ad-price-format">تومان</span>
                                @else
                                    <span class="ad-price-format">توافقی</span>
                                @endif

                                <div class="ad-f-div">
                                    <span class="ad-f-item">{{ $advertise->location }}</span>
                                    @if (isset($advertise->items_title))
                                        @foreach ($advertise->items_title as $item_title)
                                            <span class="ad-f-item">{{ $item_title }}</span>
                                        @endforeach
                                    @endif
                                </div>
                            </a>
                        </div>
                    @endforeach
                @endif

                @if (isset($affilates))
                    <div class="col-12 text-right py-2 mb-4">
                        @foreach ($affilates as $affilate)
                            <div class="my-4">
                                @include('affilate.show-box', [
                                    'affilate' => $affilate,
                                    'page' => 'advertise',
                                    'show_link' => 1,
                                ])
                                <hr>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- @if (isset($category))
                    @if ($meta_desc_editor)
                        <div class="col-12 mt-5">
                            <div class="text-right" id="pdesctor">{!! $meta_desc_editor !!}</div>
                        </div>
                    @endif
                @endif --}}

                <div class="col-12">
                    @include('mainPart.mainPage.breadc', ['page' => 'advertise'])
                </div>

            </div>

        </div>
    </div>

@endsection

@section('script')
    <script>
        let page = 'advertise';
        const index_route = "{{ route('ads.index') }}";
        let csrf_t = "{{ csrf_token() }}";
        let follow_item_route = "{{ route('follow.item') }}";
        let let_me_know_route = "{{ route('let.me.know') }}";

        let product_ids = {!! isset($affilates) ? json_encode($affilates->pluck('id')->toArray()) : '[]' !!};
    </script>
    <script type="text/javascript"
        src="{{ asset('mixassets/js/advertise/index.min.js') . '?lm=' . filemtime('mixassets/js/advertise/index.min.js') }}">
    </script>
@endsection
