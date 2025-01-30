@if (isset($item->prices))
    <div class="col-12 text-right" id="item-price-div">
        <h2 id="item-price-title">قیمت {{ $item->full_title ?? $item->title }}</h2>
        <a id="item-price-adpage-btn" href="{{ $item->withParentsAdvertiseUrl() }}">آگهی ها
            <img src="{{ $ftp_path . 'files/other/images/next.png' }}">
        </a>
        <ul id="item-price-ul">
            @foreach ($item->prices as $key => $price)
                @if ($key != 'last_update')
                    <li class="item-price-li">{{ $key }} : {{ $price }} تومان</li>
                @endif
            @endforeach
        </ul>
        <span id="item-price-messages"></span>
    </div>
@endif
