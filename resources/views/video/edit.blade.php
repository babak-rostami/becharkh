@extends('index')

@section('title')
    ویرایش {{ $video->title }}
@endsection

@section('style')
    <meta name="robots" content="noindex">

    <link href="{{ asset('assets/css/video/create-edit.css') . '?lm=' . filemtime('assets/css/video/create-edit.css') }}"
        rel="stylesheet" type="text/css" />
@endsection

@section('content')


    <div class="row bg-wht justify-content-center">
        <div class="col-12 text-center">
            <span id="max-size-v">
                تغییر این ویدیو تا حجم {{ $video->max_size }} مگابایت رایگان است.
            </span>
        </div>

        <div class="col-12 col-md-4 text-center mt-3">
            <div id="upload-video-box">
                <img class="v-img-preview" src="{{ asset($video->thumb()) }}">
                <div id="hide-upload-btn">
                    <button type="button" id="browseVideoFile" class="btn btn-danger"
                        onclick="document.getElementById('video-file-input').click()">تغییر ویدیو</button>
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
                <span id="video-upload-suc">ویدیو با موفقیت تغییر کرد</span>
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

        <input type="hidden" name="video_id" value="{{ $video->id }}">
        <input type="hidden" id="category_id" name="category_id" value="{{ $video->category_id }}">

        <div class="row bg-wht justify-content-center">

            <div class="col-12 col-md-4 text-center mt-3">
                <div id="select-img-box">
                    <img class="v-img-preview" id="blah" src="{{ asset($video->thumb()) }}" alt="تغییر عکس" />
                    <br>
                    <input onchange="readURL(this)" type="file" name="image" id="image" accept="image/*"
                        data-msg-accept="تغییر عکس" style="display:none" />
                    <button type="button" class="btn btn-outline-dark"
                        onclick="document.getElementById('image').click()">تغییر عکس</button>
                </div>
                <span id="image-error"></span>
            </div>

            <div class="col-10 text-center mt-3">
                @if (session('success'))
                    <div>
                        <p class="alert alert-success text-center my-1">{{ session('success') }}</p>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger my-1">
                        @foreach ($errors->all() as $error)
                            <p style="color: #000000">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="col-10 text-right">
                <div class="form-group">
                    <span>عنوان ویدیو</span>
                    <input oninput="countCharacters(this,15,60)" type="text" value="{{ $video->title }}"
                        class="form-control" name="title" id="title">
                    <span id="title-error"></span>
                    <span id="charCountMin-title" class="input-char-min"></span>
                    <span id="charCountMax-title" class="input-char-max"></span>
                </div>

                <div class="form-group my-4">
                    <span>توضیحات درباره ویدیو</span>
                    <textarea oninput="countCharacters(this,15,null)" required class="form-control" name="description" id="description">{{ $video->description }}</textarea>
                    <span id="description-error"></span>
                    <span id="charCountMin-description" class="input-char-min"></span>
                    <span id="charCountMax-description" class="input-char-max"></span>
                </div>

                <span id="video-cat-title">{{ $category->title }}</span>

            </div>

            <div class="col-10 mb-4 text-right" id="features-box">
            </div>

            <div class="col-10 text-center mt-3">
                <p class="error"></p>
                <button type="button" class="btn btn-success w-100 mb-5" id="updateVideoBtn"
                    onclick="updateVideo()">ثبت تغییرات</button>
            </div>

        </div>

    </form>
@endsection


@section('script')
    <script>
        const loadingGif = '<img src="{{ asset('files/other/images/loading.gif') }}">';
        const user_money = "{{ auth('user')->user()->money }}";
        const video_file_store_route = "{{ route('video.file.store') }}";
        const video_edit_csrf = "{{ csrf_token() }}";
        const video_id = "{{ $video->id }}";
        const video_max_size = "{{ $video->max_size }}";
        var uploading = 0;

        const category_id = "{{ $video->category_id }}";
        const submit_form_id = "videoform";

        //for select features modal
        const remove_item_img = "{{ asset('files/other/images/g-close.webp') }}";
        const add_new_item_img = "{{ asset('files/other/images/b-add.png') }}";
        var cat_selected = "{{ $category->id }}";
        var citems = @json($citems);
        var cfeatures = @json($cfeatures);
        var feature_items = @json($videoFeatueItems);
        //end for select features modal
    </script>

    <script type="text/javascript"
        src="{{ asset('assets/js/video/edit.js') . '?lm=' . filemtime('assets/js/video/edit.js') }}"></script>
@endsection
