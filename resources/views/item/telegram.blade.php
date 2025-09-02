<div class="mt-4" id="item-telegram-box">
    <img id="itb-img" src="{{ asset('files/other/images/telegram-48.png') }}">

    <span id="itb-title">{{ $item->title_for_tel }}</span>
    <span id="itb-desc">{{ $item->desc_for_tel }}</span>

    <span id="itb-link">{{ $item->id_for_tel }}@</span>

    <button class="mt-3 btn btn-outline-primary" id="itb-copy-btn" onclick="copyTelLink('{{ $item->id_for_tel }}')">📋 کپی
        کنید</button>

    <a class="mt-3 btn btn-outline-dark" href="https://t.me/{{ $item->id_for_tel }}" target="_blank">یا
        اینجا کلیک
        کنید</a>
</div>
