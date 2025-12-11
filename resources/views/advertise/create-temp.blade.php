@extends('index')


@section('title')
    آگهی جدید
@endsection


@section('style')
    <link
        href="{{ asset('mixassets/css/advertise/create.min.css') . '?lm=' . filemtime('mixassets/css/advertise/create.min.css') }}"
        rel="stylesheet" type="text/css" />

    <meta name="robots" content="noindex, nofollow">
@endsection


@section('content')
    <form id="adform" action="{{ route('ad.store') }}" method="post" role="form" enctype="multipart/form-data">
        @csrf

        <input type="hidden" id="category_id" name="category_id">

        <div class="row justify-content-center mt-md-4 bg-wht p-4 radius-10 mx-md-2 align-items-center">

            <div class="col-12 col-sm-10 bg-wht text-right mt-4">
                <div class="row">
                    <div class="col-12 text-center">
                        <b class="bold-font-title page-title-color">دسته بندی</b>
                        <p class="mb-0" id="image-tab-alert">قبل از ثبت آگهی باید قسمت های
                            با علامت
                            ضروری <label class="red-color">*</label> را تکمیل کنید</p>
                    </div>
                </div>
                @include('modals.create.select-category', ['categories' => $categories])
                <div class="row">
                    <div class="col-12 mb-4 text-right" id="features-box">
                    </div>
                </div>
            </div>

            <div id="after-scat-divs">
                <div class="col-12 col-sm-10 text-center mt-4">
                    <b class="bold-font-title page-title-color">جزئیات آگهی</b>
                    <br>
                    <p class="mt-2" id="image-tab-alert">اطلاعات مورد نیاز در مورد آگهی را به بازدیدکنندگان
                        ارائه دهید
                    </p>
                </div>


                <div class="col-12 col-sm-10 text-right mb-3">
                    <div id="price-option-box">
                        <span id="has-price-label">قیمت</span>
                        <br>
                        <button type="button" class="btn btn-sm btn-primary" onclick="priceShow(1)" id="show-price-option">
                            نشان
                            داده
                            شود</button>
                        <button type="button" class="btn btn-sm btn-outline-dark" onclick="priceShow(0)"
                            id="dshow-price-option">
                            نشان داده
                            نشود</button>
                    </div>

                    <div class="form-group mt-3" id="price-input-form">
                        <span id="price-tag-label">قیمت (به
                            تومان)</span>
                        <span class="red-color">*</span>
                        <span id="show-price-span"></span>
                        <input id="price" name="price" placeholder="قیمت را وارد کنید مثلا 3,000,000"
                            class="required form-control"
                            oninput="validateNumberInput(event);changePrice();countCharacters(this,0,13)" type="text">
                    </div>

                    <div id="phone-option-box">
                        <span id="has-phone-label">شماره تماس</span>
                        <br>
                        <button type="button" class="btn btn-sm btn-primary" onclick="phoneShow(1)" id="show-phone-option">
                            نشان
                            داده
                            شود</button>
                        <button type="button" class="btn btn-sm btn-outline-dark" onclick="phoneShow(0)"
                            id="dshow-phone-option">
                            نشان داده
                            نشود</button>
                    </div>

                    <div class="form-group mt-3" id="phone-input-form">
                        <span>شماره تماس</span>
                        <input class="form-control number-only" placeholder="شماره تماس" type="text" name="phone"
                            oninput="validateNumberInput(event)">
                    </div>

                    <div class="form-group">
                        <input type="hidden" class="required" name="province_id" id="province_id">
                        <input type="hidden" class="required" name="city_id" id="city_id">
                        <input type="hidden" name="district_id" id="district_id">
                        <span>موقعیت آگهی</span>
                        <span class="red-color ml-2">*</span>
                        <span id="choose-location-btn" onclick="showProvinces()" data-toggle="modal" data-dismiss="modal"
                            data-target="#choose-location-modal">انتخاب مکان آگهی
                            <img class="lazy-load" data-src="{{ $ftp_path . 'files/other/images/next.png' }}">
                        </span>
                    </div>
                    <div class="modal fade text-right" id="choose-location-modal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content radius-10">
                                <div class="modal-body">
                                    <img id="choose-location-back" class="lazy-load"
                                        data-src="{{ $ftp_path . 'files/other/images/back.png' }}">
                                    <span id="choose-location-title">انتخاب استان</span>
                                    <span class="close float-left cur-p" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </span>
                                    <input id="choose-location-search" class="form-control mt-3"
                                        placeholder="جستجو کنید..." type="text">
                                    <div id="choose-location-items">
                                    </div>
                                    <div id="search-choose-location-items">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <span>عنوان</span>
                        <span class="red-color ml-2">*</span>
                        <span id="title-error"></span>
                        <span id="charCountMin-title" class="input-char-min"></span>
                        <span id="charCountMax-title" class="input-char-max"></span>
                        <input class="required form-control" placeholder="عنوان : مثلا پژو 206 مدل 1400 کم کارکرد"
                            oninput="countCharacters(this,0,60)" id="title" type="text" name="title"
                            value="{{ old('title') }}">
                    </div>
                    <div class="form-group">
                        <span>توضیحات</span>
                        <textarea class="form-control body_style"
                            placeholder="توضیحات در مورد آگهی ، مواردی که ممکنه سوال خریدار باشه و به فروش بیشتر شما کمک کنه"
                            name="advertise_body">{{ old('advertise_body') }}</textarea>
                    </div>
                </div>

                <div class="col-12 col-sm-10 text-center mt-5">
                    <b class="bold-font-title page-title-color">تصاویر</b>
                    <br>
                    <p class="mt-2" id="image-tab-alert">انتخاب تصاویر خوب و با کیفیت بازدید آگهی را حداقل 5 برابر
                        افزایش
                        میدهد</p>
                    <hr>
                </div>

                <div class="col-12">
                    <div class="row justify-content-center" id="image-row">
                        <div class="col-12 col-md-3 text-center shadow-sm p-4 mx-1 mt-2 image-box">
                            <img class="def-image" id="blah-1"
                                src="{{ asset('files/other/images/choose-image.gif') }}" alt="تصویر را انتخاب کنید" />
                            <br>
                            <input onchange="readURL(this,1)" type='file' name="img-1" id="imgInp-1"
                                class="d-none" accept="image/*" data-msg-accept="برای انتخاب عکس کلیک کنید" />
                            <button type="button" class="btn btn-outline-dark"
                                onclick="document.getElementById('imgInp-1').click()">برای انتخاب
                                عکس
                                کلیک کنید</button>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-10 text-center my-3" id="select-image">
                    <span id="add-image-alert">تصاویر قبلی هنوز انتخاب نشدن</span>
                    <br>
                    <button onclick="addImage()" class="btn btn-info" type="button">
                        اضافه کردن عکس جدید
                        <img src="{{ asset('files/other/images/w-add.png') }}">
                    </button>
                </div>

                <div class="col-12 col-sm-10 text-center">

                    <p class="mt-2 errorCat"><span class="error-count"></span> بخش با علامت ضروری تکمیل نشده - برای ثبت
                        آگهی بخش های با علامت <label class="red-color">*</label> را
                        تکمیل
                        کنید</p>

                    <button class="btn btn-success w-100 my-3" id="sub_ad_form" type="button"
                        onclick="saveAdvertise(this)">
                        آگهی ثبت شود
                        <img src="{{ asset('files/other/images/next-light-w.png') }}">
                    </button>

                    @if (isset($user) && $user->canCreateAd())
                        <div>
                            <span id="diamond-e-span">موجودی شما برای ثبت آگهی کافی می باشد</span>
                            <br>
                            <span id="diamond-r-span">موجودی مورد نیاز {{ $ad_price }}
                                تومان</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </form>
