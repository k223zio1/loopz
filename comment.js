document.querySelectorAll('.comment-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        const section = document.getElementById('comments-' + id);

        section.classList.toggle('active');

        if (section.classList.contains('active')) {
            fetch('get_comments.php?id=' + id)
                .then(res => res.json())
                .then(data => {
                    const list = document.getElementById('comments-list-' + id);
                    list.innerHTML = '';

                    data.forEach(c => {
                        list.innerHTML += `
                            <div class="comment">
                                <strong>${c.username}</strong>: ${c.comment}
                            </div>`;
                    });
                });
        }
    });
});

// Submit comment
document.querySelectorAll('.submit-comment').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        const input = document.getElementById('new-comment-' + id);
        const comment = input.value.trim();
        if (!comment) return;

        fetch('comments.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `id=${id}&comment=${encodeURIComponent(comment)}`
        })
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById('comments-list-' + id);
            list.innerHTML += `
                <div class="comment">
                    <strong>${data.username}</strong>: ${data.comment}
                </div>`;
            input.value = '';
        });
    });
});
