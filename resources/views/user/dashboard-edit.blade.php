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

            <div class="col-12 col-md-10 text-right">

                <div class="text-center">
                    <div class="user-image-div">
                        <img class="user-image" id="user-image" src="{{ asset($user->image()) }}"
                            alt="{{ $user->username }}" title="{{ $user->username }}">
                        <input type="file" id="user-image-input" accept="image/*">
                    </div>
                    <a class="cur-p" id="uimg-select-btn" onclick="openUserImgInput(this)">
                        📸 انتخاب عکس پروفایل
                    </a>
                </div>

                <div class="row">
                    <div class="col-12 text-center">
                        <div class="form-group">
                            <span type="text" class="d-block mt-4">{{ $user->email }}</span>
                            @if (!$user->email_actived)
                                <span id="active-email-msg">
                                    <img class="lazy-load rcir-glow"
                                        data-src="{{ $ftp_path . 'files/other/images/red-circle.png' }}">
                                    ایمیل شما تایید نشده است!</span>
                                @if (isset($user->active_code))
                                    <button class="btn btn-outline-primary" id="active-email-btn"
                                        onclick="activeEmail()">ارسال مجدد
                                        لینک فعالسازی</button>
                                @else
                                    <button class="btn btn-outline-primary" id="active-email-btn"
                                        onclick="activeEmail()">ارسال
                                        لینک تایید ایمیل</button>
                                @endif
                                <a class="btn btn-outline-dark" href="" data-toggle="modal"
                                    data-target="#change_email">تغییر
                                    ایمیل</a>

                                <div class="modal fade" id="change_email" tabindex="-1" role="dialog"
                                    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
                                                <button onclick="changeEmail()" class="btn btn-primary w-100 mt-2"
                                                    id="change-email-btn">تغییر
                                                    ایمیل</button>
                                                <span id="change-email-msg"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($user_likes > 0)
                        <div class="col-12 text-center mb-3">
                            <span class="likes-count">{{ $user_likes }} ❤️</span>
                            <span class="likes-label">از طرف کاربران به نظرات شما</span>
                            <hr>
                        </div>
                    @endif

                    <div class="col-12">
                        <div class="ueform-box">
                            <div class="uuform-info">
                                <span>نام کاربری</span>
                                <span>{{ $user->username }}</span>
                            </div>
                            <div class="uuform-edit">
                                <a href="" data-toggle="modal" data-target="#changeUserNameModal">ویرایش</a>
                            </div>
                        </div>
                        <div class="modal fade text-right" id="changeUserNameModal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <h4 class="text-center mb-4">تغییر نام کاربری</h4>
                                        <div class="form-group">
                                            <label>نام کاربری جدید را بنویسید</label>
                                            <input required type="text" class="form-control" name="new_username"
                                                id="new_username" placeholder="نام کاربری جدید را اینجا بنویسید">
                                            <span id="new_username_msg"></span>
                                        </div>
                                        <ul class="mt-5">
                                            <li>فقط از حروف انگلیسی و اعداد استفاده کنید</li>
                                            <li>نام کاربری نباید با عدد شروع شود</li>
                                            <li>حداکثر طول نام کاربری 25 کاراکتر است</li>
                                        </ul>
                                        <button id="chusername-submit" type="button" onclick="handleChangeUsernameSubmit()"
                                            class="btn btn-primary mt-3 w-100">درخواست
                                            تغییر</button>
                                        <button id="chusername-loading" type="button" class="btn btn-light mt-3 w-100">در
                                            حال
                                            ثبت...</button>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="ueform-box">
                            <div class="uuform-info">
                                <span id="uuform-info-name">نام</span>
                                <span>{{ $user->name ?? 'نام ثبت نشده' }}</span>
                            </div>
                            <div class="uuform-edit">
                                <button type="button" onclick="editAccount('name')">ویرایش</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="ueform-box">
                            <div class="uuform-info">
                                <span id="uuform-info-phone">شماره تلفن</span>
                                <span>{{ $user->phone ?? 'شماره تلفن ثبت نشده' }}</span>
                            </div>
                            <div class="uuform-edit">
                                <button type="button" onclick="editAccount('phone')">ویرایش</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="ueform-box">
                            <div class="uuform-info">
                                <span id="uuform-info-body">بیوگرافی</span>
                                <span style="white-space: pre-line">{{ $user->body ?? 'درباره‌‌ی خود بنویسید...' }}</span>
                            </div>
                            <div class="uuform-edit">
                                <button type="button" onclick="editAccount('body')">ویرایش</button>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade text-right" id="account_edit" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <h5 id="account_edit_title" class="modal-title mb-4"></h5>
                                    <div id="account_edit_field"></div>
                                    <div id="account_edit_error" class="text-danger mt-2" style="display:none;"></div>
                                    <div class="mt-3">
                                        <button type="button" id="user-update-btn" class="btn btn-primary"
                                            onclick="userUpdate()">ثبت
                                            تغییرات</button>
                                        <button type="button" class="btn btn-dark" data-dismiss="modal">انصراف</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-12 text-center">
                        <a class="btn btn-danger my-5" href="" data-toggle="modal"
                            data-target="#logout_account">خروج
                            از
                            حساب کاربری</a>
                    </div>
                </div>

                <div class="modal fade text-right" id="logout_account" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <p>میخواهید از حساب کاربری خود خارج شوید؟</p>
                                <button type="button" class="btn btn-dark" data-dismiss="modal">انصراف</button>
                                <a class="btn btn-danger" href="{{ route('user.logout') }}">بله</a>
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
        const b_add = "{{ $ftp_path . 'files/other/images/b-add-24.webp' }}";

        const loading_gif = "{{ $ftp_path . 'files/other/images/loading.gif' }}";
    </script>

    @if ($user->email_actived != 1)
        <script>
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
