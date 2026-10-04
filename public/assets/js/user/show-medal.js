// for user medal

let medal_box_open = null;
let umedalBox = null;
function openMedalBox(id) {
    if (!medal_box_open) {
        let medalBox = $(`#medalbox-${id}`);
        medalBox.show();
        setTimeout(() => {
            medal_box_open = `medalbox-${id}`;
            umedalBox = $(".umedal-box");
        }, 100);
    }
}

$(document).on("click", e => {
    if (medal_box_open != null && !umedalBox.has(e.target).length) {
        $(`#${medal_box_open}`).hide();
        medal_box_open = null;
    }
});

// end for user medal
