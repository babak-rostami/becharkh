@extends('index')

@section('title')
    انتخاب دسته بندی
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/css/rtable-create-edit.css') }}">

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/vue/3.2.40/vue.global.js"></script>
@endsection

@section('content')
    <div class="row justify-content-center p-2 justify-content-center">
        <div class="col-12 col-md-10 bg-wht p-3 mb-5 text-right" style="border-radius: 8px">
            <div class="row mb-3 justify-content-center">

                <div class="col-12 col-sm-8 p-2 mb-3"
                    style="background-color: #D6EAF8; border: 2px solid #AED6F1; border-radius: 8px">
                    <p class="font-weight-bold">دسته بندی آگهی را انتخاب کنید</p>
                    <p>با انتخاب دسته بندی صحیح مخاطبان آگهی شما را سریعتر پیدا کرده و آگهی توسط افراد بیشتری دیده می شود
                    </p>
                </div>


                <div class="col-12 col-sm-8 closee">
                    <b>دسته بندی آگهی را انتخاب کنید</b>
                </div>
                <div class="col-12 col-sm-8 search-cat-box" style="position: relative">
                    <input class="form-control mt-2 cat-search-input" type="text" placeholder="در دسته ها جستجو کنید...">
                    <div class="shadow radius-10 p-2 search-cat-div" style="display: none">
                        <ul class="p-0 show-search-cat-result mb-0">
                            <li class="px-3 pt-2 pb-3 cat-result-a radius-10 text-center">جستجوی دسته بندی در بچرخ</li>
                        </ul>
                    </div>
                </div>
            </div>
            @foreach ($categories as $cat)
                @if ($cat->has_ads)
                    <div class="row mb-1 justify-content-center">
                        @if ($cat->children->count() > 0)
                            @if ($cat->hasAdButChildrenDont())
                                <div class="col-12 col-sm-8" style="cursor: pointer">
                                    <a class="q-cat-a" href="{{ route('new.ad', $cat->slug) }}">
                                        {{ $cat->title }}
                                    </a>
                                </div>
                            @else
                                <div class="col-12 col-sm-8 closee" id="get-child-{{ $cat->id }}-btn"
                                    onclick="showCategoryChildren('{{ $cat->id }}',1)">
                                    <div class="q-cat-hasch">
                                        <span>{{ $cat->title }}</span>
                                        <span class="float-left" id="showit-{{ $cat->id }}"><img
                                                src="{{ asset('files/other/images/next.png') }}"></span>
                                        <span class="float-left" id="hideit-{{ $cat->id }}" style="display: none"><img
                                                src="{{ asset('files/other/images/sub.png') }}"></span>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="col-12 col-sm-8" style="cursor: pointer">
                                <a class="q-cat-a" href="{{ route('new.ad', $cat->slug) }}">
                                    {{ $cat->title }}
                                </a>
                            </div>
                        @endif
                        <div id="cat_{{ $cat->id }}_children" style="width: 90%;float: left;"></div>
                    </div>
                @endif
            @endforeach
            <div class="row justify-content-center">
                <div class="col-12 col-sm-8">
                    <span>اگر دسته بندی کالا یا خدمات شما وجود ندارد دسته بندی جدیدی ایجاد کنید</span>
                    <br>
                    <span class="text-danger">مثال : خودرو ، موبایل ، کتاب و ...</span>
                    <input type="text" pattern="^[a-zA-Z][\sa-zA-Z]*" class="form-control" id="new_cat_input"
                        placeholder="نام دسته بندی را بنویسید">
                    <a type="button" href="" class="btn btn-success text-left w-100" style="display: none"
                        id="new_cat_btn">افزودن دسته بندی</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var route = "{{ route('new.ad') }}";
        $('#new_cat_input').on('input', function() {
            yourInput = $('#new_cat_input').val();
            $('#new_cat_input').val(yourInput ? yourInput.trimStart() : '');
            if ($('#new_cat_input').val().length != 0) {
                r = route;
                r += "/" + $('#new_cat_input').val();
                $("#new_cat_btn").attr("href", r);
                $("#new_cat_btn").css("display", "block");
            } else {
                $("#new_cat_btn").attr("href", "");
                $("#new_cat_btn").css("display", "none");
            }
        });
    </script>

    <script>
        function showCategoryChildren(cat_id, forr) {
            is_open = 0;
            cat_btn = "#get-child-" + cat_id + "-btn";
            show_icon = "#showit-" + cat_id;
            hide_icon = "#hideit-" + cat_id;
            children_div = "#cat_" + cat_id + "_children";
            $(children_div).empty();
            if ($(cat_btn).hasClass("closee")) {
                is_open = 1;
                $(show_icon).css("display", "none");
                $(hide_icon).css("display", "inline");
                $(cat_btn).removeClass("closee");
            } else {
                $(cat_btn).addClass("closee");
                $(show_icon).css("display", "inline");
                $(hide_icon).css("display", "none");
            }
            if (is_open) {
                axios
                    .get("/get-category-children-create/" + cat_id + "/" + forr)
                    .then(
                        response =>
                        $(children_div).append(response.data.children)
                    );
            }
        }
    </script>

    <script>
        var typingTimerCat; //timer identifier
        var doneTypingCatInterval = 1000; //time in ms, 5 second for example
        //on keyup, start the countdown
        $(".cat-search-input").on("keyup", function() {
            clearTimeout(typingTimerCat);
            typingTimerCat = setTimeout(doneTypingCat, doneTypingCatInterval);
        });
        //on keydown, clear the countdown
        $(".cat-search-input").on("keydown", function() {
            $(".show-search-cat-result").html("<p class='p-3 text-center'>در حال جستجو...</p>");
            clearTimeout(typingTimerCat);
        });
        //user is "finished typing," do something
        function doneTypingCat() {
            $.ajax({
                method: "get",
                url: "/search-category-for-create/1/" + $(".cat-search-input").val(),
                success: function(msg) {
                    $(".show-search-cat-result").html(msg);
                }
            });
        }
        $(document).click(function(event) {
            if (!$(event.target).closest('.search-cat-box').length) {
                $('.search-cat-div').hide();
            } else {
                $('.search-cat-div').show();
            }
        });
    </script>
@endsection
