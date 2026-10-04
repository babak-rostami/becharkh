let share_page_url = null;

function sharePage(page_url) {
    share_page_url = page_url;
    $("#share-page-modal").modal("show");
}

function copyToClipboard() {
    navigator.clipboard
        .writeText(share_page_url)
        .then(() => {
            $("#copy-clipboard-done-span").css("display", "block");
            setTimeout(() => {
                $("#copy-clipboard-done-span").css("display", "none");
            }, 4000);
        })
        .catch(err => {
            console.error("Could not copy text: ", err);
        });
}
function sentPageToTelegram() {
    var shareUrl =
        "https://t.me/share/url?url=" +
        encodeURIComponent(share_page_url) +
        "&text=" +
        encodeURIComponent($("#page-title").text());
    window.open(shareUrl, "_blank");
}

function sentPageToWhatsapp() {
    var shareUrl =
        "https://api.whatsapp.com/send?text=" + encodeURIComponent(share_page_url);
    window.open(shareUrl, "_blank");
}
