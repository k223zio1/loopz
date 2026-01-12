document.getElementById('deleteAccountBtn').addEventListener('click', () => {

    const confirmDelete = confirm(
        '⚠ Are you sure?\n\nThis will permanently delete your account and videos.'
    );

    if (!confirmDelete) return;

    fetch('delete_account.php', {
        method: 'POST'
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Account deleted successfully');
            window.location.href = 'index.html'; // back to login
        } else {
            alert(data.error || 'Failed to delete account');
        }
    })
    .catch(() => {
        alert('Server error');
    });
});
