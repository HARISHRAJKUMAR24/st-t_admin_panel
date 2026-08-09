// =============================================
// ADD TOUR PACKAGE - JAVASCRIPT (FIXED)
// =============================================

let features = [];
let members = [];
let featureIconFile = null;
let featureIconPreviewData = null;

// =============================================
// INITIALIZE
// =============================================

document.addEventListener('DOMContentLoaded', function() {
    // Add first day by default
    addDay();

    // Setup image uploads
    setupImageUpload('mainImage', 'mainImagePreview', 'mainImageBox', true);
    setupGalleryUpload('galleryImages', 'galleryImagesPreview', 'galleryImagesBox');
    setupFeatureIconUpload();

    // Add default members
    addMember('Adults', 0);
    addMember('Children', 0);
    addMember('Infants', 0);
});

// =============================================
// MEMBERS (Badge Style)
// =============================================

function addMember(label = '', count = 0) {
    const labelInput = document.getElementById('memberLabel');
    const countInput = document.getElementById('memberCount');

    let memberLabel = label || labelInput.value.trim();
    let memberCount = count || parseInt(countInput.value) || 0;

    if (!memberLabel) {
        Swal.fire({
            icon: 'warning',
            title: 'Member Type Required',
            text: 'Please enter a member type (e.g., Adults, Children)',
            confirmButtonColor: '#123b4f'
        });
        labelInput.focus();
        return;
    }

    // Check if member type already exists
    const existing = members.find(m => m.label.toLowerCase() === memberLabel.toLowerCase());
    if (existing) {
        Swal.fire({
            icon: 'warning',
            title: 'Duplicate Member Type',
            text: `"${memberLabel}" already exists. Please use a different name.`,
            confirmButtonColor: '#123b4f'
        });
        return;
    }

    members.push({
        label: memberLabel,
        count: memberCount
    });

    renderMembers();

    // Clear inputs
    labelInput.value = '';
    countInput.value = '0';
    labelInput.focus();
}

function removeMember(index) {
    members.splice(index, 1);
    renderMembers();
}

function renderMembers() {
    const container = document.getElementById('membersList');
    container.innerHTML = '';

    if (members.length === 0) {
        container.innerHTML = '<div class="empty-badges">No members added yet</div>';
        return;
    }

    members.forEach((member, index) => {
        const badge = document.createElement('span');
        badge.className = 'member-badge';
        badge.innerHTML = `
            <span class="member-label">${escapeHtml(member.label)}</span>
            <span class="member-count">${member.count}</span>
            <span class="remove-badge" onclick="removeMember(${index})">&times;</span>
        `;
        container.appendChild(badge);
    });

    // Update hidden input
    document.getElementById('members').value = JSON.stringify(members);
}

// =============================================
// IMAGE UPLOAD
// =============================================

function setupImageUpload(inputId, previewId, boxId, isSingle) {
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

                    if (!isSingle) {
                        const removeBtn = document.createElement('button');
                        removeBtn.className = 'remove-image';
                        removeBtn.innerHTML = '<i class="bi bi-x"></i>';
                        removeBtn.onclick = function(e) {
                            e.stopPropagation();
                            div.remove();
                            const dt = new DataTransfer();
                            for (let f of input.files) {
                                if (f.name !== file.name) {
                                    dt.items.add(f);
                                }
                            }
                            input.files = dt.files;
                            if (preview.children.length === 0) {
                                preview.innerHTML = '<div class="image-preview-empty">No images selected</div>';
                            }
                        };
                        div.appendChild(removeBtn);
                    }
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '<div class="image-preview-empty">No image selected</div>';
            }
        });
    }
}

function setupGalleryUpload(inputId, previewId, boxId) {
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

            if (files.length === 0) {
                preview.innerHTML = '<div class="image-preview-empty">No images selected</div>';
                return;
            }

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
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
                        const dt = new DataTransfer();
                        for (let f of input.files) {
                            if (f.name !== file.name) {
                                dt.items.add(f);
                            }
                        }
                        input.files = dt.files;
                        if (preview.children.length === 0) {
                            preview.innerHTML = '<div class="image-preview-empty">No images selected</div>';
                        }
                    };
                    div.appendChild(removeBtn);
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

