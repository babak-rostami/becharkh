@extends('admin_index')

@section('title')
    ویرایش اطلاعات
@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{asset('admin_c/plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin_c/plugins/table/datatable/dt-global_style.css')}}">
@endsection


@section('content')

    <div class="row justify-content-center">

        <div class="col-10">
            <div class="table-responsive mb-4 mt-4">
                <table id="zero-config" class="table table-hover" style="width:100%">
                    <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>ایمیل یا یوزرنیم کاربر</th>
                        <th>لینک صفحه</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($edits as $key => $edit)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{isset($edit->email)? $edit->email:$edit->user->username}}</td>
                            <td><a class="btn btn-outline-primary" target="_blank" href="{{$edit->url}}">مشاهده مطلب</a>
                            </td>
                            <td><span
                                    class="badge {{$edit->status==0?"badge-danger":"badge-success"}}">{{$edit->status == 0 ? "در دست بررسی":"تکمیل شده"}}</span>
                            </td>
                            <td>
                                @if(!$edit->status)
                                    <a class="btn btn-warning" href="" data-toggle="modal"
                                       data-target="#done-{{$edit->id}}">تغییر به وضعیت تکمیل شده</a>
                                @endif
                                <a class="btn btn-danger" href="" data-toggle="modal"
                                   data-target="#delete-{{$edit->id}}">حذف</a>
                                <a class="btn btn-primary" href="" data-toggle="modal"
                                   data-target="#show-{{$edit->id}}">مشاهده تغییرات درخواستی</a>
                            </td>
                        </tr>

                        <div class="modal fade" id="show-{{$edit->id}}" tabindex="-1" role="dialog"
                             aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">تغییرات درخواستی</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p style="white-space: pre-line">{{$edit->body}}</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">باشه
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="done-{{$edit->id}}" tabindex="-1" role="dialog"
                             aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">تکمیل شود؟</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{route('edit.informations.done')}}" method="post"
                                              role="form">
                                            @csrf
                                            {{method_field('PUT')}}

                                            <input type="hidden" name="id" value="{{$edit->id}}">

                                            <p>در صورت تغییر وضعیت به تکمیل شده به کاربر از طریق ایمیل اطلاع داده
                                                میشود</p>
                                            <p>آیا از تکمیل اطمینان دارید؟</p>

                                            <button type="submit" class="btn btn-danger">بله</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">انصراف
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="delete-{{$edit->id}}" tabindex="-1" role="dialog"
                             aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">حذف</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{route('edit.informations.delete')}}" method="post"
                                              role="form">
                                            @csrf
                                            {{method_field('DELETE')}}

                                            <input type="hidden" name="id" value="{{$edit->id}}">

                                            <p>آیا از حذف درخواست اطمینان دارید؟</p>

                                            <button type="submit" class="btn btn-danger">بله</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">انصراف
                                            </button>
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
    <script src="{{asset('admin_c/plugins/table/datatable/datatables.js')}}"></script>

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
