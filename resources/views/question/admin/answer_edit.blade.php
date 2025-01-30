@extends('index')

@section('title')
    ویرایش نظر
@endsection

@section('style')
    <script src="{{ asset('library/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('library/ckeditor/ckfinder.js') }}"></script>
    <script src="{{ asset('library/ckeditor/de.js') }}"></script>

    <link
        href="{{ asset('mixassets/css/forum/edit-answer-admin.min.css') . '?lm=' . filemtime('mixassets/css/forum/edit-answer-admin.min.css') }}"
        rel="stylesheet" type="text/css" />
    <meta name="robots" content="noindex">
@endsection

@section('content')
    <div class="row justify-content-center p-2">

        <div class="col-12 col-md-10 text-right p-2 mb-5 p-sm-5 bg-wht radius-10">

            @include('mainPart.comment-box', ['page' => 'admin_edit_qanswer'])

        </div>

    </div>
@endsection

@section('script')
    <script>
        const submit_form_id = "cm_form";

        const page = 'admin_edit_qanswer';

        var editor_img_upload_route =
            "{{ route('comment.editor.img.uplaod', ['_token' => csrf_token(), 'page' => 'admin_edit_qanswer']) }}";
    </script>
    <script type="text/javascript"
        src="{{ asset('mixassets/js/forum/edit-answer-admin.min.js') . '?lm=' . filemtime('mixassets/js/forum/edit-answer-admin.min.js') }}">
    </script>
@endsection
