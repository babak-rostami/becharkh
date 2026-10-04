@extends('index')


@section('title')
    ویدیو جدید
@endsection

@section('style')
    <meta name="robots" content="noindex">

    <link href="{{ asset('assets/css/video/create-edit.css') . '?lm=' . filemtime('assets/css/video/create-edit.css') }}"
        rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/resumable.js/1.1.0/resumable.min.js"></script>
@endsection


@section('content')



    <div class="row bg-wht justify-content-center">
        <div class="col-10 text-center">
            @if (session('success'))
                <div>
                    <p class="alert alert-success text-center my-1">{{ session('success') }}</p>
                </div>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <p class="alert alert-danger my-1">{{ $error }}</p>
                @endforeach
            @endif
        </div>

        <div class="col-12 col-md-4 text-center mt-3" id="upload-video">
            <div id="upload-video-box">
                <img src="{{ asset('files/other/images/video.gif') }}">
                <div id="hide-upload-btn">
                    <br>
                    <button type="button" id="browseVideoFile" class="btn btn-danger"
                        onclick="document.getElementById('video-file-input').click()">انتخاب ویدیو</button>
                    <br>
                    <input onchange="videoAdded()" accept="video/*" class="d-none" type="file" name="video"
                        id="video-file-input">
                    <button type="button" onclick="startUploadVideo()" class="btn btn-success mt-2"
                        id="start-upload-video-btn">شروع آپلود</button>
                </div>
            </div>
            <span id="video-error">ویدیو را آپلود کنید</span>
            <div id="video-upload-info">
                <progress value="0" max="100" id="proccess-video"></progress>
                <br>
                <span id="video-upload-er">آپلود ناموفق اتصال اینترنت خود را بررسی کنید!</span>
                <span id="video-upload-suc">ویدیو با موفقیت آپلود شد</span>
                <span id="video-uploading">در حال آپلود... صفحه را ترک نکنید</span>
            </div>
            <div id="video-require-info">
                <span id="video-size-info"></span>
                <br>
                <span id="need-diamond-info"></span>
                <br>
                <span id="user-diamond-info"></span>
                <a class="btn btn-primary" id="go_to_d_plan" href="" data-toggle="modal"
                    data-target="#charge-account">افزایش اعتبار</a>
            </div>
        </div>
    </div>

    <form id="videoform" action="{{ route('user.video.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        {{ method_field('PUT') }}

        <input type="hidden" id="category_id" name="category_id">



        <div class="row bg-wht justify-content-center">
            <div class="col-12 col-md-10 text-right mt-2">
                @include('modals.create.select-category', ['categories' => $categories])
            </div>

            <div class="col-12 col-md-10 mb-4 text-right" id="features-box">
            </div>

            <div class="col-12 col-md-4 text-center mt-3">
                <div id="select-img-box">
                    <img class="v-img-preview" id="blah" src="{{ asset('files/other/images/choose-image.gif') }}"
                        alt="انتخاب عکس" />
                    <br>
                    <input onchange="readURL(this)" type="file" name="image" id="image" accept="image/*"
                        data-msg-accept="انتخاب عکس" style="display:none" />
                    <button type="button" class="btn btn-outline-dark"
                        onclick="document.getElementById('image').click()">انتخاب عکس</button>
                </div>
                <span id="image-error"></span>
            </div>

            <div class="col-12 col-md-10 text-right mt-4">
                <div class="form-group">
                    <label>عنوان</label>
                    <input oninput="countCharacters(this,15,60)" value="{{ old('title') }}" type="text"
                        class="form-control" id="title" name="title"
                        placeholder="موضوع ویدیو ... مثلا مزایا و معایب خرید پژو 206 چیست؟">
                    <span id="title-error"></span>
                    <span id="charCountMin-title" class="input-char-min"></span>
                    <span id="charCountMax-title" class="input-char-max"></span>
                </div>

                <div class="form-group">
                    <label>توضیحات</label>
                    <textarea
                        placeholder="این متن ابتدای صفحه ویدیو نمایش داده میشود و توضیحاتی درباره ی ویدیو هستش مثلا قبل از خرید پژو 206 به نکاتی که در این ویدیو مطرح شده توجه کنید"
                        oninput="countCharacters(this,15,null)" class="form-control" name="description" id="description">{{ old('description') }}</textarea>
                    <span id="description-error"></span>
                    <span id="charCountMin-description" class="input-char-min"></span>
                    <span id="charCountMax-description" class="input-char-max"></span>
                </div>
            </div>

            <div class="col-12 col-sm-10 text-center mt-3">
                <p class="error"></p>
                <button type="button" class="btn btn-success w-100 mb-5" id="saveVideoBtn" onclick="saveVideo()">انتشار
                    مطلب</button>
            </div>

        </div>

    </form>

@endsection


@section('script')
    <script>
        var loadingGif = '<img src="{{ asset('files/other/images/loading.gif') }}">';
        var video_id = null;
        const user_money = "{{ auth('user')->user()->money }}";
        const video_file_store_route = "{{ route('video.file.store') }}";
        const video_create_csrf = "{{ csrf_token() }}";
        var uploading = 0;

        const submit_form_id = "videoform";
        //for select category modal
        const categories = @json($categories);
        var cat_children = [];
        const next_cat_img = "{{ asset('files/other/images/next.png') }}";
        const back_cat_img = "{{ asset('files/other/images/back.png') }}";
        const all_cat_img = "{{ asset('files/other/images/all-cat.webp') }}";
        const get_cat_fis_route = "{{ route('api.get.cat.fis') }}";
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
        src="{{ asset('assets/js/video/create.js') . '?lm=' . filemtime('assets/js/video/create.js') }}"></script>
@endsection
