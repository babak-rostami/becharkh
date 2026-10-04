const fifil_modal = $("#fifilter-modal");
const fifil_ibox_loading = $("#fifilter-ibox-loading");
const fifilter_ibox_ftitle = $("#fifilter-ibox-ftitle");
const fifilter_ibox_items = $("#fifilter-ibox-items");
const fifilter_ibox_back = $("#fifilter-ibox-back");
const fifilter_ibox_click = $("#fifilter-ibox-click");

let fifil_search_title;
let fifil_items = [];
let fifil_loaded_features = [];
let back_item_id;
let back_feature_id;

function fifilClickFeature(f_id, f_title) {
    fifilter_ibox_items.empty();
    back_item_id = null;
    back_feature_id = f_id;
    fifil_modal.modal("show");
    fifil_ibox_loading.show();
    fifilter_ibox_ftitle.text("انتخاب " + f_title);
    if (fifil_loaded_features.includes(f_id)) {
        let existingItems = fifil_items.filter(
            item => item.feature_id === f_id
        );
        displayItems(existingItems);
    } else {
        $.ajax({
            url: fifil_load_items_route,
            type: "GET",
            data: {
                f_id: f_id
            },
            success: function(data) {
                fifil_items = fifil_items.concat(data.items);
                fifil_loaded_features.push(f_id);
                let filteredItems = data.items.filter(
                    item =>
                        item.parent_id === undefined || item.parent_id === null
                );
                displayItems(filteredItems);
            },
            error: function(jqXHR) {
                fifil_ibox_loading.hide();
            }
        });
    }
}

function displayItems(items) {
    fifilter_ibox_items.empty();
    fifil_ibox_loading.hide();
    if (items.length > 0) {
        items.forEach(function(item) {
            let itemElement = $("<span>")
                .addClass("fifilter-ibox-item")
                .text(item.title)
                .attr("data-en", item.title_en)
                .on("click", function() {
                    fifilClickItem(item._id);
                });
            fifilter_ibox_items.append(itemElement);
        });
    } else {
        fifilter_ibox_items.append("<span>موردی پیدا نشد.</span>");
    }
}

function fifilBack() {
    fifil_ibox_loading.show();
    if (back_item_id) {
        fifilClickItem(back_item_id);
    } else {
        let feature = features.find(function(f) {
            return f.id === back_feature_id;
        });
        fifilClickFeature(back_feature_id, feature.title);
    }
}

function fifilClickItem(item_id, forceClick = 0) {
    let childrenItems = fifil_items.filter(item => item.parent_id == item_id);
    fifilter_ibox_items.empty();
    fifil_ibox_loading.show();
    fifilter_ibox_back.show();

    if (childrenItems.length > 0 && forceClick == 0) {
        let feature = features.find(function(f) {
            return f.id === childrenItems[0].feature_id;
        });
        let item = fifil_items.find(function(i) {
            return i._id === item_id;
        });
        back_item_id = item.parent_id !== undefined ? item.parent_id : null;
        if (feature) {
            fifilter_ibox_ftitle.text(feature.title);
        }

        let allitemElement = $("<span>")
            .addClass("fifilter-ibox-item")
            .text("همه " + feature.title + " ها")
            .attr("data-en", item.title_en)
            .on("click", function() {
                fifilClickItem(item._id, 1);
            });
        fifilter_ibox_items.append(allitemElement);
        childrenItems.forEach(function(chitem) {
            let itemElement = $("<span>")
                .addClass("fifilter-ibox-item")
                .text(chitem.title)
                .attr("data-en", chitem.title_en)
                .on("click", function() {
                    fifilClickItem(chitem._id);
                });
            fifilter_ibox_items.append(itemElement);
        });
        fifil_ibox_loading.hide();
    } else {
        let item = fifil_items.find(function(i) {
            return i._id === item_id;
        });

        if (item && item.with_parent_url) {
            fifilter_ibox_click.show();
            fifilter_ibox_click.text("در حال بارگیری");
            fifilter_ibox_ftitle.hide();
            fifilter_ibox_back.hide();
            $("#fifilter-ibox-search").hide();
            let currentUrl = new URL(window.location.href);
            let currentPath = currentUrl.pathname;
            let newQueryParams = new URLSearchParams(
                item.with_parent_url.split("?")[1]
            );
            if (
                page === "advertise" ||
                page === "forum" ||
                page === "blog-index"
            ) {
                newQueryParams.delete("s");
            }
            let newUrl = `${
                currentUrl.origin
            }${currentPath}?${newQueryParams.toString()}`.toLowerCase();
            window.location.href = newUrl;
        } else {
            console.warn("No item found or with_parent_url is missing.");
        }
    }
}

function fifilterSearch() {
    clearTimeout(fifil_search_title);
    fifil_search_title = setTimeout(function() {
        let searchValue = $("#fifilter-ibox-search").val();
        let items = $(".fifilter-ibox-item");
        let found = false;
        items.each(function() {
            let itemText = $(this).text();
            let itemDataEn = $(this).attr("data-en").toLowerCase();
            if (
                itemText.includes(searchValue) ||
                itemDataEn.includes(searchValue)
            ) {
                $(this).show();
                found = true;
            } else {
                $(this).hide();
            }
        });
        if (!found) {
            if ($("#fifil-noitems").length === 0) {
                fifilter_ibox_items.append(
                    "<div id='fifil-noitems'>نتیجه ای پیدا نشد!</div>"
                );
            }
        } else {
            $("#fifil-noitems").remove();
        }
    }, 500);
}

//for show item in filter if selected
let parentItems = selected_items.filter(item => item.p_id === null);
let queryString = window.location.search;
let urlParams = new URLSearchParams(queryString);
parentItems.forEach(pi => {
    let pif = features.find(f => f.id == pi.f_id);
    $(`#fifil-ftit-${pif.slug}`).text(itemSelectedChild(pi));
});
function itemSelectedChild(item) {
    let title = item.title;
    let child = selected_items.find(pi => pi.p_id === item.id);
    if (child) {
        return title + " " + itemSelectedChild(child);
    } else {
        return title;
    }
}
//end for show item in filter if selected
