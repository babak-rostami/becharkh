<!doctype html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- <link href="{{ asset('admin_c/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" /> --}}
    <link rel="stylesheet" href="{{ $ftp_path . 'library/bootstrap/bootstrap.min.css' }}">
    <link rel="stylesheet" href="{{ $ftp_path . 'library/bootstrap/font-awesome.min.css' }}">
    <link rel="stylesheet" href="{{ $ftp_path . 'library/family.css' }}">

    <script src="{{ $ftp_path . 'library/axios.min.js' }}"></script>
    <script src="{{ $ftp_path . 'library/jquery-3.1.1.min.js' }}"></script>

    <link href="{{ asset('assets/style.css') . '?lm=' . filemtime('assets/style.css') }}" rel="stylesheet"
        type="text/css" />

    <title>@yield('title')</title>
    <link rel="icon" type="image/x-icon" href="{{ $ftp_path . 'files/other/images/logo1.png' }}" />
    <link rel="apple-touch-icon" href="{{ $ftp_path . 'files/other/images/logo1.png' }}">

    @yield('style')
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-EN95ELW4G1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'G-EN95ELW4G1');
    </script>
</head>

<body>

    <div class="container-fluid bg-gray overflow-hidden">

        <header>

            <div class="row justify-content-center align-items-center py-1 bg-wht" id="top-menu-box">
                <div class="col-2 text-right">
                    <a class="top-nav-link-logo" href="{{ route('home') }}">بچرخ</a>
                </div>
                <div class="col-4 text-center">
                    <a class="mx-3 top-nav-link" href="{{ route('ads.index') }}">بازار</a>
                    {{-- <a class="mx-3 top-nav-link" href="{{ route('blog.index') }}">مجله</a> --}}
                    <a class="mx-3 top-nav-link" href="{{ route('question.index') }}">انجمن</a>
                    <a class="mx-3 top-nav-link" href="{{ route('question.index') }}?s=1">نظرات
                        کاربران</a>
                </div>

                <div class="col-12 col-md-4 text-center">
                    <span id="search-tmenu" onclick="openSearchModal()">
                        <img src="{{ $ftp_path . 'files/other/images/search-gray.png' }}" alt="search in becharkh">
                        جستجو در
                        <span>بچرخ</span>
                    </span>
                </div>

                <div class="col-2 text-left">
                    <!-- notifs Modal -->
                    <div class="modal fade" id="notifmodal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="row text-left">
                                        <div class="col-12 p-0">
                                            <p class="p-3" style="background-color: #f50d0d;color:white">اعلانات
                                            </p>
                                            <p class="pl-2 text-center">اعلانی وجود ندارد</p>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($user)
                        <a class="dropdown-toggle top-nav-link" href="#" id="navbarDropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            حساب کاربری
                            @if ($user->notif_count)
                                <span id="desk-unotif-count">{{ $user->notif_count }}</span>
                            @endif
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <span class="dropdown-header">
                                {{ $user->username }}</span>
                            <a class="dropdown-item top-nav-link" href="{{ route('user.dashboard.edit') }}">مدیریت
                                حساب</a>
                            <a class="dropdown-item top-nav-link" href="{{ route('user.notifications') }}">پیام ها
                                @if ($user->notif_count)
                                    <span id="desk-unotif-count-li">{{ $user->notif_count }}</span>
                                @endif
                            </a>
                            <a class="dropdown-item top-nav-link" href="{{ route('favorite.index') }}">انجمن های
                                شما</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item top-nav-link" href="{{ route('user.logout') }}">خروج</a>
                        </div>
                    @else
                        <a class="top-nav-link" href="" data-toggle="modal" data-dismiss="modal"
                            data-target="#login_user">
                            <img alt="پروفایل"
                                style="border-radius: 50%; width: 36px ; height: 36px;object-fit:contain"
                                src="{{ $ftp_path . 'files/other/images/profile.jpg' }}">
                        </a>
                    @endif
                </div>
            </div>
        </header>


        <div class="modal fade text-right" id="search_modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document">
                <div class="modal-content radius-10">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <span class="float-left cur-p px-3 pb-2" data-dismiss="modal"
                                    aria-hidden="true">×</span>
                                <input id="main_search_input" class="form-control my-2 w-100" type="text"
                                    placeholder="جستجو کنید...">
                                <img class="lazy-load" id="msearch-magicon" alt="search"
                                    data-src="{{ $ftp_path . 'files/other/images/search-blue.png' }}">

                                <div id="msearch-tabs">
                                    <span onclick="showMainSearchItemsResults(1)"
                                        class="msearch-tab msearch-tab-active" id="msearch-tab-items">صفحه
                                        نظرات</span>
                                    <span class="msearch-tab" id="msearch-tab-questions"
                                        onclick="showMainSearchForumResults(2)">مسائل و گفتگوها</span>
                                </div>
                                <div class="pt-2 pb-5" id="show-msearch-result"></div>
                                <div class="p-4 text-center mt-2" id="show-msearch-loading">
                                    <img class="mt-2 lazy-load" alt="searching"
                                        data-src="{{ $ftp_path . 'files/other/images/loading.gif' }}">
                                    <span>در حال جستجو</span>
                                </div>
                                <div class="p-4 text-center mt-2" id="show-msearch-empty">
                                    <img class="mt-2 lazy-load" alt="search"
                                        data-src="{{ $ftp_path . 'files/other/images/search.webp' }}">
                                    <span>جستجو کنید...</span>
                                </div>
                                <table id="search-hint-table">
                                    <tr>
                                        <th id="whtable">جستجوی غلط</th>
                                        <th id="thtable">جستجوی دقیق</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            خودرو پژو 207
                                        </td>
                                        <td>
                                            207
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            گوشی گلکسی a55 بخریم؟
                                        </td>
                                        <td>
                                            a55
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            مزایا و معایب فیدلیتی مدل 1400
                                        </td>
                                        <td>
                                            فیدلیتی
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($user == null)
            <div class="modal fade" id="login_user" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body">

                            <div class="row" id="checkEmailLoginModal">
                                <div class="col-12 text-center">
                                    <span>سلام</span>
                                    <br>
                                    <span class="font-800">برای ادامه فعالیت ایمیل خود را وارد کنید</span>
                                    <div class="form-group">
                                        <input type="email" class="form-control mb-3" name="email"
                                            id="email_for_login_check" oninput="clearSuggestion()"
                                            placeholder="ایمیل خود را وارد کنید...">
                                        <span id="email_for_login_error">
                                        </span>
                                    </div>

                                    <button id="check-email-loading" type="button" class="w-100 btn btn-light mb-2">
                                        <img data-src="{{ $ftp_path . 'files/other/images/loading.gif' }}"
                                            class="lazy-load" alt="loading">
                                    </button>
                                    <button type="button" onclick="sendLoginRequest(1)"
                                        class="w-100 btn btn-primary mb-2" id="click_for_login_fix_mistake">بله
                                        آدرس را تصحیح کن
                                    </button>
                                    <button type="button" class="w-100 btn btn-dark mb-2"
                                        id="click_for_login_is_true" onclick="sendLoginRequest(0)">خیر
                                        صحیح است
                                    </button>
                                    <button type="button" id="click_for_login" onclick="sendLoginRequest()"
                                        class="w-100 btn btn-danger mb-2">ادامه
                                        <img src="{{ $ftp_path . 'files/other/images/next-light.png' }}"
                                            alt="next">
                                    </button>
                                    <span>بچرخ - انجمنی برای تبادل دانش</span>
                                </div>
                            </div>
                            <div class="row" id="loginFormLoginModal">
                                <div class="col-12 text-center">
                                    <button class="btn btn-sm btn-light" onclick="showEnterEmailPageForAuth()"
                                        type="button">
                                        <img data-src="{{ $ftp_path . 'files/other/images/back.png' }}"
                                            alt="back" class="lazy-load ch-acc-auth-img">
                                        تغییر حساب
                                    </button>
                                    <br>
                                    <span class="hi-login">سلام</span>
                                    <span class="hi-login" id="show-name-lf"></span>
                                    <br>
                                    <span>برای ورود رمز عبور خود را وارد کنید</span>
                                </div>

                                <div class="col-12 text-right mt-4">
                                    <input type="email" class="d-none" name="email" id="email_for_login">

                                    <div class="login-fg">
                                        <span class="login-label-fg">رمز عبور</span>
                                        <input class="login-input-fg" type="password" name="password"
                                            id="password_for_login" autocomplete="off"
                                            placeholder="رمز عبور خود را وارد کنید">
                                        <img onclick="changeTypePasswordLogin()"
                                            data-src="{{ $ftp_path . 'files/other/images/eye.png' }}"
                                            class="lazy-load change-pass-eye-img" alt="eye">
                                    </div>

                                    <div class="col-12 text-center">
                                        <div class="text-right" id="login-error-box">
                                            <img data-src="{{ $ftp_path . 'files/other/images/circle.webp' }}"
                                                alt="circle" class="lazy-load">
                                            <span class="text-danger" id="login-error-message"></span>
                                        </div>
                                        <span id="login-suc-message"></span>
                                        <button id="login-submit-loading" type="button"
                                            class="w-100 btn btn-light mb-4">
                                            <img data-src="{{ $ftp_path . 'files/other/images/loading.gif' }}"
                                                class="lazy-load" alt="loading">
                                        </button>
                                        <button id="login-submit-btn" onclick="loginUser()" type="button"
                                            class="w-100 btn btn-danger mb-4">ورود به بچرخ
                                            <img data-src="{{ $ftp_path . 'files/other/images/next-light.png' }}"
                                                alt="next" class="lazy-load">
                                        </button>
                                        <br>
                                        <a href="" data-toggle="modal" data-dismiss="modal"
                                            data-target="#forget_password" onclick="setEmailForForgetPass()">رمز
                                            عبور خود را فراموش
                                            کرده ام</a>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="registerFormLoginModal">
                                <div class="col-12 text-center">
                                    <span id="register-form-title">ثبت نام</span>
                                    <button type="button" class="close float-left" data-dismiss="modal"
                                        aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <br>
                                    <span style="font-size: 14px">خوش آمدید ،فرم عضویت را تکمیل کنید</span>
                                    <hr>
                                </div>

                                <div class="col-12 text-center">
                                    <span id="email-register-box" onclick="showEnterEmailPageForAuth()">
                                        <img data-src="{{ $ftp_path . 'files/other/images/back.png' }}"
                                            class="lazy-load ch-acc-auth-img" alt="back">
                                        <span id="email-register-span"></span>
                                    </span>
                                </div>

                                <div class="col-12 text-right">

                                    <div>
                                        <span>نام</span>
                                        <input oninput="limitMaxChar(this,25)" class="form-control" type="text"
                                            name="name" id="name_for_register" placeholder="نام شما ...">
                                    </div>

                                    <div class="mt-2">
                                        <span>نام کاربری</span>
                                        <span id="username-for-register-war">(غیر قابل تغییر)</span>
                                        <span id="username-for-register-desc">نام انگلیسی شما که به کاربران نمایش داده
                                            میشود</span>
                                        <input oninput="limitMaxChar(this,25)" type="text" class="form-control"
                                            name="username" id="username_for_register"
                                            placeholder="حروف انگلیسی و اعداد...">
                                    </div>

                                    <input type="email" class="d-none" name="email" id="email_for_register">

                                    <div class="mt-2 position-relative">
                                        <span>رمز عبور</span>
                                        <input type="text" class="form-control" name="password"
                                            autocomplete="off" id="password_for_register"
                                            placeholder="رمز عبور خود را به یاد بسپارید">
                                        <img onclick="changeTypePasswordRegister()"
                                            data-src="{{ $ftp_path . 'files/other/images/eye.png' }}"
                                            class="lazy-load" id="change-pass-eye-img-register" alt="eye">
                                    </div>

                                    <div class="mt-4 text-center">
                                        <div class="text-right" id="register-error-box">
                                            <img data-src="{{ $ftp_path . 'files/other/images/circle.webp' }}"
                                                class="lazy-load" alt="circle">
                                            <span class="text-danger" id="register-error-message"></span>
                                        </div>
                                        <span id="register-suc-message"></span>
                                        <button id="register-submit-loading" type="button"
                                            class="btn btn-light w-100">
                                            <img data-src="{{ $ftp_path . 'files/other/images/loading.gif' }}"
                                                class="lazy-load" alt="loading">
                                        </button>
                                        <button id="register-submit-btn" onclick="registerUser()"
                                            class="btn btn-lg btn-danger w-100" type="button">ثبت نام</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="forget_password" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <span class="modal-title" id="exampleModalLabel">تغییر رمز عبور</span>
                            <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-right">
                            <div class="form-group">
                                <span>ایمیل اکانت خود را وارد کنید</span>
                                <input required type="email" class="form-control" name="email"
                                    id="email_forget_pass" placeholder="ایمیلی که با آن ثبت نام کرده اید وارد کنید">
                                <span id="error_for_email_forget"></span>
                            </div>
                            <div id="reset_password_suggestion">
                                <button type="button" onclick="resetPassword(1)" class="w-100 btn btn-primary mb-2"
                                    id="click_for_reset_password_fix_mistake">بله
                                    آدرس را تصحیح کن
                                </button>
                                <button type="button" class="w-100 btn btn-dark mb-2"
                                    id="click_for_reset_password_is_true" onclick="resetPassword(0)">خیر
                                    صحیح است
                                </button>
                            </div>
                            <div id="reset_password_btn">
                                <button id="click_for_reset_password" onclick="resetPassword()" type="button"
                                    class="btn btn-primary w-50">تایید</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">انصراف</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="modal fade" id="charge-account" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body p-0">
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <span id="chac-header">افزایش اعتبار</span>
                                </div>
                                <div class="col-10 text-right p-2">
                                    <form onsubmit="return submitChacForm()"
                                        action="{{ route('user.charge.account') }}" method="POST">
                                        @csrf
                                        <label>مبلغ</label>
                                        <span id="chac-tom-span">به تومان</span>
                                        <input name="ammount" class="form-control mb-4"
                                            oninput="convertToMoneyFormat(this)" placeholder="مبلغ به تومان"
                                            type="text" id="chacinp">
                                        <button class="btn btn-primary w-50" type="submit">پرداخت</button>
                                        <button class="btn btn-danger" data-dismiss="modal"
                                            type="button">انصراف</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif


        <div class="row">
            <div class="col-12 text-center" id="motto-box">
                <span id="motto1">بچرخ</span>
                <span id="motto2"></span>
            </div>
        </div>

        @yield('content')

        @include('mainPart.bmenu-mobile')

        @include('footer')

        <div style="height: 66px" class="d-sm-none"></div>

    </div>

    <script src="{{ $ftp_path . 'library/bootstrap/popper.min.js' }}"></script>
    <script src="{{ $ftp_path . 'library/bootstrap/bootstrap.min.js' }}"></script>

    @if ($user)
        <script>
            const is_user_login = 1;
        </script>
    @else
        <script>
            const is_user_login = 0;
        </script>
    @endif

    <script>
        const forget_password_route = "{{ route('user.forget.password') }}";
        const route_is_email_exist = "{{ route('check.if.email.exist') }}";
        let csrf_token = "{{ csrf_token() }}";

        let user_email_for_auth = null;
        const route_login_user_ajax = "{{ route('user.login.send.ajax') }}";
        const route_register_user_ajax = "{{ route('user.register.send.ajax') }}";

        const open_new_img = "{{ $ftp_path . 'files/other/images/b-add-32.png' }}";
        const close_new_img = "{{ $ftp_path . 'files/other/images/x-32.webp' }}";
        const bm_more_img = "{{ $ftp_path . 'files/other/images/more-menu.webp' }}";
        const bm_more_b_img = "{{ $ftp_path . 'files/other/images/more-menu-b.webp' }}";
        const x_16 = "{{ $ftp_path . 'files/other/images/x-16.webp' }}";
    </script>

    <script type="text/javascript" src="{{ asset('assets/js/main.js') . '?lm=' . filemtime('assets/js/main.js') }}">
    </script>

    @yield('script')

</body>

</html>
