@extends('index')

@section('title')
    مدیریت تگ ها
@endsection

@section('style')
    <meta name="robots" content="noindex">
@endsection

@section('content')
    <div class="row bg-wht">
        <div class="col-12 text-center mb-5 mt-4">

            @if (session('success'))
                <p class="alert alert-success text-center my-1">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <p class="alert alert-danger my-1">{{ $error }}</p>
                @endforeach
            @endif

            <a class="btn btn-primary" href="" data-toggle="modal" data-dismiss="modal" data-target="#new_tag">
                ایجاد تگ جدید +
            </a>
            <div class="modal fade" id="new_tag" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <form action="{{ route('item.tag.store.admin') }}" enctype="multipart/form-data" method="post"
                                role="form">
                                @csrf

                                @if (isset($selected_tag))
                                    <input type="hidden" name="parent_id" value="{{ $selected_tag->id }}">
                                    <div class="alert alert-dark">تگ بالایی : {{ $selected_tag->title }}</div>
                                @endif
                                <div class="form-group">
                                    <label for="title">عنوان</label>
                                    <input type="text" class="form-control" name="title" id="title">
                                </div>
                                <div class="form-group">
                                    <label for="similar_search">سرچ های مشابه</label>
                                    <textarea class="form-control" name="similar_search" rows="5"></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary mt-2 w-100">ثبت تغییرات</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <table class="table table-hover my-5">
                <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>عنوان</th>
                        <th>اولویت</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tags as $key => $tag)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <span>{{ Str::limit($tag->title, 50, '...') }}</span>
                            </td>
                            <td>
                                <span>{{ $tag->priority }}</span>
                            </td>
                            <td>
                                @if (!isset($selected_tag))
                                    <a href="{{ route('item.tags.admin', $tag->id) }}" class="btn btn-dark">زیرمجموعه</a>
                                @endif
                                <a href="{{ route('item.tag.edit.admin', $tag->id) }}" class="btn btn-warning">ویرایش</a>
                                <a class="btn btn-danger" data-toggle="modal" data-dismiss="modal"
                                    data-target="#delete-{{ $tag->id }}" href="">حذف</a>
                            </td>
                        </tr>

                        <div class="modal fade" id="delete-{{ $tag->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <form action="{{ route('item.tag.destroy.admin', $tag->id) }}" method="post">
                                            @csrf
                                            {{ method_field('DELETE') }}
                                            <p>میخواهید تگ حذف شود؟</p>
                                            <input type="submit" class="btn btn-danger" value="حذف">
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
