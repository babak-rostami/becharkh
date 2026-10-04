@extends('index')

@section('title')
    نظر جدید
@endsection

@section('style')
    <link
        href="{{ asset('mixassets/css/category/comment-create.min.css') . '?lm=' . filemtime('mixassets/css/category/comment-create.min.css') }}"
        rel="stylesheet" type="text/css" />

    <script src="{{ asset('library/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('library/ckeditor/ckfinder.js') }}"></script>
    <script src="{{ asset('library/ckeditor/de.js') }}"></script>

    <meta name="robots" content="noindex">
@endsection

@section('content')
    <div class="row justify-content-center p-2">

        <div class="col-12 col-md-10 text-right p-2 mb-5 p-sm-5 bg-wht radius-10">
            @include('mainPart.comment-box', ['page' => 'admin_create_comment'])
        </div>

    </div>
@endsection

@section('script')
    <script>
        var loadingGif = '<img src="{{ asset('files/other/images/loading.gif') }}">';

        let csrf_t = "{{ csrf_token() }}";

        //for select category modal
        const categories = @json($categories);
        var cat_children = [];
        const next_cat_img = "{{ asset('files/other/images/next.png') }}";
        const back_cat_img = "{{ asset('files/other/images/back.png') }}";
        const all_cat_img = "{{ asset('files/other/images/all-cat.webp') }}";
        const get_cat_fis_route = "{{ route('api.get.cat.fis') }}";
        var category_id = null;
        //end for select category modal
        //for select features modal
        const remove_item_img = "{{ $ftp_path . 'files/other/images/g-close.webp' }}";
        const add_new_item_img = "{{ $ftp_path . 'files/other/images/b-add.png' }}";
        var cat_selected = null;
        var citems = null;
        var cfeatures = null;
        var feature_items = null;
        //end for select features modal

        const submit_form_id = "cm_form";

        const page = 'admin_create_comment';
        const editor_img_upload_route =
            "{{ route('comment.editor.img.uplaod', ['_token' => csrf_token(), 'page' => 'admin_create_comment']) }}";

        const is_fuser_exist = "{{ route('admin.is.fuser.exist') }}";
    </script>
    <script type="text/javascript"
        src="{{ asset('mixassets/js/category/comment-create.min.js') . '?lm=' . filemtime('mixassets/js/category/comment-create.min.js') }}">
    </script>
@endsection
