<div id="sur-box">
    <span class="sur-title-label">گزینه ها</span>
    <input class="sur-inputs" oninput="countSurCharacters(this,40)" id="surop1box" name="surop1" type="text"
        placeholder="گزینه اول...">
    <input class="sur-inputs" oninput="countSurCharacters(this,40)" id="surop2box" name="surop2" type="text"
        placeholder="گزینه دوم...">
    <div class="position-relative" id="surop3box">
        <input class="sur-inputs" oninput="countSurCharacters(this,40)" name="surop3" id="surop3" type="text"
            placeholder="گزینه سوم...">
        <img class="sopx" id="sopx3" onclick="surOpX3()"
            src="{{ $ftp_path . 'files/other/images/w-close.webp' }}">
    </div>
    <div class="position-relative" id="surop4box">
        <input class="sur-inputs" oninput="countSurCharacters(this,40)" name="surop4" id="surop4" type="text"
            placeholder="گزینه چهارم...">
        <img class="sopx" id="sopx4" onclick="surOpX4()"
            src="{{ $ftp_path . 'files/other/images/w-close.webp' }}">
    </div>
    <button type="button" class="btn btn-primary my-4" id="suraddop" onclick="addSurOption()">گزینه
        جدید +</button>
    <button type="button" class="btn btn-dark my-4" onclick="removeSurvey()">حذف نظرسنجی</button>
</div>
