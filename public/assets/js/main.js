function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(";");
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == " ") c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

// Function to set a cookie
function setCookie(name, value, expires, path) {
    var d = new Date();
    d.setTime(d.getTime() + expires * 1000);
    var expiresString = "expires=" + d.toGMTString();
    document.cookie =
        name + "=" + value + "; " + expiresString + "; path=" + path;
}

function openCloseUserDashBox(has_close = 0) {
    const menu = $("#user_dash_menu");
    const user_img = $("#bm-user-img");
    if (!has_close) {
        user_img.css("opacity", "0.3");
        user_img.animate({ opacity: "1" }, 500);
    } else {
        setTimeout(() => {
            $("#bm-user-txt").attr("class", "n-active-btab-mobile");
        }, 500);
    }
    if (menu.css("bottom") == "-375px" && !has_close) {
        setTimeout(() => {
            $("#modal-back-box").show();
            $("#bm-user-txt")
                .removeClass("n-active-btab-mobile")
                .addClass("active-btab-mobile");
        }, 500);
        openBottomMenuMore(true);
        menu.animate({ bottom: "60px" }, 500);
    } else {
        setTimeout(() => {
            $("#modal-back-box").hide();
            $("#bm-user-txt")
                .removeClass("active-btab-mobile")
                .addClass("n-active-btab-mobile");
        }, 500);
        menu.animate({ bottom: "-375px" }, 500);
    }
}

function openBottomMenuMore(has_close = false) {
    const bm_box = $(".bottom-menu-box");
    const bmBoxBottom = bm_box.css("bottom");

    if (bmBoxBottom === "-66px" && !has_close) {
        bm_box.animate({ bottom: "-5px" }, 500);
        $("#bm-more-img").attr("src", bm_more_b_img);
        $("#bm-more-txt").text("کمتر");
    } else {
        bm_box.animate({ bottom: "-66px" }, 500);
        $("#bm-more-img").attr("src", bm_more_img);
        $("#bm-more-txt").text("بیشتر");
    }
}

function openCloseNewBox(has_close = 0) {
    const menu = $("#dropdown-add-bottom-menu");
    const add_img = $("#bottom-menu-add-img");
    if (!has_close) {
        add_img.css("opacity", "0.3");
        add_img.animate({ opacity: "1" }, 500);
    }
    if (menu.css("bottom") == "-375px" && !has_close) {
        setTimeout(() => {
            $("#modal-back-box").show();
        }, 500);
        openBottomMenuMore(true);
        add_img.attr("src", close_new_img);
        menu.animate({ bottom: "60px" }, 500);
    } else {
        $("#modal-back-box").hide();
        add_img.attr("src", open_new_img);
        menu.animate({ bottom: "-375px" }, 500);
    }
}

function openSearchModal() {
    $("#search_modal").modal("show");
    setTimeout(() => {
        $("#main_search_input").focus();
    }, 500);
}

$(document).ready(function () {
    $(".c-bm-btn").click(function () {
        openCloseNewBox(1);
        openCloseUserDashBox(1);
    });

    $("#modal-back-box").click(function () {
        openCloseNewBox(1);
        openCloseUserDashBox(1);
    });
    // $("#bm_open_dashbaord").click(function() {
    //     openCloseUserDashBox();
    //     openCloseNewBox(1);
    // });
    $("#dropdown-add-bottom").click(function () {
        openCloseUserDashBox(1);
        openCloseNewBox();
    });

    $(document).on("click", function (event) {
        if ($(event.target).closest("#myOverlay").length) {
            w3_close();
        }
    });
});

function gtudash() {
    if (is_user_login) {
        window.location.href = window.location.origin + "/dashboard";
    } else {
        $("#login_user").modal("show");
        openCloseUserDashBox();
        openCloseNewBox(1);
    }
}

function w3_open() {
    $("#myOverlay").css("display", "block");
    $("#open_menu_icon").css("display", "none");
    $("#close_menu_icon").css("display", "inline-block");
    document.getElementById("mySidebar").classList.toggle("visible");
}

function w3_close() {
    $("#myOverlay").css("display", "none");
    $("#close_menu_icon").css("display", "none");
    $("#open_menu_icon").css("display", "inline-block");
    document.getElementById("mySidebar").classList.toggle("visible");
}

//for main search

var typingTimer; // timer identifier
var doneTypingInterval = 1500; // time in ms, 1 second for example

