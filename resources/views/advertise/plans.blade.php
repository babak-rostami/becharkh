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
        <div class="col-10">
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

    <div class="row justify-content-center" style="background: rgb(237, 240, 249)">

        <div class="col-12 text-center mt-4 mb-3">
            <h1 class="bold-font-title" style="color: #005cbf">بسته مورد نظر را انتخاب کنید</h1>
        </div>

        <div class="col-10 col-sm-6 col-md-4 col-lg-3 mb-5">
            <div class="row p-2 mx-1 bg-gray plan-box radius-10" style="border-top: 3px solid #808099; cursor: pointer;">
                <div class="col-12 text-center" style="color: #00c854">
                    <b class="bold-font-title"> آگهی شخصی </b>
                </div>
                <div class="col-12 text-center my-4">
                    <p>پلن رایگان فروش برای شما که میخواهید چند محصول در ماه بفروشید، مناسب است. با پلن رایگان بچرخ
                        میتوانید لذت فروش آنلاین را تجربه کنید </p>
                    <span style="font-weight: 800;"><img class="ml-1" style="width: 20px" src="{{ asset('files/other/images/done.png') }}">3 آگهی
                    </span>
                    <hr>
                    <span class="discounted-price"
                        style="font-size: 28px;
                    font-weight: bold;
                    color: #0000a7;">رایگان</span>
                    <br>
                    @if (isset($user->package) && jdate($user->package->expire_date)->greaterThanCarbon(\Illuminate\Support\Carbon::now()))
                        <p>فعال شده است</p>
                        <p>بعد از زمان مشخص شده دوباره قابل استفاده است</p>
                        <b id="plan-day-time"
                            style="color: #ff3111">{{ json_decode($user->planTime()->getContent(), true)['day'] }}</b>
                        <span>روز</span>
                        <b id="plan-hour-time"
                            style="color: #ff3111">{{ json_decode($user->planTime()->getContent(), true)['hour'] }}</b>
                        <span>ساعت</span>
                        <b id="plan-min-time"
                            style="color: #ff3111">{{ json_decode($user->planTime()->getContent(), true)['min'] }}</b>
                        <span>دقیقه</span>
                        <b id="plan-sec-time"
                            style="color: #ff3111">{{ json_decode($user->planTime()->getContent(), true)['sec'] }}</b>
                        <span>ثانیه</span>
                    @else
                        <a class="btn btn-lg btn-buy-pack mt-3" href="{{ route('free.user.advertise.package') }}">فعال سازی</a>
                    @endif
                </div>
            </div>
        </div>
        @foreach ($plans as $plan)
            <div class="col-10 col-sm-6 col-md-4 col-lg-3 mb-5">
                <div class="row p-2 mx-1 bg-gray plan-box radius-10"
                    style="border-top: 3px solid #808099; cursor: pointer;">
                    <div class="col-12 text-center" style="color: #00c854">
                        <b class="bold-font-title"> {{ $plan->name }} </b>
                    </div>
                    <div class="col-12 text-center my-4">
                        <p>{{ $plan->body }}</p>
                        <span style="font-weight: 800"><img class="ml-1" style="width: 20px"
                                src="{{ asset('files/other/images/done.png') }}">{{ $plan->advertise_count }} آگهی
                        </span>
                        <hr>
                        <span style="font-weight: 800"><img class="ml-1" style="width: 20px"
                                src="{{ asset('files/other/images/rocket.png') }}">{{ $plan->ad_to_top_count }} موشک
                        </span>
                        <hr>
                        <span class="original-price">{{ $plan->org_price }}</span>
                        <span class="discounted-price">{{ $plan->price }} تومان</span>
                        <br>
                        <a class="btn btn-lg btn-buy-pack mt-3" href="{{ route('buy.ad.plan', $plan->id) }}">فعال سازی</a>
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
