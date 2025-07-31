@if (isset($video) && isset($page) && $page == 'show_product')
    <iframe class="shadow-sm p-0 m-0 mt-3 radius-10" src="{{ route('video.embedb.show', $video->slug2) }}"
        style="border:none;" width="100%" height="292px" allowfullscreen></iframe>
@endif

@if (!isset($page) || (isset($page) && $page != 'show_product'))
    <a class="prod-a-api" target="_blank" href="{{ route('product.show', $affilate->slug) }}"
        id="product-vimg-div-{{ $affilate->id }}">
        <img src="{{ isset($affilate->video_id) ? $affilate->video->image() : $affilate->image }}"
            alt="عکس {{ $affilate->title }}">
    </a>
@endif

<div id="affilb-body-{{ $affilate->id }}"
    class="px-2 py-1 radius-10 my-2 {{ $affilate->img_is_link == 1 ? 'img-is-link' : '' }}">
    {!! $affilate->body !!}</div>

@if (!isset($page) || (isset($page) && $page != 'show_product'))
    @if (isset($affilate->link) || isset($affilate->product_link))
        <button id="affilb-link-{{ $affilate->id }}" onclick="jsurl('{{ route('slink', $affilate->id) }}',1)">
            <span>مشاهده قیمت و مشخصات</span>
            <img class="lazy-load spb-arrow" data-src="{{ $ftp_path . 'files/other/images/next-light.png' }}"
                alt="shop">
        </button>
    @endif
@endif
{{-- @include('mainPart.gallery') --}}
