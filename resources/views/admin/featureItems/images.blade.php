@extends('admin_index')

@section('title')
    تصاویر {{ $item->title }}
@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin_c/plugins/table/datatable/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin_c/plugins/table/datatable/dt-global_style.css') }}">
@endsection


@section('content')
    <div class="row justify-content-center">

        <div class="col-10 text-center">

            <a class="btn btn-primary" href="" data-toggle="modal" data-target="#create">تصویر جدید</a>

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
                            <form action="{{ route('item.image.store.admin', $item->id) }}" enctype="multipart/form-data"
                                method="post" role="form">
                                @csrf


                                <div class="form-group">
                                    <label for="image">تصویر</label>
                                    <input type="file" class="form-control" name="image" id="image">
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
                            <th>تصویر</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($images as $key => $image)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    <img style="width: 100px" src="{{ asset($item->image($key)) }}">
                                </td>
                                <td>
                                    <a class="btn btn-warning" href="" data-toggle="modal"
                                        data-target="#edit-{{ $key }}">ویرایش</a>
                                </td>
                            </tr>

                            <div class="modal fade" id="edit-{{ $key }}" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('item.image.update.admin') }}"
                                                enctype="multipart/form-data" method="post" role="form">
                                                @csrf

                                                {{ method_field('PUT') }}

                                                <input type="hidden" name="image_key" value="{{ $key }}">
                                                <input type="hidden" name="item_id" value="{{ $item->id }}">

                                                <div class="form-group">
                                                    <label for="image">تصویر</label>
                                                    <input type="file" class="form-control" name="image"
                                                        id="image">
                                                </div>


                                                <img style="width: 100px" src="{{ asset($item->image($key)) }}">

                                                <br>

                                                <button type="submit" class="btn btn-primary mt-2">ثبت تغییرات</button>
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


    <script>
        function selectFeature(feature_id, feature_title, for_feu_id) {
            if (for_feu_id != '') {
                $('#feature-' + for_feu_id).modal('hide');
                $('#parent_id-' + for_feu_id).val(feature_id);
                $('#select-feu-btn-' + for_feu_id).text(feature_title);
            } else {
                $('#feature').modal('hide');
                $('#parent_id').val(feature_id);
                $('#select-feu-btn').text(feature_title);
            }
        }


        function featureDrop(feature_id, feu_edit_id = null) {
            //for store
            if (feu_edit_id == null) {
                $('#feature-drop-' + feature_id).css({
                    'background-color': 'rgb(255 52 52)',
                    'color': '#ffffff'
                })
                url = "{{ route('home') }}/admin/get-category-feature-children/" + feature_id;
                $.get(url, function(data) {
                    showchild = '#feu-children-' + feature_id;
                    $(showchild).html(data)
                })
            }
            //for edit feu_edit_id
            else {
                $('#feature-drop-' + feature_id + '-' + feu_edit_id).css({
                    'background-color': 'rgb(255 52 52)',
                    'color': '#ffffff'
                })
                url = "{{ route('home') }}/admin/get-category-feature-children/" + feature_id + "/" + feu_edit_id;
                $.get(url, function(data) {
                    showchild = '#feu-children-' + feature_id + '-' + feu_edit_id;
                    $(showchild).html(data)
                })
            }
        }
    </script>
@endsection
