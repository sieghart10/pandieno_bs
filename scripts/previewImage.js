function previewImage(event) {
    console.log("File input changed"); // Check if this is logged
    const file = event.target.files[0];
    const preview = document.getElementById('cover-image-preview');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            console.log("File read successfully"); // Check if this is logged
            preview.src = e.target.result; // Set the preview image source to the loaded file
            preview.style.display = 'block'; // Show the preview image
        }
        reader.readAsDataURL(file); // Read the file as a data URL
    } else {
        resetImage(); // Reset if no file
    }
}

function resetImage() {
    console.log("Resetting image"); // Check if this is logged
    const preview = document.getElementById('cover-image-preview');
    preview.src = defaultImagePath; // Reset the image source to the default
    preview.style.display = 'block'; // Ensure the preview is visible
    document.getElementById('file-upload-button').value = ''; // Clear the file input

    // Optionally: You can log the default image path or update the UI to show that it was reset
    console.log("Image reset to:", defaultImagePath);
}

function handleImageError(img) {
    img.src = defaultImagePath; // Set to your default image path
    img.alt = 'Default Cover Image'; // Optional: Update the alt text
}
