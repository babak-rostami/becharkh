@extends('index')

@section('title')
    درخواست های تغییر نام کاربری
@endsection

@section('style')
@endsection

@section('content')
    <div class="row bg-wht justify-content-center">

        <div class="col-12">
            @if (session('success'))
                <p class="alert alert-success text-center">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <p class="alert alert-danger text-center">{{ $error }}</p>
                @endforeach
            @endif
        </div>

        <div class="col-12 text-center">
            <table class="table table-hover my-5">
                <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>کاربر</th>
                        <th>نام کاربری جدید</th>
                        <th>توضیحات</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reqs as $key => $req)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <a href="{{ route('user.dashboard', $req->user->username) }}">{{ $req->user->username }}</a>
                            </td>
                            <td> {{ $req->username }}</td>
                            <td>{{ $req->body }}</td>
                            <td>
                                <a class="btn btn-danger" data-toggle="modal" data-dismiss="modal"
                                    data-target="#delete-{{ $req->id }}" href="">حذف</a>
                            </td>
                        </tr>

                        <div class="modal fade" id="delete-{{ $req->id }}" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <form action="{{ route('admin.destroy.chun.reqs') }}" method="post">
                                            @csrf
                                            {{ method_field('DELETE') }}
                                            <p>میخواهید درخواست حذف شود؟</p>
                                            <input type="hidden" name="req_id" value="{{ $req->id }}">
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
