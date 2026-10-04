@extends('index')


@section('title')
    آگهی های من
@endsection


@section('style')
    <meta name="robots" content="noindex">
@endsection


@section('content')

    <div class="row justify-content-center mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('user.dashboard',auth('user')->user()->username)}}">حساب
                        کاربری</a></li>
                <li class="breadcrumb-item active" aria-current="page">آگهی های من</li>
            </ol>
        </nav>
    </div>

    @if(session('success'))
        <div>
            <p class="alert alert-success text-center">{{session('success')}}</p>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <p style="color: #000000">{{$error}}</p>
            @endforeach
        </div>
    @endif

    <section id="main" class="mb-5 mt-2">

        <div class="row mx-2 justify-content-center shadow bg-wht p-4 radius-10">

            @if(!$advertises->isEmpty())
                @foreach($advertises as $advertise)
                    <div class="col-md-3 col-10 my-1 mx-2 shadow ads-box bg-wht radius-10 shadow">

                        <div class="row align-items-center justify-content-center">
                            <div class="col-12">
                                <a class="decor-none"
                                   href="{{route('car.show',['ad_random'=>$advertise->random_id , 'ad_slug'=>$advertise->slug])}}">

                                    <img style="width: 100%; height: 200px"
                                         src="{{asset($advertise->image())}}">
                                    <h2 class="font-20 mt-2 col-12">{{$advertise->title}}</h2>
                                    <p class="col-12">{{jdate($advertise->created_at)->ago()}}</p>
                                    <hr>
                                    <p class="col-12">{{$advertise->ostann->title}}-{{$advertise->shahrr->title}}</p>
                                    <div class="hr-style my-2"></div>
                                </a>
                            </div>
                            <div class="col-12 my-2">
                                <span>{{$advertise->showPrice()}}</span>
                            </div>
                            <div class="col-12 text-center my-2">
                                <a class="btn"
                                   href="{{route('car.edit',['slug'=>$advertise->slug,'random_id'=>$advertise->random_id])}}">
                                    <img src="{{asset('files/other/images/edit.png')}}" style="width: 25px;height: 25px"
                                         title="ویرایش">
                                </a>
                                <a class="btn"
                                   data-toggle="modal" data-target="#delete-{{$advertise->id}}">
                                    <img src="{{asset('files/other/images/delete.png')}}"
                                         style="width: 25px;height: 25px"
                                         title="حذف">
                                </a>

                                <a class="btn btn-primary" href=""
                                   data-toggle="modal" data-target="#top-{{$advertise->id}}">
                                    بالابر
                                </a>

                                <a class="btn btn-secondary"
                                   href="{{route('car.new.pic',['slug'=>$advertise->slug,'random_id'=>$advertise->random_id])}}">
                                    تصاویر آگهی
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- go to top advertise modal -->
                    <div class="modal fade" id="top-{{$advertise->id}}" tabindex="-1" role="dialog"
                         aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">بالابر</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-12 text-center">
                                            <p>تعداد بالابر باقی مانده شما
                                                : {{auth('user')->user()->addToTopRemain()}}</p>
                                            با استفاده از بالابر آگهی شما به ابتدای لیست رفته و بیشتر دیده می شود
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">انصراف</button>
                                    <a href="{{route('send.top.advertise',$advertise->id)}}" class="btn btn-primary">استفاده کن</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- delete advertise modal -->
                    <div class="modal fade" id="delete-{{$advertise->id}}" tabindex="-1" role="dialog"
                         aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">حذف آگهی</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-12 text-center">
                                            <p>{{$advertise->title}}</p>
                                            آیا از حذف آگهی اطمینان دارید؟
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">انصراف</button>
                                    <a href="{{route('car.destroy',$advertise->id)}}" class="btn btn-danger">حذف</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-12 my-5 p-5 text-center">
                    <p class="text-gray">در حال حاضر آگهی ثبت نشده است</p>
                </div>
            @endif

        </div>

        <div class="row justify-content-center mt-2">
            {{$advertises->links()}}
        </div>

    </section>


@endsection


@section('script')

@endsection