// =============================================
// FEATURE ICON UPLOAD (FIXED - Using Label)
// =============================================

function setupFeatureIconUpload() {
    const input = document.getElementById('featureIcon');
    const box = document.getElementById('featureIconBox');

    console.log('Setting up feature icon upload...');

    if (input) {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            console.log('File selected:', file ? file.name : 'None');

            if (file) {
                // Validate file size (max 1MB)
                if (file.size > 1 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Too Large',
                        text: 'Icon file must be less than 1MB',
                        confirmButtonColor: '#123b4f'
                    });
                    this.value = '';
                    return;
                }

                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml', 'image/x-icon'];
                if (!allowedTypes.includes(file.type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid File Type',
                        text: 'Please upload JPG, PNG, GIF, WebP, SVG, or ICO files only',
                        confirmButtonColor: '#123b4f'
                    });
                    this.value = '';
                    return;
                }

                // Store the file
                featureIconFile = file;

                // Create preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    featureIconPreviewData = e.target.result;
                    const previewDiv = document.getElementById('featureIconPreview');
                    const previewImg = document.getElementById('featureIconPreviewImg');
                    const previewName = document.getElementById('featureIconPreviewName');

                    if (previewImg) {
                        previewImg.src = e.target.result;
                    }
                    if (previewName) {
                        previewName.textContent = file.name;
                    }
                    if (previewDiv) {
                        previewDiv.style.display = 'block';
                    }

                    // Update the upload box
                    const boxElement = document.getElementById('featureIconBox');
                    const labelSpan = document.getElementById('featureIconLabel');
                    const icon = boxElement.querySelector('i');
                    
                    if (boxElement) {
                        boxElement.classList.add('has-file');
                        if (icon) icon.style.color = '#28a745';
                        if (labelSpan) {
                            labelSpan.textContent = '✓ ' + file.name.substring(0, 15) + (file.name.length > 15 ? '...' : '');
                            labelSpan.style.color = '#28a745';
                        }
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Add drag and drop support
    if (box) {
        box.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = '#ffd966';
            this.style.background = 'rgba(255,215,100,0.1)';
        });

        box.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.style.borderColor = '#e8edf3';
            this.style.background = 'rgba(255,255,255,0.4)';
        });

        box.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = '#e8edf3';
            this.style.background = 'rgba(255,255,255,0.4)';

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const input = document.getElementById('featureIcon');
                input.files = files;
                input.dispatchEvent(new Event('change'));
            }
        });
    }
}

function removeFeatureIconPreview() {
    featureIconFile = null;
    featureIconPreviewData = null;
    document.getElementById('featureIcon').value = '';
    document.getElementById('featureIconPreview').style.display = 'none';

    // Reset the upload box
    const boxElement = document.getElementById('featureIconBox');
    const labelSpan = document.getElementById('featureIconLabel');
    const icon = boxElement.querySelector('i');
    
    if (boxElement) {
        boxElement.classList.remove('has-file');
        if (icon) icon.style.color = '#9bb2c5';
        if (labelSpan) {
            labelSpan.textContent = 'Upload Icon';
            labelSpan.style.color = '#5f7d92';
        }
    }
}

// =============================================
// ITINERARY (with Day Title & Description)
// =============================================

function addDay() {
    const container = document.getElementById('itineraryContainer');
    const dayNumber = container.children.length + 1;

    const dayDiv = document.createElement('div');
    dayDiv.className = 'itinerary-day';
    dayDiv.id = 'day-' + dayNumber;
    dayDiv.innerHTML = `
        <div class="day-header">
            <span class="day-label">
                <span class="day-number">${dayNumber}</span>
                Day ${dayNumber}
            </span>
            <button type="button" class="remove-day" onclick="removeDay('${dayDiv.id}')">
                <i class="bi bi-x-circle"></i>
            </button>
        </div>
        <div class="day-title-input">
            <input type="text" class="form-control" id="day_title_${dayNumber}" placeholder="Enter day title (e.g., Arrival & Welcome)" style="font-weight:600;color:#123b4f;">
        </div>
        <textarea class="form-control" id="itinerary_${dayNumber}" rows="2" placeholder="Enter description for Day ${dayNumber}"></textarea>
    `;
    container.appendChild(dayDiv);

    updateDayCount();
}

