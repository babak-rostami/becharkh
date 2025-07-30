@extends('index')

@section('title')
    @if (isset($category))
        ویژگی جدید
    @endif
@endsection

@section('style')
@endsection


@section('content')
    <div class="row justify-content-center">
        <div class="col-12 text-center mt-4">

            @if (session('success'))
                <p class="alert alert-success text-center">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <p class="alert alert-danger text-center">{{ $error }}</p>
                @endforeach
            @endif

            <form action="{{ route('category.feature.update.admin', $feature->id) }}" enctype="multipart/form-data"
                method="post" role="form">
                @csrf
                {{ method_field('PUT') }}

                <div class="row">
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="title">نام ویژگی</label>
                            <input type="text" class="form-control" name="title" id="title"
                                value="{{ $feature->title }}">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="title_en">نام انگلیسی ویژگی</label>
                            <input type="text" class="form-control" name="title_en" id="title_en"
                                value="{{ $feature->title_en }}">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="slug">اسلاگ</label>
                            <input type="text" class="form-control" name="slug" id="slug"
                                value="{{ $feature->slug }}">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="select_items_count">چند تا میتونه انتخاب بشه؟</label>
                            <input type="number" min="1" class="form-control" name="select_items_count"
                                id="select_items_count" value="{{ $feature->select_items_count ?? 1 }}">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label>عنوان معرفی پیج</label>
                            <input type="text" class="form-control" name="page_intro_title" id="page_intro_title"
                                value="{{ $feature->page_intro_title }}">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label>معرفی پیج</label>
                            <textarea name="page_intro_desc" id="page_intro_desc" class="form-control" rows="10">{{ $feature->page_intro_desc }}</textarea>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="parent_id">ویژگی بالایی</label>
                            <select class="form-control" name="parent_id" id="parent_id">
                                <option value="">ندارد</option>
                                @foreach ($features as $pf)
                                    <option value="{{ $pf->id }}"
                                        {{ $feature->parent_id == $pf->id ? 'selected' : '' }}>
                                        {{ $pf->title }} -
                                        @foreach ($pf->categories as $c1)
                                            {{ $c1->title }}
                                        @endforeach
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="status">تایید شده</label>
                        <select class="form-control" name="status" id="status">
                            <option value="0" {{ $feature->status == 0 ? 'selected' : '' }}>خیر</option>
                            <option value="1" {{ $feature->status == 1 ? 'selected' : '' }}>بله</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6">
                        <label for="be_indexed">در کنونیکال باشد؟</label>
                        <select class="form-control" name="be_indexed" id="be_indexed">
                            <option value="0" {{ $feature->be_indexed == 0 ? 'selected' : '' }}>خیر</option>
                            <option value="1" {{ $feature->be_indexed == 1 ? 'selected' : '' }}>بله</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6">
                        <label for="is_in_filter_rtable">در فیلتر میزگرد</label>
                        <select class="form-control" name="is_in_filter_rtable" id="is_in_filter_rtable">
                            <option value="0" {{ $feature->is_in_filter_rtable == 0 ? 'selected' : '' }}>نیست</option>
                            <option value="1" {{ $feature->is_in_filter_rtable == 1 ? 'selected' : '' }}>هست</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="is_in_filter_ad">در فیلتر آگهی است؟</label>
                        <select class="form-control" name="is_in_filter_ad" id="is_in_filter_ad">
                            <option value="0" {{ $feature->is_in_filter_ad == 0 ? 'selected' : '' }}>خیر</option>
                            <option value="1" {{ $feature->is_in_filter_ad == 1 ? 'selected' : '' }}>بله</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6">
                        <label for="is_important_in_ad">در ثبت آگهی باید حتما تکمیل شود؟</label>
                        <select class="form-control" name="is_important_in_ad" id="is_important_in_ad">
                            <option value="0" {{ $feature->is_important_in_ad == 0 ? 'selected' : '' }}>خیر</option>
                            <option value="1" {{ $feature->is_important_in_ad == 1 ? 'selected' : '' }}>بله</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="input_type">نوع ورودی</label>
                        <select class="form-control" name="input_type" id="input_type">
                            <option value="0" {{ $feature->input_type == 0 ? 'selected' : '' }}>متن</option>
                            <option value="1" {{ $feature->input_type == 1 ? 'selected' : '' }}>عدد</option>
                            <option value="2" {{ $feature->input_type == 2 ? 'selected' : '' }}>بازی عددی</option>
                            <option value="3" {{ $feature->input_type == 3 ? 'selected' : '' }}>شماره</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="has_follow">قابلیت فالو شدن؟</label>
                        <select class="form-control" name="has_follow" id="has_follow">
                            <option value="0" {{ $feature->has_follow == 0 ? 'selected' : '' }}>ندارد</option>
                            <option value="1" {{ $feature->has_follow == 1 ? 'selected' : '' }}>دارد</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="has_items">گزینه دارد؟ مثلا برند گزینه دارد : هیوندای - تویوتا و ...</label>
                        <select class="form-control" name="has_items" id="has_items">
                            <option value="0" {{ $feature->has_items == 0 ? 'selected' : '' }}>ندارد</option>
                            <option value="1" {{ $feature->has_items == 1 ? 'selected' : '' }}>دارد</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="is_feature_in_title">ویژگی در عنوان صفحه باشد؟</label>
                        <select class="form-control" name="is_feature_in_title" id="is_feature_in_title">
                            <option value="0" {{ $feature->is_feature_in_title == 0 ? 'selected' : '' }}>نه</option>
                            <option value="1" {{ $feature->is_feature_in_title == 1 ? 'selected' : '' }}>بله</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="item_is_in_title">آیتم ها در عنوان صفحه باشد؟</label>
                        <select class="form-control" name="item_is_in_title" id="item_is_in_title">
                            <option value="0" {{ $feature->item_is_in_title == 0 ? 'selected' : '' }}>نه</option>
                            <option value="1" {{ $feature->item_is_in_title == 1 ? 'selected' : '' }}>بله</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6">
                        <label for="item_is_in_title_if_not_parent">آیتم ها فقط اگر بچه انتخاب نشده بود در عنوان صفحه
                            باشد؟</label>
                        <select class="form-control" name="item_is_in_title_if_not_parent"
                            id="item_is_in_title_if_not_parent">
                            <option value="0" {{ $feature->item_is_in_title_if_not_parent == 0 ? 'selected' : '' }}>
                                نه
                            </option>
                            <option value="1" {{ $feature->item_is_in_title_if_not_parent == 1 ? 'selected' : '' }}>
                                بله
                            </option>
                        </select>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="form-group">
                            <label>دسته بندی</label>
                            <div id="cats">
                                @php
                                    $oldCategories = $feature->categories->pluck('id')->toArray();
                                @endphp
                                @foreach ($oldCategories as $oldCategoryId)
                                    @php
                                        $category = $categories->firstWhere('id', $oldCategoryId);
                                    @endphp
                                    @if ($category)
                                        <span class="badge badge-dark" data-id="{{ $category->id }}"
                                            style="cursor: pointer; margin-right: 10px;"
                                            onclick="removeCategory('{{ $category->id }}', this)">
                                            {{ $category->title }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                            <input type="hidden" name="categories" id="categories"
                                value="{{ implode(',', $oldCategories) }}">
                            <select class="form-control" id="categorySelect" onchange="selectCategory(this)">
                                <option value="" disabled {{ $oldCategories ? '' : 'selected' }}>انتخاب دسته بندی
                                </option>
                                @foreach ($categories as $category)
                                    @if (!in_array($category->id, $oldCategories ?? []))
                                        <option id="{{ $category->id }}" value="{{ $category->id }}">
                                            {{ $category->title }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>

                <button type="submit" class="btn btn-primary my-4 w-100">ایجاد شود</button>
            </form>
        </div>
    </div>
@endsection



@section('script')
    <script>
        function selectCategory(selectElement) {
            // Get the selected option
            const selectedOption = selectElement.options[selectElement.selectedIndex];

            // If no option is selected, do nothing
            if (!selectedOption.value) return;

            // Get the title of the selected category
            const categoryTitle = selectedOption.textContent;
            const id = selectedOption.value;

            // Add the category ID to the hidden input
            const categoriesInput = document.getElementById('categories');
            categoriesInput.value += (categoriesInput.value ? ',' : '') + id;

            // Create a span for the category title
            const span = document.createElement('span');
            span.textContent = categoryTitle;
            span.setAttribute('data-id', id);
            span.classList.add('badge', 'badge-dark');
            span.style.cursor = 'pointer';
            span.style.marginRight = '10px';
            span.onclick = function() {
                removeCategory(id, span);
            };

            // Append the span to the #cats div
            document.getElementById('cats').appendChild(span);

            // Remove the selected option from the dropdown
            selectedOption.remove();

            // Reset the select element to the default option
            selectElement.selectedIndex = 0;
        }

        function removeCategory(id, span) {
            // Remove the span from #cats
            span.remove();

            // Update the hidden input to remove the category ID
            const categoriesInput = document.getElementById('categories');
            const ids = categoriesInput.value.split(',').filter(catId => catId !== id);
            categoriesInput.value = ids.join(',');

            // Re-add the option back to the dropdown
            const select = document.getElementById('categorySelect');
            const option = document.createElement('option');
            option.id = id;
            option.value = id;
            option.textContent = span.textContent;
            select.appendChild(option);
        }
    </script>
@endsection
