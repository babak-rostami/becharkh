document.addEventListener("DOMContentLoaded", function () {
    let video = document.getElementById("video-s");

    video.muted = true;

    let observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    video.play().catch((err) => {
                        // if (err.name !== "AbortError") {
                        //     console.warn("Video play error:", err);
                        // }
                    });
                } else {
                    if (!video.paused) video.pause();
                }
            });
        }, {
        threshold: 0.5,
    }
    );

    observer.observe(video);
});