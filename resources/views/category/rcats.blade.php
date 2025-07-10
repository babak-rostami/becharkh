{{-- @if (isset($category) && isset($category->related_cats))
    <div id="rcats">
        @if (isset($page) && $page != 'show_blog' && $page != 'show_question' && isset($item))
            <span id="rcats-title">مرتبط با {{ $item->full_title ?? $item->title }}</span>
        @endif
        <div id="rcats-list">
            @foreach ($category->relatedCategories() as $rCat)
                <a href="{{ urlForSuggest('advertise', $rCat, $category, $features, $currentQueryParams) }}"
                    class="rcat">
                    <img src="{{ $rCat->thumb() }}">
                    <span>{{ $rCat->full_title ?? $rCat->title }}</span>
                </a>
            @endforeach
        </div>
    </div>
@else --}}
@if (isset($category) && isset($item) && $item->category_id != $category->id)
    <div id="rcats">
        <div id="rcats-list">
            {{-- <a href="{{ urlForSuggest('advertise', $item->category, $category, $features, $currentQueryParams) }}" --}}
            <a href="{{ $item->withParentsCommentUrl() }}" class="rcat">
                <img src="{{ $item->thumb() }}">
                <span>{{ $item->full_title ?? $item->title }}</span>
            </a>
        </div>
    </div>
@endif
{{-- @endif --}}
