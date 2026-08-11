// =============================================
// SETTINGS WEBSITE - JAVASCRIPT
// =============================================

// =============================================
// IMAGE UPLOAD
// =============================================

function setupImageUpload(inputId, previewId, boxId) {
    const input = document.getElementById(inputId);
    const box = document.getElementById(boxId);
    const preview = document.getElementById(previewId);

    if (box) {
        box.addEventListener('click', function() {
            input.click();
        });
    }

    if (input) {
        input.addEventListener('change', function(e) {
            const files = e.target.files;
            preview.innerHTML = '';

            if (files.length > 0) {
                const file = files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'image-preview-item';
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = file.name;
                    div.appendChild(img);

                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'remove-image';
                    removeBtn.innerHTML = '<i class="bi bi-x"></i>';
                    removeBtn.onclick = function(e) {
                        e.stopPropagation();
                        div.remove();
                        input.value = '';
                        if (preview.children.length === 0) {
                            preview.innerHTML = '<div class="image-preview-empty">No new image selected</div>';
                        }
                    };
                    div.appendChild(removeBtn);
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '<div class="image-preview-empty">No new image selected</div>';
            }
        });
    }
}

// =============================================
// DELETE HERO IMAGE
// =============================================

window.deleteHeroImage = function() {
    const imgWrapper = document.querySelector('.current-image-item');
    if (imgWrapper) {
        imgWrapper.remove();
    }
    document.getElementById('deleteHeroImage').value = '1';
};

// =============================================
// FORM SUBMISSION
// =============================================

document.addEventListener('DOMContentLoaded', function() {
    // Setup image upload
    setupImageUpload('heroImage', 'heroImagePreview', 'heroImageBox');

    const form = document.getElementById('websiteSettingsForm');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const siteTitle = document.getElementById('siteTitle');
            const footerText = document.getElementById('footerText');
            const deleteHeroImage = document.getElementById('deleteHeroImage');
            const heroImage = document.getElementById('heroImage');

            // Validate site title
            if (!siteTitle || !siteTitle.value.trim()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Site title is required',
                    confirmButtonColor: '#123b4f'
                });
                if (siteTitle) siteTitle.focus();
                return;
            }

            // Show loading
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            submitBtn.disabled = true;
            submitText.style.display = 'none';
            submitSpinner.style.display = 'inline-block';

            // Prepare form data
            const formData = new FormData();
            formData.append('site_title', siteTitle.value.trim());
            formData.append('footer_text', footerText ? footerText.value.trim() : '');
            formData.append('delete_hero_image', deleteHeroImage ? deleteHeroImage.value : '0');

            // Append hero image if selected
            if (heroImage && heroImage.files[0]) {
                formData.append('hero_image', heroImage.files[0]);
            }

            // Send AJAX request
            fetch('ajax/update-website-settings.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    submitBtn.disabled = false;
                    submitText.style.display = 'inline';
                    submitSpinner.style.display = 'none';

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message,
                            confirmButtonColor: '#123b4f'
                        });
                    }
                })
                .catch(error => {
                    submitBtn.disabled = false;
                    submitText.style.display = 'inline';
                    submitSpinner.style.display = 'none';
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred. Please try again.',
                        confirmButtonColor: '#123b4f'
                    });
                });
        });
    }
});