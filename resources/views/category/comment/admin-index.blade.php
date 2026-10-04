@extends('admin_index')

@section('title')
    مدیریت نظرات دسته بندی ها
@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin_c/plugins/table/datatable/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin_c/plugins/table/datatable/dt-global_style.css') }}">
@endsection


@section('content')
    <div class="row justify-content-center">

        <div class="col-10 text-center">

            {{ $comments->links() }}


            <a class="btn btn-danger" href="{{ route('admin.category.comment.create') }}">نظر جدید</a>

            <div class="table-responsive mb-4 mt-4">
                <table id="zero-config" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>تایید شده</th>
                            <th>نظر</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($comments as $key => $comment)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                @if (isset($comment->status) && $comment->status == 0)
                                    <td>
                                        <span class="badge badge-danger">خیر</span>
                                    </td>
                                @else
                                    <td>
                                        <span class="badge badge-success">بله</span>
                                    </td>
                                @endif
                                <td>
                                    <span>{{ Str::limit($comment->body, 100, '...') }}</span>
                                    <br>
                                    @if (isset($comment->category_id))
                                        <a class="badge badge-light" rel="nofollow"
                                            href="{{ route('question.index', $comment->category->slug) }}?s=1">
                                            {{ $comment->category->title }}</a>
                                    @endif
                                    @if (isset($comment->question_id))
                                        @if (isset($comment->parent_id))
                                            <span class="badge badge-dark">ریپلای سوال</span>
                                        @endif
                                        <a class="badge badge-light" rel="nofollow"
                                            href="{{ route('question.show', $comment->question->slug2) }}">
                                            {{ $comment->question->title }}</a>
                                    @endif
                                    @foreach ($comment->getItems() as $i)
                                        <a class="badge badge-light" rel="nofollow"
                                            href="{{ $i->withParentsCommentUrl() }}">{{ $i->full_title ?? $i->title }}</a>
                                    @endforeach
                                </td>
                                <td>
                                    <a class="btn btn-warning"
                                        href="{{ route('admin.category.comment.edit', $comment->id) }}">ویرایش</a>
                                    <a class="btn btn-dark" href="{{ route('comment.item.tags.admin', $comment->id) }}">تگ
                                        ها</a>
                                    <a class="btn btn-primary" data-toggle="modal" data-dismiss="modal"
                                        data-target="#replyto-{{ $comment->id }}" href="">ریپلای</a>
                                    <a class="btn btn-danger" data-toggle="modal" data-dismiss="modal"
                                        data-target="#delete-{{ $comment->id }}" href="">حذف</a>
                                </td>
                            </tr>
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
                                                    <input type="hidden" name="parent_id"
                                                        value="{{ $comment->parent_id }}">
                                                    <input type="hidden" name="reply_id" value="{{ $comment->id }}">
                                                @else
                                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                @endif
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label>نام فیک</label>
                                                            <input type="text" class="form-control" name="name"
                                                                id="name" value="{{ old('name') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label>نام کاربری فیک</label>
                                                            <input type="text" class="form-control" name="username"
                                                                id="username" value="{{ old('username') }}">
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
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>


    </div>
@endsection



@section('script')
    <script src="{{ asset('admin_c/plugins/table/datatable/datatables.js') }}"></script>

    <script>
        $('#zero-config').DataTable({
            "oLanguage": {
                "oPaginate": {
                    "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>',
                    "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>'
                },
                "sInfo": "صفحه _PAGE_ از _PAGES_",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "جستجو کنید...",
                "sLengthMenu": "نتایج :  _MENU_",
            },
            "stripeClasses": [],
            "lengthMenu": [7, 10, 20, 50],
            "pageLength": 7
        });
    </script>
@endsection
