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
            <a class="btn btn-primary" href="" data-toggle="modal" data-target="#exampleModal">ایجاد مدل</a>
            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">ایجاد مدل</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{route('model.store')}}" method="post" role="form">
                                @csrf

                                <div class="form-group">
                                    <label for="title">نام مدل</label>
                                    <input type="text" class="form-control" name="title" id="title">
                                </div>

                                <div class="form-group">
                                    <label for="brand_id">برند مرتبط</label>
                                    <select class="form-control" name="brand_id" id="brand_id">
                                        @foreach($brands as $brand)
                                            <option
                                                {{$br->id == $brand->id ? "selected":""}} value="{{$brand->id}}">{{$brand->title}}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <button type="submit" class="btn btn-primary">ثبت</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-10 text-center">

            <div class="table-responsive mb-4 mt-4">
                <table id="zero-config" class="table table-hover" style="width:100%">
                    <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>نام مدل</th>
                        <th>slug</th>
                        <th>برند</th>
                        <th>عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($models as $key => $model)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$model->title}}</td>
                            <td>{{$model->slug}}</td>
                            <td>{{$model->brand->title}}</td>
                            <td>
                                @if(auth('admin')->user()->type == 1)
                                    <a class="btn btn-warning" data-toggle="modal" data-target="#edit-{{$model->id}}">ویرایش
                                    </a>
                                @endif
                                <a class="btn btn-secondary"
                                   href="{{route('car.detail.all',$model->id)}}">مشخصات</a>
                            </td>
                        </tr>


                        <div class="modal fade" id="edit-{{$model->id}}" tabindex="-1" role="dialog"
                             aria-labelledby="exampleModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">ویرایش مدل</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{route('model.update',$model->id)}}" method="post" role="form"
                                              enctype="multipart/form-data">
                                            @csrf
                                            {{method_field('PUT')}}

                                            <div class="form-group">
                                                <label for="title">نام مدل</label>
                                                <input type="text" class="form-control" name="title" id="title"
                                                       value="{{$model->title}}">
                                            </div>
                                            <div class="form-group">
                                                <label for="slug">اسلاگ</label>
                                                <input type="text" class="form-control" name="slug" id="slug"
                                                       value="{{$model->slug}}">
                                            </div>

                                            <div class="form-group">
                                                <label for="brand_id">برند مرتبط</label>
                                                <select class="form-control" name="brand_id" id="brand_id">
                                                    @foreach($brands as $brand)
                                                        <option
                                                            {{$brand->id == $model->brand_id ? "selected":""}} value="{{$brand->id}}">{{$brand->title}}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label for="image">انتخاب تصویر</label>
                                                <input type="file" class="form-control" name="image" id="image">
                                            </div>

                                            <button type="submit" class="btn btn-primary">ویرایش</button>
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
