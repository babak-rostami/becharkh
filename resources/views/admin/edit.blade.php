@extends('admin_index')


@section('title')
    ویرایش اطلاعات
@endsection


@section('style')

@endsection


@section('content')

    <form action="{{route('admin.update')}}" method="post" role="form" class="cmxform" id="commentForm" enctype="multipart/form-data">
        @csrf
        {{method_field('PUT')}}

        <ul class="nav nav-pills nav-pills-success" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab"
                   aria-controls="pills-home" aria-selected="true">اطلاعات کلی</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="pills-image-tab" data-toggle="pill" href="#pills-image" role="tab"
                   aria-controls="pills-image" aria-selected="false">تصویر</a>
            </li>
        </ul>
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                 aria-labelledby="pills-home-tab">

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="name">نام و نام خانوادگی</label>
                            <input required type="text" class="form-control" name="name" id="name" value="{{$admin->name}}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="email">ایمیل</label>
                            <input type="text" class="form-control" name="email" id="email" value="{{$admin->email}}"
                                   required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="username">نام کاربری</label>
                            <input required type="text" class="form-control" name="username" id="username"
                                   value="{{$admin->username}}">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="phone">شماره تماس</label>
                            <input type="text" class="form-control" name="phone" id="phone" value="{{$admin->phone}}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="password">پسورد</label>
                            <input required type="password" class="form-control" name="password" id="password"
                                   placeholder="در صورت نیاز به تغییر پسورد تکمیل کنید">
                        </div>
                    </div>
                </div>


            </div>

            <div class="tab-pane fade" id="pills-image" role="tabpanel" aria-labelledby="pills-image-tab">

                <div class="row">
                    <div class="col-6">
                        <input name="image" type="file" class="dropify" data-height="300"/>
                    </div>
                    <div class="col-6 text-center align-items-center">
                        <p>تصویر فعلی</p>
                        @if(isset(auth('admin')->user()->image))
                            <img style="width: 250px; height: 250px" src="{{asset('files/admin/images/'.$admin->image)}}">
                        @else
                            <span class="badge badge-warning">انتخاب نشده</span>
                        @endif
                    </div>
                </div>

            </div>
        </div>


        <button type="submit" class="btn btn-primary">ثبت</button>
    </form>

@endsection


@section('script')
    <script src="{{asset('melody/js/form-validation.js')}}"></script>
{{--    <script src="{{asset('melody/js/bt-maxLength.js')}}"></script>--}}

    <script src="{{asset('melody/js/dropify.js')}}"></script>

@endsection
