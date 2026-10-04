let editor_id = null;
let editor2_id = null;
let comment_editor;
let comment_editor2;
setTimeout(() => {
    if (typeof page !== "undefined") {
        if (
            // page == "comment" ||
            // page == "admin_edit_comment" ||
            page == "admin_create_comment" ||
            page == "admin_edit_qanswer" ||
            // page == "show_question" ||
            page == "edit_question_admin" ||
            page == "edit_question" ||
            page == "create_question_admin" ||
            page == "create_question" ||
            page == "admin_qanswers" ||
            page == "create_blog" ||
            page == "edit_blog" ||
            // page == "show_product" ||
            page == "admin_create_product_comment" ||
            page == "admin_edit_product_comment"
        ) {
            editor_id = "#cm-input";
        } else if (
            page == "create_affilate" ||
            page == "edit_affilate" ||
            page == "admin_edit_comment"
        ) {
            editor_id = "#cm-input";
            editor2_id = "#cm-input-2";
        }

        const pluginsToRemove = ["MediaEmbed", "CKFinder"];
        const toolbarItems = [
            "heading",
            "|",
            "bold",
            "italic",
            "|",
            "bulletedList",
            "numberedList",
            "|",
            "blockQuote",
            "|",
            "insertTable",
            "imageUpload",
            "undo",
            "redo"
        ];
        if (
            page !== "admin_edit_comment" &&
            page !== "create_question_admin" &&
            page !== "edit_question_admin"
        ) {
            pluginsToRemove.push("Link");
        } else {
            toolbarItems.splice(3, 0, "link");
        }

        const editorElement = document.querySelector(editor_id);
        if (editorElement) {
            ClassicEditor.create(document.querySelector(editor_id), {
                language: "fa",
                ckfinder: {
                    uploadUrl: editor_img_upload_route
                },
                heading: {
                    options: [
                        {
                            model: "paragraph",
                            title: "پاراگراف",
                            class: "ck-heading_paragraph"
                        },
                        {
                            model: "heading2",
                            view: "h2",
                            title: "عنوان 2",
                            class: "ck-heading_heading2"
                        },
                        {
                            model: "heading3",
                            view: "h3",
                            title: "عنوان 3",
                            class: "ck-heading_heading3"
                        },
                        {
                            model: "heading4",
                            view: "h4",
                            title: "عنوان 4",
                            class: "ck-heading_heading4"
                        },
                        {
                            model: "heading5",
                            view: "h5",
                            title: "عنوان 5",
                            class: "ck-heading_heading5"
                        },
                        {
                            model: "heading6",
                            view: "h6",
                            title: "عنوان 6",
                            class: "ck-heading_heading6"
                        }
                    ]
                },
                toolbar: toolbarItems,
                removePlugins: pluginsToRemove
            })
                .then(editor => {
                    comment_editor = editor;
                    editor.editing.view.change(writer => {
                        writer.setStyle(
                            "min-height",
                            "100px",
                            editor.editing.view.document.getRoot()
                        );
                    });
                })
                .catch(error => {
                    console.error(error);
                });
        }

        if (editor2_id != null) {
            const editor2Element = document.querySelector(editor2_id);
            if (editor2Element) {
                ClassicEditor.create(document.querySelector(editor2_id), {
                    language: "fa",
                    ckfinder: {
                        uploadUrl: editor_img_upload_route
                    },
                    heading: {
                        options: [
                            {
                                model: "paragraph",
                                title: "پاراگراف",
                                class: "ck-heading_paragraph"
                            },
                            {
                                model: "heading2",
                                view: "h2",
                                title: "عنوان 2",
                                class: "ck-heading_heading2"
                            },
                            {
                                model: "heading3",
                                view: "h3",
                                title: "عنوان 3",
                                class: "ck-heading_heading3"
                            },
                            {
                                model: "heading4",
                                view: "h4",
                                title: "عنوان 4",
                                class: "ck-heading_heading4"
                            },
                            {
                                model: "heading5",
                                view: "h5",
                                title: "عنوان 5",
                                class: "ck-heading_heading5"
                            },
                            {
                                model: "heading6",
                                view: "h6",
                                title: "عنوان 6",
                                class: "ck-heading_heading6"
                            }
                        ]
                    },
                    toolbar: toolbarItems,
                    removePlugins: pluginsToRemove
                })
                    .then(editor2 => {
                        comment_editor2 = editor2;
                        editor2.editing.view.change(writer => {
                            writer.setStyle(
                                "min-height",
                                "100px",
                                editor2.editing.view.document.getRoot()
                            );
                        });
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        }
    }
}, 1000);

if (typeof page !== "undefined") {
    if (page == "comment" || page == "show_question" || page == "show_product")
        $(document).ready(function() {
            $("#cm-input").on("input", function() {
                $(this).css("height", "auto");
                $(this).css("height", Math.max(this.scrollHeight, 100) + "px");
            });
        });
}

let editorTimeoutId;

function editorCommentSend() {
    let btn = $("#comment-editor-btn");
    let load_btn = $("#comment-editor-load-btn");
    let comment_form = $("#cm_form");
    let submit_form = 0;
    let editor_contents = comment_editor.getData();
    let error_span = $("#comeditor-msg");

    if (hasAtLeastOneParagraph(editor_contents)) {
        if ($("#sur-box").length && $("#sur-box").is(":visible")) {
            if (checkSurIsComplete()) {
                submit_form = 1;
            } else {
                clearTimeout(editorTimeoutId);
                error_span.text("بخش نظر سنجی را تکمیل کنید");
            }
        } else {
            submit_form = 1;
        }
    } else {
        clearTimeout(editorTimeoutId);
        error_span.text("نظر خود را بنویسید...");
    }
    if (submit_form == 1) {
        $("#add-survey-btn").hide();
        btn.hide();
        load_btn.show();
        setTimeout(() => {
            comment_form.submit();
        }, 3000);
    } else {
        error_span.css("display", "block");
        editorTimeoutId = setTimeout(() => {
            error_span.text("");
            error_span.hide();
        }, 5000);
    }
}

function userCcommentSend() {
    let btn = $("#comment-editor-btn");
    let load_btn = $("#comment-editor-load-btn");
    let comment_form = $("#cm_form");
    let submit_form = 0;
    let editor_contents = $("#cm-input")
        .val()
        .trim();
    let error_span = $("#comeditor-msg");

    if (editor_contents !== "") {
        if ($("#sur-box").length && $("#sur-box").is(":visible")) {
            if (checkSurIsComplete()) {
                submit_form = 1;
            } else {
                clearTimeout(editorTimeoutId);
                error_span.text("بخش نظر سنجی را تکمیل کنید");
            }
        } else {
            submit_form = 1;
        }
    } else {
        clearTimeout(editorTimeoutId);
        error_span.text("نظر خود را بنویسید...");
    }
    if (submit_form == 1) {
        $("#add-survey-btn").hide();
        btn.hide();
        load_btn.show();
        setTimeout(() => {
            comment_form.submit();
        }, 3000);
    } else {
        error_span.css("display", "block");
        editorTimeoutId = setTimeout(() => {
            error_span.text("");
            error_span.hide();
        }, 5000);
    }
}

function editorQuestionUpdate() {
    let btn = $("#comment-editor-btn");
    let load_btn = $("#comment-editor-load-btn");
    let comment_form = $("#qform");
    let error_span = $("#comeditor-msg");
    let editor_contents = comment_editor.getData();
    if (hasAtLeastOneParagraph(editor_contents)) {
        if ($("#title").val().length >= 20 && $("#title").val().length < 60) {
            if (
                $("#category_id")
                    .val()
                    .trim()
            ) {
                btn.hide();
                load_btn.show();
                setTimeout(() => {
                    comment_form.submit();
                }, 3000);
                return;
            } else {
                error_span.text("دسته بندی را انتخاب کنید");
            }
        } else {
            error_span.text("عنوان سوال باید حداقل 20 کاراکتر باشد");
        }
    } else {
        error_span.text("حداقل یک پاراگراف بنویسید");
    }
    clearTimeout(editorTimeoutId);
    error_span.css("display", "block");
    editorTimeoutId = setTimeout(() => {
        error_span.text("");
        error_span.hide();
    }, 5000);
}

function editorQuestionStore() {
    let btn = $("#comment-editor-btn");
    let load_btn = $("#comment-editor-load-btn");
    let comment_form = $("#qform");
    let error_span = $("#comeditor-msg");
    let editor_contents = comment_editor.getData();
    let cat = $("#category_id");
    if (hasAtLeastOneParagraph(editor_contents)) {
        if ($("#title").val().length >= 20 && $("#title").val().length < 60) {
            if (!cat.length || (cat.val() || "").trim()) {
                // if (send_question_after_login == 1) {
                //     $("#login_user").modal("show");
                // } else {
                // if ($("#sur-box").length && $("#sur-box").is(":visible")) {
                // if (checkSurIsComplete()) {
                btn.hide();
                load_btn.show();
                setTimeout(() => {
                    comment_form.submit();
                }, 3000);
                return;
                // } else {
                //     error_span.text("بخش نظر سنجی را تکمیل کنید");
                // }
                // } else {
                //     btn.hide();
                //     load_btn.show();
                //     setTimeout(() => {
                //         comment_form.submit();
                //     }, 3000);
                //     return;
                // }
                // }
            } else {
                error_span.text("دسته بندی را انتخاب کنید");
            }
        } else {
            error_span.text("عنوان سوال باید حداقل 20 کاراکتر باشد");
        }
    } else {
        error_span.text("حداقل یک پاراگراف بنویسید");
    }
    clearTimeout(editorTimeoutId);
    error_span.css("display", "block");
    editorTimeoutId = setTimeout(() => {
        error_span.text("");
        error_span.hide();
    }, 5000);
}

function editorBlogStore() {
    let btn = $("#comment-editor-btn");
    let load_btn = $("#comment-editor-load-btn");
    let comment_form = $("#blogform");
    let error_span = $("#comeditor-msg");
    let editor_contents = comment_editor.getData();
    if (hasAtLeastOneParagraph(editor_contents)) {
        if ($("#title").val().length >= 20 && $("#title").val().length <= 60) {
            if (
                $("#short_description").val().length >= 30 &&
                $("#short_description").val().length <= 160
            ) {
                if (
                    $("#category_id")
                        .val()
                        .trim()
                ) {
                    if ($("#image").get(0).files.length !== 0) {
                        btn.hide();
                        load_btn.show();
                        setTimeout(() => {
                            comment_form.submit();
                        }, 3000);
                        return;
                    } else {
                        error_span.text("تصویر مقاله را انتخاب کنید");
                    }
                } else {
                    error_span.text("دسته بندی را انتخاب کنید");
                }
            } else {
                error_span.text("توضیحات باید بین 30 تا 160 کاراکتر باشد");
            }
        } else {
            error_span.text("عنوان سوال باید حداقل 20 کاراکتر باشد");
        }
    } else {
        error_span.text("حداقل یک پاراگراف بنویسید");
    }
    clearTimeout(editorTimeoutId);
    error_span.css("display", "block");
    editorTimeoutId = setTimeout(() => {
        error_span.text("");
        error_span.hide();
    }, 5000);
}

function editorBlogUpdate() {
    let btn = $("#comment-editor-btn");
    let load_btn = $("#comment-editor-load-btn");
    let comment_form = $("#blogform");
    let error_span = $("#comeditor-msg");
    let editor_contents = comment_editor.getData();
    if (hasAtLeastOneParagraph(editor_contents)) {
        if ($("#title").val().length >= 20 && $("#title").val().length <= 60) {
            if (
                $("#short_description").val().length >= 30 &&
                $("#short_description").val().length <= 160
            ) {
                btn.hide();
                load_btn.show();
                setTimeout(() => {
                    comment_form.submit();
                }, 3000);
                return;
            } else {
                error_span.text("توضیحات باید بین 30 تا 160 کاراکتر باشد");
            }
        } else {
            error_span.text("عنوان سوال باید حداقل 20 کاراکتر باشد");
        }
    } else {
        error_span.text("حداقل یک پاراگراف بنویسید");
    }
    clearTimeout(editorTimeoutId);
    error_span.css("display", "block");
    editorTimeoutId = setTimeout(() => {
        error_span.text("");
        error_span.hide();
    }, 5000);
}

function affilateStoreUpdate() {
    let btn = $("#comment-editor-btn");
    let load_btn = $("#comment-editor-load-btn");
    let comment_form = $("#cm_form");
    let error_span = $("#comeditor-msg");
    let editor_contents = comment_editor.getData();
    if (hasAtLeastOneParagraph(editor_contents)) {
        if ($("#title").val().length >= 10 && $("#title").val().length <= 100) {
            btn.hide();
            load_btn.show();
            setTimeout(() => {
                comment_form.submit();
            }, 3000);
            return;
        } else {
            error_span.text("عنوان سوال باید حداقل 10 کاراکتر باشد");
        }
    } else {
        error_span.text("حداقل یک پاراگراف بنویسید");
    }
    clearTimeout(editorTimeoutId);
    error_span.css("display", "block");
    editorTimeoutId = setTimeout(() => {
        error_span.text("");
        error_span.hide();
    }, 5000);
}

function hasAtLeastOneParagraph(content) {
    const hasParagraphTag = /<p>.*?<\/p>/i.test(content);
    return hasParagraphTag;
}

// var cedshows = document.querySelectorAll(".cedshow");
// for (var i = 0; i < cedshows.length; i++) {
//     var cedshow = cedshows[i];
//     var images = cedshow.querySelectorAll("img");
//     for (var j = 0; j < images.length; j++) {
//         var img = images[j];
//         var imgId = "ed-img-" + i + "-" + j;
//         img.setAttribute("id", imgId);
//         img.setAttribute(
//             "onclick",
//             "clickGalleryImg('" + imgId + "'," + "'comment')"
//         );
//     }
// }

$("#edImageModal").on("click", function(event) {
    if ($(event.target).attr("id") !== "ed-img") {
        closeEdModalImage();
    }
});

function clickEdImg(imgId) {
    let modal = $("#edImageModal");
    let img = $(`#${imgId}`);

    let modalImg = $("#ed-img");
    if (modal.css("display") == "none") {
        modal.css("display", "flex");
    }
    modalImg.attr("src", img.attr("src"));
    $("#close-ed-img").click(function() {
        closeEdModalImage();
    });
}

function closeEdModalImage() {
    $("#edImageModal").hide();
}