// on keyup, start the countdown
$("#main_search_input").on("keyup", function () {
    clearTimeout(typingTimer);

    typingTimer = setTimeout(() => {
        main_search_items = -1;
        main_search_categories = -1;
        main_search_questions = -1;
        main_search_blogs = -1;
        searchForInput();
    }, doneTypingInterval);
});

// on keydown, clear the countdown
$("#main_search_input").on("keydown", function (event) {
    if (event.key !== "Control" && !event.key.startsWith("Arrow")) {
        clearTimeout(typingTimer);
    }
});

//1 is items and categories
//2 is questions
//3 is blogs
let main_search_for = 1;
let main_search_input;
let main_search_items = -1;
let main_search_categories = -1;
let main_search_questions = -1;
let main_search_blogs = -1;
function searchForInput() {
    main_search_input = $("#main_search_input").val();

    if (main_search_input?.length <= 1) {
        $("#show-msearch-empty").show();
        $("#show-msearch-result").hide();
        return;
    }
    $("#show-msearch-result").hide();
    $("#show-msearch-empty").hide();
    $("#show-msearch-loading").show();

    $.ajax({
        method: "get",
        url: "/main-search/" + main_search_for + "/" + main_search_input,
        success: function (data) {
            $("#show-msearch-loading").hide();
            $("#show-msearch-result").show();
            if (main_search_for == 1) {
                main_search_items = data.items;
                main_search_categories = data.categories;
                showMainSearchItemsResults();
            } else if (main_search_for == 2) {
                main_search_questions = data.questions;
                showMainSearchForumResults();
            } else if (main_search_for == 3) {
                main_search_blogs = data.blogs;
                showMainSearchBlogResults();
            }
        },
        error: function (jqXHR) {
            $("#show-msearch-empty").show();
            $("#show-msearch-loading").hide();
        }
    });
}

function showMainSearchBlogResults() {
    $(".msearch-tab").removeClass("msearch-tab-active");
    if (!$("#msearch-tab-blogs").hasClass("msearch-tab-active")) {
        $("#msearch-tab-blogs").addClass("msearch-tab-active");
        main_search_for = 3;
        let result_box;
        result_box = $("#show-msearch-result");
        result_box.empty();
        if (main_search_blogs == -1) {
            searchForInput();
        } else {
            if (main_search_blogs.length > 0) {
                main_search_blogs.forEach(blog => {
                    result_box.append(
                        `<a class="msearch-result-blog" href="${blog.url}">
                <img src="${blog.img}">
                <span>${blog.title}</span>
                </a>`
                    );
                });
            } else {
                result_box.append(`<div id="msearch-result-404">
             نتیجه ای پیدا نشد! </div>`);
            }
        }
    }
}

function showMainSearchForumResults() {
    $(".msearch-tab").removeClass("msearch-tab-active");
    if (!$("#msearch-tab-questions").hasClass("msearch-tab-active")) {
        $("#msearch-tab-questions").addClass("msearch-tab-active");
        main_search_for = 2;
        let result_box;
        result_box = $("#show-msearch-result");
        result_box.empty();
        if (main_search_questions == -1) {
            searchForInput();
        } else {
            if (main_search_questions.length > 0) {
                main_search_questions.forEach(question => {
                    result_box.append(
                        `<a class="msearch-result-question" href="${question.url}">
                ${question.title}</a>`
                    );
                });
            } else {
                result_box.append(`<div id="msearch-result-404">
             نتیجه ای پیدا نشد! </div>`);
            }
        }
    }
}

