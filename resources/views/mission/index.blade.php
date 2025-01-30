@extends('index')

@section('title')
    ماموریت ها
@endsection

@section('style')
    <meta name="robots" content="noindex">
    <link href="{{ asset('assets/css/mission/index.css') . '?lm=' . filemtime('assets/css/mission/index.css') }}"
        rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 bg-wht radius-10 text-center p-4">

            <div class="row justify-content-center radius-10 py-2 mb-4 missions-box">
                <div class="col-12">

                    @if (session('success'))
                        <div>
                            <p class="alert alert-success text-center">{{ session('success') }}</p>
                        </div>
                    @endif

                    <h1 class="text-white mt-3">ماموریت های هفتگی</h1>

                    <img class="ml-1" src="{{ asset('files/other/images/clock1.png') }}">
                    <b id="plan-day-time"
                        class="mtime-span">{{ json_decode($mission->planTime()->getContent(), true)['day'] }}</b>
                    <span class="text-white ml-2">روز</span>
                    <b id="plan-hour-time"
                        class="mtime-span">{{ json_decode($mission->planTime()->getContent(), true)['hour'] }}</b>
                    <span class="text-white ml-2">ساعت</span>

                    <b id="plan-min-time"
                        class="mtime-span">{{ json_decode($mission->planTime()->getContent(), true)['min'] }}</b>
                    <span class="text-white ml-2">دقیقه</span>
                    <b id="plan-sec-time"
                        class="mtime-span">{{ json_decode($mission->planTime()->getContent(), true)['sec'] }}</b>
                    <span class="text-white ml-2">ثانیه</span>

                </div>
                @foreach ($misssions as $m)
                    <div class="col-11 col-md-10 bg-wht m-2 py-3 radius-10 text-center">
                        <span>پاداش</span>
                        @foreach ($m->rewards as $r)
                            @if ($r->title == 'm')
                                <span class="mrm-title">{{ $r->amount * 1000 }}
                                    تومان</span>
                            @endif
                        @endforeach
                        <hr>
                        @switch($m->type)
                            @case(1)
                                <span>به یک سوال در انجمن پاسخ دهید</span>
                                <hr>
                                @if (auth('user')->user()->checkMission($m->id) == $m->done_count)
                                    @if (!auth('user')->user()->missionRewardReceived($m->id))
                                        <a href="{{ route('get.mission.rewards', $m->id) }}" class="ml-4 btn btn-danger"
                                            onclick="hideMRBtn(this)">
                                            دریافت جوایز
                                        </a>
                                    @endif
                                    <span class="done-m-span">انجام شده</span>
                                @else
                                    <a href="" data-toggle="modal" data-target="#rtableHelpModal"
                                        class="btn btn-success ml-4">
                                        راهنمایی
                                    </a>
                                    <span class="ndone-m-span">انجام نشده</span>
                                @endif

                                <div class="modal fade" id="rtableHelpModal" tabindex="-1" role="dialog"
                                    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <span>به یک سوال در انجمن پاسخ دهید</span>
                                                <hr>
                                                <a class="btn btn-danger w-100" href="{{ route('question.index') }}">ورود به
                                                    انجمن</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @break

                            @case(2)
                                <span>در بخش نظرات اطلاعاتی مفید ثبت کنید</span>
                                <hr>

                                @if (auth('user')->user()->checkMission($m->id) == $m->done_count)
                                    @if (!auth('user')->user()->missionRewardReceived($m->id))
                                        <a href="{{ route('get.mission.rewards', $m->id) }}" class="ml-4 btn btn-danger"
                                            onclick="hideMRBtn(this)">
                                            دریافت جوایز
                                        </a>
                                    @endif
                                    <span class="done-m-span">انجام شده</span>
                                @else
                                    <a href="" data-toggle="modal" data-target="#cCommentHelpModal"
                                        class="btn btn-success ml-4">
                                        راهنمایی
                                    </a>
                                    <span class="ndone-m-span">انجام نشده</span>
                                @endif

                                <div class="modal fade" id="cCommentHelpModal" tabindex="-1" role="dialog"
                                    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <span>یک نظر مفید ثبت کنید</span>
                                                <br>
                                                <span>کاربران میتونن از تجربه شما در موضوعات مختلف بهره ببرن</span>
                                                <hr>
                                                <a class="btn btn-danger w-100" href="{{ route('question.index') }}?s=1">ورود به
                                                    بخش نظرات</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @break
                        @endswitch
                    </div>
                @endforeach
            </div>

        </div>
    </div>
@endsection

@section('script')
    <script>
        function hideMRBtn(button) {
            $(button).hide();
        }

        function showTime() {
            var h = $('#plan-hour-time').text();
            var m = $('#plan-min-time').text();
            var s = $('#plan-sec-time').text();
            var d = $('#plan-day-time').text();

            s = s - 1;
            if (s < 0) {
                s = 60;
                m -= 1;
                if (m < 0) {
                    m = 59;
                    h -= 1;
                    if (h < 0) {
                        h = 23;
                        d -= 1;
                        if (d < 0) {
                            h = 0;
                            m = 0;
                            s = 0;
                            d = 0;
                        }
                    }
                }
            }
            $('#plan-day-time').text(d)
            $('#plan-hour-time').text(h)
            $('#plan-min-time').text(m)
            $('#plan-sec-time').text(s)

            setTimeout(showTime, 1000);

        }

        showTime();
    </script>
@endsection
