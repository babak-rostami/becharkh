<button type="button" id="form-img-upload-btn" onclick="handleUploadClick()">آپلود عکس</button>
<input type="file" id="form-images-input" accept="image/*">
<input type="hidden" name="images" id="input-images">

<input type="file" id="form-edit-image-input" accept="image/*" style="display:none;">

<div class="modal fade" id="delete-confirm-modal" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <p>حذف تصویر را تأیید می‌کنی؟</p>
                <button type="button" class="btn btn-dark" data-dismiss="modal">خیر حذف نشود</button>
                <button data-dismiss="modal" type="button" class="btn btn-danger" id="confirm-delete-img"
                    onclick="confirmDelete()">بله</button>
            </div>
        </div>
    </div>
</div>

<div id="inputImageBox" @if (!isset($object->iimages)) style="display:none" @endif>
    <div id="inputImageList">
        @if (isset($object->iimages))
            @foreach ($object->iimages as $img)
                <div class="uploaded-image-wrapper" id="form-img-img-box-{{ $img['id'] }}">
                    <img class="form-img-edit-img" id="form-img-edit-img-{{ $img['id'] }}"
                        src="{{ $ftp_path . $img['path'] }}">
                    <button type="button" class="form-img-edit-btn"
                        onclick="editFormImg('{{ $object->id }}','{{ $img['id'] }}')">ویرایش</button>
                    <button type="button" class="remove-uploaded-image"
                        onclick="deleteFormImg('{{ $object->id }}','{{ $img['id'] }}')">حذف</button>
                </div>
            @endforeach
        @endif
    </div>
    <div id="inputImageBoxLoading" class="hidden">
        <span>در حال بروزرسانی...</span>
    </div>
</div>