function showMainSearchItemsResults() {
    $(".msearch-tab").removeClass("msearch-tab-active");
    if (!$("#msearch-tab-items").hasClass("msearch-tab-active")) {
        $("#msearch-tab-items").addClass("msearch-tab-active");
        main_search_for = 1;
        let result_box;
        result_box = $("#show-msearch-result");
        result_box.empty();
        if (main_search_categories == -1 || main_search_items == -1) {
            searchForInput();
        } else {
            if (main_search_categories.length > 0) {
                let cat_items = "";
                main_search_categories.forEach(cat => {
                    cat_items += `<div class="msearch-result-cat">
                <a class="msearch-result-cat-a" href="${cat.url}">
                <img class="msearch-result-cat-img" src="${cat.img}">
                <span>${cat.title}</span> 
                </a>
            </div>
            `;
                });
                result_box.append(
                    `<span id="msearch-result-h">دسته بندی ها</span>
            <div id="msearch-result-cats">${cat_items}</div>`
                );
            }
            if (main_search_items.length > 0) {
                main_search_items.forEach(item => {
                    let linksHtml = "";
                    if (item.a_url !== null) {
                        linksHtml += `<a class="msearch-result-item-link" href="${item.a_url}">آگهی ها</a>`;
                    }
                    if (item.c_url !== null) {
                        linksHtml += `<a class="msearch-result-item-link" href="${item.c_url}">نظرات کاربران</a>`;
                    }
                    if (item.f_url !== null) {
                        linksHtml += `<a class="msearch-result-item-link" href="${item.f_url}">سوال ها</a>`;
                    }
                    result_box.append(`
                            <div class="msearch-result-item" onclick="openSearchItemLinks('${item.id}')">
                                <img class="msearch-result-item-img" src="${item.img}">
                                <span class="msearch-result-item-title">${item.title}</span> 
                                <span class="msearch-result-item-cat">${item.cat}</span>
                            </div>
                            <div class="msearch-result-item-links" id="msearch-result-item-links-${item.id}">${linksHtml}</div>
                    `);
                });
            }
            if (
                main_search_items.length == 0 &&
                main_search_categories.length == 0
            ) {
                result_box.append(`<div id="msearch-result-404">
             نتیجه ای پیدا نشد! </div>`);
            }
        }
    }
}

function openSearchItemLinks(item_id) {
    let result_box = $(`#msearch-result-item-links-${item_id}`);
    if (result_box.css("display") === "none") {
        result_box.show();
        setTimeout(() => {
            result_box.hide();
        }, 15000);
    }
}

//end for main search

if ($("#username_for_register").length > 0) {
    $("#username_for_register")
        .on("input", function () {
            var input = this.value;
            input = input.replace(/[^a-zA-Z0-9_.]/g, "").toLowerCase();
            if (/^\d/.test(input)) {
                input = input.replace(/^\d+/, "");
            }
            this.value = input;
        })
        .on("paste", function (event) {
            event.preventDefault();
        })
        .on("blur", function () {
            var input = this.value;
            input = input.replace(/[^a-zA-Z0-9_.]/g, "").toLowerCase();
            if (/^\d/.test(input)) {
                input = input.replace(/^\d+/, "");
            }
            this.value = input;
        })
        .attr("autocomplete", "off"); // غیرفعال کردن autocomplete
}

// if ($("#username_for_register").length > 0) {
//     $("#username_for_register")
//         .on("input", function () {
//             var input = this.value;
//             if (!validateInput(input)) {
//                 this.value = input.substring(0, input.length - 1);
//             }
//         })
//         .on("paste", function (event) {
//             event.preventDefault();
//         })
//         .attr("autocomplete", "off");
// }

function validateInput(input) {
    var regex = /^[a-z][a-z0-9_.]*$/;
    return regex.test(input);
}

function clearSuggestion() {
    if (suggest_user_email != null) {
        suggest_user_email = null;
        $("#click_for_login").show();
        $("#click_for_login_is_true").hide();
        $("#click_for_login_fix_mistake").hide();
    }
}
let popularDomains = ["gmail.com", "yahoo.com", "hotmail.com", "outlook.com"];
let suggest_user_email = null;
function sendLoginRequest(status = null) {
    $("#click_for_login").hide();
    $("#check-email-loading").show();
    if (status == 1) {
        $("#email_for_login_check").val(suggest_user_email);
        user_email_for_auth = suggest_user_email;
    } else {
        user_email_for_auth = $("#email_for_login_check").val();
    }
    var email = user_email_for_auth;
    var re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    var is_email2 = true;

    var emailParts = email.split("@");
    if (emailParts.length == 1) {
        is_email2 = false;
    } else {
        var domainParts = emailParts[1].split(".");
        if (domainParts.length > 2) {
            is_email2 = false;
        } else {
            if (status != 0 && status != 1) {
                let email_domain = emailParts[1];
                // Check if the email domain is not in the popular domains list
                if (!popularDomains.includes(email_domain)) {
                    // Suggest corrections using the Levenshtein distance algorithm
                    let suggestedDomain = suggestCorrection(
                        email_domain,
                        popularDomains
                    );
                    $("#check-email-loading").hide();
                    $("#email_for_login_error").show();
                    $("#click_for_login").hide();
                    $("#click_for_login_is_true").show();
                    $("#click_for_login_fix_mistake").show();
                    suggest_user_email = `${emailParts[0]}@${suggestedDomain}`;
                    $("#email_for_login_error").text(
                        `منظورتان ${suggest_user_email} است?`
                    );
                    return;
                }
            }
        }
    }
    var is_email = re.test(email);
    if (is_email && is_email2) {
        $("#email_for_login_error").hide();
        $.ajax({
            url: route_is_email_exist,
            type: "POST",
            data: {
                email: email,
                _token: csrf_token
            },
            success: function (data) {
                if (data.status == 0) {
                    //go to register
                    $("#checkEmailLoginModal").hide();
                    $("#registerFormLoginModal").show();
                    $("#email-register-span").text(user_email_for_auth);
                    $("#email_for_register").val(user_email_for_auth);
                    $("#password_for_register").val("");
                    $("#name_for_register").val("");
                    $("#username_for_register").val("");
                } else {
                    //go to login
                    $("#checkEmailLoginModal").hide();
                    $("#loginFormLoginModal").show();
                    $("#show-name-lf").text(data.name);
                    $("#email_for_login").val(user_email_for_auth);
                    $("#password_for_login").val("");
                }
                $("#click_for_login").show();
                $("#check-email-loading").hide();
            }
        });
    } else {
        $("#click_for_login").show();
        $("#check-email-loading").hide();
        $("#email_for_login_error").show();
        $("#email_for_login_error").text("یک آدرس ایمیل معتبر وارد کنید");
        setTimeout(() => {
            $("#email_for_login_error").hide();
        }, 4000);
    }
}

