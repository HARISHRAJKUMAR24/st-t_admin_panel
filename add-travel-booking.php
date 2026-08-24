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

// Fetch vehicles from database
$stmt = $pdo->query("SELECT id, vehicle_name, vehicle_image, per_day_amount, per_km_charge, seating_capacity, vehicle_type, vehicle_brand, pricing_type, package_days, package_price, package_km_limit, extra_km_charge FROM vehicles WHERE status = 'available' ORDER BY vehicle_name ASC");
$vehicles = $stmt->fetchAll();

// Force vehicle options
$forceVehicles = [
    'urbania' => [
        'name' => 'Force Urbania',
        'seats' => [10 => '10-Seater (Driver + 9 passengers)', 13 => '13-Seater (Driver + 12 passengers)', 17 => '17-Seater (Driver + 16 passengers)']
    ],
    'traveller' => [
        'name' => 'Force Traveller',
        'seats' => [9 => '9-Seater (Driver + 8 passengers)', 12 => '12-Seater (Driver + 11 passengers)', 13 => '13-Seater (Driver + 12 passengers)', 14 => '14-Seater (Driver + 13 passengers)', 17 => '17-Seater (Driver + 16 passengers)', 20 => '20-Seater (Driver + 19 passengers)', 26 => '26-Seater (Driver + 25 passengers)']
    ],
    'trax' => [
        'name' => 'Force Trax Cruiser',
        'seats' => [10 => '10-Seater (Driver + 9 passengers)', 12 => '12-Seater (Driver + 11 passengers)', 13 => '13-Seater (Driver + 12 passengers)']
    ]
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>Travel Booking · Tour Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .vehicle-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            margin-bottom: 15px;
        }

        .vehicle-option-card {
            padding: 15px;
            border: 2px solid #e8edf3;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.4);
            text-align: center;
        }

        .vehicle-option-card:hover {
            border-color: #ffd966;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .vehicle-option-card.active {
            border-color: #ffd966;
            background: rgba(255, 215, 100, 0.1);
            box-shadow: 0 0 0 4px rgba(255, 215, 100, 0.1);
        }

        .vehicle-option-card .card-icon {
            font-size: 1.8rem;
            color: #123b4f;
            display: block;
            margin-bottom: 5px;
        }

        .vehicle-option-card .card-title {
            font-weight: 600;
            color: #123b4f;
            font-size: 0.85rem;
        }

        .vehicle-option-card .card-desc {
            color: #5f7d92;
            font-size: 0.65rem;
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

        .stop-card .stop-price-row {
            display: flex;
            gap: 12px;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px dashed #e8edf3;
            flex-wrap: wrap;
        }

        .stop-card .stop-price-row .form-group {
            flex: 1;
            min-width: 100px;
        }

        .stop-card .stop-price-row .form-group label {
            font-size: 0.7rem;
            color: #5f7d92;
            font-weight: 500;
        }

        .stop-card .stop-price-row .form-group .form-control {
            font-size: 0.8rem;
            padding: 0.3rem 0.6rem;
        }

        .stop-card .stop-total-price {
            font-size: 0.9rem;
            font-weight: 600;
            color: #28a745;
            margin-top: 6px;
            text-align: right;
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

        .distance-label {
            background: transparent !important;
            border: none !important;
        }

        .seat-options {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }

        .seat-option {
            padding: 4px 12px;
            border: 2px solid #e8edf3;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.7rem;
            font-weight: 500;
            color: #5f7d92;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.4);
        }

        .seat-option:hover {
            border-color: #ffd966;
            color: #123b4f;
        }

        .seat-option.active {
            border-color: #ffd966;
            background: rgba(255, 215, 100, 0.15);
            color: #123b4f;
        }

        .vehicle-selection-wrapper {
            background: rgba(255, 255, 255, 0.4);
            border: 2px solid #e8edf3;
            border-radius: 12px;
            padding: 15px;
            transition: all 0.3s ease;
        }

        .vehicle-selection-wrapper:focus-within {
            border-color: #ffd966;
        }

        .vehicle-image-upload {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .vehicle-image-upload .image-upload-box {
            border: 2px dashed #e8edf3;
            border-radius: 10px;
            padding: 0.8rem 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: rgba(255, 255, 255, 0.4);
            min-height: 80px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
            min-width: 150px;
        }

        .vehicle-image-upload .image-upload-box:hover {
            border-color: #ffd966;
            background: rgba(255, 215, 100, 0.05);
        }

        .vehicle-image-upload .image-upload-box i {
            font-size: 1.8rem;
            color: #9bb2c5;
        }

        .vehicle-image-upload .image-upload-box p {
            color: #5f7d92;
            font-size: 0.7rem;
            margin-bottom: 0;
        }

        .vehicle-image-upload .image-preview {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #e8edf3;
            flex-shrink: 0;
            display: none;
        }

        .vehicle-image-upload .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .vehicle-image-upload .image-preview.show {
            display: block;
        }

        .pricing-fields-manual .form-control {
            background: white;
        }

        .auto-fill-btn {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            border: 1px solid #28a745;
            border-radius: 6px;
            padding: 0.2rem 0.8rem;
            font-size: 0.65rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            white-space: nowrap;
        }

        .auto-fill-btn:hover {
            background: #28a745;
            color: white;
        }

        .force-image-upload {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #e8edf3;
        }

        .force-image-upload .image-upload-box {
            border: 2px dashed #e8edf3;
            border-radius: 10px;
            padding: 0.5rem 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: rgba(255, 255, 255, 0.4);
            min-height: 60px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .force-image-upload .image-upload-box:hover {
            border-color: #ffd966;
            background: rgba(255, 215, 100, 0.05);
        }

        .force-image-upload .image-upload-box i {
            font-size: 1.5rem;
            color: #9bb2c5;
        }

        .force-image-upload .image-upload-box p {
            color: #5f7d92;
            font-size: 0.65rem;
            margin-bottom: 0;
        }

        .force-image-upload .image-preview {
            width: 60px;
            height: 60px;
            border-radius: 6px;
            overflow: hidden;
            border: 2px solid #e8edf3;
            flex-shrink: 0;
            display: none;
        }

        .force-image-upload .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .force-image-upload .image-preview.show {
            display: block;
        }

        .force-image-upload .preview-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 8px;
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

            .vehicle-options {
                grid-template-columns: 1fr 1fr;
            }

            .vehicle-image-upload {
                flex-direction: column;
            }

            .vehicle-image-upload .image-upload-box {
                width: 100%;
            }

            .stop-card .stop-price-row {
                flex-direction: column;
            }

            .stop-card .stop-price-row .form-group {
                min-width: 100%;
            }

            .force-image-upload .preview-wrapper {
                flex-direction: column;
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
                <p>Select a vehicle, add stops, and book your travel</p>
            </div>

            <div class="form-container">
                <form id="travelBookingForm" enctype="multipart/form-data">
                    <div class="row-2col">
                        <!-- Left Column -->
                        <div>
                            <!-- Vehicle Selection -->
                            <div class="section-title">
                                <i class="bi bi-car-front"></i> Select Vehicle
                            </div>

                            <!-- Vehicle Option Cards -->
                            <div class="vehicle-options">
                                <div class="vehicle-option-card active" data-option="db" onclick="selectVehicleOption('db')">
                                    <span class="card-icon"><i class="bi bi-database"></i></span>
                                    <div class="card-title">From Database</div>
                                    <div class="card-desc">Select from existing vehicles</div>
                                </div>
                                <div class="vehicle-option-card" data-option="urbania" onclick="selectVehicleOption('urbania')">
                                    <span class="card-icon"><i class="bi bi-truck"></i></span>
                                    <div class="card-title">Force Urbania</div>
                                    <div class="card-desc">10, 13, 17 Seater</div>
                                </div>
                                <div class="vehicle-option-card" data-option="traveller" onclick="selectVehicleOption('traveller')">
                                    <span class="card-icon"><i class="bi bi-truck-front"></i></span>
                                    <div class="card-title">Force Traveller</div>
                                    <div class="card-desc">9, 12, 13, 14, 17, 20, 26 Seater</div>
                                </div>
                                <div class="vehicle-option-card" data-option="trax" onclick="selectVehicleOption('trax')">
                                    <span class="card-icon"><i class="bi bi-truck"></i></span>
                                    <div class="card-title">Force Trax Cruiser</div>
                                    <div class="card-desc">10, 12, 13 Seater</div>
                                </div>
                                <div class="vehicle-option-card" data-option="custom" onclick="selectVehicleOption('custom')">
                                    <span class="card-icon"><i class="bi bi-pencil-square"></i></span>
                                    <div class="card-title">Custom</div>
                                    <div class="card-desc">Manual vehicle entry</div>
                                </div>
                            </div>

                            <!-- Vehicle Selection Area -->
                            <div class="vehicle-selection-wrapper" id="vehicleSelectionWrapper">
                                <!-- Database Vehicles -->
                                <div id="dbVehicles">
                                    <div class="mb-3">
                                        <label class="form-label">Select Vehicle <span class="required">*</span></label>
                                        <select class="form-select" id="dbVehicleSelect" onchange="selectDbVehicle(this)">
                                            <option value="">-- Select a Vehicle --</option>
                                            <?php foreach ($vehicles as $vehicle): ?>
                                                <option value="<?= $vehicle['id'] ?>"
                                                    data-name="<?= htmlspecialchars($vehicle['vehicle_name']) ?>"
                                                    data-type="<?= htmlspecialchars($vehicle['vehicle_type'] ?? 'Sedan') ?>"
                                                    data-brand="<?= htmlspecialchars($vehicle['vehicle_brand'] ?? '') ?>"
                                                    data-price="<?= $vehicle['per_day_amount'] ?>"
                                                    data-perkm="<?= $vehicle['per_km_charge'] ?>"
                                                    data-seats="<?= $vehicle['seating_capacity'] ?>"
                                                    data-pricing="<?= $vehicle['pricing_type'] ?? 'perday' ?>"
                                                    data-package-days="<?= $vehicle['package_days'] ?? '' ?>"
                                                    data-package-price="<?= $vehicle['package_price'] ?? '' ?>"
                                                    data-package-km="<?= $vehicle['package_km_limit'] ?? '' ?>"
                                                    data-extra-km="<?= $vehicle['extra_km_charge'] ?? '' ?>">
                                                    <?= htmlspecialchars($vehicle['vehicle_name']) ?> - <?= htmlspecialchars($vehicle['vehicle_type'] ?? 'Sedan') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Force Vehicles -->
                                <div id="forceVehicles" style="display:none;">
                                    <div class="mb-3">
                                        <label class="form-label">Select Force Vehicle <span class="required">*</span></label>
                                        <select class="form-select" id="forceVehicleSelect" onchange="selectForceVehicle(this)">
                                            <option value="">-- Select Force Vehicle --</option>
                                            <option value="urbania">Force Urbania</option>
                                            <option value="traveller">Force Traveller</option>
                                            <option value="trax">Force Trax Cruiser</option>
                                        </select>
                                    </div>
                                    <div id="seatOptionsContainer" style="display:none;">
                                        <label class="form-label">Select Seating <span class="required">*</span></label>
                                        <div class="seat-options" id="seatOptions"></div>
                                    </div>
                                    <!-- Force Vehicle Image Upload -->
                                    <div class="force-image-upload" id="forceImageUpload" style="display:none;">
                                        <label class="form-label">Vehicle Image <span class="required">*</span></label>
                                        <div class="preview-wrapper">
                                            <div class="image-upload-box" id="forceImageBox">
                                                <i class="bi bi-cloud-upload"></i>
                                                <p>Upload Image<br><small>JPG, PNG, WebP</small></p>
                                                <input type="file" id="forceImage" accept="image/*" style="display:none;">
                                            </div>
                                            <div class="image-preview" id="forceImagePreview">
                                                <img id="forceImagePreviewImg" src="" alt="Vehicle Image">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Custom Vehicle -->
                                <div id="customVehicle" style="display:none;">
                                    <div class="mb-3">
                                        <label class="form-label">Vehicle Name <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="customVehicleName" placeholder="Enter vehicle name">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Seating Capacity <span class="required">*</span></label>
                                        <input type="number" class="form-control" id="customSeats" placeholder="Number of seats" min="1">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Vehicle Type</label>
                                        <input type="text" class="form-control" id="customVehicleType" placeholder="e.g., Luxury, Mini, SUV">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Vehicle Image <span class="required">*</span></label>
                                        <div class="vehicle-image-upload">
                                            <div class="image-upload-box" id="vehicleImageBox">
                                                <i class="bi bi-cloud-upload"></i>
                                                <p>Upload Image<br><small>JPG, PNG, WebP</small></p>
                                                <input type="file" id="vehicleImage" accept="image/*" style="display:none;">
                                            </div>
                                            <div class="image-preview" id="vehicleImagePreview">
                                                <img id="vehicleImagePreviewImg" src="" alt="Vehicle Image">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden fields for selected vehicle -->
                            <input type="hidden" id="selectedVehicleId" value="">
                            <input type="hidden" id="selectedVehicleName" value="">
                            <input type="hidden" id="selectedVehicleType" value="">
                            <input type="hidden" id="selectedSeats" value="">
                            <input type="hidden" id="selectedPricingType" value="perday">
                            <input type="hidden" id="selectedPerDayAmount" value="0">
                            <input type="hidden" id="selectedPerKmCharge" value="0">

                            <!-- Vehicle Details Display -->
                            <div id="vehicleDetails" style="display:none; margin-top:15px; padding:15px; background:rgba(255,255,255,0.5); border-radius:12px; border:2px solid #e8edf3;">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label">Vehicle Name</label>
                                        <input type="text" class="form-control" id="displayVehicleName" readonly>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Seats</label>
                                        <input type="text" class="form-control" id="displaySeats" readonly>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Type</label>
                                        <input type="text" class="form-control" id="displayVehicleType" readonly>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label">Pricing Type</label>
                                        <input type="text" class="form-control" id="displayPricingType" readonly value="Per Day + KM">
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing Section - MANUAL ENTRY ENABLED -->
                            <div class="section-title mt-3">
                                <i class="bi bi-tag"></i> Pricing (Manual Entry)
                            </div>

                            <div class="price-input-group">
                                <div class="form-group">
                                    <label class="form-label">Price Per Day <span class="required">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text" style="border-radius:10px 0 0 10px;border:2px solid #e8edf3;border-right:none;background:rgba(255,255,255,0.6);font-weight:600;">
                                            <?= htmlspecialchars($currencySymbol) ?>
                                        </span>
                                        <input type="number" class="form-control" id="pricePerDay" step="0.01" placeholder="0.00" style="border-radius:0 10px 10px 0;">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Per KM Charge <span class="required">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text" style="border-radius:10px 0 0 10px;border:2px solid #e8edf3;border-right:none;background:rgba(255,255,255,0.6);font-weight:600;">
                                            <?= htmlspecialchars($currencySymbol) ?>
                                        </span>
                                        <input type="number" class="form-control" id="perKmCharge" step="0.01" placeholder="0.00" style="border-radius:0 10px 10px 0;">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3 mt-3">
                                <label class="form-label">Number of Days <span class="required">*</span></label>
                                <input type="number" class="form-control" id="days" value="1" min="1" required>
                            </div>

                            <!-- What We Provide - TEXT ONLY -->
                            <div class="section-title mt-3">
                                <i class="bi bi-check-circle"></i> What We Provide
                            </div>

                            <div class="mb-3">
                                <div class="badge-input-wrapper">
                                    <div class="badge-input-row">
                                        <input type="text" class="form-control" id="provideInput" placeholder="Enter item name" style="flex:1;">
                                        <button type="button" class="btn-sm-primary" onclick="addProvideItem()" style="background:#ffd966;color:#123b4f;border:none;border-radius:8px;padding:0.4rem 1.2rem;font-weight:600;font-size:0.8rem;cursor:pointer;">
                                            <i class="bi bi-plus-circle"></i> Add
                                        </button>
                                    </div>
                                    <div class="badges-container" id="provideContainer">
                                        <div class="empty-badges">No items added</div>
                                    </div>
                                </div>
                                <small class="text-muted" style="font-size:0.7rem;">Add items that you provide with this travel package</small>
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
        let routeColors = ['#f5b342', '#4CAF50', '#2196F3', '#9C27B0', '#FF5722', '#00BCD4', '#FF9800', '#8BC34A'];
        let selectedOption = 'db';
        let vehicleImageFile = null;
        let forceImageFile = null;

        // Force vehicle seat options
        const forceSeats = {
            'urbania': {
                10: '10-Seater (Driver + 9 passengers)',
                13: '13-Seater (Driver + 12 passengers)',
                17: '17-Seater (Driver + 16 passengers)'
            },
            'traveller': {
                9: '9-Seater (Driver + 8 passengers)',
                12: '12-Seater (Driver + 11 passengers)',
                13: '13-Seater (Driver + 12 passengers)',
                14: '14-Seater (Driver + 13 passengers)',
                17: '17-Seater (Driver + 16 passengers)',
                20: '20-Seater (Driver + 19 passengers)',
                26: '26-Seater (Driver + 25 passengers)'
            },
            'trax': {
                10: '10-Seater (Driver + 9 passengers)',
                12: '12-Seater (Driver + 11 passengers)',
                13: '13-Seater (Driver + 12 passengers)'
            }
        };

        const forceVehicleNames = {
            'urbania': 'Force Urbania',
            'traveller': 'Force Traveller',
            'trax': 'Force Trax Cruiser'
        };

        // =============================================
        // FORCE VEHICLE IMAGE UPLOAD
        // =============================================

        function setupForceImageUpload() {
            const box = document.getElementById('forceImageBox');
            const input = document.getElementById('forceImage');
            const preview = document.getElementById('forceImagePreview');
            const previewImg = document.getElementById('forceImagePreviewImg');

            if (box && input) {
                const newBox = box.cloneNode(true);
                box.parentNode.replaceChild(newBox, box);

                newBox.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    input.click();
                });

                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        if (file.size > 2 * 1024 * 1024) {
                            Swal.fire({
                                icon: 'error',
                                title: 'File Too Large',
                                text: 'Image must be less than 2MB'
                            });
                            this.value = '';
                            return;
                        }
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                        if (!allowedTypes.includes(file.type)) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid File Type',
                                text: 'Please upload JPG, PNG, GIF, or WebP'
                            });
                            this.value = '';
                            return;
                        }
                        forceImageFile = file;
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImg.src = e.target.result;
                            preview.classList.add('show');
                            Swal.fire({
                                icon: 'success',
                                title: 'Image Uploaded!',
                                text: 'Vehicle image uploaded successfully.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        }

        // =============================================
        // VEHICLE IMAGE UPLOAD (Custom)
        // =============================================

        function setupVehicleImageUpload() {
            const box = document.getElementById('vehicleImageBox');
            const input = document.getElementById('vehicleImage');
            const preview = document.getElementById('vehicleImagePreview');
            const previewImg = document.getElementById('vehicleImagePreviewImg');

            if (box && input) {
                const newBox = box.cloneNode(true);
                box.parentNode.replaceChild(newBox, box);

                newBox.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    input.click();
                });

                input.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        if (file.size > 2 * 1024 * 1024) {
                            Swal.fire({
                                icon: 'error',
                                title: 'File Too Large',
                                text: 'Image must be less than 2MB'
                            });
                            this.value = '';
                            return;
                        }
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                        if (!allowedTypes.includes(file.type)) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid File Type',
                                text: 'Please upload JPG, PNG, GIF, or WebP'
                            });
                            this.value = '';
                            return;
                        }
                        vehicleImageFile = file;
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImg.src = e.target.result;
                            preview.classList.add('show');
                            Swal.fire({
                                icon: 'success',
                                title: 'Image Uploaded!',
                                text: 'Vehicle image uploaded successfully.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        }

        // =============================================
        // WHAT WE PROVIDE - TEXT ONLY
        // =============================================

        function addProvideItem() {
            const input = document.getElementById('provideInput');
            const name = input.value.trim();
            if (!name) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Item Name Required',
                    text: 'Please enter an item name'
                });
                input.focus();
                return;
            }
            const existing = provideItems.find(i => i.toLowerCase() === name.toLowerCase());
            if (existing) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Duplicate Item',
                    text: `"${name}" already exists`
                });
                input.focus();
                return;
            }

            provideItems.push(name);
            renderProvideItems();
            input.value = '';
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
                badge.innerHTML = `
                    <span class="badge-name">${escapeHtml(item)}</span>
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
        // VEHICLE SELECTION
        // =============================================

        function selectVehicleOption(option) {
            selectedOption = option;

            document.querySelectorAll('.vehicle-option-card').forEach(card => {
                card.classList.remove('active');
                if (card.dataset.option === option) {
                    card.classList.add('active');
                }
            });

            document.getElementById('dbVehicles').style.display = 'none';
            document.getElementById('forceVehicles').style.display = 'none';
            document.getElementById('customVehicle').style.display = 'none';
            document.getElementById('forceImageUpload').style.display = 'none';
            document.getElementById('vehicleDetails').style.display = 'none';

            if (option === 'db') {
                document.getElementById('dbVehicles').style.display = 'block';
            } else if (option === 'urbania' || option === 'traveller' || option === 'trax') {
                document.getElementById('forceVehicles').style.display = 'block';
                document.getElementById('forceImageUpload').style.display = 'block';
                document.getElementById('forceVehicleSelect').value = option;
                showSeatOptions(option);
            } else if (option === 'custom') {
                document.getElementById('customVehicle').style.display = 'block';
            }

            clearVehicleSelection();
        }

        function showSeatOptions(type) {
            const container = document.getElementById('seatOptions');
            const containerDiv = document.getElementById('seatOptionsContainer');
            container.innerHTML = '';

            const seats = forceSeats[type];
            if (!seats) {
                containerDiv.style.display = 'none';
                return;
            }

            containerDiv.style.display = 'block';

            Object.keys(seats).forEach(seat => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'seat-option';
                btn.textContent = seats[seat];
                btn.dataset.seats = seat;
                btn.onclick = function() {
                    document.querySelectorAll('.seat-option').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const vehicleName = forceVehicleNames[type];
                    document.getElementById('selectedVehicleName').value = vehicleName;
                    document.getElementById('selectedSeats').value = seat;
                    document.getElementById('selectedVehicleType').value = 'Force';
                    document.getElementById('selectedVehicleId').value = 0;

                    document.getElementById('displayVehicleName').value = vehicleName;
                    document.getElementById('displaySeats').value = seat + ' Seats';
                    document.getElementById('displayVehicleType').value = 'Force';
                    document.getElementById('vehicleDetails').style.display = 'block';

                    document.getElementById('pricePerDay').readOnly = false;
                    document.getElementById('perKmCharge').readOnly = false;
                    document.getElementById('pricePerDay').value = '';
                    document.getElementById('perKmCharge').value = '';

                    const event = new Event('input');
                    document.getElementById('perKmCharge').dispatchEvent(event);
                };
                container.appendChild(btn);
            });
        }

        function selectForceVehicle(select) {
            const type = select.value;
            if (type) {
                showSeatOptions(type);
                document.getElementById('forceImageUpload').style.display = 'block';
            } else {
                document.getElementById('seatOptionsContainer').style.display = 'none';
                document.getElementById('forceImageUpload').style.display = 'none';
                document.getElementById('vehicleDetails').style.display = 'none';
                clearVehicleSelection();
            }
        }

        function selectDbVehicle(select) {
            const option = select.options[select.selectedIndex];
            if (select.value) {
                const vehicleName = option.dataset.name;
                const seats = option.dataset.seats;
                const vehicleType = option.dataset.type;
                const price = option.dataset.price;
                const perKm = option.dataset.perkm;

                document.getElementById('selectedVehicleId').value = select.value;
                document.getElementById('selectedVehicleName').value = vehicleName;
                document.getElementById('selectedSeats').value = seats;
                document.getElementById('selectedVehicleType').value = vehicleType;
                document.getElementById('selectedPerDayAmount').value = price;
                document.getElementById('selectedPerKmCharge').value = perKm;

                document.getElementById('displayVehicleName').value = vehicleName;
                document.getElementById('displaySeats').value = seats + ' Seats';
                document.getElementById('displayVehicleType').value = vehicleType;
                document.getElementById('vehicleDetails').style.display = 'block';

                document.getElementById('pricePerDay').value = price;
                document.getElementById('perKmCharge').value = perKm;
                document.getElementById('pricePerDay').readOnly = false;
                document.getElementById('perKmCharge').readOnly = false;
            } else {
                clearVehicleSelection();
                document.getElementById('vehicleDetails').style.display = 'none';
                document.getElementById('pricePerDay').value = '';
                document.getElementById('perKmCharge').value = '';
            }
        }

        function clearVehicleSelection() {
            document.getElementById('selectedVehicleId').value = '';
            document.getElementById('selectedVehicleName').value = '';
            document.getElementById('selectedSeats').value = '';
            document.getElementById('selectedVehicleType').value = '';
            document.getElementById('selectedPerDayAmount').value = '0';
            document.getElementById('selectedPerKmCharge').value = '0';
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
                <div class="stop-price-row">
                    <div class="form-group">
                        <label class="form-label">Distance (km) <span class="required">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control stop-distance-input" step="0.01" placeholder="0.00" value="0">
                            <button type="button" class="auto-fill-btn" onclick="autoFillDistance(${stopNumber})">
                                <i class="bi bi-geo-alt"></i> Auto
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Price for this Stop <span class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text" style="border-radius:10px 0 0 10px;border:2px solid #e8edf3;border-right:none;background:rgba(255,255,255,0.6);font-weight:600;">
                                <?= htmlspecialchars($currencySymbol) ?>
                            </span>
                            <input type="number" class="form-control stop-price-input" step="0.01" placeholder="0.00" value="0" style="border-radius:0 10px 10px 0;">
                        </div>
                    </div>
                </div>
                <div class="stop-total-price">
                    Total: <span class="stop-total-amount"><?= htmlspecialchars($currencySymbol) ?>0.00</span>
                </div>
            `;
            container.appendChild(stopDiv);
            setupStopAutocomplete(stopNumber);
            setupStopPriceCalculation(stopNumber);
            updateStopCount();
        }

        function setupStopPriceCalculation(stopNumber) {
            const stopElement = document.getElementById('stop-' + stopNumber);
            const distanceInput = stopElement.querySelector('.stop-distance-input');
            const priceInput = stopElement.querySelector('.stop-price-input');
            const totalSpan = stopElement.querySelector('.stop-total-amount');
            const perKmCharge = document.getElementById('perKmCharge');

            function calculateTotal() {
                const distance = parseFloat(distanceInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                const perKm = parseFloat(perKmCharge.value) || 0;

                let total = price;
                if (price === 0 && distance > 0) {
                    total = distance * perKm;
                    priceInput.value = total.toFixed(2);
                }

                totalSpan.textContent = '<?= htmlspecialchars($currencySymbol) ?>' + total.toFixed(2);
            }

            distanceInput.addEventListener('input', function() {
                const distance = parseFloat(this.value) || 0;
                const perKm = parseFloat(perKmCharge.value) || 0;
                if (distance > 0) {
                    const calculatedPrice = distance * perKm;
                    const priceInput = stopElement.querySelector('.stop-price-input');
                    priceInput.value = calculatedPrice.toFixed(2);
                }
                calculateTotal();
            });

            priceInput.addEventListener('input', calculateTotal);
            perKmCharge.addEventListener('input', function() {
                const distance = parseFloat(distanceInput.value) || 0;
                if (distance > 0) {
                    const perKm = parseFloat(this.value) || 0;
                    const calculatedPrice = distance * perKm;
                    priceInput.value = calculatedPrice.toFixed(2);
                }
                calculateTotal();
            });

            calculateTotal();
        }

        window.autoFillDistance = function(stopNumber) {
            const stopElement = document.getElementById('stop-' + stopNumber);
            const pickupInput = stopElement.querySelector('.pickup-input');
            const dropInput = stopElement.querySelector('.drop-input');
            const distanceInput = stopElement.querySelector('.stop-distance-input');
            const priceInput = stopElement.querySelector('.stop-price-input');
            const perKmCharge = parseFloat(document.getElementById('perKmCharge').value) || 0;

            const pickupCoords = stopCoords[stopNumber] ? stopCoords[stopNumber].pickup : null;
            const dropCoords = stopCoords[stopNumber] ? stopCoords[stopNumber].drop : null;

            if (pickupCoords && dropCoords) {
                const distance = calculateDistance(
                    pickupCoords.lat, pickupCoords.lng,
                    dropCoords.lat, dropCoords.lng
                );
                distanceInput.value = distance.toFixed(2);
                const calculatedPrice = distance * perKmCharge;
                priceInput.value = calculatedPrice.toFixed(2);

                const event = new Event('input');
                distanceInput.dispatchEvent(event);

                Swal.fire({
                    icon: 'success',
                    title: 'Distance Auto-Filled!',
                    text: `Distance: ${distance.toFixed(2)} km | Price: ${'<?= htmlspecialchars($currencySymbol) ?>'}${calculatedPrice.toFixed(2)}`,
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cannot Auto-Fill',
                    text: 'Please select pickup and drop locations from the map first.',
                    confirmButtonColor: '#0b2a3e'
                });
            }
        };

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
                Swal.fire({
                    icon: 'warning',
                    title: 'Cannot Remove',
                    text: 'You need at least one stop'
                });
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
                if (query.length < 2) {
                    pickupDropdown.classList.remove('show');
                    return;
                }
                pickupTimeout = setTimeout(() => {
                    searchLocation(query, pickupDropdown, this, 'pickup', stopNumber);
                }, 300);
            });

            dropInput.addEventListener('input', function() {
                clearTimeout(dropTimeout);
                const query = this.value.trim();
                if (query.length < 2) {
                    dropDropdown.classList.remove('show');
                    return;
                }
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
                                if (!stopCoords[stopNumber]) stopCoords[stopNumber] = {
                                    pickup: null,
                                    drop: null
                                };
                                if (type === 'pickup') {
                                    stopCoords[stopNumber].pickup = {
                                        lat: lat,
                                        lng: lon
                                    };
                                } else {
                                    stopCoords[stopNumber].drop = {
                                        lat: lat,
                                        lng: lon
                                    };
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
        // MAP
        // =============================================

        function initMap() {
            const defaultCenter = [9.4981, 76.3388];
            map = L.map('map', {
                center: defaultCenter,
                zoom: 12,
                zoomControl: true
            });
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
                map.fitBounds(bounds, {
                    padding: [50, 50]
                });
            } else if (allCoords.length === 1) {
                map.setView([allCoords[0].lat, allCoords[0].lng], 14);
            }
        }

        function drawIndependentRoutes() {
            const stopElements = document.querySelectorAll('.stop-card');

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

                    L.polyline(latLngs, {
                        color: color,
                        weight: 3,
                        opacity: 0.8,
                        lineJoin: 'round'
                    }).addTo(map);

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

                    const distanceInput = stopEl.querySelector('.stop-distance-input');
                    if (distanceInput && parseFloat(distanceInput.value) === 0) {
                        distanceInput.value = d.toFixed(2);
                        const perKmCharge = parseFloat(document.getElementById('perKmCharge').value) || 0;
                        const priceInput = stopEl.querySelector('.stop-price-input');
                        if (priceInput) {
                            priceInput.value = (d * perKmCharge).toFixed(2);
                        }
                        const event = new Event('input');
                        distanceInput.dispatchEvent(event);
                    }

                    const stopInfo = document.createElement('div');
                    stopInfo.style.cssText = 'display:flex;justify-content:space-between;padding:4px 0;border-bottom:1px solid #f0f3f7;font-size:0.8rem;';
                    stopInfo.innerHTML = `
                        <span>
                            <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:${color};margin-right:8px;"></span>
                            Stop ${stopNum}
                        </span>
                        <span><strong>${d.toFixed(1)} km</strong></span>
                    `;
                    container.appendChild(stopInfo);
                }
            });

            document.getElementById('directionsInfo').classList.add('show');
        }

        function addMarker(lat, lng, label, color) {
            const icon = L.divIcon({
                className: 'custom-marker',
                html: label,
                iconSize: [30, 30],
                iconAnchor: [15, 30]
            });
            const marker = L.marker([lat, lng], {
                icon: icon
            }).addTo(map);
            markers.push(marker);
        }

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        // =============================================
        // FORM SUBMISSION
        // =============================================

        document.addEventListener('DOMContentLoaded', function() {
            initMap();
            addStop();
            setupVehicleImageUpload();
            setupForceImageUpload();

            selectVehicleOption('db');

            const form = document.getElementById('travelBookingForm');

            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    let vehicleName = document.getElementById('selectedVehicleName').value;
                    let seats = document.getElementById('selectedSeats').value;
                    let vehicleType = document.getElementById('selectedVehicleType').value;
                    let vehicleImage = null;

                    if (selectedOption === 'custom') {
                        vehicleName = document.getElementById('customVehicleName').value.trim();
                        seats = document.getElementById('customSeats').value.trim();
                        vehicleType = document.getElementById('customVehicleType').value.trim();

                        if (!vehicleName) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: 'Please enter vehicle name'
                            });
                            return;
                        }
                        if (!seats || seats <= 0) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: 'Please enter seating capacity'
                            });
                            return;
                        }
                        if (!vehicleImageFile) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: 'Please upload vehicle image'
                            });
                            return;
                        }

                        document.getElementById('selectedVehicleName').value = vehicleName;
                        document.getElementById('selectedSeats').value = seats;
                        document.getElementById('selectedVehicleType').value = vehicleType || 'Custom';
                        document.getElementById('selectedVehicleId').value = 0;
                        vehicleImage = vehicleImageFile;
                    }

                    if (selectedOption === 'urbania' || selectedOption === 'traveller' || selectedOption === 'trax') {
                        if (!forceImageFile) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: 'Please upload vehicle image'
                            });
                            return;
                        }
                        vehicleImage = forceImageFile;
                    }

                    if (!vehicleName) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: 'Please select a vehicle'
                        });
                        return;
                    }

                    if (!seats || seats <= 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: 'Please select seating capacity'
                        });
                        return;
                    }

                    const pricePerDay = document.getElementById('pricePerDay').value.trim();
                    const perKmCharge = document.getElementById('perKmCharge').value.trim();

                    if (!pricePerDay || parseFloat(pricePerDay) <= 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: 'Please enter valid price per day'
                        });
                        document.getElementById('pricePerDay').focus();
                        return;
                    }
                    if (!perKmCharge || parseFloat(perKmCharge) <= 0) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: 'Please enter valid per KM charge'
                        });
                        document.getElementById('perKmCharge').focus();
                        return;
                    }

                    const stops = [];
                    const stopElements = document.querySelectorAll('.stop-card');
                    let isValid = true;
                    let totalDistance = 0;
                    let totalStopPrice = 0;

                    stopElements.forEach((stopEl, index) => {
                        const pickup = stopEl.querySelector('.pickup-input').value.trim();
                        const drop = stopEl.querySelector('.drop-input').value.trim();
                        const distance = parseFloat(stopEl.querySelector('.stop-distance-input').value) || 0;
                        const price = parseFloat(stopEl.querySelector('.stop-price-input').value) || 0;

                        if (!pickup || !drop) {
                            isValid = false;
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: `Please fill both pickup and drop for Stop ${index + 1}`
                            });
                            return;
                        }

                        if (distance <= 0) {
                            isValid = false;
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: `Please enter distance for Stop ${index + 1}`
                            });
                            return;
                        }

                        if (price <= 0) {
                            isValid = false;
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error',
                                text: `Please enter price for Stop ${index + 1}`
                            });
                            return;
                        }

                        totalDistance += distance;
                        totalStopPrice += price;

                        const stopNum = index + 1;
                        const pickupCoords = stopCoords[stopNum] ? stopCoords[stopNum].pickup : null;
                        const dropCoords = stopCoords[stopNum] ? stopCoords[stopNum].drop : null;

                        stops.push({
                            pickup: pickup,
                            drop: drop,
                            pickup_lat: pickupCoords ? pickupCoords.lat : null,
                            pickup_lng: pickupCoords ? pickupCoords.lng : null,
                            drop_lat: dropCoords ? dropCoords.lat : null,
                            drop_lng: dropCoords ? dropCoords.lng : null,
                            distance: distance,
                            price: price
                        });
                    });

                    if (!isValid) return;

                    const days = parseInt(document.getElementById('days').value) || 1;
                    const dayPrice = parseFloat(pricePerDay) * days;
                    const totalPrice = dayPrice + totalStopPrice;

                    const submitBtn = document.getElementById('submitBtn');
                    const submitText = document.getElementById('submitText');
                    const submitSpinner = document.getElementById('submitSpinner');
                    submitBtn.disabled = true;
                    submitText.style.display = 'none';
                    submitSpinner.style.display = 'inline-block';

                    const formData = new FormData();
                    formData.append('car_id', document.getElementById('selectedVehicleId').value || 0);
                    formData.append('car_name', document.getElementById('selectedVehicleName').value);
                    formData.append('car_type', document.getElementById('selectedVehicleType').value || '');
                    formData.append('seat_count', document.getElementById('selectedSeats').value);
                    formData.append('days', days);
                    formData.append('per_day_price', pricePerDay);
                    formData.append('per_km_charge', perKmCharge);
                    formData.append('stops', JSON.stringify(stops));
                    formData.append('total_price', totalPrice);
                    formData.append('total_distance', totalDistance);
                    formData.append('what_we_provide', JSON.stringify(provideItems));

                    if (vehicleImage) {
                        formData.append('vehicle_image', vehicleImage);
                    }

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
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Booking Failed',
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

        // Make functions globally accessible
        window.selectVehicleOption = selectVehicleOption;
        window.selectForceVehicle = selectForceVehicle;
        window.selectDbVehicle = selectDbVehicle;
        window.addStop = addStop;
        window.removeStop = removeStop;
        window.addProvideItem = addProvideItem;
        window.removeProvideItem = removeProvideItem;
        window.initMap = initMap;
        window.autoFillDistance = autoFillDistance;
    </script>
</body>

</html>