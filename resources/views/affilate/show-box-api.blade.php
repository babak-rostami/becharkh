<a class="prod-a-api" target="_blank" href="{{ route('product.show', $affilate->slug) }}"
    id="product-vimg-div-{{ $affilate->id }}">
    <img src="{{ isset($affilate->video_id) ? $affilate->video->image() : $affilate->image }}"
        alt="{{ $affilate->title }}">
</a>

<div id="affilb-body-{{ $affilate->id }}" class="px-2 py-1 radius-10 my-2">
    {!! $affilate->body !!}</div>

@if (isset($affilate->link) || isset($affilate->product_link))
    <button id="affilb-link-{{ $affilate->id }}" onclick="jsurl('{{ route('slink', $affilate->id) }}',1)">
        <span>مشاهده قیمت و مشخصات</span>
        <img class="spb-arrow" src="{{ $ftp_path . 'files/other/images/next-light.png' }}" alt="shop">
    </button>
@endif
