<button {{-- onclick="jsurl('{{ route('user.dashboard', $dashuser->username) }}',1)" --}} class="comusr-link">
    <img class="comusr-style rounded-circle @if ($lazyload == 1) lazy-load @endif"
        @if ($lazyload == 1) data-src="{{ asset($dashuser->thumb()) }}" @else
            src="{{ asset($dashuser->thumb()) }}" @endif>
    <span class="comusr-name">{{ $dashuser->username }}</span>
    @switch($dashuser->level)
        @case(1)
            <span class="comusr-label comusr-label-1">با تجربه</span>
        @break

        @case(2)
            <span class="comusr-label comusr-label-2">حرفه‌ای</span>
        @break

        @case(3)
            <span class="comusr-label comusr-label-3">متخصص</span>
        @break

        @default
            <span class="comusr-label comusr-label-0">کاوشگر</span>
    @endswitch
</button>
