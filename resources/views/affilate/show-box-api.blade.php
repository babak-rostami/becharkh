<div id="affil-gallery-{{ $affilate->id }}">
    @if (isset($affilate->iimages))
        @if (isset($affilate->video_id))
            <a href="{{ route('product.show', $affilate->slug) }}" target="_blank">
                <img src="{{ $affilate->video->image() }}" id="aff-img-{{ $affilate->id }}-v"
                    alt="{{ $affilate->title }} image">
            </a>
        @endif
        @foreach ($affilate->iimages as $index => $aimg)
            @if ($affilate->img_is_link)
                <a href="{{ route('product.show', $affilate->slug) }}" target="_blank">
                    <img src="{{ $ftp_path . $aimg['path'] }}" id="aff-img-{{ $affilate->id }}-{{ $index + 1 }}"
                        alt="{{ $affilate->title }} {{ $index + 1 }}">
                </a>
            @else
                <img onclick="clickGalleryImg('aff-img-{{ $affilate->id }}-{{ $index + 1 }}', 'affilate')"
                    src="{{ $ftp_path . $aimg['path'] }}" id="aff-img-{{ $affilate->id }}-{{ $index + 1 }}"
                    alt="{{ $affilate->title }} {{ $index + 1 }}">
            @endif
        @endforeach
    @endif
</div>

<div id="affilb-body-{{ $affilate->id }}" class="px-2 py-1 radius-10 my-2">
    {!! $affilate->body !!}</div>

@if (isset($affilate->link) || isset($affilate->product_link))
    <a id="affilb-link-{{ $affilate->id }}" href="{{ route('slink', $affilate->id) }}">
        <span>مشاهده قیمت و مشخصات</span>
        <img class="spb-arrow" src="{{ $ftp_path . 'files/other/images/next-light.png' }}" alt="shop">
    </a>
@endif
