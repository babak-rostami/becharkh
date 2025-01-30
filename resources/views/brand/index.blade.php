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
            <a class="btn btn-primary" href="" data-toggle="modal" data-target="#exampleModal">ایجاد برند</a>
            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">ایجاد برند</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{route('brand.store')}}" method="post" role="form">
                                @csrf

                                <div class="form-group">
                                    <label for="title">نام برند</label>
                                    <input type="text" class="form-control" name="title" id="title">
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
                        <th>نام برند</th>
                        <th>slug</th>
                        <th>عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($brands as $key => $brand)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$brand->title}}</td>
                            <td>{{$brand->slug}}</td>
                            <td>
                                <a class="btn btn-secondary" href="{{route('model.index',$brand->id)}}">مدل ها</a>
                                @if(auth('admin')->user()->type == 1)
                                    <a class="btn btn-warning" data-toggle="modal" data-target="#edit-{{$brand->id}}">ویرایش
                                    </a>
                                @endif
                            </td>
                        </tr>


                        <div class="modal fade" id="edit-{{$brand->id}}" tabindex="-1" role="dialog"
                             aria-labelledby="exampleModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">ویرایش برند</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{route('brand.update',$brand->id)}}" method="post" role="form">
                                            @csrf
                                            {{method_field('PUT')}}

                                            <div class="form-group">
                                                <label for="title">نام برند</label>
                                                <input type="text" class="form-control" name="title" id="title"
                                                       value="{{$brand->title}}">
                                            </div>

                                            <div class="form-group">
                                                <label for="title">اسلاگ</label>
                                                <input type="text" class="form-control" name="slug" id="slug"
                                                       value="{{$brand->slug}}">
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
