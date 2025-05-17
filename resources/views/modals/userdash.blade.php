<button class="comusr-link">
    <img class="comusr-style rounded-circle @if ($lazyload == 1) lazy-load @endif"
        @if ($lazyload == 1) data-src="{{ asset($dashuser->thumb()) }}" @else
            src="{{ asset($dashuser->thumb()) }}" @endif>
    <span class="comusr-name">{{ $dashuser->username }}</span>
</button>
