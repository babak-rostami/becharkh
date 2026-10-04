<a target="_blank" class="comusr-link" rel="nofollow" href="{{ route('user.dashboard', $dashuser->username) }}">
    <div class="comusr-container">
        <img alt="عکس {{ $dashuser->name }}" class="@if ($dashuser->getImage()) comusr-style @else dcomusr-style @endif"
            src="{{ asset($dashuser->thumb()) }}">

        <div class="comusr-name-div">
            <span class="comusr-username">{{ $dashuser->username }}</span>
            <span class="comusr-name">{{ $dashuser->name }}</span>
        </div>
    </div>
</a>
@if ($dashuser->body)
    <span class="comusr-biography">{{ $dashuser->body }}</span>
@endif