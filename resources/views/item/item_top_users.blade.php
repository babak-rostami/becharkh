<div class="top-users-container">
    <span class="top-users-title">اعضای گروه</span>
    @if (!empty($top_users))
        <div class="top-users-list">
            @foreach ($top_users as $user)
                <a href="{{ $user['user_dash'] }}" class="top-user-card">
                    <div class="top-user-rank">
                        {{ $loop->iteration }}
                    </div>
                    @if ($user['user_has_img'] == 1)
                        <div class="top-user-avatar">
                            <img src="{{ $user['user_img'] }}" alt="{{ $user['username'] }}">
                        </div>
                    @else
                        <div class="top-user-avatar-circle" data-name="{{ $user['username'] }}"></div>
                    @endif
                    <div class="top-user-info">
                        <h3 class="top-user-name">{{ $user['username'] }}</h3>
                        <span class="top-user-score">{{ number_format($user['score']) }} امتیاز</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
