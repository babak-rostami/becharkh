<form
    @if ($page == 'comment') action="{{ route('category.comment.store') }}"
    @elseif($page == 'show_question')
    action="{{ route('question.answer.store') }}"
    @elseif($page == 'edit_question_admin')
    action="{{ route('admin.question.update', $question->id) }}"
    @elseif($page == 'create_question_admin')
    action="{{ route('admin.question.store') }}"
    @elseif($page == 'create_question')
    action="{{ route('question.store') }}"
    @elseif($page == 'admin_edit_comment')
    action="{{ route('admin.category.comment.update', $comment->id) }}"
    @elseif($page == 'edit_question')
    action="{{ route('user.question.update') }}"
    @elseif($page == 'admin_edit_qanswer')
    action="{{ route('admin.question.answer.update', $answer->id) }}"
    @elseif($page == 'admin_qanswers')
    action="{{ route('admin.question.answer.store') }}" 
    @elseif($page == 'admin_create_comment')
    action="{{ route('admin.category.comment.store') }}"
    @elseif($page == 'create_blog')
    action="{{ route('user.store.post') }}"
    @elseif($page == 'edit_blog')
    action="{{ route('user.update.post', $blog->id) }}"
    @elseif($page == 'create_affilate')
    action="{{ route('affilate.store.admin') }}"
    @elseif($page == 'edit_affilate')
    action="{{ route('affilate.update.admin', $affilate->id) }}"
    @elseif($page == 'show_product')
    action="{{ route('product.comment.store') }}"
    @elseif($page == 'admin_create_product_comment')
    action="{{ route('admin.affilate.comment.store') }}"
    @elseif($page == 'admin_edit_product_comment')
    action="{{ route('admin.affilate.comment.update', $comment->id) }}" @endif
    @if (
        $page == 'edit_question_admin' ||
            $page == 'edit_question' ||
            $page == 'create_question_admin' ||
            $page == 'create_question') id="qform"    
    @elseif($page == 'create_blog' || $page == 'edit_blog')
    id="blogform"
    @else
    id="cm_form" @endif
    class="shadow-sm" method="POST" role="form" enctype="multipart/form-data">

    @csrf

    @switch($page)
        @case('comment')
            <input type="hidden" name="category_id" value="{{ $category->id }}">
            @if (isset($item))
                <input type="hidden" name="item_id" value="{{ $item->id }}">
            @endif
        @break

        @case('show_product')
            <input type="hidden" name="product_id" value="{{ $product->id }}">
        @break

        @case('show_question')
            <input type="hidden" name="question_id" value="{{ $question->id }}">
        @break

        @case('edit_question_admin')
            {{ method_field('PUT') }}
            <input type="hidden" id="category_id" name="category_id" value="{{ $question->category_id }}">
            <div class="form-group">
                @if ($question->getImage())
                    <img style="max-height: 256px; margin-bottom: 8px;" src="{{ $question->image() }}">
                @else
                    <span class="badge badge-warning">تصویر ثبت نشده</span>
                @endif
                <input type="file" class="form-control" name="image">
            </div>
            <div class="form-group">
                <select class="form-control" name="google_index">
                    <option {{ $question->google_index == 1 ? 'selected' : '' }} value="1">ایندکس شود</option>
                    <option {{ !isset($question->google_index) || $question->google_index == 0 ? 'selected' : '' }}
                        value="0">ایندکس نشود</option>
                </select>
            </div>
            <div class="form-group">
                <label>عنوان پرسش</label>
                <input type="text" class="form-control required" oninput="countCharacters(this,20,60)" name="title"
                    id="title" placeholder="عنوان سوال مثلا : علت صدای تق تق زیر داشبورد پژو 206"
                    value="{{ $question->title }}">
                <span id="title-error"></span>
                <span id="charCountMin-title" class="input-char-min"></span>
                <span id="charCountMax-title" class="input-char-max"></span>
            </div>
        @break

        @case('edit_question')
            {{ method_field('PUT') }}
            <input type="hidden" id="category_id" name="category_id" value="{{ $question->category_id }}">
            <input type="hidden" id="question_id" name="question_id" value="{{ $question->id }}">
            <div class="form-group">
                <label>عنوان پرسش</label>
                <input type="text" class="form-control required" oninput="countCharacters(this,20,60)" name="title"
                    id="title" placeholder="عنوان سوال مثلا : علت صدای تق تق زیر داشبورد پژو 206"
                    value="{{ $question->title }}">
                <span id="title-error"></span>
                <span id="charCountMin-title" class="input-char-min"></span>
                <span id="charCountMax-title" class="input-char-max"></span>
            </div>
        @break

        @case('create_question_admin')
            <input type="hidden" name="category_id" id="category_id">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label>نام فیک</label>
                        <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label>نام کاربری فیک</label>
                        <input type="text" class="form-control" name="username" id="fake-username"
                            value="{{ old('username') }}">
                        <span id="fake-user-exist"></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>عنوان پرسش</label>
                <input type="text" class="form-control required" oninput="countCharacters(this,20,60)" name="title"
                    id="title" placeholder="عنوان سوال مثلا : علت صدای تق تق زیر داشبورد پژو 206"
                    value="{{ old('title') }}">
                <span id="title-error"></span>
                <span id="charCountMin-title" class="input-char-min"></span>
                <span id="charCountMax-title" class="input-char-max"></span>
            </div>
        @break

        @case('create_question')
            <input type="hidden" name="category_id" id="category_id">
            <div class="form-group">
                <label>عنوان پرسش</label>
                <input type="text" class="form-control required" oninput="countCharacters(this,20,60)" name="title"
                    id="title" placeholder="عنوان سوال مثلا : علت صدای تق تق زیر داشبورد پژو 206"
                    value="{{ old('title') }}">
                <span id="title-error"></span>
                <span id="charCountMin-title" class="input-char-min"></span>
                <span id="charCountMax-title" class="input-char-max"></span>
            </div>
        @break

        @case('admin_edit_comment')
            {{ method_field('PUT') }}
            <input type="hidden" name="category_id" id="category_id" value="{{ $comment->category_id }}">
            @if ($comment->parent_id == null)
                @include('modals.create.select-category', ['categories' => $categories])

                <div class="row">
                    <div class="col-12 mb-4 text-right" id="features-box">
                    </div>
                </div>
            @else
                <span>{{ $comment->parent->body }}</span>
            @endif
        @break

        @case('admin_create_comment')
            <input type="hidden" name="category_id" id="category_id">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label>نام فیک</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label>نام کاربری فیک</label>
                        <input type="text" class="form-control" name="username" id="fake-username"
                            value="{{ old('username') }}">
                        <span id="fake-user-exist"></span>
                    </div>
                </div>
            </div>
            @include('modals.create.select-category', ['categories' => $categories])
            <div class="row">
                <div class="col-12 mb-4 text-right" id="features-box">
                </div>
            </div>
        @break

        @case('admin_edit_qanswer')
            {{ method_field('PUT') }}
        @break

        @case('admin_qanswers')
            <input type="hidden" name="question_id" value="{{ $question->id }}">
            <div class="form-group">
                <label>نام</label>
                <input type="text" class="form-control" name="name">
            </div>
            <div class="form-group">
                <label>نام کاربری</label>
                <input type="text" class="form-control" name="username">
            </div>
        @break

        @case('create_blog')
            <input type="hidden" name="category_id" id="category_id">
            @if (isset($blog))
                <input type="hidden" name="post_id" id="post_id" value="{{ $blog->id }}">
            @else
                <input type="hidden" name="post_id" id="post_id">
            @endif

            <div class="form-group text-center">
                <img style="width: 64px; margin-bottom: 8px" id="blah"
                    src="{{ asset('files/other/images/choose-image.gif') }}" alt="تصویر را انتخاب کنید" />
                <br>
                <input onchange="readURL(this)" type="file" name="image" id="image" accept="image/*"
                    data-msg-accept="برای انتخاب عکس کلیک کنید" style="display:none" />
                <button type="button" class="btn btn-outline-dark w-100"
                    onclick="document.getElementById('image').click()">برای انتخاب
                    عکس
                    کلیک کنید</button>
                <span id="image-error"></span>
            </div>

            <div class="form-group mt-3 text-right">
                <label>عنوان</label>
                <span id="title-error"></span>
                <input oninput="countCharacters(this,30,60)" value="{{ isset($blog) ? $blog->title : null }}" type="text"
                    class="form-control" id="title" name="title"
                    placeholder="موضوع پست ... مثلا مزایا و معایب خرید پژو 206 چیست؟">
                <span id="charCountMin-title" class="input-char-min"></span>
                <span id="charCountMax-title" class="input-char-max"></span>
            </div>


            <div class="form-group">
                <label>توضیحات کوتاه</label>
                <span id="short_description-error"></span>
                <textarea
                    placeholder="این متن ابتدای پست نمایش داده میشود و به بینندگان میگه توی پست قراره چه چیزی یاد بگیرن و کاربر رو تشویق به خوندن میکنه مثلا ضروری است که قبل از خرید پژو 206 به نکاتی که در این پست مطرح شده توجه کنید"
                    oninput="countCharacters(this,30,160)" class="form-control" name="short_description" id="short_description">{{ isset($blog) ? $blog->short_description : null }}</textarea>
                <span id="charCountMin-short_description" class="input-char-min"></span>
                <span id="charCountMax-short_description" class="input-char-max"></span>
            </div>

            <span id="category-error"></span>
            @include('modals.create.select-category', ['categories' => $categories])

            <div class="row">
                <div class="col-12 col-md-10 text-right" id="features-box">
                </div>
            </div>
        @break

        @case('edit_blog')
            {{ method_field('PUT') }}
            <input type="hidden" id="category_id" name="category_id" value="{{ $blog->category_id }}">
            <div class="form-group">
                <input oninput="countCharacters(this,30,60)" type="text" value="{{ $blog->title }}"
                    class="form-control" name="title" id="title">
                <span id="title-error"></span>
                <span id="charCountMin-title" class="input-char-min"></span>
                <span id="charCountMax-title" class="input-char-max"></span>
            </div>

            <div class="form-group my-4">
                <label>توضیحات کوتاه درباره مقاله</label>
                <textarea oninput="countCharacters(this,30,160)" required class="form-control" name="short_description"
                    id="short_description" id="short_description">{{ $blog->short_description }}</textarea>
                <span id="short_description-error"></span>
                <span id="charCountMin-short_description" class="input-char-min"></span>
                <span id="charCountMax-short_description" class="input-char-max"></span>
            </div>
            <div class="form-group text-center">
                <input onchange="readURL(this)" type="file" name="image" id="image" accept="image/*"
                    data-msg-accept="برای انتخاب عکس کلیک کنید" style="display:none" />
                <button type="button" class="btn btn-outline-dark w-100"
                    onclick="document.getElementById('image').click()">برای تغییر
                    عکس
                    کلیک کنید</button>
                <img class="w-75 mt-2" id="blah" src="{{ asset($blog->image()) }}">
            </div>

            <div class="alert alert-dark">{{ $blog->category->title }}
            </div>

            <div class="row">
                <div class="col-12 col-md-10 mb-2 text-right" id="features-box">
                </div>
            </div>
        @break

        @case('create_affilate')
            <div class="form-group">
                <label>عنوان</label>
                <input type="text" class="form-control" name="title" id="title" value="{{ old('title') }}">
            </div>
            <div class="form-group">
                <label>لینک اختصاصی</label>
                <input type="text" class="form-control" name="link" id="link" value="{{ old('link') }}">
            </div>
            <div class="form-group">
                <label>لینک عمومی</label>
                <select class="form-control" name="public_link">
                    <option value="">ندارد</option>
                    @foreach ($plinks as $plink)
                        <option value="{{ $plink->link }}">{{ $plink->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>لینک محصول (برای لینک عمومی)</label>
                <input type="text" class="form-control" name="product_link" id="product_link"
                    value="{{ old('product_link') }}">
            </div>
            <div class="form-group">
                <label>لینک صفحه</label>
                <input type="text" class="form-control" name="page_link" id="page_link" value="{{ old('page_link') }}">
            </div>
            <div class="form-group">
                <label>تایید شده؟</label>
                <select class="form-control" name="status">
                    <option value="1">بله</option>
                    <option value="0">خیر</option>
                </select>
            </div>
            <div class="form-group">
                <label>فقط در صفحه خودش نشون داده بشه؟</label>
                <select class="form-control" name="just_this_page">
                    <option value="1">بله</option>
                    <option value="0">خیر</option>
                </select>
            </div>

            <div class="form-group">
                <label>google index</label>
                <select class="form-control" name="google_index">
                    <option value="1">ایندکس شود</option>
                    <option value="0">ایندکس نشود</option>
                </select>
            </div>

            <input type="hidden" name="categories" id="categories" />
            <input type="hidden" name="items" id="items" />
            <input type="hidden" name="questions" id="questions" />
            <div id="selected-categories"></div>
            <div id="selected-items"></div>
            <div id="selected-questions"></div>

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
        @break

        @case('edit_affilate')
            {{ method_field('PUT') }}
            <div class="form-group">
                <label>عنوان</label>
                <input type="text" class="form-control" name="title" id="title" value="{{ $affilate->title }}">
            </div>
            <div class="form-group">
                <label>لینک اختصاصی</label>
                <input type="text" class="form-control" name="link" id="link" value="{{ $affilate->link }}">
            </div>
            <div class="form-group">
                <label>لینک عمومی</label>
                <select class="form-control" name="public_link">
                    <option value="">ندارد</option>
                    @foreach ($plinks as $plink)
                        <option value="{{ $plink->link }}" @if ($affilate->public_link == $plink->link) selected @endif>
                            {{ $plink->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>لینک محصول (برای لینک عمومی)</label>
                <input type="text" class="form-control" name="product_link" id="product_link"
                    value="{{ $affilate->product_link }}">
            </div>
            <div class="form-group">
                <label>لینک صفحه</label>
                <input type="text" class="form-control" name="page_link" id="page_link"
                    value="{{ $affilate->page_link }}">
            </div>
            <div class="form-group">
                <label>تایید شده؟</label>
                <select name="status">
                    <option {{ $affilate->status == 1 ? 'selected' : '' }} value="1">بله</option>
                    <option {{ $affilate->status == 0 ? 'selected' : '' }} value="0">خیر</option>
                </select>
            </div>
            <div class="form-group">
                <label>فقط در صفحه خودش نشون داده بشه؟</label>
                <select name="just_this_page">
                    <option {{ $affilate->just_this_page == 1 ? 'selected' : '' }} value="1">بله</option>
                    <option {{ $affilate->just_this_page == 0 ? 'selected' : '' }} value="0">خیر</option>
                </select>
            </div>

            <div class="form-group">
                <label>google index</label>
                <select class="form-control" name="google_index">
                    <option {{ $affilate->google_index == 1 ? 'selected' : '' }} value="1">ایندکس شود</option>
                    <option {{ $affilate->google_index == 0 ? 'selected' : '' }} value="0">ایندکس نشود</option>
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
        @break

        @case('admin_create_product_comment')
            <input type="hidden" name="product_id" value="{{ $product_id }}">
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label>نام فیک</label>
                        <input type="text" class="form-control" name="name" id="name"
                            value="{{ old('name') }}">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label>نام کاربری فیک</label>
                        <input type="text" class="form-control" name="username" id="fake-username"
                            value="{{ old('username') }}">
                        <span id="fake-user-exist"></span>
                    </div>
                </div>
            </div>
        @break

    @endswitch

    @if ($page == 'admin_edit_comment')
        <textarea required class="form-control comment-input" id="cm-input" name="body"
            placeholder="نظر خود را اینجا بنویسید...">{{ $comment->editor ?? $comment->body }}</textarea>
    @elseif($page == 'admin_edit_qanswer')
        <textarea required class="form-control comment-input" id="cm-input" name="body"
            placeholder="نظر خود را اینجا بنویسید...">{{ $answer->editor ?? $answer->body }}</textarea>
    @elseif($page == 'edit_question_admin')
        <label>توضیحات سوال</label>
        <textarea required class="form-control comment-input" id="cm-input" name="body"
            placeholder="توضیحات سوال را اینجا بنویسید...">{{ $question->editor }}</textarea>

        @include('mainPart.form.search-and-select-for-edit', [
            'category_input_name' => 'pin_cat_ids',
            'sasfCategoryIds' => $sasf_categoryIds ?? null,
            'sasfCategorySelects' => $sasf_categorySelects ?? null,
            'item_input_name' => 'pin_item_ids',
            'sasfItemIds' => $sasf_itemIds ?? null,
            'sasfItemSelects' => $sasf_itemSelects ?? null,
        ])

        @include('modals.create.select-category', ['categories' => $categories])
        <div class="row">
            <div class="col-12 mb-4 text-right" id="features-box">
            </div>
        </div>
    @elseif($page == 'edit_question')
        <label>توضیحات سوال</label>
        <textarea required class="form-control comment-input" id="cm-input" name="body"
            placeholder="توضیحات سوال را اینجا بنویسید...">{{ $question->editor }}</textarea>
        <div class="form-group mt-4">
            <div class="alert alert-dark">{{ $category->title }}
            </div>
        </div>
        <div class="row">
            <div class="col-12 mb-4 text-right" id="features-box">
            </div>
        </div>
    @elseif($page == 'create_question_admin' || $page == 'create_question')
        <label>توضیحات سوال</label>
        <textarea required class="form-control comment-input" id="cm-input" name="body"
            placeholder="توضیحات سوال را اینجا بنویسید..."></textarea>
        @include('modals.create.select-category', ['categories' => $categories])
        <div class="row">
            <div class="col-12 mb-4 text-right" id="features-box">
            </div>
        </div>
    @elseif($page == 'create_blog')
        <label>پست رو اینجا بنویس</label>
        <textarea class="form-control" id="cm-input" name="body"
            placeholder="استفاده از جدول , عکس و عنوان های مفید باعث می شود پست شما رتبه بالایی گرفته و بیشتر نمایش داده شود - مطالب کپی شده از اینترنت توسط هوش مصنوعی تشخیص داده شده و باعث میشود پست رتبه ی پایینی بگیرد - در صورت کپی بودن قسمتی از متن با دانش و لحن خود متن را تغییر دهید">{{ isset($blog) ? $blog->content : null }}</textarea>
    @elseif($page == 'edit_blog')
        <label>پست رو اینجا بنویس</label>
        <textarea class="form-control" id="cm-input" name="body">{{ $blog->content }}</textarea>
    @elseif($page == 'create_affilate')
        <label>توضیحات</label>
        <textarea class="form-control" id="cm-input" name="body"></textarea>
        <label>توضیحات کامل</label>
        <textarea class="form-control" id="cm-input-2" name="body2"></textarea>
    @elseif($page == 'edit_affilate')
        <label>توضیحات</label>
        <textarea class="form-control" id="cm-input" name="body">{{ $affilate->body }}</textarea>
        <label>توضیحات کامل</label>
        <textarea class="form-control" id="cm-input-2" name="body2">{{ $affilate->body2 }}</textarea>
    @elseif($page == 'admin_edit_product_comment')
        {{ method_field('PUT') }}
        <textarea class="form-control" id="cm-input" name="body">{{ $comment->editor }}</textarea>
    @else
        <textarea class="form-control comment-input" id="cm-input" name="body"
            placeholder="نظر خود را اینجا بنویسید..."></textarea>
    @endif

    @if ($page == 'edit_question_admin' || $page == 'edit_question')
        <button type="button" class="btn btn-outline-primary bg-wht w-100 my-2" onclick="editorQuestionUpdate()"
            id="comment-editor-btn">
            <img data-src="{{ $ftp_path . 'files/other/images/send-com24.webp' }}" class="lazy-load" alt="send">
            ویرایش سوال
        </button>
    @elseif($page == 'create_question_admin' || $page == 'create_question')
        @include('survey.surbox')
        <div class="d-flex">
            <button type="button" class="btn btn-outline-primary bg-wht my-2 flex-grow-1"
                onclick="editorQuestionStore()" id="comment-editor-btn">
                <img data-src="{{ $ftp_path . 'files/other/images/send-com24.webp' }}" class="lazy-load"
                    alt="send">
                ثبت سوال
            </button>
            <input type="hidden" id="has_survey" name="has_survey" value="0">
            <button class="btn btn-light bg-wht my-2" id="add-survey-btn" onclick="addSurvey()" type="button">
                <img data-src="{{ $ftp_path . 'files/other/images/test-22.png' }}" class="lazy-load" alt="survey">
                نظرسنجی
            </button>
        </div>
    @elseif($page == 'create_blog')
        <button type="button" class="btn btn-outline-primary bg-wht w-100 mb-2 mt-5" onclick="editorBlogStore()"
            id="comment-editor-btn">
            <img data-src="{{ $ftp_path . 'files/other/images/send-com24.webp' }}" class="lazy-load" alt="send">
            انتشار مطلب
        </button>
    @elseif($page == 'edit_blog')
        <button type="button" class="btn btn-outline-primary bg-wht w-100 mb-2 mt-5" onclick="editorBlogUpdate()"
            id="comment-editor-btn">
            <img data-src="{{ $ftp_path . 'files/other/images/send-com24.webp' }}" class="lazy-load" alt="send">
            ویرایش مطلب
        </button>
    @elseif($page == 'create_affilate' || $page == 'edit_affilate')
        <button type="button" class="btn btn-outline-primary bg-wht w-100 mb-2 mt-5" onclick="affilateStoreUpdate()"
            id="comment-editor-btn">
            <img data-src="{{ $ftp_path . 'files/other/images/send-com24.webp' }}" class="lazy-load" alt="send">
            ثبت افیلیت
        </button>
    @else
        @if (isset($user) ||
                $page == 'admin_edit_comment' ||
                $page == 'admin_create_comment' ||
                $page == 'admin_edit_qanswer' ||
                $page == 'admin_qanswers')
            @if ($page == 'admin_create_comment' || $page == 'comment')
                @include('survey.surbox')
                <div class="d-flex">
                    <button type="button" class="btn btn-outline-primary bg-wht my-2 flex-grow-1"
                        onclick="editorCommentSend()" id="comment-editor-btn">
                        <img data-src="{{ $ftp_path . 'files/other/images/send-com24.webp' }}" class="lazy-load"
                            alt="send">
                        ارسال نظر
                    </button>
                    <input type="hidden" id="has_survey" name="has_survey" value="0">
                    <button class="btn btn-light bg-wht my-2" id="add-survey-btn" onclick="addSurvey()"
                        type="button">
                        <img data-src="{{ $ftp_path . 'files/other/images/test-22.png' }}" class="lazy-load"
                            alt="survey">
                        نظرسنجی
                    </button>
                </div>
            @else
                <button type="button" class="btn btn-outline-primary bg-wht my-2 w-100"
                    onclick="editorCommentSend()" id="comment-editor-btn">
                    <img data-src="{{ $ftp_path . 'files/other/images/send-com24.webp' }}" class="lazy-load"
                        alt="send">
                    ارسال نظر
                </button>
            @endif
        @else
            <a href="" class="btn btn-outline-primary bg-wht w-100 my-2" data-toggle="modal"
                data-target="#login_user"
                @if ($page == 'comment') onclick="setActionForAfterAuth('comment', 'cm_form')"
            @elseif($page == 'show_question')
            onclick="setActionForAfterAuth('answer', 'cm_form')"
            @elseif($page == 'show_product')
            onclick="setActionForAfterAuth('comment', 'cm_form')" @endif>
                <img data-src="{{ $ftp_path . 'files/other/images/send-com24.webp' }}" class="lazy-load"
                    alt="send">
                ارسال نظر
            </a>
        @endif
    @endif
    @if (
        $page == 'create_question_admin' ||
            $page == 'create_question' ||
            $page == 'edit_question_admin' ||
            $page == 'edit_question' ||
            $page == 'create_affilate' ||
            $page == 'edit_affilate' ||
            $page == 'create_blog' ||
            $page == 'edit_blog')
        <button type="button" class="btn btn-light w-100 my-2" id="comment-editor-load-btn">
            <img class="lazy-load ml-2" data-src="{{ $ftp_path . 'files/other/images/loading.gif' }}">
            در حال ثبت...
        </button>
    @else
        <button type="button" class="btn btn-light w-100 my-2" id="comment-editor-load-btn">
            <img class="lazy-load ml-2" data-src="{{ $ftp_path . 'files/other/images/loading.gif' }}">
            در حال ارسال پیام...
        </button>
    @endif
    <span id="comeditor-msg"></span>
</form>

<div id="edImageModal" class="ed-imgslider-modal">
    <span id="close-ed-img">&times;</span>
    <img class="ed-imgslider-modal-content" id="ed-img">
</div>
