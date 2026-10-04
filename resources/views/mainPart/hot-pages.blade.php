@if ($hot_pages)
    @foreach ($hot_pages as $hot_page)
        <div class="col-12 col-md-10 col-lg-6">
            <a class="hop-item mb-4" href="{{ $hot_page->url }}">
                @if (isset($hot_page->image))
                    <img class="lazy-load hop-img" data-src="{{ $hot_page->image }}" alt="عکس {{ $hot_page->title }}">
                @endif
                <span class="hop-title">{{ $hot_page->title }}</span>
                <span class="hop-body">{{ $hot_page->body }}</span>
            </a>
        </div>
    @endforeach
@endif
