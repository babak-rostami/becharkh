var open_filter_box;
const x_filter_img = ftp_path + "files/other/images/w-close.webp";
const search_filter_img = ftp_path + "files/other/images/search-gray.png";
const addImage = ftp_path + "files/other/images/b-add.png";

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
            if (page == "comment") {
                back_page =
                    window.location.origin +
                    "/forum" +
                    item.url.replace(`&${f.slug}=${item.slug}`, "");
            } else if (page == "forum") {
                back_page =
                    window.location.origin +
                    "/forum" +
                    item.url
                        .replace("s=1", "")
                        .replace(`&${f.slug}=${item.slug}`, "")
                        .replace("?&", "?");
                if (back_page.endsWith("?")) {
                    back_page = back_page.slice(0, -1);
                }
            } else if (page == "advertise") {
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
            } else if (page == "blog-index") {
                back_page =
                    window.location.origin +
                    "/blogs" +
                    item.url
                        .replace("s=1", "")
                        .replace(`&${f.slug}=${item.slug}`, "")
                        .replace("?&", "?");
                if (back_page.endsWith("?")) {
                    back_page = back_page.slice(0, -1);
                }
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
        let page_url;
        if (page == "blog-index") {
            page_url = window.location.origin + "/blogs";
        } else if (page == "advertise") {
            page_url = window.location.origin + "/ads";
        } else if (page == "forum" || page == "comment") {
            page_url = window.location.origin + "/forum";
        }
        for (let i = 0; i < s_citems.length; i++) {
            let item = s_citems[i];
            if (item.f_id == f_id) {
                let item_page;
                if (page == "comment") {
                    item_page = page_url + item.url;
                } else if (
                    page == "forum" ||
                    page == "advertise" ||
                    page == "blog-index"
                ) {
                    item_page = page_url + item.url.replace("s=1&", "");
                    if (item_page.endsWith("?")) {
                        item_page = item_page.slice(0, -1);
                    }
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