@endsection


@section('script')
    @if (isset($user))
        <script>
            send_advertise_after_login = 0;
        </script>
    @else
        <script>
            send_advertise_after_login = 1;
        </script>
    @endif
    <script>
        const submit_form_id = "adform";

        page = 'create_advertise';
        var loadingGif = '<img src="{{ asset('files/other/images/loading.gif') }}">';
        var defImage = "{{ asset('files/other/images/choose-image.gif') }}";

        let province_id = null;
        let city_id = null;
        let district_id = null;
        const provinces = @json($provinces);
        const cities = @json($cities);
        const districts = @json($districts);

        //for select category modal
        const categories = @json($categories);
        var cat_children = [];
        const next_cat_img = "{{ asset('files/other/images/next.png') }}";
        const back_cat_img = "{{ asset('files/other/images/back.png') }}";
        const all_cat_img = "{{ asset('files/other/images/all-cat.webp') }}";
        const get_cat_fis_route = "{{ route('api.get.cat.fis.for.ad') }}";
        //end for select category modal
        //for select features modal
        const remove_item_img = "{{ asset('files/other/images/g-close.webp') }}";
        const add_new_item_img = "{{ asset('files/other/images/b-add.png') }}";
        var cat_selected = null;
        var citems = null;
        var cfeatures = null;
        var feature_items = null;
        //end for select features modal
    </script>

    <script type="text/javascript"
        src="{{ asset('mixassets/js/advertise/create.min.js') . '?lm=' . filemtime('mixassets/js/advertise/create.min.js') }}">
    </script>
@endsection
