@extends('admin_index')


@section('title')
    مدیریت مشاغل
@endsection


@section('style')
@endsection


@section('content')
    <div class="row justify-content-center">

        <div class="col-10 text-center">

            <a class="btn btn-primary" href="" data-toggle="modal" data-target="#create">شغل جدید</a>

            <div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('job.store.admin') }}" enctype="multipart/form-data" method="post"
                                role="form">
                                @csrf

                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label for="title">نام شغل</label>
                                            <input type="text" class="form-control" name="title" id="title"
                                                value="{{ old('title') }}">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label for="title_en">نام انگلیسی شغل</label>
                                            <input type="text" class="form-control" name="title_en" id="title_en"
                                                value="{{ old('title_en') }}">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label for="slug">اسلاگ</label>
                                            <input type="text" class="form-control" name="slug" id="slug"
                                                value="{{ old('slug') }}">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label for="image">تصویر</label>
                                            <input type="file" class="form-control" name="image" id="image">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="body">توضیحات</label>
                                            <textarea class="form-control" name="body" id="body"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="similar_search">کلمات مشابه</label>
                                            <textarea class="form-control" name="similar_search" id="similar_search"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary mt-2">ایجاد شود</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive mb-4 mt-4">
                <table id="zero-config" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>آیکون</th>
                            <th>نام شغل</th>
                            <th>نام انگلیسی شغل</th>
                            <th>اسلاگ</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jobs as $key => $job)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    <img src="{{ asset($job->thumb()) }}">
                                </td>
                                <td>{{ $job->title }}</td>
                                <td>{{ $job->title_en }}</td>
                                <td>{{ $job->slug }}</td>
                                <td>
                                    <a class="btn btn-warning" href="" data-toggle="modal"
                                        data-target="#edit-{{ $job->id }}">ویرایش</a>
                                </td>
                            </tr>

                            <div class="modal fade" id="edit-{{ $job->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('job.update.admin', $job->id) }}"
                                                enctype="multipart/form-data" method="post" role="form">
                                                @csrf

                                                {{ method_field('PUT') }}

                                                <div class="row">
                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="title">نام شغل</label>
                                                            <input type="text" class="form-control" name="title"
                                                                id="title" value="{{ $job->title }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="title_en">نام انگلیسی شغل</label>
                                                            <input type="text" class="form-control" name="title_en"
                                                                id="title_en" value="{{ $job->title_en }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="image">تصویر</label>
                                                            <input type="file" class="form-control" name="image"
                                                                id="image">
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="body">توضیحات</label>
                                                            <textarea class="form-control" name="body" id="body">{{ $job->body }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="similar_search">کلمات مشابه</label>
                                                            <textarea class="form-control" name="similar_search" id="similar_search">{{ $job->similar_search }}</textarea>
                                                        </div>
                                                    </div>

                                                </div>

                                                <button type="submit" class="btn btn-primary mt-2">ثبت تغییرات</button>
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
