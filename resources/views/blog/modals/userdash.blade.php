<div class="d-inline-block">
    <div id="udm-a-{{ $dashuser->id }}-{{ $itemid }}" class="cur-p"
        onclick="openUserDashModal('{{ $dashuser->id }}','{{ $itemid }}')">
        <img class="comment-profile-style rounded-circle @if ($lazyload == 1) lazy-load @endif"
            @if ($lazyload == 1) data-src="{{ asset($dashuser->thumb()) }}" @else
            src="{{ asset($dashuser->thumb()) }}" @endif>
        <span>{{ $dashuser->username }}</span>
    </div>
    <div class="udm-box shadow" id="udm-box-{{ $dashuser->id }}-{{ $itemid }}">
        <div class="udm-ver">
            <img class="udm-img @if ($lazyload == 1) lazy-load @endif"
                @if ($lazyload == 1) data-src="{{ asset($dashuser->thumb()) }}" @else
            src="{{ asset($dashuser->thumb()) }}" @endif>
            <div class="d-inline-block">
                <span class="udm-uname">{{ $dashuser->username }}</span>
                <br>
                <a class="udm-dash-a" rel="nofollow" href="{{ route('user.dashboard', $dashuser->username) }}">مشاهده
                    پروفایل</a>
            </div>
        </div>
        <br>
        @foreach ($dashuser->jobs() as $jkey => $job)
            <small class="badge badge-light">{{ $job->title }}</small>
        @endforeach
        @if (!isset($jkey))
            <span class="udm-njob-span">به زودی شغل و مهارت خود را ثبت میکنم</span>
        @endif
    </div>
</div>
