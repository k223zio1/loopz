let currentMedia = null;
let selectedFile = null;

function openEditor() {
    document.getElementById('editorPreview').innerHTML = '';
    document.getElementById('captionInput').value = '';
    selectedFile = null;
    currentMedia = null;
    document.getElementById('editorModal').style.display = 'flex';
}

function closeEditor() {
    document.getElementById('editorModal').style.display = 'none';
}

document.getElementById('mediaInput').addEventListener('change', e => {
    const file = e.target.files[0];
    if (!file) return;

    selectedFile = file;

    const url = URL.createObjectURL(file);
    const preview = document.getElementById('editorPreview');
    preview.innerHTML = '';

    if (file.type.startsWith('video')) {
        const v = document.createElement('video');
        v.src = url;
        v.controls = true;
        preview.appendChild(v);
        currentMedia = v;
    } else {
        const img = document.createElement('img');
        img.src = url;
        preview.appendChild(img);
        currentMedia = img;
    }
});

function applyFilter() {
    if (!currentMedia) return;
    currentMedia.style.filter = 'contrast(120%) saturate(140%)';
}

document.getElementById('uploadBtn').addEventListener('click', () => {

    if (!selectedFile) {
        alert('Select an image or video');
        return;
    }

    const uploadBtn = document.getElementById('uploadBtn');
    const loading = document.getElementById('loading');

    uploadBtn.disabled = true;
    uploadBtn.textContent = 'Uploading...';
    loading.style.display = 'block';

    const formData = new FormData();
    formData.append('media', selectedFile);

    const music = document.getElementById('musicInput').files[0];
    if (music) formData.append('music', music);

    formData.append('caption', document.getElementById('captionInput').value);

    fetch('edited.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(text => {
        console.log('SERVER RESPONSE:', text);

        let data;
        try {
            data = JSON.parse(text);
        } catch {
            alert('Server error (invalid response)');
            return;
        }

        uploadBtn.disabled = false;
        uploadBtn.textContent = 'Upload';
        loading.style.display = 'none';

        if (data.success) {
            alert('Upload successful 🎉');
            setTimeout(() => {
                window.location.href = 'main.php';
            }, 1500);
        } else {
            alert(data.error || 'Upload failed');
        }
    })
    .catch(() => {
        uploadBtn.disabled = false;
        uploadBtn.textContent = 'Upload';
        loading.style.display = 'none';
        alert('Upload error');
    });
});
