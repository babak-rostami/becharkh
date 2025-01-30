@extends('admin_index')

@section('title')
    آیتم های {{ $feature->title }}
@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin_c/plugins/table/datatable/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin_c/plugins/table/datatable/dt-global_style.css') }}">

    <script src="{{ asset('library/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('library/ckeditor/ckfinder.js') }}"></script>
    <script src="{{ asset('library/ckeditor/de.js') }}"></script>
@endsection


@section('content')
    <div class="row justify-content-center">

        <div class="col-10 text-center">

            {{ $items->links() }}

            <a class="btn btn-primary" href="" data-toggle="modal" data-target="#create">ایجاد آیتم جدید</a>

            <div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('feature.item.store.admin') }}" enctype="multipart/form-data"
                                method="post" role="form">
                                @csrf


                                <input type="hidden" value="{{ $feature->id }}" name="feature_id">

                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label for="title">نام آیتم</label>
                                            <input type="text" class="form-control" name="title" id="title"
                                                value="{{ old('title') }}">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label for="title_en">نام انگلیسی آیتم</label>
                                            <input type="text" class="form-control" name="title_en" id="title_en"
                                                value="{{ old('title_en') }}">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <div class="form-group">
                                            <label for="slug">اسلاگ</label>
                                            <input type="text" class="form-control" name="slug" id="slug"
                                                value="{{ old('slug') }}">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
                                        <label for="status">تایید شده</label>
                                        <select class="form-control" name="status" id="status">
                                            <option value="0">خیر</option>
                                            <option value="1">بله</option>
                                        </select>
                                    </div>
                                    @if ($parent_itmes != null)
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="parent_id">آیتم بالایی</label>
                                                <select class="form-control" name="parent_id" id="parent_id">
                                                    @foreach ($parent_itmes as $i)
                                                        <option value="{{ $i->id }}">{{ $i->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="category_id">دسته بندی</label>
                                            <select class="form-control" name="category_id">
                                                @foreach ($feature->categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="title_in_rtable">متا تایتل میزگرد</label>
                                            <input type="text" class="form-control" name="title_in_rtable"
                                                id="title_in_rtable">
                                        </div>
                                        <div class="form-group">
                                            <label for="desc_in_rtable">متا دسکریپشن میزگرد</label>
                                            <textarea class="form-control" name="desc_in_rtable" id="desc_in_rtable"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>دسکریپشن میزگرد ادیتور</label>
                                            <textarea class="form-control ckeditor" name="desc_in_rtable_editor">{{ old('desc_in_rtable_editor') }}</textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="title_in_comment">متا تایتل نظرات کاربران</label>
                                            <input type="text" class="form-control" name="title_in_comment"
                                                id="title_in_comment">
                                        </div>
                                        <div class="form-group">
                                            <label for="desc_in_comment">متا دسکریپشن نظرات کاربران</label>
                                            <textarea class="form-control" name="desc_in_comment" id="desc_in_comment"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>دسکریپشن نظرات ادیتور</label>
                                            <textarea class="form-control ckeditor" name="desc_in_comment_editor">{{ old('desc_in_comment_editor') }}</textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="title_in_ads">متا تایتل آگهی ها</label>
                                            <input type="text" class="form-control" name="title_in_ads"
                                                id="title_in_ads">
                                        </div>
                                        <div class="form-group">
                                            <label for="desc_in_ads">متا دسکریپشن آگهی ها</label>
                                            <textarea class="form-control" name="desc_in_ads" id="desc_in_ads"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>دسکریپشن آگهی ادیتور</label>
                                            <textarea class="form-control ckeditor" name="desc_in_ads_editor">{{ old('desc_in_ads_editor') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary mt-2">ایجاد شود</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive mb-4 mt-4">
                <table id="zero-config" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>نام آیتم</th>
                            <th>نام انگلیسی آیتم</th>
                            <th>اسلاگ</th>
                            <th>تایید شده؟</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->full_title ?? $item->title }}</td>
                                <td>{{ $item->title_en }}</td>
                                <td>{{ $item->slug }}</td>
                                <td>
                                    @if ($item->status == 1)
                                        <span class="badge badge-success">بله</span>
                                    @else
                                        <span class="badge badge-danger">خیر</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-secondary"
                                        href="{{ route('item.images.admin', $item->id) }}">تصاویر</a>

                                    <a class="btn btn-warning"
                                        href="{{ route('item.edit.admin', $item->id) }}">ویرایش</a>

                                    <a class="btn btn-danger" href="" data-toggle="modal"
                                        data-target="#delete-{{ $item->id }}">حذف</a>
                                </td>
                            </tr>
                            <div class="modal fade" id="delete-{{ $item->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            مطمئنید که میخواهید حذف شود؟
                                            <br>
                                            تنها در صورتی که آیتم تایید نشده است حذف کنید!
                                            <br>
                                            یا اینکه تازه اضافه کرده اید و مطمئن هستید که ایندکس نشده است
                                            <br>
                                            <a href="{{ route('feature.item.destroy.admin', $item->id) }}"
                                                class="btn btn-danger">حذف</a>
                                            <a class="btn btn-primary" href="" data-dismiss="modal">بیخیال</a>
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

    <script>
        const editors = document.querySelectorAll('.ckeditor');
        editors.forEach(editor => {
            ClassicEditor.create(editor, {
                language: "fa",
            });
        });
    </script>
@endsection
