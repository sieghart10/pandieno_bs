function previewImage(event) {
    console.log("File input changed");
    const file = event.target.files[0];
    const preview = document.getElementById('cover-image-preview');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            console.log("File read successfully");
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    } else {
        resetImage();
    }
}

function resetImage() {
    console.log("Resetting image");
    const preview = document.getElementById('cover-image-preview');
    preview.src = defaultImagePath;
    preview.style.display = 'block';
    document.getElementById('file-upload-button').value = '';

    console.log("Image reset to:", defaultImagePath);
}

function handleImageError(img) {
    img.src = defaultImagePath; 
    img.alt = 'Default Cover Image';
}
