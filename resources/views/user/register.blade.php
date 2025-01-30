@extends('index')


@section('title')
    ثبت نام
@endsection


@section('style')
    <meta name="robots" content="noindex">
@endsection


@section('content')

    <div class="row justify-content-center mt-4 mb-5">
        <div class="col-10 col-md-5 mt-2 shadow-lg p-4">

            <form action="{{route('user.register.send')}}" method="post" role="form">
                @csrf

                <div class="form-group">
                    <label for="name">نام و نام خانوادگی</label>
                    <input type="text" class="form-control" name="name" id="name">
                    @error('name')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="username">نام کاربری در سایت</label>
                    <input type="text" class="form-control" name="username" id="username">
                    @error('username')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">ایمیل</label>
                    <input type="email" class="form-control" name="email" id="email">
                    @error('email')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>


                <div class="form-group">
                    <label for="password">پسورد</label>
                    <input type="password" class="form-control" name="password" id="password">
                    @error('password')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>


                <p class="my-2" style="font-size: 14px ; color: #808080">
                    توجه: لطفا از طریق هیچ پیامک یا ایمیلی برای پرداخت وجه جهت انتشار آگهی اقدام نکنید.
                </p>

                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary disable-after-click">ثبت نام</button>
                    <br>
                    <a href="{{route('user.login')}}" class="btn btn-warning mt-4">اکانت دارید؟ وارد شوید</a>
                </div>
            </form>

        </div>
    </div>

@endsection


@section('script')
    <script type="text/javascript" src="{{asset('assets/js/main.js')}}"></script>
@endsection
