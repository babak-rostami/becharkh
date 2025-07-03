@extends('index')


@section('title')
    صفحه مورد نظر پیدا نشد
@endsection

@section('style')
    <meta name="robots" content="noindex">
    <style>
        .hop-item {
            text-align: right;
            display: block;
            border: 1px solid #dee2e6;
            margin-bottom: 8px;
            padding: 8px;
            border-radius: 8px;
            text-decoration: none !important;
        }

        .hop-item:hover {
            background-color: #dee2e6;
        }

        .hop-title {
            font-weight: 600;
            color: #1c244f;
            display: block;
        }

        .hop-body {
            font-size: 14px;
            color: #525252;
        }

        .hop-img {
            max-width: 100%;
            max-height: 160px;
            border-radius: 8px;
            margin-bottom: 8px;
        }
    </style>
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
@endsection
