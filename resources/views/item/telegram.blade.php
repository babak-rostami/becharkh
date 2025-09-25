<div class="mt-4" id="item-telegram-box">
    <img id="itb-img" alt="telegram icon" src="{{ $ftp_path . 'files/other/images/telegram-48.png' }}">
    <h2 id="itb-title" class="fw-bold">گروه تلگرام {{ $item->full_title }}</h2>
    <p id="itb-desc" class="text-muted">برای عضویت شماره تلگرام خود را وارد کنید</p>
    <input type="text" id="itb-phone-input" name="phone" class="form-control text-center"
        placeholder="مثال: 09121234567" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
    <input type="hidden" id="item_id" name="item_id" value="{{ $item->id }}">

    <button class="mt-3 btn btn-lg btn-dark w-100" id="itb-copy-btn" onclick="saveTelNumber()">عضویت</button>

    <div id="itb-message" class="mt-2"></div>
</div>
