@extends('index')

@section('title')
    داشبورد
@endsection

@section('style')
    <link
        href="{{ asset('mixassets/css/user/dashboard-edit.min.css') . '?lm=' . filemtime('mixassets/css/user/dashboard-edit.min.css') }}"
        rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <section id="main">

        <div class="modal fade" id="change_email" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content radius-10 py-3">
                    <div class="modal-body text-right">
                        <span>ایمیل ثبت شده شما</span>
                        <br>
                        <span id="change-email-ue">{{ $user->email }}</span>
                        <hr>
                        <span>آدرس ایمیل جدید را وارد کنید</span>
                        <input class="form-control my-2" type="text" id="change-email-input">
                        <span>برای تایید ایمیل لینک فعالسازی به آدرس ایمیل جدید ارسال
                            میشود</span>
                        <button onclick="changeEmail()" class="btn btn-primary w-100 mt-2" id="change-email-btn">تغییر
                            ایمیل</button>
                        <span id="change-email-msg"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center py-2 bg-wht">

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
            @if (!$user->email_actived)
                @if (isset($user->active_code))
                    <div class="col-12 text-center mt-2">
                        <button class="btn btn-sm btn-primary" id="active-email-btn" onclick="activeEmail()">ارسال مجدد
                            لینک فعالسازی</button>
                        <a class="btn btn-sm btn-secondary" href="" data-toggle="modal"
                            data-target="#change_email">تغییر ایمیل</a>
                        <span id="active-email-msg">پیامی برای فعالسازی حساب کاربری به ایمیل شما ارسال شده است
                            {{ $user->email }}</span>
                        <hr>
                    </div>
                @else
                    <div class="col-12 text-center mt-2">
                        <button class="btn btn-sm btn-primary" id="active-email-btn" onclick="activeEmail()">ارسال
                            لینک تایید ایمیل</button>
                        <a class="btn btn-sm btn-secondary" href="" data-toggle="modal"
                            data-target="#change_email">تغییر ایمیل</a>
                        <span id="active-email-msg">ایمیل شما تایید نشده است!</span>
                        <img class="lazy-load rcir-glow" data-src="{{ $ftp_path . 'files/other/images/red-circle.png' }}">
                        <hr>
                    </div>
                @endif
            @endif

            <div class="p-2 mt-2 text-center d-none d-sm-block">

                {{-- <a class="btn btn-lg btn-outline-dark mt-1" href="{{ route('user.dashboard.edit', 'profile') }}">
                    <img src="{{ $ftp_path . 'files/other/images/edit-profile2.png' }}">
                    ویرایش اطلاعات
                </a> --}}

                <a class="btn btn-lg mt-1 mx-1 btn-outline-primary" href="{{ route('new.ad') }}">
                    <img style="width: 24px" src="{{ $ftp_path . 'files/other/images/basket.png' }}">
                    آگهی جدید</a>

                <a class="btn btn-lg mt-1 mx-1 btn-outline-secondary" href="{{ route('question.create') }}">
                    <img style="width: 24px" src="{{ $ftp_path . 'files/other/images/question.png' }}">
                    سوال جدید</a>
            </div>

            <div class="col-12 text-right mt-4 bg-gray pb-3">
                <div class="user-image-div">
                    <img class="user-image" id="user-image" src="{{ asset($user->image()) }}" alt="{{ $user->username }}"
                        title="{{ $user->username }}">
                    <img id="loading-image-icon" src="{{ $ftp_path . 'files/other/images/loading.png' }}">
                    <input type="file" id="user-image-input" accept="image/*">
                    <a class="btn btn-sm btn-info text-white"
                        onclick="document.getElementById('user-image-input').click()">تغییر
                        عکس</a>
                </div>
                <br>
                <span class="bold-font-title mt-2">موجودی</span>
                <b id="money-amount-txt">{{ $user_money }} تومان</b>
                <a class="btn btn-sm btn-primary mr-2" id="inc-money-btn" href="" data-toggle="modal"
                    data-target="#charge-account">افزایش
                    اعتبار</a>
                <div class="mt-3">
                    <span>{{ $user->username }}</span>
                    <a class="btn btn-sm btn-danger" href="{{ route('user.logout') }}">خروج از حساب کاربری</a>
                </div>
            </div>

            <div class="col-12 mt-2 text-center" id="dash-tabs">
                <a href={{ route('user.dashboard.edit', 'profile') }}
                    class="btn mt-2 {{ $tab == 'profile' || $tab == '' ? 'active-tab' : 'not-active-tab' }}">مدیریت
                    حساب</a>
                <a href={{ route('user.dashboard.edit', 'ad') }}
                    class="btn mt-2 {{ $tab == 'ad' ? 'active-tab' : 'not-active-tab' }}">آگهی ها</a>
                {{-- <a href={{ route('user.dashboard.edit', 'post') }}
                    class="btn mt-2 {{ $tab == 'post' ? 'active-tab' : 'not-active-tab' }}">مطالب</a> --}}
                <a href={{ route('user.dashboard.edit', 'forum') }}
                    class="btn mt-2 {{ $tab == 'forum' ? 'active-tab' : 'not-active-tab' }}">سوال ها</a>
            </div>

            <div class="col-12">

                @switch($tab)
                    @case('ad')
                        <div class="row justify-content-center">
                            @if (!$uadvertises->isEmpty())
                                @foreach ($uadvertises as $key => $adver)
                                    <div class="col-12 col-md-6 text-right ad-item-div">
                                        <img class="rounded" style="width: 80px" alt="{{ $adver->title }}"
                                            title="{{ $adver->title }}" src="{{ asset($adver->thumbnail()) }}">
                                        <div>
                                            <span class="mx-2">{{ $adver->title }}</span>
                                            <br>
                                            @if ($adver->status == 1)
                                                <span class="text-success mx-2">تایید شده</span>
                                            @else
                                                @if ($user->email_actived != 1)
                                                    <span class="text-danger mx-2">
                                                        برای نمایش باید ایمیل خود را تایید کنید
                                                    </span>
                                                @else
                                                    @if ($adver->not_paid == 1)
                                                        <span class="text-danger mx-2">در
                                                            انتظار پرداخت</span>
                                                    @else
                                                        @if ($adver->not_cat == 1)
                                                            <span class="text-danger mx-2">در انتشار
                                                                تایید</span>
                                                        @endif
                                                    @endif
                                                @endif
                                            @endif
                                            <br>
                                            @if ($adver->status == 1)
                                                <a class="btn btn-sm btn-outline-success mt-2" target="_blank" data-toggle="modal"
                                                    data-target="#rocket-ad-modal-{{ $adver->id }}" href="">
                                                    موشک
                                                </a>
                                                <a class="btn btn-sm btn-outline-primary mt-2" target="_blank"
                                                    href="{{ route('ad.show', ['category_slug' => $adver->category->slug, 'slug' => $adver->slug, 'random' => $adver->random_id]) }}">
                                                    مشاهده
                                                </a>
                                                <a class="btn btn-sm btn-outline-secondary mt-2"
                                                    href="{{ route('ad.edit', $adver->id) }}">
                                                    ویرایش
                                                </a>
                                                <a class="btn btn-sm btn-outline-danger mt-2" data-toggle="modal"
                                                    data-target="#delete-ad-modal-{{ $adver->id }}" href="">
                                                    حذف
                                                </a>
                                            @else
                                                @if ($adver->not_paid == 1)
                                                    @if ($user->canCreateAd())
                                                        <a href="" href="" data-toggle="modal"
                                                            data-dismiss="modal" data-target="#pay-ad-from-acc"
                                                            class="btn btn-sm btn-success">از
                                                            موجودی اکانت پرداخت شود؟</a>
                                                        <div class="modal fade" id="pay-ad-from-acc" tabindex="-1"
                                                            role="dialog" aria-labelledby="exampleModalLabel"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-body">
                                                                        <div class="row">
                                                                            <div class="col-12 text-center">
                                                                                <p>{{ $adver->title }}</p>
                                                                                <hr>
                                                                                <p>موجودی حساب کافی است ، میخواهید آگهی تایید و
                                                                                    نمایش داده شود؟</p>
                                                                                <span id="ac-ad-ymoney-t">موجودی شما</span>
                                                                                <span
                                                                                    class="ac-ad-money-c">{{ $user->money ?? 0 }}
                                                                                    تومان</span>
                                                                                <br>
                                                                                <span id="ac-ad-nmoney-t">مورد نیاز</span>
                                                                                <span class="ac-ad-money-c">{{ $ad_price }}
                                                                                    تومان</span>
                                                                                <br>
                                                                                <form class="d-inline"
                                                                                    action="{{ route('pay.ad.from.acc', $adver->id) }}"
                                                                                    method="POST">
                                                                                    @csrf
                                                                                    <button id="pay-ad-btn-{{ $adver->id }}"
                                                                                        onclick="payAd('{{ $adver->id }}')"
                                                                                        type="submit"
                                                                                        class="btn btn-success w-50 mt-3">بله</button>
                                                                                    <button
                                                                                        id="pay-ad-btn-loading-{{ $adver->id }}"
                                                                                        type="button"
                                                                                        class="btn btn-light w-50 mt-3">منتظر
                                                                                        بمانید...</button>
                                                                                </form>
                                                                                <a class="btn btn-secondary mt-3" href=""
                                                                                    data-dismiss="modal">بعدا</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                    @endif
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    <div class="modal fade" id="rocket-ad-modal-{{ $adver->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-12 text-center">
                                                            <img style="width: 24px"
                                                                src="{{ $ftp_path . 'files/other/images/rocket.png' }}">
                                                            <b>آیا میخواهید از قابلیت <span style="color: #38b000">موشک</span>
                                                                برای این آگهی
                                                                استفاده کنید؟</b>
                                                            <p>با تایید این گزینه آگهی شما دوباره به ابتدای لیست آگهی ها
                                                                باز میگردد</p>
                                                            <hr>
                                                            <span id="ac-ad-ymoney-t">موجودی شما</span>
                                                            <span class="ac-ad-money-c">{{ $user->money ?? 0 }}
                                                                تومان</span>
                                                            <br>
                                                            <span id="ac-ad-nmoney-t">مورد نیاز</span>
                                                            <span class="ac-ad-money-c">{{ $ad_rocket }}
                                                                تومان</span>
                                                            <hr>
                                                            <a id="rock-ad-btn-{{ $adver->id }}"
                                                                onclick="rockAd('{{ $adver->id }}')"
                                                                class="btn btn-success w-50"
                                                                href="{{ route('rocket.ad', $adver->id) }}">انجام
                                                                شود</a>
                                                            <button id="rock-ad-btn-loading-{{ $adver->id }}"
                                                                class="btn btn-light w-50" type="button">منتظر بمانید...</button>
                                                            <a class="btn btn-secondary" href=""
                                                                data-dismiss="modal">بعدا</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="delete-ad-modal-{{ $adver->id }}" tabindex="-1"
                                        role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-12 text-center">

                                                            <h4>آگهی حذف شود؟</h4>
                                                            <b>مطمئنید میخواهید آگهی حذف شود؟</b>
                                                            <hr>
                                                            <a class="btn btn-warning w-50" href=""
                                                                data-dismiss="modal">بعدا</a>
                                                            <a class="btn btn-danger"
                                                                href="{{ route('destroy.ad', $adver->id) }}">بله</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-11 col-sm-10 col-md-8 p-4 bg-wht radius-10 mb-5 mt-2 text-center">
                                    <p class="text-center mt-4" style="font-size: 22px;font-weight: 600">آگهی ندارید
                                    </p>
                                    <p>محصول یا خدمات خود را آگهی کنید</p>
                                    <a class="btn btn-lg my-3 btn-outline-success" href="{{ route('new.ad') }}">آگهی جدید +</a>
                                </div>
                            @endif

                        </div>
                    @break

                    {{-- @case('post')
                        @if ($uBlogs->count() > 0)
                            <div class="row justify-content-center mt-3">
                                @foreach ($uBlogs as $key => $blog)
                                    <div class="col-11 col-md-5 shadow-sm mb-2 text-right post-box mx-1">
                                        <img class="post-img mb-2" alt="{{ $blog->title }}" title="{{ $blog->title }}"
                                            src="{{ asset($blog->thumb()) }}">
                                        <span>وضعیت </span>
                                        @if ($blog->status == 2)
                                            <span class="text-danger">ذخیره موقت</span>
                                        @elseif($blog->status == 0)
                                            <span class="text-danger">در انتظار تایید</span>
                                        @else
                                            <span class="text-success">ثبت شده</span>
                                        @endif

                                        <div class="position-relative d-inline-block mr-2">
                                            <img class="img-count" src="{{ $ftp_path . 'files/other/images/b-comment.gif' }}">
                                            <span class="span-count">{{ $blog->comment_count ?? 0 }}</span>
                                        </div>
                                        <div class="position-relative d-inline-block mr-4">
                                            <img class="img-count" src="{{ $ftp_path . 'files/other/images/b-show.gif' }}">
                                            <span class="span-count">{{ $blog->seen_count }}</span>
                                        </div>
                                        <div class="float-left post-info-closed" id="open-edit-post-btn-{{ $blog->id }}"
                                            onclick="openEditPostBox('{{ $blog->id }}')">
                                        </div>
                                        <br>
                                        <span class="font-w-600">{{ $blog->title }}</span>

                                        <div id="post-edit-{{ $blog->id }}">
                                            @if ($blog->status == 2)
                                                <a class="btn btn-sm btn-outline-secondary my-2"
                                                    href="{{ route('user.new.post', $blog->id) }}">
                                                    <img class="edit-post-img"
                                                        src="{{ $ftp_path . 'files/other/images/edit1.png' }}">
                                                    ویرایش
                                                </a>
                                                <a class="btn btn-sm btn-danger" href="" data-toggle="modal"
                                                    data-dismiss="modal" data-target="#dlpost-{{ $blog->id }}">حذف
                                                </a>
                                            @else
                                                <a class="btn btn-sm btn-outline-primary" target="_blank"
                                                    href="{{ route('blog.show', ['category_slug' => $blog->category->slug, 'slug' => $blog->slug, 'random_id' => $blog->random_id]) }}">
                                                    <img src="{{ $ftp_path . 'files/other/images/eye.png' }}">
                                                    مشاهده
                                                </a>
                                                <a class="btn btn-sm btn-outline-secondary my-2"
                                                    href="{{ route('user.edit.post', $blog->id) }}">
                                                    <img class="edit-post-img"
                                                        src="{{ $ftp_path . 'files/other/images/edit1.png' }}">
                                                    ویرایش
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="modal fade" id="dlpost-{{ $blog->id }}" tabindex="-1" role="dialog"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <div class="row" id="checkEmailLoginModal">
                                                        <div class="col-12 text-center">
                                                            <p>مطمئنید میخواهید این پست حذف شود؟</p>
                                                            <form action="{{ route('user.destroy.post') }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <input type="hidden" name="post_id"
                                                                    value="{{ $blog->id }}">
                                                                <button class="btn btn-dark" data-dismiss="modal">بیخیال</button>
                                                                <button class="btn btn-danger" type="submit">حذف شود</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        @else
                            <div class="row justify-content-center">
                                <div class="col-11 col-sm-10 col-md-8 p-4 bg-wht radius-10 mb-5 mt-2 text-center">
                                    <div class="text-center mt-4">
                                        <p style="font-size: 22px;font-weight: 600">مطلبی آموزشی ننوشته اید</p>
                                        <p>در انجمن مورد نظر خود مطلبی آموزشی بنویسید</p>
                                    </div>
                                    <a class="btn btn-lg btn-outline-success mt-2 mb-5" href="{{ route('user.new.post') }}">
                                        مطلبی جدید بنویسید +
                                    </a>
                                </div>
                            </div>
                        @endif
                    @break --}}
                    @case('profile')
                        <div class="row justify-content-center my-3">
                            <div class="col-11 col-sm-10 text-right">
                                <form action="{{ route('user.update') }}" method="POST" role="form"
                                    enctype="multipart/form-data" id="userUpdateForm"
                                    onsubmit="return handleUserUpdateSubmit();">
                                    @csrf
                                    {{ method_field('PUT') }}
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>بیوگرافی</label>
                                                <textarea style="resize: none;height: 150px;" class="form-control" name="body"
                                                    placeholder="درباره خود بنویسید...">{{ $user->body }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>نام شما</label>
                                                <input required type="text" class="form-control" name="name"
                                                    placeholder="نام خود را بنویسید" value="{{ $user->name }}">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>شماره تلفن</label>
                                                <input type="text" class="form-control" name="phone" id="phone"
                                                    placeholder="شماره تلفن را اینجا وارد کنید" value="{{ $user->phone }}">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <input readonly="readonly" required type="text" class="form-control"
                                                    placeholder="نام کاربری را اینجا وارد کنید" value="{{ $user->username }}">
                                                <a class="btn btn-sm btn-danger" href="" data-toggle="modal"
                                                    data-target="#changeUserNameModal">تغییر نام کاربری</a>
                                            </div>
                                            <div class="form-group">
                                                <input type="text" readonly="readonly" class="form-control"
                                                    value="{{ $user->email }}">
                                            </div>
                                        </div>

                                        <div class="col-12 mb-4">
                                            <button id="update-user-submit-btn" type="submit"
                                                class="btn btn-success mt-3 w-100">ذخیره تغییرات</button>
                                            <button id="update-user-loading-btn" type="button"
                                                class="btn btn-light mt-3 w-100">در حال ثبت...</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @break

                    @case('forum')
                        <div class="row justify-content-center my-4" style="overflow-x:auto">
                            @if ($uquestions->count() > 0)
                                <div class="col-12 text-center">
                                    <table class="table table-hover">
                                        <tbody>
                                            @foreach ($uquestions as $key => $question)
                                                <tr>
                                                    <td>{{ $question->title }}</td>
                                                    @if ($question->status == 1)
                                                        <td><span class="text-success">تایید شده</span></td>
                                                    @else
                                                        <td><span class="text-danger">تایید نشده</span></td>
                                                    @endif
                                                    <td><span
                                                            class="badge badge-success">{{ $question->answers()->count() }}</span>
                                                        پاسخ
                                                    </td>
                                                    <td>{{ jdate($question->created_at)->ago() }}</td>
                                                    <td>
                                                        <a class="btn btn-outline-primary" target="_blank"
                                                            href="{{ route('question.show', ['category' => $question->category->slug, 'slug' => $question->slug, 'random' => $question->random_id]) }}">
                                                            <img src="{{ $ftp_path . 'files/other/images/eye.png' }}">
                                                            مشاهده
                                                        </a>
                                                        <a class="btn btn-outline-secondary my-2"
                                                            href="{{ route('question.edit', $question->id) }}">
                                                            <img src="{{ $ftp_path . 'files/other/images/edit1.png' }}">
                                                            ویرایش
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="col-11 col-sm-10 col-md-8 bg-wht p-3 text-center radius-10">
                                    <div class="text-center mt-4">
                                        <p style="font-size: 22px; font-weight: 600">سوالی مطرح نکرده اید</p>
                                        <p>سوال خود را در انجمن مطرح کرده و از تجربه هزاران نفر استفاده کنید</p>
                                    </div>

                                    <a class="btn btn-lg btn-outline-success my-2" href="{{ route('question.create') }}">سوال
                                        جدید +</a>
                                </div>
                            @endif

                        </div>
                    @break

                    @default
                        <div class="row justify-content-center my-3">
                            <div class="col-11 col-sm-10 text-right">
                                <form action="{{ route('user.update') }}" method="POST" role="form"
                                    enctype="multipart/form-data" id="userUpdateForm"
                                    onsubmit="return handleUserUpdateSubmit();">
                                    @csrf
                                    {{ method_field('PUT') }}
                                    <div class="row">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>بیوگرافی</label>
                                                <textarea style="resize: none;height: 150px;" class="form-control" name="body"
                                                    placeholder="درباره خود بنویسید...">{{ $user->body }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>نام شما</label>
                                                <input required type="text" class="form-control" name="name"
                                                    placeholder="نام خود را بنویسید" value="{{ $user->name }}">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>شماره تلفن</label>
                                                <input type="text" class="form-control" name="phone" id="phone"
                                                    placeholder="شماره تلفن را اینجا وارد کنید" value="{{ $user->phone }}">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <input readonly="readonly" required type="text" class="form-control"
                                                    placeholder="نام کاربری را اینجا وارد کنید" value="{{ $user->username }}">
                                                <a class="btn btn-sm btn-danger" href="" data-toggle="modal"
                                                    data-target="#changeUserNameModal">تغییر نام کاربری</a>
                                            </div>
                                            <div class="form-group">
                                                <input type="text" readonly="readonly" class="form-control"
                                                    value="{{ $user->email }}">
                                            </div>
                                        </div>

                                        <div class="col-12 mb-4">
                                            <button id="update-user-submit-btn" type="submit"
                                                class="btn btn-success mt-3 w-100">ذخیره تغییرات</button>
                                            <button id="update-user-loading-btn" type="button"
                                                class="btn btn-light mt-3 w-100">در حال ثبت...</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                @endswitch

                <div class="modal fade text-right" id="changeUserNameModal" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label>نام کاربری جدید را بنویسید(حروف انگلیسی)</label>
                                            <input required type="text" class="form-control" name="new_username"
                                                id="new_username" placeholder="نام کاربری جدید را اینجا بنویسید">
                                            <span id="new_username_msg"></span>
                                        </div>
                                        <div class="form-group">
                                            <label>توضیحات (اختیاری)</label>
                                            <textarea id="new_username_body" name="body" class="form-control" rows="5"
                                                placeholder="در صورت نیاز توضیحات خود را بنویسید"></textarea>
                                        </div>
                                        <button id="chusername-submit" type="button"
                                            onclick="handleChangeUsernameSubmit()"
                                            class="btn btn-warning mt-3 w-100">درخواست
                                            تغییر</button>
                                        <button id="chusername-loading" type="button"
                                            class="btn btn-light mt-3 w-100">در حال
                                            ثبت...</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection

@section('script')
    <script>
        const user_img_input = document.getElementById('user-image-input');
        const user_img_preview = document.getElementById('user-image');
        const dash_edit_csrf = "{{ csrf_token() }}";
        const upload_user_img_route = "{{ route('upload.user.image') }}";
        const req_change_username_route = "{{ route('user.req.change.username') }}";
        const user_id = "{{ $user->id }}";
        // const first_post = "{{ count($user->blogs) > 0 ? $user->blogs->first()->id : null }}";
        const b_min = "{{ $ftp_path . 'files/other/images/b-minim.webp' }}";
        const b_add = "{{ $ftp_path . 'files/other/images/b-add-24.webp' }}";

        const loading_gif = "{{ $ftp_path . 'files/other/images/loading.gif' }}";
    </script>

    @if ($tab == 'job')
        <script>
            const user_job_store_route = "{{ route('user.job.store') }}";
            const user_job_destroy_route = "{{ route('user.job.destroy') }}";
            const user_job_remove_img = "{{ $ftp_path . 'files/other/images/remove2.png' }}";
            let user_jobs = @json($ujobs);
        </script>
    @endif

    @if ($user->email_actived != 1)
        <script>
            const active_email_route = "{{ route('actice.email') }}";
            const change_email_route = "{{ route('user.change.email') }}";
            let last_active_code = 91;
        </script>
        @if (isset($user->last_active_code))
            <script>
                last_active_code = {{ now()->diffInSeconds($user->last_active_code) }};
            </script>
        @endif
    @endif

    <script type="text/javascript"
        src="{{ asset('mixassets/js/user/dashboard-edit.min.js') . '?lm=' . filemtime('mixassets/js/user/dashboard-edit.min.js') }}">
    </script>
@endsection
