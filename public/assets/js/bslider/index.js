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

    // if (!no_scroll) {
    //     setInterval(() => {
    //         if (autoScrolling) {
    //             if (!autoScrollingDir) {
    //                 if (sliderElement.scrollLeft >= 0) {
    //                     autoScrollingDir = true;
    //                 } else {
    //                     sliderElement.scrollLeft += 5;
    //                     velX = 5;
    //                 }
    //             } else {
    //                 if (scroll_smooth) {
    //                     if (
    //                         sliderElement.scrollLeft - 1 <=
    //                         -(
    //                             sliderElement.scrollWidth -
    //                             sliderElement.offsetWidth
    //                         )
    //                     ) {
    //                         sliderElement.scrollLeft = 0;
    //                     }
    //                     velX = -0.1;
    //                 } else {
    //                     if (
    //                         sliderElement.scrollLeft - 1 <=
    //                         -(
    //                             sliderElement.scrollWidth -
    //                             sliderElement.offsetWidth
    //                         )
    //                     ) {
    //                         autoScrollingDir = false;
    //                     } else {
    //                         sliderElement.scrollLeft -= 5;
    //                         velX = -5;
    //                     }
    //                 }
    //             }
    //             beginMomentumTracking();
    //         }
    //     }, scroll_time);
    // }
}
