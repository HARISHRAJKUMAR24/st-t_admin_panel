// =============================================
// EDIT CAR RENTAL - AJAX HANDLER
// =============================================

let carTypes = [];
let deletedImages = [];
let mainImageDeleted = false;

document.addEventListener('DOMContentLoaded', function() {
    console.log('Edit Car Rental JS loaded successfully!');

    // =============================================
    // CAR TYPES - Load existing types
    // =============================================
    
    // Load existing car types from hidden input
    const carTypesHidden = document.getElementById('carTypesHidden');
    if (carTypesHidden && carTypesHidden.value) {
        try {
            const existingTypes = JSON.parse(carTypesHidden.value);
            if (Array.isArray(existingTypes) && existingTypes.length > 0) {
                carTypes = existingTypes;
                renderCarTypes();
            }
        } catch (e) {
            console.error('Error parsing car types:', e);
        }
    }

    // =============================================
    // CAR TYPES - Badge Style (Input field)
    // =============================================

    const addCarTypeBtn = document.getElementById('addCarTypeBtn');
    const carTypeInput = document.getElementById('carTypeInput');

    // Add car type on button click
    if (addCarTypeBtn) {
        addCarTypeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            addCarType();
        });
    }

    // Add car type on Enter key
    if (carTypeInput) {
        carTypeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addCarType();
            }
        });
    }

    function addCarType() {
        const input = document.getElementById('carTypeInput');
        const type = input.value.trim();

        if (!type) {
            Swal.fire({
                icon: 'warning',
                title: 'Car Type Required',
                text: 'Please enter a car type',
                confirmButtonColor: '#0b2a3e'
            });
            input.focus();
            return;
        }

        // Check if car type already exists
        const existing = carTypes.find(c => c.toLowerCase() === type.toLowerCase());
        if (existing) {
            Swal.fire({
                icon: 'warning',
                title: 'Duplicate Car Type',
                text: `"${type}" already exists. Please enter a different type.`,
                confirmButtonColor: '#0b2a3e'
            });
            input.value = '';
            input.focus();
            return;
        }

        carTypes.push(type);
        renderCarTypes();

        // Clear input and focus
        input.value = '';
        input.focus();
    }

    window.addCarType = addCarType;

    window.removeCarType = function(index) {
        carTypes.splice(index, 1);
        renderCarTypes();
    }

    function renderCarTypes() {
        const container = document.getElementById('carTypesList');
        container.innerHTML = '';

        if (carTypes.length === 0) {
            container.innerHTML = '<div class="empty-badges">No car types added</div>';
            return;
        }

        carTypes.forEach((type, index) => {
            const badge = document.createElement('span');
            badge.className = 'badge-item';
            badge.innerHTML = `
                <span class="badge-name">${escapeHtml(type)}</span>
                <span class="remove-badge" onclick="removeCarType(${index})">&times;</span>
            `;
            container.appendChild(badge);
        });

        // Update hidden input
        document.getElementById('carTypes').value = JSON.stringify(carTypes);
    }

    // =============================================
    // IMAGE UPLOAD HANDLING
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
                    // Hide empty message
                    previewContainer.querySelector('.image-preview-empty')?.remove();
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
                reader.onload = function(e) {
                    const div = createImagePreviewItem(e.target.result, file.name, isSingle);
                    previewContainer.appendChild(div);
                    previewContainer.querySelector('.image-preview-empty')?.remove();
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
    // DELETE IMAGE FUNCTIONS
    // =============================================

    window.deleteMainImage = function() {
        Swal.fire({
            title: 'Delete Main Image?',
            text: "This will remove the main image. You can upload a new one.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const imgWrapper = document.querySelector('.current-image-item');
                if (imgWrapper) {
                    imgWrapper.remove();
                }
                mainImageDeleted = true;
                document.getElementById('deleteMainImage').value = '1';
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'Main image will be removed when you save.',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    };

    window.deleteAdditionalImage = function(index, imagePath) {
        Swal.fire({
            title: 'Delete Image?',
            text: "This will remove this additional image.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const imgItem = document.querySelector(`.current-image-item[data-index="${index}"]`);
                if (imgItem) {
                    imgItem.remove();
                }

                deletedImages.push(imagePath);
                document.getElementById('deletedImages').value = JSON.stringify(deletedImages);

                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: 'Image will be removed when you save.',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    };

    // =============================================
    // SWEETALERT2 - CENTERED ALERT
    // =============================================

    function showAlert(message, type = 'success') {
        const icons = {
            success: 'success',
            error: 'error',
            warning: 'warning',
            info: 'info'
        };

        Swal.fire({
            icon: icons[type] || 'info',
            title: type === 'success' ? 'Success!' : type === 'error' ? 'Error!' : type === 'warning' ? 'Warning!' : 'Info',
            text: message,
            confirmButtonColor: '#0b2a3e',
            confirmButtonText: 'OK',
            timer: type === 'success' ? 2000 : 5000,
            timerProgressBar: type === 'success' ? true : false,
            position: 'center'
        });
    }

    // =============================================
    // UTILITY FUNCTIONS
    // =============================================

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // =============================================
    // FORM SUBMISSION WITH AJAX
    // =============================================

    const editForm = document.getElementById('editCarRentalForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate required fields
            const carName = document.getElementById('carName').value.trim();
            const perDayAmount = document.getElementById('perDayAmount').value.trim();
            const perKmCharge = document.getElementById('perKmCharge').value.trim();
            const seatingCapacity = document.getElementById('seatingCapacity').value.trim();

            // Validate car types
            if (carTypes.length === 0) {
                showAlert('Please add at least one car type', 'warning');
                document.getElementById('carTypeInput').focus();
                return;
            }

            if (!carName) {
                showAlert('Please enter car name', 'warning');
                document.getElementById('carName').focus();
                return;
            }

            if (!perDayAmount || isNaN(perDayAmount) || parseFloat(perDayAmount) <= 0) {
                showAlert('Please enter valid per day amount', 'warning');
                document.getElementById('perDayAmount').focus();
                return;
            }

            if (!perKmCharge || isNaN(perKmCharge) || parseFloat(perKmCharge) <= 0) {
                showAlert('Please enter valid per KM charge', 'warning');
                document.getElementById('perKmCharge').focus();
                return;
            }

            if (!seatingCapacity || isNaN(seatingCapacity) || parseInt(seatingCapacity) <= 0) {
                showAlert('Please enter valid seating capacity', 'warning');
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
            
            // Send car types as JSON
            formData.append('car_type', JSON.stringify(carTypes));
            
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
                .then(response => response.json())
                .then(data => {
                    submitBtn.disabled = false;
                    submitText.style.display = 'inline';
                    submitSpinner.style.display = 'none';

                    if (data.success) {
                        showAlert(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = 'car-rentals.php';
                        }, 1500);
                    } else {
                        showAlert(data.message, 'error');
                    }
                })
                .catch(error => {
                    submitBtn.disabled = false;
                    submitText.style.display = 'inline';
                    submitSpinner.style.display = 'none';
                    console.error('Error:', error);
                    showAlert('An error occurred. Please try again.', 'error');
                });
        });
    }
});