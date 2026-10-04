@extends('index')

@section('title')
    نظر های {{ $question->title }}
@endsection

@section('style')
    <script src="{{ asset('library/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('library/ckeditor/ckfinder.js') }}"></script>
    <script src="{{ asset('library/ckeditor/de.js') }}"></script>

    <link
        href="{{ asset('mixassets/css/forum/answers-admin.min.css') . '?lm=' . filemtime('mixassets/css/forum/answers-admin.min.css') }}"
        rel="stylesheet" type="text/css" />
    <meta name="robots" content="noindex">
@endsection


@section('content')
    <div class="row justify-content-center">
        <div class="col-10 text-center mt-4">
            <h4>نظر های سوال {{ $question->title }}</h4>

            @include('mainPart.comment-box', ['page' => 'admin_qanswers'])

            <div class="row mb-4 mt-4 bg-wht radius-10 p-2">
                @foreach ($answers as $key => $answer)
                    <div class="col-12" style="border-bottom: 1px solid #ddd;padding: 12px;">
                        <p>{{ Str::limit($answer->body) }}</p>
                        <a class="btn btn-danger" href="" data-toggle="modal"
                            data-target="#delete-{{ $answer->id }}">حذف</a>
                        <a class="btn btn-primary" href="" data-toggle="modal"
                            data-target="#reply-{{ $answer->id }}">جواب</a>
                        <a class="btn btn-warning" href="{{ route('admin.category.comment.edit', $answer->id) }}">ویرایش</a>

                        <div class="modal fade" id="reply-{{ $answer->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">جواب</h5>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('admin.question.answer.store') }}" method="post"
                                            role="form">
                                            @csrf

                                            <input type="hidden" name="question_id" value="{{ $question->id }}">
                                            <input type="hidden" name="parent_id"
                                                value="{{ $answer->parent_id ?? $answer->id }}">
                                            <input type="hidden" name="reply_id"
                                                value="{{ isset($answer->parent_id) ? $answer->id : null }}">
                                            <div class="form-group">
                                                <label>نام</label>
                                                <input type="text" class="form-control" name="name">
                                            </div>
                                            <div class="form-group">
                                                <label>نام کاربری</label>
                                                <input type="text" class="form-control" name="username">
                                            </div>
                                            <textarea style="min-height: 150px" required class="form-control" name="body"
                                                placeholder="نظر خود را اینجا بنویسید..."></textarea>

                                            <button type="submit" class="btn btn-primary w-100">ارسال نظر</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="delete-{{ $answer->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">حذف نظر</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('admin.question.answer.destroy', $answer->id) }}"
                                            method="post" role="form">
                                            @csrf
                                            {{ method_field('DELETE') }}

                                            <p>مطمئنید که میخواهید نظر حذف شود؟</p>

                                            <button type="submit" class="btn btn-danger">بله</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">انصراف
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection



@section('script')
    <script>
        const submit_form_id = "cm_form";

        const page = 'admin_qanswers';

        const editor_img_upload_route =
            "{{ route('comment.editor.img.uplaod', ['_token' => csrf_token(), 'page' => 'admin_qanswers']) }}";

        const is_fuser_exist = "{{ route('admin.is.fuser.exist') }}";
    </script>
    <script type="text/javascript"
        src="{{ asset('mixassets/js/forum/answers-admin.min.js') . '?lm=' . filemtime('mixassets/js/forum/answers-admin.min.js') }}">
    </script>
@endsection
