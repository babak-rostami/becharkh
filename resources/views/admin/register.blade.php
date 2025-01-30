<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from demo.imanpa.ir/cork/go/rtl/demo1/auth_register_boxed.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 18 Jul 2020 11:07:13 GMT -->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>Register Boxed | CORK الگوی مدیریتی تمام ریسپانسیو </title>
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico"/>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="{{asset('admin_c/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('admin_c/assets/css/plugins.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('admin_c/assets/css/authentication/form-2.css')}}" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <link rel="stylesheet" type="text/css" href="{{asset('admin_c/assets/css/forms/theme-checkbox-radio.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin_c/assets/css/forms/switches.css')}}">
</head>
<body class="form">


<div class="form-container outer">
    <div class="form-form">
        <div class="form-form-wrap">
            <div class="form-container">
                <div class="form-content">

                    @if(session('success'))
                        <div>
                            <p class="alert alert-success text-center">{{session('success')}}</p>
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                <p style="color: #000000">{{$error}}</p>
                            @endforeach
                        </div>
                    @endif

                    <h1 class="">ثبت نام</h1>
                    <p class="signup-link register">حساب دارید؟ <a href="auth_login_boxed.html">وارد شوید</a></p>
                    <form class="pt-3" action="{{route('admin.register.send')}}" method="post">
                        @csrf

                        <div class="form-group">
                            <label>نام و نام خانوادگی</label>
                            <div class="input-group">

                                <input type="text" class="form-control form-control-lg border-right-0"
                                       placeholder="نام و نام خانوادگی" name="name">
                            </div>
                        </div>


                        <div class="form-group">
                            <label>نام کاربری</label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-lg border-right-0"
                                       placeholder="نام کاربری" name="username">
                            </div>
                        </div>


                        <div class="form-group">
                            <label>ایمیل</label>
                            <div class="input-group">
                                <input type="email" class="form-control form-control-lg border-right-0"
                                       placeholder="ایمیل" name="email">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>رمز عبور</label>
                            <div class="input-group">
                                <input type="password" class="form-control form-control-lg border-right-0"
                                       id="exampleInputPassword" name="password"
                                       placeholder="رمز عبور">
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit"
                                    class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">ثبت
                                نام
                            </button>
                        </div>
                        <div class="text-center mt-4 font-weight-light">
                            قبلا حساب کاربری داشته اید؟ <a href="{{route('admin.login')}}" class="text-primary">ورود</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>


<!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
<script src="{{asset('admin_c/assets/js/libs/jquery-3.1.1.min.js')}}"></script>
<script src="{{asset('admin_c/bootstrap/js/popper.min.js')}}"></script>
<script src="{{asset('admin_c/bootstrap/js/bootstrap.min.js')}}"></script>

<!-- END GLOBAL MANDATORY SCRIPTS -->
<script src="{{asset('admin_c/assets/js/authentication/form-2.js')}}"></script>

</body>

<!-- Mirrored from demo.imanpa.ir/cork/go/rtl/demo1/auth_register_boxed.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 18 Jul 2020 11:07:13 GMT -->
</html>
