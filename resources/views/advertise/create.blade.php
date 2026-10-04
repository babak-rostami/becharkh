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

        @if ($categories->count() == 1)
            <input type="hidden" id="category_id" name="category_id" value="{{ $categories->first()['title'] }}">
        @else
            <input type="hidden" id="category_id" name="category_id">
        @endif

        <div class="row justify-content-center bg-wht p-4" id="ad-cat-page">
            <div class="col-12 col-sm-10 my-2 text-right px-0">
                @include('modals.create.select-category', ['categories' => $categories])
                <div class="row">
                    <div class="col-12 mb-4 text-right" id="features-box">
                    </div>
                </div>
                <span id="cat-error"></span>
                <button onclick="nextPage()" type="button" id="next-page-btn"
                    class="btn btn-primary next-page-btn mt-4">بعدی</button>
            </div>
        </div>

        <div class="row justify-content-center bg-wht p-4" id="ad-detail-page">

            <div class="col-12 col-sm-10 my-2 text-right px-0">
                <button onclick="prePage()" type="button" class="btn btn-light next-page-btn mt-4">صفحه قبل</button>
            </div>

            <div class="col-12 col-sm-10 mt-2 text-right px-0">
                <span class="label-title">ثبت آگهی</span>
                <span class="label-desc">با انتخاب عکس خوب آگهی حداقل 5 برابر بیشتر دیده میشه</span>
            </div>

            <div class="col-12 col-sm-10 text-right mt-2 px-0" id="select-img-box">
                <input type="file" id="images" name="images[]" class="d-none" accept="image/*" multiple />

                <div class="select-img-item" id="add-image-btn" onclick="clickSelectImage()">
                    <img src="{{ $ftp_path . 'files/other/images/b-add-32.png' }}" alt="add">
                    <br>
                    <span>افزودن عکس</span>
                </div>
            </div>

            <div class="col-12 col-sm-10 my-2 text-right px-0">
                <span class="label-title">عنوان آگهی <span class="red-color">*</span></span>
                <input class="form-control input-style" placeholder="مثلا 206 مدل 1400 سفید"
                    oninput="titleChange(this,0,60)" id="title" type="text" name="title">
                <span id="title-error"></span>
            </div>

            <div class="col-12 col-sm-10 my-2 text-right px-0">
                <span class="label-title">توضیحات آگهی <span class="red-color">*</span></span>
                <textarea class="form-control body-style" id="advertise_body" name="advertise_body" oninput="bodyChange()"
                    placeholder="توضیحات دقیقی بنویسید که اگه خودتون خریدار بودین دوست داشتین بدونین."></textarea>
                <span id="body-error"></span>
            </div>

            <div class="col-12 col-sm-10 my-2 text-right px-0">
                <span class="label-title" id="price-tag-label">قیمت (به تومان)</span>
                <button type="button" class="btn btn-primary mt-2" onclick="priceShow(1)" id="show-price-option">
                    نشان
                    داده
                    شود</button>
                <button type="button" class="btn btn-outline-dark mt-2" onclick="priceShow(0)" id="dshow-price-option">
                    نشان داده
                    نشود</button>
                <div id="price-input-form">
                    <span id="show-price-span"></span>
                    <input id="price" name="price" placeholder="3,500,000,000" class="form-control input-style"
                        oninput="validateNumberInput(event);changePrice();countCharacters(this,0,13)" type="text">
                </div>
            </div>

            <div class="col-12 col-sm-10 my-2 text-right px-0">
                <span class="label-title d-inline-block" id="price-tag-label">شماره تماس</span>
                <span class="red-color">*</span>

                {{-- <button type="button" class="btn btn-primary mt-2" onclick="phoneShow(1)" id="show-phone-option">
                    نشان
                    داده
                    شود</button>
                <button type="button" class="btn btn-outline-dark mt-2" onclick="phoneShow(0)" id="dshow-phone-option">
                    نشان داده
                    نشود</button> --}}
                <div id="phone-input-form">
                    <input class="form-control number-only input-style" placeholder="شماره تماس" type="text"
                        name="phone" id="advertise-phone" oninput="phoneOnChange(event, this, 0, 18)">
                    <span id="phone-error"></span>
                </div>
            </div>

            <div class="col-12 col-sm-10 my-2 text-right px-0">
                <input type="hidden" name="province_id" id="province_id">
                <input type="hidden" name="city_id" id="city_id">
                <input type="hidden" name="district_id" id="district_id">
                <span class="label-title">موقعیت آگهی <span class="red-color">*</span></span>

                <span id="choose-location-btn" onclick="showProvinces()" data-toggle="modal" data-dismiss="modal"
                    data-target="#choose-location-modal">انتخاب موقعیت مکانی
                    <img class="lazy-load" data-src="{{ $ftp_path . 'files/other/images/next.png' }}">
                </span>
                <span id="location-error"></span>

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
                                <input id="choose-location-search" class="form-control mt-3" placeholder="جستجو کنید..."
                                    type="text">
                                <div id="choose-location-items">
                                </div>
                                <div id="search-choose-location-items">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-10 my-2 text-right px-0">
                <button onclick="storeAdvertise()" id="store-ad-btn" type="button"
                    class="btn btn-primary next-page-btn mt-4">ثبت
                    آگهی</button>
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
