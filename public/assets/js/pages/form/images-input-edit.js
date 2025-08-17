let editTargetObjectId = null;
let editTargetImageId = null;
const inputImgBtnUpload = $('#form-img-upload-btn');
const inputImgInputFile = $('#form-images-input');
const inputImgImageBox = $('#inputImageBox');
const inputImgImageList = $('#inputImageList');
const inputImgInputImages = $('#input-images');
const inputImgInputFileEdit = $('#form-edit-image-input');
const inputImgBoxLoading = $('#inputImageBoxLoading');

function handleUploadClick() {
    $('#form-images-input').click();
}

function editFormImg(objectId, imageId) {
    editTargetObjectId = objectId;
    editTargetImageId = imageId;
    $('#form-edit-image-input').click();
}

$(document).ready(function () {
    inputImgInputFile.on('change', function () {
        const file = this.files[0];
        if (!file) return;

        const inputImageFormData = new FormData();
        inputImageFormData.append('image', file);

        if (typeof page !== 'undefined') {
            if (page === 'admin_edit_comment' || page === 'edit_affilate') {
                inputImageFormData.append('_token', csrf_t);
            }
        }

        const savingMsg = $('<span>', {
            id: 'form-img-upload-msg',
            text: 'در حال ذخیره‌سازی تصویر...'
        });
        inputImgBtnUpload.after(savingMsg);
        inputImgBtnUpload.hide();

        inputImgBoxLoading.removeClass("hidden");

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
                    inputImgBoxLoading.addClass("hidden");
                }, 3000);
            }
        });
    });

    // for edit image
    inputImgInputFileEdit.on('change', function () {
        const file = this.files[0];
        if (!file || !editTargetImageId) return;

        const inputImageFormDataEdit = new FormData();
        inputImageFormDataEdit.append('image', file);
        inputImageFormDataEdit.append('object_id', editTargetObjectId);
        inputImageFormDataEdit.append('image_id', editTargetImageId);

        if (typeof page !== 'undefined') {
            if (page === 'admin_edit_comment') {
                inputImageFormDataEdit.append('is_for', 'ccomment');
                inputImageFormDataEdit.append('_token', csrf_t);
            } else if (page === 'edit_affilate') {
                inputImageFormDataEdit.append('is_for', 'affilate');
                inputImageFormDataEdit.append('_token', csrf_t);
            }
        }

        inputImgBoxLoading.removeClass("hidden");

        $.ajax({
            url: '/input-images-update',
            type: 'POST',
            data: inputImageFormDataEdit,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success && response.new_image_url) {
                    $(`#form-img-edit-img-${editTargetImageId}`)
                        .attr('src', response.new_image_url + '?_=' + new Date()
                            .getTime());
                } else {
                    alert('خطا در آپدیت تصویر');
                }
            },
            error: function () {
                alert('خطا در آپدیت تصویر');
            },
            complete: function () {
                $('#form-edit-image-input').val('');
                editTargetObjectId = null;
                editTargetImageId = null;
                inputImgBoxLoading.addClass("hidden");
            }
        });
    });
    // end for edit image
});

function removeUploadedImage(imageId) {
    $('#uimage-wrapper-' + imageId).remove();

    let current_images = inputImgInputImages.val();
    if (!current_images) return;

    current_images = current_images.split(',').filter(id => id !== imageId.toString());
    inputImgInputImages.val(current_images.join(','));

    if (inputImgImageList.children().length === 0) {
        inputImgImageBox.hide();
    }
}

//for delete image
let deleteTargetCommentId = null;
let deleteTargetImageId = null;

function deleteFormImg(commentId, imageId) {
    deleteTargetCommentId = commentId;
    deleteTargetImageId = imageId;
    $('#delete-confirm-modal').modal('show');
}

function confirmDelete() {
    inputImgBoxLoading.removeClass("hidden");
    let delete_csrf = null;
    let is_for = null;
    if (typeof page !== 'undefined') {
        if (page === 'admin_edit_comment') {
            delete_csrf = csrf_t;
            is_for = 'ccomment';
        } else if (page === 'edit_affilate') {
            delete_csrf = csrf_t;
            is_for = 'affilate';
        }
    }

    $.ajax({
        url: '/input-images-destroy',
        type: 'POST',
        data: {
            object_id: deleteTargetCommentId,
            image_id: deleteTargetImageId,
            is_for: is_for,
            _token: delete_csrf
        },
        success: function (response) {
            if (response.success) {
                $(`#form-img-img-box-${deleteTargetImageId}`).remove();
            } else {
                alert('حذف تصویر موفق نبود');
            }
        },
        error: function () {
            alert('حذف تصویر موفق نبود');
        },
        complete: function () {
            if (inputImgImageList.children().length === 0) {
                inputImgImageBox.hide();
            }
            deleteTargetCommentId = null;
            deleteTargetImageId = null;
            inputImgBoxLoading.addClass("hidden");
        }
    });
}
//end for delete image