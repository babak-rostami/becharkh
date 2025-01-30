@extends('index')

@section('title')
    ویرایش مقاله
@endsection

@section('style')
    {{-- <script src="{{ asset('library/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('library/ckeditor/ckfinder.js') }}"></script>
    <script src="{{ asset('library/ckeditor/de.js') }}"></script> --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/34.1.0/classic/ckeditor.js"></script>
    <script src="https://ckeditor.com/apps/ckfinder/3.5.0/ckfinder.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/34.1.0/classic/translations/de.js"></script>
    <link
        href="{{ asset('mixassets/css/blog/create-edit.min.css') . '?lm=' . filemtime('mixassets/css/blog/create-edit.min.css') }}"
        rel="stylesheet" type="text/css" />
@endsection

@section('content')

    <div class="row bg-wht justify-content-center align-items-center pt-4">
        <div class="col-12 col-md-10 text-right mt-4">

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

            @include('mainPart.comment-box', ['page' => 'edit_blog'])

        </div>
    </div>
@endsection


@section('script')
    <script>
        const submit_form_id = "blogform";
        const page = "edit_blog";

        //for select features modal
        const remove_item_img = "{{ $ftp_path . 'files/other/images/g-close.webp' }}";
        const add_new_item_img = "{{ $ftp_path . 'files/other/images/b-add.png' }}";
        var cat_selected = "{{ $category->id }}";
        var citems = @json($citems);
        var cfeatures = @json($cfeatures);
        var feature_items = @json($blogFeatueItems);
        //end for select features modal
        const editor_img_upload_route =
            "{{ route('comment.editor.img.uplaod', ['_token' => csrf_token(), 'page' => 'edit_blog']) }}";
    </script>

    <script type="text/javascript"
        src="{{ asset('mixassets/js/blog/edit.min.js') . '?lm=' . filemtime('mixassets/js/blog/edit.min.js') }}"></script>
@endsection
