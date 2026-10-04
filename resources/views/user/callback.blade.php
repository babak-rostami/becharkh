@extends('index')



@section('title')
    نتیجه پرداخت
@endsection

@section('style')
    <meta name="robots" content="noindex">
@endsection

@section('content')

    <section class="check-out-section pt-80 pb-50">
        <div class="container">
            <div class="row justify-content-center p-4 bg-wht" dir="rtl">
                @if(isset($success))
                    <div class="col-lg-6 mb-30 text-center mt-5 shadow p-5" style="border: 1px solid #00bb00">
                        <span>پرداخت شما با موفقیت انجام شد</span>
                    </div>
                @endif
                @if(isset($error))
                    <div class="col-lg-6 mb-30 text-center mt-5 shadow p-5" style="border: 1px solid #bb0000">
                        <p>پرداخت ناموفق بود</p>
                        <p></p>
                        <span>{{$error}}</span>
                    </div>
                @endif
                @if(auth('user')->check())
                    <div class="col-12 text-center my-5">
                        <a class="btn btn-info mt-2" href="{{route('user.dashboard.edit')}}">بازگشت
                            به حساب کاربری</a>
                        <a class="btn btn-danger mt-2" href="{{route('contactus.create')}}">پیام به پشتیبانی</a>
                    </div>
                @endif
            </div>
        </div>
    </section>

@endsection

@section('script')

@endsection
