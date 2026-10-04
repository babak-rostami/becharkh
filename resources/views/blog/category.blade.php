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
            <a class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">ایجاد دسته بندی</a>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">ایجاد</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('blog.category.store')}}" method="POST" role="form"
                              enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="title">عنوان دسته</label>
                                <input type="text" class="form-control" name="title" id="title">
                            </div>

                            <div class="form-group">
                                <label for="slug">slug</label>
                                <input type="text" class="form-control" name="slug" id="slug">
                            </div>

                            <div class="form-group">
                                <label for="meta_title">meta_title</label>
                                <input type="text" class="form-control" name="meta_title" id="meta_title">
                            </div>


                            <div class="form-group">
                                <label for="description">description</label>
                                <textarea class="form-control" name="description" id="description"></textarea>
                            </div>


                            <div class="form-group">
                                <label for="meta_description">meta_description</label>
                                <textarea class="form-control" name="meta_description" id="meta_description"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="image">تصویر</label>
                                <input name="image" id="image" type="file">
                            </div>


                            <button type="submit" class="btn btn-primary">ثبت</button>
                        </form>
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
                        <th>تصویر</th>
                        <th>عنوان</th>
                        <th>بازدید</th>
                        <th>عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($categories as $key => $category)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td><img src="{{asset('files/blogcategory/images/'.$category->image)}}" style="width: 60px;height: 60px"></td>
                            <td>{{$category->title}}</td>
                            <td>{{$category->seen_count}}</td>
                            <td>
                                <a class="btn btn-warning" data-toggle="modal" data-target="#edit-{{$category->id}}">ویرایش</a>
                            </td>
                        </tr>

                        <div class="modal fade" id="edit-{{$category->id}}" tabindex="-1" role="dialog"
                             aria-labelledby="exampleModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">ویرایش</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{route('blog.category.update',$category)}}" method="POST"
                                              role="form"
                                              enctype="multipart/form-data">
                                            {{method_field('PUT')}}
                                            @csrf

                                            <div class="form-group">
                                                <label for="title">عنوان دسته</label>
                                                <input type="text" class="form-control" name="title" id="title"
                                                       value="{{$category->title}}">
                                            </div>

                                            <div class="form-group">
                                                <label for="slug">slug</label>
                                                <input type="text" class="form-control" name="slug" id="slug"
                                                       value="{{$category->slug}}">
                                            </div>

                                            <div class="form-group">
                                                <label for="meta_title">meta_title</label>
                                                <input type="text" class="form-control" name="meta_title"
                                                       id="meta_title" value="{{$category->meta_title}}">
                                            </div>


                                            <div class="form-group">
                                                <label for="description">description</label>
                                                <textarea class="form-control" name="description"
                                                          id="description">{{$category->description}}</textarea>
                                            </div>


                                            <div class="form-group">
                                                <label for="meta_description">meta_description</label>
                                                <textarea class="form-control" name="meta_description"
                                                          id="meta_description">{{$category->meta_description}}</textarea>
                                            </div>


                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="image">تصویر</label>
                                                        <input name="image" id="image" type="file">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <img style="width: 70px; height: 70px"
                                                         src="{{asset('files/blogcategory/images/'.$category->image)}}">
                                                </div>
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
