@extends('index')

@section('title')
    ارور های سایت
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

        <div class="col-12 text-center overflow-auto">

            <a class="btn btn-danger mt-4" href="{{ route('admin.destroy.page.errors') }}">حذف همه</a>

            <table class="table table-hover my-5">
                <thead>
                    <tr>
                        <th>آدرس</th>
                        <th>آی پی</th>
                        <th>بات</th>
                        <th>متد</th>
                        <th>پیام</th>
                        <th>کد</th>
                        <th>زمان</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($site_errors as $key => $serror)
                        <tr>
                            <td> {{ $serror->url }}</td>
                            <td> {{ $serror->ip_address }}</td>
                            <td> {{ $serror->user_agent }}</td>
                            <td> {{ $serror->method }}</td>
                            <td> {{ $serror->message }}</td>
                            <td> {{ $serror->status_code }}</td>
                            <td>{{ jdate($serror->created_at)->ago() }}</td>
                            <td>
                                <a class="btn btn-danger" href="{{ route('admin.destroy.page.error', $serror->id) }}">حذف</a>
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
