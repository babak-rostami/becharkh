@extends('index')

@section('title')
    مطلب جدید
@endsection

@section('style')
    <link href="{{ asset('mixassets/css/forum/create.min.css') . '?lm=' . filemtime('mixassets/css/forum/create.min.css') }}"
        rel="stylesheet" type="text/css" />

    <script src="{{ $ftp_path . 'library/ckeditor/ckeditor.js' }}"></script>
    <script src="{{ $ftp_path . 'library/ckeditor/ckfinder.js' }}"></script>
    <script src="{{ $ftp_path . 'library/ckeditor/de.js' }}"></script>

    <meta name="robots" content="noindex, nofollow">
@endsection

@section('content')
    <div class="row justify-content-center bg-wht pt-2 pb-5">

        <div class="col-12 col-md-10 text-right p-4 p-sm-5">

            <span id="cq-page-title">مطلب جدید</span>
            <hr>

            @include('mainPart.comment-box', ['page' => 'create_question'])

        </div>

    </div>
@endsection

@section('script')
    <script>
        const submit_form_id = "qform";
        const page = 'create_question';

        const editor_img_upload_route =
            "{{ route('comment.editor.img.uplaod', ['_token' => csrf_token(), 'page' => 'create_question']) }}";
    </script>
    <script type="text/javascript"
        src="{{ asset('mixassets/js/forum/create.min.js') . '?lm=' . filemtime('mixassets/js/forum/create.min.js') }}">
    </script>
@endsection
