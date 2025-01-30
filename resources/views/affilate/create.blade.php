@extends('index')

@section('title')
    افیلیت جدید
@endsection

@section('style')
    <script src="{{ asset('library/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('library/ckeditor/ckfinder.js') }}"></script>
    <script src="{{ asset('library/ckeditor/de.js') }}"></script>

    <link
        href="{{ asset('mixassets/css/affilate/create.min.css') . '?lm=' . filemtime('mixassets/css/affilate/create.min.css') }}"
        rel="stylesheet" type="text/css" />

    <meta name="robots" content="noindex">
@endsection

@section('content')
    <div class="row justify-content-center p-2">
        <div class="col-12 col-md-10 text-right p-2 mb-5 p-sm-5 create-div">
            @include('mainPart.comment-box', ['page' => 'create_affilate'])
        </div>
    </div>
@endsection

@section('script')
    <script>
        const page = 'create_affilate';
        const editor_img_upload_route =
            "{{ route('comment.editor.img.uplaod', ['_token' => csrf_token(), 'page' => 'create_affilate']) }}";
    </script>
    <script type="text/javascript"
        src="{{ asset('mixassets/js/affilate/create.min.js') . '?lm=' . filemtime('mixassets/js/affilate/create.min.js') }}">
    </script>
@endsection
