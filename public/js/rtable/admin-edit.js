const form = document.getElementById(submit_form_id);
form.addEventListener("keydown", function (event) {
    if (event.key === "Enter") {
        event.preventDefault();
    }
});

function countCharacters(input, min = null, max = null) {
    var currentLength = $(input).val().length;
    var maxLength = max;
    var checkMax = 0;
    var minLength = min;
    var charMinSpanId = "#charCountMin-" + input.id;
    var CharMinSpan = $(charMinSpanId);
    var charMaxSpanId = "#charCountMax-" + input.id;
    var CharMaxSpan = $(charMaxSpanId);

    $(".error").hide();
    if (input.id == "title") {
        $("#title-error").hide();
    }

    if (minLength != null) {
        var remainingCharMin = minLength - currentLength;
        if (remainingCharMin < 0) {
            remainingCharMin = 0;
        }
        if (currentLength >= minLength) {
            CharMinSpan.hide();
            checkMax = 1;
        } else {
            checkMax = 0;
            CharMinSpan.show();
            CharMaxSpan.hide();
            CharMinSpan.text(
                "حداقل " + remainingCharMin + " کاراکتر دیگر وارد کنید"
            );
        }
    }
    if (maxLength != null && checkMax == 1) {
        CharMaxSpan.show();
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
        CharMaxSpan.text("تعداد کاراکتر باقی مانده : " + remainingCharMax);
    }
}

function changeChildrens(item, fea_id) {
    var item_id = item.value;
    axios.get("/feature-children-item/" + fea_id + "/" + item_id).then(
        response => (
            $.each(response.data.all_child, function (i, value) {
                var id = "#" + value.slug;
                $(id).empty();
                $(id).append(
                    "<option value=" + null + ">انتخاب نشده است</option>"
                );
            }),
            $.each(response.data.child_items, function (i, value) {
                $(response.data.child_slug).append(
                    "<option value=" +
                    value.id +
                    ">" +
                    value.title +
                    "</option>"
                );
            })
        )
    );
}

// for select category for cu modal
function findCategoryForSCFCE(cat_id) {
    for (let i = 0; i < categories.length; i++) {
        if (categories[i].id == cat_id) {
            return categories[i];
        }
    }
    return categories[i];
}
function changeCategoryChildrenForSCFCE(cat_id) {
    cat_children = [];
    categories.forEach(category => {
        if (category.p_id == cat_id) {
            cat_children.push(category);
        }
    });
}
function hasChildrenForSCFCE(cat_id) {
    for (let i = 0; i < categories.length; i++) {
        if (categories[i].p_id == cat_id) {
            return 1;
        }
    }
    return 0;
}

function showCatChildrenModalForSCFCE(cat_id) {
    new_list = "";
    show_div = $("#show_categories_list_ch_modal");
    let modal_ce_cat_selected = null;
    if (cat_id == 0) {
        cat_children = [];
        categories.forEach(cat => {
            if (cat.p_id == null) {
                cat_children.push(cat);
            }
        });
    } else {
        modal_ce_cat_selected = findCategoryForSCFCE(cat_id);
        changeCategoryChildrenForSCFCE(cat_id);
        if (modal_ce_cat_selected.p_id == null) {
            new_list =
                `<div class="col-12 filter-cat-return-box-desk cur-p text-right"
        onclick="showCatChildrenModalForSCFCE(0)">
        <img src="` +
                back_cat_img +
                `">
        <span>بازگشت</span>
        </div>`;
        } else {
            new_list =
                `<div class="col-12 filter-cat-return-box-desk cur-p text-right"
        onclick="showCatChildrenModalForSCFCE('` +
                modal_ce_cat_selected.p_id +
                `')">
        <img src="` +
                back_cat_img +
                `">
        <span>بازگشت</span>
        </div>`;
        }
    }
    cat_children.forEach(child => {
        if (hasChildrenForSCFCE(child.id)) {
            new_list +=
                `<div class="col-12 filter-cat-box-desk cur-p text-right"
            onclick="showCatChildrenModalForSCFCE('` +
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
            new_list +=
                `<div class="col-12 filter-cat-box-desk text-right p-0" data-dismiss="modal" onclick="selectCatItemForSCFCE('${child.id}')">
                            <div class="cat-item-ch-cat-modal cur-p">` +
                child.title +
                `</div>
                        </div>`;
        }
    });
    if (cat_id != 0) {
        t = "مطالب مرتبط با ";
        new_list +=
            `<div class="col-12 filter-cat-all-box-desk text-right p-0" data-dismiss="modal" onclick="selectCatItemForSCFCE('${modal_ce_cat_selected.id}')">
                    <div class="cat-item-ch-cat-modal cur-p">` +
            `<img class="ml-2" src="` +
            all_cat_img +
            `"></img>` +
            t +
            modal_ce_cat_selected.title +
            `</div>
                </div>`;
    }
    show_div.html(new_list);
}

