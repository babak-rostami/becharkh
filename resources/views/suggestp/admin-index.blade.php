@extends('index')

@section('title')
    مدیریت پیشنهاد ها
@endsection

@section('style')
@endsection

@section('content')
    <div class="row justify-content-center">

        <div class="col-12 text-center pt-4">

            {{ $suggests->links() }}

            <a class="btn btn-danger" href="{{ route('suggestp.create.admin') }}">پیشنهاد جدید</a>

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
                    @foreach ($suggests as $key => $suggest)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <span>{{ $suggest->just_this_page == 1 ? 1 : 0 }}</span>
                            </td>
                            <td>
                                <span>{{ $suggest->seen_count ?? 0 }}</span>
                            </td>
                            <td>
                                <span>{{ Str::limit($suggest->title, 50, '...') }}</span>
                            </td>
                            <td>
                                <a class="btn btn-warning" href="{{ route('suggestp.edit.admin', $suggest->id) }}">ویرایش</a>
                                <a target="_blank" class="btn btn-light" href="{{ $suggest->link }}">مشاهده</a>
                                <a class="btn btn-danger" data-toggle="modal" data-dismiss="modal"
                                    data-target="#delete-{{ $suggest->id }}" href="">حذف</a>
                            </td>
                        </tr>

                        <div class="modal fade" id="delete-{{ $suggest->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <form action="{{ route('suggestp.destroy.admin', $suggest->id) }}" method="post">
                                            @csrf
                                            {{ method_field('DELETE') }}
                                            <p>میخواهید پیشنهاد حذف شود؟</p>
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
