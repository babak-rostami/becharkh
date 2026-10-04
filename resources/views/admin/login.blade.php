<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from demo.imanpa.ir/cork/go/rtl/demo1/auth_login_boxed.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 18 Jul 2020 11:07:12 GMT -->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>Login Boxed | CORK الگوی مدیریتی تمام ریسپانسیو </title>
    <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico" />
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="{{asset('admin_c/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('admin_c/assets/css/plugins.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('admin_c/assets/css/authentication/form-2.css')}}" rel="stylesheet" type="text/css" />
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

                        <h1 class="">ورودی</h1>
                        <p class="">برای ادامه به حساب کاربری خود وارد شوید</p>


                        <form class="pt-3" action="{{route('admin.login.send')}}" method="post">

                            @csrf

                            <div class="form-group">
                                <label for="exampleInputEmail">ایمیل</label>
                                <div class="input-group">
                                    <input name="email" type="email" class="form-control form-control-lg border-right-0"
                                        id="exampleInputEmail" placeholder="ایمیل خود را وارد کنید">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword">رمز عبور</label>
                                <div class="input-group">
                                    <input name="password" type="password"
                                        class="form-control form-control-lg border-right-0" id="exampleInputPassword"
                                        placeholder="رمز عبور خود را وارد کنید">
                                </div>
                            </div>
                            <div class="my-2 d-flex justify-content-between align-items-center">

                                {{-- <a href="#" class="auth-link text-black">رمز عبور خود را فراموش کرده اید ؟</a>--}}
                            </div>
                            <div class="my-3">
                                <button type="submit"
                                    class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">ورود
                                </button>
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


</html>