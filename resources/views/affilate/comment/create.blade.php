@extends('index')

@section('title')
    نظر جدید
@endsection

@section('style')
    <link
        href="{{ asset('mixassets/css/affilate/comment-create.min.css') . '?lm=' . filemtime('mixassets/css/affilate/comment-create.min.css') }}"
        rel="stylesheet" type="text/css" />

    <script src="{{ asset('library/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('library/ckeditor/ckfinder.js') }}"></script>
    <script src="{{ asset('library/ckeditor/de.js') }}"></script>

    <meta name="robots" content="noindex">
@endsection

@section('content')
    <div class="row justify-content-center p-2">
        <div class="col-12 col-md-10 text-right p-2 mb-5 p-sm-5 bg-wht radius-10">
            @include('mainPart.comment-box', ['page' => 'admin_create_product_comment'])
        </div>
    </div>
@endsection

@section('script')
    <script>
        var loadingGif = '<img src="{{ asset('files/other/images/loading.gif') }}">';

        const submit_form_id = "cm_form";

        const page = 'admin_create_product_comment';
        const editor_img_upload_route =
            "{{ route('comment.editor.img.uplaod', ['_token' => csrf_token(), 'page' => 'admin_create_product_comment']) }}";

        const is_fuser_exist = "{{ route('admin.is.fuser.exist') }}";
    </script>
    <script type="text/javascript"
        src="{{ asset('mixassets/js/affilate/comment-create.min.js') . '?lm=' . filemtime('mixassets/js/affilate/comment-create.min.js') }}">
    </script>
@endsection
