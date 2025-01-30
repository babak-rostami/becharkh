@extends('index')

@section('title')
    بازیابی رمز عبور
@endsection

@section('style')
    <meta name="robots" content="noindex">

    <style>
        #show-pass-img,
        #show-apass-img {
            position: absolute;
            top: 37px;
            left: 12px;
            background-color: #dfdfdf;
            border-radius: 50%;
            padding: 6px;
        }
    </style>
@endsection

@section('content')


    <div class="row justify-content-center">
        <div class="col-12">
            @if (session('success'))
                <div>
                    <p class="alert alert-success text-center">{{ session('success') }}</p>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <p style="color: #000000">{{ $error }}</p>
                    @endforeach
                </div>
            @endif
        </div>
    </div>


    <div class="row mt-2 bg-gray justify-content-center">
        <div class="col-12 col-md-8 text-right bg-wht mt-3 mb-5 radius-10 pt-3 pb-5 px-3">

            <h1 class="text-center">بازیابی رمز عبور</h1>

            <form action="{{ route('reset.password.post') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label for="email_address">ایمیل خود را وارد کنید</label>
                    <input autocomplete="off" type="text" id="email_address" class="form-control" name="email" required
                        autofocus>
                    @if ($errors->has('email'))
                        <span class="text-danger">{{ $errors->first('email') }}</span>
                    @endif
                </div>

                <div class="form-group position-relative">
                    <label for="password">رمز عبور جدید را وارد کنید</label>
                    <input autocomplete="off" oninput="checkPasAndConf()" type="password" id="password"
                        class="form-control" name="password" required autofocus>
                    <img src="{{ $ftp_path . 'files/other/images/eye.png' }}" id="show-pass-img" onclick="showPassword()">
                    @if ($errors->has('password'))
                        <span class="text-danger">{{ $errors->first('password') }}</span>
                    @endif
                </div>

                <div class="form-group position-relative">
                    <label for="password-confirm">برای جلوگیری از اشتباه رمز عبور جدید را تکرار کنید</label>
                    <input autocomplete="off" oninput="checkPasAndConf()" type="password" id="password-confirm"
                        class="form-control" name="password_confirmation" required autofocus>
                    <img src="{{ $ftp_path . 'files/other/images/eye.png' }}" id="show-apass-img" onclick="showAPassword()">
                    @if ($errors->has('password_confirmation'))
                        <span class="text-danger">{{ $errors->first('password_confirmation') }}</span>
                    @endif
                </div>

                <p class="alert alert-danger" id="error" style="display: none">رمز عبور وارد شده با تکرار آن یکی نمی
                    باشد</p>
                <button onclick="changePassword()" id="send-btn" disabled type="submit" class="btn btn-primary w-100">
                    تغییر رمز عبور
                </button>

            </form>
        </div>
    </div>


@endsection

@section('script')
    <script>
        function changePassword() {
            let submit_btn = $("#send-btn");
            if (submit_btn.prop('disabled') == false) {
                setTimeout(() => {
                    submit_btn.prop('disabled', true);
                    submit_btn.text('در حال ثبت تغییرات...')
                }, 100);
            }
        }

        function checkPasAndConf() {
            var password = $("#password").val();
            var conf_password = $("#password-confirm").val();
            if (conf_password != '' && password != '') {
                if (password != conf_password) {
                    $("#error").css('display', 'block');
                    $("#send-btn").prop('disabled', true);
                } else {
                    $("#error").css('display', 'none');
                    $("#send-btn").prop('disabled', false);
                }
            }
        }

        function showAPassword() {
            if ($('#password-confirm').attr('type') === 'password') {
                $('#password-confirm').attr('type', 'text');
            } else {
                $('#password-confirm').attr('type', 'password');
            }
        }

        function showPassword() {
            if ($('#password').attr('type') === 'password') {
                $('#password').attr('type', 'text');
            } else {
                $('#password').attr('type', 'password');
            }
        }
    </script>
@endsection
