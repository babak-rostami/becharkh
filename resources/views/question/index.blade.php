@extends('index')

@section('title')
    @if (isset($meta_title) && $meta_title != null)
        {{ $meta_title }}
    @else
        انجمن {{ isset($category) ? $category->title : '' }}
    @endif
@endsection

@section('style')
    @if (isset($meta_title) && $meta_title != null)
        <meta name="title" content="{{ $meta_title }}">
        <meta name="description" content="{{ $meta_desc }}">
    @else
        <meta name="title" content="انجمن {{ isset($category) ? $category->title : '' }}">
        <meta name="description" content="بحث و گفتگو و حل مشکلات در انجمن هر سوالی داری اینجا به جواب میرسی">
    @endif

    @if (isset($item))
        <link rel="canonical" href="{{ $item->withParentsForumUrl() }}">
    @else
        @if (isset($category))
            <link rel="canonical" href="{{ route('question.index', $category->slug) }}">
        @else
            <link rel="canonical" href="{{ route('question.index') }}">
        @endif
    @endif

    <meta name="robots" content="index, follow">

    <link href="{{ asset('mixassets/css/forum/index.min.css') . '?lm=' . filemtime('mixassets/css/forum/index.min.css') }}"
        rel="stylesheet" type="text/css" />
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
                'page' => 'forum',
                'suggetItems' => isset($suggetItems) ? $suggetItems : null,
                'suggestCats' => isset($suggestCats) ? $suggestCats : null,
            ])

            @if (isset($category))
                {{-- @if (isset($item_video))
                    <iframe class="shadow-sm p-0 m-0 mt-3 radius-10"
                        src="{{ route('video.embedb.show', $item_video->slug2) }}" style="border:none;" width="100%"
                        height="292px" allowfullscreen></iframe>
                @else --}}
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
                <img class="mb-3 mt-4" src="{{ $ftp_path . 'files/other/images/crtable.png' }}" title="انجمن"
                    alt="انجمن">
            @endif
            @include('mainPart.gallery')
        </div>
    </div>

    <div class="row bg-wht justify-content-center">
        <div class="col-12 col-md-10">
            @include('mainPart.mainPage.pages-tabs', [
                'page' => 'forum',
                'item' => isset($item) ? $item : null,
                'user' => isset($user) ? $user : null,
                'is_follow' => isset($is_follow) ? $is_follow : null,
            ])

            <div class="row mt-4">
                @include('item.top-users')
            </div>

            @if (isset($category))
                <h1 class="mt-4 text-right" id="page-title">{{ $meta_title }}</h1>
                <p class="textarea-preline text-right">{{ $meta_desc }}</p>
            @else
                <h1 class="mt-4 text-right" id="page-title">انجمن بچرخ</h1>
                <p class="text-right">انجمنی برای حل مشکلات و به اشتراک گذاشتن تجربیات و ایده ها</p>
            @endif

            <a id="new-q-btn" class="btn btn-lg btn-primary" target="_blank" rel="nofollow"
                href="{{ isset($category) ? $category->newQuestionUrl($category->slug) : route('question.create') }}">
                سوال جدید +
            </a>

            @include('category.rcats', ['page' => 'forum'])

            {{-- <div class="row my-4 px-0">
                <div class="col-12 text-center mt-2">
                    <span>سوال شما قبلا در انجمن پرسیده نشده است؟</span>
                    <br>
                    <img class="mt-3 lazy-load" data-src="{{ $ftp_path . 'files/other/images/darrow.gif' }}"
                        alt="arrow down">
                    <br>
                    <a class="btn btn-lg btn-primary mt-4" rel="nofollow"
                        href="{{ isset($category) ? $data->newQuestionUrl($category->slug) : route('question.create') }}">
                        سوال جدید +
                    </a>
                </div>
            </div> --}}
            @if (isset($page_intro_title) && isset($page_intro_desc))
                <div id="page-g-div" class="text-center mt-3">
                    <img class="lazy-load" id="page-g-img"
                        data-src="{{ $ftp_path . 'files/other/images/approval-36.png' }}">
                    <span id="page-g-title">{{ $page_intro_title }}</span>
                    <span id="page-g">{{ $page_intro_desc }}</span>
                </div>
            @endif

            <div class="row px-0 mt-3">
                @if ($questions->count() > 0)
                    @include('question.question-items', ['questions' => $questions])
                @else
                    <div class="col-12 text-right">
                        <span id="last-qs-title">آخرین سوال ها</span>
                    </div>
                    @include('question.question-items', ['questions' => $data->suggestRtables()])
                @endif
            </div>
            <div class="mt-5">
                {{ $questions->links() }}
            </div>

            @if (isset($category))
                @if ($meta_desc_editor)
                    <div class="row mt-5">
                        <div class="col-12">
                            <div class="text-right" id="pdesctor">{!! $meta_desc_editor !!}</div>
                        </div>
                    </div>
                @endif
            @endif


            @include('mainPart.mainPage.breadc', ['page' => 'forum'])

            {{-- @if (isset($hot_pages))
                <div class="row mt-4 justify-content-center">
                    @include('mainPart.hot-pages')
                </div>
            @endif --}}

        </div>
    </div>
@endsection

@section('script')
    <script>
        page = 'forum';
    </script>

    <script>
        const index_route = "{{ route('question.index') }}";
        const is_rtable_page = 1;

        const csrf_t = "{{ csrf_token() }}";
        const follow_item_route = '{{ route('follow.item') }}';
        const loadingGif = '<img src="{{ $ftp_path . 'files/other/images/loading.gif' }}">';

        let product_ids = {!! isset($affilate) ? json_encode([$affilate->id]) : '[]' !!};
    </script>


    <script type="text/javascript"
        src="{{ asset('mixassets/js/forum/index.min.js') . '?lm=' . filemtime('mixassets/js/forum/index.min.js') }}">
    </script>
@endsection
