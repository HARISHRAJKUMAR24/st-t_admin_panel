// =============================================
// PACKAGE TYPES - JAVASCRIPT
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
                            preview.innerHTML = '<div class="image-preview-empty">No image</div>';
                        }
                    };
                    div.appendChild(removeBtn);
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '<div class="image-preview-empty">No image</div>';
            }
        });
    }
}

// =============================================
// DELETE CURRENT IMAGE
// =============================================

window.deleteCurrentImage = function() {
    const imgWrapper = document.querySelector('.current-image-item');
    if (imgWrapper) {
        imgWrapper.remove();
    }
    document.getElementById('deleteImage').value = '1';
};

// =============================================
// EDIT TYPE
// =============================================

window.editType = function(typeId) {
    window.location.href = 'package-types.php?edit=' + typeId;
};

// =============================================
// CANCEL EDIT
// =============================================

window.cancelEdit = function() {
    window.location.href = 'package-types.php';
};

// =============================================
// DELETE TYPE
// =============================================

window.deleteType = function(id, name) {
    Swal.fire({
        title: 'Delete ' + name + '?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Deleting...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            const formData = new FormData();
            formData.append('id', id);

            fetch('ajax/delete-package-type.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
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
                            text: data.message,
                            confirmButtonColor: '#123b4f'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred. Please try again.',
                        confirmButtonColor: '#123b4f'
                    });
                });
        }
    });
};

// =============================================
// FORM SUBMISSION
// =============================================

document.addEventListener('DOMContentLoaded', function() {
    setupImageUpload('typeImage', 'imagePreview', 'imageBox');

    const form = document.getElementById('packageTypeForm');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const typeName = document.getElementById('typeName');
            const typeImage = document.getElementById('typeImage');
            const editId = document.getElementById('editId');
            const deleteImage = document.getElementById('deleteImage');

            // Check if image is uploaded or delete is requested
            const hasImage = typeImage && typeImage.files && typeImage.files[0];
            const isDelete = deleteImage && deleteImage.value === '1';
            
            if (!hasImage && !isDelete) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Change',
                    text: 'Please upload a new image or click X to remove the current image.',
                    confirmButtonColor: '#123b4f'
                });
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            submitBtn.disabled = true;
            submitText.style.display = 'none';
            submitSpinner.style.display = 'inline-block';

            const formData = new FormData();
            formData.append('id', editId.value);
            formData.append('type_id', document.getElementById('editTypeId').value);
            formData.append('name', typeName ? typeName.value : '');

            if (deleteImage) {
                formData.append('delete_image', deleteImage.value);
            }

            if (typeImage && typeImage.files[0]) {
                formData.append('image', typeImage.files[0]);
            }

            fetch('ajax/update-package-type.php', {
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
                            title: 'Updated!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = 'package-types.php';
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