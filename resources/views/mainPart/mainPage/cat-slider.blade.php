<div class="bslider mt-2" id="cat-slider">
    @if (isset($suggetItems))
        @switch($page)
            @case('comment')
                @foreach ($suggetItems as $suggetItem)
                    <div class="bslider-item cat-slider-item">
                        <a class="suggest-item" id="slidera-{{ $suggetItem->id }}" draggable="false"
                            href="{{ $suggetItem->withParentsCommentUrl() }}">
                            {{-- <img draggable="false" alt="عکس {{ $suggetItem->full_title ?? $suggetItem->title }}"
                                src="{{ $suggetItem->thumb() }}"> --}}
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
                            {{-- <img draggable="false" alt="عکس {{ $suggetItem->full_title ?? $suggetItem->title }}"
                                src="{{ $suggetItem->thumb() }}"> --}}
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
                        {{-- @if (isset($category) && $suggetItem->category_id != $category->id)
                            <a class="suggest-item" id="slidera-{{ $suggetItem->id }}" draggable="false"
                                href="{{ str_replace($suggetItem->category->slug, $category->slug, $suggetItem->withParentsAdvertiseUrl()) }}">
                                <img draggable="false" alt="عکس {{$suggetItem->full_title ?? $suggetItem->title}}" src="{{ $suggetItem->thumb() }}">
                                <span>
                                    {{ $suggetItem->full_title ?? $suggetItem->title }}
                                </span>
                            </a>
                        @else --}}
                        <a class="suggest-item" id="slidera-{{ $suggetItem->id }}" draggable="false"
                            href="{{ $suggetItem->withParentsAdvertiseUrl() }}">
                            {{-- <img draggable="false" alt="عکس {{ $suggetItem->full_title ?? $suggetItem->title }}"
                                src="{{ $suggetItem->thumb() }}"> --}}
                            <span>
                                {{ $suggetItem->full_title ?? $suggetItem->title }}
                            </span>
                        </a>
                        {{-- @endif --}}
                    </div>
                @endforeach
            @break

            @case('blog-index')
                @foreach ($suggetItems as $suggetItem)
                    <div class="bslider-item cat-slider-item">
                        <a class="suggest-item" id="slidera-{{ $suggetItem->id }}" draggable="false"
                            href="{{ $suggetItem->withParentsBlogUrl() }}">
                            {{-- <img draggable="false" alt="عکس {{ $suggetItem->full_title ?? $suggetItem->title }}"
                                src="{{ $suggetItem->thumb() }}"> --}}
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
                            {{-- <img draggable="false" alt="عکس {{ $suggetItem->full_title ?? $suggetItem->title }}"
                                src="{{ $suggetItem->thumb() }}"> --}}
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
                            {{-- <img draggable="false" alt="عکس {{ $suggetItem->full_title ?? $suggetItem->title }}"
                                src="{{ $suggetItem->thumb() }}"> --}}
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
                            {{-- <img draggable="false" alt="عکس {{ $suggetItem->full_title ?? $suggetItem->title }}"
                                src="{{ $suggetItem->thumb() }}"> --}}
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
                            {{-- <img draggable="false" alt="عکس {{ $suggetItem->full_title ?? $suggetItem->title }}"
                                src="{{ $suggetItem->thumb() }}"> --}}
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
                    {{-- <img draggable="false" alt="عکس {{ $suggestCat->full_title ?? $suggestCat->title }}"
                        src="{{ $suggestCat->thumb() }}"> --}}
                    <span>
                        {{ $suggestCat->full_title ?? $suggestCat->title }}
                    </span>
                </a>
            </div>
        @endforeach
    @endif
</div>
