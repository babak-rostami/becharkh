@extends('index')

@section('style')
    <meta name="robots" content="noindex">
@endsection


@section('title')بسته های شما@endsection

@section('content')

    <div class="row justify-content-center mt-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('user.dashboard',auth('user')->user()->username)}}">حساب
                        کاربری</a></li>
                <li class="breadcrumb-item active" aria-current="page">بسته های آگهی شما</li>
            </ol>
        </nav>
    </div>

    <div class="row justify-content-center">
        <div class="col-10">
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
        </div>
    </div>


    <div class="row justify-content-center">

        <div class="col-10 text-center my-3">
            <div class="row justify-content-center">
                <div class="col-5">
                    <h2 class="bold-font-title">پلن فعلی شما : <span
                            style="color: #ff3111">{{$user->activePackages->count()>0?$user->activePackages->last()->name:"پلن فعالی ندارید"}}</span>
                    </h2>
                </div>
                <div class="col-5">
                    <h2 class="bold-font-title">زمان باقی مانده : <span
                            style="color: #ff3111">{{$user->activePackages->count()>0?jdate($user->activePackages->last()->expire_date)->ago():"0"}}</span>
                    </h2>
                </div>
            </div>

        </div>

        <div class="col-3 text-center p-2 mx-1" style="border: 3px solid #070939">
            <b>آگهی</b>
            <br>
            {{$user->advertiseRemain()}}
        </div>
        <div class="col-3 text-center p-2 mx-1" style="border: 3px solid #185100">
            <b>بالابر</b>
            <br>
            {{$user->addToTopRemain()}}
        </div>
        <div class="col-3 text-center p-2 mx-1" style="border: 3px solid #6a1f00">
            <b>تعداد عکس برای هر آگهی</b>
            <br>
            {{$user->activePackages->count()>0?$user->activePackages->last()->image_count:"0"}}
        </div>

        <div class="col-12 my-3">
            <hr>
        </div>

        <div class="col-12 text-center">

            <h2 class="bold-font-title">پلن های خریداری شده</h2>

            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">بسته</th>
                    <th scope="col">تعداد آگهی</th>
                    <th scope="col">تعداد بالابر</th>
                    <th scope="col">تعداد عکس</th>
                </tr>
                </thead>
                <tbody>
                @foreach($user->activePackagesOrderByDesc as $key=>$package)
                    <tr>
                        <th scope="row">{{$key+1}}</th>
                        <td>{{$package->name}}</td>
                        <td>{{$package->advertise_count}}</td>
                        <td>{{$package->ad_to_top_count}}</td>
                        <td>{{$package->image_count}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>

    </div>

@endsection
