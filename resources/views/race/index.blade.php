@extends('index')

@section('title')نظرسنجی ها@endsection

@section('content')

    @foreach($races as $race)
        <div class="row bg-wht shadow mx-2 my-4 p-2">
            <div class="col-4">
                <img class="w-100" src="{{asset('files/race/images/'.$race->image)}}">
            </div>
            <div class="col-8">
                <h2 class="bold-font-title mt-2">
                    <a href="{{route('race.show',$race->slug)}}">{{$race->title}}</a>
                </h2>
                <p>{{$race->body}}</p>

                @foreach($race->options as $option)
                    <button class="btn btn-outline-dark">{{$option->title}}</button>
                    <span class="badge badge-danger">{{$option->percent()}}%</span>
                @endforeach
                <br>
                <a class="btn btn-primary mt-4" href="{{route('race.show',$race->slug)}}">مشاهده نظرات و نتایج</a>
            </div>
        </div>
    @endforeach

@endsection
