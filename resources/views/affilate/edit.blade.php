@extends('index')

@section('title')
    ویرایش افیلیت
@endsection

@section('style')
    <link
        href="{{ asset('mixassets/css/affilate/edit.min.css') . '?lm=' . filemtime('mixassets/css/affilate/edit.min.css') }}"
        rel="stylesheet" type="text/css" />

    <script src="{{ asset('library/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('library/ckeditor/ckfinder.js') }}"></script>
    <script src="{{ asset('library/ckeditor/de.js') }}"></script>

    <meta name="robots" content="noindex">
@endsection

@section('content')
    <div class="row justify-content-center p-2">

        <div class="col-12 col-md-10 text-right p-2 mb-5 p-sm-5 create-div">

            @include('mainPart.comment-box', ['page' => 'edit_affilate'])

        </div>

    </div>
@endsection

@section('script')
    <script>
        const page = 'edit_affilate';
        const csrf_t = "{{ csrf_token() }}";
        const editor_img_upload_route =
            "{{ route('comment.editor.img.uplaod', ['_token' => csrf_token(), 'page' => 'edit_affilate']) }}";
    </script>
    <script type="text/javascript"
        src="{{ asset('mixassets/js/affilate/edit.min.js') . '?lm=' . filemtime('mixassets/js/affilate/edit.min.js') }}">
    </script>
    <script>
        sasf_categories = {!! json_encode(isset($categoryIds) ? explode(',', $categoryIds) : []) !!};
        sasf_items = {!! json_encode(isset($itemIds) ? explode(',', $itemIds) : []) !!};
        sasf_questions = {!! json_encode(isset($questionIds) ? explode(',', $questionIds) : []) !!};
        sasf_video = {!! json_encode(isset($videoId) ? explode(',', $videoId) : []) !!};
    </script>
@endsection
