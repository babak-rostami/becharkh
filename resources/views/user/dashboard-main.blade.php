@extends('index')

@section('title')
    داشبورد
@endsection

@section('style')
    <link
        href="{{ asset('mixassets/css/user/dashboard-main.min.css') . '?lm=' . filemtime('mixassets/css/user/dashboard-main.min.css') }}"
        rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row justify-content-center py-2">

        <div class="col-12 radius-10">
            @if (session('success'))
                <p class="alert alert-success text-center">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <p class="alert alert-danger text-center">{{ $error }}</p>
                @endforeach
            @endif
        </div>

        <div class="col-12 col-md-10 bg-wht text-right py-2 my-2 radius-10">
            <a class="text-decoration-none text-dark" href="{{ route('home') }}"><img class="ml-1"
                    src="{{ $ftp_path . 'files/other/images/back.png' }}" alt="back">خانه</a>
            <span class="float-left">داشبورد</span>
        </div>

        <div class="col-12 col-md-10 text-right bg-wht radius-10">
            <div class="row align-items-center p-3">
                <div class="col-auto pl-0">
                    <img src="{{ asset($user->image()) }}" alt="user image" class="user-image">
                </div>

                <div class="col overflow-hidden">
                    <h5 class="mb-1">{{ $user->username }}</h5>
                    <p class="text-muted mb-0">{{ $user->email }}</p>
                </div>

                <a id="dash-edit-btn" href="{{ route('user.dashboard.edit', 'edit') }}">
                    ویرایش
                    <img src="{{ $ftp_path . 'files/other/images/write-16.png' }}" alt="write icon">
                </a>
            </div>
        </div>


        <div class="col-12 col-md-10 text-right radius-10" id="active-email-div">
            <a class="decor-none" href="{{ route('user.dashboard.edit', 'edit') }}">
                <span id="active-email-title">ایمیل شما تایید نشده است!</span>
                <span id="active-email-desc">برای تایید ایمیل کلیک کنید.</span>
            </a>
        </div>

        {{-- <div class="col-12 col-md-10 text-right bg-wht mb-3 py-4 radius-10">
            <span id="user-money-number">{{ number_format($user->money) }}</span>
            <span id="user-money-format">تومان</span>
            <a id="user-money-charge-btn" href="" data-toggle="modal" data-target="#charge-account">شارژ
                حساب</a>
        </div> --}}

        <div class="col-12 col-md-10 text-right bg-wht mb-3 py-4 radius-10">
            <div class="row align-items-center">
                <div class="col-auto">
                    <span id="user-like-number">{{ $user_likes }}</span>
                    <span id="user-like-title">لایک</span>
                    <br>
                    <span>از طرف کاربران به نظرات شما</span>
                </div>
                <div class="col text-left">
                    <img src="{{ $ftp_path . 'files/other/images/like-black-36.png' }}" alt="like icon">
                </div>
            </div>
        </div>

        {{-- <div class="col-6 col-md-5 text-center mb-2 p-1">
            <a class="dash-option-btn" href="{{ route('user.dashboard.edit', 'ads') }}">
                <img src="{{ $ftp_path . 'files/other/images/shop-gray.png' }}" alt="show icon">
                <span class="dash-option-btn-title">آگهی های من</span>
            </a>
        </div> --}}
        <div class="col-6 col-md-5 text-center mb-2 p-1">
            <a class="dash-option-btn" href="{{ route('user.dashboard', $user->username) }}">
                <img src="{{ $ftp_path . 'files/other/images/profile-gray-20.png' }}" alt="show icon">
                <span class="dash-option-btn-title">پروفایل</span>
            </a>
        </div>
        <div class="col-6 col-md-5 text-center mb-2 p-1">
            <a class="dash-option-btn" href="{{ route('user.notifications') }}">
                <img src="{{ $ftp_path . 'files/other/images/notif-gray.png' }}" alt="show icon">
                <span class="dash-option-btn-title">پیام ها</span>
            </a>
        </div>
        <div class="col-6 col-md-5 text-center mb-2 p-1">
            <a class="dash-option-btn" href="" data-toggle="modal" data-target="#logout_account">
                <img src="{{ $ftp_path . 'files/other/images/remove2.png' }}" alt="show icon">
                <span class="dash-option-btn-title">خروج از حساب</span>
            </a>
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
@endsection

@section('script')
    <script>
        const user_img_input = document.getElementById('user-image-input');
        const user_img_preview = document.getElementById('user-image');
        const dash_edit_csrf = "{{ csrf_token() }}";
        const b_add = "{{ $ftp_path . 'files/other/images/b-add-24.webp' }}";

        const loading_gif = "{{ $ftp_path . 'files/other/images/loading.gif' }}";
    </script>

    <script type="text/javascript"
        src="{{ asset('mixassets/js/user/dashboard-main.min.js') . '?lm=' . filemtime('mixassets/js/user/dashboard-main.min.js') }}">
    </script>
@endsection
