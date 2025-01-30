<div class="col-12" id="hop-box">
    <span id="hop-box-title">
        <img class="lazy-load rcir-glow" data-src="{{ $ftp_path . 'files/other/images/red-circle.png' }}">
        نظرات داغ
        <img class="lazy-load rcir-glow" data-src="{{ $ftp_path . 'files/other/images/red-circle.png' }}">
    </span>
</div>
@foreach ($hot_pages as $hot_page)
    <div class="col-12 col-md-6 text-right">
        <a class="hop-item" href="{{ $hot_page->url }}">
            <span class="hop-title">{{ $hot_page->title }}</span>
            <span class="hop-body">{{ $hot_page->body }}</span>
        </a>
    </div>
@endforeach
