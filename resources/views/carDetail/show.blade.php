@extends('index')

@section('title'){{ "مشخصات ". $detail->brand->title." " .$detail->model->title." ".$detail->production_year}}@endsection

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

    <meta name="title"
          content="{{ "مشخصات ". $detail->brand->title." " .$detail->model->title . " ". $detail->production_year . " ".$detail->cylinder}}">

    <meta name="description"
          content="بررسی مشخصات فنی {{$detail->brand->title}} {{$detail->model->title}} {{$detail->production_year}} {{$detail->cylinder}} - بررسی نقاط قوت و ضعف {{$detail->brand->title}} {{$detail->model->title}} {{$detail->production_year}} {{$detail->cylinder}}">

    <meta name="keywords"
          content="بررسی مشخصات فنی خودرو , بررسی مشخصات خودرو , {{$detail->brand->title}}, {{$detail->model->title}}">

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

    <div class="row shadow ads-box bg-wht radius-10 p-4 mt-4 mx-0 mx-md-5">
        <div class="col-12 col-lg-5 text-center">
            <img class="w-100" alt="{{$detail->brand->title}} {{$detail->model->title}} {{$detail->production_year}}"
                 title="{{$detail->brand->title}} {{$detail->model->title}} {{$detail->production_year}}"
                 src="{{asset('files/carmodel/images/'.$detail->image)}}">

            <div class="row mt-4 justify-content-center">
                <div class="btn-group mt-1">
                    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                        {{$detail->production_year}} - {{$detail->cylinder}}
                    </button>
                    <div class="dropdown-menu">
                        @foreach($detail->model->details as $key => $dt)
                            <a class="dropdown-item"
                               href="{{route('car.detail.show',['brand_slug'=> $detail->brand->nameEn, 'model_slug'=>$detail->model->nameEn])}}?s={{$key}}"
                            >{{$dt->production_year}}
                                - {{$dt->cylinder}}</a>
                        @endforeach

                    </div>
                </div>
                <a class="text-center detail-btn p-2 ml-4 mt-1"
                   href="{{route('car.filter.get',['brand'=> $detail->brand->nameEn, 'model'=> $detail->model->nameEn])}}">
                    قیمت بازار
                </a>
            </div>
        </div>
        <div class="col-12 col-lg-7">
            <h1 class="bold-font-title">
                مشخصات {{$detail->brand->title}} {{$detail->model->title}} {{$detail->production_year}} {{$detail->cylinder}}</h1>
            <p>{!! $detail->description !!}</p>
            <div class="mt-5">
                @if($detail->shortlink != null)
                    <span>لینک کوتاه صفحه</span>
                    <input id="short_link"
                           value="{{route('shortlink.show',$detail->shortlink->short_link)}}"/>
                    <button onclick="copyToClipboard()">کپی</button>
                    <span class="badge badge-success" style="display: none" id="saved-span">ذخیره شد</span>
                @endif
                <br>
                <a class="btn btn-outline-dark mt-2" href="" data-toggle="modal" data-target="#editInformaionModal">ویرایش
                    و
                    تکمیل اطلاعات<img class="mx-1" style="width: 20px"
                                      src="{{asset('files/other/images/edit.png')}}"></a>

                <!-- edit page information Modal -->
                <div class="modal fade" id="editInformaionModal" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">ویرایش یا تکمیل اطلاعات صفحه</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <form action="{{route('edit.information')}}" method="POST" role="form">
                                    @csrf

                                    <input type="hidden" name="url" value="{{request()->url()}}">

                                    @if(!auth('user')->check())
                                        <div class="form-group">
                                            <label for="">ایمیل</label>
                                            <input type="email" class="form-control" name="email"
                                                   placeholder="ایمیل...">
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <label for="">اطلاعات تکمیلی یا ایرادات صفحه را توضیح دهید...</label>
                                        <textarea class="form-control" style="width: 100%; height: 200px"
                                                  name="body"></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary">ثبت</button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <div class="row justify-content-center mx-0 mx-md-5 p-4">
        <div class="col-12 col-md-6 mb-4">
            <h2 class="text-center" style="background-color: #007965 ; color: #ffffff">نقاط قوت</h2>
            <ul class="text-center list-group">
                @foreach(explode(',',$detail->power_points) as $power)
                    <li class="list-group-item">{{$power}}</li>
                @endforeach
            </ul>
        </div>
        <div class="col-12 col-md-6 mb-4">
            <h2 class="text-center" style="background-color: #ec4646 ; color: #ffffff">نقاط ضعف</h2>
            <ul class="text-center list-group">
                @foreach(explode(',',$detail->weak_points) as $weak)
                    <li class="list-group-item">{{$weak}}</li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="row mx-0 mx-md-5 p-4">
        <div class="col-12 text-center p-2">
            <h2 class="p-2" style="background-color: #0f1123 ; color: #ffffff">
                مشخصات {{$detail->model->title}} {{$detail->brand->title}}</h2>

            <table class="table table-striped">
                <tbody>
                <tr>
                    <td>برند</td>
                    <td>{{$detail->brand->title}}</td>
                </tr>
                <tr>
                    <td>مدل</td>
                    <td>{{$detail->model->title}}</td>
                </tr>
                <tr>
                    <td>حجم موتور</td>
                    <td>{{$detail->engine_volume}}</td>
                </tr>
                <tr>
                    <td>شتاب</td>
                    <td>{{$detail->acceleration}}</td>
                </tr>
                <tr>
                    <td>قدرت موتور</td>
                    <td>{{$detail->engine_power}}</td>
                </tr>
                <tr>
                    <td>گشتاور</td>
                    <td>{{$detail->torque}}</td>
                </tr>
                <tr>
                    <td>حداکثر سرعت</td>
                    <td>{{$detail->max_speed}}</td>
                </tr>
                <tr>
                    <td>گیربکس</td>
                    <td>{{$detail->gearbox}}</td>
                </tr>
                <tr>
                    <td>دیفرانسیل</td>
                    <td>{{$detail->differential}}</td>
                </tr>
                <tr>
                    <td>کلاس بدنه</td>
                    <td>{{$detail->body_class}}</td>
                </tr>
                <tr>
                    <td>وزن خودرو</td>
                    <td>{{$detail->car_weight}}</td>
                </tr>
                <tr>
                    <td>مصرف سوخت</td>
                    <td>{{$detail->fuel_consumption}}</td>
                </tr>
                <tr>
                    <td>حجم باک</td>
                    <td>{{$detail->buck_volume}}</td>
                </tr>
                <tr>
                    <td>کشور سازنده</td>
                    <td>{{$detail->country}}</td>
                </tr>
                <tr>
                    <td>سیستم ترمز</td>
                    <td>{{$detail->brake_system}}</td>
                </tr>
                <tr>
                    <td>سیستم مدیا</td>
                    <td>{{$detail->media_system}}</td>
                </tr>
                <tr>
                    <td>سیستم آینه و شیشه</td>
                    <td>{{$detail->mirror_glass_system}}</td>
                </tr>
                <tr>
                    <td>روشنایی</td>
                    <td>{{$detail->lighting}}</td>
                </tr>
                <tr>
                    <td>رفاهی</td>
                    <td>{{$detail->welfare}}</td>
                </tr>
                @if(isset($detail->others))
                    <tr>
                        <td>سایر امکانات</td>
                        <td>{{$detail->others}}</td>
                    </tr>
                @endif
                </tbody>
            </table>

        </div>
    </div>

    <hr>


    <div class="row justify-content-center">
        <div class="col-10">

            <h3>پیشنهادات</h3>

            <div class="swiper-container my-4 z-index-0">
                <div class="swiper-wrapper">
                    @foreach($detail->brand->models as $key => $model)
                        @if($model->details->count() > 0 && $model->id != $detail->model->id)
                            <div class="swiper-slide">
                                <div class="row justify-content-center">
                                    <div class="col-12">
                                        <img class="w-100 text-center"
                                             src="{{asset('files/carmodel/images/'.$model->details->last()->image)}}"
                                             alt="{{$model->brand->title}} {{$model->title}}"
                                             title="{{$model->brand->title}} {{$model->title}}">
                                    </div>
                                    <div class="col-12 text-center">
                                        <a href="{{route('car.detail.show',['brand_slug'=>$detail->brand->nameEn,'model_slug'=>$model->nameEn])}}">
                                            <h2 class="p-2"
                                                style="background-color: #005cbf; color: #ffffff ; font-size: 22px">{{$model->title}}</h2>
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
        </div>
    </div>

    <hr>

    <div class="row justify-content-center my-4" id="comments">
        <div class="col-10 col-md-8">

            <div class="row align-items-center">
                <div class="col-4">
                    <h3 class="font-20 font-weight-bold ml-2">نظرات کاربران</h3>
                </div>
                <div class="col-8">
                    <a href="" class="btn btn-outline-primary" data-toggle="modal" data-target="#comment"><img
                            class="mx-2" src="{{asset('files/other/images/reply.png')}}">برای ثبت نظر کلیک کنید</a>
                </div>
            </div>

        </div>

        <div class="modal fade" id="comment" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">ثبت نظر</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form action="{{route('detail.comment.store')}}" method="POST" role="form">
                            @csrf

                            <input type="hidden" name="detail_id" value="{{$detail->id}}">

                            <div class="form-group">
                            <textarea required class="form-control" style="width: 100%; height: 150px; resize: none"
                                      name="body"
                                      placeholder="دیدگاه خود را بنویسید...">{{old('body')}}</textarea>
                                @error('body')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <input type="text" required
                                               class="form-control"
                                               name="name"
                                               value="{{old('name')}}" placeholder="نام...">
                                        @error('name')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <input type="email" required class="form-control"
                                               value="{{old('email')}}"
                                               name="email"
                                               placeholder="ایمیل...">
                                    </div>
                                    @error('email')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success">فرستادن دیدگاه</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>


        @if($detail->comments->count()>0)
            @foreach($detail->comments as $comment)
                <div class="col-10 col-md-8 shadow bg-wht p-4 radius-10 my-2">

                    <a target="_blank" class="text-decoration-none">
                        <img class="rounded-circle mr-2" style="width: 50px; height: 50px"
                             src="{{asset('files/other/images/profile.jpg')}}"
                        >
                        <span class="text-bold-style">{{$comment->name}}</span>
                    </a>
                    <span class="mx-2">({{jdate($comment->created_at)->ago()}})</span>

                    <p>{{$comment->body}}</p>
                    <a class="text-decoration-none" data-toggle="modal" data-target="#reply-to-comment-{{$comment->id}}"
                       href="">پاسخ
                        <img
                            src="{{asset('files/other/images/reply.png')}}">
                    </a>
                </div>
                @foreach($comment->replies as $reply)
                    <div class="col-10 col-md-8 shadow p-4 radius-10 ml-5 mb-2">
                        <img class="rounded-circle mr-2" style="width: 50px; height: 50px"
                             src="{{asset('files/other/images/profile.jpg')}}"
                        >
                        <span class="text-bold-style">{{$reply->name}}</span><span class="mx-2">({{jdate($reply->created_at)->ago()}})</span>
                        <span class="badge badge-info">پاسخ به {{$reply->replyTo->name}}</span>
                        <p>{{$reply->body}}</p>
                        <a class="text-decoration-none" data-toggle="modal" data-target="#reply-to-reply-{{$reply->id}}"
                           href="">پاسخ<img
                                src="{{asset('files/other/images/reply.png')}}"></a>
                    </div>

                    <div class="modal fade" id="reply-to-reply-{{$reply->id}}" tabindex="-1" role="dialog"
                         aria-labelledby="exampleModalLabel"
                         aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">پاسخ</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form class="mb-4" action="{{route('detail.comment.store')}}" method="post"
                                          role="form">
                                        <div class="form-group">
                                            @csrf
                                            <input type="hidden" name="detail_id" value="{{$detail->id}}">
                                            <input type="hidden" name="parent_id" value="{{$comment->id}}">
                                            <input type="hidden" name="reply_id" value="{{$reply->id}}">

                                            <textarea required name="body"
                                                      style="width: 100%; height: 150px;resize: none"
                                                      class="form-control"
                                                      placeholder="نظر خود را بنویسید"></textarea>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="name"
                                                           placeholder="نام...">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" name="email"
                                                           placeholder="ایمیل...">
                                                </div>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-info">ثبت نظر</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                @endforeach

            <!-- Modal -->
                <div class="modal fade" id="reply-to-comment-{{$comment->id}}" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">پاسخ</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form class="mb-4" action="{{route('detail.comment.store')}}" method="post" role="form">

                                    <div class="form-group">
                                        @csrf
                                        <input type="hidden" name="detail_id" value="{{$detail->id}}">
                                        <input type="hidden" name="parent_id" value="{{$comment->id}}">
                                        <input type="hidden" name="reply_id" value="{{$comment->id}}">

                                        <textarea required name="body" style="width: 100%; height: 150px;resize: none"
                                                  class="form-control"
                                                  placeholder="نظر خود را منتشر کنید"></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="name"
                                                       placeholder="نام...">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="email"
                                                       placeholder="ایمیل...">
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-info">ثبت نظر</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-10 text-center bg-wht px-4 py-2 radius-10 m-2 shadow">
                <p class="text-gray">نظری ثبت نشده است</p>
            </div>
        @endif

    </div>

    <button onClick="document.getElementById('comments').scrollIntoView();"
            class="fixed-bottom mb-4 ml-auto mr-4 px-2 go-to-comment-btn text-decoration-none" style="">
        نظرات کاربران
        <span>{{$detail->comments->count()>0? $detail->comments->count() . "+" : "0"}}</span>
    </button>

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


    <script>
        function copyToClipboard() {
            document.getElementById("short_link").select();
            document.execCommand('copy');
            $('#saved-span').css('display', 'inline');
        }
    </script>

@endsection