function removeDay(dayId) {
    const day = document.getElementById(dayId);
    if (day && document.getElementById('itineraryContainer').children.length > 1) {
        day.remove();
        renumberDays();
        updateDayCount();
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Cannot Remove',
            text: 'You need at least one day in the itinerary',
            confirmButtonColor: '#123b4f'
        });
    }
}

function renumberDays() {
    const container = document.getElementById('itineraryContainer');
    const days = container.children;
    for (let i = 0; i < days.length; i++) {
        const day = days[i];
        const dayNumber = i + 1;
        day.id = 'day-' + dayNumber;
        const numberSpan = day.querySelector('.day-number');
        if (numberSpan) {
            numberSpan.textContent = dayNumber;
        }
        const label = day.querySelector('.day-label');
        if (label) {
            label.innerHTML = `
                <span class="day-number">${dayNumber}</span>
                Day ${dayNumber}
            `;
        }
        const titleInput = day.querySelector('.day-title-input input');
        if (titleInput) {
            titleInput.id = 'day_title_' + dayNumber;
            titleInput.placeholder = 'Enter day title (e.g., Arrival & Welcome)';
        }
        const textarea = day.querySelector('textarea');
        if (textarea) {
            textarea.id = 'itinerary_' + dayNumber;
            textarea.placeholder = `Enter description for Day ${dayNumber}`;
        }
    }
}

function updateDayCount() {
    const count = document.getElementById('itineraryContainer').children.length;
    document.getElementById('dayCount').textContent = count + ' Day' + (count > 1 ? 's' : '');
}

// =============================================
// FEATURES (with Icon Upload)
// =============================================

function addFeature() {
    const input = document.getElementById('featureInput');
    const name = input.value.trim();

    if (!name) {
        Swal.fire({
            icon: 'warning',
            title: 'Feature Name Required',
            text: 'Please enter a feature name',
            confirmButtonColor: '#123b4f'
        });
        input.focus();
        return;
    }

    // Check if feature already exists
    const existing = features.find(f => f.name.toLowerCase() === name.toLowerCase());
    if (existing) {
        Swal.fire({
            icon: 'warning',
            title: 'Duplicate Feature',
            text: `"${name}" already exists in the features list`,
            confirmButtonColor: '#123b4f'
        });
        input.focus();
        return;
    }

    // Create feature with icon
    const feature = {
        name: name,
        icon: featureIconFile ? featureIconFile.name : null,
        iconFile: featureIconFile,
        iconPreview: featureIconPreviewData
    };

    features.push(feature);
    renderFeatures();

    // Clear inputs
    input.value = '';
    featureIconFile = null;
    featureIconPreviewData = null;
    document.getElementById('featureIcon').value = '';
    document.getElementById('featureIconPreview').style.display = 'none';

    // Reset upload box
    const boxElement = document.getElementById('featureIconBox');
    const labelSpan = document.getElementById('featureIconLabel');
    const icon = boxElement.querySelector('i');
    
    if (boxElement) {
        boxElement.classList.remove('has-file');
        if (icon) icon.style.color = '#9bb2c5';
        if (labelSpan) {
            labelSpan.textContent = 'Upload Icon';
            labelSpan.style.color = '#5f7d92';
        }
    }

    updateFeatureCount();
}

function removeFeature(index) {
    features.splice(index, 1);
    renderFeatures();
    updateFeatureCount();
}

