@extends('index')


@section('title')
    ورود
@endsection


@section('style')
    <meta name="robots" content="noindex">
@endsection


@section('content')

    <div class="row justify-content-center mt-4 mb-5">
        <div class="col-10 col-md-5 mt-2 shadow-lg p-4">
            @if(session('success'))
                <div>
                    <p class="alert alert-success text-center">{{session('success')}}</p>
                </div>
            @endif

            <form action="{{route('user.login.send')}}" method="post" role="form">
                @csrf

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

                <div class="col-12 text-center mb-5">
                    <button type="submit" class="btn btn-primary mb-2 disable-after-click">ورود</button>
                    <br>
                    <a href="" data-toggle="modal" data-target="#exampleModal">رمز عبور خود را فراموش کرده ام</a>
                    <br>
                    <a href="{{route('user.register')}}" class="btn btn-warning mt-2">اکانت ندارید؟ ثبت نام کنید</a>
                </div>
            </form>
            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">بازیابی پسورد</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{route('user.forget.password')}}" method="post" role="form">
                                @csrf
                                <div class="form-group">
                                    <input type="email" class="form-control" name="email"
                                           placeholder="ایمیل خود را وارد کنید">
                                </div>

                                <button type="submit" class="btn btn-primary disable-after-click">ثبت</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">انصراف</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection


@section('script')
    <script type="text/javascript" src="{{asset('assets/js/main.js')}}"></script>
@endsection
