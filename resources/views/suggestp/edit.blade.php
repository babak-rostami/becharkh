@extends('index')

@section('title')
    ویرایش پیشنهاد
@endsection

@section('style')
    <meta name="robots" content="noindex">
    <link
        href="{{ asset('mixassets/css/suggestp/edit.min.css') . '?lm=' . filemtime('mixassets/css/suggestp/edit.min.css') }}"
        rel="stylesheet" type="text/css" />
@endsection

@section('content')
    <div class="row justify-content-center p-2">
        <div class="col-12 col-md-10 text-right p-2 mb-5 p-sm-5 create-div">
            <form action="{{ route('suggest-page.update.admin', $sugp->id) }}" method="POST" role="form"
                enctype="multipart/form-data">
                @csrf
                {{ method_field('PUT') }}

                <img src="{{ $sugp->image() }}" id="esugp-image">
                <div class="form-group">
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="form-group">
                    <label>عنوان</label>
                    <input required type="text" class="form-control" name="title" value="{{ $sugp->title }}">
                </div>
                <div class="form-group">
                    <label>متن</label>
                    <textarea name="body" class="form-control" rows="3">{{ $sugp->body }}</textarea>
                </div>
                <div class="form-group">
                    <label>لینک صفحه</label>
                    <input type="text" required class="form-control" name="link" value="{{ $sugp->link }}">
                </div>
                <div class="form-group">
                    <label>فقط در صفحه خودش نشون داده بشه؟</label>
                    <select class="form-control" name="just_this_page">
                        <option value="0" {{ $sugp->just_this_page == 0 ? 'selected' : '' }}>خیر</option>
                        <option value="1" {{ $sugp->just_this_page == 1 ? 'selected' : '' }}>بله</option>
                    </select>
                </div>

                @include('mainPart.form.search-and-select-for-edit', [
                    'category_input_name' => 'categories',
                    'sasfCategoryIds' => $categoryIds ?? null,
                    'sasfCategorySelects' => $categorySelects ?? null,
                    'item_input_name' => 'items',
                    'sasfItemIds' => $itemIds ?? null,
                    'sasfItemSelects' => $itemSelects ?? null,
                    'question_input_name' => 'questions',
                    'sasfQuestionIds' => $questionIds ?? null,
                    'sasfQuestionSelects' => $questionSelects ?? null,
                    'video_input_name' => 'video',
                    'sasfVideoId' => $videoId ?? null,
                    'sasfVideoSelect' => $videoSelect ?? null,
                ])

                <input class="btn btn-success w-100 mt-4" type="submit" value="ثبت تغییرات">

            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const page = 'edit_suggestp';
    </script>
    <script type="text/javascript"
        src="{{ asset('mixassets/js/suggestp/edit.min.js') . '?lm=' . filemtime('mixassets/js/suggestp/edit.min.js') }}">
    </script>
    <script>
        sasf_categories = {!! json_encode(isset($categoryIds) ? explode(',', $categoryIds) : []) !!};
        sasf_items = {!! json_encode(isset($itemIds) ? explode(',', $itemIds) : []) !!};
    </script>
@endsection
