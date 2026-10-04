@extends('index')

@section('title')
    پیشنهاد جدید
@endsection

@section('style')
    <meta name="robots" content="noindex">
@endsection

@section('content')
    <div class="row justify-content-center p-2">
        <div class="col-12 col-md-10 text-right p-2 mb-5 p-sm-5 create-div">
            <form action="{{ route('suggest-page.store.admin') }}" method="POST" role="form" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <input required type="file" name="image" class="form-control">
                </div>
                <div class="form-group">
                    <label>عنوان</label>
                    <input required type="text" class="form-control" name="title" id="title"
                        value="{{ old('title') }}">
                </div>
                <div class="form-group">
                    <label>متن</label>
                    <textarea name="body" class="form-control" rows="3">{{ old('body') }}</textarea>
                </div>
                <div class="form-group">
                    <label>لینک صفحه</label>
                    <input type="text" required class="form-control" name="link" id="link"
                        value="{{ old('link') }}">
                </div>
                <div class="form-group">
                    <label>فقط در صفحه خودش نشون داده بشه؟</label>
                    <select class="form-control" name="just_this_page">
                        <option value="1">بله</option>
                        <option value="0">خیر</option>
                    </select>
                </div>

                <input type="hidden" name="categories" id="categories" />
                <input type="hidden" name="items" id="items" />
                <div id="selected-categories"></div>
                <div id="selected-items"></div>

                <input id="cisearch_input" class="form-control my-2 w-100" type="text" placeholder="جستجو کنید...">
                <div class="pt-2 pb-5" id="show-cisearch-result"></div>
                <div class="p-4 text-center mt-2" id="show-cisearch-loading">
                    <img class="mt-2 lazy-load" data-src="{{ $ftp_path . 'files/other/images/loading.gif' }}">
                    <span>در حال جستجو</span>
                </div>
                <div class="p-4 text-center mt-2" id="show-cisearch-empty">
                    <img class="mt-2 lazy-load" data-src="{{ $ftp_path . 'files/other/images/search.webp' }}">
                    <span>جستجو کنید...</span>
                </div>

                <input class="btn btn-success w-100 mt-4" type="submit" value="ثبت">

            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const page = 'create_suggestp';
    </script>
    <script type="text/javascript"
        src="{{ asset('mixassets/js/suggestp/create.min.js') . '?lm=' . filemtime('mixassets/js/suggestp/create.min.js') }}">
    </script>
@endsection
