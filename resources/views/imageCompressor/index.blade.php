@extends('index2')

@section('title')
    کاهش حجم عکس آنلاین
@endsection

@section('style')
    <meta name="title" content="کاهش حجم عکس آنلاین">
    <meta name="description"
        content="برای کاهش حجم عکس آنلاین میتونین از هوش مصنوعی بچرخ استفاده کنید برای کاهش حجم تصویر مورد نظرتون رو انتخاب کنید">
    <meta name="robots" content="index, follow">
    <link
        href="{{ asset('assets/css/imagecompressor/index.css') . '?lm=' . filemtime('assets/css/imagecompressor/index.css') }}"
        rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row justify-content-center bg-wht p-2 mt-2 radius-10">
        <div class="col-12 text-center mt-4">
            <h1 id="page-title-ic">کاهش حجم عکس آنلاین</h1>
            <p>برای کاهش حجم عکس آنلاین میتونین از هوش مصنوعی بچرخ استفاده کنید برای کاهش حجم تصویر مورد نظرتون رو انتخاب
                کنید</p>
            <img id="uploaded-image" src="{{ asset('files/other/images/ic-upload.webp') }}" alt="کاهش حجم و سایز عکس"
                title="کاهش حجم و سایز عکس">
            <br>
            <input type="file" id="uploaded-image-input" accept="image/*">
            <button class="btn btn-primary mt-2" id="btn-upload-ic"
                onclick="document.getElementById('uploaded-image-input').click()">برای
                انتخاب
                عکس کلیک کنید</button>
            <button class="btn mt-2" id="btn-loading-ic">
                در حال آپلود
                <img src="{{ asset('files/other/images/loading.gif') }}">
            </button>
            <button class="btn btn-danger mt-2" id="btn-error-ic">
                خطایی رخ داد
            </button>
        </div>

        <div class="col-12 col-md-8 text-center" id="ic-compress-form-div">
            <hr>
            <div class="form-group">
                <span>کیفیت تصویر خروجی</span>
                <span id="ic-q-span">90</span>
                <br>
                <input class="form-control" id="ic-qua-input" oninput="getRangeValue()" value="90" type="range"
                    min="1" max="100">
            </div>
            <div class="form-group">
                <label>فرمت تصویر خروجی</label>
                <select class="form-control" id="ic-image-format">
                    <option value="0">تغییر نکند</option>
                    <option value="png">png</option>
                    <option value="jpg">jpg</option>
                    <option value="webp">webp</option>
                </select>
            </div>
            <label>سایز تصویر خروجی</label>
            <select class="form-control mb-4" id="ic-is-size-change" onchange="icHasChangeSize()">
                <option value="0">تغییر نکند</option>
                <option value="1">تغییر سایز</option>
            </select>
            <div id="ic-size-change-input-div">
                <div class="ic-s-input-div">
                    <span class="ic-s-input-span">طول</span>
                    <input class="ic-s-input" id="ic-image-width" type="text" placeholder="طول">
                </div>
                <span>*</span>
                <div class="ic-s-input-div">
                    <span class="ic-s-input-span">عرض</span>
                    <input class="ic-s-input" id="ic-image-height" type="text" placeholder="عرض">
                </div>
            </div>
            <br>
            <button type="button" class="btn btn-danger w-100 mb-3" id="ic-compress-image-btn"
                onclick="compressImage()">فشرده کردن</button>
            <button class="btn mb-3 w-100" id="compress-btn-loading-ic">
                در حال فشرده سازی
                <img src="{{ asset('files/other/images/loading.gif') }}">
            </button>
            <button class="btn btn-warning mb-3 w-100" id="compress-btn-error-ic">
                خطایی رخ داد
            </button>
            <br>
            <div id="ic-download-div">
                <span>فایل خروجی</span>
                <br>
                <span>حجم</span>
                <span id="ic-new-image-size"></span>
                <span>مگابایت</span>
                <br>
                <img id="ic-output-img" src="{{ asset('files/other/images/ic-upload.webp') }}">
                <br>
                <a target="_blank" href="" download id="ic-output-dwn-btn" onclick="icDownloadBtnClick()"
                    class="btn btn-success">دانلود</a>
                <button class="btn" id="ic-output-dwning-btn">
                    تا چند ثانیه دیگر دانلود شروع میشود
                    <img src="{{ asset('files/other/images/loading.gif') }}">
                </button>
            </div>
        </div>

        <div class="col-12 text-right">
            <h2 class="mt-3">کاهش حجم تصویر آنلاین</h2>
            <ul>
                <li>کاهش حجم عکس آنلاین</li>
                <li>تغییر فرمت عکس آنلاین</li>
                <li>تغییر سایز عکس آنلاین</li>
            </ul>
            <p>توی سایت بچرخ آگهی ها ، پست ها ، نظرات و سوالات زیادی مطرح میشه و توی بسیاری از بخش ها کاربران
                تصویر ارسال میکنن و این میتونه سرعت سایت رو پایین بیاره و حجم سرور رو پر کنه.
            </p>
            <p>برای همین ابزار کاهش حجم بدون افت کیفیت عکس رو طراحی کردم که تصاویر قبل ذخیره در سرور از این برنامه عبور
                میکنن.</p>
            <p>گفتم شاید به درد خیلیا بخوره چون که بعضی جا ها برای ارسال عکس باید حجم عکس پایین باشه و فرمت خاصی داشته باشه
            </p>
            <p>برای همین این صفحه ابزار کاهش حجم عکس آنلاین رو هم جداگونه طراحی کردم.</p>
            <p>البته برای ثبت آگهی در سایت بچرخ و ارسال پست نیازی نیست از اینجا حجم تصویر رو کاهش بدین و موقع ارسال تصویر
                خود
                به خود پردازش تصویر انجام میشه این ابزار برای کسایی هستش که میخوان فرمت عکس رو عوض کنن و حجم تصویر رو کم کنن
                و در برنامه
                های دیگه از عکس استفاده کنن.
            </p>
            <h2>آموزش کاهش حجم عکس</h2>
            <p>بسیار ساده هستش برای کاهش حجم عکس توی همین صفحه روی دکمه ی "برای انتخاب عکس کلیک کنید" که بالای صفحه اومده
                کلیک کنید و تصویر
                مورد نظرتون رو انتخاب کنید</p>
            <p>وقتی آپلود انجام شد صفحه ی تنظیمات براتون باز میشه که خیلی ساده میتونین در حالت های مختلف از عکستون خروجی
                بگیرین و سایز و کیفیت عکس فشرده شده رو بررسی کنید و عکس خروجی رو دانلود کنید.</p>
            <h2>تغییر فرمت عکس آنلاین</h2>
            <p>برای تبدیل و <b>تغییر فرمت عکس</b> از بین گزینه هایی که بهتون نمایش میده میتونین یکی رو انتخاب کنید</p>
            <ol>
                <li>webp</li>
                <li>png</li>
                <li>jpg</li>
            </ol>
            <p>اگه فرمت دیگه ای خواستین از بخش پشتیبانی بگین حداکثر تا 24 ساعت براتون اضافه میکنم</p>
            <p>تبدیل فرمت عکس به jpg و png ممکنه حجم عکس رو افزایش بده و این ابزار رو برای این گذاشتم چون خیلی نیازتون میشه
                که
                بخواین فرمت عکس رو تغییر بدین</p>
            <p>با تبدیل فرمت عکس به webp میتونین <b>بیشترین کاهش حجم عکس</b> رو بدست بیارین.</p>
            <h2>تغییر سایز عکس آنلاین</h2>
            <p>برای <b>تغییر سایز عکس</b> به سادگی گزینه سایز تصویر خروجی را روی تغییر سایز قرار بدین بعد ازتون طول و عرض
                میخواد
                و
                میتونین طول و عرض
                عکس رو وارد کنید و سایز تصویر رو تغییر بدین</p>
            <p>برای <b>کاهش حجم تصویر</b> میتونین سایز تصویر رو هم کاهش بدین</p>
            <p>امیدوارم به کارتون اومده باشه.</p>
        </div>

    </div>
@endsection

@section('script')
    <script>
        const ic_upload_image_route = '{{ route('image.compressor.upload') }}';
        const ic_compress_image_route = '{{ route('image.compressor.compress') }}';
        const ic_csrf_token = '{{ csrf_token() }}';
        var image_id = null;
    </script>
    <script type="text/javascript"
        src="{{ asset('assets/js/imagecompressor/index.js') . '?lm=' . filemtime('assets/js/imagecompressor/index.js') }}">
    </script>
@endsection
