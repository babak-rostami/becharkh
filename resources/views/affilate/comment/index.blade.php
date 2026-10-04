@extends('index')

@section('title')
    مدیریت نظرات
@endsection

@section('style')
@endsection


@section('content')
    <div class="row justify-content-center">

        <div class="col-12 text-center pt-4">

            {{ $comments->links() }}


            <a class="btn btn-primary" href="{{ route('admin.affilate.comment.create', $product_id) }}">نظر جدید</a>

            <table class="table table-hover my-5">
                <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>نظر</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($comments as $key => $comment)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <span>{{ Str::limit($comment->body, 100, '...') }}</span>
                            </td>
                            <td>
                                <a class="btn btn-warning"
                                    href="{{ route('admin.affilate.comment.edit', $comment->id) }}">ویرایش</a>
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
                                        <form action="{{ route('admin.affilate.comment.delete') }}" method="post">
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
                                        <form action="{{ route('admin.affilate.comment.store') }}" method="post">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product_id }}">
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
@endsection



@section('script')
@endsection
