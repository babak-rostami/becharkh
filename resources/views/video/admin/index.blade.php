@extends('admin_index')

@section('title')
    مدیریت ویدیو ها
@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin_c/plugins/table/datatable/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin_c/plugins/table/datatable/dt-global_style.css') }}">
@endsection


@section('content')
    <div class="row justify-content-center">

        <div class="col-10 text-center">

            <a class="btn btn-primary" href="{{ route('admin.create.video') }}">ویدیو جدید</a>

            {{ $videos->links() }}

            <div class="table-responsive mb-4 mt-4">
                <table id="zero-config" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>عنوان</th>
                            <th>دسته بندی</th>
                            <th>وضعیت</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($videos as $key => $video)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    <span>{{ $video->title }}</span>
                                    <br>
                                    <span class="badge badge-light">{{ $video->seen_count ?? 0 }} بازدید</span>
                                </td>
                                <td>
                                    {{ $video->category->title }}
                                </td>
                                <td>
                                    @if ($video->status == 0)
                                        <span class="badge badge-danger">در اف تی پی تایید نشده</span>
                                    @elseif($video->status == 1)
                                        <span class="badge badge-success">در اف تی پی تایید شده</span>
                                    @elseif($video->status == 2)
                                        <span class="badge badge-danger">در یوتیوب تایید نشده</span>
                                    @elseif($video->status == 3)
                                        <span class="badge badge-success">در یوتیوب تایید شده</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-warning" href="{{ route('admin.edit.video', $video->id) }}">ویرایش</a>
                                    @if (isset($video->slug2))
                                        <a class="btn btn-primary"
                                            href="{{ route('video.show', $video->slug2) }}">مشاهده</a>
                                    @endif
                                    <a class="btn btn-danger" href="" data-toggle="modal"
                                        data-target="#delete-{{ $video->id }}">حذف</a>
                                </td>
                            </tr>
                            <div class="modal fade" id="delete-{{ $video->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <a data-dismiss="modal" class="btn btn-warning">بعدا</a>
                                            <a class="btn btn-danger"
                                                href="{{ route('admin.destroy.video', $video->id) }}">حذف</a>
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