// Levenshtein distance algorithm function
function suggestCorrection(input, domains) {
    let minDistance = Infinity;
    let suggestedDomain = "";

    for (let domain of domains) {
        let distance = levenshteinDistance(input, domain);
        if (distance < minDistance) {
            minDistance = distance;
            suggestedDomain = domain;
        }
    }

    return suggestedDomain;
}
// Levenshtein distance algorithm implementation
function levenshteinDistance(a, b) {
    let m = a.length;
    let n = b.length;
    let d = new Array(m + 1);

    for (let i = 0; i <= m; i++) {
        d[i] = new Array(n + 1);
        d[i][0] = i;
    }

    for (let j = 0; j <= n; j++) {
        d[0][j] = j;
    }

    for (let i = 1; i <= m; i++) {
        for (let j = 1; j <= n; j++) {
            let cost = a[i - 1] === b[j - 1] ? 0 : 1;
            d[i][j] = Math.min(
                d[i - 1][j] + 1,
                d[i][j - 1] + 1,
                d[i - 1][j - 1] + cost
            );
        }
    }

    return d[m][n];
}

function loginUser() {
    user_email = $("#email_for_login").val();
    user_password = $("#password_for_login").val();

    if (user_password.trim() == "") {
        $("#login-error-box").show();
        $("#login-error-message").show();
        $("#login-error-message").text("رمز عبور خود را وارد کنید");
        return;
    }

    $("#login-submit-btn").hide();
    $("#login-submit-loading").show();
    $.ajax({
        url: route_login_user_ajax,
        type: "POST",
        data: {
            email: user_email,
            password: user_password,
            _token: csrf_token
        },
        success: function (data) {
            if (data.status == 1) {
                $("#login-error-box").hide();
                $("#login-suc-message").text("خوش آمدید");
                $("#login-suc-message").show();
                if (typeof page !== "undefined") {
                    if (
                        page == "comment" ||
                        page == "show_blog" ||
                        page == "show_question" ||
                        page == "create_question" ||
                        page == "create_advertise" ||
                        page == "show_product"
                    ) {
                        doThisAfterAuth();
                    } else {
                        location.reload();
                    }
                } else {
                    location.reload();
                }
            }
        },
        error: function (jqXHR) {
            $("#login-error-box").show();
            $("#login-error-message").show();
            $("#login-error-message").text(jqXHR.responseJSON.message);
            $("#login-submit-btn").show();
            $("#login-submit-loading").hide();
        }
    });
}

