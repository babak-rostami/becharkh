const next_cat_img = ftp_path + "/files/other/images/next.png";
const back_cat_img = ftp_path + "/files/other/images/back.png";
const all_cat_img = ftp_path + "/files/other/images/all-cat.webp";

function findCategory(cat_id) {
    for (let i = 0; i < categories.length; i++) {
        if (categories[i].id == cat_id) {
            return categories[i];
        }
    }
    return categories[i];
}
function changeCategoryChildren(cat_id) {
    cat_children = [];
    categories.forEach(category => {
        if (category.p_id == cat_id) {
            cat_children.push(category);
        }
    });
}
function hasChildren(cat_id) {
    for (let i = 0; i < categories.length; i++) {
        if (categories[i].p_id == cat_id) {
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
            if (cat.p_id == null) {
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
            if (
                page == "forum" ||
                page == "advertise" ||
                page == "blog-index"
            ) {
                route_is = index_route + "/" + child.slug;
            } else {
                route_is = index_route + "/" + child.slug + "?s=1";
            }
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
        if (page == "forum") {
            route_is = index_route + "/" + cat_selected.slug;
            t = "همه ی بحث های ";
        } else if (page == "comment") {
            route_is = index_route + "/" + cat_selected.slug + "?s=1";
            t = "همه ی نظرات ";
        } else if (page == "advertise") {
            route_is = index_route + "/" + cat_selected.slug;
            t = "همه ی آگهی های ";
        } else if (page == "blog-index") {
            route_is = index_route + "/" + cat_selected.slug;
            t = "همه ی پست های ";
        }
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
