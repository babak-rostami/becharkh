$("#img-gal-modal").on("click", function (event) {
    if (
        $(event.target).attr("id") !== "gal-modal-img" &&
        $(event.target).attr("id") !== "gal-modal-prev" &&
        $(event.target).attr("id") !== "gal-modal-next"
    ) {
        closeGalleryModal();
    }
});

function clickGalleryImg(imgId, forw) {
    let modal = $("#img-gal-modal");
    let img = null;
    if (forw == "affilate") {
        img = $(`#${imgId}`);
    } else if (forw == "comment") {
        img = $(`#${imgId}`);
    } else if (forw == "item-gallery") {
        img = $(`#${imgId}`);
    }
    setNextAndPrev(forw, imgId);
    let modal_img = $("#gal-modal-img");
    if (modal.css("display") == "none") {
        modal.css("display", "flex");
    }
    let imgSrc = img.attr("src") ? img.attr("src") : img.data("src");
    modal_img.attr("src", imgSrc);
    $("#close-gal-img-modal").click(function () {
        closeGalleryModal();
    });
}

function setNextAndPrev(forw, imgId) {
    let nextBtn = $("#gal-modal-next");
    let prevBtn = $("#gal-modal-prev");
    if (forw === "affilate") {
        let imgNumber = parseInt(imgId.split("-").pop(), 10);
        let nextImgNumber = imgNumber + 1;
        let prevImgNumber = imgNumber - 1;

        let lastHyphenIndex = imgId.lastIndexOf("-");
        let baseImgId = imgId.substring(0, lastHyphenIndex);

        let nextImgSelector = `${baseImgId}-${nextImgNumber}`;
        let prevImgSelector = `${baseImgId}-${prevImgNumber}`;

        if ($(`#${nextImgSelector}`).length > 0) {
            nextBtn.off("click").on("click", function () {
                clickGalleryImg(nextImgSelector, "affilate");
            });
            nextBtn.show();
        } else {
            nextBtn.hide();
        }

        if ($(`#${prevImgSelector}`).length > 0) {
            prevBtn.off("click").on("click", function () {
                clickGalleryImg(prevImgSelector, "affilate");
            });
            prevBtn.show();
        } else {
            prevBtn.hide();
        }
    } else if (forw === "comment") {
        let imgNumber = parseInt(imgId.match(/-(\d+)$/)[1]);
        let nextImgNumber = imgNumber + 1;
        let prevImgNumber = imgNumber - 1;
        let prefix = imgId.slice(0, imgId.lastIndexOf("-") + 1);

        let nextImgSelector = `${prefix}${nextImgNumber}`;
        let prevImgSelector = `${prefix}${prevImgNumber}`;

        if ($(`#${nextImgSelector}`).length > 0) {
            nextBtn.off("click").on("click", function () {
                clickGalleryImg(nextImgSelector, "comment");
            });
            nextBtn.show();
        } else {
            nextBtn.hide();
        }
        if ($(`#${prevImgSelector}`).length > 0) {
            prevBtn.off("click").on("click", function () {
                clickGalleryImg(prevImgSelector, "comment");
            });
            prevBtn.show();
        } else {
            prevBtn.hide();
        }
    } else if (forw === "item-gallery") {
        let imgNumber = parseInt(imgId.match(/-(\d+)$/)[1]);
        let nextImgNumber = imgNumber + 1;
        let prevImgNumber = imgNumber - 1;
        let prefix = imgId.slice(0, imgId.lastIndexOf("-") + 1);

        let nextImgSelector = `${prefix}${nextImgNumber}`;
        let prevImgSelector = `${prefix}${prevImgNumber}`;

        if ($(`#${nextImgSelector}`).length > 0) {
            nextBtn.off("click").on("click", function () {
                clickGalleryImg(nextImgSelector, "item-gallery");
            });
            nextBtn.show();
        } else {
            nextBtn.hide();
        }
        if ($(`#${prevImgSelector}`).length > 0) {
            prevBtn.off("click").on("click", function () {
                clickGalleryImg(prevImgSelector, "item-gallery");
            });
            prevBtn.show();
        } else {
            prevBtn.hide();
        }
    }
}

function closeGalleryModal() {
    $("#img-gal-modal").hide();
}
