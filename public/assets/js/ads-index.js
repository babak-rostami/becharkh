let slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n) {
    showSlides((slideIndex += n));
}

function currentSlide(n) {
    showSlides((slideIndex = n));
}

function showSlides(n) {
    let i;
    let slides = document.getElementsByClassName("ImageSlides");
    if (slides.length >= 1) {
        let dots = document.getElementsByClassName("dot");
        if (n > slides.length) {
            slideIndex = 1;
        }
        if (n < 1) {
            slideIndex = slides.length;
        }
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        for (i = 0; i < dots.length; i++) {
            dots[i].className = dots[i].className.replace(" active", "");
        }
        slides[slideIndex - 1].style.display = "block";
        dots[slideIndex - 1].className += " active";
    }
}

function clickImg(imgId) {
    open_image = imgId;
    setPrevImageId(open_image);
    setNextImageId(open_image);
    var modal = $("#imageModal");
    var img = $(`#slideImg-${imgId}`);
    var modalImg = $("#slide-img");
    if (modal.css("display") == "none") {
        modal.css("display", "flex");
    }
    modalImg.attr("src", img.attr("src"));
    var closeImage = $("#close-slide-img");
    closeImage.click(function() {
        modal.css("display", "none");
    });
}

function setPrevImageId(open_image_id) {
    if (open_image_id == 1) {
        prev_slide_image = parseInt(slide_count);
    } else {
        prev_slide_image = open_image_id - 1;
    }
}

function setNextImageId(open_image_id) {
    if (open_image_id == slide_count) {
        next_slide_image = 1;
    } else {
        next_slide_image = open_image_id + 1;
    }
}

$(document).ready(function() {
    // attached click event handler
    $("#gotoad").click(function() {
        $("html, body").animate(
            {
                scrollTop: $("#adsSection").offset().top
            },
            "slow"
        );
    });
});

// for filter category
function findCategory(cat_id) {
    for (let i = 0; i < categories.length; i++) {
        if (categories[i].id == cat_id) {
            return categories[i];
        }
    }
    return null;
    // return categories[i];
}
function changeCategoryChildren(cat_id) {
    cat_children = [];
    categories.forEach(category => {
        if (category.ha == 1 && category.p_id == cat_id) {
            cat_children.push(category);
        }
    });
}
function hasChildren(cat_id) {
    for (let i = 0; i < categories.length; i++) {
        if (categories[i].ha == 1 && categories[i].p_id == cat_id) {
            return 1;
        }
    }
    return 0;
}
function showCatChildrenModal(cat_id) {
    new_list = "";
    show_div = $("#show_categories_list_ch_modal");
    if (cat_id == 0) {
        cat_children = [];
        cat_selected = null;
        categories.forEach(cat => {
            if (cat.ha == 1 && cat.p_id == null) {
                cat_children.push(cat);
            }
        });
    } else {
        cat_selected = findCategory(cat_id);
        changeCategoryChildren(cat_id);
        if (cat_selected.p_id == null) {
            new_list =
                `<div class="col-12 filter-cat-box-desk cur-p text-right"
        onclick="showCatChildrenModal(0)">
        <img src="` +
                back_cat_img +
                `">
        <span>بازگشت</span>
        </div>`;
        } else {
            new_list =
                `<div class="col-12 filter-cat-box-desk cur-p text-right"
        onclick="showCatChildrenModal('` +
                cat_selected.p_id +
                `')">
        <img src="` +
                back_cat_img +
                `">
        <span>بازگشت</span>
        </div>`;
        }
    }
    cat_children.forEach(child => {
        if (hasChildren(child.id)) {
            new_list +=
                `<div class="col-12 filter-cat-box-desk cur-p text-right"
            onclick="showCatChildrenModal('` +
                child.id +
                `')">
            <span>` +
                child.title +
                `</span>
            <img class="float-left" src="` +
                next_cat_img +
                `">
            </div>`;
        } else {
            route_is = index_route + "/" + child.slug;

            new_list +=
                `<div class="col-12 filter-cat-box-desk text-right p-0">
                            <a rel="nofollow" class="cat-item-ch-cat-modal" href="` +
                route_is +
                `">` +
                child.title +
                `</a>
                        </div>`;
        }
    });
    if (cat_id != 0) {
        route_is = index_route + "/" + cat_selected.slug;
        t = "همه ی آگهی های ";

        new_list +=
            `<div class="col-12 filter-cat-box-desk text-right p-0">
                    <a rel="nofollow" class="cat-item-ch-cat-modal" href="` +
            route_is +
            `">` +
            `<img class="ml-2" src="` +
            all_cat_img +
            `"></img>` +
            t +
            cat_selected.title +
            `</a>
                </div>`;
    }
    show_div.html(new_list);
}
// end for filter category