function showCategoryInput() {
    let c = findCategoryForSCFCE(category_id);
    $("#category_id").val(c.id);
    $("#modcat-select-input").text(c.title);
}

function selectCatItemForSCFCE(category_id) {
    if (category_id != cat_selected) {
        $("#features-box").empty();
        $("#cf-inputs").empty();
        let c = findCategoryForSCFCE(category_id);
        $("#category_id").val(c.id);
        $("#modcat-select-input").text(c.title);
        cat_selected = category_id;
        $.ajax({
            url: get_cat_fis_route,
            method: "GET",
            data: {
                category_id: category_id
            },
            success: function (data) {
                cfeatures = data.cfeatures;
                citems = data.citems;
                feature_items = null;
                showChildrenFeaturesBoxs();
            }
        });
    }
}

function findCategoryWithTitleForSCFCE(cat_title) {
    for (let i = 0; i < categories.length; i++) {
        if (categories[i].title == cat_title) {
            return categories[i];
        }
    }
    return null;
}

function getAllChildrenCategories(categoryId) {
    let childrenCategories = [];
    let currentCategory = categories.find(
        category => category.id === categoryId
    );
    if (currentCategory) {
        childrenCategories.push(currentCategory);
        let subCategories = categories.filter(
            category => category.p_id === currentCategory.id
        );
        subCategories.forEach(subCategory => {
            childrenCategories = childrenCategories.concat(
                getAllChildrenCategories(subCategory.id)
            );
        });
    }
    return childrenCategories;
}

