<?php
require_once 'config/config.php';
require_once 'config/function.php';
requireLogin();

// Verify token
if (!verifyToken($pdo)) {
    header("Location: " . APP_URL . "login.php");
    exit();
}

// Get vehicle ID
$vehicleId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($vehicleId <= 0) {
    header("Location: vehicle.php");
    exit();
}

// Fetch vehicle data
$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
$stmt->execute([$vehicleId]);
$vehicle = $stmt->fetch();

if (!$vehicle) {
    header("Location: vehicle.php");
    exit();
}

// Get current user
$currentUser = getCurrentUser($pdo);
$additionalImages = json_decode($vehicle['additional_images'], true) ?: [];

// Convert vehicle_type string to array for badge display
$vehicleTypesArray = [];
if (!empty($vehicle['vehicle_type'])) {
    // Check if it's stored as JSON array
    if (strpos($vehicle['vehicle_type'], '[') === 0 || strpos($vehicle['vehicle_type'], '{') === 0) {
        $decoded = json_decode($vehicle['vehicle_type'], true);
        if (is_array($decoded) && !empty($decoded)) {
            $vehicleTypesArray = array_filter(array_map('trim', $decoded), function ($val) {
                return $val !== '' && $val !== null;
            });
        } else {
            $vehicleTypesArray = [trim($vehicle['vehicle_type'])];
        }
    } else {
        // If it's a comma-separated string
        $vehicleTypesArray = array_filter(array_map('trim', explode(',', $vehicle['vehicle_type'])), function ($val) {
            return $val !== '';
        });
    }
}

