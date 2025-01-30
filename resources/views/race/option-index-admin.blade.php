@extends('admin_index')

@section('title')
    مسابقات
@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{asset('admin_c/plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin_c/plugins/table/datatable/dt-global_style.css')}}">
@endsection


@section('content')

    <div class="row justify-content-center">

        <span class="badge badge-secondary my-4">گزینه های مسابقه {{$race->title}}</span>


        <div class="col-12 text-center">
            <a class="btn btn-primary" href="" data-toggle="modal"
               data-target="#create">ایجاد گزینه</a>
        </div>

        <div class="modal fade" id="create" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">ایجاد گزینه جدید</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('race.option.store')}}" method="POST" role="form"
                              enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" value="{{$race->id}}" name="race_id">

                            <div class="form-group">
                                <label for="title">عنوان گزینه</label>
                                <input type="text" class="form-control" name="title" id="title"
                                       placeholder="عنوان گزینه را وارد کنید">
                            </div>

                            <button type="submit" class="btn btn-primary">ثبت</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-10">

            <div class="table-responsive mb-4 mt-4">
                <table id="zero-config" class="table table-hover" style="width:100%">
                    <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>گزینه</th>
                        <th>عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($options as $key => $option)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$option->title}}</td>
                            <td>
                                <a class="btn btn-danger" href="" data-toggle="modal"
                                   data-target="#delete-{{$option->id}}">حذف</a>
                                <a class="btn btn-warning" href="" data-toggle="modal"
                                   data-target="#edit-{{$option->id}}">ویرایش</a>
                            </td>
                        </tr>

                        <div class="modal fade" id="edit-{{$option->id}}" tabindex="-1" role="dialog"
                             aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">ویرایش گزینه</h5>
                                        <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{route('race.option.update',$option->id)}}"
                                              method="POST" role="form"
                                              enctype="multipart/form-data">
                                            {{method_field('put')}}
                                            @csrf

                                            <div class="form-group">
                                                <label for="title">عنوان گزینه</label>
                                                <input type="text" class="form-control" name="title"
                                                       id="title"
                                                       placeholder="عنوان گزینه را وارد کنید"
                                                       value="{{$option->title}}">
                                            </div>

                                            <button type="submit" class="btn btn-primary">ویرایش</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="modal fade" id="delete-{{$option->id}}" tabindex="-1" role="dialog"
                             aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">حذف گزینه</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>آیا از حذف گزینه اطمینان دارید؟</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">خیر
                                        </button>
                                        <a href="{{route('race.option.destroy',$option->id)}}" class="btn btn-danger">بله</a>
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
