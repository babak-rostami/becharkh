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
            <a class="btn btn-primary" href="" data-toggle="modal" data-target="#create">بسته جدید</a>
        </div>

        <div class="col-10">

            <div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">ایجاد بسته</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('advertise.package.store') }}" method="post" role="form">
                                @csrf

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="name">نام بسته</label>
                                            <input type="text" class="form-control" name="name" id="name"
                                                value="{{ old('name') }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="name">توضیحات بسته</label>
                                            <input type="text" class="form-control" name="body" id="body"
                                                value="{{ old('body') }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="date_number">تعداد روز</label>
                                            <input type="number" class="form-control" name="date_number" id="date_number"
                                                value="{{ old('date_number') }}">
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="advertise_count">تعداد آگهی</label>
                                            <input type="number" class="form-control" name="advertise_count"
                                                id="advertise_count" value="{{ old('advertise_count') }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="ad_to_top_count">تعداد بالابر</label>
                                            <input type="number" class="form-control" name="ad_to_top_count"
                                                id="ad_to_top_count" value="{{ old('ad_to_top_count') }}">
                                        </div>
                                    </div>

                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="image_count">تعداد تصویر</label>
                                            <input type="number" class="form-control" name="image_count" id="image_count"
                                                value="{{ old('image_count') }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="price">قیمت تخفیفی</label>
                                            <input type="text" class="form-control" name="price" id="price"
                                                value="{{ old('price') }}">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="org_price">قیمت اصلی</label>
                                            <input type="text" class="form-control" name="org_price" id="org_price"
                                                value="{{ old('org_price') }}">
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">ثبت</button>
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
                            <th>نام بسته</th>
                            <th>تعداد تبلیغ</th>
                            <th>تعداد بالابر</th>
                            <th>تعداد عکس</th>
                            <th>قیمت بسته</th>
                            <th>تعداد روز</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($packages as $key => $package)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $package->name }}</td>
                                <td>{{ $package->body }}</td>
                                <td>{{ $package->advertise_count }}</td>
                                <td>{{ $package->ad_to_top_count }}</td>
                                <td>{{ $package->image_count }}</td>
                                <td>{{ $package->price }}</td>
                                <td>{{ $package->date_number }}</td>
                                <td>
                                    <a class="btn btn-warning" data-toggle="modal"
                                        data-target="#edit-{{ $package->id }}">ویرایش
                                        بسته
                                    </a>
                                    <a class="btn btn-danger" data-toggle="modal"
                                        data-target="#delete-{{ $package->id }}">حذف
                                    </a>
                                </td>
                            </tr>


                            <div class="modal fade" id="edit-{{ $package->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">ویرایش بسته</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('advertise.package.update', $package->id) }}"
                                                method="post" role="form">
                                                {{ method_field('put') }}
                                                @csrf

                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="name">نام بسته</label>
                                                            <input type="text" class="form-control" name="name"
                                                                id="name" value="{{ $package->name }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="name">توضیحات بسته</label>
                                                            <input type="text" class="form-control" name="body"
                                                                id="body" value="{{ $package->body }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="date_number">تعداد روز</label>
                                                            <input type="number" class="form-control" name="date_number"
                                                                id="date_number" value="{{ $package->date_number }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="advertise_count">تعداد آگهی</label>
                                                            <input type="number" class="form-control"
                                                                name="advertise_count" id="advertise_count"
                                                                value="{{ $package->advertise_count }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="ad_to_top_count">تعداد بالابر</label>
                                                            <input type="number" class="form-control"
                                                                name="ad_to_top_count" id="ad_to_top_count"
                                                                value="{{ $package->ad_to_top_count }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="image_count">تعداد تصویر</label>
                                                            <input type="number" class="form-control" name="image_count"
                                                                id="image_count" value="{{ $package->image_count }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="price">قیمت تخفیفی</label>
                                                            <input type="text" class="form-control" name="price"
                                                                id="price" value="{{ $package->price }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="form-group">
                                                            <label for="org_price">قیمت اصلی</label>
                                                            <input type="text" class="form-control" name="org_price"
                                                                id="org_price" value="{{ $package->org_price }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                <button type="submit" class="btn btn-primary">ثبت تغییرات</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="modal fade" id="delete-{{ $package->id }}" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">حذف بسته</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            آیا از حذف بسته {{ $package->name }} اطمینان دارید؟
                                            <form action="{{ route('advertise.package.destroy', $package->id) }}"
                                                class="mt-4" method="post" role="form">
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
            "lengthMenu": [50, 100, 250, 500],
            "pageLength": 50
        });
    </script>
@endsection
