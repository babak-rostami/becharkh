@extends('admin_index')

@section('title')
    بسته های آگهی {{$user->username}}
@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{asset('admin_c/plugins/table/datatable/datatables.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('admin_c/plugins/table/datatable/dt-global_style.css')}}">
@endsection


@section('content')

    <div class="row justify-content-center">

        <div class="col-10 text-center">

            <a class="btn btn-secondary" href="{{route('admin.users')}}">برگرد به مدیریت کاربران</a>

            <div class="table-responsive mb-4 mt-4">
                <table id="zero-config" class="table table-hover" style="width:100%">
                    <thead>
                    <tr>
                        <th>ردیف</th>
                        <th>کاربر</th>
                        <th>نام بسته</th>
                        <th>آگهی باقی مانده</th>
                        <th>بالابر باقی مانده</th>
                        <th>تصویر باقی مانده</th>
                        <th>قیمت</th>
                        <th>زمان باقیمانده</th>
                        <th>پرداخت شده؟</th>
                        <th>#</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($user->allPackages as $key => $pack)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$user->username}}</td>
                            <td>{{$pack->name}} تومان</td>
                            <td>{{$pack->advertise_count}}</td>
                            <td>{{$pack->ad_to_top_count}}</td>
                            <td>{{$pack->image_count}}</td>
                            <td>{{$pack->price}} تومان</td>
                            <td>{{$pack->expire_date}}</td>
                            <td>
                                @if($pack->status == 1)
                                    <span class="badge badge-success">فعال</span>
                                @else
                                    <span class="badge badge-danger">غیر فعال</span>
                                @endif
                            </td>
                            <td>{{$pack->username}}</td>
                            <td>
                                <a class="btn btn-primary">فیش</a>
                            </td>
                        </tr>
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
