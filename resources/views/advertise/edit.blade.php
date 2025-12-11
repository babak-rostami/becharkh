@extends('index')


@section('title')
    ویرایش آگهی
@endsection


@section('style')
    <link
        href="{{ asset('mixassets/css/advertise/edit.min.css') . '?lm=' . filemtime('mixassets/css/advertise/edit.min.css') }}"
        rel="stylesheet" type="text/css" />

    <meta name="robots" content="noindex">
@endsection


@section('content')
    <form id="adform" action="{{ route('ad.update', $advertise->id) }}" method="post" role="form"
        enctype="multipart/form-data">
        @csrf

        {{ method_field('PUT') }}

        <div class="row justify-content-center bg-wht p-4">

            <div class="col-12 col-sm-10 mt-2 text-right px-0">
                <span class="label-title">ویرایش آگهی</span>
                <span class="label-desc">با انتخاب عکس خوب آگهی حداقل 5 برابر بیشتر دیده میشه</span>
            </div>

            <div class="col-12 col-sm-10 mt-2 text-right px-0">
                <div id="image-container" class="d-flex flex-wrap">

                    {{-- عکس‌های موجود از دیتابیس --}}
                    @foreach ($advertise->getImages() as $index => $img)
                        <div class="select-img-item old-image" id="select-img-item-{{ $index }}"
                            data-id="{{ $index }}">
                            <img src="{{ $img['url'] }}" alt="image">
                            {{-- <div class="img-actions">
                                <button type="button" class="edit-img-btn"
                                    onclick="editOldImage({{ $index }}, '{{ $advertise->id }}')">ویرایش</button>
                                <button type="button" class="delete-img-btn"
                                    onclick="confirmDeleteImage({{ $index }}, '{{ $advertise->id }}')">حذف</button>
                            </div> --}}
                        </div>
                    @endforeach

                    {{-- مدال تأیید حذف --}}
                    {{-- <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content text-center p-3">
                                <p>آیا از حذف این تصویر مطمئن هستید؟</p>
                                <div class="d-flex justify-content-center mt-3">
                                    <button class="btn btn-danger mx-2" id="confirmDeleteBtn">بله</button>
                                    <button class="btn btn-secondary mx-2" data-bs-dismiss="modal">خیر</button>
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    {{-- input انتخاب عکس‌های جدید --}}
                    <input type="file" id="images" name="images[]" class="d-none" multiple accept="image/*">

                    {{-- دکمه افزودن عکس جدید --}}
                    <div class="select-img-item add-btn" id="add-image-btn" onclick="clickSelectImage()">
                        <img src="{{ $ftp_path . 'files/other/images/b-add-32.png' }}" alt="add">
                        <br>
                        <span>افزودن عکس</span>
                    </div>
                </div>
            </div>


            <div class="col-12 col-sm-10 my-2 text-right px-0">
                <span class="label-title">عنوان آگهی <span class="red-color">*</span></span>
                <input class="form-control input-style" placeholder="مثلا 206 مدل 1400 سفید" type="text"
                    oninput="titleChange(this,0,60)" id="title" name="title" value="{{ $advertise->title }}">
                <span id="title-error"></span>
            </div>

            <div class="col-12 col-sm-10 my-2 text-right px-0">
                <span class="label-title">توضیحات آگهی <span class="red-color">*</span></span>
                <textarea class="form-control body-style" oninput="bodyChange()"
                    placeholder="توضیحات دقیقی بنویسید که اگه خودتون خریدار بودین دوست داشتین بدونین." id="advertise_body" name="advertise_body">{{ $advertise->body }}</textarea>
                <span id="body-error"></span>
            </div>


            <div class="col-12 col-sm-10 my-2 text-right px-0">
                <span class="label-title" id="has-price-label">قیمت (به تومان)</span>
                <button type="button" class="btn btn-sm btn-primary mt-2" onclick="priceShow(1)" id="show-price-option">
                    نشان
                    داده
                    شود</button>
                <button type="button" class="btn btn-sm btn-outline-dark mt-2" onclick="priceShow(0)"
                    id="dshow-price-option">
                    نشان داده
                    نشود</button>

                <div id="price-input-form">
                    <span id="show-price-span"></span>
                    <input id="price" name="price" value="{{ $advertise->price }}"
                        placeholder="قیمت را وارد کنید مثلا 3,000,000" class="input-style form-control"
                        oninput="validateNumberInput(event);changePrice();countCharacters(this,0,13)" type="text">
                </div>
            </div>

            <div class="col-12 col-sm-10 my-2 text-right px-0">
                <span class="label-title d-inline-block" id="price-tag-label">شماره تماس</span>
                <span class="red-color">*</span>
                {{-- <button type="button" class="btn btn-sm btn-primary mt-2" onclick="phoneShow(1)" id="show-phone-option">
                    نشان
                    داده
                    شود</button>
                <button type="button" class="btn btn-sm btn-outline-dark mt-2" onclick="phoneShow(0)"
                    id="dshow-phone-option">
                    نشان داده
                    نشود</button> --}}

                <div id="phone-input-form">
                    <input class="form-control number-only input-style" value="{{ $advertise->phone }}"
                        placeholder="شماره تماس" type="text" name="phone" id="advertise-phone"
                        oninput="phoneOnChange(event, this, 0, 18)">
                    <span id="phone-error"></span>
                </div>
            </div>

            <div class="col-12 col-sm-10 my-2 text-right px-0">
                <input type="hidden" name="province_id" id="province_id">
                <input type="hidden" name="city_id" id="city_id">
                <input type="hidden" name="district_id" id="district_id">
                <span class="label-title">موقعیت آگهی <span class="red-color">*</span></span>
                <span id="choose-location-btn" onclick="showProvinces()" data-toggle="modal" data-dismiss="modal"
                    data-target="#choose-location-modal">انتخاب مکان آگهی
                    <img class="lazy-load" data-src="{{ $ftp_path . 'files/other/images/next.png' }}">
                </span>
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

            <div class="col-12 col-sm-10 text-center px-0">
                <button class="btn btn-primary next-page-btn mt-4" id="store-ad-btn" type="button"
                    onclick="storeAdvertise()">
                    ویرایش آگهی
                </button>
            </div>

        </div>

    </form>
@endsection

@section('script')
    <script>
        var loadingGif = '<img src="{{ asset('files/other/images/loading.gif') }}">';
        var adImageCount = "{{ count($advertise->getImages()) }}";
        var defImage = "{{ asset('files/other/images/choose-image.gif') }}";

        const submit_form_id = 'adform';

        const provinces = @json($provinces);
        const cities = @json($cities);
        const districts = @json($districts);
        let province_id = "{{ $advertise->province_id }}"
        let city_id = "{{ $advertise->city_id }}"
        let district_id = "{{ $advertise->district_id ?? null }}"

        //for select features modal
        const remove_item_img = "{{ asset('files/other/images/g-close.webp') }}";
        const add_new_item_img = "{{ asset('files/other/images/b-add.png') }}";
        var citems = @json($citems);
        var cfeatures = @json($cfeatures);
        var feature_items = @json($advertiseFeatueItems);;
        //end for select features modal
    </script>
    <script type="text/javascript"
        src="{{ asset('mixassets/js/advertise/edit.min.js') . '?lm=' . filemtime('mixassets/js/advertise/edit.min.js') }}">
    </script>

    @if (!isset($advertise->price))
        <script>
            priceShow(0);
        </script>
    @endif
    @if (!isset($advertise->phone))
        <script>
            phoneShow(0);
        </script>
    @endif
@endsection
