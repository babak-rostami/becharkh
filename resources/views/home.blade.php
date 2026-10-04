@extends('index')

@section('title')
    بچرخ انجمنی برای تبادل نظر
@endsection


@section('style')
    <link href="{{ asset('mixassets/css/home.min.css') . '?lm=' . filemtime('mixassets/css/home.min.css') }}" rel="stylesheet"
        type="text/css" />

    <meta name="title" content="بچرخ انجمنی برای تبادل نظر">
    <meta name="description"
        content="هر سوالی جوابی داره - بچرخ انجمنی برای تبادل نظر و بالا بردن اطلاعات عمومی در مورد موضوعات مختلف">

    <link href="/" rel="canonical">
    <meta name="robots" content="index, follow">
@endsection

@section('content')
    <div class="row justify-content-center bg-wht">
        <div class="col-12">

            @if (session('success'))
                <p class="alert alert-success text-center">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <p class="alert alert-success text-center">{{ $error }}</p>
                @endforeach
            @endif

            <div class="row justify-content-center mt-4 mb-2">
                <div class="col-12 text-center">
                    <h1 id="page-title">بچرخ</h1>
                    <p class="mb-0" id="page-desc">هر سوالی جوابی داره</p>

                    <img id="online-users-home-bicon" alt="online user"
                        src="{{ $ftp_path . 'files/other/images/blue-circle.png' }}">
                    <span id="online-users-home">
                        {{ $online_user_count }} نفر آنلاین</span>
                    <img id="online-users-home-bicon" alt="online user"
                        src="{{ $ftp_path . 'files/other/images/blue-circle.png' }}">
                </div>
            </div>

            <div class="row justify-content-center pb-3">
                <div class="col-12 mx-2 text-center">
                    <div class="bslider mt-4" id="cat-slider">
                        @foreach ($categories as $category)
                            <div class="bslider-item cat-slider-item">
                                <a draggable="false" class="decor-none" id="{{ $category->slug }}"
                                    @if ($category->has_comments == 1) href="{{ route('question.index', $category->slug) }}?s=1"
                                    @elseif ($category->has_forums == 1)
                                    href="{{ route('question.index', $category->slug) }}"
                                    @else
                                    href="{{ route('ads.index', $category->slug) }}" @endif>
                                    <img draggable="false" class="home-cat-slider-img"
                                        alt="{{ $category->full_title ?? $category->title }}"
                                        title="{{ $category->full_title ?? $category->title }}"
                                        src="{{ asset($category->thumb()) }}">
                                    <span class="cat-slider-title">{{ $category->title }}
                                    </span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if (isset($hot_pages))
                <div class="row pt-4 justify-content-center">
                    @include('mainPart.hot-pages')
                </div>
            @endif

        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript"
        src="{{ asset('mixassets/js/home.min.js') . '?lm=' . filemtime('mixassets/js/home.min.js') }}"></script>
@endsection