// for filter item
var open_filter_box;

$(document).on("click", function(event) {
    if (
        open_filter_box != null &&
        $(event.target).closest("#filter_search_box").length &&
        !$(event.target).closest(".feature-items-filter").length
    ) {
        if ($(event.target).is($(`#f-i-f-box-${open_filter_box}`)) == false) {
            $(`#f-i-f-box-${open_filter_box}`).hide();
            $("#filter_search_box").hide();
        }
    }
});

if (typeof cfeatures !== "undefined") {
    selected_features = [];
    showfiFilter();
}

function showfiFilter() {
    let url = new URL(window.location.href);
    const params = new URLSearchParams(url.search);
    cfeatures.forEach(f => {
        if (f.p_id) {
            if (!selected_features.includes(f.p_id)) {
                return;
            }
        }

        const fitem_slug = params.get(f.slug);
        let item = citems.find(i => i.slug === fitem_slug && i.f_id === f.id);
        let filter = null;
        let filter_search = null;
        if (item) {
            selected_features.push(f.id);
            let back_page = null;
            back_page =
                window.location.origin +
                "/ads" +
                item.url
                    .replace("s=1", "")
                    .replace(`&${f.slug}=${item.slug}`, "")
                    .replace("?&", "?");
            if (back_page.endsWith("?")) {
                back_page = back_page.slice(0, -1);
            }

            filter = `<span class="cat-item-selected-bread-mobile">
                                <span onclick="showItemsFilterFeature('${f.id}')"
                                    class="f-s-f-i">${item.title}</span>
                                <a class="decor-none" href="${back_page}">
                                    <img class="x-filter-mobile" src="${x_filter_img}">
                                </a>
                            </span>`;
        } else {
            filter = `<span class="cat-item-bread-mobile" onclick="showItemsFilterFeature('${f.id}')">
                                انتخاب ${f.title}
                                <img src="${addImage}">
                            </span>`;
        }
        if (filter) {
            $("#filter_box_sm").append(filter);
        }
        filter_search = `<div class="feature-items-filter shadow" id="f-i-f-box-${f.id}">
        <img data-src="${search_filter_img}" class="lazy-load s-f-f-img">
                        <input placeholder="جستجو کنید..." class="feature-filter-input"
                            oninput="searchItemFeature('${f.id}')" id="search-fi-input-${f.id}"
                            type="text">
                        <div class="f-f-list" id="feature-items-filter-${f.id}">
                        </div>
                        </div>`;
        $("#filter_search_box").append(filter_search);
    });
}

function showItemsFilterFeature(f_id) {
    if (open_filter_box != null) {
        $(`#f-i-f-box-${open_filter_box}`).hide();
    }
    if ($(`#f-i-f-box-${f_id}`).css("display") == "none") {
        setTimeout(() => {
            $(`#search-fi-input-${f_id}`).focus();
        }, 10);
        getItemsFeature(f_id);
        $("#filter_search_box").css("display", "flex");
        $(`#f-i-f-box-${f_id}`).show();
        open_filter_box = f_id;
    } else {
        $(`#f-i-f-box-${f_id}`).hide();
        open_filter_box = null;
    }
}

$(document).ready(function() {
    var $featureFilterInput = $(".feature-filter-input");
    var $featureItemsFilter = $(".feature-items-filter");

    $featureFilterInput.on("focus", function() {
        if ($(window).width() <= 768) {
            // adjust the breakpoint as needed
            $featureItemsFilter.addClass("mobile-keyboard-open");
        }
    });

    $featureFilterInput.on("blur", function(event) {
        if (
            event.type === "blur" &&
            event.originalEvent instanceof FocusEvent &&
            event.originalEvent.relatedTarget === null
        ) {
            $featureItemsFilter.removeClass("mobile-keyboard-open");
        }
    });
});

