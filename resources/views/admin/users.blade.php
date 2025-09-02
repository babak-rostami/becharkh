@extends('admin_index')

@section('title')
@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin_c/plugins/table/datatable/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin_c/plugins/table/datatable/dt-global_style.css') }}">
@endsection


@section('content')
    <div class="row justify-content-center">

        <div class="col-10 text-center">

            <a class="btn btn-dark" href="{{ route('admin.users') . '/like' }}">آخرین کسایی که لایکشون رو دیدن</a>

            {{ $users->links() }}

            <div class="table-responsive mb-4 mt-4">
                <table id="zero-config" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>پروفایل</th>
                            <th>نام کاربری</th>
                            <th>بیوگرافی</th>
                            @if ($type == 'like')
                                <th>لایک</th>
                            @endif
                            <th>ایمیل</th>
                            <th>وضعیت تایید ایمیل</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $key => $user)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    @if ($user->getImage() !== null)
                                        <img style="width: 64px;height: 64px;object-fit: contain"
                                            src="{{ $user->image() }}">
                                    @else
                                        ندارد
                                    @endif
                                </td>
                                <td>{{ $user->username }}</td>
                                <td>
                                    @if (isset($user->body))
                                        <span class="badge badge-success">نوشته</span>
                                    @else
                                        <span class="badge badge-danger">ندارد</span>
                                    @endif
                                </td>
                                @if ($type == 'like')
                                    <td>{{ $user->likes_count }}</td>
                                @endif
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if (!isset($user->email_actived))
                                        <span class="badge badge-warning">تایید نشده</span>
                                    @elseif(isset($user->email_actived) && $user->email_actived == 1)
                                        <span class="badge badge-success">تایید شده</span>
                                    @elseif(isset($user->email_actived) && $user->email_actived == 0)
                                        <span class="badge badge-danger">ایمیل اشتباه</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-primary" href="" data-toggle="modal"
                                        data-target="#edit-{{ $user->id }}">
                                        ویرایش</a>
                                </td>
                            </tr>
                            <div class="modal fade" id="edit-{{ $user->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <form action="{{ route('user.update.admin', $user->id) }}" method="POST"
                                                role="form" enctype="multipart/form-data">
                                                @csrf

                                                <input class="btn btn-success" type="submit" value="ثبت تغییرات">
                                                <br>
                                                <label>name</label>
                                                <input class="form-control" type="text" name="name"
                                                    value="{{ $user->name }}">
                                                <label>username</label>
                                                <input class="form-control" type="text" name="username"
                                                    value="{{ $user->username }}">
                                                <label>email</label>
                                                <input class="form-control" type="text" name="email"
                                                    value="{{ $user->email }}">
                                                <label>money</label>
                                                <input class="form-control" type="text" name="money"
                                                    value="{{ $user->money ?? '' }}">
                                                <label>email actived</label>
                                                <input class="form-control" type="text" name="email_actived"
                                                    value="{{ $user->email_actived ?? '' }}">
                                                <label>is fake?</label>
                                                <input class="form-control" type="text" name="is_fake"
                                                    value="{{ $user->is_fake ?? '' }}">
                                                <label>image</label>
                                                <input class="form-control" type="file" name="image">
                                                <label>body</label>
                                                <textarea class="form-control" name="body" cols="30" rows="3">{{ $user->body }}</textarea>

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

    </div>
@endsection



@section('script')
    <script src="{{ asset('admin_c/plugins/table/datatable/datatables.js') }}"></script>

    <script>
        $('#zero-config').DataTable({
            "oLanguage": {
                "oPaginate": {
                    "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>',
                    "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>'
                },
                "sInfo": "صفحه _PAGE_ از _PAGES_",
                "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                "sSearchPlaceholder": "جستجو کنید...",
                "sLengthMenu": "نتایج :  _MENU_",
            },
            "stripeClasses": [],
            "lengthMenu": [7, 10, 20, 50],
            "pageLength": 7
        });
    </script>
@endsection
