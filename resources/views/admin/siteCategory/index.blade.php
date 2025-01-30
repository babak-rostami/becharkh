@extends('index')

@section('title')
    مدیریت دسته بندی ها
@endsection

@section('style')
    <script src="{{ asset('library/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('library/ckeditor/ckfinder.js') }}"></script>
    <script src="{{ asset('library/ckeditor/de.js') }}"></script>
@endsection


@section('content')
    <div class="row justify-content-center">

        <div class="col-12 col-md-10 text-center">

            @if (session('success'))
                <p class="alert alert-success text-center">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <p class="alert alert-danger text-center">{{ $error }}</p>
                @endforeach
            @endif

            @if (isset($selectedCat))
                <h4>دسته بندی {{ $selectedCat->title }}
                    @if ($selectedCat->status == 1)
                        <span class="badge badge-success">تایید شده</span>
                    @else
                        <span class="badge badge-danger">تایید نشده</span>
                    @endif
                </h4>
                @if ($selectedCat->canDelete())
                    <a class="btn btn-danger" href="" data-toggle="modal"
                        data-target="#delete-{{ $selectedCat->id }}">حذف</a>
                @endif
                <a class="btn btn-secondary" href="{{ route('category.features.admin', $selectedCat->id) }}">ویژگی
                    ها
                    {{ $selectedCat->features()->count() }}
                </a>
                <a class="btn btn-dark" href="{{ route('admin.advertise.all', $selectedCat->slug) }}">آگهی
                    ها
                    {{ $selectedCat->advertises->count() }}
                </a>
                <a class="btn btn-info" href="{{ route('question.index.admin', $selectedCat->slug) }}">سوال ها
                    {{ $selectedCat->questions->count() }}
                </a>
                <hr>
            @endif
            <div class="modal fade" id="delete-{{ $selectedCat->id }}" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">حذف دسته بندی</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            آیا از حذف دسته بندی {{ $selectedCat->title }} اطمینان دارید؟
                            <form action="{{ route('category.destroy', $selectedCat->id) }}" class="mt-4" method="post"
                                role="form">
                                @csrf
                                {{ method_field('DELETE') }}

                                <button type="submit" class="btn btn-danger">حذف</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    انصراف
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-4 mt-4">
                <table id="zero-config" class="table table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>ردیف</th>
                            <th>نام دسته</th>
                            <th>نام انگلیسی دسته</th>
                            <th>اسلاگ</th>
                            <th>تایید شده؟</th>
                            <th>#</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $key => $category)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $category->title }}</td>
                                <td>{{ $category->title_en }}</td>
                                <td>{{ $category->slug }}</td>
                                @if ($category->status == 1)
                                    <td><span class="badge badge-success">بله</span></td>
                                @else
                                    <td><span class="badge badge-danger">خیر</span></td>
                                @endif
                                <td>
                                    <a class="btn btn-primary"
                                        href="{{ route('site.category.admin', $category->id) }}">ویرایش</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <form class="bg-wht p-3 radius-10" action="{{ route('site.category.update.admin', $selectedCat->id) }}"
                enctype="multipart/form-data" method="post" role="form">
                @csrf

                <div class="row">
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="title">نام دسته</label>
                            <input type="text" class="form-control" name="title" id="title"
                                value="{{ $selectedCat->title }}">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="title_en">نام انگلیسی دسته</label>
                            <input type="text" class="form-control" name="title_en" id="title_en"
                                value="{{ $selectedCat->title_en }}">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="slug">slug *تغییر کنه آدرسا بهم میریزه</label>
                            <input type="text" class="form-control" name="slug" id="slug"
                                value="{{ $selectedCat->slug }}">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="image">تصویر</label>
                            <input type="file" class="form-control" name="image" id="image">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="date_number">دسته بندی بالایی</label>
                            <select class="form-control" name="parent_id">
                                <option value="">ندارد</option>
                                @foreach ($parent_cats as $pcat)
                                    <option {{ $selectedCat->parent_id == $pcat->id ? 'selected' : '' }}
                                        value="{{ $pcat->id }}">{{ $pcat->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group">
                            <label>دسته بندی های مرتبط</label>
                            <div id="rcats">
                                @php
                                    $oldCategories = isset($selectedCat->related_cats)
                                        ? $selectedCat->relatedCategories()->pluck('id')->toArray()
                                        : [];
                                @endphp
                                @foreach ($oldCategories as $oldCategoryId)
                                    @php
                                        $category = $allCats->firstWhere('id', $oldCategoryId);
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
                            <input type="hidden" name="related_cats" id="related_cats"
                                value="{{ implode(',', $oldCategories) }}">
                            <select class="form-control" id="categorySelect" onchange="selectCategory(this)">
                                <option value="" disabled {{ $oldCategories ? '' : 'selected' }}>انتخاب دسته بندی
                                </option>
                                @foreach ($allCats as $cat)
                                    @if (!in_array($cat->id, $oldCategories ?? []))
                                        <option id="{{ $cat->id }}" value="{{ $cat->id }}">
                                            {{ $cat->title }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6">
                        <label for="status">تایید شده</label>
                        <select class="form-control" name="status" id="status">
                            <option {{ $selectedCat->status == 1 ? 'selected' : '' }} value="1">بله
                            </option>
                            <option {{ $selectedCat->status == 0 ? 'selected' : '' }} value="0">خیر
                            </option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label for="cost_description">قیمت یا هزینه یا اجاره یا
                                ....</label>
                            <input type="text" class="form-control" name="cost_description" id="cost_description"
                                value="{{ $selectedCat->cost_description }}">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="has_ads">صفحه آگهی دارد؟</label>
                        <select class="form-control" name="has_ads" id="has_ads">
                            <option {{ $selectedCat->has_ads == 1 ? 'selected' : '' }} value="1">بله
                            </option>
                            <option {{ $selectedCat->has_ads == 0 ? 'selected' : '' }} value="0">خیر
                            </option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="has_forums">صفحه انجمن دارد؟</label>
                        <select class="form-control" name="has_forums" id="has_forums">
                            <option {{ $selectedCat->has_forums == 1 ? 'selected' : '' }} value="1">
                                بله
                            </option>
                            <option {{ $selectedCat->has_forums == 0 ? 'selected' : '' }} value="0">
                                خیر
                            </option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="has_comments">صفحه نظرات دارد؟</label>
                        <select class="form-control" name="has_comments" id="has_comments">
                            <option {{ $selectedCat->has_comments == 1 ? 'selected' : '' }} value="1">
                                بله</option>
                            <option {{ $selectedCat->has_comments == 0 ? 'selected' : '' }} value="0">
                                خیر</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="has_blogs">صفحه بلاگ دارد؟</label>
                        <select class="form-control" name="has_blogs" id="has_blogs">
                            <option {{ $selectedCat->has_blogs == 1 ? 'selected' : '' }} value="1">
                                بله</option>
                            <option {{ $selectedCat->has_blogs == 0 ? 'selected' : '' }} value="0">
                                خیر</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6">
                        <label for="is_cat_in_title">اسم دسته بندی تو عنوان ها
                            باشه؟</label>
                        <select class="form-control" name="is_cat_in_title" id="is_cat_in_title">
                            <option {{ $selectedCat->is_cat_in_title == 1 ? 'selected' : '' }} value="1">بله</option>
                            <option {{ $selectedCat->is_cat_in_title == 0 ? 'selected' : '' }} value="0">خیر</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="title_in_rtable">متا تایتل میزگرد</label>
                            <input type="text" class="form-control" name="title_in_rtable" id="title_in_rtable"
                                value="{{ $selectedCat->title_in_rtable }}">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="desc_in_rtable">متا دسکریپشن میزگرد</label>
                            <textarea class="form-control" name="desc_in_rtable" id="desc_in_rtable">{{ $selectedCat->desc_in_rtable }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>دسکریپشن میزگرد ادیتور</label>
                            <textarea class="form-control ckeditor" name="desc_in_rtable_editor">{{ $selectedCat->desc_in_rtable_editor }}</textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="title_in_comment">متا تایتل نظرات کاربران</label>
                            <input type="text" class="form-control" name="title_in_comment" id="title_in_comment"
                                value="{{ $selectedCat->title_in_comment }}">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="desc_in_comment">متا دسکریپشن نظرات کاربران</label>
                            <textarea class="form-control" name="desc_in_comment" id="desc_in_comment">{{ $selectedCat->desc_in_comment }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>دسکریپشن نظرات ادیتور</label>
                            <textarea class="form-control ckeditor" name="desc_in_comment_editor">{{ $selectedCat->desc_in_comment_editor }}</textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="title_in_ads">متا تایتل آگهی ها</label>
                            <input type="text" class="form-control" name="title_in_ads" id="title_in_ads"
                                value="{{ $selectedCat->title_in_ads }}">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="desc_in_ads">متا دسکریپشن آگهی ها</label>
                            <textarea class="form-control" name="desc_in_ads" id="desc_in_ads">{{ $selectedCat->desc_in_ads }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>دسکریپشن آگهی ادیتور</label>
                            <textarea class="form-control ckeditor" name="desc_in_ads_editor">{{ $selectedCat->desc_in_ads_editor }}</textarea>
                        </div>
                    </div>
                </div>


                <button type="submit" class="btn btn-primary mt-2 w-100">ثبت تغییرات</button>
            </form>

        </div>


    </div>
@endsection

@section('script')
    <script>
        function selectCategory(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            if (!selectedOption.value) return;
            const categoryTitle = selectedOption.textContent;
            const id = selectedOption.value;
            const categoriesInput = document.getElementById('related_cats');
            categoriesInput.value += (categoriesInput.value ? ',' : '') + id;
            const span = document.createElement('span');
            span.textContent = categoryTitle;
            span.setAttribute('data-id', id);
            span.classList.add('badge', 'badge-dark');
            span.style.cursor = 'pointer';
            span.style.marginRight = '10px';
            span.onclick = function() {
                removeCategory(id, span);
            };
            document.getElementById('rcats').appendChild(span);
            selectedOption.remove();
            selectElement.selectedIndex = 0;
        }

        function removeCategory(id, span) {
            span.remove();
            const categoriesInput = document.getElementById('related_cats');
            const ids = categoriesInput.value.split(',').filter(catId => catId !== id);
            categoriesInput.value = ids.join(',');
            const select = document.getElementById('categorySelect');
            const option = document.createElement('option');
            option.id = id;
            option.value = id;
            option.textContent = span.textContent;
            select.appendChild(option);
        }
        const editors = document.querySelectorAll('.ckeditor');
        editors.forEach(editor => {
            ClassicEditor.create(editor, {
                language: "fa",
            });
        });
    </script>
@endsection
