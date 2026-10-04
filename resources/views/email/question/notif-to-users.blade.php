<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>بچرخ</title>
</head>

<body dir="rtl">
    <div style="width: 100%;text-align: center;">
        <img style="margin-top: 42px" src="https://dl.becharkh.com/user_files/files/other/images/new-question.gif">
        <span style="display: block;font-size: 14px;margin-bottom: 8px;">{{ $user->name }} عزیز; سلام</span>
        <span>{{ $question->user->name }} یک سوال در انجمن مطرح کرده است</span>
        <h2 style="color: #000000">{{ $question->title }}</h2>
        <p>{{ $question->body }}</p>
        @if ($question->items_title)
            @foreach ($question->items_title as $qt)
                <span
                    style="border: 1px solid #ccc;
    border-radius: 10px;
    padding: 2px 10px;
    background-color: #f6f8fa;
    display: inline-block;
    margin: 4px;
    font-size: 14px;">{{ $qt }}</span>
            @endforeach
            <br>
        @endif

        <a target="_blank"
            style="background-color: #3771e0;color:#fff;padding: 8px 42px;text-decoration: none;border-radius: 8px;display: inline-block;margin: 18px 0;"
            href="{{ $route }}">برای مشاهده مطلب کلیک کنید</a>
        <br>
        <span>اگه به هر دلیلی گزینه بالا کار نکرد برای مشاهده مطلب آدرس پایین رو کپی کنید و در مرورگر خود باز
            کنید</span>
        <span style="display: block;
        margin-top: 21px;
        color: #0000ff;">{{ $route }}</span>

        <span
            style="
        color: #000000;
        font-size: 18px;
        display: block;
        margin-top: 24px;
        border-top: 1px solid #c9c9c9;
        padding-top: 12px;
    ">انجمن
            بچرخ</span>
        <p style="
        color: #000000;
        display: inline-block;
        font-size: 21px;
    ">
            becharkh.com
        </p>
    </div>
</body>

</html>
