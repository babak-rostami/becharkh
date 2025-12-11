@extends('index')


@section('title')
    صفحه مورد نظر پیدا نشد
@endsection

@section('style')
    <meta name="robots" content="noindex">
    <style>
        .hop-item {
            text-align: center;
            display: block;
            text-decoration: none !important;
            background-color: #fff;
            border: 2px solid #f7f7f7;
            color: #000;
            border-radius: 8px;
            padding: 8px 12px;
            box-shadow: 0px 0px 20px 0 #f1f1f1;
        }

        .hop-item:hover {
            box-shadow: inset 0 0 20px 0 #ddd;
        }

        .hop-title {
            font-weight: 600;
            font-size: 22px;
            display: block;
            line-height: 36px;
        }

        .hop-body {
            font-size: 18px;
            color: #525252;
            line-height: 36px;
        }

        .hop-img {
            max-width: 100%;
            max-height: 240px;
            object-fit: contain;
            border-radius: 36px;
            margin: 12px 0;
            border: 2px solid #f1f1f1;
            background-color: #f7f7f7;
            padding: 8px;
        }
    </style>
    <link href="{{ asset('mixassets/css/style.min.css') . '?lm=' . filemtime('mixassets/css/style.min.css') }}"
        rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row justify-content-center bg-wht mb-5 mx-2 radius-10">
        <div class="col-12 text-center bg-wht mt-5 radius-10">
            <h2 class="bold-font-title">صفحه مورد نظر پیدا نشد</h2>
            <p class="mt-3">ممکن است آدرس تغییر کرده باشد دوباره جستجو کنید</p>
        </div>

        {{-- <div class="col-12 text-center mt-2 mb-5">
            <a class="btn btn-primary mt-4" rel="nofollow" href="{{ route('question.create') }}">
                سوال جدید +
            </a>
            <br>
            <img class="mt-3 lazy-load" data-src="{{ $ftp_path . 'files/other/images/uarrow.gif' }}" alt="arrow down">
            <br>
            <span>سوال شما قبلا در انجمن پرسیده نشده است؟</span>
        </div> --}}

        @include('mainPart.hot-pages', ['hot_pages' => Cache::get('hot_pages')])

    </div>
@endsection


@section('script')
    <script type="text/javascript"
        src="{{ asset('mixassets/js/main.min.js') . '?lm=' . filemtime('mixassets/js/main.min.js') }}"></script>
@endsection
