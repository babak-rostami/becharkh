@extends('index')


@section('title')
    پیام های {{ auth('user')->user()->name }}
@endsection

@section('style')
    <meta name="robots" content="noindex">
@endsection

@section('content')

    <div class="row justify-content-center my-2">
        <div class="col-12 col-md-10 text-center bg-wht radius-10" style="min-height: 450px">
            <ol class="breadcrumb bg-wht">
                <li class="breadcrumb-item"><a class="decor-none" href="{{ route('home') }}">بچرخ</a></li>
                <li class="breadcrumb-item"><a class="decor-none" href="{{ route('user.dashboard.edit') }}">مدیریت
                        حساب</a>
                </li>
                <li class="breadcrumb-item">اعلانات</li>

            </ol>
            <table class="table table-striped mb-5">
                @if ($notifications->count() > 0)
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">پیام</th>
                            <th scope="col">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notifications as $key => $notification)
                            <tr>
                                <th scope="row">{{ $key + 1 }}</th>
                                <td><a href="{{ $notification->data['route'] }}">{{ $notification->data['action'] }}</a>
                                </td>
                                <td>
                                    <a data-toggle="modal" data-target="#delete-{{ $notification->id }}"><img
                                            src="{{ asset('files/other/images/remove.png') }}"></a>
                                </td>
                            </tr>

                            <!-- delete notification Modal -->
                            <div class="modal fade" id="delete-{{ $notification->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">حذف پیام</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            آیا از حذف پیام اطمینان دارید؟
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">خیر
                                            </button>
                                            <a href="{{ route('notification.destroy', $notification->id) }}"
                                                class="btn btn-danger">بله</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                @else
                    <p class="bg-wht p-4" style="border: 2px solid #e3e3e3">اعلانی برای نمایش وجود ندارد</p>
                @endif
            </table>
        </div>
    </div>
@endsection
