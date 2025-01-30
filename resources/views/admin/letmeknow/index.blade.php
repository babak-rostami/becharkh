@extends('index')

@section('title')
    بهم خبر بده
@endsection

@section('style')
@endsection


@section('content')
    <div class="row mt-5">
        <div class="col-12">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>دسته</th>
                        <th>آیتم</th>
                        <th>آی پی</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lmks as $key => $lmk)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $lmk->category->full_title ?? $lmk->category->title }}</td>
                            <td>{{ $lmk->item->full_title ?? $lmk->item->title }}</td>
                            <td>{{ $lmk->ip }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection



@section('script')
@endsection
