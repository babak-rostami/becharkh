@extends('index')

@section('title'){{$meta_title}}@endsection

{{--@dd($meta_title)--}}

@section('style')


    <link rel="stylesheet" href="{{asset('css/chosen.css')}}">
    {{--    <link rel="stylesheet" href="{{asset('css/chosen-dark.css')}}">--}}

    <script src="{{asset('js/chosen.jquery.js')}}"></script>
    <script src="{{asset('js/chosen.proto.js')}}"></script>



    <script>
        $(document).ready(function () {
            $(".chosen-select").chosen();
        });

    </script>

    @if(request()->brand!= null)
        <meta name="title"
              content="{{$meta_title}}">
    @else
        <meta name="title" content="خرید و فروش انواع خودرو صفر و کارکرده در ایران">
    @endif
    <meta name="description"
          content="{{$meta_description}}">
    <meta name="keywords"
          content="{{$keywords}}">
    <meta name="robots" content="index, follow">

@endsection

@section('content')

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

    <nav aria-label="breadcrumb" class="mt-2">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('home')}}">بچرخ</a></li>
            @if(!isset(request()->brand) && !isset(request()->model))
                <li class="breadcrumb-item active">خودرو</li>
            @elseif(isset(request()->brand) && !isset(request()->model))
                <li class="breadcrumb-item"><a href="{{route('car.filter.get')}}">خودرو</a></li>
                <li class="breadcrumb-item active"
                    aria-current="page">{{$data->brandWithNameEn(request()->brand)->title}}</li>
            @elseif(isset(request()->brand) && isset(request()->model))
                <li class="breadcrumb-item"><a href="{{route('car.filter.get')}}">خودرو</a></li>
                <li class="breadcrumb-item">
                    <a
                        href="{{route('car.filter.get',request()->brand)}}">{{$data->brandWithNameEn(request()->brand)->title}}</a>
                </li>
                <li class="breadcrumb-item active"
                    aria-current="page">{{$data->brandModel(request()->brand,request()->model)->title}}</li>
            @endif

        </ol>
    </nav>

    <section id="main" class="my-4">

        <div class="row justify-content-center">
            <div class="col-12 col-md-8">
                <div class="row mx-1 justify-content-center">

                    @if(!$advertises->isEmpty())

                        @foreach($advertises as $advertise)
                            @if(isset($advertise) && $advertise->adModel()!= null)
                                <div class="col-12 mt-1 mb-2 ads-box bg-wht radius-10 shadow">

                                    <div class="row align-items-center">
                                        <div class="col-5 col-md-3">
                                            <a class="decor-none" target="_blank"
                                               href="{{route('car.show',['username'=>$advertise->user->username,'random'=>$advertise->random_id])}}">
                                                <img class="w-100" alt="{{$advertise->title}}"
                                                     title="{{$advertise->title}}"
                                                     src="{{asset($advertise->image())}}">
                                            </a>
                                        </div>
                                        <div class="col-7 col-md-9">
                                            <div class="row">

                                                <div class="col-12">
                                                    <a class="decor-none" target="_blank"
                                                       href="{{route('car.show',['username'=>$advertise->user->username,'random'=>$advertise->random_id])}}">
                                                        <h2
                                                            class="my-3 advertise-title">{{$advertise->title}}</h2>
                                                    </a>
                                                </div>

                                                <div class="col-12 col-md-3 mt-2" style="font-size: 14px">
                                                    <img class="mr-1"
                                                         src="{{asset('files/other/images/clock.png')}}"><span>{{jdate($advertise->created_at)->ago()}}</span>
                                                </div>
                                                <div class="col-12 col-md-3 mt-2">
                                                    <span class="ml-1" style="font-size: 14px">{{$advertise->ostann->title}}
                                                    -{{$advertise->shahrr->title}}</span>
                                                </div>
                                                <div class="col-12 col-md-3 mt-2">
                                                    <span
                                                        style="font-size: 14px">{{$advertise->adModel->kilometer}} کیلومتر</span>
                                                </div>

                                                <div class="col-11 mx-auto hr-style my-2">
                                                </div>

                                                <div class="col-12 col-md-9 pl-4 mb-3" style="font-size: 15px">
                                                    @if($advertise->price != null)
                                                        <span class="price-border">{{number_format((int)$advertise->showPrice())}} تومان</span>
                                                    @else
                                                        <span>توافقی</span>
                                                    @endif
                                                </div>
                                                <div class="col-12 col-md-2 mb-3 float-right d-none d-sm-block">
                                                    <a class="float-right btn btn-secondary"
                                                       style="font-size: 15px; text-decoration: none"
                                                       target="_blank"
                                                       href="{{route('car.show',['username'=>$advertise->user->username,'random'=>$advertise->random_id])}}">
                                                        مشاهده
                                                    </a>
                                                </div>

                                            </div>
                                        </div>

                                    </div>

                                </div>
                            @endif
                        @endforeach

                    @else

                        <div class="col-12 mb-5 text-center p-4 shadow ads-box bg-wht radius-10 shadow">
                            <span>آگهی پیدا نشد.</span>
                            <hr>
                            <a class="btn detail-btn" data-toggle="modal" data-target="#car-reminder" href="">موجود شد
                                بهم خبر بده</a>
                        </div>
                        <!-- Modal -->
                        <div class="modal fade" id="car-reminder" tabindex="-1" role="dialog"
                             aria-labelledby="car-reminder-modal" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="car-reminder-modal">بهم خبر بده</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="reminder-form" action="{{route('car.reminder.store')}}"
                                              method="POST" role="form">
                                            @csrf
                                            <div class="form-group">
                                                <select class="form-control" required name="brand" id="brand-reminer">
                                                    <option value="{{null}}">برند را انتخاب کنید</option>
                                                    @if(request()->brand != null && request()->brand != "all")
                                                        @foreach($brands as $brand)
                                                            <option
                                                                {{$brand->nameEn == request()->brand?"selected":""}} value="{{$brand->id}}">{{$brand->title}}</option>
                                                        @endforeach
                                                    @else
                                                        @foreach($brands as $brand)
                                                            <option
                                                                value="{{$brand->id}}">{{$brand->title}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <select class="form-control" required name="model" id="model-reminder">
                                                    <option value="{{null}}">مدل را انتخاب کنید</option>
                                                    @if(request()->brand != null && request()->brand != "all")
                                                        @if(isset(request()->model))
                                                            @foreach($brand->findIt(request()->brand)->models as $model)
                                                                <option
                                                                    {{$model->nameEn == request()->model ? "selected":""}}
                                                                    value="{{$model->id}}">{{$model->title}}</option>
                                                            @endforeach
                                                        @else
                                                            @foreach($brand->findIt(request()->brand)->models as $model)
                                                                <option
                                                                    value="{{$model->id}}">{{$model->title}}</option>
                                                            @endforeach
                                                        @endif
                                                    @endif
                                                </select>
                                            </div>

                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="">قیمت از (تومان)</label>
                                                        <input class="form-control number-only" name="price1"
                                                               id="price1"
                                                               type="text" value="{{old('price1')}}"
                                                               onkeyup="javascript:FormatNumber('price1','price1-span');">
                                                        <input disabled id="price1-span" placeholder="قیمت به تومان">
                                                        <span class="badge badge-danger alert-price1"></span>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="">قیمت تا (تومان)</label>
                                                        <input class="form-control number-only" name="price2"
                                                               id="price2"
                                                               value="{{old('price2')}}"
                                                               type="text"
                                                               onkeyup="javascript:FormatNumber('price2','price2-span');">
                                                        <input disabled id="price2-span" placeholder="قیمت به تومان">
                                                        <span class="badge badge-danger alert-price2"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="">ایمیل جهت اطلاع به شما</label>
                                                <input class="form-control" required name="email" id="email"
                                                       type="email">
                                            </div>

                                            <div class="row justify-content-center">
                                                <button type="submit" class="btn btn-primary">ثبت درخواست</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

            <div class="col-11 col-md-3 ads-box bg-wht radius-10 p-4">

                @if(isset(request()->brand) && !isset(request()->model))
                    <p>فیلتر ها</p>
                    <div class="hr-style my-2"></div>
                    <div class="row">
                        <a class="delete-filter ml-2"
                           href="{{route('car.filter.get')}}">{{$data->brandWithNameEn(request()->brand)->title}}<img
                                src="{{asset('files/other/images/delete-icon.png')}}"></a>
                    </div>
                @elseif(isset(request()->brand) && isset(request()->model))
                    <a
                        href="{{route('car.page',['brand_slug'=>request()->brand,'model_slug'=>request()->model])}}">
                        <h2 class="mb-3 text-center detail-btn p-2">
                            نظرات کاربران
                            درباره {{$data->brandWithNameEn(request()->brand)->title}} {{$data->carModel(request()->brand,request()->model)->title}}
                        </h2>
                    </a>

                    <p>فیلتر ها</p>
                    <div class="hr-style my-2"></div>
                    <div class="row">
                        <a class="delete-filter ml-2"
                           href="{{route('car.filter.get')}}">{{$data->brandWithNameEn(request()->brand)->title}}<img
                                src="{{asset('files/other/images/delete-icon.png')}}"></a>
                        <a class="delete-filter ml-2"
                           href="{{route('car.filter.get',request()->brand)}}">{{$data->carModel(request()->brand,request()->model)->title}}
                            <img src="{{asset('files/other/images/delete-icon.png')}}"></a>
                    </div>
                @endif

                @if(request()->path() != 'car' && isset(request()->brand) && !isset(request()->model))
                    <h3 class="my-3">مدل های موجود</h3>
                    <div class="hr-style my-2"></div>
                    <div class="row">
                        @foreach($data->brandWithNameEn(request()->brand)->models as $model)
                            @if($model->cars->count()>0)
                                <div class="col-5 p-1 mx-1 bg-gray my-1">
                                    <a class="search-filter-a ml-2"
                                       href="{{route('car.filter.get',['brand'=>request()->brand,'model'=>$model->nameEn])}}">{{$model->title}}</a>
                                    <span class=" px-2 cat-span">{{$model->cars->count()}}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif


                <form
                    action="@if(isset(request()->brand)){{route('car.filter',['brand'=>request()->brand,'model'=>request()->model])}} @else {{route('car.filter')}} @endif"
                    method="get" role="form"
                    id="search_form">

                    <div class="row justify-content-center mt-5">
                        <div class="col-12">
                            <div class="form-group">
                                <select class="chosen-select form-control" name="" id="brand">
                                    <option value="{{null}}">همه ی برند ها</option>
                                    @if(request()->brand != null && request()->brand != "all")
                                        @foreach($brands as $brand)
                                            @if($brand->cars->count()>0)
                                                <option
                                                    {{$brand->nameEn == request()->brand?"selected":""}} value="{{$brand->nameEn}}">{{$brand->title}}</option>
                                            @endif
                                        @endforeach
                                    @else
                                        @foreach($brands as $brand)
                                            @if($brand->cars->count()>0)
                                                <option value="{{$brand->nameEn}}">{{$brand->title}}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <select class="form-control" name="" id="model">
                                    <option value="{{null}}">همه ی مدل ها</option>
                                    @if(request()->brand != null && request()->brand != "all")
                                        @if(isset(request()->model))
                                            @foreach($brand->findIt(request()->brand)->models as $model)
                                                @if($model->cars->count() >0)
                                                    <option
                                                        {{$model->nameEn == request()->model ? "selected":""}}
                                                        value="{{$model->nameEn}}">{{$model->title}}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            @foreach($brand->findIt(request()->brand)->models as $model)
                                                @if($model->cars->count() >0)
                                                    <option
                                                        value="{{$model->nameEn}}">{{$model->title}}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <select class="chosen-select form-control" name="ostan" id="ostan">
                                    <option value="all">همه ی استان ها</option>

                                    @if(request()->ostan != null && request()->ostan != "all")
                                        @foreach($ostans as $ostan)
                                            @if($ostan->advertises->count()>0)
                                                <option
                                                    {{$ostan->id == request()->ostan?"selected":""}} value="{{$ostan->id}}">{{$ostan->title}}</option>
                                            @endif
                                        @endforeach
                                    @else
                                        @foreach($ostans as $ostan)
                                            @if($ostan->advertises->count()>0)
                                                <option value="{{$ostan->id}}">{{$ostan->title}}</option>
                                            @endif
                                        @endforeach
                                    @endif

                                </select>
                                @error('ostan')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <select class="form-control" name="city" id="city">
                                    <option value="all">همه ی شهر ها</option>

                                    {{--                                    اگر صفحه ای که توشیم استان مشخص شده بود چون اگه مشخص نباشه در یو ار ال با جاوااسکریپت انتخاب میشه با هر بار انتخاب--}}
                                    @if(request()->ostan != null && request()->ostan != "all")
                                        {{--                                        اگخ شهر از قبل انتخاب شده بود--}}
                                        @if(request()->city != "all")
                                            {{--                                            همه ی شهر های استان انتخاب شده رو حلقه بزن--}}
                                            @foreach($ostan->findIt(request()->ostan)->cities as $city)
                                                {{--                                                اگه شهره اگهی داشت --}}
                                                @if($city->advertises->count() > 0)
                                                    <option
                                                        {{$city->id == request()->city ? "selected":""}}
                                                        value="{{$city->id}}">{{$city->title}}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            @foreach($ostan->findIt(request()->ostan)->cities as $city)
                                                @if($city->advertises->count() > 0)
                                                    <option
                                                        value="{{$city->id}}">{{$city->title}}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endif

                                </select>
                                @error('city')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <input placeholder="از سال"
                                       value="{{request()->from_year != null? request()->from_year:""}}"
                                       class="form-control number-only" type="text" onfocus="focusFunction(3)"
                                       onfocusout="focusOutFunction(3)" name="from_year">
                                <div id="message-3" style="display: none">
                                    <img src="{{asset('files/other/images/circle.webp')}}">
                                    <span style="font-size: 14px">چهار رقمی شمسی مثلا : 1390</span>
                                </div>
                                @error('from_year')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <input placeholder="تا سال"
                                       value="{{request()->to_year != null? request()->to_year:""}}"
                                       class="form-control number-only" type="text" onfocus="focusFunction(4)"
                                       onfocusout="focusOutFunction(4)" name="to_year">
                                <div id="message-4" style="display: none">
                                    <img src="{{asset('files/other/images/circle.webp')}}">
                                    <span style="font-size: 14px">چهار رقمی شمسی مثلا : 1398</span>
                                </div>
                                @error('to_year')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        <div class="col-6">
                            <div class="form-group">
                                <input placeholder="از قیمت"
                                       value="{{request()->from_price != null? request()->from_price:""}}"
                                       class="form-control number-only" type="text" onfocus="focusFunction(1)"
                                       onfocusout="focusOutFunction(1)" name="from_price">
                                <div id="message-1" style="display: none">
                                    <img src="{{asset('files/other/images/circle.webp')}}">
                                    <span style="font-size: 14px">عدد برحسب ملیون تومان</span>
                                </div>
                                @error('from_price')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <input placeholder="تا قیمت"
                                       value="{{request()->to_price != null? request()->to_price:""}}"
                                       class="form-control number-only" type="text" onfocus="focusFunction(2)"
                                       onfocusout="focusOutFunction(2)" name="to_price">
                                <div id="message-2" style="display: none">
                                    <img src="{{asset('files/other/images/circle.webp')}}">
                                    <span style="font-size: 14px">عدد برحسب ملیون تومان</span>
                                </div>
                                @error('to_price')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>


                    <div class="row justify-content-center mt-4">
                        <div class="col-12 mt-1 text-center">
                            <button type="submit" class="btn btn-danger w-100">
                                فیلتر کردن
                            </button>
                        </div>
                    </div>


                </form>

                <h3 class="my-3">برند خودرو</h3>
                <div class="hr-style-home mb-2"></div>
                <div class="row">
                    @foreach($data->brandsSortByCarCount() as $brand)
                        @if($brand->nameEn != request()->brand)
                            @if($brand->cars->count()>0)
                                <div class="col-5 p-1 mx-1 bg-gray my-1">
                                    <a class="search-filter-a ml-2"
                                       href="{{route('car.filter.get',$brand->nameEn)}}">{{$brand->title}}</a>
                                    <span class="ml-auto px-2 cat-span">{{$brand->cars->count()}}</span>
                                </div>
                            @endif
                        @endif
                    @endforeach
                </div>

                <h3 class="my-3">استان</h3>
                <div class="hr-style-home mb-2"></div>
                <div class="row">
                    @foreach($ostans as $ostan)
                        @if($ostan->id != request()->ostan)
                            @if(isset(request()->brand) && !isset(request()->model))
                                @if($ostan->hasBrand(request()->brand))

                                    <div class="col-5 p-1 mx-1 bg-gray my-1">
                                        <a class="search-filter-a ml-2"
                                           href="/cars/{{request()->brand}}?ostan={{$ostan->id}}"
                                        >{{$ostan->title}}
                                        </a>
                                        <span class=" px-2 cat-span">{{$ostan->advertises->count()}}</span>
                                    </div>

                                @endif
                            @elseif(isset(request()->brand) && isset(request()->model))
                                @if($ostan->hasBrand(request()->brand) && $ostan->hasModel(request()->model))
                                    <div class="col-5 p-1 mx-1 bg-gray my-1">
                                        <a class="search-filter-a ml-2"
                                           href="/cars/{{request()->brand}}/{{request()->model}}?ostan={{$ostan->id}}"
                                        >{{$ostan->title}}
                                        </a>
                                        <span class=" px-2 cat-span">{{$ostan->advertises->count()}}</span>
                                    </div>

                                @endif
                            @elseif($ostan->advertises->count()>0)
                                <div class="col-5 p-1 mx-1 bg-gray my-1">
                                    <a class="search-filter-a ml-2"
                                       href="/cars/?ostan={{$ostan->id}}"
                                    >{{$ostan->title}}
                                    </a>
                                    <span class=" px-2 cat-span">{{$ostan->advertises->count()}}</span>
                                </div>
                            @endif
                        @else
                            <div class="col-5 p-1 mx-1 bg-gray my-1">
                                <a class=" ml-2 cat-selected"
                                   disabled=""
                                >{{$ostan->title}}
                                </a>
                                <span class=" px-2 cat-span">{{$ostan->advertises->count()}}</span>
                            </div>
                        @endif
                    @endforeach
                </div>

                @if(request()->ostan != "all" && isset(request()->ostan))
                    <h3 class="my-3">شهر</h3>
                    <div class="hr-style-home mb-2"></div>
                    <div class="row">
                        @foreach($data->ostan(request()->ostan)->cities as $ct)
                            @if($ct->advertises->count()>0)
                                @if(request()->city!= $ct->id)
                                    @if(isset(request()->brand) && !isset(request()->model))
                                        <div class="col-5 p-1 mx-1 bg-gray my-1">
                                            <a class="search-filter-a ml-2"
                                               href="/cars/{{request()->brand}}?ostan={{request()->ostan}}&city={{$ct->id}}"
                                            >{{$ct->title}}
                                            </a>
                                            <span class=" px-2 cat-span">{{$ct->advertises->count()}}</span>
                                        </div>
                                    @elseif(isset(request()->brand) && isset(request()->model))
                                        <div class="col-5 p-1 mx-1 bg-gray my-1">
                                            <a class="search-filter-a ml-2"
                                               href="/cars/{{request()->brand}}/{{request()->model}}?ostan={{request()->ostan}}&city={{$ct->id}}"
                                            >{{$ct->title}}
                                            </a>
                                            <span class=" px-2 cat-span">{{$ct->advertises->count()}}</span>
                                        </div>
                                    @else
                                        <div class="col-5 p-1 mx-1 bg-gray my-1">
                                            <a class="search-filter-a ml-2"
                                               href="/cars/?ostan={{request()->ostan}}&city={{$ct->id}}"
                                            >{{$ct->title}}
                                            </a>
                                            <span class=" px-2 cat-span">{{$ct->advertises->count()}}</span>
                                        </div>
                                    @endif
                                @else
                                    <div class="col-5 p-1 mx-1 bg-gray my-1">
                                        <a class="cat-selected ml-2"
                                           disabled=""
                                        >{{$ct->title}}
                                        </a>
                                        <span class=" px-2 cat-span">{{$ct->advertises->count()}}</span>
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                @endif

                <hr class="mt-5">
                <h1 class="font-18">آگهی خرید و فروش
                    خودرو {{request()->brand != null ? $data->brandWithNameEn(request()->brand)->title : ""}} {{request()->model != null ? $data->brandModel(request()->brand,request()->model)->title : ""}} {{request()->ostan != "all" && request()->ostan != null ? $data->ostan(request()->ostan)->title:""}}</h1>


                <div id="pos-article-display-sticky-62835"></div>

            </div>

        </div>

    </section>


@endsection

@section('script')


    <script type="text/javascript"
            src="{{asset('assets/js/car-home.js')."?lm=".filemtime('assets/js/car-home.js')}}"></script>

@endsection
