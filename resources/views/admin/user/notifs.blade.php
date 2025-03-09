@extends('index')

@section('title')
    اعلان های کاربران
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
                        <th>متن</th>
                        <th>پیام</th>
                        <th>به کاربر</th>
                        <th>خوانده شده</th>
                        <th>زمان</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($notifs as $key => $notif)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td> {{ $notif->msg }}</td>
                            <td> {{ $notif->body }}</td>
                            <td> {{ $notif->user->username }}</td>
                            <td>
                                @if ($notif->unread)
                                    <span class="badge badge-warning">خوانده نشده</span>
                                @else
                                    <span class="badge badge-light">خوانده شده</span>
                                @endif
                            </td>
                            <td>{{ jdate($notif->created_at)->ago() }}</td>
                            <td><a class="btn btn-danger" href="{{ route('admin.user.notif.destroy', $notif->id) }}">حذف</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
@endsection
