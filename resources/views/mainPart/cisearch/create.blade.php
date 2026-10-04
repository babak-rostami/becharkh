@if ($has_cats == 1)
    <input type="hidden" name="categories" id="categories" />
    <div id="selected-categories"></div>
@endif
@if ($has_items == 1)
    <input type="hidden" name="items" id="items" />
    <div id="selected-items"></div>
@endif
@if ($has_questions == 1)
    <input type="hidden" name="questions" id="questions" />
    <div id="selected-questions"></div>
@endif

<input id="cisearch_input" class="form-control my-2 w-100" type="text" placeholder="جستجو کنید...">
<div class="pt-2 pb-5" id="show-cisearch-result"></div>
<div class="p-4 text-center mt-2" id="show-cisearch-loading">
    <img class="mt-2" loading="lazy" src="{{ $ftp_path . 'files/other/images/loading.gif' }}">
    <span>در حال جستجو</span>
</div>
<div class="p-4 text-center mt-2" id="show-cisearch-empty">
    <img class="mt-2" loading="lazy" src="{{ $ftp_path . 'files/other/images/search.webp' }}">
    <span>جستجو کنید...</span>
</div>