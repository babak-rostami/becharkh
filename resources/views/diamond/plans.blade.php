@extends('index')

@section('title')
    پکیج های آگهی
@endsection

@section('style')
    <meta name="robots" content="noindex">
    <style>
        .plan-box:hover {
            border: 2px solid #1543CD;
        }

        .plan-box {
            background: rgb(245, 248, 253);
            box-shadow: 0 6.67587px 25.869px -1.66897px rgba(73, 141, 255, .3);
        }

        .original-price {
            text-decoration: line-through;
            color: gray;
            font-size: 20px;
        }

        .discounted-price {
            font-size: 20px;
            font-weight: bold;
            color: #0000a7;
        }
    </style>
@endsection

@section('content')

    <div class="row justify-content-center bg-wht">
        <div class="col-12">
            @if (session('success'))
                <div>
                    <p class="alert alert-success text-center mb-0">{{ session('success') }}</p>
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

    <div class="row justify-content-center" style="background: rgb(237, 240, 249)">

        <div class="col-12 text-center mt-4 mb-3">
            <h1 class="bold-font-title" style="color: #005cbf">بسته مورد نظر را انتخاب کنید</h1>
        </div>

        @if ($user->free_pack == 0)
            <div class="col-10 col-sm-6 col-md-4 col-lg-3 mb-5">
                <div class="row p-2 mx-1 bg-gray plan-box radius-10"
                    style="border-top: 3px solid #808099; cursor: pointer;">
                    <div class="col-12 text-center" style="color: #00c854">
                        <b class="bold-font-title"> هدیه </b>
                    </div>
                    <div class="col-12 text-center my-4">
                        <p>به بچرخ خوش اومدین , با بسته هدیه خوش آمد گویی لذت فروش آنلاین را تجربه کنید </p>
                        <hr>
                        <span style="font-weight: 800">
                            <img src="{{ asset('files/other/images/diamond.png') }}">
                            <br>
                            30 الماس
                        </span>
                        <hr>
                        <span class="discounted-price"
                            style="font-size: 28px;
                    font-weight: bold;
                    color: #0000a7;">رایگان</span>
                        <br>
                        <a class="btn btn-lg btn-buy-pack mt-3" href="{{ route('free.user.diamond.package') }}">فعال
                            سازی</a>
                    </div>
                </div>
            </div>
        @endif
        @foreach ($plans as $plan)
            <div class="col-10 col-sm-6 col-md-4 col-lg-3 mb-5">
                <div class="row p-2 mx-1 bg-gray plan-box radius-10"
                    style="border-top: 3px solid #808099; cursor: pointer;">
                    <div class="col-12 text-center" style="color: #00c854">
                        <b class="bold-font-title"> {{ $plan->name }} </b>
                    </div>
                    <div class="col-12 text-center my-4">
                        <p>{{ $plan->body }}</p>
                        <hr>
                        <span style="font-weight: 800">
                            <img src="{{ asset('files/other/images/diamond.png') }}">
                            <br>
                            {{ $plan->diamond_count }} الماس
                        </span>
                        <hr>
                        <span class="original-price">{{ $plan->org_price }}</span>
                        <span class="discounted-price">{{ $plan->showPrice() }}</span>
                        <br>
                        <a class="btn btn-lg btn-buy-pack mt-3" href="{{ route('buy.diamond.plan', $plan->id) }}">فعال
                            سازی</a>
                    </div>
                </div>
            </div>
        @endforeach

    </div>

@endsection

@section('script')
    <script>
        function showTime() {
            var h = $('#plan-hour-time').text();
            var m = $('#plan-min-time').text();
            var s = $('#plan-sec-time').text();
            var d = $('#plan-day-time').text();

            s = s - 1;
            if (s < 0) {
                s = 60;
                m -= 1;
                if (m < 0) {
                    m = 59;
                    h -= 1;
                    if (h < 0) {
                        h = 23;
                        d -= 1;
                        if (d < 0) {
                            h = 0;
                            m = 0;
                            s = 0;
                            d = 0;
                        }
                    }
                }
            }
            $('#plan-day-time').text(d)
            $('#plan-hour-time').text(h)
            $('#plan-min-time').text(m)
            $('#plan-sec-time').text(s)

            setTimeout(showTime, 1000);

        }

        showTime();
    </script>
@endsection
