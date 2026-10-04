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

function clickImg(imgId) {
    // Get the modal
    var modalId = "imageModal-" + imgId;
    var modal = document.getElementById(modalId);

    // Get the image and insert it inside the modal - use its "alt" text as a caption
    var imageId = "slideImg-" + imgId;
    var img = document.getElementById(imageId);
    var modalImage = "slide-img-" + imgId;
    var modalImg = document.getElementById(modalImage);
    img.onclick = function() {
        modal.style.display = "flex";
        modalImg.src = this.src;
        captionText.innerHTML = this.alt;
    };

    // Get the <span> element that closes the modal
    var closeId = "close-" + imgId;
    var span = document.getElementById(closeId);

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    };
}
