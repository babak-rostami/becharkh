@if (isset($video))
    <iframe class="shadow-sm p-0 m-0 radius-10" src="{{ route('video.embedb.show', $video->slug2) }}" style="border:none;"
        width="100%" height="400px" allowfullscreen></iframe>
@endif

<div id="affil-gallery-{{ $affilate->id }}">
    @if (isset($affilate->iimages))
        @if (isset($affilate->video_id))
            @if ($show_link == 0)
                <img onclick="clickGalleryImg('aff-img-{{ $affilate->id }}-v', 'affilate')" src="{{ $affilate->video->image() }}"
                    id="aff-img-{{ $affilate->id }}-v" alt="{{ $affilate->title }} image">
            @else
                <a rel="nofollow" href="{{ route('product.show', $affilate->slug) }}" target="_blank">
                    <img src="{{ $affilate->video->image() }}" id="aff-img-{{ $affilate->id }}-v"
                        alt="{{ $affilate->title }} image">
                </a>
            @endif
        @endif
        @foreach ($affilate->iimages as $index => $aimg)
            @if ($show_link == 0)
                <img onclick="clickGalleryImg('aff-img-{{ $affilate->id }}-{{ $index + 1 }}', 'affilate')"
                    src="{{ $ftp_path . $aimg['path'] }}" id="aff-img-{{ $affilate->id }}-{{ $index + 1 }}"
                    alt="{{ $affilate->title }} {{ $index + 1 }}">
            @else
                @if ($affilate->img_is_link)
                    <a rel="nofollow" href="{{ route('product.show', $affilate->slug) }}" target="_blank">
                        <img src="{{ $ftp_path . $aimg['path'] }}" id="aff-img-{{ $affilate->id }}-{{ $index + 1 }}"
                            alt="{{ $affilate->title }} {{ $index + 1 }}">
                    </a>
                @else
                    <img onclick="clickGalleryImg('aff-img-{{ $affilate->id }}-{{ $index + 1 }}', 'affilate')"
                        src="{{ $ftp_path . $aimg['path'] }}" id="aff-img-{{ $affilate->id }}-{{ $index + 1 }}"
                        alt="{{ $affilate->title }} {{ $index + 1 }}">
                @endif
            @endif
        @endforeach
    @endif
</div>

<div id="affilb-body-{{ $affilate->id }}"
    class="px-2 py-1 radius-10 my-2 text-right {{ $affilate->img_is_link == 1 ? 'img-is-link' : '' }}">
    {!! $affilate->body !!}
</div>

@if ($show_link == 1)
    @if (isset($affilate->link) || isset($affilate->product_link))
        <a id="affilb-link-{{ $affilate->id }}" rel="nofollow" href="{{ route('slink', $affilate->id) }}" class="mb-4">
            <span>مشاهده قیمت و مشخصات</span>
            <img class="spb-arrow" loading="lazy" src="{{ $ftp_path . 'files/other/images/next-light.png' }}" alt="shop">
        </a>
    @endif
@endif