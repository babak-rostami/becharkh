@if (isset($category))
    <div class="text-right">
        <a class="breadcr" href="{{ route('home') }}">بچرخ</a>
        <span class="breadcr-devider">></span>
        @switch($page)
            @case('comment')
                <a class="breadcr" href="{{ route('question.index') . '?s=1' }}">انجمن</a>
                <span class="breadcr-devider">></span>
                <a class="breadcr"
                    href="{{ route('question.index', $category->slug) . '?s=1' }}">{{ $category->full_title ?? $category->title }}</a>
            @break

            @case('forum')
                <a class="breadcr" href="{{ route('question.index') }}">انجمن</a>
                <span class="breadcr-devider">></span>
                <a class="breadcr"
                    href="{{ route('question.index', $category->slug) }}">{{ $category->full_title ?? $category->title }}</a>
            @break

            @case('advertise')
                <a class="breadcr" href="{{ route('ads.index') }}">آگهی ها</a>
                <span class="breadcr-devider">></span>
                <a class="breadcr"
                    href="{{ route('ads.index', $category->slug) }}">{{ $category->full_title ?? $category->title }}</a>
            @break

            @case('blog-index')
                <a class="breadcr" href="{{ route('blog.index') }}">آموزشی</a>
                <span class="breadcr-devider">></span>
                <a class="breadcr"
                    href="{{ route('blog.index', $category->slug) }}">{{ $category->full_title ?? $category->title }}</a>
            @break

            @case('show_blog')
                <a class="breadcr" href="{{ route('blog.index') }}">آموزشی</a>
                <span class="breadcr-devider">></span>
                <a class="breadcr"
                    href="{{ route('blog.index', $category->slug) }}">{{ $category->full_title ?? $category->title }}</a>
            @break

            @case('show_question')
                <a class="breadcr" href="{{ route('question.index') }}">انجمن</a>
                <span class="breadcr-devider">></span>
                <a class="breadcr"
                    href="{{ route('question.index', $category->slug) }}">{{ $category->full_title ?? $category->title }}</a>
            @break

            @case('show_advertise')
                <a class="breadcr" href="{{ route('ads.index') }}">آگهی ها</a>
                <span class="breadcr-devider">></span>
                <a class="breadcr"
                    href="{{ route('ads.index', $category->slug) }}">{{ $category->full_title ?? $category->title }}</a>
            @break

            @case('show_product')
                <a class="breadcr" href="{{ route('ads.index') }}">آگهی ها</a>
                <span class="breadcr-devider">></span>
                <a class="breadcr"
                    href="{{ route('ads.index', $category->slug) }}">{{ $category->full_title ?? $category->title }}</a>
            @break
        @endswitch
    </div>
@else
    <div class="text-right">
        <a class="breadcr" href="{{ route('home') }}">بچرخ</a>
        @switch($page)
            @case('show_blog')
                <span class="breadcr-devider">></span>
                <a class="breadcr" href="{{ route('blog.index') }}">آموزشی</a>
                <span class="breadcr-devider">></span>
                <a class="breadcr"
                    href="{{ route('blog.index', $category->slug) }}">{{ $category->full_title ?? $category->title }}</a>
            @break

            @case('show_question')
                <span class="breadcr-devider">></span>
                <a class="breadcr" href="{{ route('question.index') }}">انجمن</a>
                <span class="breadcr-devider">></span>
                <a class="breadcr"
                    href="{{ route('question.index', $category->slug) }}">{{ $category->full_title ?? $category->title }}</a>
            @break

            @case('show_advertise')
                <span class="breadcr-devider">></span>
                <a class="breadcr" href="{{ route('ads.index') }}">آگهی ها</a>
                <span class="breadcr-devider">></span>
                <a class="breadcr"
                    href="{{ route('ads.index', $category->slug) }}">{{ $category->full_title ?? $category->title }}</a>
            @break

            @case('show_product')
                <span class="breadcr-devider">></span>
                <a class="breadcr" href="{{ route('ads.index') }}">آگهی ها</a>
                <span class="breadcr-devider">></span>
                <a class="breadcr"
                    href="{{ route('ads.index', $category->slug) }}">{{ $category->full_title ?? $category->title }}</a>
            @break
        @endswitch
    </div>
