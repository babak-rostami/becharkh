@extends('index')

@section('title')
    سوال جدید
@endsection

@section('style')
    <link href="{{ asset('mixassets/css/forum/create.min.css') . '?lm=' . filemtime('mixassets/css/forum/create.min.css') }}"
        rel="stylesheet" type="text/css" />

    {{-- <script src="{{ asset('library/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('library/ckeditor/ckfinder.js') }}"></script>
    <script src="{{ asset('library/ckeditor/de.js') }}"></script> --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/34.1.0/classic/ckeditor.js"></script>
    <script src="https://ckeditor.com/apps/ckfinder/3.5.0/ckfinder.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/34.1.0/classic/translations/de.js"></script>

    <meta name="robots" content="noindex, nofollow">
@endsection

@section('content')
    <div class="row justify-content-center p-2">

        <div class="col-12 col-md-10 text-right p-2 mb-5 p-sm-5 create-div">

            @include('mainPart.comment-box', ['page' => 'create_question'])

        </div>

    </div>
@endsection

@section('script')
    @if (!auth('user')->check())
        <script>
            send_question_after_login = 1;
        </script>
    @else
        <script>
            send_question_after_login = 0;
        </script>
    @endif
    <script>
        const submit_form_id = "qform";
        const page = 'create_question';

        //for select category modal
        const categories = @json($categories);
        var cat_children = [];
        const next_cat_img = "{{ asset('files/other/images/next.png') }}";
        const back_cat_img = "{{ asset('files/other/images/back.png') }}";
        const all_cat_img = "{{ asset('files/other/images/all-cat.webp') }}";
        const get_cat_fis_route = "{{ route('api.get.cat.fis') }}";
        //end for select category modal
        //for select features modal
        const remove_item_img = "{{ $ftp_path . 'files/other/images/g-close.webp' }}";
        const add_new_item_img = "{{ $ftp_path . 'files/other/images/b-add.png' }}";
        var cat_selected = null;
        var citems = null;
        var cfeatures = null;
        var feature_items = null;
        //end for select features modal

        const editor_img_upload_route =
            "{{ route('comment.editor.img.uplaod', ['_token' => csrf_token(), 'page' => 'create_question']) }}";
    </script>
    <script type="text/javascript"
        src="{{ asset('mixassets/js/forum/create.min.js') . '?lm=' . filemtime('mixassets/js/forum/create.min.js') }}">
    </script>
@endsection
