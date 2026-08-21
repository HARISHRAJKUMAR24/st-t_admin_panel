// =============================================
// ADD TESTIMONIAL - JAVASCRIPT
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
// FORM SUBMISSION
// =============================================

document.addEventListener('DOMContentLoaded', function() {
    setupImageUpload('logo', 'logoPreview', 'logoBox');

    const form = document.getElementById('testimonialForm');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const name = document.getElementById('name').value.trim();
            const testimonial = document.getElementById('testimonial').value.trim();

            if (!name) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Name is required',
                    confirmButtonColor: '#123b4f'
                });
                document.getElementById('name').focus();
                return;
            }

            if (!testimonial) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Testimonial text is required',
                    confirmButtonColor: '#123b4f'
                });
                document.getElementById('testimonial').focus();
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            submitBtn.disabled = true;
            submitText.style.display = 'none';
            submitSpinner.style.display = 'inline-block';

            const formData = new FormData();
            formData.append('name', name);
            formData.append('testimonial', testimonial);
            formData.append('status', document.getElementById('status').value);

            const logo = document.getElementById('logo').files[0];
            if (logo) {
                formData.append('logo', logo);
            }

            fetch('ajax/add-testimonial.php', {
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
                            title: 'Added!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = 'testimonials.php';
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