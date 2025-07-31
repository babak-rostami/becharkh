<a class="btn btn-light mt-4 w-100" id="aths-btn" href="" data-toggle="modal" data-dismiss="modal"
    data-target="#add-to-home-screen-help">
    ذخیره انجمن روی موبایل
    <img alt="save icon" class="mr-1 w-24" id="aths-btn-icon" src="{{ $ftp_path . 'files/other/images/telegram.png' }}">
</a>
<div class="modal fade" id="add-to-home-screen-help" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 text-center">
                        <img alt="becharkh logo" id="aths-timg" src="{{ $ftp_path . 'files/other/images/logo1.png' }}">
                        <span id="aths-title">ذخیره انجمن روی موبایل</span>

                        <div class="btn-group mb-3" role="group">
                            <button id="btn-android" class="btn btn-primary ml-1">آموزش اندروید</button>
                            <button id="btn-iphone" class="btn btn-outline-primary mr-1">آموزش آیفون</button>
                        </div>

                        <div id="android-instructions" class="mt-4">
                            <p class="aths-way">1. روی دکمه
                                <img alt="more icon" src="{{ $ftp_path . 'files/other/images/chrome-more.png' }}">
                                در نوار بالای صفحه کلیک کنید.
                            </p>
                            <p class="aths-way">2. روی گزینه Add to Home Screen کلیک کنید</p>
                            <br>
                            <p class="aths-way">3. روی Add کلیک کنید.</p>
                            <br>
                            <p class="aths-way">4. دوباره Add را بزنید تا تایید شود.</p>
                        </div>

                        <div id="iphone-instructions" class="mt-4" style="display:none;">
                            <p class="aths-way">1. در مرورگر Safari پایین صفحه روی آیکون
                                <img alt="share iconn" src="{{ $ftp_path . 'files/other/images/ios-share.png' }}">
                                کلیک کنید.
                            </p>
                            <p class="aths-way">2. گزینه Add to Home Screen را انتخاب کنید.</p>
                            <br>
                            <p class="aths-way">3. روی Add بزنید.</p>
                        </div>

                        <p id="aths-suc">انجمن با موفقیت به صفحه اصلی اضافه شد.</p>
                        <button class="btn btn-info mt-4 w-100" data-dismiss="modal">متوجه شدم</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
