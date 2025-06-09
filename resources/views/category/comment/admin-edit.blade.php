@extends('index')

@section('title')
    ویرایش نظر
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

            @if (isset($comment->parent_id))
                <form action="{{ route('admin.category.comment.update', $comment->id) }}" class="shadow-sm" id="cm_form"
                    method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    {{ method_field('PUT') }}
                    <input type="hidden" name="category_id" id="category_id" value="{{ $comment->category_id }}">
                    @if ($parent_url)
                        <a target="_blank" href="{{ $parent_url }}">{{ $parent->body }}</a>
                    @else
                        <span>{{ $parent->body }}</span>
                    @endif
                    <textarea required class="form-control comment-input" style="min-height: 200px" id="cm-input" name="body"
                        placeholder="نظر خود را اینجا بنویسید...">{{ $comment->editor ?? $comment->body }}</textarea>
                    <input type="submit" class="btn btn-outline-primary bg-wht w-100 my-2" value="ارسال نظر">
                </form>
            @else
                @include('mainPart.comment-box', ['page' => 'admin_edit_comment'])
            @endif

            {{-- <form id="cm_form" action="{{ route('admin.category.comment.update', $comment->id) }}" method="POST"
                role="form">
                @csrf
                {{ method_field('PUT') }}

                <input type="hidden" name="category_id" id="category_id" value="{{ $comment->category_id }}">

                <div class="form-group">
                    <label>نظر</label>
                    <textarea class="form-control" style="height: 150px" id="body" name="body">{{ $comment->body }}</textarea>
                </div>

                @if ($parent_id == null)
                    @include('modals.create.select-category', ['categories' => $categories])

                    <div class="row">
                        <div class="col-12 mb-4 text-right" id="features-box">
                        </div>
                    </div>
                @else
                    <span>{{ $parent->body }}</span>
                @endif

                <button type="submit" class="btn btn-success w-100">ثبت
                    نظر</button>
            </form> --}}
        </div>

    </div>
@endsection

@section('script')
    <script>
        const submit_form_id = "cm_form";

        const page = 'admin_edit_comment';

        //for select category modal
        const categories = @json($categories);
        var cat_children = [];
        const next_cat_img = "{{ asset('files/other/images/next.png') }}";
        const back_cat_img = "{{ asset('files/other/images/back.png') }}";
        const all_cat_img = "{{ asset('files/other/images/all-cat.webp') }}";
        const get_cat_fis_route = "{{ route('api.get.cat.fis') }}";
        //end for select category modal
        //for select features modal
        const remove_item_img = "{{ asset('files/other/images/g-close.webp') }}";
        const add_new_item_img = "{{ asset('files/other/images/b-add.png') }}";
        var cat_selected = "{{ $category->id ?? null }}";
        var citems = @json($citems);
        var cfeatures = @json($cfeatures);
        var feature_items = @json($commentFeatueItems);
        //end for select features modal

        var editor_img_upload_route =
            "{{ route('comment.editor.img.uplaod', ['_token' => csrf_token(), 'page' => 'admin_edit_comment']) }}";
    </script>
    <script type="text/javascript"
        src="{{ asset('mixassets/js/category/comment-create.min.js') . '?lm=' . filemtime('mixassets/js/category/comment-create.min.js') }}">
    </script>
    <script>
        sasf_questions = {!! json_encode(isset($questionIds) ? explode(',', $questionIds) : []) !!};
    </script>
@endsection
