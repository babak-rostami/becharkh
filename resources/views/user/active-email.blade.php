@extends('index')


@section('title')
    تایید ایمیل
@endsection

@section('style')
    <meta name="robots" content="noindex">
@endsection

@section('content')

    <div class="row justify-content-center">
        <div class="col-10">
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
        </div>
    </div>


    <div class="row justify-content-center text-white">
        <div class="col-11 col-md-6 shadow ads-box bg-wht radius-10 shadow my-5 text-center p-5" style="background-color: #005cbf">


            <form class="mb-1" action="{{route('check.active.code')}}" method="post" role="form">
                @csrf
                <h2 class="bold-font-title">تایید ایمیل</h2>

                <div class="row justify-content-center">
                    <div class="col-8 text-center">
                        <div class="form-group">
                            <p>کد تایید ارسال شده به ایمیل را وارد کنید</p>
                            <p>{{auth('user')->user()->email}}</p>
                            <div class="col-12 col-md-10 mx-auto">
                                <input type="text" class="form-control text-center" name="code" id="code">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-lg btn-primary px-5">ثبت</button>
            </form>
            <div class="mt-4 show-timer-div p-2" style="display: none">
                در صورت ارسال نشدن کد <span class="show-timer-section"></span> ثانیه دیگر تلاش کنید
            </div>
            <a class="btn btn-lg btn-dark px-5 active-again-btn mt-2">ارسال مجدد کد</a>

        </div>
    </div>

@endsection


@section('script')

    <script>
        var again_time = 30;
        var active = 0;

        setInterval(MyMessage, 1000);

        $(".active-again-btn").click(function () {
            $.ajax({
                method: 'get',
                url: '{{route('actice.email.again')}}',
            })
            again_time = 30;
            active = 1;
            $(".active-again-btn").hide();
            $(".show-timer-section").show();
            $(".show-timer-div").show();
        });

        function MyMessage() {

            if (active == 1) {
                again_time = again_time - 1;
                if (again_time == 0) {
                    active = 0;
                    $(".active-again-btn").show();
                    $(".show-timer-section").hide();
                    $(".show-timer-div").hide();

                } else {
                    $(".show-timer-section").text(again_time);
                }
            }

        }


    </script>

@endsection


