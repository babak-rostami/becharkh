@extends('index')

@section('title')
    ویرایش نظر
@endsection

@section('style')
    <link
        href="{{ asset('mixassets/css/category/comment-edit.min.css') . '?lm=' . filemtime('mixassets/css/category/comment-edit.min.css') }}"
        rel="stylesheet" type="text/css" />

    <script src="{{ asset('library/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('library/ckeditor/ckfinder.js') }}"></script>
    <script src="{{ asset('library/ckeditor/de.js') }}"></script>

    <meta name="robots" content="noindex">
@endsection

@section('content')
    <div class="row justify-content-center p-2">

        <div class="col-12 text-center my-4">
            @if (!isset($comment->parent_id))
                <a class="btn btn-dark" href="{{ route('comment.item.tags.admin', $comment->id) }}">تگ
                    ها</a>
            @endif
            <a class="btn btn-primary" data-toggle="modal" data-dismiss="modal" data-target="#replyto-{{ $comment->id }}"
                href="">ریپلای</a>
            <a class="btn btn-danger" data-toggle="modal" data-dismiss="modal" data-target="#delete-{{ $comment->id }}"
                href="">حذف</a>

            <div class="modal fade" id="delete-{{ $comment->id }}" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <form action="{{ route('admin.category.comment.delete') }}" method="post">
                                @csrf
                                {{ method_field('DELETE') }}
                                <input type="hidden" name="comment_id" value="{{ $comment->id }}">
                                <p>میخواهید نظر حذف شود؟</p>
                                <input type="submit" class="btn btn-danger" value="حذف">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="replyto-{{ $comment->id }}" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <form action="{{ route('admin.category.comment.store') }}" method="post">
                                @csrf
                                @if (isset($comment->parent_id))
                                    <input type="hidden" name="parent_id" value="{{ $comment->parent_id }}">
                                    <input type="hidden" name="reply_id" value="{{ $comment->id }}">
                                @else
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                @endif
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>نام فیک</label>
                                            <input type="text" class="form-control" name="name" id="name"
                                                value="{{ old('name') }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>نام کاربری فیک</label>
                                            <input type="text" class="form-control" name="username" id="username"
                                                value="{{ old('username') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">نظر:</label>
                                    <textarea style="height: 150px;" name="body" class="form-control"></textarea>
                                </div>
                                <input type="submit" class="btn btn-success">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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

        </div>

    </div>
@endsection

@section('script')
    <script>
        const submit_form_id = "cm_form";

        let csrf_t = "{{ csrf_token() }}";

        const page = 'admin_edit_comment';

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
        var cat_selected = "{{ $category->id ?? null }}";
        var citems = @json($citems);
        var cfeatures = @json($cfeatures);
        var feature_items = @json($commentFeatueItems);
        //end for select features modal

        let editor_img_upload_route =
            "{{ route('comment.editor.img.uplaod', ['_token' => csrf_token(), 'page' => 'admin_edit_comment']) }}";
    </script>
    <script type="text/javascript"
        src="{{ asset('mixassets/js/category/comment-edit.min.js') . '?lm=' . filemtime('mixassets/js/category/comment-edit.min.js') }}">
    </script>
    <script>
        sasf_questions = {!! json_encode(isset($questionIds) ? explode(',', $questionIds) : []) !!};
    </script>
@endsection
