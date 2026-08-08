// =============================================
// EDIT CAR RENTAL - AJAX HANDLER
// =============================================

let deletedImages = [];
let mainImageDeleted = false;

// =============================================
// DELETE IMAGE FUNCTIONS - GLOBAL SCOPE
// =============================================

// Delete Main Image
window.deleteMainImage = function () {
    // Remove the image element
    const imgWrapper = document.querySelector('.current-image-item');
    if (imgWrapper) {
        imgWrapper.remove();
    }
    mainImageDeleted = true;
    document.getElementById('deleteMainImage').value = '1';
};

// Delete Additional Image
window.deleteAdditionalImage = function (index, imagePath) {
    // Remove the image element
    const imgItem = document.querySelector(`.current-image-item[data-index="${index}"]`);
    if (imgItem) {
        imgItem.remove();
    }

    // Add to deleted images list
    deletedImages.push(imagePath);
    document.getElementById('deletedImages').value = JSON.stringify(deletedImages);

    // Check if no images left
    const container = document.getElementById('currentAdditionalImages');
    if (container && container.children.length === 0) {
        // Hide the container or show message
        container.innerHTML = '<div class="text-muted small">No additional images</div>';
    }
};

document.addEventListener('DOMContentLoaded', function () {
    // =============================================
    // IMAGE UPLOAD HANDLING
    // =============================================

    // Main Image Upload
    const mainImageBox = document.getElementById('mainImageBox');
    const mainImageInput = document.getElementById('mainImage');
    const mainImagePreview = document.getElementById('mainImagePreview');

    if (mainImageBox) {
        mainImageBox.addEventListener('click', function () {
            mainImageInput.click();
        });
    }

    if (mainImageInput) {
        mainImageInput.addEventListener('change', function (e) {
            handleImageUpload(e, 'mainImagePreview', true);
        });
    }

    // Additional Images Upload
    const additionalImagesBox = document.getElementById('additionalImagesBox');
    const additionalImagesInput = document.getElementById('additionalImages');
    const additionalImagesPreview = document.getElementById('additionalImagesPreview');

    if (additionalImagesBox) {
        additionalImagesBox.addEventListener('click', function () {
            additionalImagesInput.click();
        });
    }

    if (additionalImagesInput) {
        additionalImagesInput.addEventListener('change', function (e) {
            handleImageUpload(e, 'additionalImagesPreview', false);
        });
    }

    function handleImageUpload(event, previewId, isSingle) {
        const files = event.target.files;
        const previewContainer = document.getElementById(previewId);

        previewContainer.innerHTML = '';

        if (isSingle) {
            const file = files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const div = createImagePreviewItem(e.target.result, file.name, isSingle);
                    previewContainer.appendChild(div);
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.innerHTML = '<div class="image-preview-empty">No new image selected</div>';
            }
        } else {
            if (files.length === 0) {
                previewContainer.innerHTML = '<div class="image-preview-empty">No new images selected</div>';
                return;
            }

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                reader.onload = function (e) {
                    const div = createImagePreviewItem(e.target.result, file.name, isSingle);
                    previewContainer.appendChild(div);
                };
                reader.readAsDataURL(file);
            }
        }
    }

    function createImagePreviewItem(src, fileName, isSingle) {
        const div = document.createElement('div');
        div.className = 'image-preview-item';

        const img = document.createElement('img');
        img.src = src;
        img.alt = fileName;
        div.appendChild(img);

        if (!isSingle) {
            const removeBtn = document.createElement('button');
            removeBtn.className = 'remove-image';
            removeBtn.innerHTML = '<i class="bi bi-x"></i>';
            removeBtn.onclick = function (e) {
                e.stopPropagation();
                div.remove();
                const parent = div.parentElement;
                if (parent && parent.children.length === 0) {
                    parent.innerHTML = '<div class="image-preview-empty">No new images selected</div>';
                }
            };
            div.appendChild(removeBtn);
        }

        return div;
    }

    // =============================================
    // FORM SUBMISSION WITH AJAX
    // =============================================

    const editForm = document.getElementById('editCarRentalForm');
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            e.preventDefault();

            // Validate required fields
            const carName = document.getElementById('carName').value.trim();
            const perDayAmount = document.getElementById('perDayAmount').value.trim();
            const perKmCharge = document.getElementById('perKmCharge').value.trim();
            const seatingCapacity = document.getElementById('seatingCapacity').value.trim();

            if (!carName) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please enter car name'
                });
                document.getElementById('carName').focus();
                return;
            }

            if (!perDayAmount || isNaN(perDayAmount) || parseFloat(perDayAmount) <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please enter valid per day amount'
                });
                document.getElementById('perDayAmount').focus();
                return;
            }

            if (!perKmCharge || isNaN(perKmCharge) || parseFloat(perKmCharge) <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please enter valid per KM charge'
                });
                document.getElementById('perKmCharge').focus();
                return;
            }

            if (!seatingCapacity || isNaN(seatingCapacity) || parseInt(seatingCapacity) <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please enter valid seating capacity'
                });
                document.getElementById('seatingCapacity').focus();
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
            const formData = new FormData();
            formData.append('id', document.getElementById('carId').value);
            formData.append('car_name', carName);
            formData.append('car_model', document.getElementById('carModel').value.trim());
            formData.append('car_brand', document.getElementById('carBrand').value.trim());
            formData.append('car_type', document.getElementById('carType').value);
            formData.append('per_day_amount', perDayAmount);
            formData.append('per_km_charge', perKmCharge);
            formData.append('fuel_type', document.getElementById('fuelType').value);
            formData.append('transmission', document.getElementById('transmission').value);
            formData.append('seating_capacity', seatingCapacity);
            formData.append('ac_available', document.getElementById('acAvailable').value);
            formData.append('status', document.getElementById('status').value);
            formData.append('description', document.getElementById('description').value.trim());

            // Append deleted images
            formData.append('deleted_images', JSON.stringify(deletedImages));
            formData.append('delete_main_image', mainImageDeleted ? '1' : '0');

            // Append main image if selected
            const mainImage = document.getElementById('mainImage').files[0];
            if (mainImage) {
                formData.append('main_image', mainImage);
            }

            // Append additional images
            const additionalFiles = document.getElementById('additionalImages').files;
            for (let i = 0; i < additionalFiles.length; i++) {
                formData.append('additional_images[]', additionalFiles[i]);
            }

            // AJAX request
            fetch('ajax/edit-car-rental.php', {
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
                            title: 'Updated!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = 'car-rentals.php';
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