document.addEventListener("DOMContentLoaded", function () {
    const video = document.getElementById("video-s");

    video.addEventListener("ended", function () {
        video.currentTime = 0;
        video.play().catch(() => { });
    });

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                // وقتی نزدیک دید میشه، فقط متادیتا و تکه اولش رو لود کن
                if (entry.isIntersecting && !video.dataset.prefetched) {
                    video.dataset.prefetched = "true";
                    video.preload = "metadata";
                    video.load();

                }

                // وقتی کامل دیده میشه، پخش کن
                if (entry.isIntersecting && entry.intersectionRatio > 0.5) {
                    video.muted = true;
                    video.play().catch(() => { });
                } else if (!entry.isIntersecting) {
                    if (!video.paused) video.pause();
                }
            });
        },
        { threshold: [0.2, 0.5] }
    );

    observer.observe(video);
});