function registerUser() {
    user_email = $("#email_for_register").val();
    user_password = $("#password_for_register").val();
    user_name = $("#name_for_register").val();
    user_username = $("#username_for_register").val();

    if (
        user_password.trim() == "" ||
        user_name.trim() == "" ||
        user_username.trim() == ""
    ) {
        $("#register-error-box").show();
        $("#register-error-message").show();
        $("#register-error-message").text("همه موارد را تکمیل کنید");
        return;
    }

    $("#register-submit-btn").hide();
    $("#register-submit-loading").show();
    $.ajax({
        url: route_register_user_ajax,
        type: "POST",
        data: {
            email: user_email,
            password: user_password,
            name: user_name,
            username: user_username,
            _token: csrf_token
        },
        success: function (data) {
            if (data.success == 1) {
                $("#register-error-box").hide();
                $("#register-suc-message").text("خوش آمدید");
                $("#register-suc-message").show();
                if (typeof page !== "undefined") {
                    if (
                        page == "comment" ||
                        page == "show_blog" ||
                        page == "show_question" ||
                        page == "create_question" ||
                        page == "create_advertise" ||
                        page == "show_product"
                    ) {
                        doThisAfterAuth();
                    }
                } else {
                    location.reload();
                }
            }
        },
        error: function (jqXHR) {
            $("#register-error-box").show();
            $("#register-error-message").show();
            $("#register-error-message").text(jqXHR.responseJSON.message);
            $("#register-submit-btn").show();
            $("#register-submit-loading").hide();
        }
    });
}

function setEmailForForgetPass() {
    $("#email_forget_pass").val(user_email_for_auth);
}

function changeTypePasswordRegister() {
    if ($("#password_for_register").attr("type") === "text") {
        $("#password_for_register").prop("type", "password");
    } else {
        $("#password_for_register").prop("type", "text");
    }
}
function changeTypePasswordLogin() {
    if ($("#password_for_login").attr("type") === "text") {
        $("#password_for_login").prop("type", "password");
    } else {
        $("#password_for_login").prop("type", "text");
    }
}

function showEnterEmailPageForAuth() {
    $("#registerFormLoginModal").hide();
    $("#loginFormLoginModal").hide();
    $("#click_for_login_fix_mistake").hide();
    $("#click_for_login_is_true").hide();
    $("#checkEmailLoginModal").show();
}

//end register validate

function limitMaxChar(input, max = null) {
    var currentLength = $(input).val().length;
    var maxLength = max;

    if (maxLength != null) {
        var remainingCharMax = maxLength - currentLength;
        if (remainingCharMax < 0) {
            remainingCharMax = 0;
        }
        if (currentLength > maxLength) {
            $(input).val(
                $(input)
                    .val()
                    .substring(0, maxLength)
            ); // Truncate the input value to the maximum limit
        }
    }
}

function resetPassword(status = null) {
    $("#click_for_reset_password").prop("disabled", true);
    $("#click_for_reset_password").text("منتظر بمانید...");

    let email;
    if (status) {
        $("#reset_password_btn").show();
        $("#reset_password_suggestion").hide();
        if (status == 1) {
            email = suggest_user_email;
            $("#email_forget_pass").val(suggest_user_email);
        }
    } else {
        email = $("#email_forget_pass").val();
    }

    let re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    let is_email2 = true;

    let emailParts = email.split("@");
    let domainParts = emailParts[1].split(".");
    if (domainParts.length > 2) {
        is_email2 = false;
    }
    let is_email = re.test(email);
    if (is_email && is_email2) {
        if (status != 0 && status != 1) {
            let email_domain = emailParts[1];
            // Check if the email domain is not in the popular domains list
            if (!popularDomains.includes(email_domain)) {
                // Suggest corrections using the Levenshtein distance algorithm
                let suggestedDomain = suggestCorrection(
                    email_domain,
                    popularDomains
                );
                $("#click_for_reset_password").prop("disabled", false);
                $("#click_for_reset_password").text("تایید");
                $("#error_for_email_forget").show();
                $("#reset_password_btn").hide();
                $("#reset_password_suggestion").show();
                suggest_user_email = `${emailParts[0]}@${suggestedDomain}`;
                $("#error_for_email_forget").text(
                    `منظورتان ${suggest_user_email} است?`
                );
                return;
            }
        }

        $.ajax({
            type: "POST",
            url: forget_password_route,
            data: {
                _token: csrf_token,
                email: email
            },
            success: function (data) {
                $("#error_for_email_forget").show();
                $("#error_for_email_forget").text(data.message);
                $("#click_for_reset_password").prop("disabled", false);
                $("#click_for_reset_password").text("تایید");
            },
            error: function (error) {
                $("#error_for_email_forget").show();
                $("#error_for_email_forget").text(error.responseJSON.error);
                setTimeout(() => {
                    $("#error_for_email_forget").hide();
                }, 5000);
                $("#click_for_reset_password").prop("disabled", false);
                $("#click_for_reset_password").text("تایید");
            }
        });
    } else {
        $("#error_for_email_forget").show();
        $("#error_for_email_forget").text("یک آدرس ایمیل معتبر وارد کنید");
        setTimeout(() => {
            $("#error_for_email_forget").hide();
        }, 5000);
        $("#click_for_reset_password").prop("disabled", false);
        $("#click_for_reset_password").text("تایید");
    }
}

