<div class="row">
    <div class="col-12" id="modal-back-box">
    </div>

    <div class="col-12 d-sm-none">

        <div class="row justify-content-center bottom-menu-box">
            <div class="col-2 text-center buttom-menu-item p-2 overflow-hidden"
                @if ($user) onclick="gtudash('{{ $user->username }}')" @else onclick="gtudash()" @endif
                id="bm_open_dashbaord">
                @if ($user)
                    <img id="bm-user-img" alt="{{ $user->username }}" src="{{ asset($user->thumb()) }}">
                    <br>
                    <span class="n-active-btab-mobile" id="bm-user-txt">{{ str_limit($user->name, 12, '') }}</span>
                @else
                    <img id="bm-user-img" alt="user image" src="{{ $ftp_path . 'files/other/images/profile.png' }}">
                    <br>
                    <span class="n-active-btab-mobile" id="bm-user-txt">پروفایل</span>
                @endif
                @if ($user)
                    @if (!$user->getImage() || !$user->email_actived)
                        <img id="bm-war-icon-user" class="bm-war-icon-user" alt="warning"
                            src="{{ $ftp_path . 'files/other/images/warning16.webp' }}" alt="warning">
                    @endif
                @else
                    <img id="bm-war-icon-user" alt="warning"
                        src="{{ $ftp_path . 'files/other/images/warning16.webp' }}" alt="warning">
                @endif
            </div>
            <div class="col-2 text-center buttom-menu-item p-2">
                @if ($user)
                    @if (request()->is('new-question'))
                        <span class="text-white text-decoration-none d-block">
                            <img alt="favorite icon" src="{{ $ftp_path . 'files/other/images/add-20-blue.png' }}">
                            <br>
                            <span class="active-btab-mobile">نوشتن</span>
                        </span>
                    @else
                        <a href="{{ route('question.create') }}" class="text-white text-decoration-none d-block">
                            <img alt="favorite icon" src="{{ $ftp_path . 'files/other/images/add-20-gray.png' }}">
                            <br>
                            <span class="n-active-btab-mobile">نوشتن</span>
                        </a>
                    @endif
                @else
                    <a href="" class="c-bm-btn decor-none d-block" data-toggle="modal" data-dismiss="modal"
                        data-target="#login_user">
                        <img alt="favorite icon" src="{{ $ftp_path . 'files/other/images/add-20-gray.png' }}">
                        <br>
                        <span class="n-active-btab-mobile">نوشتن</span>
                    </a>
                @endif
            </div>
            <div class="col-2 text-center buttom-menu-item p-2">
                @if ($user)
                    @if (strpos(request()->fullUrl(), 'notifications') !== false)
                        <span class="text-white">
                            <img alt="notif icon" src="{{ $ftp_path . 'files/other/images/notif-blue.png' }}">
                            <br>
                            <span class="active-btab-mobile">پیام ها</span>
                        </span>
                    @else
                        <a href="{{ route('user.notifications') }}" class="text-white text-decoration-none d-block">
                            <img alt="notif icon" src="{{ $ftp_path . 'files/other/images/notif-gray.png' }}">
                            <br>
                            <span class="n-active-btab-mobile">پیام ها</span>
                            @if ($user->notif_count)
                                <span id="bm-unotif-count">{{ $user->notif_count }}</span>
                            @endif
                        </a>
                    @endif
                @else
                    <a href="" class="c-bm-btn decor-none d-block" data-toggle="modal" data-dismiss="modal"
                        data-target="#login_user">
                        <img alt="notif icon" src="{{ $ftp_path . 'files/other/images/notif-gray.png' }}">
                        <br>
                        <span class="n-active-btab-mobile">پیام ها</span>
                    </a>
                @endif
            </div>

            <div class="col-2 text-center buttom-menu-item p-2" onclick="openSearchModal()">
                <img alt="search icon" src="{{ $ftp_path . 'files/other/images/search-gray.png' }}">
                <br>
                <span class="n-active-btab-mobile">جستجو</span>
            </div>

            <div class="col-2 text-center buttom-menu-item p-2">
                @if (request()->is('/'))
                    <span class="text-white text-decoration-none">
                        <img alt="home" src="{{ $ftp_path . 'files/other/images/home-blue.png' }}">
                        <br>
                        <span class="active-btab-mobile">خانه</span>
                    </span>
                @else
                    <a href="{{ route('home') }}" class="text-white text-decoration-none">
                        <img alt="home" src="{{ $ftp_path . 'files/other/images/home-gray.png' }}">
                        <br>
                        <span class="n-active-btab-mobile">خانه</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
