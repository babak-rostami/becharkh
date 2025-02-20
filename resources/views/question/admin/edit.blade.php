@extends('index')

@section('title')
    ویرایش سوال
@endsection

@section('style')
    <script src="{{ $ftp_path . 'library/ckeditor/ckeditor.js' }}"></script>
    <script src="{{ $ftp_path . 'library/ckeditor/ckfinder.js' }}"></script>
    <script src="{{ $ftp_path . 'library/ckeditor/de.js' }}"></script>

    <link href="{{ asset('mixassets/css/forum/create.min.css') . '?lm=' . filemtime('mixassets/css/forum/create.min.css') }}"
        rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row justify-content-center p-2">

        <div class="col-12 col-md-10 text-right p-2 mb-5 p-sm-5 create-div">

            @include('mainPart.comment-box', ['page' => 'edit_question_admin'])

        </div>
    </div>
@endsection

@section('script')
    <script>
        const submit_form_id = "qform";
        const page = "edit_question_admin";

        //for select category modal
        const categories = @json($categories);
        var cat_children = [];
        const next_cat_img = "{{ $ftp_path . 'files/other/images/next.png' }}";
        const back_cat_img = "{{ $ftp_path . 'files/other/images/back.png' }}";
        const all_cat_img = "{{ $ftp_path . 'files/other/images/all-cat.webp' }}";
        const get_cat_fis_route = "{{ route('api.get.cat.fis') }}";
        //end for select category modal
        //for select features modal
        const remove_item_img = "{{ $ftp_path . 'files/other/images/g-close.webp' }}";
        const add_new_item_img = "{{ $ftp_path . 'files/other/images/b-add.png' }}";
        var cat_selected = "{{ $category->id }}";
        var citems = @json($citems);
        var cfeatures = @json($cfeatures);
        var feature_items = @json($questionFeatueItems);
        //end for select features modal
        const editor_img_upload_route =
            "{{ route('comment.editor.img.uplaod', ['_token' => csrf_token(), 'page' => 'edit_question_admin']) }}";
    </script>

    <script type="text/javascript"
        src="{{ asset('mixassets/js/forum/admin-edit.min.js') . '?lm=' . filemtime('mixassets/js/forum/admin-edit.min.js') }}">
    </script>

    <script>
        sasf_categories = {!! json_encode(isset($sasf_categoryIds) ? explode(',', $sasf_categoryIds) : []) !!};
        sasf_items = {!! json_encode(isset($sasf_itemIds) ? explode(',', $sasf_itemIds) : []) !!};
    </script>
@endsection
