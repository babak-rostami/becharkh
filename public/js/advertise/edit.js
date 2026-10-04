let isSubmitting = false;
function storeAdvertise() {
    let ad_title = $("#title").val().trim();
    let ad_body = $("#advertise_body").val().trim();
    let ad_phone = $("#advertise-phone").val();
    let send_advertise = 1;
    if (ad_title === '') {
        send_advertise = 0;
        $("#title-error").show()
        $("#title").addClass('input-style-error')
        $("#title-error").text('لطفا این قسمت را تکمیل کنید')
        scrollToId('title', 50)
    }
    if (ad_body === '') {
        send_advertise = 0;
        $("#body-error").show()
        $("#advertise_body").addClass('input-style-error')
        $("#body-error").text('لطفا این قسمت را تکمیل کنید')
        if (ad_title !== '') {
            scrollToId('advertise_body', 50)
        }
    }
    if (ad_phone === '') {
        send_advertise = 0;
        $("#phone-error").show()
        $("#advertise-phone").addClass('input-style-error')
        $("#phone-error").text('لطفا این قسمت را تکمیل کنید')
    }
    if (send_advertise) {
        if (isSubmitting) return;
        isSubmitting = true;

        let store_btn = $("#store-ad-btn");
        store_btn.prop('disabled', true);
        store_btn.text('در حال ثبت آگهی...');
        store_btn.removeClass('btn-primary').addClass('btn-light');

        $("#adform").submit();
    }
}

function scrollToId(id, extra_offset = 0) {
    let sId = "#" + id;
    let scrollOffset = $(sId).offset().top - extra_offset;
    $("html, body").animate({
        scrollTop: scrollOffset
    },
        1000
    );
}

$("#has_price").change(function () {
    const priceDiv = document.getElementById("price");
    var id = $(this)
        .find("option:selected")
        .val();
    if (id == 1) {
        priceDiv.disabled = false;
        priceDiv.classList.add("required");
    } else {
        $("#price").val("");
        $("#show-price-span").hide();
        priceDiv.disabled = true;
        priceDiv.classList.remove("required");
    }
});

function phoneOnChange(event, el, min, max) {
    validateNumberInput(event);
    countCharacters(el, min, max);
    $("#phone-error").hide();
    $("#advertise-phone").removeClass('input-style-error');
}

function validateNumberInput(event) {
    const input = event.target;
    const inputValue = input.value.replace(/[^0-9]/g, "");
    input.value = inputValue;
    // Prevent pasting text
    if (event.clipboardData && event.clipboardData.setData) {
        event.clipboardData.setData("text/plain", "");
        event.preventDefault();
    }
}

function changePrice() {
    inputValue = $("#price").val();
    if (inputValue.length < 14 && inputValue.length > 0) {
        var price = formatNumber(inputValue) + " تومان";
        $("#show-price-span").text(price);
        $("#show-price-span").show();
    }
    if (inputValue.length == 0) {
        $("#show-price-span").text("");
    }
}
function formatNumber(number) {
    return new Intl.NumberFormat().format(number);
}

function readURL(input, i) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            id = "#blah-" + i;
            $(id).attr("src", e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
        $("#add-image-alert").hide();
    }
}

function addImage() {
    const imageBoxes = document.getElementsByClassName("image-box");
    let newImageKey = parseInt(adImageCount) + 1;
    let imageLength = parseInt(imageBoxes.length);
    if (imageLength == 7) {
        $("#select-image").hide();
    }
    newImageId = imageLength + 1;

    for (var i = newImageKey; i <= imageLength; i++) {
        var fileInputId = "#imgInp-" + i;
        if ($(fileInputId).length) {
            var fileInput = $(fileInputId).get(0);

            if (fileInput.files.length === 0) {
                $("#add-image-alert").show();
                return;
            }
        }
    }

    newImageHtml =
        '<div class="col-12 col-md-3 text-center shadow-sm p-4 mx-1 mt-2 image-box"><img class="def-image" style="width: 64px; margin-bottom: 8px" id="blah-' +
        newImageId +
        '" src="' +
        defImage +
        '" alt="تصویر را انتخاب کنید" /><br><input onchange="readURL(this,' +
        newImageId +
        ')" type="file" name="img-' +
        newImageId +
        '" id="imgInp-' +
        newImageId +
        '" accept="image/*" data-msg-accept="تغییر عکس" style="display:none" />' +
        '<button type="button" class="btn btn-outline-dark" onclick="document.getElementById(' +
        "'" +
        "imgInp-" +
        newImageId +
        "'" +
        ').click()">تغییر عکس</button></div>';
    $("#image-row").append(newImageHtml);
}

