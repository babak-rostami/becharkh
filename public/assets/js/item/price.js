// for item price
const item_price_ul = $("#item-price-ul");
const lis_width = $(".item-price-li")
    .map(function() {
        return $(this).outerWidth(true);
    })
    .get()
    .reduce((a, b) => a + b, 0);

if ($("#item-price-div").width() < lis_width) {
    item_price_ul.addClass("item-price-scroll");
}

//end for item price
