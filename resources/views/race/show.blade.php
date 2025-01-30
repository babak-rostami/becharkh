@extends('index')

@section('title'){{$race->title}}@endsection

@section('style')
    <meta name="title" content="{{$race->title}}">
    <meta name="description" content="{{$race->body}}">
    <meta name="robots" content="index, follow">
@endsection

@section('content')

    <div class="row bg-wht shadow mx-2 my-4 p-2">
        <div class="col-4">
            <img class="w-100" src="{{asset('files/race/images/'.$race->image)}}">
        </div>
        <div class="col-6">
            <h1 class="bold-font-title mt-2">{{$race->title}}</h1>
            <p>{{$race->body}}</p>

            <span class="badge badge-dark">نتایج نظرسنجی تا این لحظه...</span>
            @foreach($race->options as $option)
                <form class="my-1" action="{{route('vote.store',$option->id)}}" method="POST" role="form">
                    @csrf
                    <button type="submit" class="btn btn-outline-dark">{{$option->title}}</button>
                    <span class="badge badge-danger">{{$option->percent()}}%</span>
                </form>
            @endforeach
        </div>

        <hr>

        <div class="col-12">

            @foreach($race->tags() as $tag)
                <a class="mx-1 badge badge-dark mt-1" href="{{route('tag.show',$tag->slug)}}">#{{$tag->title}}</a>
            @endforeach

            <div class="row align-items-center mt-3">
                <div class="col-4">
                    <h3 class="font-20 font-weight-bold ml-2">نظرات کاربران</h3>
                </div>
                <div class="col-8">
                    <a href="" class="btn btn-outline-primary" data-toggle="modal" data-target="#comment"><img
                            class="mx-2" src="{{asset('files/other/images/reply.png')}}">برای ثبت نظر کلیک کنید</a>
                </div>
            </div>
            {{--                Comment Modal--}}
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

                            <form action="{{route('race.comment.store')}}" method="POST" role="form">
                                @csrf

                                <input type="hidden" name="race_id" value="{{$race->id}}">

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
                                                   class="form-control @error('name') is-invalid @enderror"
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

            @if($race->comments->isEmpty())
                <div class="row justify-content-center">
                    <div class="col-10 text-center bg-wht px-4 py-2 radius-10 m-2 shadow">
                        <p class="text-gray">نظری ثبت نشده است</p>
                    </div>
                </div>
            @else
                @foreach($race->comments as $comment)
                    <div class="bg-wht px-4 py-2 radius-10 my-2 shadow">
                        <span><img class="comment-profile-style rounded-circle mr-2"
                                   src="{{asset('files/other/images/profile.jpg')}}">{{$comment->name}}</span>
                        <span class="ml-1">({{jdate($comment->created_at)->ago()}})</span>
                        <p>{{$comment->body}}</p>
                        <a class="btn btn-outline-info" href="" data-toggle="modal"
                           data-target="#reply-{{$comment->id}}">پاسخ<img
                                class="ml-2"
                                src="{{asset('files/other/images/reply.png')}}"></a>
                    </div>

                    @foreach($comment->replies as $reply)
                        <div class="bg-wht px-4 py-2 ml-4 radius-10 my-2 shadow" style="background-color: #f7f7f7">
                        <span><img class="comment-profile-style rounded-circle mr-2"
                                   src="{{asset('files/other/images/profile.jpg')}}">{{$reply->name}}</span>
                            <span class="ml-1">({{jdate($reply->created_at)->ago()}})</span>
                            <span
                                class="badge badge-info">{{isset($reply->replyto)?"پاسخ به ". $reply->replyto->name:""}}</span>
                            <p>{{$reply->body}}</p>
                            <a class="btn btn-outline-info" href="" data-toggle="modal"
                               data-target="#replyto-{{$reply->id}}">پاسخ<img
                                    class="ml-2"
                                    src="{{asset('files/other/images/reply.png')}}"></a>
                        </div>

                        <!-- Comment Reply Modal-->
                        <div class="modal fade" id="replyto-{{$reply->id}}" tabindex="-1" role="dialog"
                             aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">پاسخ</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">

                                        <form action="{{route('race.comment.store')}}" method="POST" role="form">
                                            @csrf

                                            <input type="hidden" name="race_id" value="{{$race->id}}">
                                            <input type="hidden" name="parent_id" value="{{$comment->id}}">
                                            <input type="hidden" name="reply_to_id" value="{{$reply->id}}">

                                            <div class="form-group">
                            <textarea required class="form-control" style="width: 100%; height: 150px; resize: none"
                                      name="body"
                                      placeholder="دیدگاه خود را بنویسید..."></textarea>
                                            </div>

                                            <div class="row">
                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <input type="text" required
                                                               class="form-control @error('name') is-invalid @enderror"
                                                               name="name" placeholder="نام...">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <input type="email" required class="form-control"
                                                               name="email"
                                                               placeholder="ایمیل...">
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="submit" class="btn btn-success">فرستادن دیدگاه</button>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>

                    @endforeach
                <!-- Comment Reply Modal-->
                    <div class="modal fade" id="reply-{{$comment->id}}" tabindex="-1" role="dialog"
                         aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">پاسخ</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">

                                    <form action="{{route('race.comment.store')}}" method="POST" role="form">
                                        @csrf

                                        <input type="hidden" name="race_id" value="{{$race->id}}">
                                        <input type="hidden" name="parent_id" value="{{$comment->id}}">

                                        <div class="form-group">
                            <textarea required class="form-control" style="width: 100%; height: 150px; resize: none"
                                      name="body"
                                      placeholder="دیدگاه خود را بنویسید..."></textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <input type="text" required
                                                           class="form-control @error('name') is-invalid @enderror"
                                                           name="name" placeholder="نام...">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <input type="email" required class="form-control"
                                                           name="email"
                                                           placeholder="ایمیل...">
                                                </div>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-success">فرستادن دیدگاه</button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>

                @endforeach
            @endif
        </div>
    </div>

@endsection