function renderFeatures() {
    const container = document.getElementById('featuresContainer');
    container.innerHTML = '';

    if (features.length === 0) {
        container.innerHTML = '<div class="empty-badges">No features added yet</div>';
        return;
    }

    features.forEach((feature, index) => {
        const badge = document.createElement('span');
        badge.className = 'badge-item';

        let iconHtml = '';
        if (feature.iconPreview) {
            iconHtml = `<img src="${feature.iconPreview}" class="badge-icon" alt="icon">`;
        } else if (feature.icon && typeof feature.icon === 'string' && feature.icon.startsWith('http')) {
            iconHtml = `<img src="${escapeHtml(feature.icon)}" class="badge-icon" alt="icon">`;
        }

        badge.innerHTML = `
            ${iconHtml}
            <span class="badge-name">${escapeHtml(feature.name)}</span>
            <span class="remove-badge" onclick="removeFeature(${index})">&times;</span>
        `;
        container.appendChild(badge);
    });
}

function updateFeatureCount() {
    const count = features.length;
    document.getElementById('featureCount').textContent = count + ' Feature' + (count > 1 ? 's' : '');
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
// FORM SUBMISSION
// =============================================

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('tourPackageForm');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate required fields
            const packageName = document.getElementById('packageName').value.trim();
            const daysCount = document.getElementById('daysCount').value;
            const price = document.getElementById('price').value;
            const shortDescription = document.getElementById('shortDescription').value.trim();
            const mainImage = document.getElementById('mainImage').files[0];

            if (!packageName) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Package name is required',
                    confirmButtonColor: '#123b4f'
                });
                document.getElementById('packageName').focus();
                return;
            }

            if (!daysCount || daysCount < 1) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please enter valid number of days',
                    confirmButtonColor: '#123b4f'
                });
                document.getElementById('daysCount').focus();
                return;
            }

            if (!price || parseFloat(price) <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please enter a valid price',
                    confirmButtonColor: '#123b4f'
                });
                document.getElementById('price').focus();
                return;
            }

            if (!shortDescription) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Short description is required',
                    confirmButtonColor: '#123b4f'
                });
                document.getElementById('shortDescription').focus();
                return;
            }

            if (!mainImage) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Main image is required',
                    confirmButtonColor: '#123b4f'
                });
                return;
            }

            // Collect itinerary with day titles
            const itinerary = {};
            const dayElements = document.querySelectorAll('#itineraryContainer .itinerary-day');
            dayElements.forEach((dayElement, index) => {
                const dayNumber = index + 1;
                const titleInput = dayElement.querySelector('.day-title-input input');
                const textarea = dayElement.querySelector('textarea');
                const title = titleInput ? titleInput.value.trim() : '';
                const description = textarea ? textarea.value.trim() : '';
                itinerary['day' + dayNumber] = {
                    title: title,
                    description: description
                };
            });

            // Show loading
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');
            submitBtn.disabled = true;
            submitText.style.display = 'none';
            submitSpinner.style.display = 'inline-block';

            // Prepare form data
            const formData = new FormData();
            formData.append('package_name', packageName);
            formData.append('package_type', document.getElementById('packageType').value);
            formData.append('days_count', daysCount);

            const membersData = members.map(m => ({
                label: m.label,
                count: m.count
            }));
            formData.append('members', JSON.stringify(membersData));

            formData.append('price', price);
            formData.append('status', document.getElementById('status').value);
            formData.append('short_description', shortDescription);
            formData.append('description', document.getElementById('description').value.trim());
            formData.append('itinerary', JSON.stringify(itinerary));

            // Features with icons
            const featuresData = features.map(f => ({
                name: f.name,
                icon: f.icon || null
            }));
            formData.append('features', JSON.stringify(featuresData));

            // Images
            formData.append('main_image', mainImage);

            const galleryFiles = document.getElementById('galleryImages').files;
            for (let i = 0; i < galleryFiles.length; i++) {
                formData.append('gallery_images[]', galleryFiles[i]);
            }

            // Feature icons - upload each icon file
            features.forEach((f) => {
                if (f.iconFile) {
                    formData.append('feature_icons[]', f.iconFile);
                    formData.append('feature_icon_names[]', f.name);
                }
            });

            // Submit
            fetch('ajax/add-tour-package.php', {
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
                            title: 'Created!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = 'tour-packages.php';
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