// Determine pricing type
$pricingType = $vehicle['pricing_type'] ?? 'perday';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>Edit Vehicle · Tour Admin</title>
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

        /* Pricing Options */
        .pricing-options {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .pricing-option {
            flex: 1;
            min-width: 200px;
            padding: 15px;
            border: 2px solid #e8edf3;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.4);
            text-align: center;
        }

        .pricing-option:hover {
            border-color: #ffd966;
            background: rgba(255, 215, 100, 0.05);
        }

        .pricing-option.active {
            border-color: #ffd966;
            background: rgba(255, 215, 100, 0.1);
            box-shadow: 0 0 0 4px rgba(255, 215, 100, 0.1);
        }

        .pricing-option i {
            font-size: 1.5rem;
            color: #123b4f;
            display: block;
            margin-bottom: 5px;
        }

        .pricing-option .option-title {
            font-weight: 600;
            color: #123b4f;
            font-size: 0.9rem;
        }

        .pricing-option .option-desc {
            color: #5f7d92;
            font-size: 0.75rem;
        }

        .pricing-fields {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 15px;
            border: 1px solid #e8edf3;
            display: none;
        }

        .pricing-fields.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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

            .pricing-options {
                flex-direction: column;
            }

            .pricing-option {
                min-width: unset;
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
                <small>Edit Vehicle</small>
            </div>
        </div>

        <!-- ====== EDIT VEHICLE FORM ====== -->
        <div class="form-section">
            <h5 class="section-title"><i class="bi bi-pencil-square me-2" style="color:#f5b342;"></i>Edit Vehicle</h5>

            <form id="editVehicleForm" enctype="multipart/form-data">
                <input type="hidden" id="vehicleId" value="<?= $vehicle['id'] ?>">
                <input type="hidden" id="deletedImages" value="">
                <input type="hidden" id="deleteMainImage" value="0">
                <input type="hidden" id="vehicleTypesHidden" value='<?= htmlspecialchars(json_encode(array_values($vehicleTypesArray)), ENT_QUOTES, 'UTF-8') ?>'>
                <input type="hidden" id="pricingTypeHidden" value="<?= $pricingType ?>">

                <!-- Row 1: Vehicle Name, Model, Brand (3 columns) -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Vehicle Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="vehicleName" value="<?= htmlspecialchars($vehicle['vehicle_name']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Vehicle Model</label>
                        <input type="text" class="form-control" id="vehicleModel" value="<?= htmlspecialchars($vehicle['vehicle_model']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Brand</label>
                        <input type="text" class="form-control" id="vehicleBrand" value="<?= htmlspecialchars($vehicle['vehicle_brand']) ?>">
                    </div>
                </div>

                <!-- Row 2: Vehicle Type with Badge Style -->
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label">Vehicle Type <span class="text-danger">*</span></label>
                        <div class="badge-input-wrapper">
                            <div class="badge-input-row">
                                <input type="text" class="form-control" id="vehicleTypeInput" placeholder="Enter vehicle type (e.g., SUV, Sedan, Luxury)">
                                <button type="button" class="btn-sm-primary" id="addVehicleTypeBtn">
                                    <i class="bi bi-plus-circle"></i> Add
                                </button>
                            </div>
                            <div class="badges-container" id="vehicleTypesList">
                                <div class="empty-badges">No vehicle types added</div>
                            </div>
                            <input type="hidden" id="vehicleTypes" name="vehicle_types" value="">
                            <small class="text-muted" style="font-size:0.7rem;">Type a vehicle type and click Add to include it</small>
                        </div>
                    </div>
                </div>

                <!-- Row 3: Pricing Options -->
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label">Pricing Type <span class="text-danger">*</span></label>
                        <div class="pricing-options">
                            <!-- Option 1: Per Day + Per KM -->
                            <div class="pricing-option <?= $pricingType == 'perday' ? 'active' : '' ?>" data-option="perday" onclick="selectPricing('perday')">
                                <i class="bi bi-calendar-day"></i>
                                <div class="option-title">Per Day + Per KM</div>
                                <div class="option-desc">Daily rate + per kilometer charge</div>
                            </div>
                            <!-- Option 2: Package -->
                            <div class="pricing-option <?= $pricingType == 'package' ? 'active' : '' ?>" data-option="package" onclick="selectPricing('package')">
                                <i class="bi bi-box-seam"></i>
                                <div class="option-title">Package</div>
                                <div class="option-desc">Fixed days with KM limit + extra KM charge</div>
                            </div>
                        </div>
                        <input type="hidden" id="pricingType" name="pricing_type" value="<?= $pricingType ?>">
                    </div>
                </div>

                <!-- Pricing Fields -->
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <!-- Per Day Pricing -->
                        <div class="pricing-fields <?= $pricingType == 'perday' ? 'active' : '' ?>" id="perdayPricing">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Per Day Amount <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="perDayAmount" value="<?= $vehicle['per_day_amount'] ?>" step="0.01" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Per KM Charge <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="perKmCharge" value="<?= $vehicle['per_km_charge'] ?>" step="0.01" required>
                                </div>
                            </div>
                        </div>

                        <!-- Package Pricing -->
                        <div class="pricing-fields <?= $pricingType == 'package' ? 'active' : '' ?>" id="packagePricing">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Package Days <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="packageDays" value="<?= $vehicle['package_days'] ?: 7 ?>" min="1">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Package Price <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="packagePrice" value="<?= $vehicle['package_price'] ?: 0 ?>" step="0.01">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">KM Limit <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="packageKmLimit" value="<?= $vehicle['package_km_limit'] ?: 300 ?>" min="0">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Extra KM Charge <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="extraKmCharge" value="<?= $vehicle['extra_km_charge'] ?: 0 ?>" step="0.01">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 4: Fuel, Transmission, Seating (3 columns) -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Fuel Type</label>
                        <select class="form-select" id="fuelType">
                            <option value="">Select Fuel</option>
                            <option value="Petrol" <?= $vehicle['fuel_type'] == 'Petrol' ? 'selected' : '' ?>>Petrol</option>
                            <option value="Diesel" <?= $vehicle['fuel_type'] == 'Diesel' ? 'selected' : '' ?>>Diesel</option>
                            <option value="Electric" <?= $vehicle['fuel_type'] == 'Electric' ? 'selected' : '' ?>>Electric</option>
                            <option value="Hybrid" <?= $vehicle['fuel_type'] == 'Hybrid' ? 'selected' : '' ?>>Hybrid</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Transmission</label>
                        <select class="form-select" id="transmission">
                            <option value="">Select Transmission</option>
                            <option value="Automatic" <?= $vehicle['transmission'] == 'Automatic' ? 'selected' : '' ?>>Automatic</option>
                            <option value="Manual" <?= $vehicle['transmission'] == 'Manual' ? 'selected' : '' ?>>Manual</option>
                            <option value="CVT" <?= $vehicle['transmission'] == 'CVT' ? 'selected' : '' ?>>CVT</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Seating Capacity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="seatingCapacity" value="<?= $vehicle['seating_capacity'] ?: 4 ?>" min="1" required>
                    </div>
                </div>

                <!-- Row 5: AC, Status (2 columns) -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">AC Available</label>
                        <select class="form-select" id="acAvailable">
                            <option value="1" <?= $vehicle['ac_available'] == 1 ? 'selected' : '' ?>>Yes</option>
                            <option value="0" <?= $vehicle['ac_available'] == 0 ? 'selected' : '' ?>>No</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="status">
                            <option value="available" <?= $vehicle['status'] == 'available' ? 'selected' : '' ?>>Available</option>
                            <option value="booked" <?= $vehicle['status'] == 'booked' ? 'selected' : '' ?>>Booked</option>
                            <option value="maintenance" <?= $vehicle['status'] == 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                        </select>
                    </div>
                </div>

                <!-- Row 6: Description (full width) -->
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="description" rows="2"><?= htmlspecialchars($vehicle['description']) ?></textarea>
                    </div>
                </div>

                <!-- Row 7: Main Image -->
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label">Main Image</label>
                        <?php if ($vehicle['vehicle_image']): ?>
                            <div class="mb-2">
                                <div class="current-image-label">Current Image:</div>
                                <div class="current-image-wrapper">
                                    <div class="current-image-item">
                                        <img src="<?= APP_URL . $vehicle['vehicle_image'] ?>" alt="Current main image">
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
                    <button type="button" class="btn-cancel" onclick="window.location.href='vehicle.php'">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <span id="submitText">Update Vehicle</span>
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
        // EDIT VEHICLE - AJAX HANDLER
        // =============================================

        let vehicleTypes = [];
        let deletedImages = [];
        let mainImageDeleted = false;

        document.addEventListener('DOMContentLoaded', function() {
            console.log('Edit Vehicle JS loaded successfully!');

            // =============================================
            // PRICING OPTIONS
            // =============================================

            window.selectPricing = function(type) {
                // Update active state
                document.querySelectorAll('.pricing-option').forEach(el => {
                    el.classList.remove('active');
                });
                document.querySelector(`.pricing-option[data-option="${type}"]`).classList.add('active');

                // Show/hide pricing fields
                document.getElementById('perdayPricing').classList.remove('active');
                document.getElementById('packagePricing').classList.remove('active');

                if (type === 'perday') {
                    document.getElementById('perdayPricing').classList.add('active');
                    document.getElementById('pricingType').value = 'perday';
                    // Make fields required
                    document.getElementById('perDayAmount').required = true;
                    document.getElementById('perKmCharge').required = true;
                    document.getElementById('packageDays').required = false;
                    document.getElementById('packagePrice').required = false;
                    document.getElementById('packageKmLimit').required = false;
                    document.getElementById('extraKmCharge').required = false;
                } else {
                    document.getElementById('packagePricing').classList.add('active');
                    document.getElementById('pricingType').value = 'package';
                    // Make fields required
                    document.getElementById('perDayAmount').required = false;
                    document.getElementById('perKmCharge').required = false;
                    document.getElementById('packageDays').required = true;
                    document.getElementById('packagePrice').required = true;
                    document.getElementById('packageKmLimit').required = true;
                    document.getElementById('extraKmCharge').required = true;
                }
            }

            // =============================================
            // VEHICLE TYPES - Load existing types
            // =============================================

            const vehicleTypesHidden = document.getElementById('vehicleTypesHidden');
            if (vehicleTypesHidden && vehicleTypesHidden.value) {
                try {
                    const existingTypes = JSON.parse(vehicleTypesHidden.value);
                    if (Array.isArray(existingTypes) && existingTypes.length > 0) {
                        vehicleTypes = existingTypes.filter(type => type && type.trim() !== '');
                        renderVehicleTypes();
                    }
                } catch (e) {
                    console.error('Error parsing vehicle types:', e);
                    const rawValue = vehicleTypesHidden.value;
                    if (rawValue && rawValue.includes(',')) {
                        vehicleTypes = rawValue.split(',').map(t => t.trim()).filter(t => t);
                        renderVehicleTypes();
                    }
                }
            }

            // =============================================
            // VEHICLE TYPES - Badge Style (Input field)
            // =============================================

            const addVehicleTypeBtn = document.getElementById('addVehicleTypeBtn');
            const vehicleTypeInput = document.getElementById('vehicleTypeInput');

            if (addVehicleTypeBtn) {
                addVehicleTypeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    addVehicleType();
                });
            }

            if (vehicleTypeInput) {
                vehicleTypeInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        addVehicleType();
                    }
                });
            }

            function addVehicleType() {
                const input = document.getElementById('vehicleTypeInput');
                const type = input.value.trim();

                if (!type) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Vehicle Type Required',
                        text: 'Please enter a vehicle type',
                        confirmButtonColor: '#0b2a3e'
                    });
                    input.focus();
                    return;
                }

                const existing = vehicleTypes.find(c => c.toLowerCase() === type.toLowerCase());
                if (existing) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Duplicate Vehicle Type',
                        text: `"${type}" already exists. Please enter a different type.`,
                        confirmButtonColor: '#0b2a3e'
                    });
                    input.value = '';
                    input.focus();
                    return;
                }

                vehicleTypes.push(type);
                renderVehicleTypes();
                input.value = '';
                input.focus();
            }

            window.addVehicleType = addVehicleType;

            window.removeVehicleType = function(index) {
                vehicleTypes.splice(index, 1);
                renderVehicleTypes();
            }

            function renderVehicleTypes() {
                const container = document.getElementById('vehicleTypesList');
                container.innerHTML = '';

                if (vehicleTypes.length === 0) {
                    container.innerHTML = '<div class="empty-badges">No vehicle types added</div>';
                    return;
                }

                vehicleTypes.forEach((type, index) => {
                    const badge = document.createElement('span');
                    badge.className = 'badge-item';
                    badge.innerHTML = `
                        <span class="badge-name">${escapeHtml(type)}</span>
                        <span class="remove-badge" onclick="removeVehicleType(${index})">&times;</span>
                    `;
                    container.appendChild(badge);
                });

                document.getElementById('vehicleTypes').value = JSON.stringify(vehicleTypes);
            }

            // =============================================
            // IMAGE UPLOAD HANDLING
            // =============================================

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
            // UTILITY FUNCTIONS
            // =============================================

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

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
            // FORM SUBMISSION
            // =============================================

            const editForm = document.getElementById('editVehicleForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Validate required fields
                    const vehicleName = document.getElementById('vehicleName').value.trim();
                    const seatingCapacity = document.getElementById('seatingCapacity').value.trim();
                    const pricingType = document.getElementById('pricingType').value;

                    if (vehicleTypes.length === 0) {
                        showAlert('Please add at least one vehicle type', 'warning');
                        document.getElementById('vehicleTypeInput').focus();
                        return;
                    }

                    if (!vehicleName) {
                        showAlert('Please enter vehicle name', 'warning');
                        document.getElementById('vehicleName').focus();
                        return;
                    }

                    if (!seatingCapacity || isNaN(seatingCapacity) || parseInt(seatingCapacity) <= 0) {
                        showAlert('Please enter valid seating capacity', 'warning');
                        document.getElementById('seatingCapacity').focus();
                        return;
                    }

                    // Validate pricing
                    if (pricingType === 'perday') {
                        const perDayAmount = document.getElementById('perDayAmount').value.trim();
                        const perKmCharge = document.getElementById('perKmCharge').value.trim();

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
                    } else {
                        const packageDays = document.getElementById('packageDays').value.trim();
                        const packagePrice = document.getElementById('packagePrice').value.trim();
                        const packageKmLimit = document.getElementById('packageKmLimit').value.trim();
                        const extraKmCharge = document.getElementById('extraKmCharge').value.trim();

                        if (!packageDays || isNaN(packageDays) || parseInt(packageDays) <= 0) {
                            showAlert('Please enter valid package days', 'warning');
                            document.getElementById('packageDays').focus();
                            return;
                        }

                        if (!packagePrice || isNaN(packagePrice) || parseFloat(packagePrice) <= 0) {
                            showAlert('Please enter valid package price', 'warning');
                            document.getElementById('packagePrice').focus();
                            return;
                        }

                        if (!packageKmLimit || isNaN(packageKmLimit) || parseFloat(packageKmLimit) <= 0) {
                            showAlert('Please enter valid package KM limit', 'warning');
                            document.getElementById('packageKmLimit').focus();
                            return;
                        }

                        if (!extraKmCharge || isNaN(extraKmCharge) || parseFloat(extraKmCharge) <= 0) {
                            showAlert('Please enter valid extra KM charge', 'warning');
                            document.getElementById('extraKmCharge').focus();
                            return;
                        }
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
                    formData.append('id', document.getElementById('vehicleId').value);
                    formData.append('vehicle_name', vehicleName);
                    formData.append('vehicle_model', document.getElementById('vehicleModel').value.trim());
                    formData.append('vehicle_brand', document.getElementById('vehicleBrand').value.trim());
                    formData.append('vehicle_type', JSON.stringify(vehicleTypes));
                    formData.append('pricing_type', pricingType);

                    // Pricing fields
                    if (pricingType === 'perday') {
                        formData.append('per_day_amount', document.getElementById('perDayAmount').value.trim());
                        formData.append('per_km_charge', document.getElementById('perKmCharge').value.trim());
                        formData.append('package_days', '');
                        formData.append('package_price', '');
                        formData.append('package_km_limit', '');
                        formData.append('extra_km_charge', '');
                    } else {
                        formData.append('per_day_amount', '');
                        formData.append('per_km_charge', '');
                        formData.append('package_days', document.getElementById('packageDays').value.trim());
                        formData.append('package_price', document.getElementById('packagePrice').value.trim());
                        formData.append('package_km_limit', document.getElementById('packageKmLimit').value.trim());
                        formData.append('extra_km_charge', document.getElementById('extraKmCharge').value.trim());
                    }

                    formData.append('fuel_type', document.getElementById('fuelType').value);
                    formData.append('transmission', document.getElementById('transmission').value);
                    formData.append('seating_capacity', seatingCapacity);
                    formData.append('ac_available', document.getElementById('acAvailable').value);
                    formData.append('status', document.getElementById('status').value);
                    formData.append('description', document.getElementById('description').value.trim());

                    formData.append('deleted_images', JSON.stringify(deletedImages));
                    formData.append('delete_main_image', mainImageDeleted ? '1' : '0');

                    const mainImage = document.getElementById('mainImage').files[0];
                    if (mainImage) {
                        formData.append('main_image', mainImage);
                    }

                    const additionalFiles = document.getElementById('additionalImages').files;
                    for (let i = 0; i < additionalFiles.length; i++) {
                        formData.append('additional_images[]', additionalFiles[i]);
                    }

                    // AJAX request
                    fetch('ajax/edit-vehicle.php', {
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
                                    window.location.href = 'vehicle.php';
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