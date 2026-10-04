<div class="pshbox">
    @if ($page == 'show_product' && isset($video) && $show_link == 0)
        <iframe class="shadow-sm p-0 m-0 radius-10" src="{{ route('video.embedb.show', $video->slug2) }}"
            style="border:none;" width="100%" height="400px" allowfullscreen></iframe>
    @endif

    @if (isset($show_video) && $show_video && isset($affilate->video))
        <iframe id class="shadow-sm p-0 m-0 radius-10"
            src="{{ route('video.embedb.show', $affilate->video->slug2) }}" style="border:none;" width="100%"
            height="400px" allowfullscreen></iframe>
    @else
        <div id="affil-gallery-{{ $affilate->id }}">
            @if (isset($affilate->iimages))
                @if (isset($affilate->video_id))
                    @if ($show_link == 0)
                        <img onclick="clickGalleryImg('aff-img-{{ $affilate->id }}-v', 'affilate')"
                            src="{{ $affilate->video->image() }}" id="aff-img-{{ $affilate->id }}-v"
                            alt="{{ $affilate->title }} image">
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
                            src="{{ $ftp_path . $aimg['path'] }}"
                            id="aff-img-{{ $affilate->id }}-{{ $index + 1 }}"
                            alt="{{ $affilate->title }} {{ $index + 1 }}">
                    @else
                        @if ($affilate->img_is_link)
                            <a rel="nofollow" href="{{ route('product.show', $affilate->slug) }}" target="_blank">
                                <img src="{{ $ftp_path . $aimg['path'] }}"
                                    id="aff-img-{{ $affilate->id }}-{{ $index + 1 }}"
                                    alt="{{ $affilate->title }} {{ $index + 1 }}">
                            </a>
                        @else
                            <img onclick="clickGalleryImg('aff-img-{{ $affilate->id }}-{{ $index + 1 }}', 'affilate')"
                                src="{{ $ftp_path . $aimg['path'] }}"
                                id="aff-img-{{ $affilate->id }}-{{ $index + 1 }}"
                                alt="{{ $affilate->title }} {{ $index + 1 }}">
                        @endif
                    @endif
                @endforeach
            @endif
        </div>
    @endif

    <div id="affilb-body-{{ $affilate->id }}"
        class="px-2 py-1 radius-10 my-2 {{ $affilate->img_is_link == 1 ? 'img-is-link' : '' }}">
        {!! $affilate->body !!}</div>

    @if ($show_link == 1)
        @if (isset($affilate->link) || isset($affilate->product_link))
            {{-- <button class="mb-4" id="affilb-link-{{ $affilate->id }}"
            onclick="jsurl('{{ route('slink', $affilate->id) }}',1)">
            <span>مشاهده قیمت و مشخصات</span>
            <img class="lazy-load spb-arrow" data-src="{{ $ftp_path . 'files/other/images/next-light.png' }}"
                alt="shop">
        </button> --}}
            <a id="affilb-link-{{ $affilate->id }}" rel="nofollow" target="_blank" href="{{ route('slink', $affilate->id) }}"
                @if ($show_link == 1 && $page == 'show_product') class="mb-4" @endif>
                <span>مشاهده قیمت و مشخصات</span>
                <img class="lazy-load spb-arrow" data-src="{{ $ftp_path . 'files/other/images/next-light.png' }}"
                    alt="shop">
            </a>
        @endif
    @endif
</div>
