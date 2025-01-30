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

            <div class="table-responsive mb-4 mt-4">
                <table id="zero-config" class="table table-hover" style="width:100%">
                    <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>نام</th>
                        <th>ایمیل</th>
                        <th>مقاله</th>
                        <th>نظر</th>
                        <th>زمان</th>
                        <th>عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($comments as $key => $comment)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$comment->name}}</td>
                            <td>{{$comment->email}}</td>
                            <td><a target="_blank"
                                   href="{{route('blog.show',$comment->blog->slug)}}">{{$comment->blog->title}}</a></td>
                            <td>{{$comment->body}}</td>
                            <td>{{jdate($comment->created_at)->ago()}}</td>
                            <td>
                                <a class="btn btn-warning" href="" data-toggle="modal"
                                   data-target="#edit-{{$comment->id}}">ویرایش
                                </a>
                                <a class="btn btn-danger" data-toggle="modal" data-target="#delete-{{$comment->id}}">حذف
                                </a>
                            </td>
                        </tr>

                        <div class="modal fade" id="delete-{{$comment->id}}" tabindex="-1" role="dialog"
                             aria-labelledby="exampleModalLabel"
                             aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">حذف نظر</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        آیا از حذف نظر اطمینان دارید؟
                                        <form action="{{route('blog.comment.destroy',$comment->id)}}" class="mt-4"
                                              method="post"
                                              role="form">
                                            @csrf
                                            {{method_field('DELETE')}}

                                            <button type="submit" class="btn btn-danger">حذف</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                انصراف
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="edit-{{$comment->id}}" tabindex="-1" role="dialog"
                             aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">ویرایش پاسخ</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">

                                        <form action="{{route('blog.comment.update',$comment->id)}}" method="POST"
                                              role="form">
                                            @csrf
                                            {{method_field('PUT')}}

                                            <div class="form-group">
                            <textarea required class="form-control" style="width: 100%; height: 150px; resize: none"
                                      name="body"
                                      placeholder="دیدگاه خود را بنویسید...">{{$comment->body}}</textarea>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <input type="text" required
                                                               class="form-control" value="{{$comment->name}}"
                                                               name="name" placeholder="نام...">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <input type="email" required class="form-control"
                                                               name="email"
                                                               value="{{$comment->email}}"
                                                               placeholder="ایمیل...">
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="submit" class="btn btn-success">ویرایش دیدگاه</button>
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
