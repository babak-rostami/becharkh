<div id="affil-gallery-{{ $affilate->id }}">
    @if (isset($affilate->image_urls))
        @foreach ($affilate->image_urls as $index => $image_url)
            @if ($index == 0)
                <img @if (isset($page) && $page == 'show_product') onclick="clickGalleryImg('aff-img-{{ $affilate->id }}-1', 'affilate')"
                    @else
                    onclick="jsurl('{{ route('product.show', $affilate->slug) }}', 1)" @endif
                    src="{{ isset($affilate->video_id) ? $affilate->video->image() : $affilate->image }}"
                    id="aff-img-{{ $affilate->id }}-{{ $index + 1 }}" alt="{{ $affilate->title }} 1">
            @else
                <img onclick="clickGalleryImg('aff-img-{{ $affilate->id }}-{{ $index + 1 }}', 'affilate')"
                    src="{{ $image_url }}" id="aff-img-{{ $affilate->id }}-{{ $index + 1 }}"
                    alt="{{ $affilate->title }} {{ $index + 1 }}">
            @endif
        @endforeach
    @endif
</div>

<div id="affilb-body-{{ $affilate->id }}" class="px-2 py-1 radius-10 my-2">
    {!! $affilate->body !!}</div>

@if (isset($affilate->link) || isset($affilate->product_link))
    <button id="affilb-link-{{ $affilate->id }}" onclick="jsurl('{{ route('slink', $affilate->id) }}',1)">
        <span>مشاهده قیمت و مشخصات</span>
        <img class="spb-arrow" src="{{ $ftp_path . 'files/other/images/next-light.png' }}" alt="shop">
    </button>
@endif
