const input = document.getElementById("uploaded-image-input");
const preview = document.getElementById("uploaded-image");

input.addEventListener("change", () => {
    const formData = new FormData();
    if (input.files.length === 0) {
        return;
    }
    formData.append("image", input.files[0]);
    formData.append("_token", ic_csrf_token);

    $.ajax({
        url: ic_upload_image_route,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            $("#btn-loading-ic").show();
            $("#btn-upload-ic").hide();
        },
        success: function(data) {
            $("#btn-loading-ic").hide();
            $("#btn-upload-ic").show();
            $("#ic-compress-form-div").show();
            image_id = data.image_id;
            preview.src = data.filePath;
        },
        error: function() {
            $("#btn-loading-ic").hide();
            $("#btn-error-ic").show();
            setTimeout(() => {
                $("#btn-error-ic").hide();
                $("#btn-upload-ic").show();
            }, 2000);
        }
    });
});

function getRangeValue() {
    $("#ic-q-span").text($("#ic-qua-input").val());
}

function icHasChangeSize() {
    var hasChange = $("#ic-is-size-change").val();
    if (hasChange == 0) {
        $("#ic-size-change-input-div").hide();
    } else {
        $("#ic-size-change-input-div").show();
    }
}

function compressImage() {
    const compressData = new FormData();
    compressData.append("_token", ic_csrf_token);

    var img_id = image_id;
    var image_quality = $("#ic-qua-input").val();
    var image_format = $("#ic-image-format").val();
    compressData.append("image_id", img_id);
    compressData.append("image_quality", image_quality);
    compressData.append("image_format", image_format);

    var imgHasWH = $("#ic-is-size-change").val();
    if (imgHasWH == 1) {
        var image_width = $("#ic-image-width").val();
        var image_height = $("#ic-image-height").val();
        compressData.append("image_width", image_width);
        compressData.append("image_height", image_height);
    }

    $.ajax({
        url: ic_compress_image_route,
        type: "POST",
        data: compressData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            $("#compress-btn-loading-ic").show();
            $("#ic-compress-image-btn").hide();
        },
        success: function(data) {
            $("#compress-btn-loading-ic").hide();
            $("#ic-compress-image-btn").show();
            $("#ic-download-div").show();
            $("#ic-new-image-size").text(data.new_image_size / 1000000);
            $("#ic-output-img").attr("src", data.new_image);
            $("#ic-output-dwn-btn").attr("href", data.new_image_download);
        },
        error: function() {
            $("#compress-btn-loading-ic").hide();
            $("#compress-btn-error-ic").show();
            setTimeout(() => {
                $("#compress-btn-error-ic").hide();
                $("#ic-compress-image-btn").show();
            }, 2000);
        }
    });
}
function icDownloadBtnClick() {
    $("#ic-output-dwn-btn").hide();
    $("#ic-output-dwning-btn").show();
    setTimeout(() => {
        $("#ic-output-dwn-btn").show();
        $("#ic-output-dwning-btn").hide();
    }, 6000);
}
