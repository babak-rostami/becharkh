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

            <div class="table-responsive mb-4 mt-4">
                <table id="zero-config" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>عنوان مقاله</th>
                            <th>توضیحات کوتاه</th>
                            <th>بازدید</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($blogs as $key => $blog)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $blog->title }}
                                    <br>
                                    <span
                                        class="badge badge-dark">{{ $blog->category_id == 0 ? 'بدون دسته' : $blog->category->title }}</span>
                                    <br>
                                    @if ($blog->status == 0)
                                        <span class="badge badge-danger">منتشر نشده</span>
                                    @elseif($blog->status == 1)
                                        <span class="badge badge-success">منتشر شده</span>
                                    @endif
                                    @if ($blog->google_index == 1)
                                        <span class="badge badge-success">درگوگل</span>
                                    @else
                                        <span class="badge badge-danger">درگوگل نیست</span>
                                    @endif
                                    <span class="badge badge-primary">{{ $blog->comment_count ?? 0 }}</span>
                                </td>
                                <td>{{ $blog->short_description }}</td>
                                <td>{{ $blog->seen_count }}</td>
                                <td>
                                    @if (isset($blog->category))
                                        <a class="btn btn-light" target="_blank"
                                            href="{{ route('blog.show', ['category_slug' => $blog->category->slug, 'slug' => $blog->slug, 'random_id' => $blog->random_id]) }}">مشاهده</a>
                                    @endif
                                    <a class="btn btn-warning" href="{{ route('user.edit.post', $blog->id) }}">ویرایش
                                    </a>
                                    <a class="btn btn-primary" href="" data-toggle="modal"
                                        data-target="#comment-{{ $blog->id }}">نظر</a>
                                    <a class="btn btn-danger" data-toggle="modal"
                                        data-target="#delete-{{ $blog->id }}">حذف
                                    </a>
                                </td>
                            </tr>
                            <div class="modal fade" id="comment-{{ $blog->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">نظر</h5>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('admin.blog.comment.store') }}" method="post"
                                                role="form">
                                                @csrf

                                                <input type="hidden" name="blog_id" value="{{ $blog->id }}">
                                                <div class="form-group">
                                                    <label>نام</label>
                                                    <input type="text" class="form-control" name="name">
                                                </div>
                                                <div class="form-group">
                                                    <label>نام کاربری</label>
                                                    <input required type="text" class="form-control" name="username">
                                                </div>
                                                <textarea style="min-height: 150px" required class="form-control" name="body"
                                                    placeholder="نظر خود را اینجا بنویسید..."></textarea>

                                                <button type="submit" class="btn btn-primary w-100">ارسال نظر</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="delete-{{ $blog->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">حذف مقاله</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            آیا از حذف مقاله {{ $blog->title }} اطمینان دارید؟
                                            <form action="{{ route('blog.destroy', $blog->id) }}" class="mt-4"
                                                method="post" role="form">
                                                @csrf
                                                {{ method_field('DELETE') }}

                                                <button type="submit" class="btn btn-danger">حذف</button>
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    انصراف
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
