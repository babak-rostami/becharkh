@extends('index')


@section('title')
    تماس با ما
@endsection


@section('style')
    <link
        href="{{ asset('mixassets/css/pages/contact-us.min.css') . '?lm=' . filemtime('mixassets/css/pages/contact-us.min.css') }}"
        rel="stylesheet" type="text/css" />
@endsection


@section('content')
    <div class="row justify-content-center bg-wht">

        <div class="col-12 col-md-8 p-4 my-4 radius-10 text-right">

            @if (session('success'))
                <p class="row alert alert-success text-center">{{ session('success') }}</p>
            @endif

            <h1 id="cu-page-title">تماس با ما</h1>
            <p>سلام ، این بخش را برای ارتباط راحت تر با شما ایجاد کرده ایم.</p>
            <p>هرگونه سوال ، پیشنهاد یا مشکلی دارید این فرم را تکمیل کنید و ما در اسرع وقت به پیام شما پاسخ میدهیم.</p>
            <b>چطور میتوانیم کمکتان کنیم؟</b>

            <form id="con_form" action="{{ route('contactus.store') }}" method="post" role="form">
                @csrf
                <div class="row">
                    @if (auth('user')->check())
                        <div class="col-12 mt-2">
                            <a target="_blank" class="decor-none" href="{{ route('user.dashboard', $user->username) }}">
                                <img style="width: 45px ; border-radius: 50%" src="{{ asset($user->image()) }}">
                                {{ $user->username }}</a>
                        </div>
                    @endif

                    <div class="col-12 mt-2">
                        <div class="form-group">
                            <input type="text" placeholder="موضوع" class="form-control" name="title" id="title">
                        </div>
                    </div>
                </div>

                <div class="form-group my-2">
                    <textarea class="form-control" id="cu-ta" placeholder="پیام خود را بنویسید" name="body" id="body"></textarea>
                    @error('body')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>

                @if (isset($user))
                    <button type="submit" id="submit_btn" class="w-100 btn btn-primary">ارسال</button>
                @else
                    <p class="alert alert-danger text-center">برای ارسال پیام وارد حساب کاربری خود شوید</p>
                @endif
            </form>

        </div>
    </div>
@endsection


@section('script')
    <script type="text/javascript"
        src="{{ asset('mixassets/js/pages/contact-us.min.js') . '?lm=' . filemtime('mixassets/js/pages/contact-us.min.js') }}">
    </script>
@endsection
