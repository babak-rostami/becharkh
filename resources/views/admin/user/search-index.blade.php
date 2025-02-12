@extends('index')

@section('title')
    جستجوهای اخیر
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
                        <th>زمان</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($searches as $key => $search)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td> {{ $search->text }}</td>
                            <td>{{ jdate($search->created_at)->ago() }}</td>
                            <td>
                                <a class="btn btn-danger" href="{{ route('admin.destroy.user.search', $search->id) }}">حذف</a>
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
