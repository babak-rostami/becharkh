// $(document).ready(function () {
//     product_ids.forEach(function (productId) {
//         let galleryDiv = $(`<div id="affil-gallery-${productId}"></div>`);
//         let figures = $(`#affilb-body-${productId} figure`).detach();

//         let productVimgDiv = $(`#product-vimg-div-${productId}`);
//         if (productVimgDiv.length > 0) {
//             galleryDiv.append(productVimgDiv);
//         }

//         if (figures.length > 0 || productVimgDiv.length > 0) {
//             figures.each(function (index) {
//                 let newId = `aff-img-${productId}-` + (index + 1);
//                 $(this)
//                     .find("img")
//                     .attr("id", newId);
//                 $(this).on("click", function () {
//                     if ($(`#affilb-body-${productId}`).hasClass('img-is-link') && $(`#affilb-route-${productId}`).attr('href')) {
//                         let new_url = $(`#affilb-route-${productId}`).attr('href');
//                         jsurl(new_url, 1);
//                     } else {
//                         clickGalleryImg(newId, "affilate");
//                     }
//                 });
//                 galleryDiv.append($(this));
//             });
//             $(`#affilb-route-${productId}`).before(galleryDiv);
//             $(`#affilb-body-${productId}`).append(
//                 $(`#affilb-link-${productId}`)
//             );
//         }
//     });
// });
