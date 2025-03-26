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


            <div class="row justify-content-center q-bg mt-2 py-4">
                <div class="col-12 text-center">
                    <h1 id="page-title">بچرخ</h1>
                    <p class="mb-0" id="page-desc">هر سوالی جوابی داره</p>
                </div>
                <div class="col-12 mt-4 px-0">
                    <div class="bslider" id="question-slider">
                        @foreach ($questions as $question)
                            <div class="bslider-item mt-2 bg-wht">
                                <a draggable="false" id="{{ $question->id }}"
                                    href="{{ route('question.show', $question->slug2) }}" class="w-100 decor-none q-box">
                                    @if ($question->getImage())
                                        <img draggable="false" class="lazy-load hop-img"
                                            data-src="{{ $question->image() }}" alt="{{ $question->title }}">
                                    @endif
                                    <span class="q-title">{{ $question->title }}</span>
                                    <span class="q-ans">-{{ $question->answer }}</span>
                                    <br>
                                    @if (isset($question->items_title))
                                        <div class="w-100 overflow-hidden">
                                            @foreach ($question->items_title as $qi)
                                                <span class="q-item-title">{{ $qi }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="row justify-content-center bg-wht py-4" id="blog-box">
                <div class="col-12">
                    <div class="bslider" id="blog-slider">
                        @foreach ($products as $product)
                            <div class="bslider-item blog-slider-item mx-4">
                                <a class="blog-h-btn" draggable="false" id="product-{{ $product->id }}"
                                    href="{{ route('product.show', $product->slug) }}">
                                    <div class="bimg-box">
                                        <img draggable="false" class="mb-2 radius-10 blog-image lazy-load"
                                            alt="{{ $product->title }}" data-src="{{ $product->image }}">
                                    </div>
                                    <span class="blog-item-title">
                                        {{ Str::limit($product->title, 75) }}
                                    </span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if (isset($hot_pages))
                <div class="row q-bg pt-4">
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