@endif
<div class="bslider mt-2" id="cat-slider">
    @if (isset($suggetItems))
        @switch($page)
            @case('comment')
                @foreach ($suggetItems as $suggetItem)
                    <div class="bslider-item cat-slider-item">
                        <a class="suggest-item" id="slidera-{{ $suggetItem->id }}" draggable="false"
                            href="{{ $suggetItem->withParentsCommentUrl() }}">
                            <img draggable="false" src="{{ $suggetItem->thumb() }}">
                            <span>
                                {{ $suggetItem->full_title ?? $suggetItem->title }}
                            </span>
                        </a>
                    </div>
                @endforeach
            @break

            @case('forum')
                @foreach ($suggetItems as $suggetItem)
                    <div class="bslider-item cat-slider-item">
                        <a class="suggest-item" id="slidera-{{ $suggetItem->id }}" draggable="false"
                            href="{{ $suggetItem->withParentsForumUrl() }}">
                            <img draggable="false" src="{{ $suggetItem->thumb() }}">
                            <span>
                                {{ $suggetItem->full_title ?? $suggetItem->title }}
                            </span>
                        </a>
                    </div>
                @endforeach
            @break

            @case('advertise')
                @foreach ($suggetItems as $suggetItem)
                    <div class="bslider-item cat-slider-item">
                        @if (isset($category) && $suggetItem->category_id != $category->id)
                            <a class="suggest-item" id="slidera-{{ $suggetItem->id }}" draggable="false"
                                href="{{ str_replace($suggetItem->category->slug, $category->slug, $suggetItem->withParentsAdvertiseUrl()) }}">
                                <img draggable="false" src="{{ $suggetItem->thumb() }}">
                                <span>
                                    {{ $suggetItem->full_title ?? $suggetItem->title }}
                                </span>
                            </a>
                        @else
                            <a class="suggest-item" id="slidera-{{ $suggetItem->id }}" draggable="false"
                                href="{{ $suggetItem->withParentsAdvertiseUrl() }}">
                                <img draggable="false" src="{{ $suggetItem->thumb() }}">
                                <span>
                                    {{ $suggetItem->full_title ?? $suggetItem->title }}
                                </span>
                            </a>
                        @endif
                    </div>
                @endforeach
            @break

            @case('blog-index')
                @foreach ($suggetItems as $suggetItem)
                    <div class="bslider-item cat-slider-item">
                        <a class="suggest-item" id="slidera-{{ $suggetItem->id }}" draggable="false"
                            href="{{ $suggetItem->withParentsBlogUrl() }}">
                            <img draggable="false" src="{{ $suggetItem->thumb() }}">
                            <span>
                                {{ $suggetItem->full_title ?? $suggetItem->title }}
                            </span>
                        </a>
                    </div>
                @endforeach
            @break

            @case('show_blog')
                @foreach ($suggetItems as $suggetItem)
                    <div class="bslider-item cat-slider-item">
                        <a class="suggest-item" id="slidera-{{ $suggetItem->id }}" draggable="false"
                            href="{{ $suggetItem->withParentsCommentUrl() }}">
                            <img draggable="false" src="{{ $suggetItem->thumb() }}">
                            <span>
                                {{ $suggetItem->full_title ?? $suggetItem->title }}
                            </span>
                        </a>
                    </div>
                @endforeach
            @break

            @case('show_question')
                @foreach ($suggetItems as $suggetItem)
                    <div class="bslider-item cat-slider-item">
                        <a class="suggest-item" id="slidera-{{ $suggetItem->id }}" draggable="false"
                            href="{{ $suggetItem->withParentsForumUrl() }}">
                            <img draggable="false" src="{{ $suggetItem->thumb() }}">
                            <span>
                                {{ $suggetItem->full_title ?? $suggetItem->title }}
                            </span>
                        </a>
                    </div>
                @endforeach
            @break

            @case('show_advertise')
                @foreach ($suggetItems as $suggetItem)
                    <div class="bslider-item cat-slider-item">
                        <a class="suggest-item" id="slidera-{{ $suggetItem->id }}" draggable="false"
                            href="{{ $suggetItem->withParentsAdvertiseUrl() }}">
                            <img draggable="false" src="{{ $suggetItem->thumb() }}">
                            <span>
                                {{ $suggetItem->full_title ?? $suggetItem->title }}
                            </span>
                        </a>
                    </div>
                @endforeach
            @break

            @case('show_product')
                @foreach ($suggetItems as $suggetItem)
                    <div class="bslider-item cat-slider-item">
                        <a class="suggest-item" id="slidera-{{ $suggetItem->id }}" draggable="false"
                            href="{{ $suggetItem->withParentsAdvertiseUrl() }}">
                            <img draggable="false" src="{{ $suggetItem->thumb() }}">
                            <span>
                                {{ $suggetItem->full_title ?? $suggetItem->title }}
                            </span>
                        </a>
                    </div>
                @endforeach
            @break
        @endswitch
    @elseif(isset($suggestCats))
        @foreach ($suggestCats as $suggestCat)
            <div class="bslider-item cat-slider-item">
                <a class="suggest-item" id="slidera-{{ $suggestCat->id }}" draggable="false"
                    href="{{ urlForSuggest($page, $suggestCat, $category ?? null, $features ?? null, $currentQueryParams ?? null) }}">
                    <img draggable="false" src="{{ $suggestCat->thumb() }}" alt="{{ $suggestCat->title }}">
                    <span>
                        {{ $suggestCat->full_title ?? $suggestCat->title }}
                    </span>
                </a>
            </div>
        @endforeach
    @endif
</div>
