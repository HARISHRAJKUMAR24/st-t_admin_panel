<?php
require_once 'config/config.php';
require_once 'config/function.php';
requireLogin();

// Verify token
if (!verifyToken($pdo)) {
    header("Location: " . APP_URL . "login.php");
    exit();
}

// Get current user
$currentUser = getCurrentUser($pdo);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>Add Car Rental · Tour Admin</title>
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

        /* Image upload with left/right layout */
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

        /* Button alignment */
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
                <small>Add new car rental</small>
            </div>
        </div>

        <!-- ====== ADD CAR RENTAL FORM ====== -->
        <div class="form-section">
            <h5 class="section-title"><i class="bi bi-car-front-fill me-2" style="color:#f5b342;"></i>Add New Car Rental</h5>

            <form id="addCarRentalForm" enctype="multipart/form-data">
                <!-- Row 1: Car Name, Model, Brand (3 columns) -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Car Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="carName" placeholder="e.g., Toyota Innova" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Car Model</label>
                        <input type="text" class="form-control" id="carModel" placeholder="e.g., 2024 Crysta">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Brand</label>
                        <input type="text" class="form-control" id="carBrand" placeholder="e.g., Toyota">
                    </div>
                </div>

                <!-- Row 2: Type, Per Day, Per KM (3 columns) -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Car Type</label>
                        <select class="form-select" id="carType">
                            <option value="">Select Type</option>
                            <option value="Sedan">Sedan</option>
                            <option value="SUV">SUV</option>
                            <option value="MUV">MUV</option>
                            <option value="Hatchback">Hatchback</option>
                            <option value="Luxury-Cars">Luxury Cars</option>
                            <option value="Mini">Mini</option>
                             <option value="Tempo-Traveller">Tempo Traveller</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Per Day Amount <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="perDayAmount" placeholder="0.00" step="0.01" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Per KM Charge <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="perKmCharge" placeholder="0.00" step="0.01" required>
                    </div>
                </div>

                <!-- Row 3: Fuel, Transmission, Seating (3 columns) -->
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Fuel Type</label>
                        <select class="form-select" id="fuelType">
                            <option value="">Select Fuel</option>
                            <option value="Petrol">Petrol</option>
                            <option value="Diesel">Diesel</option>
                            <option value="Electric">Electric</option>
                            <option value="Hybrid">Hybrid</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Transmission</label>
                        <select class="form-select" id="transmission">
                            <option value="">Select Transmission</option>
                            <option value="Automatic">Automatic</option>
                            <option value="Manual">Manual</option>
                            <option value="CVT">CVT</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Seating Capacity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="seatingCapacity" placeholder="4" min="1" value="4" required>
                    </div>
                </div>

                <!-- Row 4: AC, Status (2 columns) -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">AC Available</label>
                        <select class="form-select" id="acAvailable">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="status">
                            <option value="available">Available</option>
                            <option value="booked">Booked</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                </div>

                <!-- Row 5: Description (full width) -->
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="description" rows="2" placeholder="Enter car description, features, etc."></textarea>
                    </div>
                </div>

                <!-- Row 6: Main Image Upload - Left/Right layout -->
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label">Main Image <span class="text-danger">*</span></label>
                        <div class="image-upload-wrapper">
                            <div class="image-upload-box" id="mainImageBox">
                                <i class="bi bi-cloud-upload"></i>
                                <p>Click to upload<br><small>JPG, PNG, WebP</small></p>
                                <input type="file" id="mainImage" accept="image/*" style="display:none;">
                            </div>
                            <div class="image-preview-wrapper">
                                <div id="mainImagePreview" class="image-preview">
                                    <div class="image-preview-empty">No image selected</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row 7: Additional Images - Left/Right layout -->
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label">Additional Images</label>
                        <div class="image-upload-wrapper">
                            <div class="image-upload-box" id="additionalImagesBox">
                                <i class="bi bi-images"></i>
                                <p>Click to upload<br><small>Multiple images</small></p>
                                <input type="file" id="additionalImages" accept="image/*" multiple style="display:none;">
                            </div>
                            <div class="image-preview-wrapper">
                                <div id="additionalImagesPreview" class="image-preview">
                                    <div class="image-preview-empty">No additional images</div>
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
                        <span id="submitText">Save</span>
                        <span id="submitSpinner" class="spinner-border spinner-border-sm" style="display:none;"></span>
                    </button>
                </div>
            </form>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>
    <script src="<?= APP_URL ?>javascript/main.js"></script>
    <!-- Custom JS for car rental add -->
    <script src="<?= APP_URL ?>javascript/add-car-rental.js"></script>
</body>

</html>