function modcatSelectSearchCategories() {
    let search = $("#search-categories-ch-modal-input")
        .val()
        .toLowerCase();
    // Get the categories
    let categories_div = $(`.filter-cat-box-desk`);
    // Hide all categories
    categories_div.hide();

    categories_div.filter((_, category_div) => {
        let category_title = $(category_div)
            .find("span")
            .text()
            .toLowerCase();
        if (category_title == "") {
            category_title = $(category_div)
                .find("div")
                .text()
                .toLowerCase();
        }
        let category = findCategoryWithTitleForSCFCE(category_title);
        if (category) {
            let childCategories = getAllChildrenCategories(category.id);
            let showCategory = false;
            childCategories.forEach(chCat => {
                if (
                    chCat.title.includes(search.toLowerCase()) ||
                    chCat.slug.includes(search.toLowerCase())
                ) {
                    showCategory = true;
                }
            });
            if (showCategory) {
                $(category_div).show(); // Wrap category_div with jQuery
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    });
}

let timeoutSugCatId = null;
function suggestCategories(input) {
    if (timeoutSugCatId) {
        clearTimeout(timeoutSugCatId);
    }
    timeoutSugCatId = setTimeout(() => {
        let title = input.value.toLowerCase();
        let sug_box = $("#sug-cat-box");
        let show_box = 0;
        $(".sug-cat").remove();
        categories.forEach(category => {
            clearStepWords(title).forEach(word => {
                if (
                    category.title.includes(word) ||
                    category.slug.includes(word) ||
                    category.ss?.includes(word)
                ) {
                    let sug_cat = `<span class="sug-cat" onclick="selectCatItemForSCFCE('${category.id}')">${category.title}</span>`;
                    sug_box.append(sug_cat);
                    show_box = 1;
                }
            });
        });
        if (show_box == 1) {
            sug_box.show();
        } else {
            sug_box.hide();
        }
    }, 5000);
}

function clearStepWords(text) {
    const words = text.split(" ");
    const stepWords = [
        "را",
        "ده",
        "های",
        "ان",
        "خرید",
        "فروش",
        "او",
        "کی",
        "سر",
        "باز",
        "پا",
        "دست",
        "روز",
        "این",
        "شد",
        "گر",
        "بد",
        "جا",
        "برای",
        "که",
        "کم",
        "یک",
        "دو",
        "سه",
        "کرد",
        "چرا",
        "تا",
        "می",
        "پر",
        "بی",
        "ترین",
        "ای",
        "با",
        "چه",
        "یک",
        "نه",
        "یا",
        "رو",
        "در",
        "به",
        "از",
        "سلام"
    ];
    const filteredWords = words.filter(
        word => word.length > 2 && !stepWords.includes(word.toLowerCase())
    );
    return filteredWords;
}

// end for select category for cu modal

// for select feature item for cu modal
showChildrenFeaturesBoxs();
setFeatureItems();

function setFeatureItems(parent_feature_id = null) {
    if (feature_items != null) {
        feature_items.forEach(fi => {
            let feature = findFeatureForSCFCE(fi.f_id);
            if (parent_feature_id == null) {
                if (feature.p_id == null) {
                    showChildrenFeaturesBoxs(fi.f_id);
                    selectItem(fi.i_id);
                    setFeatureItems(feature.id);
                }
            } else {
                if (feature.p_id == parent_feature_id) {
                    showChildrenFeaturesBoxs(fi.f_id);
                    selectItem(fi.i_id);
                    setFeatureItems(feature.id);
                }
            }
        });
    }
}

function showChildrenFeaturesBoxs(f_id = null) {
    if (cat_selected == null) {
        return;
    }
    let children_features;
    let fbox;
    if (f_id == null) {
        children_features = cfeatures.filter(f => f.p_id === null);
        fbox = $(`#features-box`);
    } else {
        let feature = findFeatureForSCFCE(f_id);
        children_features = featureChildrenForSCFCE(f_id);
        if (children_features.length == 0) {
            return;
        }
        fbox = $(`#fselect-box-${feature.slug}`);
    }
    children_features.forEach(chf => {
        if (chf.i_count > 1) {
            let chfInput = $(`#${chf.slug}`);
            if (chfInput.length > 0) {
                return;
            }
        }
        if ($(`#fselect-box-${chf.slug}`).children().length == 0) {
            let featureInput = `<div class="position-relative" id="fselect-box-${chf.slug}">
                                <span class="d-block">${chf.title}</span>
                                <div class="fselect-input" id="fselect-input-${chf.slug}"
                                    onclick="showFItemsForSCFCE('${chf.id}')">انتخاب
                                    ${chf.title}</div>
                                <div class="sfbox shadow" id="sfbox-${chf.slug}">
                                    <input class="sfsearch form-control" oninput="searchFItem('${chf.id}')"
                                        id="sfsearch-${chf.slug}" type="text" placeholder="جستجو کنید...">
                                    <div id="sfitems-list-${chf.slug}">
                                    </div>
                                    <span id="sfnores-${chf.id}" onclick="showCreateNewItemBox('${chf.id}')">وجود ندارد؟ اضافه کنید +</span>
                                    <div class="p-3" id="sfnores-input-box-${chf.id}">
                                    <input type="text" id="sfnores-input-${chf.id}" class="form-control mb-2">
                                    <button type="button" class="btn btn-sm btn-primary px-5" onclick="CreateNewItem('${chf.id}')" id="sfnores-input-add-${chf.id}">افزودن</button>
                                    <button type="button" class="btn btn-sm btn-danger px-3" onclick="closeCreateNewItemBox('${chf.id}')" id="sfnores-input-x-${chf.id}">لغو</button>
                                    </div>
                                </div>
                            </div>`;
            if (f_id == null) {
                fbox.append(featureInput);
            } else {
                fbox.after(featureInput);
            }
        }
    });
}

function generateRandomString(length) {
    const characters = "abcdefghijklmnopqrstuvwxyz0123456789";
    let result = "";
    const charactersLength = characters.length;

    for (let i = 0; i < length; i++) {
        result += characters.charAt(
            Math.floor(Math.random() * charactersLength)
        );
    }

    return result;
}

function CreateNewItem(feature_id) {
    let item_name = $(`#sfnores-input-${feature_id}`).val();
    if (item_name !== "") {
        let new_item_id = "test" + generateRandomString(20);
        let newfi = {
            e_title: item_name,
            f_id: feature_id,
            id: new_item_id,
            p_id: null,
            slug: item_name,
            title: item_name
        };
        citems.push(newfi);
        selectItem(new_item_id);
        closeCreateNewItemBox(feature_id);
        $(`#sfnores-input-${feature_id}`).val("");
        let item_input = $("<input>").attr({
            type: "hidden",
            name: feature_id + "-" + new_item_id,
            value: item_name + "-" + new_item_id
        });
        $(`#${submit_form_id}`).append(item_input);
    }
}

function showCreateNewItemBox(feature_id) {
    $(`#sfnores-input-box-${feature_id}`).show();
    $(`#sfnores-${feature_id}`).hide();
}
function closeCreateNewItemBox(feature_id) {
    $(`#sfnores-input-box-${feature_id}`).hide();
    $(`#sfnores-${feature_id}`).show();
}

$(document).click(function (event) {
    if (open_box != null) {
        if (!$(event.target).closest(open_box).length) {
            $(open_box).hide();
            open_box = null;
        }
    }
});

var open_box = null;
function showFItemsForSCFCE(f_id) {
    f = findFeatureForSCFCE(f_id);
    $(`#sfbox-${f.slug}`).show();
    setTimeout(() => {
        open_box = `#sfbox-${f.slug}`;
    }, 40);
    $(`#sfsearch-${f.slug}`).val("");
    let items_list_box = $(`.sfitems-list-${f.slug}`);
    if (items_list_box.children().length == 0) {
        let items_list = showFeatureItemsInput(f.id);
        $(`#sfitems-list-${f.slug}`).html(items_list);
    }
}

function findFeatureForSCFCE(f_id) {
    for (let i = 0; i < cfeatures.length; i++) {
        if (cfeatures[i].id == f_id) {
            return cfeatures[i];
        }
    }
    return null;
}
function findItemForSCFCE(i_id) {
    for (let i = 0; i < citems.length; i++) {
        if (citems[i].id == i_id) {
            return citems[i];
        }
    }
    return null;
}

function selectItem(item_id) {
    let item = findItemForSCFCE(item_id);
    if (item == null) {
        return;
    }
    let feature = findFeatureForSCFCE(item.f_id);
    let featureInput = $(`#${feature.slug}`);
    setTimeout(() => {
        $(`#sfbox-${feature.slug}`).hide();
        open_box = null;
    }, 50);

    if (feature.i_count == 1) {
        if (featureInput.length > 0) {
            if (featureInput.val() == item_id) {
                return;
            }
        } else {
            if ($("#cf-inputs").length == 0) {
                let features_inputs_el = `<div id="cf-inputs"></div>`;
                $(`#${submit_form_id}`).append(features_inputs_el);
            }
            let feature_input = `<input type="hidden" name="${feature.slug}" id="${feature.slug}">`;
            $(`#cf-inputs`).append(feature_input);

            featureInput = $(`#${feature.slug}`);
            featureInput.val(`${item.id}`);
            deleteSelectedItemsChildFeatures(feature.id);
            showChildrenFeaturesBoxs(feature.id);
        }
        featureInput.val(`${item.id}`);
    } else {
        if (featureInput.length > 0) {
            //some items already selected
            let featuresArray = JSON.parse(featureInput.val());
            if (featuresArray.length >= feature.i_count) {
                //selected maximum
                return;
            }
            if ($.inArray(item.id, featuresArray) === -1) {
                //if not selected yet
                featuresArray.push(item.id);
                featureInput.val(JSON.stringify(featuresArray));
                showChildrenFeaturesBoxs(feature.id);
                if (featuresArray.length == feature.i_count) {
                    $(`#choose-new-fitem-${feature.id}`).hide();
                }
            } else {
                return;
            }
        } else {
            if ($("#cf-inputs").length == 0) {
                let features_inputs_el = `<div id="cf-inputs"></div>`;
                $(`#${submit_form_id}`).append(features_inputs_el);
            }
            let feature_input = `<input type="hidden" name="${feature.slug}" id="${feature.slug}">`;
            $(`#cf-inputs`).append(feature_input);

            featureInput = $(`#${feature.slug}`);
            featureInput.val(`["${item.id}"]`);
            $(`#fselect-input-${feature.slug}`).removeAttr("onclick");
            let newItemChoose = `<span onclick="showFItemsForSCFCE('${feature.id}')" id="choose-new-fitem-${feature.id}">افزودن <img src="${add_new_item_img}"></span>`;
            $(`#fselect-input-${feature.slug}`).html(newItemChoose);
            deleteSelectedItemsChildFeatures(feature.id);
            showChildrenFeaturesBoxs(feature.id);
        }
    }
    if (feature.i_count == 1) {
        $(`#fselect-input-${feature.slug}`).text(item.title);
        let f_children = featureChildrenForSCFCE(feature.id);
        f_children.forEach(fchild => {
            if (fchild.i_count == 1) {
                $(`#fselect-input-${fchild.slug}`).text('انتخاب ' + fchild.title);
            } else {
                let chitemsValue = $(`#${fchild.slug}`).val();
                if (chitemsValue) {
                    let chitems = JSON.parse(chitemsValue);
                    if (Array.isArray(chitems)) {
                        chitems.forEach(item => {
                            removeFeatureItem(fchild.id, item);
                        });
                    }
                }
            }
        });
    } else {
        let itemspan = `<span onclick="removeFeatureItem('${feature.id}','${item.id}')" id="close-fitem-${feature.id}-${item.id}">${item.title} <img src="${remove_item_img}"></span>`;
        $(`#fselect-input-${feature.slug}`).append(itemspan);
    }
}

function removeFeatureItem(feature_id, item_id) {
    let feature = findFeatureForSCFCE(feature_id);
    let featureInput = $(`#${feature.slug}`);
    let featuresArray = JSON.parse(featureInput.val());
    let item_index = featuresArray.indexOf(item_id);
    if (item_index !== -1) {
        featuresArray.splice(item_index, 1);
    }
    if (featuresArray.length == 0) {
        featureInput.remove();
        $(`#fselect-input-${feature.slug}`).text(`انتخاب ${feature.title}`);
        setTimeout(() => {
            $(`#fselect-input-${feature.slug}`).attr(
                "onclick",
                `showFItemsForSCFCE('${feature.id}')`
            );
        }, 100);
        deleteFeatureChildrenBox(feature.id);
    } else {
        featureInput.val(JSON.stringify(featuresArray));
        showChildrenFeaturesBoxs(feature.id);
    }
    $(`#close-fitem-${feature.id}-${item_id}`).remove();
    $(`#choose-new-fitem-${feature.id}`).show();
    deleteSelectedItemsChildFeatures(feature.id, item_id);
}

function deleteFeatureChildrenBox(feature_id) {
    let f_children = featureChildrenForSCFCE(feature_id);
    f_children.forEach(fchild => {
        $(`#fselect-box-${fchild.slug}`).remove();
        deleteFeatureChildrenBox(fchild.id);
    });
}

function deleteSelectedItemsChildFeatures(feature_id, item_id = null) {
    let f_children = featureChildrenForSCFCE(feature_id);
    f_children.forEach(fchild => {
        let chfInput = $(`#${fchild.slug}`);
        if (fchild.i_count > 1) {
            if (chfInput.length > 0) {
                let chfvalue = JSON.parse(chfInput.val());
                let parent_item = findItemForSCFCE(item_id);
                for (let i = 0; i < chfvalue.length; i++) {
                    let chitem = findItemForSCFCE(chfvalue[i]);
                    if (chitem.p_id == parent_item.id) {
                        removeFeatureItem(fchild.id, chitem.id);
                    }
                }
            } else {
                deleteSelectedItemsChildFeatures(fchild);
            }
        } else {
            $(`#fselect-input-${fchild.slug}`).text(`انتخاب ${fchild.title}`);
            if (chfInput.length > 0) {
                chfInput.remove();
                deleteSelectedItemsChildFeatures(fchild);
            }
        }
    });
}

function getCurrectFormatFeatureInput(feature_slug) {
    let featureInput = $(`#${feature_slug}`).val();
    if (featureInput.startsWith("[") && featureInput.endsWith("]")) {
        let innerValues = featureInput.slice(1, -1).split(",");
        let fixedValues = innerValues.map(item => `"${item.trim()}"`);
        featureInput = `[${fixedValues.join(",")}]`;
    }
    return featureInput;
}

function showFeatureItemsInput(feature_id) {
    let feature = findFeatureForSCFCE(feature_id);
    let featureInput = $(`#${feature.slug}`);
    let selected_items;
    if (featureInput.length) {
        if (feature.i_count > 1) {
            selected_items = JSON.parse(featureInput.val());
        } else {
            selected_items = JSON.parse(`"${featureInput.val()}"`);
        }
    } else {
        selected_items = [];
    }
    let parent_item_id = null;
    let ilist = "";
    if (feature.p_id != null) {
        let pfeature = findFeatureForSCFCE(feature.p_id);
        let pfeatureInput = $(`#${pfeature.slug}`).val();
        if (pfeatureInput.length > 0) {
            if (pfeature.i_count > 1) {
                JSON.parse(pfeatureInput).forEach(pitem_id => {
                    parent_item_id = pitem_id;
                    let pitem = findItemForSCFCE(pitem_id);
                    featureItemsForSCFCE(feature_id, parent_item_id).forEach(
                        i => {
                            ilist += `<span class="sfitem-${feature_id} ${selected_items.includes(i.id)
                                    ? "item-selected"
                                    : ""
                                }" data-entitle="${i.e_title
                                }" onclick="selectItem('${i.id}')">${pitem.title
                                } - ${i.title}</span>`;
                        }
                    );
                });
            } else {
                parent_item_id = pfeatureInput;
                featureItemsForSCFCE(feature_id, parent_item_id).forEach(i => {
                    ilist += `<span class="sfitem-${feature_id} ${selected_items.includes(i.id) ? "item-selected" : ""
                        }" data-entitle="${i.e_title}" onclick="selectItem('${i.id
                        }')">${i.title}</span>`;
                });
            }
        }
    } else {
        featureItemsForSCFCE(feature_id, null).forEach(i => {
            ilist += `<span class="sfitem-${feature_id} ${selected_items.includes(i.id) ? "item-selected" : ""
                }" data-entitle="${i.e_title}" onclick="selectItem('${i.id}')">${i.title
                }</span>`;
        });
    }
    return ilist;
}

function featureChildrenForSCFCE(fea_id) {
    fea_children = [];
    cfeatures.forEach(feature => {
        if (feature.p_id == fea_id) {
            fea_children.push(feature);
        }
    });
    return fea_children;
}

function featureItemsForSCFCE(f_id, parent_item_id = null) {
    let fitems = [];
    if (parent_item_id == null) {
        citems.forEach(item => {
            if (item.f_id == f_id) {
                fitems.push(item);
            }
        });
    } else {
        citems.forEach(item => {
            if (item.f_id == f_id && item.p_id == parent_item_id) {
                fitems.push(item);
            }
        });
    }
    return fitems;
}

function searchFItem(f_id) {
    // Get the user's input
    let feature = findFeatureForSCFCE(f_id);
    let input = $(`#sfsearch-${feature.slug}`).val();
    // Get the items
    let items = $(`.sfitem-${f_id}`);
    // Hide all items
    items.hide();
    // Filter the items based on the user's input
    let filteredItems = items.filter((_, item) => {
        let text = $(item)
            .text()
            .toLowerCase();
        let dataEntitle = String($(item).data("entitle")).toLowerCase();
        return (
            text.includes(input.toLowerCase()) ||
            dataEntitle.includes(input.toLowerCase())
        );
    });
    // Show the filtered items
    filteredItems.each(function () {
        $(this).show();
    });
}

// end for select feature item for cu modal
