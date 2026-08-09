
// =============================================
// DISCOUNT TYPE CHANGE HANDLER
// =============================================

document.getElementById('discountType').addEventListener('change', function () {
    const label = document.getElementById('discountTypeLabel');
    const symbol = '<?= htmlspecialchars($currencySymbol) ?>';
    if (this.value === 'percentage') {
        label.textContent = 'Enter percentage value (e.g., 20 for 20%)';
    } else {
        label.textContent = 'Enter fixed amount in ' + symbol;
    }
});

// =============================================
// MULTI-SELECT DROPDOWN
// =============================================

let selectedPackages = [];

// Initialize selected packages from hidden input
function initSelectedPackages() {
    const hiddenInput = document.getElementById('selectedPackageIds');
    if (hiddenInput.value) {
        try {
            const ids = JSON.parse(hiddenInput.value);
            selectedPackages = ids.map(id => {
                const checkbox = document.getElementById('pkg_' + id);
                const label = checkbox ? checkbox.nextElementSibling.textContent : '';
                return {
                    id: id,
                    label: label
                };
            });
        } catch (e) {
            selectedPackages = [];
        }
    }
}

function toggleDropdown() {
    const dropdown = document.getElementById('packageDropdown');
    dropdown.classList.toggle('show');
}

// Close dropdown when clicking outside
document.addEventListener('click', function (e) {
    const wrapper = document.querySelector('.multi-select-wrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        document.getElementById('packageDropdown').classList.remove('show');
    }
});

function updateSelectedPackages() {
    const checkboxes = document.querySelectorAll('#packageDropdown input[type="checkbox"]:checked');
    const container = document.getElementById('selectedPackages');
    const hiddenInput = document.getElementById('selectedPackageIds');

    selectedPackages = [];
    container.innerHTML = '';

    checkboxes.forEach((checkbox) => {
        const id = checkbox.value;
        const label = checkbox.nextElementSibling.textContent;
        selectedPackages.push({
            id: id,
            label: label
        });

        const tag = document.createElement('span');
        tag.className = 'selected-tag';
        tag.innerHTML = `
                    ${label}
                    <span class="remove-tag" onclick="removePackage('${id}')">&times;</span>
                `;
        container.appendChild(tag);
    });

    // Update hidden input
    const ids = selectedPackages.map(p => p.id);
    hiddenInput.value = JSON.stringify(ids);

    // Update search input text
    const searchInput = document.getElementById('packageSearch');
    if (selectedPackages.length === 0) {
        searchInput.value = 'Click to select packages...';
    } else {
        searchInput.value = selectedPackages.length + ' package(s) selected';
    }
}

function removePackage(id) {
    const checkbox = document.getElementById('pkg_' + id);
    if (checkbox) {
        checkbox.checked = false;
        updateSelectedPackages();
    }
}

function removePackageByLabel(label) {
    // Find package by label and remove
    const items = document.querySelectorAll('#packageDropdown .option-item');
    items.forEach(item => {
        const labelEl = item.querySelector('.option-label');
        if (labelEl && labelEl.textContent.trim() === label) {
            const checkbox = item.querySelector('input[type="checkbox"]');
            if (checkbox) {
                checkbox.checked = false;
            }
        }
    });
    updateSelectedPackages();
}

// =============================================
// DELETE MAIN IMAGE
// =============================================

function deleteMainImage() {
    const imgWrapper = document.querySelector('.current-image-item');
    if (imgWrapper) {
        imgWrapper.remove();
    }
    document.getElementById('deleteMainImage').value = '1';
}

// =============================================
// IMAGE UPLOAD
// =============================================

document.addEventListener('DOMContentLoaded', function () {
    initSelectedPackages();
    setupImageUpload('mainImage', 'mainImagePreview', 'mainImageBox');
});

function setupImageUpload(inputId, previewId, boxId) {
    const input = document.getElementById(inputId);
    const box = document.getElementById(boxId);
    const preview = document.getElementById(previewId);

    if (box) {
        box.addEventListener('click', function () {
            input.click();
        });
    }

    if (input) {
        input.addEventListener('change', function (e) {
            const files = e.target.files;
            preview.innerHTML = '';

            if (files.length > 0) {
                const file = files[0];
                const reader = new FileReader();
                reader.onload = function (e) {
                    const div = document.createElement('div');
                    div.className = 'image-preview-item';
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = file.name;
                    div.appendChild(img);

                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'remove-image';
                    removeBtn.innerHTML = '<i class="bi bi-x"></i>';
                    removeBtn.onclick = function (e) {
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

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('offerForm');

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Validate required fields
            const title = document.getElementById('offerTitle').value.trim();
            const discountValue = document.getElementById('discountValue').value;
            const selectedPackagesIds = document.getElementById('selectedPackageIds').value;

            if (!title) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Offer title is required',
                    confirmButtonColor: '#123b4f'
                });
                document.getElementById('offerTitle').focus();
                return;
            }

            if (!discountValue || parseFloat(discountValue) <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please enter a valid discount value',
                    confirmButtonColor: '#123b4f'
                });
                document.getElementById('discountValue').focus();
                return;
            }

            // Check percentage max 100%
            const discountType = document.getElementById('discountType').value;
            if (discountType === 'percentage' && parseFloat(discountValue) > 100) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Percentage discount cannot exceed 100%',
                    confirmButtonColor: '#123b4f'
                });
                document.getElementById('discountValue').focus();
                return;
            }

            if (!selectedPackagesIds || JSON.parse(selectedPackagesIds).length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please select at least one tour package',
                    confirmButtonColor: '#123b4f'
                });
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
            formData.append('id', document.getElementById('offerId').value);
            formData.append('offer_code', document.getElementById('offerCode').value);
            formData.append('title', title);
            formData.append('discount_type', document.getElementById('discountType').value);
            formData.append('discount_value', discountValue);
            formData.append('package_ids', document.getElementById('selectedPackageIds').value);
            formData.append('start_date', document.getElementById('startDate').value);
            formData.append('end_date', document.getElementById('endDate').value);
            formData.append('status', document.getElementById('status').value);
            formData.append('description', document.getElementById('description').value.trim());
            formData.append('delete_main_image', document.getElementById('deleteMainImage').value);

            // Main image (if new one selected)
            const mainImage = document.getElementById('mainImage').files[0];
            if (mainImage) {
                formData.append('main_image', mainImage);
            }

            // Submit
            fetch('ajax/edit-offer.php', {
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
                            window.location.href = 'offers.php';
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
