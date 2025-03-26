@extends('index')


@section('title')
    نظرات کاربران درباره {{ $brand->title }} {{ $model->title }}
@endsection

@section('style')
    <meta name="title" content="نظرات کاربران درباره {{ $brand->title }} {{ $model->title }}">
    <meta name="description" content="{{ $meta_description }}">

    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

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
@endsection

@section('content')

    @if (session('success'))
        <div>
            <p class="alert alert-success text-center">{{ session('success') }}</p>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <p style="color: #000000">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="row bg-wht pt-3 justify-content-center align-items-center">
        <div class="col-12 col-md-5 text-center text-md-left">
            <div class="row align-items-center">
                <div class="col-12 col-md-4 col-lg-3">
                    <img src="{{ asset('files/other/images/crtable.png') }}">
                </div>
                <div class="col-12 col-md-8">
                    <span class="badge badge-warning">میزگرد خودرو</span>
                    <h2 class="bold-font-title">{{ $brand->title }} {{ $model->title }}</h2>
                    @if (isset($sTrim))
                        <span>{{ request()->years }} - {{ $sTrim->title }}</span>
                    @endif
                    <br>
                    <span>{{ $model->advocates->count() }} نفر طرفدار این خودرو هستند</span>
                </div>
            </div>
        </div>

        <div class="col-12 mt-4 mt-md-0 text-center text-md-left col-md-4">
            @if (auth('user')->check())
                <a class="btn btn-lg btn-danger mb-2" href="{{ route('question.create') }}">
                    ایجاد میزگرد جدید
                    <img class="ml-2" src="{{ asset('files/other/images/add.png') }}">
                </a>
            @else
                <a class="btn btn-lg btn-danger mb-2" href="" data-toggle="modal" data-target="#login_user">
                    ایجاد میزگرد جدید
                    <img class="ml-2" src="{{ asset('files/other/images/add.png') }}">
                </a>
            @endif
        </div>

    </div>

    <div class="row ads-box bg-wht radius-10 p-4 justify-content-between">

        <div class="col-12 col-md-5 text-center">

            @if (auth('user')->check())
                @if (auth('user')->user()->isCarAdvocated($model->id))
                    <form class="d-inline" action="{{ route('car.advocate.store') }}" method="POST" role="form">
                        @csrf
                        <input type="hidden" value="{{ $model->id }}" name="model_id">
                        <button type="submit" class="btn btn-lg btn-danger mb-2">طرفدارید
                            <img style="width:24px" src="{{ asset('files/other/images/like2.png') }}">
                        </button>
                    </form>
                @else
                    <form class="d-inline" action="{{ route('car.advocate.store') }}" method="POST" role="form">
                        @csrf
                        <input type="hidden" value="{{ $model->id }}" name="model_id">
                        <button type="submit" class="btn btn-lg btn-outline-danger mb-2">طرفدار
                            <img style="width:24px" src="{{ asset('files/other/images/like2.png') }}">
                        </button>
                    </form>
                @endif
            @else
                <a class="btn btn-lg btn-outline-danger mb-2" href="" data-toggle="modal" data-dismiss="modal"
                    data-target="#login_user">طرفدارم
                    <img style="width:24px" src="{{ asset('files/other/images/like2.png') }}">
                </a>
            @endif

        </div>

    </div>


    <div class="row bg-wht">

        <div class="col-12">
            <hr>
        </div>

        <div class="col-12 col-md-4 order-1 order-md-0">
            <h4 class="ml-4 mb-4 mt-4 mt-md-0"><b>جدیدترین میزگرد ها</b></h4>
            <ul style="list-style-type: none">
                @foreach ($questions as $ques)
                    <li class="category-li">
                        <div class="row bg-gray my-3 p-2 shadow-sm radius-10">
                            <div class="col-12">
                                <a class="text-decoration-none text-dark"
                                    href="{{ route('question.show', $ques->slug2) }}">{{ $ques->title }}</a>
                            </div>
                            <div class="col-6">
                                <span style="color: #005cbf">{{ $ques->user->username }}</span>
                            </div>
                            <div class="col-6 text-right">
                                <img src="{{ asset('files/other/images/comment.png') }}">
                                <span>{{ $ques->answers()->count() }}</span>
                            </div>
                        </div>
                    </li>
                @endforeach
                <a href="{{ route('question.index') }}" class="btn btn-lg btn-primary w-100">ورود به میزگرد</a>
            </ul>
        </div>

        <div class="col-12 col-md-8 order-0 order-md-1">
            <div class="row align-items-center mt-3">
                <div class="col-12 text-center">

                    <span>هر سوالی درباره {{ $brand->title }} {{ $model->title }} داری اینجا به جواب میرسی</span>

                    <h1 class="bold-font-title my-3"> نظرات کاربران درباره {{ $brand->title }} {{ $model->title }}</h1>
                    @if (isset($sTrim))
                        <span class="badge badge-dark">{{ request()->years }} - {{ $sTrim->title }}</span>
                    @endif
                </div>
                <div class="col-12 mt-3">
                    <form action="" method="get">
                        <div class="row justify-content-center">
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <select required class="form-control" name="years" id="years">
                                        <option value="" class="d-none">سال تولید را انتخاب کنید</option>
                                        @for ($i = 0; $i < count($years); $i++)
                                            @if (isset(request()->years) && request()->years == $years[$i])
                                                <option selected value="{{ $years[$i] }}">{{ $years[$i] }}</option>
                                            @else
                                                <option value="{{ $years[$i] }}">{{ $years[$i] }}</option>
                                            @endif
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="form-group">
                                    <select required class="form-control" name="trims" id="trims">
                                        @if (isset($sTrim))
                                            <option selected value="{{ $sTrim->id }}">{{ $sTrim->title }}</option>
                                        @else
                                            <option value="" class="d-none">تریم را انتخاب کنید</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 text-center">
                                <button class="btn btn-danger" type="submit">فیلتر</button>
                                <a class="btn btn-secondary"
                                    href="{{ route('car.page', ['brand_slug' => $brand->nameEn, 'model_slug' => $model->nameEn]) }}">همه
                                    ی نظرات</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-12 text-center my-3">
                    @if (auth('user')->check())
                        <a href="" class="btn btn-lg btn-outline-primary" data-toggle="modal"
                            data-target="#comment"><img class="mx-2"
                                src="{{ asset('files/other/images/reply.png') }}">برای ثبت نظر کلیک کنید</a>
                    @else
                        <a href="" class="btn btn-lg btn-outline-primary" data-toggle="modal"
                            data-target="#login_user"><img class="mx-2"
                                src="{{ asset('files/other/images/reply.png') }}">برای ثبت نظر کلیک کنید</a>
                    @endif
                </div>
            </div>

            @if ($comments->isEmpty())
                <div class="row justify-content-center">
                    <div class="col-10 text-center bg-wht px-4 py-2 radius-10 m-2 shadow">
                        <p class="text-gray">نظری ثبت نشده است اولین نظر را ثبت کنید</p>
                    </div>
                </div>
            @else
                @foreach ($comments as $comment)
                    <div class="bg-wht px-4 py-2 radius-10 my-2 shadow">
                        <div class="row">
                            <div class="col-9">

                                <img class="comment-profile-style rounded-circle mr-2"
                                    src="{{ asset($comment->image()) }}">
                                @if (isset($comment->user_id))
                                    <a target="_blank"
                                        href="{{ route('user.dashboard', $comment->name()) }}">{{ $comment->name() }}</a>
                                @else
                                    <span>{{ $comment->name() }}(کاربر مهمان)</span>
                                @endif
                                <span class="ml-1">({{ jdate($comment->created_at)->ago() }})</span>
                                <span class="badge badge-info">{{ $comment->category() }}</span>
                                @if (isset($comment->trim_id))
                                    <span class="badge badge-dark">{{ $comment->year_id }} -
                                        {{ $comment->trim->title }}</span>
                                @endif
                                <p class="textarea-preline">{{ $comment->body }}</p>

                                @if (auth('user')->check())
                                    <a class="btn btn-outline-info" href="" data-toggle="modal"
                                        data-target="#reply-{{ $comment->id }}">پاسخ<img class="ml-2"
                                            src="{{ asset('files/other/images/reply.png') }}"></a>
                                @else
                                    <a class="btn btn-outline-info" href="" data-toggle="modal"
                                        data-target="#login_user">پاسخ<img class="ml-2"
                                            src="{{ asset('files/other/images/reply.png') }}"></a>
                                @endif

                            </div>
                            <div class="col-2 ml-auto">
                                <a onclick="likeModelComment({{ $comment->id }})"><img
                                        src="{{ asset('files/other/images/like1.png') }}"></a>
                                <br>
                                <span id="model-comment-like-count-{{ $comment->id }}"
                                    class="badge badge-success text-center px-2">{{ $comment->likes()->count() }}</span>
                                <span id="model-comment-unlike-count-{{ $comment->id }}"
                                    class="badge badge-danger text-center px-2">{{ $comment->unlikes()->count() }}</span>
                                <br>
                                <a onclick="unlikeModelComment({{ $comment->id }})"><img
                                        src="{{ asset('files/other/images/dislike.png') }}"></a>
                            </div>
                        </div>
                    </div>

                    @foreach ($comment->replies as $reply)
                        <div class="bg-wht px-4 py-2 ml-4 radius-10 my-2 shadow" style="background-color: #f7f7f7">
                            <div class="row">

                                <div class="col-9">

                                    <img class="comment-profile-style rounded-circle mr-2"
                                        src="{{ asset($reply->image()) }}">
                                    @if (isset($reply->user_id))
                                        <a target="_blank"
                                            href="{{ route('user.dashboard', $reply->name()) }}">{{ $reply->name() }}</a>
                                    @else
                                        <span>{{ $reply->name() }}(کاربر مهمان)</span>
                                    @endif

                                    <span class="ml-1">({{ jdate($reply->created_at)->ago() }})</span>
                                    <p class="textarea-preline">{{ $reply->body }}</p>
                                    @if (auth('user')->check())
                                        <a class="btn btn-outline-info" href="" data-toggle="modal"
                                            data-target="#replyto-{{ $reply->id }}">پاسخ<img class="ml-2"
                                                src="{{ asset('files/other/images/reply.png') }}"></a>
                                    @else
                                        <a class="btn btn-outline-info" href="" data-toggle="modal"
                                            data-target="#login_user">پاسخ<img class="ml-2"
                                                src="{{ asset('files/other/images/reply.png') }}"></a>
                                    @endif
                                </div>

                                <div class="col-2 ml-auto">
                                    <a onclick="likeModelComment({{ $reply->id }})"><img
                                            src="{{ asset('files/other/images/like1.png') }}"></a>
                                    <br>
                                    <span id="model-comment-like-count-{{ $reply->id }}"
                                        class="badge badge-success text-center px-2">{{ $reply->likes()->count() }}</span>
                                    <span id="model-comment-unlike-count-{{ $reply->id }}"
                                        class="badge badge-danger text-center px-2">{{ $reply->unlikes()->count() }}</span>
                                    <br>
                                    <a onclick="unlikeModelComment({{ $reply->id }})"><img
                                            src="{{ asset('files/other/images/dislike.png') }}"></a>
                                </div>

                            </div>

                        </div>

                        <!-- Comment Reply Modal-->
                        <div class="modal fade" id="replyto-{{ $reply->id }}" tabindex="-1" role="dialog"
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

                                        <form action="{{ route('model.comment.store') }}" method="POST" role="form">
                                            @csrf

                                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                            <input type="hidden" name="reply_to_id" value="{{ $reply->id }}">
                                            <input type="hidden" name="trim_id" value="{{ request()->trims }}">
                                            <input type="hidden" name="year_id" value="{{ request()->years }}">

                                            <div class="form-group">
                                                <textarea required class="form-control" style="width: 100%; height: 150px; resize: none" name="body"
                                                    placeholder="دیدگاه خود را بنویسید..."></textarea>
                                            </div>

                                            <button type="submit" class="btn btn-success">ثبت نظر</button>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <!-- Comment Reply Modal-->
                    <div class="modal fade" id="reply-{{ $comment->id }}" tabindex="-1" role="dialog"
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

                                    <form action="{{ route('model.comment.store') }}" method="POST" role="form">
                                        @csrf

                                        <input type="hidden" name="model_id" value="{{ $model->id }}">
                                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                        <input type="hidden" name="trim_id" value="{{ request()->trims }}">
                                        <input type="hidden" name="year_id" value="{{ request()->years }}">

                                        <div class="form-group">
                                            <textarea required class="form-control" style="width: 100%; height: 150px; resize: none" name="body"
                                                placeholder="دیدگاه خود را بنویسید..."></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-success">ثبت نظر</button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

    </div>
    {{-- Comment Modal --}}
    <div class="modal fade" id="comment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">ثبت نظر</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <form action="{{ route('model.comment.store') }}" method="POST" role="form">
                        @csrf

                        <input type="hidden" name="model_id" value="{{ $model->id }}">
                        <input type="hidden" name="trim_id" value="{{ request()->trims }}">
                        <input type="hidden" name="year_id" value="{{ request()->years }}">

                        <div class="form-group">
                            <textarea required class="form-control" style="width: 100%; height: 150px; resize: none" name="body"
                                placeholder="دیدگاه خود را بنویسید...">{{ old('body') }}</textarea>
                            @error('body')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">

                            <div class="col-12">
                                <div class="form-group">
                                    <label>دسته بندی</label>
                                    <select class="form-control" name="car_topic_category">
                                        <option value="1">نظرات کلی</option>
                                        <option value="2">مقایسه با رقبا</option>
                                        <option value="3">مشکلات و معایب</option>
                                        <option value="4">نقد و بررسی</option>
                                        <option value="5">تصاویر ارسالی کاربران</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <button type="submit" class="btn btn-success">ثبت نظر</button>
                    </form>

                </div>
            </div>
        </div>
    </div>


    @if ($similars->count() > 0)
        <div class="col-12">
            <hr>
            <h3>مقالات مرتبط</h3>
            <div class="swiper-container my-4 z-index-0" style="height: 350px">
                <div class="swiper-wrapper">
                    @foreach ($similars as $key => $similar)
                        <div class="swiper-slide">
                            <div class="row justify-content-center">
                                <div class="col-12">
                                    <img class="w-100 text-center"
                                        src="{{ asset('files/blog/images/' . $similar->image) }}"
                                        alt="{{ $similar->title }}" title="{{ $similar->title }}">
                                </div>
                                <div class="col-12 text-center">
                                    <a
                                        href="{{ route('blog.show', ['category_slug' => $similar->category->slug, 'slug' => $similar->slug, 'random_id' => $similar->random_id]) }}">
                                        <h2 class="p-2"
                                            style="background-color: #005cbf; color: #ffffff ; font-size: 22px">
                                            {{ $similar->title }}</h2>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <!-- Add Pagination -->
                <div class="swiper-pagination"></div>
            </div>
        </div>
    @endif


