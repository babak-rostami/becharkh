@extends('admin_index')


@section('title')
    بازدید
@endsection


@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin_c/plugins/table/datatable/datatables.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin_c/plugins/table/datatable/dt-global_style.css') }}">
@endsection


@section('content')
    <div class="row justify-content-center">
        @if (!isset($page))
            <div class="col-12 text-center">
                <a href="{{ route('site.view.count.index', 'shop') }}" class="btn btn-lg btn-primary mt-3">بازدید
                    بازار
                    {{ $adCount }}
                </a>
                <a href="{{ route('site.view.count.index', 'rtable') }}" class="btn btn-lg btn-secondary mt-3">بازدید
                    میزگرد
                    {{ $rtableCount }}
                </a>
                <a href="{{ route('site.view.count.index', 'comment') }}" class="btn btn-lg btn-dark mt-3">بازدید بحث
                    آزاد
                    {{ $ccommentCount }}
                </a>
            </div>


            <div class="col-10">
                <div class="table-responsive mb-4 mt-4">
                    <table id="zero-config" class="table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>دسته بندی</th>
                                <th>ویژگی</th>
                                <th>نام آیتم</th>
                                <th>نام انگلیسی آیتم</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->category->title }}</td>
                                    <td>{{ $item->feature->title }}</td>
                                    <td>{{ $item->withParentsTitle() }}</td>
                                    <td>{{ $item->title_en }}</td>
                                    <td>
                                        <a class="btn btn-secondary"
                                            href="{{ route('item.images.admin', $item->id) }}">تصاویر</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="col-12 text-center">
                <a href="{{ route('site.view.count.index', 'shop') }}" class="btn btn-lg btn-primary mt-3">بازدید
                    بازار
                    {{ $adCount }}
                </a>
                <a href="{{ route('site.view.count.index', 'rtable') }}" class="btn btn-lg btn-secondary mt-3">بازدید
                    میزگرد
                    {{ $rtableCount }}
                </a>
                <a href="{{ route('site.view.count.index', 'comment') }}" class="btn btn-lg btn-dark mt-3">بازدید بحث
                    آزاد
                    {{ $ccommentCount }}
                </a>
            </div>
            <div class="col-12 text-center">
                @if ($page == 'shop')
                    <h1>بازدید بازار</h1>
                @elseif($page == 'rtable')
                    <h1>بازدید میزگرد</h1>
                @elseif($page == 'comment')
                    <h1>بازدید نظرات کاربران</h1>
                @endif

                <div class="table-responsive mb-4 mt-4">
                    <table id="zero-config" class="table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>ردیف</th>
                                <th>صفحه</th>
                                <th>بازدید</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->withParentsTitle() }}</td>
                                    @if ($page == 'shop')
                                        <td>{{ $item->ad_seen_count }}</td>
                                    @elseif($page == 'rtable')
                                        <td>{{ $item->rtable_seen_count }}</td>
                                    @elseif($page == 'comment')
                                        <td>{{ $item->ccomment_seen_count }}</td>
                                    @endif
                                    <td>
                                        @if ($page == 'shop')
                                            <a target="_blank" class="btn btn-primary"
                                                href="{{ $item->withParentsAdvertiseUrl() }}">مشاهده</a>
                                        @elseif($page == 'rtable')
                                            <a target="_blank" class="btn btn-primary"
                                                href="{{ $item->withParentsRtableUrl() }}">مشاهده</a>
                                        @elseif($page == 'comment')
                                            <a target="_blank" class="btn btn-primary"
                                                href="{{ $item->withParentsCommentUrl() }}">مشاهده</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        @endif
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
