<button class="comusr-link">
    <img class="
    @if ($dashuser->getImage()) comusr-style
    @else
    dcomusr-style @endif
    @if ($lazyload == 1) lazy-load @endif"
        @if ($lazyload == 1) data-src="{{ asset($dashuser->thumb()) }}" @else
            src="{{ asset($dashuser->thumb()) }}" @endif>
    <span class="comusr-name">{{ $dashuser->username }}</span>
</button>
