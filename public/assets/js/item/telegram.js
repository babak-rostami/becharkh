function copyTelLink(tel_link) {
    let btn = $('#itb-copy-btn');
    let originalText = btn.text();
    let textToCopy = "https://t.me/" + tel_link;

    navigator.clipboard.writeText(textToCopy).then(() => {
        btn.text("✅ لینک کپی شد").prop("disabled", true);

        setTimeout(function () {
            btn.text(originalText).prop("disabled", false);
        }, 5000);
    });
}