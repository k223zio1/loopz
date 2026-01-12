/* ===== UI + COMMENTS + LIKES ===== */
document.addEventListener('DOMContentLoaded', () => {

    // PROFILE DROPDOWN
    const profileToggle = document.getElementById('profileToggle');
    const profileDropdown = document.getElementById('profileDropdown');

    profileToggle.addEventListener('click', e => {
        e.stopPropagation();
        profileDropdown.classList.toggle('active');
    });

    document.addEventListener('click', () => {
        profileDropdown.classList.remove('active');
    });

    // LIKE BUTTON
    document.querySelectorAll('.like-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            fetch('like.php?id=' + btn.dataset.id)
            .then(res => res.json())
            .then(data => {
                btn.nextElementSibling.textContent = data.likes;
                btn.classList.toggle('liked', data.status === 'liked');
            });
        });
    });

    // COMMENTS
    document.querySelectorAll('.comment-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const modal = document.getElementById('comment-modal-' + id);
            const list = document.getElementById('comments-list-' + id);

            modal.style.display = 'flex';

            fetch('get_comments.php?id=' + id)
            .then(res => res.json())
            .then(data => {
                list.innerHTML = '';
                data.forEach(c => {
                    list.innerHTML += `<p><strong>${c.username}</strong>: ${c.comment}</p>`;
                });
            });
        });
    });

    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('comment-modal-' + btn.dataset.id).style.display = 'none';
        });
    });

    document.querySelectorAll('.submit-comment').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            const input = document.getElementById('new-comment-' + id);
            const list = document.getElementById('comments-list-' + id);

            if (!input.value.trim()) return;

            fetch('comments.php', {
                method: 'POST',
                headers: {'Content-Type':'application/x-www-form-urlencoded'},
                body: `id=${id}&comment=${encodeURIComponent(input.value)}`
            })
            .then(res => res.json())
            .then(data => {
                list.innerHTML = '';
                data.forEach(c => {
                    list.innerHTML += `<p><strong>${c.username}</strong>: ${c.comment}</p>`;
                });
                input.value = '';
            });
        });
    });

});


/* ===== FEED VIDEO OBSERVER (UNCHANGED) ===== */
document.addEventListener('DOMContentLoaded', () => {

    const videos = document.querySelectorAll('.feed-video');

    const observerOptions = {
        root: null,
        threshold: 0.75
    };

    let currentVideo = null;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const video = entry.target;
            if (entry.isIntersecting) {
                if (currentVideo && currentVideo !== video) {
                    currentVideo.pause();
                }
                video.play().catch(() => {});
                currentVideo = video;
            } else {
                video.pause();
            }
        });
    }, observerOptions);

    videos.forEach(video => {
        observer.observe(video);

        video.addEventListener('click', () => {
            video.paused ? video.play() : video.pause();
        });
    });

});

document.querySelectorAll('.video-card').forEach(card => {

    const upBtn = card.querySelector('.up-btn');
    const downBtn = card.querySelector('.down-btn');

    upBtn?.addEventListener('click', () => {
        const prev = card.previousElementSibling;
        if (prev) {
            prev.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

    downBtn?.addEventListener('click', () => {
        const next = card.nextElementSibling;
        if (next) {
            next.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

});

const settingsToggle = document.getElementById('settingsToggle');
const settingsMenu = document.getElementById('settingsMenu');

settingsToggle.addEventListener('click', e => {
    e.stopPropagation();
    settingsMenu.classList.toggle('active');
});

document.addEventListener('click', () => {
    settingsMenu.classList.remove('active');
});