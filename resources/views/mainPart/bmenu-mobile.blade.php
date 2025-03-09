<div class="row">
    <div class="col-12" id="modal-back-box">
    </div>

    <div class="col-12 d-sm-none">

        {{-- <div class="row pt-3 pb-2" id="dropdown-add-bottom-menu">
            <div class="col-12 text-center">
                <span id="bm-new-bt">جدید</span>
            </div>
            <div class="col-12 text-right py-2">
                <a href="{{ route('new.ad') }}" class="decor-none d-block">
                    <img class="bm-npost-img lazy-load" data-src="{{ $ftp_path . 'files/other/images/bm-new-ad.webp' }}"
                        alt="new post">
                    <div class="bm-new-text-box">
                        <span class="bm-n-title">ثبت آگهی</span>
                        <br>
                        <span class="bm-n-desc">محصولات یا خدمات خود را آگهی کنید</span>
                    </div>
                </a>
            </div>
            <div class="col-12 text-right py-2">
                <a href="{{ route('question.create') }}" class="decor-none d-block">
                    <img class="bm-npost-img lazy-load"
                        data-src="{{ $ftp_path . 'files/other/images/bm-new-ques.webp' }}" alt="new post">
                    <div class="bm-new-text-box">
                        <span class="bm-n-title">سوال جدید</span>
                        <br>
                        <span class="bm-n-desc">سوال خود را در انجمن مطرح کنید</span>
                    </div>
                </a>
            </div>
        </div> --}}
        {{-- <div class="row pt-2 pb-4" id="user_dash_menu">
            <div class="col-12 mb-2 text-right">
                <img class="lazy-load c-bm-btn" id="ud-close-img"
                    data-src="{{ $ftp_path . 'files/other/images/x-16.webp' }}">
            </div>
            <div class="col-12 text-right mb-4">
                @if ($user)
                    <div class="d-flex">
                        <img id="ud_user_img" class="lazy-load" data-src="{{ asset($user->thumb()) }}" alt="user image">
                        <div>
                            <a id="ud-go-udash" href="{{ route('user.dashboard.edit') }}">
                                ویرایش اطلاعات
                                <img class="lazy-load"
                                    data-src="{{ $ftp_path . 'files/other/images/edit-profile2.png' }}">
                            </a>
                            <br>
                            <span id="ud-name">{{ $user->name }}</span>
                        </div>
                    </div>
                @else
                    <img id="ud_user_img" class="lazy-load"
                        data-src="{{ $ftp_path . 'files/other/images/profile.png' }}" alt="user image">
                    <span id="ud-name">کاربر مهمان</span>
                @endif

                <div>
                    <span id="ud-money-title">موجودی:</span>
                    @if ($user)
                        <span id="ud-money-amount" class="convert-m">{{ $user->money }}</span>
                        <span id="ud-money-currency">تومان</span>

                        <a class="ud-inc-money c-bm-btn" href="" data-toggle="modal"
                            data-target="#charge-account">افزایش
                            اعتبار +</a>
                    @else
                        <span>وارد حساب کاربری خود شوید</span>
                    @endif
                </div>
                <hr>

                @if ($user)
                    @if (!$user->getImage())
                        <img class="ud-prog-img lazy-load"
                            data-src="{{ $ftp_path . 'files/other/images/warning16.webp' }}">
                        <span class="ud-prog-msg">عکس پروفایل خود را انتخاب نکرده اید</span>
                        <br>
                    @endif
                    @if (!$user->email_actived)
                        <img class="ud-prog-img lazy-load"
                            data-src="{{ $ftp_path . 'files/other/images/warning16.webp' }}">
                        <span class="ud-prog-msg">ایمیل خود را تایید نکرده اید!</span>
                        <br>
                    @endif
                @endif

                @if ($user)
                    <div class="row">
                        <div class="col-4">
                            <a class="ud-list-a" href="{{ route('user.messages') }}">
                                <img class="lazy-load"
                                    data-src="{{ $ftp_path . 'files/other/images/chat-blue2.png' }}">
                                پیام ها
                            </a>
                        </div>
                        <div class="col-4">
                            <a class="ud-list-a" href="{{ route('user.dashboard.edit', 'ad') }}">
                                <img class="lazy-load"
                                    data-src="{{ $ftp_path . 'files/other/images/bm-new-ad.webp' }}">
                                آگهی ها
                            </a>
                        </div>
                        <div class="col-4">
                            <a class="ud-list-a" href="{{ route('user.dashboard.edit', 'forum') }}">
                                <img class="lazy-load" data-src="{{ $ftp_path . 'files/other/images/question1.png' }}">
                                سوال ها
                            </a>
                        </div>
                    </div>
                @else
                    <span class="font-14">برای فعالیت در انجمن وارد حساب کاربری خود شوید</span>
                    <br>
                    <a class="c-bm-btn" href="" id="ud-setting-txt" data-toggle="modal" data-dismiss="modal"
                        data-target="#login_user">ورود به حساب کاربری</a>
                @endif

                @if ($user)
                    <div class="text-center mt-4">
                        <a class="decor-none" href="{{ route('user.logout') }}">
                            خروج از حساب
                        </a>
                    </div>
                @endif
            </div>
        </div> --}}
        <div class="row justify-content-center bottom-menu-box">
            <div class="col-2 text-center buttom-menu-item p-2 overflow-hidden" onclick="gtudash()" id="bm_open_dashbaord">
                @if ($user)
                    <img id="bm-user-img" src="{{ asset($user->thumb()) }}">
                    <br>
                    <span class="n-active-btab-mobile" id="bm-user-txt">{{ str_limit($user->name, 12, '') }}</span>
                @else
                    <img id="bm-user-img" src="{{ $ftp_path . 'files/other/images/profile.png' }}">
                    <br>
                    <span class="n-active-btab-mobile" id="bm-user-txt">پروفایل</span>
                @endif
                @if ($user)
                    @if (!$user->getImage() || !$user->email_actived)
                        <img id="bm-war-icon-user" src="{{ $ftp_path . 'files/other/images/warning16.webp' }}"
                            alt="warning">
                    @endif
                @else
                    <img id="bm-war-icon-user" src="{{ $ftp_path . 'files/other/images/warning16.webp' }}"
                        alt="warning">
                @endif
            </div>
            <div class="col-2 text-center buttom-menu-item p-2">
                @if ($user)
                    @if (request()->is('favorite'))
                        <a class="text-white text-decoration-none d-block">
                            <img src="{{ $ftp_path . 'files/other/images/foryou-blue.png' }}">
                            <br>
                            <span class="active-btab-mobile">ذخیره</span>
                        </a>
                    @else
                        <a href="{{ route('favorite.index') }}" class="text-white text-decoration-none d-block">
                            <img src="{{ $ftp_path . 'files/other/images/foryou-gray.png' }}">
                            <br>
                            <span class="n-active-btab-mobile">ذخیره</span>
                        </a>
                    @endif
                @else
                    <a href="" class="c-bm-btn decor-none d-block" data-toggle="modal" data-dismiss="modal"
                        data-target="#login_user">
                        <img src="{{ $ftp_path . 'files/other/images/foryou-gray.png' }}">
                        <br>
                        <span class="n-active-btab-mobile">ذخیره</span>
                    </a>
                @endif
            </div>
            <div class="col-2 text-center buttom-menu-item p-2">
                @if ($user)
                    @if (strpos(request()->fullUrl(), 'notifications') !== false)
                        <span class="text-white">
                            <img src="{{ $ftp_path . 'files/other/images/notif-blue.png' }}">
                            <br>
                            <span class="active-btab-mobile">پیام ها</span>
                        </span>
                    @else
                        <a href="{{ route('user.notifications') }}" class="text-white text-decoration-none d-block">
                            <img src="{{ $ftp_path . 'files/other/images/notif-gray.png' }}">
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
                        <img src="{{ $ftp_path . 'files/other/images/notif-gray.png' }}">
                        <br>
                        <span class="n-active-btab-mobile">پیام ها</span>
                    </a>
                @endif
            </div>

            <div class="col-2 text-center buttom-menu-item p-2" onclick="openSearchModal()">
                <img src="{{ $ftp_path . 'files/other/images/search-gray.png' }}">
                <br>
                <span class="n-active-btab-mobile">جستجو</span>
            </div>
            <div class="col-2 text-center buttom-menu-item p-2 c-bm-btn" onclick="openBottomMenuMore()">
                <img id="bm-more-img" src="{{ $ftp_path . 'files/other/images/more-menu.webp' }}">
                <br>
                <span id="bm-more-txt" class="n-active-btab-mobile">بیشتر</span>
            </div>
            <div class="col-3 text-center buttom-menu-item p-2">
                @if (request()->is('/'))
                    <a class="text-white text-decoration-none">
                        <img src="{{ $ftp_path . 'files/other/images/home-blue.png' }}">
                        <br>
                        <span class="active-btab-mobile">خانه</span>
                    </a>
                @else
                    <a href="{{ route('home') }}" class="text-white text-decoration-none">
                        <img src="{{ $ftp_path . 'files/other/images/home-gray.png' }}">
                        <br>
                        <span class="n-active-btab-mobile">خانه</span>
                    </a>
                @endif
            </div>
            <div class="col-3 text-center buttom-menu-item p-2">
                @if (request()->is('ads') || request()->is('ads/*'))
                    <a class="text-white text-decoration-none">
                        <img src="{{ $ftp_path . 'files/other/images/shop-blue.png' }}">
                        <br>
                        <span class="active-btab-mobile">بازار</span>
                    </a>
                @else
                    <a href="{{ route('ads.index') }}" class="text-white text-decoration-none">
                        <img src="{{ $ftp_path . 'files/other/images/shop-gray.png' }}">
                        <br>
                        <span class="n-active-btab-mobile">بازار</span>
                    </a>
                @endif
            </div>
            <div class="col-3 text-center buttom-menu-item p-2">
                @if (strpos(request()->fullUrl(), 'forum') !== false && strpos(request()->fullUrl(), 's=1') !== false)
                    <a class="text-white text-decoration-none">
                        <img src="{{ $ftp_path . 'files/other/images/chat-blue.png' }}">
                        <br>
                        <span class="active-btab-mobile">نظرات</span>
                    </a>
                @else
                    <a href="{{ route('question.index') . '?s=1' }}" class="text-white text-decoration-none">
                        <img src="{{ $ftp_path . 'files/other/images/chat-gray.png' }}">
                        <br>
                        <span class="n-active-btab-mobile">نظرات</span>
                    </a>
                @endif
            </div>
            <div class="col-3 text-center buttom-menu-item p-2">
                @if (
                    (strpos(request()->fullUrl(), 'forum') !== false || strpos(request()->fullUrl(), 'question/create') !== false) &&
                        strpos(request()->fullUrl(), 's=1') === false)
                    <a class="text-white text-decoration-none">
                        <img src="{{ $ftp_path . 'files/other/images/group-blue.png' }}">
                        <br>
                        <span class="active-btab-mobile">انجمن</span>
                    </a>
                @else
                    <a href="{{ route('question.index') }}" class="text-white text-decoration-none">
                        <img src="{{ $ftp_path . 'files/other/images/group-gray.png' }}">
                        <br>
                        <span class="n-active-btab-mobile">انجمن</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
