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


    <form id="videoform" action="{{ route('admin.video.update') }}" method="post" enctype="multipart/form-data">
        @csrf
        {{ method_field('PUT') }}

        <input type="hidden" name="video_id" value="{{ $video->id }}">
        <input type="hidden" id="category_id" name="category_id" value="{{ $video->category_id }}">

        <div class="row bg-wht justify-content-center">

            <div class="col-12 col-md-4 text-center mt-3">
                <div id="select-img-box">
                    <img class="v-img-preview" id="blah" src="{{ asset($video->image()) }}" alt="تغییر عکس" />
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

                <div class="form-group">
                    <label>لینک محصول</label>
                    <input value="{{ $video->pr_link }}" type="text" class="form-control" id="pr_link" name="pr_link">
                </div>

                <div class="form-group my-4">
                    <span>توضیحات درباره ویدیو</span>
                    <textarea oninput="countCharacters(this,15,null)" required class="form-control" name="description" id="description">{{ $video->description }}</textarea>
                    <span id="description-error"></span>
                    <span id="charCountMin-description" class="input-char-min"></span>
                    <span id="charCountMax-description" class="input-char-max"></span>
                </div>

            </div>

            <div class="col-10 text-right mt-2">
                @include('modals.create.select-category', ['categories' => $categories])
            </div>

            <div class="col-10 mb-4 text-right" id="features-box">
            </div>

            <div class="col-10 text-center mt-3">
                <p class="error"></p>
                <button type="button" class="btn btn-success w-100 mb-5" id="updateVideoBtn" onclick="updateVideo()">ثبت
                    تغییرات</button>
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
        var cat_selected = "{{ $category->id }}";
        var citems = @json($citems);
        var cfeatures = @json($cfeatures);
        var feature_items = @json($videoFeatueItems);
        //end for select features modal
    </script>

    <script type="text/javascript"
        src="{{ asset('assets/js/video/admin-edit.js') . '?lm=' . filemtime('assets/js/video/admin-edit.js') }}"></script>
@endsection
