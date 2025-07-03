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

    @if (isset($category))
        <link rel="canonical" href="{{ $data->AdvertiseCanonUrl($category, $selected_items ?? null) }}">
    @endif

    <meta name="robots" content="index, follow">
    {{-- <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" /> --}}
@endsection

@section('content')

    <div class="row bg-wht justify-content-center">

        <div class="col-12">
            @if (session('success'))
                <p class="alert alert-success text-center">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <p class="alert alert-danger text-center">{{ $error }}</p>
                @endforeach
            @endif
        </div>

        <div class="col-12 text-center">

            @include('mainPart.mainPage.cat-slider', [
                'page' => 'advertise',
                'suggetItems' => isset($suggetItems) ? $suggetItems : null,
                'suggestCats' => isset($suggestCats) ? $suggestCats : null,
            ])

            @if (isset($category))
                @if (isset($item_video))
                    <iframe class="shadow-sm p-0 m-0 mt-3 radius-10"
                        src="{{ route('video.embedb.show', $item_video->slug2) }}" style="border:none;" width="100%"
                        height="292px" allowfullscreen></iframe>
                @else
                    @if (isset($item->images))
                        <div id="item-gallery">
                            @foreach ($item->images as $key => $img)
                                <img id="item-img-{{ $key }}" class="my-3 lazy-load"
                                    onclick="clickGalleryImg('item-img-{{ $key }}','item-gallery')"
                                    data-src="{{ asset($item->image($key)) }}"
                                    title="{{ $item->full_title ?? $item->title }}"
                                    alt="عکس {{ $item->full_title ?? $item->title }}">
                            @endforeach
                        </div>
                    @else
                        <img id="page-img" class="mb-3 mt-4" src="{{ asset($category->image()) }}"
                            title="{{ $category->title }}" alt="{{ $category->title }}">
                    @endif
                @endif
            @else
                <img id="page-img" class="mb-3 mt-4" src="{{ $ftp_path . 'files/other/images/shop.jpg' }}"
                    title="خرید و فروش و استخدام و خدمات" alt="خرید و فروش و استخدام و خدمات">
            @endif
        </div>

    </div>


    <div class="row justify-content-center bg-wht">
        <div class="col-12 col-md-10 px-0">
            @include('mainPart.mainPage.pages-tabs', [
                'page' => 'advertise',
                'item' => isset($item) ? $item : null,
                'user' => isset($user) ? $user : null,
                'is_follow' => isset($is_follow) ? $is_follow : null,
            ])

            <div class="row mx-1 mt-4">
                @include('item.top-users')
            </div>

            <div class="row mx-1 justify-content-center">

                <div class="col-12 text-right">
                    @if (isset($category))
                        <h1 class="mt-4" id="page-title">{{ $meta_title }}</h1>
                        <p class="textarea-preline">{{ $meta_desc }}</p>
                    @else
                        <h1 class="mt-4" id="page-title">آگهی خرید و فروش و استخدام</h1>
                        <p>انجمن بچرخ محلی برای ثبت آگهی و فروش محصول و خدمات شما.</p>
                    @endif
                </div>

                {{-- @if (isset($category) && $category->has_ads)
                    <button id="new-a-btn" class="btn btn-lg btn-primary"
                        onclick="jsurl('{{ $category->newAdvertiseUrl($category->slug) }}',1)">
                        آگهی جدید +
                    </button>
                @else
                    <button id="new-a-btn" class="btn btn-lg btn-primary" onclick="jsurl('{{ route('new.ad') }}',1)">
                        آگهی جدید +
                    </button>
                @endif --}}

                <div class="col-12 mt-4">

                    @include('category.rcats', ['page' => 'advertise'])

                    {{-- @if ($advertises->isEmpty() && isset($category) && isset($item) && !isset($suggestCats))
                        <div id="let-me-know">
                            <span id="lmk-title">بهمون اطلاع بدین</span>
                            <br>
                            <button id="lmk-btn" onclick="letMeKnow('{{ $category->id }}','{{ $item->id }}')"
                                class="btn btn-dark my-3">سریعتر موجود شود
                                +</button>
                            <br>
                            <span>محصول مورد نظرتون موجود نیست؟</span>
                            <br>
                            <span>با ضربه زدن روی دکمه بالا بهمون اطلاع بدین</span>
                        </div>
                    @endif --}}
                    <div class="row">
                        {{-- <div class="col-12 text-center">
                            <span id="dywt">آگهی خود را ثبت نکرده اید؟</span>
                            @if (isset($category) && $category->has_ads)
                                <button class="btn btn-outline-primary mb-4 mt-2 w-100" id="adsSection"
                                    onclick="jsurl('{{ $category->newAdvertiseUrl($category->slug) }}',1)">
                                    ثبت آگهی جدید +
                                </button>
                            @else
                                <button class="btn btn-outline-primary mb-4 mt-2 w-100" id="adsSection"
                                    onclick="jsurl('{{ route('new.ad') }}',1)">
                                    ثبت آگهی جدید +
                                </button>
                            @endif
                        </div> --}}
                        @if (!$advertises->isEmpty())
                            @foreach ($advertises as $key => $advertise)
                                <div class="col-12 shadow-sm bg-wht text-right ad-box"><a class="decor-none" rel="nofollow"
                                        href="{{ route('ad.show', ['category_slug' => $advertise->category->slug, 'slug' => $advertise->slug, 'random' => $advertise->random_id]) }}">
                                        <div class="row align-items-center pr-3">
                                            <div><img class="ad-image lazy-load" alt="{{ $advertise->title }}"
                                                    title="{{ $advertise->title }}" data-src="{{ $advertise->image() }}">
                                            </div>
                                            <div class="col-7 col-sm-8 py-3">
                                                <b>{{ $advertise->title }}</b><br><span
                                                    class="text-gray">{{ jdate($advertise->created_at)->ago() }}</span><br><span
                                                    class="text-gray">{{ $advertise->location }}</span><br>
                                                @if (isset($advertise->price))
                                                    <p class="mt-2 font-17 font-600">
                                                        {{ number_format((int) $advertise->price) }}
                                                        تومان</p>
                                                @else
                                                    <p class="mt-2 font-17 font-600">
                                                        توافقی</p>
                                                @endif
                                            </div>
                                        </div>
                                    </a></div>
                            @endforeach
                        @endif
                        @if ($isset_ads)
                            <div class="col-12 mt-5">{{ $advertises->links() }} </div>
                        @endif

                        @if (isset($affilates))
                            <div class="col-12 text-right py-2 mb-4">
                                @foreach ($affilates as $affilate)
                                    @include('affilate.show-box', ['page' => 'advertise'])
                                @endforeach
                            </div>
                        @endif

                        @if (isset($category))
                            @if ($meta_desc_editor)
                                <div class="col-12 mt-5">
                                    <div class="text-right" id="pdesctor">{!! $meta_desc_editor !!}</div>
                                </div>
                            @endif
                        @endif

                        <div class="col-12">
                            @include('mainPart.mainPage.breadc', ['page' => 'advertise'])
                        </div>

                        @if (isset($hot_pages))
                            <div class="col-12">
                                <div class="row mt-4">
                                    @include('mainPart.hot-pages')
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('script')
    {{-- <script src="https://cdn.plyr.io/3.7.8/plyr.polyfilled.js"></script> --}}
    <script>
        let page = 'advertise';
        // const yplayer = new Plyr('#affilb-video');
        const index_route = "{{ route('ads.index') }}";
        let csrf_t = "{{ csrf_token() }}";
        let follow_item_route = "{{ route('follow.item') }}";
        let let_me_know_route = "{{ route('let.me.know') }}";

        let product_ids = {!! isset($affilates) ? json_encode($affilates->pluck('id')->toArray()) : '[]' !!};

        //for fifil
        // const fifil_load_items_route = "{{ route('fifil.load.items') }}";
        // let features = @json($features ?? []);
        // features = features.map(function(feature) {
        //     return {
        //         id: feature._id,
        //         title: feature.title,
        //         slug: feature.slug,
        //         p_id: feature.parent_id,
        //     };
        // });
        // let selected_items = @json($selected_items ?? []);
        // selected_items = selected_items.map(function(item) {
        //     return {
        //         id: item._id,
        //         title: item.title,
        //         p_id: item.parent_id ?? null,
        //         f_id: item.feature_id ?? null,
        //     };
        // });
        //end for fifil
    </script>
    <script type="text/javascript"
        src="{{ asset('mixassets/js/advertise/index.min.js') . '?lm=' . filemtime('mixassets/js/advertise/index.min.js') }}">
    </script>
@endsection
