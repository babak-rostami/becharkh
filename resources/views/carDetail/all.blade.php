@extends('admin_index')

@section('title')

@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{asset('admin_c/plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin_c/plugins/table/datatable/dt-global_style.css')}}">
@endsection


@section('content')

    <div class="row justify-content-center">
        <div class="col-10 text-center">
            <a class="btn btn-primary" href="{{route('car.detail.create',$model->id)}}">ایجاد</a>
        </div>


        <div class="col-10 text-center">

            <div class="table-responsive mb-4 mt-4">
                <table id="zero-config" class="table table-hover" style="width:100%">
                    <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>تصویر</th>
                        <th>برند</th>
                        <th>مدل</th>
                        <th>سال ساخت</th>
                        <th>تعداد سیلندر</th>
                        <th>بازدید</th>
                        <th>عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($details as $key=>$detail)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>
                                @if(isset($detail->image))
                                    <img src="{{asset('files/carmodel/images/'.$detail->image)}}"
                                         style="width: 50px;height: 50px">
                                @else
                                    <span class="badge badge-warning">انتخاب نشده</span>
                                @endif
                            </td>
                            <td>{{$detail->brand->title}}</td>
                            <td>{{$detail->model->title}}</td>
                            <td>{{$detail->production_year}}</td>
                            <td>{{$detail->cylinder}}</td>
                            <td>{{$detail->seen_count}}</td>
                            <td>
                                <a class="btn btn-secondary"
                                   href="{{route('car.detail.edit',['model_id'=>$model->id ,'detail_id'=>$detail->id])}}">ویرایش</a>
                                <a class="btn btn-info" data-toggle="modal" data-target="#priority-{{$detail->id}}">اولویت</a>
                                <a class="btn btn-danger" data-toggle="modal"
                                   data-target="#delete-{{$detail->id}}">حذف</a>
                            </td>
                        </tr>

                        <!--priority Modal -->
                        <div class="modal fade" id="priority-{{$detail->id}}" tabindex="-1" role="dialog"
                             aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">اولویت در لیستی که در صفحه اول
                                            نمایش می دهد از 1 تا 15</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{route('car.detail.priority')}}" method="post" role="form">
                                            @csrf
                                            <input type="hidden" name="detail_id" value="{{$detail->id}}">

                                            <div class="form-group">
                                                <label for="priority"></label>
                                                <input type="number" class="form-control" name="priority" id="priority" value="{{$detail->priority}}">
                                            </div>

                                            <button type="submit" class="btn btn-primary">ثبت</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Modal -->
                        <div class="modal fade" id="delete-{{$detail->id}}" tabindex="-1" role="dialog"
                             aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">حذف مشخصات</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        آیا از حذف این مشخصات اطمینان دارید
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">خیر
                                        </button>
                                        <a href="{{route('car.detail.delete',$detail->id)}}"
                                           class="btn btn-danger">بله</a>
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
