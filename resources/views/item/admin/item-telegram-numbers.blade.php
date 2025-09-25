@extends('index')

@section('title')
    شماره های تلگرام
@endsection

@section('style')
    <link href="{{ asset('mixassets/css/style.min.css') . '?lm=' . filemtime('mixassets/css/style.min.css') }}"
        rel="stylesheet" type="text/css" />
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
                        <th>آیتم</th>
                        <th>شماره</th>
                        <th>تعداد</th>
                        <th>زمان</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($itel_numbers as $key => $itnumber)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td> {{ $itnumber->item->full_title }}</td>
                            <td> {{ $itnumber->phone }}</td>
                            <td> {{ $itnumber->count ?? 0 }}</td>
                            <td>{{ jdate($itnumber->created_at)->ago() }}</td>
                            <td>
                                <a class="btn btn-danger"
                                    href="{{ route('itel.numbers.destroy.admin', $itnumber->id) }}">حذف</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript"
        src="{{ asset('mixassets/js/main.min.js') . '?lm=' . filemtime('mixassets/js/main.min.js') }}"></script>
@endsection