function searchItemFeature(f_id) {
    let search = $(`#search-fi-input-${f_id}`)
        .val()
        .toLowerCase();
    let items = $(`#feature-items-filter-${f_id} .f-i-li-f`);

    items.each(function() {
        let text = $(this)
            .text()
            .toLowerCase();
        let etitle = $(this)
            .find("a")
            .data("etitle");
        if (etitle !== undefined) {
            etitle = etitle.toString().toLowerCase();
        } else {
            etitle = null;
        }
        if (
            text.indexOf(search) !== -1 ||
            (etitle != null && etitle.indexOf(search) !== -1)
        ) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

function getItemsFeature(f_id) {
    if ($(`#feature-items-filter-${f_id}`).children().length === 0) {
        let feature = cfeatures.find(f => f.id == f_id);
        let parent_feature = cfeatures.find(f => f.id == feature.p_id);

        parent_i = null;
        let s_citems = citems;
        if (parent_feature != null) {
            parent_i = getFeatureItemFromUrl(parent_feature.slug);
            s_citems = s_citems.filter(i => i.p_id === parent_i.id);
        }
        let page_url = window.location.origin + "/ads";
        for (let i = 0; i < s_citems.length; i++) {
            let item = s_citems[i];
            if (item.f_id == f_id) {
                let item_page = page_url + item.url.replace("s=1&", "");
                if (item_page.endsWith("?")) {
                    item_page = item_page.slice(0, -1);
                }

                item_span = `<span class="f-i-li-f"><a class="decor-none d-block" data-etitle="${item.e_title}" href="${item_page}">${item.title}</a></span>`;
                $(`#feature-items-filter-${f_id}`).append(item_span);
            }
        }
    }
}

function getFeatureItemFromUrl(f_sulg) {
    let queryString = window.location.search;
    let urlParams = new URLSearchParams(queryString);
    var f_item_slug = urlParams.get(`${f_sulg}`);
    let finded = citems.find(i => i.slug == f_item_slug);
    return finded;
}
// end for filter item

var typingTimerFeaItem; //timer identifier
var doneTypingCatInterval = 1000; //time in ms, 5 second for example
//on keyup, start the countdown
$(".fea-item-search-input").on("keyup", function(event) {
    if (
        event.key !== "Control" &&
        !(
            event.key === "ArrowUp" ||
            event.key === "ArrowDown" ||
            event.key === "ArrowLeft" ||
            event.key === "ArrowRight"
        )
    ) {
        clearTimeout(typingTimerFeaItem);
        typingTimerFeaItem = setTimeout(doneTypingCat, doneTypingCatInterval);
    }
});
//on keydown, clear the countdown
$(".fea-item-search-input").on("keydown", function(event) {
    if (
        event.key !== "Control" &&
        !(
            event.key === "ArrowUp" ||
            event.key === "ArrowDown" ||
            event.key === "ArrowLeft" ||
            event.key === "ArrowRight"
        )
    ) {
        $(".show-search-fea-item-result").html(
            "<p class='p-3 text-center'>در حال جستجو...</p>"
        );
        clearTimeout(typingTimerFeaItem);
    }
});
//user is "finished typing," do something
function doneTypingCat() {
    $.ajax({
        method: "get",
        url: routeSearchItem + $(".fea-item-search-input").val(),
        success: function(msg) {
            $(".show-search-fea-item-result").html(msg);
        }
    });
}
$(document).click(function(event) {
    if (!$(event.target).closest(".search-fea-item-box").length) {
        $(".search-fea-item-div").hide();
    } else {
        $(".search-fea-item-div").show();
    }
});

/* for bslider */
function createSlider(
    sliderElement,
    itemClass,
    no_scroll = 0,
    scroll_time = 4000,
    scroll_smooth = 0
) {
    let isDown = false;
    let startX;
    let scrollLeft;
    let selected_a;
    let velX = 0;
    let momentumID;
    let autoScrolling = true;
    let autoScrollingDir = true;

    sliderElement.addEventListener("mousedown", e => {
        if (e.button === 0) {
            // only trigger on left clicks
            isDown = true;
            sliderElement.classList.add("bslider-active");
            startX = e.pageX - sliderElement.offsetLeft;
            scrollLeft = sliderElement.scrollLeft;
            cancelMomentumTracking();
            let anchor = e.target.closest(`a`);
            if (anchor) {
                selected_a = anchor.id;
            }
        }
    });

    sliderElement.addEventListener("mouseup", () => {
        isDown = false;
        sliderElement.classList.remove("bslider-active");
        beginMomentumTracking();
        if (selected_a != null) {
            setTimeout(() => {
                document
                    .getElementById(selected_a)
                    .dispatchEvent(new MouseEvent("click", { bubbles: true }));
                selected_a = null;
            }, 50);
        }
    });

    sliderElement.addEventListener("click", event => {
        event.preventDefault();
    });

    sliderElement.addEventListener("mousemove", e => {
        autoScrolling = false;
        if (!isDown) return;
        selected_a = null;
        const x = e.pageX - sliderElement.offsetLeft;
        const walk = (x - startX) * 0.75;
        var prevScrollLeft = sliderElement.scrollLeft;
        sliderElement.scrollLeft = scrollLeft - walk;
        velX = sliderElement.scrollLeft - prevScrollLeft;
    });

    sliderElement.addEventListener("mouseleave", () => {
        autoScrolling = true;
        isDown = false;
        sliderElement.classList.remove("bslider-active");
    });

    sliderElement.addEventListener("wheel", e => {
        cancelMomentumTracking();
    });

    function beginMomentumTracking() {
        cancelMomentumTracking();
        momentumID = requestAnimationFrame(momentumLoop);
    }

    function cancelMomentumTracking() {
        cancelAnimationFrame(momentumID);
    }

    function momentumLoop() {
        sliderElement.scrollLeft += velX;
        velX *= 0.95;
        if (scroll_smooth) {
            momentumID = requestAnimationFrame(momentumLoop);
        } else {
            if (Math.abs(velX) > 0.5) {
                momentumID = requestAnimationFrame(momentumLoop);
            }
        }
    }

    if (!no_scroll) {
        setInterval(() => {
            if (autoScrolling) {
                if (!autoScrollingDir) {
                    if (sliderElement.scrollLeft >= 0) {
                        autoScrollingDir = true;
                    } else {
                        sliderElement.scrollLeft += 5;
                        velX = 5;
                    }
                } else {
                    if (scroll_smooth) {
                        if (
                            sliderElement.scrollLeft - 1 <=
                            -(
                                sliderElement.scrollWidth -
                                sliderElement.offsetWidth
                            )
                        ) {
                            sliderElement.scrollLeft = 0;
                        }
                        velX = -0.1;
                    } else {
                        if (
                            sliderElement.scrollLeft - 1 <=
                            -(
                                sliderElement.scrollWidth -
                                sliderElement.offsetWidth
                            )
                        ) {
                            autoScrollingDir = false;
                        } else {
                            sliderElement.scrollLeft -= 5;
                            velX = -5;
                        }
                    }
                }
                beginMomentumTracking();
            }
        }, scroll_time);
    }
}

const catSlider = document.getElementById("cat-slider");
createSlider(catSlider, "cat-slider-item");

const videoSlider = document.getElementById("video-slider");
createSlider(videoSlider, "video-slider-item");

/* end for bslider */

/* for tabs */
let tabText = $("#tab-title-box-text");
let tabTextBox = $("#tab-title-box");
let tabTextMaxWidth = 120;
tabTextBox.hide();
const stickyElement = $("#tabs");
const offsetTop = stickyElement.offset().top;
$(window).scroll(function() {
    if ($(window).scrollTop() >= offsetTop) {
        stickyElement.addClass("sticky-tab");
        tabTextBox.show();
        if (tabText.width() > tabTextMaxWidth) {
            $("#tab-title-box-text").addClass("tab-scroll-text");
            $("#top-menu-box").addClass("d-none");
        }
    } else {
        stickyElement.removeClass("sticky-tab");
        tabTextBox.hide();
        $("#top-menu-box").removeClass("d-none");
    }
});
/* end for tabs */
