<?php
require_once 'config/config.php';
require_once 'config/function.php';
requireLogin();

if (!verifyToken($pdo)) {
    header("Location: " . APP_URL . "login.php");
    exit();
}

$currentUser = getCurrentUser($pdo);
$currencyCode = getCurrencyCode($pdo);
$currencySymbol = getCurrencySymbol($currencyCode);

$stmt = $pdo->query("SELECT id, car_name, car_image, per_day_amount, per_km_charge, seating_capacity, car_type, car_brand FROM car_rentals WHERE status = 'available' ORDER BY car_name ASC");
$cars = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>Travel Booking · Tour Admin</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .page-wrapper {
            padding: 20px;
        }
        .page-header {
            margin-bottom: 25px;
        }
        .page-header h4 {
            font-weight: 600;
            color: #123b4f;
            margin-bottom: 5px;
            font-size: 1.3rem;
        }
        .page-header p {
            color: #5f7d92;
            font-size: 0.85rem;
        }
        .back-link {
            display: inline-block;
            color: #5f7d92;
            text-decoration: none;
            font-size: 0.85rem;
            margin-bottom: 1rem;
            transition: color 0.2s;
        }
        .back-link:hover {
            color: #123b4f;
        }
        .form-container {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 32px rgba(0, 20, 30, 0.06);
            max-width: 1100px;
            margin: 0 auto;
        }
        .section-title {
            font-weight: 600;
            color: #123b4f;
            font-size: 1rem;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #ffd966;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-title .badge-count {
            background: #ffd966;
            color: #123b4f;
            font-size: 0.7rem;
            padding: 0.1rem 0.6rem;
            border-radius: 12px;
            font-weight: 600;
        }
        .form-label {
            font-weight: 500;
            color: #123b4f;
            font-size: 0.8rem;
        }
        .form-label .required {
            color: #dc3545;
            margin-left: 2px;
        }
        .form-control,
        .form-select {
            border-radius: 10px;
            padding: 0.5rem 0.8rem;
            border: 2px solid #e8edf3;
            background: rgba(255, 255, 255, 0.6);
            font-weight: 500;
            transition: all 0.2s;
            font-size: 0.85rem;
        }
        .form-control:focus,
        .form-select:focus {
            border-color: #ffd966;
            box-shadow: 0 0 0 4px rgba(255, 215, 100, 0.15);
            background: white;
        }
        .form-control[readonly] {
            background: rgba(255, 255, 255, 0.3);
            cursor: not-allowed;
        }
        .stop-card {
            background: rgba(255, 255, 255, 0.5);
            border: 2px solid #e8edf3;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 12px;
            transition: all 0.3s ease;
            position: relative;
        }
        .stop-card:hover {
            border-color: #d5dce6;
        }
        .stop-card .stop-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .stop-card .stop-header .stop-label {
            font-weight: 600;
            color: #123b4f;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .stop-card .stop-header .stop-number {
            background: #ffd966;
            color: #123b4f;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .stop-card .stop-header .remove-stop {
            color: #dc3545;
            cursor: pointer;
            background: none;
            border: none;
            font-size: 1.2rem;
            padding: 4px 8px;
            border-radius: 6px;
            transition: all 0.2s ease;
            opacity: 0.5;
        }
        .stop-card .stop-header .remove-stop:hover {
            opacity: 1;
            background: rgba(220, 53, 69, 0.08);
        }
        .stop-card .stop-price {
            font-size: 1rem;
            font-weight: 600;
            color: #28a745;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px dashed #e8edf3;
        }
        .btn-add-stop {
            background: rgba(18, 59, 79, 0.04);
            color: #123b4f;
            border: 2px dashed #e8edf3;
            border-radius: 10px;
            padding: 0.8rem;
            width: 100%;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 0.85rem;
        }
        .btn-add-stop:hover {
            background: rgba(255, 215, 100, 0.08);
            border-color: #ffd966;
        }
        #map {
            width: 100%;
            height: 350px;
            border-radius: 12px;
            border: 2px solid #e8edf3;
            z-index: 1;
        }
        .map-directions-info {
            margin-top: 12px;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 10px;
            border: 1px solid #e8edf3;
            display: none;
        }
        .map-directions-info.show {
            display: block;
        }
        .map-directions-info .distance-info {
            font-size: 0.85rem;
            color: #123b4f;
            font-weight: 500;
        }
        .btn-submit {
            background: linear-gradient(145deg, #0b2a3e 0%, #123b4f 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.7rem 2.5rem;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 0.95rem;
            min-width: 160px;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(11, 42, 62, 0.2);
            color: #ffd966;
        }
        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }
        .btn-secondary {
            border-radius: 10px;
            padding: 0.65rem 1.8rem;
            font-weight: 500;
            font-size: 0.9rem;
            border: 2px solid #e8edf3;
            background: transparent;
            color: #5f7d92;
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background: #e8edf3;
            color: #123b4f;
            border-color: #d5dce6;
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 2px solid #f0f3f7;
        }
        .row-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .custom-marker {
            background: #ffd966;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #0b2a3e;
            border: 3px solid #0b2a3e;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .custom-marker.pickup {
            background: #28a745;
            color: white;
            border-color: #1e7e34;
        }
        .custom-marker.drop {
            background: #dc3545;
            color: white;
            border-color: #bd2130;
        }
        .autocomplete-container {
            position: relative;
        }
        .autocomplete-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 2px solid #e8edf3;
            border-top: none;
            border-radius: 0 0 10px 10px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }
        .autocomplete-dropdown.show {
            display: block;
        }
        .autocomplete-item {
            padding: 8px 12px;
            cursor: pointer;
            transition: background 0.2s;
            font-size: 0.85rem;
            color: #123b4f;
            border-bottom: 1px solid #f0f3f7;
        }
        .autocomplete-item:hover {
            background: #fff8e8;
        }
        .autocomplete-item .highlight {
            color: #f5b342;
            font-weight: 600;
        }
        .pickup-drop-row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .pickup-drop-row .form-group {
            flex: 1;
            min-width: 150px;
        }
        .price-input-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .price-input-group .form-group {
            flex: 1;
            min-width: 150px;
        }

        /* Badge Feature Input */
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
            background: rgba(255, 215, 100, 0.2);
            color: #b8860b;
            padding: 4px 12px 4px 8px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 500;
            gap: 6px;
            transition: all 0.2s ease;
            animation: badgeIn 0.3s ease;
        }
        .badge-item:hover {
            background: rgba(255, 215, 100, 0.3);
            transform: translateY(-1px);
        }
        .badge-item .badge-icon {
            width: 22px;
            height: 22px;
            object-fit: contain;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.5);
            padding: 2px;
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
        .badge-input {
            border: none;
            background: transparent;
            padding: 4px 8px;
            flex: 1;
            min-width: 120px;
            outline: none;
            font-size: 0.85rem;
            color: #123b4f;
        }
        .badge-input::placeholder {
            color: #9bb2c5;
        }
        .empty-badges {
            color: #9bb2c5;
            font-size: 0.8rem;
            padding: 6px 0;
            width: 100%;
            text-align: center;
        }
        .feature-icon-upload-box {
            display: flex;
            align-items: center;
            gap: 6px;
            border: 2px dashed #e8edf3;
            border-radius: 8px;
            padding: 0.3rem 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.4);
            min-height: 38px;
        }
        .feature-icon-upload-box:hover {
            border-color: #ffd966;
            background: rgba(255, 215, 100, 0.05);
        }
        .feature-icon-upload-box.has-file {
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.05);
        }

        .distance-label {
            background: transparent !important;
            border: none !important;
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
            .page-wrapper {
                padding: 10px;
            }
            .form-container {
                padding: 1rem;
            }
            .form-actions {
                flex-direction: column;
            }
            .form-actions .btn {
                width: 100%;
                justify-content: center;
            }
            .row-2col {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            #map {
                height: 250px;
            }
            .price-input-group {
                flex-direction: column;
            }
            .price-input-group .form-group {
                min-width: 100%;
            }
            .badge-input-row {
                flex-direction: column;
                align-items: stretch;
            }
            .badge-input-row .form-control {
                min-width: unset;
            }
            .feature-icon-upload-box {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <?php include_once 'includes/nav.php'; ?>

    <div class="main-wrapper">
        <div class="top-bar">
            <button class="burger-btn" id="burgerBtn" aria-label="Toggle navigation">
                <i class="bi bi-list"></i>
            </button>
            <div class="greeting-center">
                Welcome back, <strong><?= htmlspecialchars($currentUser['name'] ?? 'Admin') ?></strong>
                <small>Book a Travel</small>
            </div>
        </div>

        <div class="page-wrapper">

            <div class="page-header">
                <h4><i class="bi bi-car-front-fill me-2" style="color:#f5b342;"></i>Book Your Travel</h4>
               
            </div>

            <div class="form-container">
                <form id="travelBookingForm" enctype="multipart/form-data">
                    <div class="row-2col">
                        <!-- Left Column -->
                        <div>
                            <!-- Car Selection -->
                            <div class="section-title">
                                <i class="bi bi-car-front"></i> Select Car
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Select Car <span class="required">*</span></label>
                                <select class="form-select" id="carSelect" required>
                                    <option value="">-- Select a Car --</option>
                                    <?php foreach ($cars as $car): ?>
                                        <option value="<?= $car['id'] ?>" 
                                                data-name="<?= htmlspecialchars($car['car_name']) ?>"
                                                data-type="<?= htmlspecialchars($car['car_type'] ?? 'Sedan') ?>"
                                                data-brand="<?= htmlspecialchars($car['car_brand'] ?? '') ?>"
                                                data-price="<?= $car['per_day_amount'] ?>"
                                                data-perkm="<?= $car['per_km_charge'] ?>"
                                                data-seats="<?= $car['seating_capacity'] ?>">
                                            <?= htmlspecialchars($car['car_name']) ?> - <?= htmlspecialchars($car['car_type'] ?? 'Sedan') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Car Name <span class="required">*</span></label>
                                <input type="text" class="form-control" id="carName" readonly required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Car Type</label>
                                <input type="text" class="form-control" id="carType" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Seat Count <span class="required">*</span></label>
                                <input type="number" class="form-control" id="seatCount" readonly>
                            </div>

                            <div class="section-title mt-3">
                                <i class="bi bi-tag"></i> Pricing
                            </div>

                            <div class="price-input-group">
                                <div class="form-group">
                                    <label class="form-label">Price Per Day <span class="required">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text" style="border-radius:10px 0 0 10px;border:2px solid #e8edf3;border-right:none;background:rgba(255,255,255,0.6);font-weight:600;">
                                            <?= htmlspecialchars($currencySymbol) ?>
                                        </span>
                                        <input type="number" class="form-control" id="pricePerDay" step="0.01" style="border-radius:0 10px 10px 0;">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Per KM Charge <span class="required">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text" style="border-radius:10px 0 0 10px;border:2px solid #e8edf3;border-right:none;background:rgba(255,255,255,0.6);font-weight:600;">
                                            <?= htmlspecialchars($currencySymbol) ?>
                                        </span>
                                        <input type="number" class="form-control" id="perKmCharge" step="0.01" style="border-radius:0 10px 10px 0;">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 mt-2">
                                <label class="form-label">Number of Days <span class="required">*</span></label>
                                <input type="number" class="form-control" id="days" value="1" min="1" required>
                            </div>

                            <!-- What We Provide -->
                            <div class="section-title mt-3">
                                <i class="bi bi-check-circle"></i> What We Provide
                            </div>

                            <div class="mb-3">
                                <div class="badge-input-wrapper">
                                    <div class="badge-input-row">
                                        <input type="text" class="form-control" id="provideInput" placeholder="Enter item name" style="flex:1;">
                                        <div style="display:flex;gap:6px;align-items:center;">
                                            <label for="provideIcon" class="feature-icon-upload-box" id="provideIconBox">
                                                <i class="bi bi-image" style="font-size:1rem;color:#9bb2c5;"></i>
                                                <span id="provideIconLabel" style="font-size:0.7rem;color:#5f7d92;white-space:nowrap;">Upload Icon</span>
                                            </label>
                                            <input type="file" id="provideIcon" accept="image/*" style="display:none;">
                                            <button type="button" class="btn-sm-primary" onclick="addProvideItem()" style="background:#ffd966;color:#123b4f;border:none;border-radius:8px;padding:0.4rem 1.2rem;font-weight:600;font-size:0.8rem;cursor:pointer;">
                                                <i class="bi bi-plus-circle"></i> Add
                                            </button>
                                        </div>
                                    </div>
                                    <div id="provideIconPreview" class="mt-2" style="display:none;">
                                        <span style="background:rgba(40,167,69,0.1);color:#28a745;padding:0.2rem 0.8rem;border-radius:12px;font-size:0.75rem;display:inline-flex;align-items:center;gap:6px;">
                                            <img id="provideIconPreviewImg" src="" style="width:18px;height:18px;object-fit:contain;border-radius:4px;">
                                            <span id="provideIconPreviewName"></span>
                                            <button type="button" onclick="removeProvideIconPreview()" style="background:none;border:none;color:#dc3545;cursor:pointer;font-size:1rem;padding:0 4px;">×</button>
                                        </span>
                                    </div>
                                    <div class="badges-container" id="provideContainer">
                                        <div class="empty-badges">No items added</div>
                                    </div>
                                </div>
                                <small class="text-muted" style="font-size:0.7rem;">Add what you provide with optional icons (JPG, PNG, WebP)</small>
                            </div>
                        </div>

                        <!-- Right Column: Stops -->
                        <div>
                            <div class="section-title">
                                <i class="bi bi-geo-alt"></i> Stops
                                <span class="badge-count" id="stopCount">0 Stops</span>
                            </div>

                            <div id="stopsContainer"></div>

                            <button type="button" class="btn-add-stop" onclick="addStop()">
                                <i class="bi bi-plus-circle me-2"></i> Add Stop
                            </button>
                        </div>
                    </div>

                    <!-- Map -->
                    <div class="section-title mt-3">
                        <i class="bi bi-map"></i> Route Map
                    </div>

                    <div id="map"></div>
                    <div class="map-directions-info" id="directionsInfo">
                        <div class="distance-info" id="stopDistancesContainer">
                            <!-- Stop distances will be displayed here -->
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="window.location.href='travel-bookings.php'">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <span id="submitText">Book Now</span>
                            <span id="submitSpinner" class="spinner-border spinner-border-sm" style="display:none;"></span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= APP_URL ?>javascript/main.js"></script>
    <script>
        let map;
        let routeLayer = null;
        let markers = [];
        let stopCoords = {};
        let provideItems = [];
        let provideIconFile = null;
        let provideIconPreviewData = null;
        let routeColors = ['#f5b342', '#4CAF50', '#2196F3', '#9C27B0', '#FF5722', '#00BCD4', '#FF9800', '#8BC34A'];

        // =============================================
        // CAR SELECTION
        // =============================================

        document.getElementById('carSelect').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (this.value) {
                document.getElementById('carName').value = selected.dataset.name;
                document.getElementById('carType').value = selected.dataset.type || 'Sedan';
                document.getElementById('seatCount').value = selected.dataset.seats || 4;
                document.getElementById('pricePerDay').value = selected.dataset.price || 0;
                document.getElementById('perKmCharge').value = selected.dataset.perkm || 0;
            } else {
                document.getElementById('carName').value = '';
                document.getElementById('carType').value = '';
                document.getElementById('seatCount').value = '';
                document.getElementById('pricePerDay').value = '';
                document.getElementById('perKmCharge').value = '';
            }
        });

        // =============================================
        // WHAT WE PROVIDE - BADGE STYLE
        // =============================================

        function setupProvideIconUpload() {
            const box = document.getElementById('provideIconBox');
            const input = document.getElementById('provideIcon');

            if (box) {
                box.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    input.click();
                });
            }

            if (input) {
                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        if (file.size > 1 * 1024 * 1024) {
                            Swal.fire({ icon: 'error', title: 'File Too Large', text: 'Icon file must be less than 1MB' });
                            this.value = '';
                            return;
                        }
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
                        if (!allowedTypes.includes(file.type)) {
                            Swal.fire({ icon: 'error', title: 'Invalid File Type', text: 'Please upload JPG, PNG, GIF, WebP, or SVG' });
                            this.value = '';
                            return;
                        }
                        provideIconFile = file;
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            provideIconPreviewData = e.target.result;
                            document.getElementById('provideIconPreviewImg').src = e.target.result;
                            document.getElementById('provideIconPreviewName').textContent = file.name;
                            document.getElementById('provideIconPreview').style.display = 'block';
                            document.getElementById('provideIconBox').classList.add('has-file');
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        }

        function removeProvideIconPreview() {
            provideIconFile = null;
            provideIconPreviewData = null;
            document.getElementById('provideIcon').value = '';
            document.getElementById('provideIconPreview').style.display = 'none';
            document.getElementById('provideIconBox').classList.remove('has-file');
        }

        function addProvideItem() {
            const input = document.getElementById('provideInput');
            const name = input.value.trim();
            if (!name) {
                Swal.fire({ icon: 'warning', title: 'Item Name Required', text: 'Please enter an item name' });
                input.focus();
                return;
            }
            const existing = provideItems.find(i => i.name.toLowerCase() === name.toLowerCase());
            if (existing) {
                Swal.fire({ icon: 'warning', title: 'Duplicate Item', text: `"${name}" already exists` });
                input.focus();
                return;
            }
            provideItems.push({
                name: name,
                icon: provideIconFile ? provideIconFile.name : null,
                iconFile: provideIconFile,
                iconPreview: provideIconPreviewData
            });
            renderProvideItems();
            input.value = '';
            provideIconFile = null;
            provideIconPreviewData = null;
            document.getElementById('provideIcon').value = '';
            document.getElementById('provideIconPreview').style.display = 'none';
            document.getElementById('provideIconBox').classList.remove('has-file');
        }

        function removeProvideItem(index) {
            provideItems.splice(index, 1);
            renderProvideItems();
        }

        function renderProvideItems() {
            const container = document.getElementById('provideContainer');
            container.innerHTML = '';
            if (provideItems.length === 0) {
                container.innerHTML = '<div class="empty-badges">No items added</div>';
                return;
            }
            provideItems.forEach((item, index) => {
                const badge = document.createElement('span');
                badge.className = 'badge-item';
                let iconHtml = '';
                if (item.iconPreview) {
                    iconHtml = `<img src="${item.iconPreview}" class="badge-icon" alt="icon">`;
                }
                badge.innerHTML = `
                    ${iconHtml}
                    <span class="badge-name">${escapeHtml(item.name)}</span>
                    <span class="remove-badge" onclick="removeProvideItem(${index})">&times;</span>
                `;
                container.appendChild(badge);
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // =============================================
        // STOPS MANAGEMENT
        // =============================================

        function addStop() {
            const container = document.getElementById('stopsContainer');
            const stopNumber = container.children.length + 1;

            const stopDiv = document.createElement('div');
            stopDiv.className = 'stop-card';
            stopDiv.id = 'stop-' + stopNumber;
            stopDiv.innerHTML = `
                <div class="stop-header">
                    <span class="stop-label">
                        <span class="stop-number">${stopNumber}</span>
                        Stop ${stopNumber}
                    </span>
                    <button type="button" class="remove-stop" onclick="removeStop('${stopDiv.id}')">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>
                <div class="pickup-drop-row">
                    <div class="form-group">
                        <label class="form-label">Pickup <span class="required">*</span></label>
                        <div class="autocomplete-container">
                            <input type="text" class="form-control pickup-input" placeholder="Enter pickup">
                            <div class="autocomplete-dropdown pickup-dropdown"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Drop <span class="required">*</span></label>
                        <div class="autocomplete-container">
                            <input type="text" class="form-control drop-input" placeholder="Enter drop">
                            <div class="autocomplete-dropdown drop-dropdown"></div>
                        </div>
                    </div>
                </div>
                <div class="stop-price">
                    Distance: <span class="stop-distance">--</span> km | 
                    Price: <span class="stop-price-amount"><?= htmlspecialchars($currencySymbol) ?>0.00</span>
                </div>
            `;
            container.appendChild(stopDiv);
            setupStopAutocomplete(stopNumber);
            updateStopCount();
        }

        function removeStop(stopId) {
            const stop = document.getElementById(stopId);
            if (stop && document.getElementById('stopsContainer').children.length > 1) {
                const stopNum = parseInt(stopId.split('-')[1]);
                delete stopCoords[stopNum];
                stop.remove();
                renumberStops();
                updateStopCount();
                updateMap();
            } else {
                Swal.fire({ icon: 'warning', title: 'Cannot Remove', text: 'You need at least one stop' });
            }
        }

        function renumberStops() {
            const container = document.getElementById('stopsContainer');
            const stops = container.children;
            const newCoords = {};
            for (let i = 0; i < stops.length; i++) {
                const stop = stops[i];
                const stopNumber = i + 1;
                stop.id = 'stop-' + stopNumber;
                const numberSpan = stop.querySelector('.stop-number');
                if (numberSpan) numberSpan.textContent = stopNumber;
                const label = stop.querySelector('.stop-label');
                if (label) {
                    label.innerHTML = `<span class="stop-number">${stopNumber}</span> Stop ${stopNumber}`;
                }
                const oldNum = parseInt(stop.id.split('-')[1]);
                if (stopCoords[oldNum]) {
                    newCoords[stopNumber] = stopCoords[oldNum];
                }
            }
            stopCoords = newCoords;
            updateStopCount();
        }

        function updateStopCount() {
            const count = document.getElementById('stopsContainer').children.length;
            document.getElementById('stopCount').textContent = count + ' Stop' + (count > 1 ? 's' : '');
        }

        // =============================================
        // STOP AUTOCOMPLETE
        // =============================================

        function setupStopAutocomplete(stopNumber) {
            const stopElement = document.getElementById('stop-' + stopNumber);
            const pickupInput = stopElement.querySelector('.pickup-input');
            const dropInput = stopElement.querySelector('.drop-input');
            const pickupDropdown = stopElement.querySelector('.pickup-dropdown');
            const dropDropdown = stopElement.querySelector('.drop-dropdown');

            let pickupTimeout, dropTimeout;

            pickupInput.addEventListener('input', function() {
                clearTimeout(pickupTimeout);
                const query = this.value.trim();
                if (query.length < 2) { pickupDropdown.classList.remove('show'); return; }
                pickupTimeout = setTimeout(() => {
                    searchLocation(query, pickupDropdown, this, 'pickup', stopNumber);
                }, 300);
            });

            dropInput.addEventListener('input', function() {
                clearTimeout(dropTimeout);
                const query = this.value.trim();
                if (query.length < 2) { dropDropdown.classList.remove('show'); return; }
                dropTimeout = setTimeout(() => {
                    searchLocation(query, dropDropdown, this, 'drop', stopNumber);
                }, 300);
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.autocomplete-container')) {
                    pickupDropdown.classList.remove('show');
                    dropDropdown.classList.remove('show');
                }
            });
        }

        function searchLocation(query, dropdown, input, type, stopNumber) {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&countrycodes=in`)
                .then(response => response.json())
                .then(data => {
                    dropdown.innerHTML = '';
                    if (data && data.length > 0) {
                        data.forEach(result => {
                            const item = document.createElement('div');
                            item.className = 'autocomplete-item';
                            const displayName = result.display_name;
                            const lat = parseFloat(result.lat);
                            const lon = parseFloat(result.lon);
                            const highlighted = displayName.replace(
                                new RegExp(query, 'gi'),
                                match => `<span class="highlight">${match}</span>`
                            );
                            item.innerHTML = highlighted;
                            item.dataset.lat = lat;
                            item.dataset.lon = lon;
                            item.dataset.name = displayName;
                            item.addEventListener('click', function() {
                                const lat = parseFloat(this.dataset.lat);
                                const lon = parseFloat(this.dataset.lon);
                                const name = this.dataset.name;
                                input.value = name;
                                dropdown.classList.remove('show');
                                if (!stopCoords[stopNumber]) stopCoords[stopNumber] = { pickup: null, drop: null };
                                if (type === 'pickup') {
                                    stopCoords[stopNumber].pickup = { lat: lat, lng: lon };
                                } else {
                                    stopCoords[stopNumber].drop = { lat: lat, lng: lon };
                                }
                                updateMap();
                            });
                            dropdown.appendChild(item);
                        });
                        dropdown.classList.add('show');
                    } else {
                        dropdown.classList.remove('show');
                    }
                })
                .catch(error => {
                    console.error('Error searching location:', error);
                    dropdown.classList.remove('show');
                });
        }

        // =============================================
        // MAP - INDEPENDENT STOPS WITH INDIVIDUAL DISTANCES
        // =============================================

        function initMap() {
            const defaultCenter = [9.4981, 76.3388];
            map = L.map('map', { center: defaultCenter, zoom: 12, zoomControl: true });
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);
        }

        function updateMap() {
            if (routeLayer) { 
                map.removeLayer(routeLayer); 
                routeLayer = null; 
            }
            markers.forEach(m => map.removeLayer(m));
            markers = [];

            const allCoords = [];
            const stopElements = document.querySelectorAll('.stop-card');

            stopElements.forEach((stopEl, index) => {
                const stopNum = index + 1;
                const pickupInput = stopEl.querySelector('.pickup-input');
                const dropInput = stopEl.querySelector('.drop-input');
                
                if (pickupInput && pickupInput.value && stopCoords[stopNum] && stopCoords[stopNum].pickup) {
                    const coords = stopCoords[stopNum].pickup;
                    allCoords.push(coords);
                    addMarker(coords.lat, coords.lng, 'P' + stopNum, '#28a745');
                }
                
                if (dropInput && dropInput.value && stopCoords[stopNum] && stopCoords[stopNum].drop) {
                    const coords = stopCoords[stopNum].drop;
                    allCoords.push(coords);
                    addMarker(coords.lat, coords.lng, 'D' + stopNum, '#dc3545');
                }
            });

            if (allCoords.length > 1) {
                drawIndependentRoutes();
                const bounds = L.latLngBounds(allCoords.map(c => [c.lat, c.lng]));
                map.fitBounds(bounds, { padding: [50, 50] });
            } else if (allCoords.length === 1) {
                map.setView([allCoords[0].lat, allCoords[0].lng], 14);
            }
        }

        function drawIndependentRoutes() {
            const stopElements = document.querySelectorAll('.stop-card');
            const perKmCharge = parseFloat(document.getElementById('perKmCharge').value) || 0;

            // Clear previous distance info
            const container = document.getElementById('stopDistancesContainer');
            container.innerHTML = '';

            stopElements.forEach((stopEl, index) => {
                const stopNum = index + 1;
                const pickupCoords = stopCoords[stopNum] ? stopCoords[stopNum].pickup : null;
                const dropCoords = stopCoords[stopNum] ? stopCoords[stopNum].drop : null;

                if (pickupCoords && dropCoords) {
                    const latLngs = [
                        [pickupCoords.lat, pickupCoords.lng],
                        [dropCoords.lat, dropCoords.lng]
                    ];

                    const d = calculateDistance(
                        pickupCoords.lat, pickupCoords.lng,
                        dropCoords.lat, dropCoords.lng
                    );

                    const color = routeColors[index % routeColors.length];

                    // Draw route
                    L.polyline(latLngs, {
                        color: color,
                        weight: 3,
                        opacity: 0.8,
                        lineJoin: 'round'
                    }).addTo(map);

                    // Add distance label on map
                    const midLat = (pickupCoords.lat + dropCoords.lat) / 2;
                    const midLng = (pickupCoords.lng + dropCoords.lng) / 2;

                    L.marker([midLat, midLng], {
                        icon: L.divIcon({
                            className: 'distance-label',
                            html: `<span style="background:#123b4f;color:#fff;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:600;">${d.toFixed(1)} km</span>`,
                            iconSize: [60, 20],
                            iconAnchor: [30, 10]
                        })
                    }).addTo(map);

                    // Update stop card
                    const distanceSpan = stopEl.querySelector('.stop-distance');
                    const priceSpan = stopEl.querySelector('.stop-price-amount');
                    if (distanceSpan) {
                        distanceSpan.textContent = d.toFixed(1);
                    }
                    if (priceSpan) {
                        priceSpan.textContent = '<?= htmlspecialchars($currencySymbol) ?>' + (d * perKmCharge).toFixed(2);
                    }

                    // Add per-stop distance to the info container
                    const stopInfo = document.createElement('div');
                    stopInfo.style.cssText = 'display:flex;justify-content:space-between;padding:4px 0;border-bottom:1px solid #f0f3f7;font-size:0.8rem;';
                    stopInfo.innerHTML = `
                        <span>
                            <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:${color};margin-right:8px;"></span>
                            Stop ${stopNum}
                        </span>
                        <span><strong>${d.toFixed(1)} km</strong> (${'<?= htmlspecialchars($currencySymbol) ?>' + (d * perKmCharge).toFixed(2)})</span>
                    `;
                    container.appendChild(stopInfo);
                }
            });

            // Show the container
            document.getElementById('directionsInfo').classList.add('show');
        }

        function addMarker(lat, lng, label, color) {
            const icon = L.divIcon({
                className: 'custom-marker',
                html: label,
                iconSize: [30, 30],
                iconAnchor: [15, 30]
            });
            const marker = L.marker([lat, lng], { icon: icon }).addTo(map);
            markers.push(marker);
        }

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        // =============================================
        // FORM SUBMISSION
        // =============================================

        document.addEventListener('DOMContentLoaded', function() {
            initMap();
            addStop();
            setupProvideIconUpload();

            const form = document.getElementById('travelBookingForm');

            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const carSelect = document.getElementById('carSelect');
                    const carName = document.getElementById('carName').value;
                    const carType = document.getElementById('carType').value;
                    const seatCount = document.getElementById('seatCount').value;
                    const pricePerDay = document.getElementById('pricePerDay').value;
                    const perKmCharge = document.getElementById('perKmCharge').value;
                    const days = document.getElementById('days').value;

                    if (!carSelect.value) {
                        Swal.fire({ icon: 'error', title: 'Validation Error', text: 'Please select a car' });
                        return;
                    }

                    const stops = [];
                    const stopElements = document.querySelectorAll('.stop-card');
                    let isValid = true;

                    stopElements.forEach((stopEl, index) => {
                        const pickup = stopEl.querySelector('.pickup-input').value.trim();
                        const drop = stopEl.querySelector('.drop-input').value.trim();

                        if (!pickup || !drop) {
                            isValid = false;
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: `Please fill both pickup and drop for Stop ${index + 1}`
                            });
                            return;
                        }

                        const stopNum = index + 1;
                        const pickupCoords = stopCoords[stopNum] ? stopCoords[stopNum].pickup : null;
                        const dropCoords = stopCoords[stopNum] ? stopCoords[stopNum].drop : null;

                        let distance = 0;
                        if (pickupCoords && dropCoords) {
                            distance = calculateDistance(
                                pickupCoords.lat, pickupCoords.lng,
                                dropCoords.lat, dropCoords.lng
                            );
                        }

                        stops.push({
                            pickup: pickup,
                            drop: drop,
                            pickup_lat: pickupCoords ? pickupCoords.lat : null,
                            pickup_lng: pickupCoords ? pickupCoords.lng : null,
                            drop_lat: dropCoords ? dropCoords.lat : null,
                            drop_lng: dropCoords ? dropCoords.lng : null,
                            distance: distance,
                            price: distance * parseFloat(perKmCharge || 0)
                        });
                    });

                    if (!isValid) return;

                    const dayPrice = parseFloat(pricePerDay) * parseInt(days);
                    let kmPrice = 0;
                    stops.forEach(s => kmPrice += s.price);
                    const totalPrice = dayPrice + kmPrice;
                    const totalDistance = stops.reduce((sum, s) => sum + s.distance, 0);

                    const provideData = provideItems.map(item => ({
                        name: item.name,
                        icon: item.icon || null
                    }));

                    const submitBtn = document.getElementById('submitBtn');
                    const submitText = document.getElementById('submitText');
                    const submitSpinner = document.getElementById('submitSpinner');
                    submitBtn.disabled = true;
                    submitText.style.display = 'none';
                    submitSpinner.style.display = 'inline-block';

                    const formData = new FormData();
                    formData.append('car_id', carSelect.value);
                    formData.append('car_name', carName);
                    formData.append('car_type', carType);
                    formData.append('seat_count', seatCount);
                    formData.append('days', days);
                    formData.append('per_day_price', pricePerDay);
                    formData.append('per_km_charge', perKmCharge);
                    formData.append('stops', JSON.stringify(stops));
                    formData.append('total_price', totalPrice);
                    formData.append('total_distance', totalDistance);
                    formData.append('what_we_provide', JSON.stringify(provideData));

                    provideItems.forEach((item) => {
                        if (item.iconFile) {
                            formData.append('provide_icons[]', item.iconFile);
                            formData.append('provide_icon_names[]', item.name);
                        }
                    });

                    fetch('ajax/add-book-travel.php', {
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
                                title: 'Booking Confirmed!',
                                text: data.message + ' (Booking ID: ' + data.booking_id + ')',
                                timer: 3000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = 'travel-bookings.php';
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Booking Failed', text: data.message });
                        }
                    })
                    .catch(error => {
                        submitBtn.disabled = false;
                        submitText.style.display = 'inline';
                        submitSpinner.style.display = 'none';
                        console.error('Error:', error);
                        Swal.fire({ icon: 'error', title: 'Error!', text: 'An error occurred. Please try again.' });
                    });
                });
            }
        });

        window.addStop = addStop;
        window.removeStop = removeStop;
        window.initMap = initMap;
        window.addProvideItem = addProvideItem;
        window.removeProvideItem = removeProvideItem;
        window.removeProvideIconPreview = removeProvideIconPreview;
    </script>
</body>

</html>