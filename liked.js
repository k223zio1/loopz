document.addEventListener('DOMContentLoaded', () => {

    const videos = document.querySelectorAll('.feed-video');
    let current = null;

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            const video = entry.target;

            if (entry.isIntersecting) {
                if (current && current !== video) {
                    current.pause();
                }
                video.play().catch(()=>{});
                current = video;
            } else {
                video.pause();
            }
        });
    }, { threshold: 0.75 });

    videos.forEach(v => {
        observer.observe(v);
        v.addEventListener('click', () => {
            v.paused ? v.play() : v.pause();
        });
    });

});

let audioUnlocked = false;

document.querySelectorAll('.grid-item video').forEach(video => {

    // Hover play
    video.addEventListener('mouseenter', () => {
        video.play().catch(()=>{});
    });


    // Global unlock (optional)
document.body.addEventListener('click', () => {
    audioUnlocked = true;
});
    video.addEventListener('mouseleave', () => {
        video.pause();
        video.currentTime = 0;
        video.muted = true;
    });

    // FIRST CLICK unlocks sound
    video.addEventListener('click', () => {
        audioUnlocked = true;
        video.muted = false;
        video.volume = 1;
        video.play();
    });

});