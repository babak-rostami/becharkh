@extends('index')


@section('title')
    اعلانات
@endsection

@section('style')
    <meta name="robots" content="noindex">

    <link href="{{ asset('mixassets/css/user/notifs.min.css') . '?lm=' . filemtime('mixassets/css/user/notifs.min.css') }}"
        rel="stylesheet" type="text/css" />
@endsection

@section('content')

    <div class="row justify-content-center bg-wht">

        <div class="col-12 col-md-10 text-center" id="no-notif">
            @if ($notifications->isEmpty())
                <img src="{{ $ftp_path . 'files/other/images/notifis.gif' }}">
                <span id="no-notif-title">اعلان های شما</span>
                <span id="no-notif-desc">پاسخ های کاربران به سوال ها و نظرات شما اینجا به شما اطلاع داده میشود</span>
            @else
                <span id="notif-h">اعلانات شما</span>
                <span id="notifs-desc">پاسخ های کاربران به سوال ها و نظرات شما اینجا به شما اطلاع داده میشود</span>
                @foreach ($notifications as $key => $notification)
                    <a class="notif-a" target="_blank" href="{{ $notification->route }}">
                        <span class="notif-a-title">{{ $notification->msg }}</span>
                        <span class="notif-a-body">{{ str_limit($notification->body, 45, '...') }}</span>
                        <span class="notif-a-time">{{ jdate($notification->created_at)->ago() }}</span>
                        @if ($notification->seen)
                            <span class="notif-new">جدید</span>
                        @endif
                    </a>
                @endforeach
            @endif
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript"
        src="{{ asset('mixassets/js/main.min.js') . '?lm=' . filemtime('mixassets/js/main.min.js') }}"></script>
@endsection
