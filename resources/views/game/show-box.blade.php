<div class="col-12 title-race-style text-center mb-2 pt-2 px-3 radius-10">
    <p class="f_title pt-2"></p>
    <div class="row justify-content-center bg-wht p-2 align-items-center">
        <div class="col-12 mt-2">
            <button type="button" class="btn game-answer-btn" data-toggle="modal" data-target="#show-race">جواب
                <img class="lazy-load" data-src="{{ asset('files/other/images/play-dark.gif') }}">
            </button>
            <button type="button" class="btn next-game-btn" onclick="startGame()">سوال
                بعدی
                <img data-src="{{ asset('files/other/images/next-game.gif') }}" class="w-24 lazy-load">
            </button>
            <hr>
        </div>
        <div class="col-12">
            <span class="font-15">طراح سوال</span>
            <a target="_blank" class="f_user_dash text-decoration-none"><span class="badge f_user"></span>
                <img class="profile-img-style game-user-img lazy-load"
                    data-src="{{ asset('files/other/images/profile.png') }}">
            </a>
            <br>
            @if (auth('user')->check())
                <a href="" class="btn next-game-btn mt-2" data-toggle="modal" data-target="#new-fgame-modal">یک
                    تست هوش
                    جدید ایجاد کنید
                    <img data-src="{{ asset('files/other/images/add.gif') }}" class="w-24 lazy-load">
                </a>
            @else
                <a href="" class="btn next-game-btn mt-2" data-toggle="modal" data-target="#login_user">یک تست
                    هوش جدید
                    ایجاد کنید
                    <img data-src="{{ asset('files/other/images/add.gif') }}" class="w-24 lazy-load">
                </a>
            @endif
        </div>
    </div>
</div>
