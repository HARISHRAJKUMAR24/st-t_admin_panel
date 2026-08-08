// =============================================
// SETTINGS GENERAL - JAVASCRIPT
// =============================================

// =============================================
// IMAGE UPLOAD HANDLING
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
                            preview.innerHTML = '<div class="image-preview-empty">No new image</div>';
                        }
                    };
                    div.appendChild(removeBtn);
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '<div class="image-preview-empty">No new image</div>';
            }
        });
    }
}

// Setup all image uploads
document.addEventListener('DOMContentLoaded', function() {
    setupImageUpload('websiteLogo', 'websiteLogoPreview', 'websiteLogoBox');
    setupImageUpload('favicon', 'faviconPreview', 'faviconBox');
    setupImageUpload('panelLogo', 'panelLogoPreview', 'panelLogoBox');
});

// =============================================
// DELETE LOGO FUNCTION
// =============================================

window.deleteLogo = function(type) {
    Swal.fire({
        title: 'Remove Logo?',
        text: 'This action will delete the logo from the server',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, remove it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('delete_type', type);

            fetch('ajax/delete-logo.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Removed!',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred. Please try again.'
                    });
                });
        }
    });
};

// =============================================
// SINGLE FORM SUBMISSION - SAVE ALL SETTINGS
// =============================================

document.addEventListener('DOMContentLoaded', function() {
    const settingsForm = document.getElementById('settingsForm');

    if (settingsForm) {
        settingsForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate required fields
            const name = document.getElementById('settingsName').value.trim();
            const email = document.getElementById('settingsEmail').value.trim();
            const timezone = document.getElementById('timezone').value;

            if (!name || !email) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Name and email are required'
                });
                document.getElementById('settingsName').focus();
                return;
            }

            if (!timezone) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please select a timezone'
                });
                document.getElementById('timezone').focus();
                return;
            }

            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            submitBtn.disabled = true;
            submitText.style.display = 'none';
            submitSpinner.style.display = 'inline-block';

            // Prepare form data
            const formData = new FormData(this);
            formData.append('name', name);
            formData.append('email', email);
            formData.append('phone', document.getElementById('settingsPhone').value.trim());
            formData.append('timezone', timezone);

            // AJAX request
            fetch('ajax/update-general-settings.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    submitBtn.disabled = false;
                    submitText.style.display = 'inline';
                    submitSpinner.style.display = 'none';

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
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
                            text: data.message
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
                        text: 'An error occurred. Please try again.'
                    });
                });
        });
    }
});