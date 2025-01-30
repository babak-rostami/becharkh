{{-- @if (isset($top_users) && count($top_users) > 0)
    <div class="col-12 text-right">
        <div id="top-users-box">
            <div id="top-users-tbox">
                <span id="top-users-utitle">کاربران برتر</span>
                <br>
                <span id="top-users-ptitle">انجمن {{ $item->full_title ?? $item->title }}</span>
            </div>
            <div id="top-users-list">
                @foreach ($top_users as $tuser)
                    <a target="_blank" href="{{ route('user.dashboard', $tuser->username) }}" class="top-user-a">
                        <img src="{{ $tuser->thumb() }}">
                        <span>{{ $tuser->username }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        <span id="online-users-span">
            <img id="online-users-icon" src="{{ $ftp_path . 'files/other/images/blue-circle.png' }}">
            {{ $online_user_count }} نفر آنلاین</span>
    </div>
@else --}}
<div class="col-12 text-right">
    <span id="online-users-span">
        <img id="online-users-icon" src="{{ $ftp_path . 'files/other/images/blue-circle.png' }}">
        {{ $online_user_count }} نفر آنلاین</span>
    <img class="ousers-img lazy-load" data-src="{{ $ftp_path . 'files/other/images/ousers.gif' }}">
</div>
{{-- @endif --}}
