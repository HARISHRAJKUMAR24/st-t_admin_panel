<?php
require_once 'config/config.php';
require_once 'config/function.php';
requireLogin();

// Verify token
if (!verifyToken($pdo)) {
    header("Location: " . APP_URL . "login.php");
    exit();
}

// Get car ID
$carId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($carId <= 0) {
    header("Location: car-rentals.php");
    exit();
}

// Fetch car data
$stmt = $pdo->prepare("SELECT * FROM car_rentals WHERE id = ?");
$stmt->execute([$carId]);
$car = $stmt->fetch();

if (!$car) {
    header("Location: car-rentals.php");
    exit();
}

// Get current user
$currentUser = getCurrentUser($pdo);
$additionalImages = json_decode($car['additional_images'], true) ?: [];

// Convert car_type string to array for badge display - FIXED
$carTypesArray = [];
if (!empty($car['car_type'])) {
    // Check if it's stored as JSON array
    if (strpos($car['car_type'], '[') === 0 || strpos($car['car_type'], '{') === 0) {
        // Try to decode as JSON
        $decoded = json_decode($car['car_type'], true);
        if (is_array($decoded) && !empty($decoded)) {
            // Filter out empty values and convert to strings
            $carTypesArray = array_filter(array_map('trim', $decoded), function ($val) {
                return $val !== '' && $val !== null;
            });
        } else {
            // If it's a single value in JSON, handle it
            $carTypesArray = [trim($car['car_type'])];
        }
    } else {
        // If it's a comma-separated string
        $carTypesArray = array_filter(array_map('trim', explode(',', $car['car_type'])), function ($val) {
            return $val !== '';
        });
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>Edit Car Rental · Tour Admin</title>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .form-section {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(4px);
            border-radius: 32px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 24px rgba(0, 20, 30, 0.04);
        }

        .form-section .section-title {
            font-weight: 600;
            color: #123b4f;
            border-bottom: 2px solid #ffd966;
            padding-bottom: 0.8rem;
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            color: #123b4f;
            font-size: 0.85rem;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            padding: 0.6rem 0.9rem;
            border: 2px solid #e8edf3;
            background: rgba(255, 255, 255, 0.6);
            font-weight: 500;
            transition: all 0.2s;
            font-size: 0.9rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #ffd966;
            box-shadow: 0 0 0 4px rgba(255, 215, 100, 0.15);
            background: white;
        }

        .image-upload-wrapper {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .image-upload-box {
            flex: 0 0 200px;
            border: 2px dashed #e8edf3;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: rgba(255, 255, 255, 0.4);
            min-height: 150px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .image-upload-box:hover {
            border-color: #ffd966;
            background: rgba(255, 215, 100, 0.05);
        }

        .image-upload-box i {
            font-size: 2.5rem;
            color: #9bb2c5;
        }

        .image-upload-box p {
            color: #5f7d92;
            margin-top: 0.3rem;
            font-size: 0.8rem;
            margin-bottom: 0;
        }

        .image-preview-wrapper {
            flex: 1;
            min-width: 200px;
        }

        .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .image-preview-item {
            position: relative;
            width: 80px;
            height: 80px;
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid #e8edf3;
        }

        .image-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-preview-item .remove-image {
            position: absolute;
            top: 3px;
            right: 3px;
            background: rgba(231, 76, 94, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            transition: 0.2s;
        }

        .image-preview-item .remove-image:hover {
            transform: scale(1.1);
        }

        .image-preview-empty {
            color: #9bb2c5;
            font-size: 0.85rem;
            padding: 0.5rem 0;
        }

        .current-image-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 0.8rem;
        }

        .current-image-item {
            position: relative;
            width: 100px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #e8edf3;
        }

        .current-image-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .current-image-item .delete-image-btn {
            position: absolute;
            top: 3px;
            right: 3px;
            background: rgba(231, 76, 94, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            transition: 0.2s;
        }

        .current-image-item .delete-image-btn:hover {
            transform: scale(1.1);
        }

        .current-image-label {
            font-size: 0.75rem;
            color: #5f7d92;
            margin-bottom: 0.3rem;
        }

        /* Badge Input Styles */
        .badge-input-wrapper {
            background: rgba(255, 255, 255, 0.4);
            border: 2px solid #e8edf3;
            border-radius: 12px;
            padding: 14px;
            transition: all 0.3s ease;
        }

        .badge-input-wrapper:focus-within {
            border-color: #ffd966;
            box-shadow: 0 0 0 4px rgba(255, 215, 100, 0.08);
        }

        .badge-input-row {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .badge-input-row .form-control {
            flex: 1;
            min-width: 120px;
        }

        .badges-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
            min-height: 44px;
            padding: 8px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            border: 1px dashed #e8edf3;
        }

        .badge-item {
            display: inline-flex;
            align-items: center;
            background: rgba(18, 59, 79, 0.08);
            color: #123b4f;
            padding: 4px 12px 4px 10px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 500;
            gap: 6px;
            transition: all 0.2s ease;
            animation: badgeIn 0.3s ease;
            border: 1px solid rgba(18, 59, 79, 0.06);
        }

        .badge-item:hover {
            background: rgba(18, 59, 79, 0.12);
            transform: translateY(-1px);
        }

        .badge-item .badge-name {
            margin: 0 2px;
        }

        .badge-item .remove-badge {
            cursor: pointer;
            color: #dc3545;
            font-weight: 700;
            font-size: 1rem;
            line-height: 1;
            padding: 0 2px;
            transition: all 0.2s ease;
        }

        .badge-item .remove-badge:hover {
            transform: scale(1.3);
            color: #c82333;
        }

        .empty-badges {
            color: #9bb2c5;
            font-size: 0.8rem;
            padding: 6px 0;
            width: 100%;
            text-align: center;
        }

        .btn-sm-primary {
            background: #ffd966;
            color: #123b4f;
            border: none;
            border-radius: 8px;
            padding: 0.4rem 1.2rem;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.3s ease;
            cursor: pointer;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-sm-primary:hover {
            background: #f5c842;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 215, 100, 0.3);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e8edf3;
        }

        .btn-submit {
            background: linear-gradient(145deg, #0b2a3e 0%, #123b4f 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 0.7rem 2.5rem;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 8px 20px rgba(11, 42, 62, 0.15);
            min-width: 160px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(11, 42, 62, 0.25);
            background: linear-gradient(145deg, #123b4f 0%, #0b2a3e 100%);
            color: #ffd966;
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
        }

        .btn-cancel {
            background: #e8edf3;
            color: #5f7d92;
            border: none;
            border-radius: 12px;
            padding: 0.7rem 1.8rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-cancel:hover {
            background: #d5dce6;
            color: #123b4f;
        }

        @keyframes badgeIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @media (max-width: 768px) {
            .form-section {
                padding: 1rem;
            }

            .image-upload-box {
                flex: 0 0 100%;
                min-height: 120px;
            }

            .image-preview-item {
                width: 70px;
                height: 70px;
            }

            .form-actions {
                flex-direction: column-reverse;
            }

            .form-actions .btn-submit,
            .form-actions .btn-cancel {
                width: 100%;
                text-align: center;
            }

            .current-image-item {
                width: 80px;
                height: 70px;
            }

            .badge-input-row {
                flex-direction: column;
                align-items: stretch;
            }

            .badge-input-row .form-control {
                min-width: unset;
            }

            .badge-input-row .btn-sm-primary {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <!-- overlay mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ========== SIDE NAV ========== -->
    <?php include_once 'includes/nav.php'; ?>

    <!-- ========== MAIN ========== -->
    <div class="main-wrapper">

        <!-- TOP BAR compact -->
        <div class="top-bar">
            <button class="burger-btn" id="burgerBtn" aria-label="Toggle navigation">
                <i class="bi bi-list"></i>
            </button>
            <div class="greeting-center">
                Welcome back, <strong><?= htmlspecialchars($currentUser['name'] ?? 'Admin') ?></strong>
                <small>Edit car rental</small>
            </div>
        </div>

        <!-- ====== EDIT CAR RENTAL FORM ====== -->
        <div class="form-section">
            <h5 class="section-title"><i class="bi bi-pencil-square me-2" style="color:#f5b342;"></i>Edit Car Rental</h5>

            <form id="editCarRentalForm" enctype="multipart/form-data">
                <input type="hidden" id="carId" value="<?= $car['id'] ?>">
                <input type="hidden" id="deletedImages" value="">
                <input type="hidden" id="deleteMainImage" value="0">
                <input type="hidden" id="carTypesHidden" value='<?= htmlspecialchars(json_encode(array_values($carTypesArray)), ENT_QUOTES, 'UTF-8') ?>'>

                <!-- Row 1: Car Name, Model, Brand (3 columns) -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Car Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="carName" value="<?= htmlspecialchars($car['car_name']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Car Model</label>
                        <input type="text" class="form-control" id="carModel" value="<?= htmlspecialchars($car['car_model']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Brand</label>
                        <input type="text" class="form-control" id="carBrand" value="<?= htmlspecialchars($car['car_brand']) ?>">
                    </div>
                </div>

                <!-- Row 2: Car Type with Badge Style -->
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label">Car Type <span class="text-danger">*</span></label>
                        <div class="badge-input-wrapper">
                            <div class="badge-input-row">
                                <input type="text" class="form-control" id="carTypeInput" placeholder="Enter car type (e.g., SUV, Sedan, Luxury)">
                                <button type="button" class="btn-sm-primary" id="addCarTypeBtn">
                                    <i class="bi bi-plus-circle"></i> Add
                                </button>
                            </div>
                            <div class="badges-container" id="carTypesList">
                                <div class="empty-badges">No car types added</div>
                            </div>
                            <input type="hidden" id="carTypes" name="car_types" value="">
                            <small class="text-muted" style="font-size:0.7rem;">Type a car type and click Add to include it</small>
                        </div>
                    </div>
                </div>

                <!-- Row 3: Per Day, Per KM (2 columns) -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Per Day Amount <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="perDayAmount" value="<?= $car['per_day_amount'] ?>" step="0.01" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Per KM Charge <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="perKmCharge" value="<?= $car['per_km_charge'] ?>" step="0.01" required>
                    </div>
                </div>

                <!-- Row 4: Fuel, Transmission, Seating (3 columns) -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Fuel Type</label>
                        <select class="form-select" id="fuelType">
                            <option value="">Select Fuel</option>
                            <option value="Petrol" <?= $car['fuel_type'] == 'Petrol' ? 'selected' : '' ?>>Petrol</option>
                            <option value="Diesel" <?= $car['fuel_type'] == 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                            <option value="Electric" <?= $car['fuel_type'] == 'Electric' ? 'selected' : '' ?>>Electric</option>
                            <option value="Hybrid" <?= $car['fuel_type'] == 'Hybrid' ? 'selected' : '' ?>>Hybrid</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Transmission</label>
                        <select class="form-select" id="transmission">
                            <option value="">Select Transmission</option>
                            <option value="Automatic" <?= $car['transmission'] == 'Automatic' ? 'selected' : '' ?>>Automatic</option>
                            <option value="Manual" <?= $car['transmission'] == 'Manual' ? 'selected' : '' ?>>Manual</option>
                            <option value="CVT" <?= $car['transmission'] == 'CVT' ? 'selected' : '' ?>>CVT</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Seating Capacity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="seatingCapacity" value="<?= $car['seating_capacity'] ?: 4 ?>" min="1" required>
                    </div>
                </div>

                <!-- Row 5: AC, Status (2 columns) -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">AC Available</label>
                        <select class="form-select" id="acAvailable">
                            <option value="1" <?= $car['ac_available'] == 1 ? 'selected' : '' ?>>Yes</option>
                            <option value="0" <?= $car['ac_available'] == 0 ? 'selected' : '' ?>>No</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="status">
                            <option value="available" <?= $car['status'] == 'available' ? 'selected' : '' ?>>Available</option>
                            <option value="booked" <?= $car['status'] == 'booked' ? 'selected' : '' ?>>Booked</option>
                            <option value="maintenance" <?= $car['status'] == 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                        </select>
                    </div>
                </div>

                <!-- Row 6: Description (full width) -->
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="description" rows="2"><?= htmlspecialchars($car['description']) ?></textarea>
                    </div>
                </div>

                <!-- Row 7: Main Image -->
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label">Main Image</label>
                        <?php if ($car['car_image']): ?>
                            <div class="mb-2">
                                <div class="current-image-label">Current Image:</div>
                                <div class="current-image-wrapper">
                                    <div class="current-image-item">
                                        <img src="<?= APP_URL . $car['car_image'] ?>" alt="Current main image">
                                        <button type="button" class="delete-image-btn" onclick="deleteMainImage()">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="image-upload-wrapper">
                            <div class="image-upload-box" id="mainImageBox">
                                <i class="bi bi-cloud-upload"></i>
                                <p>Change image<br><small>JPG, PNG, WebP</small></p>
                                <input type="file" id="mainImage" accept="image/*" style="display:none;">
                            </div>
                            <div class="image-preview-wrapper">
                                <div id="mainImagePreview" class="image-preview">
                                    <div class="image-preview-empty">No new image selected</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 8: Additional Images -->
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label">Additional Images</label>
                        <?php if (!empty($additionalImages)): ?>
                            <div class="mb-2">
                                <div class="current-image-label">Current Additional Images:</div>
                                <div class="current-image-wrapper" id="currentAdditionalImages">
                                    <?php foreach ($additionalImages as $index => $img): ?>
                                        <div class="current-image-item" data-index="<?= $index ?>">
                                            <img src="<?= APP_URL . $img ?>" alt="Additional image">
                                            <button type="button" class="delete-image-btn" onclick="deleteAdditionalImage(<?= $index ?>, '<?= $img ?>')">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="image-upload-wrapper">
                            <div class="image-upload-box" id="additionalImagesBox">
                                <i class="bi bi-images"></i>
                                <p>Add more images<br><small>Multiple images</small></p>
                                <input type="file" id="additionalImages" accept="image/*" multiple style="display:none;">
                            </div>
                            <div class="image-preview-wrapper">
                                <div id="additionalImagesPreview" class="image-preview">
                                    <div class="image-preview-empty">No new images selected</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buttons - Bottom Right -->
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="window.location.href='car-rentals.php'">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <span id="submitText"><i class="bi bi-floppy me-2"></i>Update Car</span>
                        <span id="submitSpinner" class="spinner-border spinner-border-sm" style="display:none;"></span>
                    </button>
                </div>
            </form>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>
    <script src="<?= APP_URL ?>javascript/main.js"></script>
    <script>
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
                        // Filter out empty values
                        carTypes = existingTypes.filter(type => type && type.trim() !== '');
                        renderCarTypes();
                    }
                } catch (e) {
                    console.error('Error parsing car types:', e);
                    // If JSON parsing fails, try to handle as comma-separated string
                    const rawValue = carTypesHidden.value;
                    if (rawValue && rawValue.includes(',')) {
                        carTypes = rawValue.split(',').map(t => t.trim()).filter(t => t);
                        renderCarTypes();
                    }
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
    </script>
</body>

</html>