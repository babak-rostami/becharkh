@extends('index')

@section('title')
    ویرایش {{ $tag->title }}
@endsection

@section('style')
    <link href="{{ asset('mixassets/css/tag/edit.min.css') . '?lm=' . filemtime('mixassets/css/tag/edit.min.css') }}"
        rel="stylesheet" type="text/css" />

    <meta name="robots" content="noindex">
@endsection

@section('content')
    <div class="row justify-content-center p-2">
        <div class="col-12 col-md-10 text-right p-2 mb-5 p-sm-5 create-div">

            @if (session('success'))
                <p class="alert alert-success text-center my-1">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <p class="alert alert-danger my-1">{{ $error }}</p>
                @endforeach
            @endif

            <p class="alert alert-warning">اگه تگ بالایی رو انتخاب کنی آیتم هایی که انتخاب کردی به تگ بالایی هم اضافه میشن
            </p>
            <p class="alert alert-warning">اگه تگ بالایی رو به هر دلیلی تغییر دادی آیتم های تگ بالایی قبلی حذف نمیشه دستی
                برو
                حذف کن اگه لازم نیست</p>

            <form action="{{ route('item.tag.update.admin', $tag->id) }}" enctype="multipart/form-data" method="post"
                role="form">
                @csrf

                {{ method_field('PUT') }}

                <div class="form-group">
                    <label for="title">عنوان</label>
                    <input type="text" class="form-control" name="title" id="title" value="{{ $tag->title }}">
                </div>
                <div class="form-group">
                    <label for="priority">اولویت</label>
                    <input type="number" class="form-control" name="priority" id="priority" value="{{ $tag->priority }}">
                </div>
                <select class="form-control" name="parent_id">
                    <option value="">ندارد</option>
                    @foreach ($tags as $pt)
                        <option value="{{ $pt->id }}" @if ($tag->parent_id == $pt->id) selected @endif>
                            {{ $pt->title }}
                        </option>
                    @endforeach
                </select>
                <div class="form-group mt-2">
                    <label for="similar_search">سرچ های مشابه</label>
                    <textarea class="form-control" name="similar_search" rows="5">{{ $tag->similar_search }}</textarea>
                </div>

                @include('mainPart.form.search-and-select-for-edit', [
                    'item_input_name' => 'items',
                    'sasfItemIds' => $itemIds ?? null,
                    'sasfItemSelects' => $itemSelects ?? null,
                ])

                <button type="submit" class="btn btn-primary mt-2 w-100">ثبت تغییرات</button>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript"
        src="{{ asset('mixassets/js/tag/edit.min.js') . '?lm=' . filemtime('mixassets/js/tag/edit.min.js') }}"></script>
    <script>
        sasf_items = {!! json_encode(isset($itemIds) ? explode(',', $itemIds) : []) !!};
    </script>
@endsection