@endsection

@section('script')
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

    <script>
        $('#years').on('change', function() {
            $('#trims').html('').fadeIn(800).append(
                '<option value="{{ null }}">لطفا کمی صبر کنید ...</option>');
            $.ajax({
                method: 'get',
                url: '/get-year-trims',
                data: {
                    'year': this.value,
                    'model': "{{ $model->id }}"
                },
                success: function(msg) {
                    $('#trims').html(msg);
                }
            })
        });
    </script>

    <script>
        function copyToClipboard() {
            document.getElementById("short_link").select();
            document.execCommand('copy');
            $('#saved-span').css('display', 'inline');
        }

        function likeModelComment(model_comment_id) {
            $.ajax({
                type: "POST",
                url: "/model-comment-like",
                data: {
                    "_token": "{{ csrf_token() }}",
                    like_or_unlike: true,
                    model_comment_id: model_comment_id
                },
                success: function(data) {
                    var ellike = 'model-comment-like-count-' + model_comment_id;
                    document.getElementById(ellike).innerHTML = data.likecount

                    var elunlike = 'model-comment-unlike-count-' + model_comment_id;
                    document.getElementById(elunlike).innerHTML = data.unlikecount
                }
            });
        }

        function unlikeModelComment(model_comment_id) {
            $.ajax({
                type: "POST",
                url: "/model-comment-like",
                data: {
                    "_token": "{{ csrf_token() }}",
                    like_or_unlike: false,
                    model_comment_id: model_comment_id
                },
                success: function(data) {
                    var ellike = 'model-comment-like-count-' + model_comment_id;
                    document.getElementById(ellike).innerHTML = data.likecount

                    var elunlike = 'model-comment-unlike-count-' + model_comment_id;
                    document.getElementById(elunlike).innerHTML = data.unlikecount
                }
            });
        }

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
