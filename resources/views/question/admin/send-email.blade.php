@extends('index')

@section('title')
    ارسال ایمیل
@endsection

@section('style')
    <link
        href="{{ asset('assets/css/forum/send-email-index.css') . '?lm=' . filemtime('assets/css/forum/send-email-index.css') }}"
        rel="stylesheet" type="text/css" />
    <meta name="robots" content="noindex">
@endsection

@section('content')
    <div class="row bg-wht">
        <div class="col-12 text-right mt-4">
            <h2>{{ $question->title }}</h2>
            <br>
            <input id="qe_search_input" class="form-control my-2 w-100" type="text" placeholder="جستجو کنید...">
            <img id="qesearch-magicon" src="{{ $ftp_path . 'files/other/images/search-blue.png' }}">
            <div class="pt-2 pb-5" id="show-qesearch-result"></div>
            <div class="p-4 text-center mt-2" id="show-qesearch-loading">
                <img class="mt-2 lazy-load" data-src="{{ $ftp_path . 'files/other/images/loading.gif' }}">
                <span>در حال جستجو</span>
            </div>
            <div class="p-4 text-center mt-2" id="show-qesearch-empty">
                <img class="mt-2 lazy-load" data-src="{{ $ftp_path . 'files/other/images/search.webp' }}">
                <span>جستجو کنید...</span>
            </div>
        </div>
        <div class="col-12 text-center mt-4">
            <span id="send-email-message"></span>
            <br>
            <input type="text" class="form-control" id="email-title">
            <a class="btn btn-primary text-white w-50" id="send-email-btn" onclick="sendEmail()">ارسال ایمیل</a>
        </div>
        <div class="col-12 text-right mt-4" id="select-user-list">
        </div>
    </div>
@endsection

@section('script')
    <script>
        const question_id = "{{ $question->id }}"
        const get_cat_eusers_route = "{{ route('admin.get.cat.eusers') }}"
        const get_item_eusers_route = "{{ route('admin.get.item.eusers') }}"
        const send_email_route = "{{ route('admin.send.question.email') }}"
    </script>
    <script type="text/javascript"
        src="{{ asset('assets/js/forum/send-email-index.js') . '?lm=' . filemtime('assets/js/forum/send-email-index.js') }}">
    </script>
@endsection
