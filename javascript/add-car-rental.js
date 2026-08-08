// =============================================
// CAR RENTAL ADD - AJAX HANDLER
// =============================================

document.addEventListener('DOMContentLoaded', function() {
    // =============================================
    // IMAGE UPLOAD HANDLING - Horizontal layout
    // =============================================

    // Main Image Upload
    const mainImageBox = document.getElementById('mainImageBox');
    const mainImageInput = document.getElementById('mainImage');
    const mainImagePreview = document.getElementById('mainImagePreview');

    if (mainImageBox) {
        mainImageBox.addEventListener('click', function() {
            mainImageInput.click();
        });
    }

    if (mainImageInput) {
        mainImageInput.addEventListener('change', function(e) {
            handleImageUpload(e, 'mainImagePreview', true);
        });
    }

    // Additional Images Upload
    const additionalImagesBox = document.getElementById('additionalImagesBox');
    const additionalImagesInput = document.getElementById('additionalImages');
    const additionalImagesPreview = document.getElementById('additionalImagesPreview');

    if (additionalImagesBox) {
        additionalImagesBox.addEventListener('click', function() {
            additionalImagesInput.click();
        });
    }

    if (additionalImagesInput) {
        additionalImagesInput.addEventListener('change', function(e) {
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
                reader.onload = function(e) {
                    const div = createImagePreviewItem(e.target.result, file.name, isSingle);
                    previewContainer.appendChild(div);
                };
                reader.readAsDataURL(file);
            }
        } else {
            // Multiple images - displayed left to right
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                reader.onload = function(e) {
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
            removeBtn.onclick = function(e) {
                e.stopPropagation();
                div.remove();
            };
            div.appendChild(removeBtn);
        }

        return div;
    }

    // =============================================
    // TOAST NOTIFICATIONS WITH SWEETALERT2
    // =============================================

    function showToast(message, type = 'success') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        const icons = {
            success: 'success',
            error: 'error',
            info: 'info'
        };

        Toast.fire({
            icon: icons[type] || 'info',
            title: message,
            background: type === 'success' ? '#1a7a55' : type === 'error' ? '#721c24' : '#1f5777',
            color: '#ffffff',
            iconColor: '#ffffff',
            customClass: {
                popup: 'swal2-toast-custom'
            }
        });
    }

    // =============================================
    // FORM SUBMISSION WITH AJAX
    // =============================================

    const loginForm = document.getElementById('addCarRentalForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate required fields
            const carName = document.getElementById('carName').value.trim();
            const perDayAmount = document.getElementById('perDayAmount').value.trim();
            const perKmCharge = document.getElementById('perKmCharge').value.trim();
            const seatingCapacity = document.getElementById('seatingCapacity').value.trim();
            const mainImage = document.getElementById('mainImage').files[0];

            if (!carName) {
                showToast('Please enter car name', 'error');
                document.getElementById('carName').focus();
                return;
            }

            if (!perDayAmount || isNaN(perDayAmount) || parseFloat(perDayAmount) <= 0) {
                showToast('Please enter valid per day amount', 'error');
                document.getElementById('perDayAmount').focus();
                return;
            }

            if (!perKmCharge || isNaN(perKmCharge) || parseFloat(perKmCharge) <= 0) {
                showToast('Please enter valid per KM charge', 'error');
                document.getElementById('perKmCharge').focus();
                return;
            }

            if (!seatingCapacity || isNaN(seatingCapacity) || parseInt(seatingCapacity) <= 0) {
                showToast('Please enter valid seating capacity', 'error');
                document.getElementById('seatingCapacity').focus();
                return;
            }

            if (!mainImage) {
                showToast('Please upload a main image', 'error');
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

            // Append main image
            formData.append('main_image', mainImage);

            // Append additional images
            const additionalFiles = document.getElementById('additionalImages').files;
            for (let i = 0; i < additionalFiles.length; i++) {
                formData.append('additional_images[]', additionalFiles[i]);
            }

            // AJAX request
            fetch('ajax/add-car-rental.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Hide loading state
                    submitBtn.disabled = false;
                    submitText.style.display = 'inline';
                    submitSpinner.style.display = 'none';

                    if (data.success) {
                        showToast(data.message, 'success');
                        // Reset form after delay
                        setTimeout(() => {
                            document.getElementById('addCarRentalForm').reset();
                            document.getElementById('mainImagePreview').innerHTML = '';
                            document.getElementById('additionalImagesPreview').innerHTML = '';
                            // Set default seating capacity again
                            document.getElementById('seatingCapacity').value = 4;
                            // Redirect to car rentals list
                            window.location.href = 'car-rentals.php';
                        }, 2000);
                    } else {
                        showToast(data.message, 'error');
                    }
                })
                .catch(error => {
                    submitBtn.disabled = false;
                    submitText.style.display = 'inline';
                    submitSpinner.style.display = 'none';
                    console.error('Error:', error);
                    showToast('An error occurred. Please try again.', 'error');
                });
        });
    }
});