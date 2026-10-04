const inputImgBtnUpload = $('#form-img-upload-btn');
const inputImgInputFile = $('#form-images-input');
const inputImgImageBox = $('#inputImageBox');
const inputImgImageList = $('#inputImageList');
const inputImgInputImages = $('#input-images');

function handleUploadClick() {
    $('#form-images-input').click();
}

$(document).ready(function () {
    inputImgInputFile.on('change', function () {
        const file = this.files[0];
        if (!file) return;

        const inputImageFormData = new FormData();
        inputImageFormData.append('image', file);

        if (typeof page !== 'undefined') {
            if (page === 'admin_create_comment' || page === 'create_affilate') {
                inputImageFormData.append('_token', csrf_t);
            }
        }

        const savingMsg = $('<span>', {
            id: 'form-img-upload-msg',
            text: 'در حال ذخیره‌سازی تصویر...'
        });
        inputImgBtnUpload.after(savingMsg);
        inputImgBtnUpload.hide();

        $.ajax({
            url: '/input-images-store',
            type: 'POST',
            data: inputImageFormData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.image_url && response.id) {
                    inputImgImageBox.show();

                    const imgWrapper = $(`
                            <div class="uploaded-image-wrapper" id="uimage-wrapper-${response.id}">
                                <img src="${response.image_url}" class="input-file-d-img">
                                <button type="button" onclick="removeUploadedImage('${response.id}')" class="remove-uploaded-image">حذف</button>
                            </div>
                        `);

                    inputImgImageList.append(imgWrapper);

                    const current_images = inputImgInputImages.val();
                    const new_images = current_images ?
                        current_images + ',' + response.id :
                        response.id;
                    inputImgInputImages.val(new_images);
                    $('#form-img-upload-msg').text('تصویر با موفقیت ذخیره شد');
                } else {
                    $('#form-img-upload-msg').text('آپلود موفق نبود');
                }
            },
            error: function () {
                $('#form-img-upload-msg').text('آپلود موفق نبود');
            },
            complete: function () {
                setTimeout(() => {
                    $('#form-img-upload-msg').remove();
                    inputImgBtnUpload.show();
                    inputImgInputFile.val('');
                }, 3000);
            }
        });
    });
});

function removeUploadedImage(imageId) {
    $('#uimage-wrapper-' + imageId).remove();

    let current_images = inputImgInputImages.val();
    if (!current_images) return;

    current_images = current_images.split(',').filter(id => id !== imageId.toString());
    inputImgInputImages.val(current_images.join(','));

    if ($('#inputImageList').children().length === 0) {
        inputImgImageBox.hide();
    }
}