//for lazy loading
document.addEventListener("DOMContentLoaded", function () {
    const lazyImages = [].slice.call(
        document.querySelectorAll("img.lazy-load")
    );
    if ("IntersectionObserver" in window) {
        let lazyImageObserver = new IntersectionObserver(function (
            entries,
            observer
        ) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    let lazyImage = entry.target;
                    lazyImage.src = lazyImage.dataset.src;
                    lazyImage.classList.remove("lazy-load");
                    lazyImageObserver.unobserve(lazyImage);
                }
            });
        });

        lazyImages.forEach(function (lazyImage) {
            lazyImageObserver.observe(lazyImage);
        });
    } else {
        // Possibly fall back to a more compatible method here
    }
});
//end for lazy loading

/* for user dash model */
// var open_dash = 0;
// var checkdash = 0;
// function openUserDashModal(userid, itemid) {
//     if (open_dash != 0) {
//         return;
//     }
//     const modalBox = $(`#udm-box-${userid}-${itemid}`);
//     open_dash = `#udm-box-${userid}-${itemid}`;
//     checkdash = 0;
//     modalBox.show();
//     setTimeout(() => {
//         checkdash = 1;
//     }, 100);
// }
// $(document).click(function(event) {
//     if (open_dash != 0 && checkdash == 1) {
//         if (!$(event.target).closest(`${open_dash}`).length) {
//             $(open_dash).hide();
//             open_dash = 0;
//         }
//     }
// });

/* end for user dash model */

function formatMoney(amount) {
    return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

$(".convert-m").each(function () {
    var amount = $(this).text();
    $(this).text(formatMoney(amount));
});

function convertToMoneyFormat(input) {
    var maxValueLength = 8;
    var currentValue = input.value.replace(/\D/g, ""); // remove non-digit characters
    if (currentValue.length > maxValueLength) {
        input.value = currentValue
            .substring(0, maxValueLength)
            .replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    } else {
        input.value = currentValue.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
}

function submitChacForm() {
    const amount = parseInt(
        $("#chacinp")
            .val()
            .replace(/,/g, "")
    );
    if (amount < 10000) {
        $("#chacinp").val("10,000");
        return false;
    } else {
        return true;
    }
}

function jsurl(new_url, blank = 0) {
    if (blank) {
        window.open(new_url, "_blank");
    } else {
        window.location.href = new_url;
    }
}

$(document).ready(function () {
    $('.modal').on('show.bs.modal', function () {
        let modalDialog = $(this).find('.modal-dialog');
        if ($(window).width() < 768) {
            modalDialog.removeClass('modal-dialog-centered');
            modalDialog.css('margin-top', '64px');
        } else {
            modalDialog.addClass('modal-dialog-centered');
            modalDialog.css('margin-top', '');
        }
    });

    $(window).resize(function () {
        $('.modal:visible').each(function () {
            let modalDialog = $(this).find('.modal-dialog');
            if ($(window).width() < 768) {
                modalDialog.removeClass('modal-dialog-centered');
                modalDialog.css('margin-top', '64px');
            } else {
                modalDialog.addClass('modal-dialog-centered');
                modalDialog.css('margin-top', '');
            }
        });
    });
});

let mottos = [
    "بپرس",
    "یاد بگیر",
    "یاد بده",
    "حرفه‌ای شو",
    "سوال کن",
    "رشد کن",
    "همراه شو",
    "الهام بگیر",
    "الهام بده",
    "نقد کن",
    "بررسی کن",
    "کمک کن",
    "فکر کن",
    "بحث کن",
    "تحلیل کن",
    "جستجو کن",
    "مقایسه کن",
    "نظر بده",
];
let motto_count = 0;

function changeMotto() {
    $("#motto2").text(mottos[motto_count]);
    $("#motto2").removeClass('fadeMottoCls');
    void $("#motto2")[0].offsetWidth;
    $("#motto2").addClass('fadeMottoCls');

    motto_count++;
    if (motto_count >= mottos.length) {
        motto_count = 0;
    }
}

setInterval(changeMotto, 2500);

const ftp_path = "https://dl.becharkh.com/user_files/";