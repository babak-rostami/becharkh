<div class="modal fade" id="share-page-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row mt-3">
                    <div class="col text-center cur-p" onclick="sentPageToTelegram()">
                        <img loading="lazy" src="{{ $ftp_path . 'files/other/images/telegram.png' }}">
                        <br>
                        <span>تلگرام</span>
                    </div>
                    <div class="col text-center cur-p" onclick="copyToClipboard()">
                        <img loading="lazy" src="{{ $ftp_path . 'files/other/images/chain.png' }}">
                        <br>
                        <span>کپی کردن آدرس</span>
                    </div>
                    <div class="col text-center cur-p" onclick="sentPageToWhatsapp()">
                        <img loading="lazy" src="{{ $ftp_path . 'files/other/images/whatsapp.png' }}">
                        <br>
                        <span>واتساپ</span>
                    </div>
                    <div class="col-12 mt-3 text-center">
                        <p id="copy-clipboard-done-span">آدرس کپی شد</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>