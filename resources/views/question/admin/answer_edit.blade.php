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

            @if (isset($answer->parent_id) || isset($answer->reply_id))
                <form action="{{ route('admin.question.answer.update', $answer->id) }}" class="shadow-sm" id="cm_form"
                    method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    {{ method_field('PUT') }}

                    <textarea required class="form-control comment-input" style="min-height: 200px" name="body"
                        placeholder="نظر خود را اینجا بنویسید...">{{ $answer->body }}</textarea>

                    <span>{{ isset($answer->reply_id) ? $answer->parentReply->body : $answer->parent->body }}</span>

                    <input type="submit" class="btn btn-outline-primary bg-wht w-100 my-2" value="ارسال نظر">
                </form>
            @else
                @include('mainPart.comment-box', ['page' => 'admin_edit_qanswer'])
            @endif


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
