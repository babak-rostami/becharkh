<div class="d-inline-block">
    <button onclick="jsurl('{{ route('user.dashboard', $dashuser->username) }}',1)"
        id="udm-a-{{ $dashuser->id }}-{{ $itemid }}" target="_blank" class="udm-ua">
        <img class="comment-profile-style rounded-circle @if ($lazyload == 1) lazy-load @endif"
            @if ($lazyload == 1) data-src="{{ asset($dashuser->thumb()) }}" @else
            src="{{ asset($dashuser->thumb()) }}" @endif>
        <span>{{ $dashuser->username }}</span>
    </button>
</div>
