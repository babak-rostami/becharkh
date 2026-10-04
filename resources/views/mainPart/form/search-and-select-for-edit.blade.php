@if (isset($category_input_name))
    <input type="hidden" name="{{ $category_input_name }}" id="sasf-categories" value="{{ $sasfCategoryIds ?? null }}" />
    <div id="sasf-selected-categories">
        @if (isset($sasfCategorySelects))
            @foreach ($sasfCategorySelects as $cs)
                <span
                    onclick="sasfRemoveCategory('{{ $cs->id }}', '{{ $cs->title }}')">{{ $cs->title }}</span>
            @endforeach
        @endif
    </div>
@endif
@if (isset($item_input_name))
    <input type="hidden" name="{{ $item_input_name }}" id="sasf-items" value="{{ $sasfItemIds ?? null }}" />
    <div id="sasf-selected-items">
        @if (isset($sasfItemSelects))
            @foreach ($sasfItemSelects as $is)
                <span onclick="sasfRemoveItem('{{ $is->id }}', '{{ $is->title }}')">{{ $is->title }}</span>
            @endforeach
        @endif
    </div>
@endif
@if (isset($question_input_name))
    <input type="hidden" name="{{ $question_input_name }}" id="sasf-questions"
        value="{{ $sasfQuestionIds ?? null }}" />
    <div id="sasf-selected-questions">
        @if (isset($sasfQuestionSelects))
            @foreach ($sasfQuestionSelects as $qs)
                <span
                    onclick="sasfRemoveQuestion('{{ $qs->id }}', '{{ $qs->title }}')">{{ $qs->title }}</span>
            @endforeach
        @endif
    </div>
@endif
@if (isset($video_input_name))
    <input type="hidden" name="{{ $video_input_name }}" id="sasf-video" value="{{ $sasfVideoId ?? null }}" />
    <div id="sasf-selected-video">
        @if (isset($sasfVideoSelect))
            <span>video:</span>
            <span
                onclick="sasfRemoveVideo('{{ $sasfVideoSelect->id }}', '{{ $sasfVideoSelect->title }}')">{{ $sasfVideoSelect->title }}</span>
        @endif
    </div>
@endif

<input id="sasf_cisearch_input" class="form-control my-2 w-100" type="text" placeholder="جستجو کنید...">
<div class="pt-2 pb-5" id="sasf-show-cisearch-result"></div>
<div class="p-4 text-center mt-2" id="sasf-show-cisearch-loading">
    <img class="mt-2 lazy-load" data-src="{{ $ftp_path . 'files/other/images/loading.gif' }}">
    <span>در حال جستجو</span>
</div>
<div class="p-4 text-center mt-2" id="sasf-show-cisearch-empty">
    <img class="mt-2 lazy-load" data-src="{{ $ftp_path . 'files/other/images/search.webp' }}">
    <span>جستجو کنید...</span>
</div>
