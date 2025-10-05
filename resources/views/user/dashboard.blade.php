@extends('index')

@section('title')
    {{ $dashuser->name }} - {{ $dashuser->username }} | بچرخ
@endsection

@section('style')
    <meta name="robots" content="noindex, nofollow">

    <link href="{{ asset('mixassets/css/user/profile.min.css') . '?lm=' . filemtime('mixassets/css/user/profile.min.css') }}"
        rel="stylesheet" type="text/css" />
@endsection

@section('content')

    <div class="row justify-content-center pt-2 bg-wht align-items-center">
        <div class="col-12 col-md-10 text-center">
            @if (session('success'))
                <p class="alert alert-success">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <p class="alert alert-danger">{{ $error }}</p>
                @endforeach
            @endif
        </div>

        <div class="col-12 col-md-10 text-center mb-5 pb-5">
            <img id="profile-image" class="mb-2" src="{{ asset($dashuser->image()) }}">
            <br>
            <b class="font-28">{{ $dashuser->name }}</b>
            <br>
            <span>{{ $dashuser->username }}</span>
            @if (isset($dashuser->body) && $dashuser->body != '')
                <p class="text-gray mt-2 textarea-preline">{{ $dashuser->body }}</p>
            @else
                <p class="text-gray mt-2">به زودی بیوگرافی خودم را در بچرخ تکمیل میکنم</p>
            @endif
            @if ($user && $user->id == $dashuser->id)
                <a class="btn btn-dark mt-2" href="{{ route('user.dashboard.edit') }}">
                    مدیریت حساب
                </a>
            @endif

            @if ($user_likes > 0)
                <div class="col-12 text-center mb-3">
                    <span class="likes-count">{{ $user_likes }} ❤️</span>
                </div>
            @endif

            @if ($comments->isEmpty())
                <div id="duser-no-coms-box">
                    <span id="duser-no-coms-title">نظرات و تجربیات</span>
                    <span>نظرات کاربر در انجمن بچرخ اینجا نمایش داده میشه</span>
                    <br>
                    <span>هنوز نظری ثبت نشده</span>
                </div>
            @else
                <div class="row mx-0" id="commentsBox">
                    @foreach ($comments as $count => $comment)
                        <div class="col-12 px-3 pb-3 pt-2 radius-10 mt-4 shadow-sm comment-box text-right"
                            id="comment-box-{{ $comment->id }}">
                            <span class="cm-box-time">{{ jdate($comment->created_at)->ago() }}</span>
                            @if (isset($comment->iimages))
                                <div class="com-img-box">
                                    @foreach ($comment->iimages as $iimg)
                                        <img alt="comment image {{ $iimg['id'] }}"
                                            onclick="clickGalleryImg('iimg-{{ $comment->id }}-{{ $iimg['id'] }}','comment')"
                                            id="iimg-{{ $comment->id }}-{{ $iimg['id'] }}" class="com-img lazy-load"
                                            data-src="{{ $ftp_path . $iimg['path'] }}">
                                    @endforeach
                                </div>
                            @endif

                            @if (isset($comment->editor))
                                <div class="cedshow">
                                    {!! $comment->editor !!}
                                </div>
                            @elseif(isset($comment->editor2))
                                <div class="cedshow">
                                    {!! $comment->editor2 !!}
                                </div>
                            @else
                                <p class="ml-2 mt-4 font-18 textarea-preline">{{ $comment->body }}</p>
                            @endif

                            @include('survey.surshow', [
                                'object' => $comment,
                            ])

                            <hr>

                            @if ($comment->replies_count)
                                <p class="text-center">{{ $comment->replies_count }} پاسخ داده شده</p>
                            @endif
                            @if ($comment->page_url)
                                <a class="btn btn-lg btn-primary w-100" href="{{ $comment->page_url }}">مشاهده مطلب</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

        </div>

    </div>

@endsection

@section('script')
    <script type="text/javascript"
        src="{{ asset('mixassets/js/main.min.js') . '?lm=' . filemtime('mixassets/js/main.min.js') }}"></script>
@endsection
