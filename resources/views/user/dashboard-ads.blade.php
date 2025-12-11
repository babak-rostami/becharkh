@extends('index')

@section('title')
    مدیریت آگهی ها
@endsection

@section('style')
    <link
        href="{{ asset('mixassets/css/user/dashboard-ads.min.css') . '?lm=' . filemtime('mixassets/css/user/dashboard-ads.min.css') }}"
        rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 bg-wht text-right py-2 mt-2 radius-10">
            <a class="text-decoration-none text-dark" href="{{ route('user.dashboard.edit') }}"><img class="ml-1"
                    src="{{ $ftp_path . 'files/other/images/back.png' }}" alt="back">بازگشت</a>
            <span class="float-left">مدیریت آگهی ها</span>
        </div>
        <div class="col-12 col-md-8 p-4 mb-2 mt-3" id="edit-ad-page-info-box">
            <span id="edit-ad-page-info-title">مدیریت آگهی ها
            </span>
            <span class="d-block">آگهی های شما در انجمن بچرخ</span>
            <a class="btn btn-lg my-3 btn-primary px-5 radius-10" href="{{ route('new.ad') }}">ثبت آگهی جدید</a>
        </div>
        @if (!$advertises->isEmpty())
            @foreach ($advertises as $key => $adver)
                <div class="col-12 col-md-8 text-right ad-item-div">

                    <img class="ad-item-img" alt="{{ $adver->title }}" title="{{ $adver->title }}"
                        src="{{ asset($adver->thumbnail()) }}">
                    <span class="ad-item-title">{{ $adver->title }}</span>

                    <div class="ad-btns">
                        @if ($adver->status == 1)
                            <a class="btn btn-outline-primary ml-2" target="_blank"
                                href="{{ route('ad.show', $adver->slug) }}">
                                مشاهده
                            </a>
                            <a class="btn btn-outline-secondary ml-2" href="{{ route('ad.edit', $adver->id) }}">
                                ویرایش
                            </a>
                            <a class="btn btn-outline-danger" data-toggle="modal"
                                data-target="#delete-ad-modal-{{ $adver->id }}" href="">
                                حذف
                            </a>
                        @else
                            @if ($adver->not_paid == 1)
                                @if ($user->canCreateAd())
                                    <a href="" href="" data-toggle="modal" data-dismiss="modal"
                                        data-target="#pay-ad-from-acc" class="btn btn-lg btn-success w-100">
                                        آگهی منتشر شود؟</a>
                                    <div class="modal fade" id="pay-ad-from-acc" tabindex="-1" role="dialog"
                                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-12 text-center">
                                                            <p>{{ $adver->title }}</p>
                                                            <hr>
                                                            <h2 class="mb-3">آگهی منتشر شود؟</h2>
                                                            <span id="ac-ad-ymoney-t">موجودی شما</span>
                                                            <span class="ac-ad-money-c">{{ $user->money ?? 0 }}
                                                                تومان</span>
                                                            <br>
                                                            <span id="ac-ad-nmoney-t">مورد نیاز</span>
                                                            <span class="ac-ad-money-c">{{ $ad_price }}
                                                                تومان</span>
                                                            <br>
                                                            <form class="d-inline"
                                                                action="{{ route('pay.ad.from.acc', $adver->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                <button id="pay-ad-btn-{{ $adver->id }}"
                                                                    onclick="payAd('{{ $adver->id }}')" type="submit"
                                                                    class="btn btn-lg btn-success w-50 mt-3">بله</button>
                                                                <button id="pay-ad-btn-loading-{{ $adver->id }}"
                                                                    type="button"
                                                                    class="btn btn-lg btn-light w-50 mt-3">منتظر
                                                                    بمانید...</button>
                                                            </form>
                                                            <a class="btn btn-lg btn-secondary mt-3" href=""
                                                                data-dismiss="modal">بعدا</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <a href="" data-toggle="modal" data-target="#charge-account"
                                        onclick="chacSelectOption('{{ Config::get('gvars.ad_price') }}')"
                                        class="btn btn-lg btn-success w-100">
                                        پرداخت و انتشار آگهی
                                    </a>
                                @endif
                            @endif
                        @endif
                    </div>

                    @if ($adver->status == 1)
                        <span class="ad-active-alert">منتشر شده</span>
                    @else
                        @if ($adver->not_paid == 1)
                            <div class="d-flex text-center">
                                <div id="ad-notp-alert">
                                    <span>موجودی شما: {{ number_format($user->money) ?? 0 }}</span>
                                </div>
                                <div id="ad-notp-need-alert">
                                    <span>مورد نیاز: 75 تومان</span>
                                </div>

                            </div>
                        @else
                            @if ($adver->not_cat == 1)
                                <div id="ad-notcat-alert">
                                    <span>در انتظار تایید</span>
                                </div>
                            @endif
                        @endif
                    @endif
                </div>
                {{-- <a class="btn btn-outline-success ml-2" target="_blank" data-toggle="modal"
                                    data-target="#rocket-ad-modal-{{ $adver->id }}" href="">
                                    موشک
                                </a> --}}
                {{-- <div class="modal fade" id="rocket-ad-modal-{{ $adver->id }}" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <img style="width: 24px" src="{{ $ftp_path . 'files/other/images/rocket.png' }}">
                                        <b>آیا میخواهید از قابلیت <span style="color: #38b000">موشک</span>
                                            برای این آگهی
                                            استفاده کنید؟</b>
                                        <p>با تایید این گزینه آگهی شما دوباره به ابتدای لیست آگهی ها
                                            باز میگردد</p>
                                        <hr>
                                        <span id="ac-ad-ymoney-t">موجودی شما</span>
                                        <span class="ac-ad-money-c">{{ $user->money ?? 0 }}
                                            تومان</span>
                                        <br>
                                        <span id="ac-ad-nmoney-t">مورد نیاز</span>
                                        <span class="ac-ad-money-c">{{ $ad_rocket }}
                                            تومان</span>
                                        <hr>
                                        <a id="rock-ad-btn-{{ $adver->id }}" onclick="rockAd('{{ $adver->id }}')"
                                            class="btn btn-success w-50" href="{{ route('rocket.ad', $adver->id) }}">انجام
                                            شود</a>
                                        <button id="rock-ad-btn-loading-{{ $adver->id }}" class="btn btn-light w-50"
                                            type="button">منتظر بمانید...</button>
                                        <a class="btn btn-secondary" href="" data-dismiss="modal">بعدا</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
                <div class="modal fade" id="delete-ad-modal-{{ $adver->id }}" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12 text-center">

                                        <h4>آگهی حذف شود؟</h4>
                                        <b>مطمئنید میخواهید آگهی حذف شود؟</b>
                                        <hr>
                                        <a class="btn btn-warning w-50" href="" data-dismiss="modal">بعدا</a>
                                        <a class="btn btn-danger" href="{{ route('destroy.ad', $adver->id) }}">بله</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12 col-md-8 py-5 mb-3 mt-2" id="edit-ad-page-404-box">
                <span id="edit-ad-page-404-title">هنوز آگهی ثبت نکردی
                </span>
                <span class="d-block">اولین آگهی رو منتشر کن</span>
            </div>
        @endif

    </div>
@endsection

@section('script')
    <script>
        const dash_edit_csrf = "{{ csrf_token() }}";
        const b_add = "{{ $ftp_path . 'files/other/images/b-add-24.webp' }}";
        const loading_gif = "{{ $ftp_path . 'files/other/images/loading.gif' }}";
    </script>

    <script type="text/javascript"
        src="{{ asset('mixassets/js/user/dashboard-ads.min.js') . '?lm=' . filemtime('mixassets/js/user/dashboard-ads.min.js') }}">
    </script>
@endsection
