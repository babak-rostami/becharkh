@extends('admin_index')


@section('title')
    داشبورد {{ auth('admin')->user()->name }}
@endsection


@section('style')
@endsection


@section('content')
    <div class="row justify-content-center">

        <div class="col-12 text-center my-2">
            <a data-toggle="modal" data-target="#emailtouser" class="btn btn-info">ارسال ایمیل</a>
            <a href="{{ route('event.all') }}" class="btn btn-secondary ml-2">رویداد های اخیر<span
                    class="mx-2 badge badge-danger">{{ admin_notification_count() }}</span></a>

            @if (auth('admin')->user()->type == 1)
                <a class="btn btn-dark" href="{{ route('admin.users') }}">مدیریت کاربران</a>
            @endif

            <a class="btn btn-warning" href="{{ route('orders') }}">پرداخت ها</a>

            <a class="btn btn-dark" href="{{ route('cats.items.admin') }}">دسته بندی سایت
                <span class="badge badge-danger">{{ $cat_waiting_count }}</span>
            </a>

            <a class="btn btn-primary" href="{{ route('admin.diamond.packages') }}">بسته های الماس</a>

            <a class="btn btn-danger" href="{{ route('admin.job.index') }}">مشاغل</a>


            <a class="btn btn-light" href="{{ route('admin.category.comment.index') }}">مدیریت نظرات</a>

            <a class="btn btn-info" href="{{ route('admin.image.compress.index') }}">تصاویر فشرده شده</a>

            <hr>

            <a class="btn btn-danger" href="{{ route('question.index.admin') }}">سوال ها
                @if ($notAcceptedQuestions > 0)
                    <span class="badge badge-primary">{{ $notAcceptedQuestions }}</span>
                @endif
            </a>

            <a class="btn btn-dark" href="{{ route('admin.video.index') }}">ویدیو ها
                @if ($videoNotAcceptedCount > 0)
                    <span class="badge badge-primary">{{ $videoNotAcceptedCount }}</span>
                @endif
            </a>

            <a class="btn btn-danger" href="" data-toggle="modal" data-target="#newitems">آیتم های جدید
                @if (count($itemNotAccepted) > 0)
                    <span class="badge badge-primary">{{ count($itemNotAccepted) }}</span>
                @endif
            </a>

            <a class="btn btn-primary" href="{{ route('hot.items.admin') }}">آیتم های پرطرفدار
            </a>

            <a class="btn btn-secondary" href="{{ route('affilate.index.admin') }}">افیلیت
            </a>

            <a class="btn btn-dark" href="{{ route('lmk.index') }}">بهم خبر بده
                @if ($lmks_count > 0)
                    <span class="badge badge-danger">{{ $lmks_count }}</span>
                @endif
            </a>

            <a class="btn btn-primary" href="{{ route('admin.advertise.all') }}">آگهی ها
            </a>

            <div class="modal fade" id="newitems" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            @foreach ($itemNotAccepted as $ina)
                                <a target="_blank" class="btn btn-danger w-100"
                                    href="{{ route('feature.items.admin', $ina->feature_id) }}">{{ $ina->title }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <div class="col-12 text-center">

            <a href="{{ route('site.view.count.index') }}" class="btn btn-primary w-100">بازدید سایت</a>

        </div>

        <div class="col-6 text-center mt-2 p-4">
            <div class="card">
                <div class="card-body" style="background-color: #fcfdd9; border-radius: 8px">
                    <h5 class="card-title" style="color: #000000">تعداد اعضای سایت</h5>
                    <hr>
                    <p class="card-text" style="color: #000000">{{ $user_count }}</p>
                </div>
            </div>
        </div>
        <div class="col-6 text-center mt-2 p-4">
            <div class="card">
                <div class="card-body" style="background-color: #fcfdd9; border-radius: 8px">
                    <h5 class="card-title" style="color: #000000">تعداد مقالات</h5>
                    <hr>
                    <p class="card-text" style="color: #000000">{{ $blogs_count }}</p>
                </div>
            </div>
        </div>
        <div class="col-6 text-center mt-2 p-4">
            <div class="card">
                <div class="card-body" style="background-color: #fcfdd9; border-radius: 8px">
                    <h5 class="card-title" style="color: #000000">تعداد آگهی ها</h5>
                    <hr>
                    <p class="card-text" style="color: #000000">{{ $ad_count }}</p>
                </div>
            </div>
        </div>
    </div>



    <!-- Modal -->
    <div class="modal fade" id="emailtouser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">ارسال ایمیل از طرف تندایران</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('email.to.user') }}" method="POST" role="form">
                        @csrf

                        <div class="form-group">
                            <label for="email">ایمیل کاربر دریافت کننده</label>
                            <input type="email" class="form-control" name="email" id="email">
                        </div>

                        <div class="form-group">
                            <label for="title">عنوان پیام</label>
                            <input type="text" class="form-control" name="title" id="title">
                        </div>

                        <div class="form-group">
                            <label for="body">متن پیام</label>
                            <textarea class="form-control" name="body"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">ارسال</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
@endsection
