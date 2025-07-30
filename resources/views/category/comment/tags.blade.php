@extends('index')

@section('title')
    انتخاب تگ
@endsection

@section('style')
@endsection

@section('content')
    <div class="row bg-wht">
        <div class="col-12 text-center my-5">

            @if (session('success'))
                <p class="alert alert-success text-center">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <p class="alert alert-danger text-center">{{ $error }}</p>
                @endforeach
            @endif

            <p>{{ $comment->body }}</p>

            @if (!$tags->isEmpty())
                <form action="{{ route('comment.item.tag.store.admin') }}" method="post" role="form">
                    @csrf

                    <input type="hidden" name="comment_id" value="{{ $comment->id }}">
                    <select class="form-control" name="tag_id">
                        @foreach ($tags as $tag)
                            <option value="{{ $tag['id'] }}">{{ $tag['title'] }}</option>
                        @endforeach
                    </select>

                    <button type="submit" id="submit_btn" class="w-100 btn btn-primary mt-4">اضافه کردن</button>

                </form>
            @endif

            @if (!$com_tags->isEmpty())
                <table class="table table-hover my-5">
                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>عنوان</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($com_tags as $key => $ctag)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    <span>{{ $ctag->title }}</span>
                                </td>
                                <td>
                                    <a class="btn btn-danger" data-toggle="modal" data-dismiss="modal"
                                        data-target="#delete-{{ $ctag->id }}" href="">حذف</a>
                                </td>
                            </tr>

                            <div class="modal fade" id="delete-{{ $ctag->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <form action="{{ route('comment.item.tag.delete.admin') }}" method="post">
                                                @csrf
                                                {{ method_field('DELETE') }}
                                                <input type="hidden" name="tag_id" value="{{ $ctag->id }}">
                                                <input type="hidden" name="comment_id" value="{{ $comment->id }}">
                                                <p>میخواهید کامنت از این تگ خارج شود؟</p>
                                                <input type="submit" class="btn btn-danger" value="حذف">
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="alert alert-warning mt-4">هنوز تگی انتخاب نشده</p>
            @endif

        </div>

    </div>
@endsection

@section('script')
@endsection
