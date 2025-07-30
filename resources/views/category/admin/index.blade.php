@extends('index')

@section('title')
    مدیریت دسته بندی ها ، ویژگی ها و آیتم ها
@endsection

@section('style')
    <script src="{{ asset('library/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('library/ckeditor/ckfinder.js') }}"></script>
    <script src="{{ asset('library/ckeditor/de.js') }}"></script>
    <link
        href="{{ asset('assets/css/category/admin/index.css') . '?lm=' . filemtime('assets/css/category/admin/index.css') }}"
        rel="stylesheet" type="text/css" />
    <meta name="robots" content="noindex">
@endsection

@section('content')
    <div class="row bg-wht">
        <div class="col-12 text-center mt-4">

            @if (session('success'))
                <p class="alert alert-success text-center">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <p class="alert alert-danger text-center">{{ $error }}</p>
                @endforeach
            @endif

            <button onclick="openCreateCat()" class="btn btn-primary">ایجاد دسته جدید</button>
            <a class="btn btn-dark" href="{{ route('item.tags.admin') }}">تگ ها</a>
        </div>

        <div class="col-12 text-right mt-4">

            <div id="create-cat-div">
                <form action="{{ route('site.category.store.admin') }}" enctype="multipart/form-data" method="post"
                    role="form">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-6 mt-3">
                            <div class="form-group">
                                <label for="title">نام دسته</label>
                                <input type="text" class="form-control" name="title" id="title"
                                    value="{{ old('title') }}">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-3">
                            <div class="form-group">
                                <label for="title_en">نام انگلیسی دسته</label>
                                <input type="text" class="form-control" name="title_en" id="title_en"
                                    value="{{ old('title_en') }}">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-3">
                            <div class="form-group">
                                <label for="slug">اسلاگ</label>
                                <input type="text" class="form-control" name="slug" id="slug"
                                    value="{{ old('slug') }}">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-3">
                            <div class="form-group">
                                <label for="image">تصویر</label>
                                <input type="file" class="form-control" name="image" id="image">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-3">
                            <div class="form-group">
                                <label for="cost_description">قیمت یا هزینه یا اجاره یا ....</label>
                                <input type="text" class="form-control" name="cost_description" id="cost_description">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-3">
                            <div class="form-group">
                                <label>دسته بندی بالایی</label>
                                <select class="form-control" name="parent_id">
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-3">
                            <label for="status">تایید شده</label>
                            <select class="form-control" name="status" id="status">
                                <option value="0">خیر</option>
                                <option value="1">بله</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 mt-3">
                            <label for="has_ads">صفحه آگهی دارد؟</label>
                            <select class="form-control" name="has_ads" id="has_ads">
                                <option value="0">خیر</option>
                                <option value="1">بله</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 mt-3">
                            <label for="has_forums">صفحه انجمن دارد؟</label>
                            <select class="form-control" name="has_forums" id="has_forums">
                                <option value="0">خیر</option>
                                <option value="1">بله</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 mt-3">
                            <label for="has_comments">صفحه نظرات دارد؟</label>
                            <select class="form-control" name="has_comments" id="has_comments">
                                <option value="0">خیر</option>
                                <option value="1">بله</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 mt-3">
                            <label for="has_blogs">صفحه بلاگ دارد؟</label>
                            <select class="form-control" name="has_blogs" id="has_blogs">
                                <option value="0">خیر</option>
                                <option value="1">بله</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 mt-3">
                            <label for="is_cat_in_title">اسم دسته بندی تو عنوان ها باشه؟</label>
                            <select class="form-control" name="is_cat_in_title" id="is_cat_in_title">
                                <option value="1">بله</option>
                                <option value="0">خیر</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 mt-3">
                            <div class="form-group">
                                <label for="title_in_rtable">متا تایتل میزگرد</label>
                                <input type="text" class="form-control" name="title_in_rtable" id="title_in_rtable">
                            </div>
                            <div class="form-group">
                                <label for="desc_in_rtable">متا دسکریپشن میزگرد</label>
                                <textarea class="form-control" name="desc_in_rtable" id="desc_in_rtable"></textarea>
                            </div>
                            <div class="form-group">
                                <label>دسکریپشن میزگرد ادیتور</label>
                                <textarea class="form-control ckeditor" name="desc_in_rtable_editor">{{ old('desc_in_rtable_editor') }}</textarea>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-3">
                            <div class="form-group">
                                <label for="title_in_comment">متا تایتل نظرات کاربران</label>
                                <input type="text" class="form-control" name="title_in_comment"
                                    id="title_in_comment">
                            </div>
                            <div class="form-group">
                                <label for="desc_in_comment">متا دسکریپشن نظرات کاربران</label>
                                <textarea class="form-control" name="desc_in_comment" id="desc_in_comment"></textarea>
                            </div>
                            <div class="form-group">
                                <label>دسکریپشن نظرات ادیتور</label>
                                <textarea class="form-control ckeditor" name="desc_in_comment_editor">{{ old('desc_in_comment_editor') }}</textarea>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 mt-3">
                            <div class="form-group">
                                <label for="title_in_ads">متا تایتل آگهی ها</label>
                                <input type="text" class="form-control" name="title_in_ads" id="title_in_ads">
                            </div>
                            <div class="form-group">
                                <label for="desc_in_ads">متا دسکریپشن آگهی ها</label>
                                <textarea class="form-control" name="desc_in_ads" id="desc_in_ads"></textarea>
                            </div>
                            <div class="form-group">
                                <label>دسکریپشن آگهی ادیتور</label>
                                <textarea class="form-control ckeditor" name="desc_in_ads_editor">{{ old('desc_in_ads_editor') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-2 w-100">ایجاد شود</button>
                </form>
            </div>

            <hr>

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
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript"
        src="{{ asset('assets/js/category/admin/index.js') . '?lm=' . filemtime('assets/js/category/admin/index.js') }}">
    </script>
@endsection
