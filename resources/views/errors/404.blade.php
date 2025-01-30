@extends('index')


@section('title')
    صفحه مورد نظر پیدا نشد
@endsection

@section('style')
    <meta name="robots" content="noindex">
@endsection

@section('content')
    <div class="row justify-content-center bg-wht mb-5 mx-2 radius-10">
        <div class="col-11 col-md-7 text-center p-5 bg-wht shadow my-5 radius-10"
            style="background-color: #669bbc;color: #ffffff">
            <h2 class="bold-font-title">صفحه مورد نظر پیدا نشد</h2>
            <p class="mt-3">ممکن است آدرس تغییر کرده باشد دوباره جستجو کنید</p>

            <div class="mt-4" id="magnify-input-404">
                <input class="form-control search-input-404 my-2" type="text" placeholder="جستجو کنید...">
                <div class="show-search-result-404 pt-2 pb-5 text-dark"></div>
            </div>

            <hr>
            <a class="btn btn-light mt-2" href="{{ route('question.index') }}">
                <img src="{{ asset('files/other/images/crtable.png') }}" style="width: 25px">
                ورود به انجمن</a>
            <a class="btn btn-light mt-2" href="{{ route('ads.index') }}">
                <img src="{{ asset('files/other/images/basket.png') }}" style="width: 25px">
                بازار</a>
            <a class="btn btn-light mt-2" href="{{ route('blog.index') }}">
                <img src="{{ asset('files/other/images/adlist.png') }}" style="width: 25px">
                مجله</a>
            <br>
            <a class="btn btn-warning mt-2" href="{{ route('home') }}">صفحه اصلی</a>
        </div>


        <div class="col-12 col-md-6">
            <h2 class="bold-font-title text-right my-2">
                <img style="width: 48px" src="{{ asset('files/other/images/shop.jpg') }}">
                آخرین آگهی ها
            </h2>
            @foreach (App\Models\RtablePageData::staticSuggestAdvertises() as $advertise)
                <div class="row align-items-center radius-10 shadow mt-2 mx-1">
                    <div class="col-5 col-md-3">
                        <a class="decor-none" target="_blank"
                            href="{{ route('ad.show', ['category_slug' => $advertise->category->slug, 'slug' => $advertise->slug, 'random' => $advertise->random_id]) }}">
                            @if ($advertise->image() == null)
                                <img class="w-100" alt="{{ $advertise->title }}" title="{{ $advertise->title }}"
                                    src="{{ asset('files/other/images/default.jpg') }}">
                            @else
                                <img class="w-100" alt="{{ $advertise->title }}" title="{{ $advertise->title }}"
                                    src="{{ asset($advertise->image()) }}">
                            @endif
                        </a>
                    </div>
                    <div class="col-7 col-md-9">
                        <div class="row">

                            <div class="col-12 text-right">
                                <a class="decor-none" target="_blank"
                                    href="{{ route('ad.show', ['category_slug' => $advertise->category->slug, 'slug' => $advertise->slug, 'random' => $advertise->random_id]) }}">
                                    <h2 class="my-3 advertise-title">{{ $advertise->title }}</h2>
                                </a>
                            </div>

                            <div class="col-12 col-md-3 mt-2">
                                <span class="ml-1" style="font-size: 14px">{{ $advertise->location }}</span>
                            </div>

                            <div class="col-11 mx-auto hr-style my-2">
                            </div>

                            <div class="col-12 col-md-9 pl-4 mb-3 text-right" style="font-size: 15px">
                                @if ($advertise->price != null)
                                    <span class="price-border">{{ number_format((int) $advertise->price) }}
                                        تومان</span>
                                @else
                                    <span>توافقی</span>
                                @endif
                            </div>
                            <div class="col-12 col-md-2 mb-3 float-right d-none d-sm-block">
                                <a class="float-right btn btn-secondary" style="font-size: 15px; text-decoration: none"
                                    target="_blank"
                                    href="{{ route('ad.show', ['category_slug' => $advertise->category->slug, 'slug' => $advertise->slug, 'random' => $advertise->random_id]) }}">
                                    مشاهده
                                </a>
                            </div>

                        </div>
                    </div>

                </div>
            @endforeach
        </div>


        <div class="col-12 col-md-6">
            <h2 class="bold-font-title text-right my-2">
                <img style="width: 48px" src="{{ asset('files/other/images/crtable.png') }}">
                آخرین سوال ها
            </h2>
            @foreach (App\Models\RtablePageData::staticSuggestRtables() as $sr)
                <a href="{{ route('question.show', ['category' => $sr->category->slug, 'slug' => $sr->slug, 'random' => $sr->random_id]) }}"
                    style="border-bottom: 2px solid #c0c0c0"
                    class="decor-none row justify-content-between shadow mb-4 p-3 mx-1">
                    <div class="col-8 col-md-6 text-left">
                        <img style="width: 36px; border-radius: 50%" src="{{ asset($sr->user->image()) }}">
                        <span>{{ $sr->title }}</span>
                        <p>{{ Str::limit($sr->body, 60, '...') }}</p>
                    </div>
                    <div class="col-4 col-md-3 text-right">
                        @if (isset($sr->answer_count))
                            <b>{{ $sr->answer_count }} <img style="width: 22px"
                                    src="{{ asset('files/other/images/comment.svg') }}"></b>
                        @else
                            <img style="width: 22px" src="{{ asset('files/other/images/help.png') }}">
                            <br>
                            <b class="text-primary">کمک</b>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

    </div>
@endsection


@section('script')
    <script>
        //for search 404
        var typingTimer404; //timer identifier
        var doneTypingInterval404 = 500; //time in ms, 5 second for example
        //on keyup, start the countdown
        $(".search-input-404").on("keyup", function() {
            clearTimeout(typingTimer404);
            typingTimer404 = setTimeout(doneTyping404, doneTypingInterval404);
        });
        //on keydown, clear the countdown
        $(".search-input-404").on("keydown", function(event) {
            if (
                event.key !== "Control" &&
                !(
                    event.key === "ArrowUp" ||
                    event.key === "ArrowDown" ||
                    event.key === "ArrowLeft" ||
                    event.key === "ArrowRight"
                )
            ) {
                $(".show-search-result-404").html("<span>در حال جستجو...</span>");
                clearTimeout(typingTimer404);
            }
        });
        //user is "finished typing," do something
        function doneTyping404() {
            $.ajax({
                method: "get",
                url: "/main-search/" + $(".search-input-404").val(),
                success: function(msg) {
                    $(".show-search-result-404").html(msg);
                }
            });
        }
    </script>
@endsection
