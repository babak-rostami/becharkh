@extends('index')


@section('title')
    پست جدید
@endsection

@section('style')
    <script src="{{ $ftp_path . 'library/ckeditor/ckeditor.js' }}"></script>
    <script src="{{ $ftp_path . 'library/ckeditor/ckfinder.js' }}"></script>
    <script src="{{ $ftp_path . 'library/ckeditor/de.js' }}"></script>

    <meta name="robots" content="noindex, nofollow">

    <link
        href="{{ asset('mixassets/css/blog/create-edit.min.css') . '?lm=' . filemtime('mixassets/css/blog/create-edit.min.css') }}"
        rel="stylesheet" type="text/css" />
@endsection


@section('content')


    <div class="row bg-wht justify-content-center">
        <div class="col-12 col-md-10 text-right mt-4">

            @if (session('success'))
                <div>
                    <p class="alert alert-success text-center my-1">{{ session('success') }}</p>
                </div>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <p class="alert alert-danger my-1">{{ $error }}</p>
                @endforeach
            @endif

            @include('mainPart.comment-box', ['page' => 'create_blog'])

        </div>
    </div>

@endsection


@section('script')
    <script>
        const create_blog_csrf = "{{ csrf_token() }}";
        let post_id = "{{ isset($blog) ? $blog->id : 0 }}";
        const route_store_post_temprory = "{{ route('user.store.post.temprory') }}";


        const submit_form_id = "blogform";
        const page = 'create_blog';

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
        var cat_selected = null;
        var citems = null;
        var cfeatures = null;
        var feature_items = null;
        //end for select features modal

        const editor_img_upload_route =
            "{{ route('comment.editor.img.uplaod', ['_token' => csrf_token(), 'page' => 'create_blog']) }}";
    </script>

    <script type="text/javascript"
        src="{{ asset('mixassets/js/blog/create.min.js') . '?lm=' . filemtime('mixassets/js/blog/create.min.js') }}">
    </script>
@endsection
