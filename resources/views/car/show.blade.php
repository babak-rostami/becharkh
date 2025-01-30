@extends('index')


@section('title')
    {{$advertise->title}}
@endsection


@section('style')
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css"/>

    <style>
        .swiper-container {
            width: 100%;
            height: 100%;
        }

        .swiper-slide {
            text-align: center;
            font-size: 18px;

            /* Center slide text vertically */
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            -webkit-justify-content: center;
            justify-content: center;
            -webkit-box-align: center;
            -ms-flex-align: center;
            -webkit-align-items: center;
            align-items: center;
        }
    </style>

    <meta name="title" content="{{$advertise->title}} - {{$advertise->ostann->title}} | تند ایران">

    <meta name="description"
          content="{{$advertise->body}} - فروش {{$advertise->adModel->brand->title}} {{$advertise->adModel->model->title}} - {{$advertise->title}} - در {{$advertise->ostann->title}} - کارکرد {{$advertise->adModel->kilometer}}">

    <meta name="keywords"
          content="خرید خودرو , فروش خودرو , car ,تند ایران , tondiran , {{$advertise->title}} ,{{$advertise->adModel->brand->title}},{{$advertise->adModel->model->title}}">

    <link href="{{request()->url()}}" rel="canonical">
    <meta name="robots" content="noindex">

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
            <li class="breadcrumb-item"><a href="{{route('home')}}">تند ایران</a></li>
            <li class="breadcrumb-item"><a href="{{route('car.filter.get')}}">خودرو</a></li>
            <li class="breadcrumb-item"><a
                    href="{{route('car.filter.get',$advertise->adModel->brand->nameEn)}}">{{$advertise->adModel->brand->title}}</a>
            </li>
            <li class="breadcrumb-item"><a
                    href="{{route('car.filter.get',['brand'=>$advertise->adModel->brand->nameEn,'model'=>$advertise->adModel->model->nameEn])}}">{{$advertise->adModel->model->title}}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{$advertise->title}}</li>
        </ol>
    </nav>

    <section id="main" class="mb-5 mt-2 bg-wht">


        <div class="row justify-content-around">
            <div class="col-md-5 col-11 p-4 radius-10 order-0 order-md-2 m-1">

                @if(!$advertise->images->isEmpty())
                    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators">
                            @foreach($advertise->images as $key=>$image)
                                <li data-target="#carouselExampleIndicators"
                                    data-slide-to="{{$key}}" @if($key==0) class="active"@endif></li>
                            @endforeach
                        </ol>
                        <div class="carousel-inner">
                            @foreach($advertise->images as $key=>$image)
                                <div class="carousel-item @if($key==0) active @endif">
                                    <img class="d-block w-100" src="{{asset('files/advertise/images/'.$image->image)}}"
                                         alt="First slide">
                                </div>
                            @endforeach
                        </div>
                        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button"
                           data-slide="next">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button"
                           data-slide="prev">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                @else
                    <img class="w-100" src="{{asset($advertise->image())}}">
                @endif


            </div>

            <div class="col-md-6 col-11 px-4 py-2 radius-10 order-1 order-md-1 m-1">
                <div class="row align-items-center">

                    <div class="col-12 mb-3">
                        <b>اطلاعات فروشنده</b>
                        <div class="row align-items-center my-4">
                            <a target="_blank"
                               href="{{route('user.dashboard',$advertise->user->username)}}"
                               class="text-decoration-none">
                                <img class="avatar-question-home mx-2" src="{{asset($advertise->user->image())}}">
                            </a>
                            <div class="col">
                                <a target="_blank"
                                   href="{{route('user.dashboard',$advertise->user->username)}}"
                                   class="text-decoration-none text-dark">
                                    <span class="font-weight-bold"
                                          style="color: #005cbf">{{$advertise->user->username}}@</span>
                                </a>
                                <br>
                                <span>{{$advertise->user->name}}</span>
                                <br>
                                @if(auth('user')->check())
                                    @if(auth('user')->id() != $advertise->user->id)
                                        @if(auth('user')->user()->isFollow($advertise->user->id))
                                            <a class="btn btn-outline-info"
                                               href="{{route('unfollow',$advertise->user->id)}}">دنبال
                                                شده
                                                <img style="width: 24px" src="{{asset('files/other/images/done.png')}}">
                                            </a>
                                        @else
                                            <a class="btn btn-dark" href="{{route('follow',$advertise->user->id)}}">دنبال
                                                کردن
                                                <img class="ml-2" style="width: 18px"
                                                     src="{{asset('files/other/images/add.png')}}">
                                            </a>
                                        @endif
                                    @endif
                                @else
                                    <a class="btn btn-dark" href="" data-toggle="modal" data-target="#login_user">دنبال
                                        کردن
                                        <img class="ml-2" style="width: 18px"
                                             src="{{asset('files/other/images/add.png')}}">
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="dropdown mr-1">
                            <button class="btn btn-success mt-2 dropdown-toggle" type="button" id="dropdownMenuButton"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                اطلاعات تماس
                                <img style="width: 24px" src="{{asset('files/other/images/phone.png')}}">
                            </button>
                            <div class="dropdown-menu text-center" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item">شماره تماس : {{$advertise->phone}}</a>
                            </div>

                            @if(auth('user')->check())
                                @if(auth('user')->id() != $advertise->user->id)
                                    <div class="modal fade" id="messageuser" tabindex="-1" role="dialog"
                                         aria-labelledby="exampleModalLabel"
                                         aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">ارسال پیام</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{route('user.message.send')}}" method="POST"
                                                          role="form">
                                                        @csrf
                                                        <input type="hidden" name="to_user" value="{{$user->id}}">

                                                        <div class="form-group">
                                                            <label>موضوع</label>
                                                            <input class="form-control" name="title">
                                                        </div>

                                                        <div class="form-group">
                                                            <label>پیام</label>
                                                            <textarea style="height: 150px" class="form-control"
                                                                      name="body"></textarea>
                                                        </div>

                                                        <button type="submit" class="btn btn-primary">ارسال</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <a class="btn btn-info mt-2" data-toggle="modal" data-target="#login_user" href="">پیام
                                    به
                                    فروشنده
                                    <img style="width: 24px;" src="{{asset('files/other/images/message.png')}}">
                                </a>
                            @endif

                            <a class="btn btn-danger mt-2 mr-2" href="" data-toggle="modal"
                               data-target="#advertise-report">
                                گزارش آگهی
                                <img style="width: 24px;" src="{{asset('files/other/images/warning.png')}}">
                            </a>

                            <!-- Modal -->
                            <div class="modal fade" id="advertise-report" tabindex="-1" role="dialog"
                                 aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">گزارش آگهی</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{route('report.advertise')}}" method="post" role="form">
                                                @csrf
                                                <div class="form-group">
                                                    <label for="">چرا این آگهی را گزارش می کنید؟</label>
                                                    <input type="hidden" name="advertise_id" value="{{$advertise->id}}">
                                                    <textarea class="form-control" name="body"></textarea>
                                                </div>

                                                <button type="submit" class="btn btn-primary">ارسال</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                    </div>

                </div>
                <div class="row">

                    <div class="col-12">
                        <b>مشخصات خودرو</b>
                        <h1 class="font-18 text-gray mx-2 mt-4">{{$advertise->title}}
                            در {{$advertise->ostann->title}}</h1>
                        <hr>
                    </div>
                    <div class="col-6">
                        خودرو
                        <img style="width: 24px" src="{{asset('files/other/images/car.png')}}">
                    </div>
                    <div class="col-6">
                        {{$advertise->adModel->brand->title}} {{$advertise->adModel->model->title}}
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        قیمت
                        <img style="width: 24px" src="{{asset('files/other/images/price.png')}}">
                    </div>
                    <div class="col-6">
                        @if(isset($advertise->price))
                            <span>{{number_format((int)$advertise->showPrice())}}</span>
                        @else
                            <span>توافقی</span>
                        @endif
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        کارکرد
                        <img style="width: 24px" src="{{asset('files/other/images/menu.png')}}">
                    </div>
                    <div class="col-6">
                        {{$advertise->adModel->kilometer}}
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        سال تولید
                        <img style="width: 24px" src="{{asset('files/other/images/year.png')}}">
                    </div>
                    <div class="col-6">
                        {{$advertise->adModel->production_year}}
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        نحوه فروش
                        <img style="width: 24px" src="{{asset('files/other/images/how-sell.png')}}">
                    </div>
                    <div class="col-6">
                        @if($advertise->adModel->how_sell==1)
                            نقدی
                        @elseif($advertise->adModel->how_sell == 2)
                            اقساطی
                        @elseif($advertise->adModel->how_sell == 3)
                            نقدی و اقساطی
                        @endif
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        نوع سوخت
                        <img style="width: 24px" src="{{asset('files/other/images/fuel-type.png')}}">
                    </div>
                    <div class="col-6">
                        @if($advertise->adModel->fuel_type==1)
                            بنزین
                        @elseif($advertise->adModel->fuel_type == 2)
                            گازوئیل(دیزل)
                        @elseif($advertise->adModel->fuel_type == 3)
                            دوگانه سوز
                        @elseif($advertise->adModel->fuel_type == 4)
                            هیبریدی
                        @endif
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        محل آگهی
                        <img style="width: 24px" src="{{asset('files/other/images/location.png')}}">
                    </div>
                    <div class="col-6">
                        {{$advertise->ostann->title}}/{{$advertise->shahrr->title}}
                    </div>
                </div>

            </div>

            <div class="col-12 order-2 bg-gray p-4 m-2">
                <p class="font-20">توضیحات</p>
                <p class="textarea-preline">{{$advertise->body}}</p>
            </div>

        </div>


        {{--آگهی های مشابه--}}
        <div class="row mx-2 justify-content-center">
            @if(get_class($advertise->adModel) == "App\Models\Car")
                @if(!$advertise->adModel->brand->cars->isEmpty())
                    <div class="col-12">
                        <h2 class="my-4 mx-2 text-gray font-20">آگهی های مشابه</h2>
                    </div>
                    <div class="col-12 hr-style mb-2"></div>

                    <div class="swiper-container my-4 z-index-0">
                        <div class="swiper-wrapper">
                            @foreach($advertise->adModel->brand->cars as $car)
                                @if($car->advertise($car->id,"car") != null && $car->advertise($car->id,"car")->id != $advertise->id)
                                    <div class="swiper-slide">
                                        <div class="row justify-content-center">
                                            <div class="col-12">
                                                <img class="w-100 text-center" style=""
                                                     src="{{asset($car->advertise($car->id,"car")->image())}}"
                                                     alt="">
                                            </div>
                                            <div class="col-11 text-left shadow-lg"
                                                 style="border-bottom: 2px solid #005cbf">
                                                <a class="text-decoration-none" style="color: #000000"
                                                   href="{{route('car.show',['username'=>$car->advertise($car->id,"car")->user->username,'random'=>$car->advertise($car->id,"car")->random_id])}}"
                                                >

                                                    <h2 class="p-2 font-18"
                                                        style="height: 70px">{{$car->advertise($car->id,"car")->title}}</h2>

                                                    <p class="col-12">{{jdate($car->advertise($car->id,"car")->created_at)->ago()}}</p>
                                                    <hr>
                                                    <p style="height: 50px"
                                                       class="col-12">{{$car->advertise($car->id,"car")->ostann->title}}
                                                        -{{$car->advertise($car->id,"car")->shahrr->title}}</p>

                                                    <hr>
                                                    <p class="pl-3">{{$car->advertise($car->id,"car")->showPrice()}}</p>

                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <!-- Add Pagination -->
                        <div class="swiper-pagination"></div>
                    </div>

                @endif
            @endif
        </div>

    </section>




@endsection


@section('script')
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper('.swiper-container', {
            spaceBetween: 20,
            freeMode: true,
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },
            breakpoints: {
                // when window width is <= 480px
                480: {
                    slidesPerView: 1,
                    spaceBetweenSlides: 20
                },
                // when window width is <= 600px
                600: {
                    slidesPerView: 2,
                    spaceBetweenSlides: 30
                },
                // when window width is <= 800px
                800: {
                    slidesPerView: 3,
                    spaceBetweenSlides: 30
                },
                // when window width is <= 950px
                950: {
                    slidesPerView: 4,
                    spaceBetweenSlides: 30
                }
            }
        });
    </script>
@endsection
