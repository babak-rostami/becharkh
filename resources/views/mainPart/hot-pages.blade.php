@if ($hot_pages)
    <div class="col-12 text-right text-md-center">
        @foreach ($hot_pages as $hot_page)
            <a class="hop-item mb-4" href="{{ $hot_page->url }}">
                @if (isset($hot_page->image))
                    <img class="lazy-load hop-img" data-src="{{ $hot_page->image }}" alt="{{ $hot_page->title }}">
                @endif
                <span class="hop-title">{{ $hot_page->title }}</span>
                <span class="hop-body">{{ $hot_page->body }}</span>
            </a>
        @endforeach
    </div>
@endif
