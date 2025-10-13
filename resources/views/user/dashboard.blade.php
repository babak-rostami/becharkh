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

            @if ($user && $user->id == $dashuser->id && !$user->email_actived)
                <span id="active-email-msg">
                    <img class="lazy-load rcir-glow" data-src="{{ $ftp_path . 'files/other/images/red-circle.png' }}">
                    ایمیل شما تایید نشده است!
                </span>
                <a class="btn btn-dark my-2" href="{{ route('user.dashboard.edit') }}">
                    مدیریت حساب و تایید ایمیل
                </a>
            @elseif ($user && $user->id == $dashuser->id)
                <a class="btn btn-dark my-2" href="{{ route('user.dashboard.edit') }}">
                    مدیریت حساب
                </a>
            @endif

            @if ($user_contents->isEmpty())
                <div id="duser-no-coms-box">
                    <span id="duser-no-coms-title">نظرات و تجربیات</span>
                    <span>نظرات کاربر در انجمن بچرخ اینجا نمایش داده میشه</span>
                    <br>
                    <span>هنوز نظری ثبت نشده</span>
                </div>
            @else
                <div class="row mx-0" id="commentsBox">
                    @foreach ($user_contents as $count => $user_content)
                        @if ($user_content->type == 'comment')
                            <div class="col-12 px-3 pb-3 pt-2 radius-10 mt-4 shadow-sm comment-box text-right"
                                id="comment-box-{{ $user_content->id }}">

                                <span class="cm-box-time">{{ jdate($user_content->created_at)->ago() }}</span>
                                @if (isset($user_content->iimages))
                                    <div class="com-img-box">
                                        @foreach ($user_content->iimages as $iimg)
                                            <img alt="comment image {{ $iimg['id'] }}"
                                                onclick="clickGalleryImg('iimg-{{ $user_content->id }}-{{ $iimg['id'] }}','comment')"
                                                id="iimg-{{ $user_content->id }}-{{ $iimg['id'] }}"
                                                class="com-img lazy-load" data-src="{{ $ftp_path . $iimg['path'] }}">
                                        @endforeach
                                    </div>
                                @endif

                                @if (isset($user_content->editor))
                                    <div class="cedshow">
                                        {!! $user_content->editor !!}
                                    </div>
                                @elseif(isset($user_content->editor2))
                                    <div class="cedshow">
                                        {!! $user_content->editor2 !!}
                                    </div>
                                @else
                                    <p class="ml-2 mt-4 font-18 textarea-preline">{{ $user_content->body }}</p>
                                @endif

                                <hr>

                                @if ($user_content->replies_count)
                                    <p class="text-center">{{ $user_content->replies_count }} پاسخ داده شده</p>
                                @endif

                                @if ($user_content->page_img)
                                    <div class="text-center">
                                        @if ($user_content->page_url)
                                            <a href="{{ $user_content->page_url }}" class="cm-img-link">
                                                <img class="cm-box-img" src="{{ $user_content->page_img }}">
                                                <span class="link-icon">
                                                    مشاهده مطلب
                                                    <img src="{{ $ftp_path . 'files/other/images/next-light.png' }}">
                                                </span>
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    @if ($user_content->page_url)
                                        <a class="btn btn-lg btn-primary w-100" href="{{ $user_content->page_url }}">مشاهده
                                            مطلب</a>
                                    @endif
                                @endif
                            </div>
                        @elseif($user_content->type == 'question')
                            @if ($user && $user->id == $dashuser->id)
                                <div class="col-12 px-3 pb-3 pt-2 radius-10 mt-4 shadow-sm comment-box text-right"
                                    id="comment-box-{{ $user_content->id }}">

                                    <span class="q-box-title">{{ $user_content->title }}</span>
                                    @if (isset($user_content->body))
                                        <p class="ml-2 mt-4 font-18 textarea-preline">{{ $user_content->body }}</p>
                                    @endif


                                    @if ($user_content->answer_count || $user_content->page_url)
                                        <hr>
                                    @endif

                                    @if ($user_content->answer_count)
                                        <p class="text-center">{{ $user_content->answer_count }} پاسخ داده شده</p>
                                    @endif

                                    @if ($user_content->page_img)
                                        @if ($user_content->page_url)
                                            <div class="text-center">
                                                <a href="{{ $user_content->page_url }}" class="cm-img-link">
                                                    <img class="cm-box-img" src="{{ $user_content->page_img }}">
                                                    <span class="link-icon">
                                                        مشاهده مطلب
                                                        <img src="{{ $ftp_path . 'files/other/images/next-light.png' }}">
                                                    </span>
                                                </a>
                                            </div>
                                        @endif
                                    @else
                                        @if ($user_content->page_url)
                                            <a class="btn btn-lg btn-primary w-100"
                                                href="{{ $user_content->page_url }}">مشاهده
                                                مطلب</a>
                                        @endif
                                    @endif
                                </div>
                            @else
                                @if ($user_content->status == 1)
                                    <div class="col-12 px-3 pb-3 pt-2 radius-10 mt-4 shadow-sm comment-box text-right"
                                        id="comment-box-{{ $user_content->id }}">

                                        <span class="q-box-title">{{ $user_content->title }}</span>
                                        @if (isset($user_content->body))
                                            <p class="ml-2 mt-4 font-18 textarea-preline">{{ $user_content->body }}</p>
                                        @endif


                                        @if ($user_content->answer_count || $user_content->page_url)
                                            <hr>
                                        @endif

                                        @if ($user_content->answer_count)
                                            <p class="text-center">{{ $user_content->answer_count }} پاسخ داده شده</p>
                                        @endif

                                        @if ($user_content->page_img)
                                            @if ($user_content->page_url)
                                                <div class="text-center">
                                                    <a href="{{ $user_content->page_url }}" class="cm-img-link">
                                                        <img class="cm-box-img" src="{{ $user_content->page_img }}">
                                                        <span class="link-icon">
                                                            مشاهده مطلب
                                                            <img
                                                                src="{{ $ftp_path . 'files/other/images/next-light.png' }}">
                                                        </span>
                                                    </a>
                                                </div>
                                            @endif
                                        @else
                                            @if ($user_content->page_url)
                                                <a class="btn btn-lg btn-primary w-100"
                                                    href="{{ $user_content->page_url }}">مشاهده
                                                    مطلب</a>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            @endif
                        @endif
                    @endforeach
                </div>

                @include('mainPart.gallery')
            @endif

        </div>

    </div>

@endsection

@section('script')
    <script type="text/javascript"
        src="{{ asset('mixassets/js/user/profile.min.js') . '?lm=' . filemtime('mixassets/js/user/profile.min.js') }}">
    </script>
@endsection
