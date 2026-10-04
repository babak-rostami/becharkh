@extends('index')

@section('title')
    آگهی ها
@endsection

@section('style')
@endsection


@section('content')
    <div class="row justify-content-center">


        @if (isset($category))
            <div class="col-10 text-center">
                <h4>آگهی های {{ $category->title }}</h4>
                <a class="btn btn-primary" href="{{ route('admin.advertise.all') }}">همه آگهی ها</a>
                <hr>
            </div>
        @endif


        <div class="col-10 text-center">

            <div class="table-responsive mb-4 mt-2">
                <table id="zero-config" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>تصویر</th>
                            <th>عنوان آگهی</th>
                            <th>انتشار دهنده</th>
                            <th>دسته بندی</th>
                            <th>زمان ثبت</th>
                            <th>تعداد بازدید</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($advertises as $key => $advertise)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    <img style="width: 100px; height: 60px" src="{{ asset($advertise->image()) }}">
                                </td>
                                <td>{{ $advertise->title }}</td>
                                <td>{{ $advertise->user->name }}</td>
                                <td>
                                    <a target="_blank" href="{{ route('site.category.admin', $advertise->category_id) }}">
                                        {{ $advertise->category->title }}
                                    </a>
                                </td>
                                <td>{{ jdate($advertise->created_at)->ago() }}</td>
                                <td>{{ $advertise->seen_count }}</td>
                                @if ($advertise->status == 1)
                                    <td><span class="text-success">تایید شده</span></td>
                                @else
                                    <td><span class="text-danger">تایید نشده</span></td>
                                @endif
                                <td>
                                    <a class="btn btn-primary" target="_blank"
                                        href="{{ route('ad.show', $advertise->slug) }}">مشاهده</a>
                                    <a class="btn btn-warning" href="{{ route('ad.edit.admin', $advertise->id) }}">ویرایش
                                    </a>
                                    <a class="btn btn-sm btn-outline-danger mt-2" data-toggle="modal"
                                        data-target="#delete-ad-modal-{{ $advertise->id }}" href="">
                                        حذف
                                    </a>
                                </td>
                            </tr>
                            <div class="modal fade" id="delete-ad-modal-{{ $advertise->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-12 text-center">

                                                    <h4>آگهی حذف شود؟</h4>
                                                    <b>مطمئنید میخواهید آگهی حذف شود؟</b>
                                                    <hr>
                                                    <a class="btn btn-warning w-50" href=""
                                                        data-dismiss="modal">بعدا</a>
                                                    <a class="btn btn-danger"
                                                        href="{{ route('destroy.ad', $advertise->id) }}">بله</a>
                                                </div>
                                            </div>
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
@endsection
