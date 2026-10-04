@extends('index')

@section('title')
    علاقه مندی ها
@endsection


@section('style')
    <link
        href="{{ asset('mixassets/css/user/favorite.min.css') . '?lm=' . filemtime('mixassets/css/user/favorite.min.css') }}"
        rel="stylesheet" type="text/css" />

    <meta name="robots" content="noindex">
@endsection

@section('content')
    <div class="row justify-content-center bg-wht p-2">
        @if (count($followItems) > 0)
            <div class="col-12 text-center mt-3 mb-2">
                <h1 id="fpage-title">انجمن های شما</h1>
            </div>
            @foreach ($followItems as $item)
                <div class="col-12 col-sm-8 col-md-5 mr-sm-2 text-right item-box">
                    <a class="item-link" href="{{ $item->withParentsCommentUrl() }}">
                        <img class="item-img" src="{{ $item->thumb() }}">
                        <span class="item-title">{{ $item->full_title ?? $item->title }}</span>
                    </a>
                </div>
            @endforeach
            <div class="col-12 my-4 overflow-auto text-center" id="pagination-div">
                {{ $followItems->links() }}
            </div>
        @else
            <div class="col-12 text-right p-2 mb-4">
                <div id="nofav-box">
                    <img src="{{ $ftp_path . 'files/other/images/nofav.gif' }}">
                    <br>
                    <span id="nofav-title">هنوز در هیچ انجمنی عضو نشده اید</span>
                </div>
            </div>
        @endif
        <div class="col-12 text-center mb-5">
            <a class="btn btn-primary mb-2" href="{{ route('question.index') }}">انجمن
                <img loading="lazy" src="{{ $ftp_path . 'files/other/images/next-light-w.png' }}">
            </a>
            <a class="btn btn-dark mb-2" href="{{ route('question.index') . '?s=1' }}">تجربیات و نظرات کاربران
                <img loading="lazy" src="{{ $ftp_path . 'files/other/images/next-light-w.png' }}">
            </a>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript"
        src="{{ asset('mixassets/js/user/favorite.min.js') . '?lm=' . filemtime('mixassets/js/user/favorite.min.js') }}">
        </script>
@endsection