function titleChange(element, min, max) {
    countCharacters(element, min, max)
    $("#title-error").hide();
    const title_input = $("#title");
    if (title_input.hasClass('input-style-error')) {
        title_input.removeClass('input-style-error');
    }
}

function bodyChange() {
    $("#body-error").hide();
    const body_input = $("#advertise_body");
    if (body_input.hasClass('input-style-error')) {
        body_input.removeClass('input-style-error');
    }
}

function countCharacters(input, min = null, max = null) {
    var currentLength = $(input).val().length;
    var maxLength = max;
    var checkMax = 0;
    var minLength = min;
    var charMinSpanId = "#charCountMin-" + input.id;
    var CharMinSpan = $(charMinSpanId);
    var charMaxSpanId = "#charCountMax-" + input.id;
    var CharMaxSpan = $(charMaxSpanId);

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

// for select feature item for cu modal
showChildrenFeaturesBoxs();
setFeatureItems();

function setFeatureItems(parent_feature_id = null) {
    if (feature_items != null) {
        feature_items.forEach(fi => {
            let feature = findFeatureForSCFCE(fi.f_id);
            if (fi.type == 0) {
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
            } else if (fi.type == 1) {
                showChildrenFeaturesBoxs(fi.f_id);
                $(`#${feature.slug}`).val(fi.value);
            }
        });
    }
}

function showChildrenFeaturesBoxs(f_id = null) {
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
        if ($(`#fselect-box-${chf.slug}`).children().length == 0) {
            let featureInput;
            if (chf.type == 0) {
                featureInput = `<div class="position-relative ${chf.require ? "required" : ""
                    }" id="fselect-box-${chf.slug}">
                                <span class="d-block">${chf.title}`;
                if (chf.require) {
                    featureInput += `<span class="red-color">*</span>`;
                }
                featureInput += `</span>
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
            } else if (chf.type == 1) {
                featureInput = `<div class="my-2" id="fselect-box-${chf.slug}">
                                <span class="d-block">${chf.title}`;
                if (chf.require) {
                    featureInput += `<span class="red-color">*</span>`;
                }
                featureInput += `</span><input class="form-control ${chf.require ? "required" : ""
                    }" id="${chf.slug}" name="${chf.slug
                    }" type="text" placeholder="${chf.title}">
                            </div>`;
            }
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

    $(`#fselect-input-${feature.slug}`).text(item.title);
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
        $(`#fselect-input-${fchild.slug}`).text(`انتخاب ${fchild.title}`);
        if (chfInput.length > 0) {
            chfInput.remove();
            deleteSelectedItemsChildFeatures(fchild);
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
        selected_items = JSON.parse(`"${featureInput.val()}"`);
    } else {
        selected_items = [];
    }
    let parent_item_id = null;
    let ilist = "";
    if (feature.p_id != null) {
        let pfeature = findFeatureForSCFCE(feature.p_id);
        let pfeatureInput = $(`#${pfeature.slug}`).val();
        if (pfeatureInput.length > 0) {
            parent_item_id = pfeatureInput;
            featureItemsForSCFCE(feature_id, parent_item_id).forEach(i => {
                ilist += `<span class="sfitem-${feature_id} ${selected_items.includes(i.id) ? "item-selected" : ""
                    }" data-entitle="${i.e_title}" onclick="selectItem('${i.id
                    }')">${i.title}</span>`;
            });
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

function priceShow(show) {
    if (show == 1) {
        $("#price-input-form").show();
        $("#price").addClass("required");
        $("#show-price-option")
            .removeClass("btn-outline-dark")
            .addClass("btn-primary");
        $("#dshow-price-option")
            .removeClass("btn-primary")
            .addClass("btn-outline-dark");
        $("#price-option-box").removeClass("mb-3");
    } else {
        $("#price").val("");
        $("#price").removeClass("required");
        $("#price-input-form").hide();
        $("#dshow-price-option")
            .removeClass("btn-outline-dark")
            .addClass("btn-primary");
        $("#show-price-option")
            .removeClass("btn-primary")
            .addClass("btn-outline-dark");
        $("#price-option-box").addClass("mb-3");
    }
}
function phoneShow(show) {
    if (show == 1) {
        $("#phone-input-form").show();
        $("#show-phone-option")
            .removeClass("btn-outline-dark")
            .addClass("btn-primary");
        $("#dshow-phone-option")
            .removeClass("btn-primary")
            .addClass("btn-outline-dark");
        $("#phone-option-box").removeClass("mb-3");
    } else {
        $("#phone").val("");
        $("#phone-input-form").hide();
        $("#dshow-phone-option")
            .removeClass("btn-outline-dark")
            .addClass("btn-primary");
        $("#show-phone-option")
            .removeClass("btn-primary")
            .addClass("btn-outline-dark");
        $("#phone-option-box").addClass("mb-3");
    }
}

/* for choose location */

$(document).ready(function () {
    setTimeout(() => {
        if (district_id) {
            chooseDistrict(district_id);
        } else {
            chooseCity(city_id);
        }
    }, 2000);
});

let location_modal_page = "prov";
function showProvinces() {
    location_modal_page = "prov";
    $("#choose-location-title").text("انتخاب استان");
    $("#choose-location-back").unbind("click");
    const container = $("#choose-location-items");
    container.show();
    container.empty();
    $("#search-choose-location-items").hide();
    $("#choose-location-search").val("");
    $.each(provinces, function (index, province) {
        let new_item = $("<span>")
            .text(province.name)
            .addClass("choose-location-item")
            .append(
                `<img class="float-left" src="${ftp_path}files/other/images/next.png">`
            )
            .click(function () {
                chooseProvince(province.id);
            });
        if (index == 0) {
            new_item.addClass("mt-2");
        }
        new_item.appendTo(container);
    });
}
function chooseProvince(prov_id) {
    location_modal_page = "city";
    city_id = null;
    district_id = null;
    province_id = prov_id;
    addLocation();
    $("#choose-location-back").show();
    $("#choose-location-back")
        .unbind("click")
        .bind("click", function () {
            showProvinces();
        });
    $("#choose-location-title").text("انتخاب شهر");
    const container = $("#choose-location-items");
    container.show();
    $("#search-choose-location-items").hide();
    $("#choose-location-search").val("");

    let prov_cities = cities.filter(function (city) {
        return city.p_id === prov_id;
    });
    if (prov_cities.length > 0) {
        container.empty();
        $.each(prov_cities, function (index, city) {
            let new_item = $("<span>")
                .text(city.name)
                .addClass("choose-location-item")
                .append(
                    `<img class="float-left" src="${ftp_path}files/other/images/next.png">`
                )
                .click(function () {
                    chooseCity(city.id);
                });
            if (index == 0) {
                new_item.addClass("mt-2");
            }
            new_item.appendTo(container);
        });
    }
}
function chooseCity(c_id) {
    location_modal_page = "dist";
    province_id = findProvByCityId(c_id).id;
    city_id = c_id;
    district_id = null;
    addLocation();
    $("#choose-location-back").show();
    $("#choose-location-back")
        .unbind("click")
        .bind("click", function () {
            chooseProvince(province_id);
        });
    $("#choose-location-title").text("انتخاب محله");
    const container = $("#choose-location-items");
    container.show();
    $("#search-choose-location-items").hide();
    $("#choose-location-search").val("");

    let prov_districts = districts.filter(function (district) {
        return district.c_id === c_id;
    });
    if (prov_districts.length > 0) {
        container.empty();
        $.each(prov_districts, function (index, district) {
            let new_item = $("<span>")
                .text(district.name)
                .addClass("choose-location-item")
                .click(function () {
                    chooseDistrict(district.id);
                });
            if (index == 0) {
                new_item.addClass("mt-2");
            }
            new_item.appendTo(container);
        });
    } else {
        $("#choose-location-modal").modal("hide");
    }
}
function chooseDistrict(d_id) {
    city = findCityByDistId(d_id);
    city_id = city.id;
    province_id = city.p_id;
    district_id = d_id;
    addLocation();
    $("#choose-location-modal").modal("hide");
}

function addLocation() {
    let text = "";
    if (province_id) {
        let province = provinces.find(p => p.id === province_id);
        text += province.name;
        $("#province_id").val(province_id);
    } else {
        $("#province_id").val(null);
    }
    if (city_id) {
        let city = cities.find(c => c.id === city_id);
        text += " > " + city.name;
        $("#city_id").val(city_id);
    } else {
        $("#city_id").val(null);
    }
    if (district_id) {
        let district = districts.find(d => d.id === district_id);
        text += " > " + district.name;
        $("#district_id").val(district_id);
    } else {
        $("#district_id").val(null);
    }
    $("#choose-location-btn").text(text);
}

var search_loc_delay = 1000;
var search_loc_timeoutId;
$("#choose-location-search").on("input", function () {
    clearTimeout(search_loc_timeoutId);
    // search_loc_timeoutId = setTimeout(function () {
    searchLocation();
    // }, search_loc_delay);
});
function searchLocation() {
    if (location_modal_page == "prov" || location_modal_page == "city") {
        const result_box = $("#choose-location-items");
        const search_result_box = $("#search-choose-location-items");
        let search_input = $("#choose-location-search").val();
        if (search_input.trim() !== "") {
            let search_provinces = provinces.filter(function (p) {
                return p.name.includes(search_input);
            });
            let search_cities = cities.filter(function (city) {
                return (
                    (province_id != null ? city.p_id == province_id : true) &&
                    city.name.includes(search_input)
                );
            });
            result_box.hide();
            search_result_box.show();
            search_result_box.empty();
            if (search_cities.length > 0 || search_provinces.length > 0) {
                $.each(search_provinces, function (index, province) {
                    let new_item = $("<span>")
                        .text("استان " + province.name)
                        .addClass("choose-location-item")
                        .append(
                            `<img class="float-left" src="${ftp_path}files/other/images/next.png">`
                        )
                        .click(function () {
                            chooseProvince(province.id);
                        });
                    if (index == 0) {
                        new_item.addClass("mt-2");
                    }
                    new_item.appendTo(search_result_box);
                });
                $.each(search_cities, function (index, city) {
                    let new_item = $("<span>")
                        .text(
                            "شهر " +
                            city.name +
                            " در استان " +
                            findProvById(city.p_id).name
                        )
                        .addClass("choose-location-item")
                        .append(
                            `<img class="float-left" src="${ftp_path}files/other/images/next.png">`
                        )
                        .click(function () {
                            chooseCity(city.id);
                        });
                    if (index == 0) {
                        new_item.addClass("mt-2");
                    }
                    new_item.appendTo(search_result_box);
                });
            } else {
                let new_item = $("<span>")
                    .text("محل مورد نظر پیدا نشد")
                    .addClass("search-location-404");
                new_item.appendTo(search_result_box);
            }
        } else {
            result_box.show();
            search_result_box.empty();
            search_result_box.hide();
        }
    } else if (location_modal_page == "dist") {
        const result_box = $("#choose-location-items");
        const search_result_box = $("#search-choose-location-items");
        let search_input = $("#choose-location-search").val();
        if (search_input.trim() !== "") {
            let search_districts = districts.filter(function (district) {
                return (
                    district.c_id == city_id &&
                    district.name.includes(search_input)
                );
            });
            result_box.hide();
            search_result_box.show();
            search_result_box.empty();
            if (search_districts.length > 0) {
                $.each(search_districts, function (index, district) {
                    let new_item = $("<span>")
                        .text(district.name)
                        .addClass("choose-location-item")
                        .click(function () {
                            chooseDistrict(district.id);
                        });
                    if (index == 0) {
                        new_item.addClass("mt-2");
                    }
                    new_item.appendTo(search_result_box);
                });
            } else {
                let new_item = $("<span>")
                    .text("محل مورد نظر پیدا نشد")
                    .addClass("search-location-404");
                new_item.appendTo(search_result_box);
            }
        } else {
            result_box.show();
            search_result_box.empty();
            search_result_box.hide();
        }
    }
}

function findCityByDistId(d_id) {
    let district = districts.find(d => d.id === d_id);
    return findCityById(district.c_id);
}
function findProvByCityId(c_id) {
    let city = cities.find(c => c.id === c_id);
    return findProvById(city.p_id);
}
function findProvById(p_id) {
    return provinces.find(p => p.id === p_id);
}
function findCityById(c_id) {
    return cities.find(c => c.id === c_id);
}

/* end for choose location */



// for select images box


let imageFiles = {}; // ذخیره‌ی فایل‌ها بر اساس id یکتا
let nextId = 1; // شناسه یکتا برای هر عکس
const maxImages = 8;

function clickSelectImage() {
    $('#images').click();
}

// فقط یک بار event ثبت می‌کنیم
$(document).on('change', '#images', function (event) {
    const files = event.target.files;

    for (let file of files) {
        if (Object.keys(imageFiles).length >= maxImages) break;

        const id = nextId++;
        imageFiles[id] = file;

        const ext = file.name.split('.').pop().toLowerCase();
        if (['heic', 'heif'].includes(ext)) {
            // preview برای آیفون غیر فعال
            addImagePreview(id, null, file, false);
        } else {
            const reader = new FileReader();
            reader.onload = e => addImagePreview(id, e.target.result, file, true);
            reader.readAsDataURL(file);
        }
    }

    $('#images').val('');
    updateHiddenInput();
});

function addImagePreview(id, src, file, showPreview = true) {
    let imgHtml = showPreview
        ? `<img src="${src}" alt="selected">`
        : `<div class="no-preview">انتخاب شد</div>`;

    const html = `
                    <div class="select-img-item" id="select-img-item-${id}">
                        ${imgHtml}
                        <div class="img-actions">
                            <button type="button" class="edit-img-btn" onclick="editImage(${id})">ویرایش</button>
                            <button type="button" class="delete-img-btn" onclick="removeImage(${id})">حذف</button>
                        </div>
                    </div>
                `;
    $('#add-image-btn').before(html);

    if (Object.keys(imageFiles).length >= maxImages) {
        $('#add-image-btn').hide();
    }
}

function removeImage(id) {
    delete imageFiles[id];
    $(`#select-img-item-${id}`).remove();
    updateHiddenInput();

    if (Object.keys(imageFiles).length < maxImages) {
        $('#add-image-btn').show();
    }
}

function editImage(id) {
    const input = $('<input type="file" accept="image/*" class="d-none">');
    input.on('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        const ext = file.name.split('.').pop().toLowerCase();
        imageFiles[id] = file;

        if (['heic', 'heif'].includes(ext)) {
            $(`#select-img-item-${id} img`).remove();
            $(`#select-img-item-${id}`).prepend('<div class="no-preview">انتخاب شد</div>');
        } else {
            const reader = new FileReader();
            reader.onload = ev => {
                let imgEl = $(`#select-img-item-${id} img`);
                if (imgEl.length === 0) {
                    $(`#select-img-item-${id} .no-preview`).remove();
                    $(`#select-img-item-${id}`).prepend(`<img src="${ev.target.result}" alt="selected">`);
                } else {
                    imgEl.attr('src', ev.target.result);
                }
            };
            reader.readAsDataURL(file);
        }

        updateHiddenInput();
    });
    input.click();
}

function updateHiddenInput() {
    const newInput = $('<input>', {
        type: 'file',
        id: 'images',
        name: 'images[]',
        class: 'd-none',
        multiple: true,
        accept: 'image/*'
    });

    const dt = new DataTransfer();
    Object.values(imageFiles).forEach(file => dt.items.add(file));
    newInput[0].files = dt.files;

    $('#images').replaceWith(newInput);
}

// let deleteTarget = null;

// // وقتی کاربر روی "ویرایش" عکس قدیمی کلیک می‌کند
// function editOldImage(index, advertiseId) {
//     const input = $('<input type="file" accept="image/*" class="d-none">');
//     input.on('change', function (e) {
//         const file = e.target.files[0];
//         if (!file) return;

//         const formData = new FormData();
//         formData.append('image', file);
//         formData.append('advertise_id', advertiseId);
//         formData.append('index', index);
//         formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

//         $.ajax({
//             url: '/advertises/update-image',
//             type: 'POST',
//             data: formData,
//             processData: false,
//             contentType: false,
//             beforeSend: () => {
//                 $(`#select-img-item-${index} .edit-img-btn`).text('در حال ویرایش...');
//             },
//             success: (response) => {
//                 if (response.url) {
//                     $(`#select-img-item-${index} img`).attr('src', response.url);
//                 }
//                 toastr.success('عکس با موفقیت ویرایش شد.');
//             },
//             error: () => {
//                 toastr.error('خطا در ویرایش عکس');
//             },
//             complete: () => {
//                 $(`#select-img-item-${index} .edit-img-btn`).text('ویرایش');
//             }
//         });
//     });
//     input.click();
// }

// // وقتی کاربر روی "حذف" عکس قدیمی کلیک می‌کند
// function confirmDeleteImage(index, advertiseId) {
//     deleteTarget = { index, advertiseId };
//     $('#deleteConfirmModal').modal('show');
// }

// // وقتی کاربر حذف را تأیید می‌کند
// $('#confirmDeleteBtn').on('click', function () {
//     if (!deleteTarget) return;

//     const { index, advertiseId } = deleteTarget;
//     $('#deleteConfirmModal').modal('hide');

//     $.ajax({
//         url: '/advertises/delete-image',
//         type: 'POST',
//         data: {
//             advertise_id: advertiseId,
//             index: index,
//             _token: $('meta[name="csrf-token"]').attr('content')
//         },
//         beforeSend: () => {
//             $(`#select-img-item-${index} .delete-img-btn`).text('در حال حذف...');
//         },
//         success: (response) => {
//             $(`#select-img-item-${index}`).remove();
//             toastr.success('عکس با موفقیت حذف شد.');
//         },
//         error: () => {
//             toastr.error('خطا در حذف عکس');
//         }
//     });
// });

// end for select images box