@extends('admin_index')


@section('title')
    داشبورد ادمین
@endsection


@section('style')
@endsection


@section('content')
    <div class="row justify-content-center">

        <div class="col-12 text-center my-2">
            <a href="{{ route('admin.user.notifs') }}" class="btn btn-light ml-2">اعلان های کاربران<span
                    class="mx-2 badge badge-danger">{{ $user_notifs_count }}</span></a>

            <a href="{{ route('event.all') }}" class="btn btn-secondary ml-2">رویداد های اخیر<span
                    class="mx-2 badge badge-danger">{{ admin_notification_count() }}</span></a>

            @if (auth('admin')->user()->type == 1)
                <a class="btn btn-dark" href="{{ route('admin.users') }}">مدیریت کاربران
                    <span class="badge badge-danger">{{ $user_new_imgs_count }}</span>
                </a>
            @endif

            <a class="btn btn-dark" href="{{ route('cats.items.admin') }}">دسته بندی سایت
                <span class="badge badge-danger">{{ $cat_waiting_count }}</span>
            </a>

            <a class="btn btn-light" href="{{ route('admin.category.comment.index') }}">مدیریت نظرات
                <span class="badge badge-danger">{{ $nac_coms_count }}</span>
            </a>
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

            <a class="btn btn-dark" href="{{ route('admin.change.username.reqs') }}">تغییر نام کاربری
                @if ($cun_count > 0)
                    <span class="badge badge-danger">{{ $cun_count }}</span>
                @endif
            </a>

            <a class="btn btn-primary" href="{{ route('admin.advertise.all') }}">آگهی ها
            </a>

            <a class="btn btn-white" href="{{ route('admin.user.searches') }}">جستجو های اخیر
                @if ($search_count > 0)
                    <span class="badge badge-danger">{{ $search_count }}</span>
                @endif
            </a>

            <a class="btn btn-primary" href="{{ route('admin.page.errors') }}">خطاهای سایت
                @if ($site_errors_count > 0)
                    <span class="badge badge-danger">{{ $site_errors_count }}</span>
                @endif
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
    </div>
@endsection


@section('script')
@endsection
