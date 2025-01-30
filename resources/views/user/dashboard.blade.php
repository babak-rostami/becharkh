@extends('index')

@section('title')
    {{ $dashuser->username }}
@endsection

@section('style')
    <meta name="robots" content="noindex, nofollow">

    <link href="{{ asset('mixassets/css/user/profile.min.css') . '?lm=' . filemtime('mixassets/css/user/profile.min.css') }}"
        rel="stylesheet" type="text/css" />
@endsection

@section('content')

    <div class="row justify-content-center pt-2 bg-wht align-items-center">
        <div class="col-12 text-center">
            @if (session('success'))
                <p class="alert alert-success">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <p class="alert alert-danger">{{ $error }}</p>
                @endforeach
            @endif
        </div>

        <div class="col-12 col-md-4 text-center">
            <img id="profile-image" class="mb-2" src="{{ asset($dashuser->image()) }}">
            <br>
            <b class="font-28">{{ $dashuser->name }}</b>
            <br>
            <span>{{ $dashuser->username }}</span>
            <div class="row mt-2 mb-3">
                <div class="col-6">
                    <b class="font-22">{{ $dashuser->follower_count ?? 0 }}</b>
                    <span>دنبال کننده</span>

                </div>
                <div class="col-6">
                    <b class="font-22">{{ $dashuser->following_count ?? 0 }}</b>
                    <span>دنبال شونده</span>
                </div>
            </div>
            @if ($user && $user->id != $dashuser->id)
                @if ($user->isFollow($dashuser->id))
                    <a class="btn btn-success mt-2" href="{{ route('unfollow', $dashuser->id) }}">دنبال
                        شده</a>
                @else
                    <a class="btn btn-primary mt-2" href="{{ route('follow', $dashuser->id) }}">دنبال
                        کردن</a>
                @endif
                <a class="btn btn-dark mt-2" href="{{ route('user.chat.start', $dashuser->id) }}">ارسال
                    پیام</a>
            @elseif(!$user)
                <a class="btn btn-primary mt-2" href="" data-toggle="modal" data-target="#login_user">دنبال
                    کردن</a>
                <a class="btn btn-dark mt-2" href="" data-toggle="modal" data-target="#login_user">ارسال
                    پیام</a>
            @endif
            @if ($user && $user->id == $dashuser->id)
                <a class="btn btn-dark mt-2" href="{{ route('user.dashboard.edit') }}">
                    مدیریت حساب
                    <img style="width: 32px" src="{{ $ftp_path . 'files/other/images/edit-profile.png' }}">
                </a>
            @endif
        </div>
        <div class="col-12 col-md-4 p-2 text-center mt-4">
            <span class="bold-font-title">درباره {{ $dashuser->username }}</span>

            @if (isset($dashuser->body) && $dashuser->body != '')
                <p class="text-gray textarea-preline">{{ $dashuser->body }}</p>
            @else
                <p class="text-gray">سلام من در انجمن بچرخ فعالیت میکنم</p>
            @endif
        </div>

        <div class="col-12 text-center mt-2">
            <a href={{ route('user.dashboard', ['username' => $dashuser->username, 'tab' => 'post']) }}
                class="px-4 btn btn-lg {{ $tab == 'post' || $tab == null ? 'active-tab' : 'not-active-tab' }}">مطالب</a>
            <a href={{ route('user.dashboard', ['username' => $dashuser->username, 'tab' => 'ad']) }}
                class="px-4 btn btn-lg {{ $tab == 'ad' ? 'active-tab' : 'not-active-tab' }}">آگهی
                ها</a>
        </div>
    </div>

    <div class="row py-3 bg-wht">
        <div class="col-12 text-right mx-0">
            @switch($tab)
                @case('ad')
                    <div class="row mx-0 px-2 px-md-0">
                        @if (!$advertises->isEmpty())
                            @foreach ($advertises as $advertise)
                                <div class="col-12 col-md-6 text-right px-0 w-100">
                                    <a class="decor-none" target="_blank"
                                        href="{{ route('ad.show', ['category_slug' => $advertise->category->slug, 'slug' => $advertise->slug, 'random' => $advertise->random_id]) }}">
                                        <div class="row align-items-center radius-10 ad-box mx-md-1 my-1 shadow-sm bg-wht">
                                            <div class="col-5 col-sm-4">
                                                <img style="object-fit: contain; width: 100%;height:140px"
                                                    alt="{{ $advertise->title }}" loading="lazy" title="{{ $advertise->title }}"
                                                    src="{{ asset($advertise->image()) }}">
                                            </div>
                                            <div class="col-7 col-sm-8 py-3">

                                                <b>{{ $advertise->title }}</b>
                                                <br>
                                                <span class="text-gray">{{ jdate($advertise->created_at)->ago() }}</span>
                                                <br>
                                                <span class="text-gray">{{ $advertise->location }}</span>
                                                <br>
                                                @if (isset($advertise->price))
                                                    <p class="mt-2" style="font-size: 17px;font-weight: 600">
                                                        {{ number_format((int) $advertise->price) }}
                                                        تومان</p>
                                                @else
                                                    <p class="mt-2" style="font-size: 17px;font-weight: 600">توافقی</p>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12 mb-5 mt-4 text-center p-4 bg-wht radius-10">
                                <b class="bold-font-title">آگهی ثبت نشده است</b>
                                <p class="my-3 my-3">در حال حاضر {{ $dashuser->username }} محصولی برای فروش ندارد</p>

                                @if ($user && $user->id == $dashuser->id)
                                    <a class="btn btn-light" href="{{ route('new.ad') }}">
                                        اولین آگهی را ثبت کنید
                                    </a>
                                @endif

                            </div>
                        @endif
                    </div>
                @break

                @case('post')
                    @if ($blogs->count() > 0)
                        <div class="row">
                            @foreach ($blogs as $blog)
                                <div class="col-6 col-md-4 px-0 px-lg-2 my-2">
                                    <a class="decor-none"
                                        href="{{ route('blog.show', ['category_slug' => $blog->category->slug, 'slug' => $blog->slug, 'random_id' => $blog->random_id]) }}">

                                        <div class="row mx-1 bg-wht radius-10 shadow-sm">
                                            <div class="col-12 text-center p-0">
                                                <img class="blog-image" src="{{ asset($blog->image()) }}">
                                            </div>
                                            <div class="col-12 my-2">
                                                <span class="mt-2 blog-title">
                                                    {{ $blog->title }}</span>
                                                <hr>
                                                <img class="like-image"
                                                    src="{{ $ftp_path . 'files/other/images/white-like.png' }}">
                                                <span id="like-count-span">{{ $blog->likes->count() }}</span>
                                                <img class="mr-3 comment-image"
                                                    src="{{ $ftp_path . 'files/other/images/comment2.png' }}">
                                                <span class="mr-1">
                                                    {{ isset($blog->comment_count) ? $blog->comment_count : 0 . '+' }}
                                                </span>
                                            </div>
                                        </div>

                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="row justify-content-center">
                            <div class="col-12 mb-5 mt-4 text-center p-4 bg-wht radius-10">
                                <b class="bold-font-title">مطلبی پیدا نشد</b>
                                <p class="my-3">هنوز {{ $dashuser->username }} مطلبی ننوشته است</p>

                            </div>
                        </div>
                    @endif
                @break

                @default
                    @if ($blogs->count() > 0)
                        <div class="row">
                            @foreach ($blogs as $blog)
                                <div class="col-6 col-md-4 px-0 px-lg-2 my-2">
                                    <a class="decor-none"
                                        href="{{ route('blog.show', ['category_slug' => $blog->category->slug, 'slug' => $blog->slug, 'random_id' => $blog->random_id]) }}">

                                        <div class="row mx-1 bg-wht radius-10 shadow-sm">
                                            <div class="col-12 text-center p-0">
                                                <img class="blog-image" src="{{ asset($blog->image()) }}">
                                            </div>
                                            <div class="col-12 my-2">
                                                <span class="mt-2 blog-title">
                                                    {{ $blog->title }}</span>
                                                <hr>
                                                <img class="like-image"
                                                    src="{{ $ftp_path . 'files/other/images/white-like.png' }}">
                                                <span id="like-count-span">{{ $blog->likes->count() }}</span>
                                                <img class="mr-3 comment-image"
                                                    src="{{ $ftp_path . 'files/other/images/comment2.png' }}">
                                                <span class="mr-1">
                                                    {{ isset($blog->comment_count) ? $blog->comment_count : 0 . '+' }}
                                                </span>
                                            </div>
                                        </div>

                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="row justify-content-center">
                            <div class="col-12 mb-5 mt-4 text-center p-4 bg-wht radius-10">
                                <b class="bold-font-title">مطلبی پیدا نشد</b>
                                <p class="my-3">هنوز {{ $dashuser->username }} مطلبی ننوشته است</p>

                            </div>
                        </div>
                    @endif
            @endswitch

        </div>
    </div>



@endsection

@section('script')
@endsection
