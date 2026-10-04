@extends('index')

@section('title', 'درباره ما')

@section('style')
    <link
        href="{{ asset('mixassets/css/pages/about-us.min.css') . '?lm=' . filemtime('mixassets/css/pages/about-us.min.css') }}"
        rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row justify-content-center bg-wht">
        <div class="col-12 col-md-8 text-right">
            <div class="mb-5 p-4 bg-white rounded">
                <h1 class="font-bold mb-4" id="ptfs">درباره بچرخ</h1>
                <p>
                    خیلی وقتا یه مشکلی برات پیش میاد که هر چی می‌گردی، راه‌حل درست و حسابی براش پیدا نمی‌کنی.
                </p>
                <p>حتی ممکنه
                    تعمیرکارها
                    هم
                    نتونن مشکلتو حل کنن!</p>

                <p>
                    اما باید بدونی که هزاران نفر قبل از تو همین ماشین یا همون وسیله رو داشتن.
                </p>

                <p>احتمال زیاد اون مشکل برای اونا
                    هم پیش
                    اومده و شاید خیلی راحت‌تر از چیزی که فکر می‌کنی حلش کرده باشن.</p>

                <p>
                    انجمن بچرخ دقیقاً برای همین ساخته شده؛ که قبل از اینکه بخوای تصمیمی بگیری یا هزینه‌ای کنی، یه نگاه به
                    تجربه‌های
                    واقعی بقیه بندازی.
                </p>
                <p>شاید همون چیزی که الان درگیرشی، یکی قبلاً تجربه‌اش کرده باشه و راهش رو گفته باشه</p>

                <p>
                    با کمک هم، نه وقتمون تلف میشه، نه پولمون هدر میره.
                </p>


            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript"
        src="{{ asset('mixassets/js/pages/about-us.min.js') . '?lm=' . filemtime('mixassets/js/pages/about-us.min.js') }}">
    </script>
@endsection
