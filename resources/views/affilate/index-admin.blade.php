@extends('index')

@section('title')
    مدیریت افیلیت ها
@endsection

@section('style')
@endsection

@section('content')
    <div class="row justify-content-center">

        <div class="col-12 text-center pt-4">

            {{ $affilates->links() }}

            <a class="btn btn-danger" href="{{ route('affilate.create.admin') }}">افیلیت
                جدید</a>
            <a class="btn btn-primary" href="{{ route('affilate.plinks.admin') }}">لینک عمومی</a>

            <table class="table table-hover my-5">
                <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>just this page?</th>
                        <th>seen</th>
                        <th>عنوان</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($affilates as $key => $affilate)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <span>{{ $affilate->just_this_page == 1 ? 1 : 0 }}</span>
                            </td>
                            <td>
                                <span>{{ $affilate->seen_count ?? 0 }}</span>
                            </td>
                            <td>
                                <span>{{ Str::limit($affilate->title, 50, '...') }}</span>
                            </td>
                            <td>
                                <a class="btn btn-warning" href="{{ route('affilate.edit.admin', $affilate->id) }}">ویرایش</a>
                                <a target="_blank" class="btn btn-light"
                                    href="{{ route('product.show', $affilate->slug) }}">مشاهده</a>
                                <a class="btn btn-danger" data-toggle="modal" data-dismiss="modal"
                                    data-target="#delete-{{ $affilate->id }}" href="">حذف</a>
                                <a class="btn btn-primary"
                                    href="{{ route('affilate.comments.admin', $affilate->id) }}">نظرات</a>
                            </td>
                        </tr>

                        <div class="modal fade" id="delete-{{ $affilate->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <form action="{{ route('affilate.destroy.admin', $affilate->id) }}" method="post">
                                            @csrf
                                            {{ method_field('DELETE') }}
                                            <p>میخواهید افیلیت حذف شود؟</p>
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
