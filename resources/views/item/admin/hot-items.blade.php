@extends('index')

@section('title')
    آیتم های داغ
@endsection

@section('style')
    <script src="{{ asset('library/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('library/ckeditor/ckfinder.js') }}"></script>
    <script src="{{ asset('library/ckeditor/de.js') }}"></script>
@endsection

@section('content')
    <div class="row justify-content-center bg-wht">
        <div class="col-12 text-center mt-4">
            <table class="table">
                <thead class="thead-inverse">
                    <tr>
                        <th>#</th>
                        <th>آیتم</th>
                        <th>کاربر</th>
                        <th>تعداد طرفدار</th>
                        <th>زمان دنبال کردن</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($followItems as $key => $followItem)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td><a class="text-dark" target="_blank"
                                    href="{{ $followItem->item->withParentsCommentUrl() }}">{{ $followItem->item->full_title ?? $followItem->item->title }}</a>
                            </td>
                            <td><a class="text-dark" target="_blank"
                                    href="{{ route('user.dashboard', $followItem->user->username) }}">{{ $followItem->user->username }}</a>
                            </td>
                            <td>{{ $followItem->item->follow_count }}</td>
                            <td>{{ jdate($followItem->created_at)->ago() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection



@section('script')
@endsection
