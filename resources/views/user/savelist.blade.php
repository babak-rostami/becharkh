@extends('index')


@section('title')
    علاقه مندی ها
@endsection


@section('style')
    <meta name="robots" content="noindex">
@endsection


@section('content')

    <div class="row justify-content-center mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('user.dashboard',auth('user')->user()->username)}}">حساب کاربری</a></li>
                <li class="breadcrumb-item active" aria-current="page">علاقه مندی ها</li>
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

            @if(!$saveItems->isEmpty())
                @foreach($saveItems as $saveItem)
                    <div class="col-md-3 col-10 my-1 mx-2 shadow ads-box bg-wht radius-10 shadow">

                        <div class="row align-items-center justify-content-center">
                            <div class="col-12">
                                <a class="decor-none"
                                   href="{{route('car.show',['ad_random'=>$saveItem->random_id , 'ad_slug'=>$saveItem->slug])}}">

                                    <img style="width: 100%; height: 200px"
                                         src="{{asset($saveItem->image())}}">
                                    <h2 class="font-20 mt-2 col-12">{{$saveItem->title}}</h2>
                                    <p class="col-12">{{jdate($saveItem->created_at)->ago()}}</p>
                                    <hr>
                                    <p class="col-12">{{$saveItem->ostann->title}}-{{$saveItem->shahrr->title}}</p>
                                    <div class="hr-style my-2"></div>
                                </a>
                            </div>
                            <div class="col-12 my-2">
                                <span>{{$saveItem->showPrice()}}</span>
                                @if(auth('user')->user()->isAdSave($saveItem->id))
                                    <a class="decor-none" href="{{route('ad.save.remove',$saveItem->id)}}">
                                        <img class="save-icon-size float-right"
                                             src="{{asset('files/other/images/saved.png')}}">
                                    </a>
                                @else
                                    <a class="decor-none" href="{{route('ad.save',$saveItem->id)}}">
                                        <img class="save-icon-size float-right"
                                             src="{{asset('files/other/images/save.png')}}">
                                    </a>
                                @endif
                            </div>
                        </div>

                    </div>
                @endforeach
            @else
                <div class="col-12 my-5 p-5 text-center">
                    <p class="text-gray">لیست علاقه مندی ها خالی می باشد</p>
                </div>
            @endif
        </div>

        <div class="row justify-content-center mt-2">
            {{$saveItems->links()}}
        </div>

    </section>


@endsection


@section('script')

@endsection
