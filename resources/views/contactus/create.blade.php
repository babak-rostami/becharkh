@extends('index')


@section('title')
    ارتباط با ما
@endsection


@section('style')
    <style>
        #cu-page-title {
            font-weight: 600;
            font-size: 24px;
        }

        #cu-ta {
            min-height: 100px
        }
    </style>
@endsection


@section('content')
    <div class="row justify-content-center bg-wht">

        <div class="col-12 col-md-8 p-4 my-4 radius-10 text-right">

            @if (session('success'))
                <p class="row alert alert-success text-center">{{ session('success') }}</p>
            @endif

            <h1 id="cu-page-title">ارتباط با پشتیبانی</h1>
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
                    {{-- @else
                        <div class="col-12 col-md-6 mt-2">
                            <div class="form-group">
                                <input type="text" placeholder="نام" class="form-control" name="name" id="name">
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mt-3">
                            <div class="form-group">
                                <input type="email" placeholder="ایمیل" class="form-control" name="email"
                                    id="email">
                            </div>
                        </div>
                    @endif --}}

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


                {{-- @if (!auth('user')->check())
                    <div class="row my-3">
                        <div class="col-12 text-center" dir="ltr">
                            <h4>معادله را حل کنید</h4>
                            <span style="font-size: 20px" id="first_number" class="badge badge-dark"></span>
                            <span style="font-size: 20px" class="badge badge-dark">+</span>
                            <span style="font-size: 20px" id="second_number" class="badge badge-dark"></span>
                            <span style="font-size: 20px" class="badge badge-dark">=</span>
                            <input type="number" style="width: 60px" id="eq_answer" />
                        </div>
                    </div>
                @endif --}}

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
    <script>
        $(document).ready(function() {
            var textarea = $('#cu-ta');

            textarea.on('input', function() {
                this.style.overflow = 'hidden';
                this.style.height = 0;
                this.style.height = this.scrollHeight + 'px';
            });

            // Force trigger the input event after setting the value of the textarea programmatically
            textarea.trigger('input');
        });
    </script>

    @if (!auth('user')->check())
        <script>
            $("#submit_btn").prop('disabled', true);
            $('#con_form').attr('action', "");
            first = Math.floor(Math.random() * 10);
            second = Math.floor(Math.random() * 10);
            $("#first_number").text(first);
            $("#second_number").text(second);
            $('#eq_answer').on('input', function() {
                if ($('#eq_answer').val() == first + second) {
                    $("#submit_btn").prop('disabled', false);
                    $('#con_form').attr('action', "{{ route('contactus.store') }}");
                } else {
                    $("#submit_btn").prop('disabled', true);
                    $('#con_form').attr('action', "");
                }
            });
        </script>
    @endif
@endsection
