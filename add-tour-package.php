<?php
require_once 'config/config.php';
require_once 'config/function.php';
requireLogin();

// Verify token
if (!verifyToken($pdo)) {
    header("Location: " . APP_URL . "login.php");
    exit();
}

$currentUser = getCurrentUser($pdo);
$pageTitle = "Add Tour Package";

// Get settings for currency
$stmt = $pdo->prepare("SELECT currency FROM settings WHERE id = 1");
$stmt->execute();
$settings = $stmt->fetch();
$currencySymbol = $settings['currency'] ?? 'USD';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>Add Tour Package · Tour Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* ============================================
           BASE & WRAPPER
           ============================================ */
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
            font-size: 0.9rem;
            margin-bottom: 0;
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
            text-decoration: none;
        }

        /* ============================================
           FORM CONTAINER
           ============================================ */
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

        /* ============================================
           FORM ELEMENTS
           ============================================ */
        .form-label {
            font-weight: 500;
            color: #123b4f;
            font-size: 0.8rem;
            margin-bottom: 0.4rem;
        }

        .form-label .required {
            color: #dc3545;
            margin-left: 2px;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            padding: 0.55rem 0.9rem;
            border: 2px solid #e8edf3;
            background: rgba(255, 255, 255, 0.6);
            font-weight: 500;
            transition: all 0.25s ease;
            font-size: 0.85rem;
            color: #123b4f;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #ffd966;
            box-shadow: 0 0 0 4px rgba(255, 215, 100, 0.15);
            background: white;
        }

        .form-control::placeholder {
            color: #9bb2c5;
            font-weight: 400;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        /* ============================================
           BUTTONS
           ============================================ */
        .btn-submit {
            background: linear-gradient(145deg, #0b2a3e 0%, #123b4f 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.65rem 2.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            min-width: 160px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(11, 42, 62, 0.2);
            color: #ffd966;
        }

        .btn-submit:disabled {
            opacity: 0.7;
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

        .btn-add-day {
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

        .btn-add-day:hover {
            background: rgba(255, 215, 100, 0.08);
            border-color: #ffd966;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 2px solid #f0f3f7;
        }

        /* ============================================
           IMAGE UPLOAD
           ============================================ */
        .image-upload-wrapper {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .image-upload-box {
            flex: 0 0 160px;
            border: 2px dashed #e8edf3;
            border-radius: 12px;
            padding: 1.2rem 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.4);
            min-height: 130px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .image-upload-box:hover {
            border-color: #ffd966;
            background: rgba(255, 215, 100, 0.05);
            transform: translateY(-2px);
        }

        .image-upload-box i {
            font-size: 2.2rem;
            color: #9bb2c5;
            transition: all 0.3s ease;
        }

        .image-upload-box:hover i {
            color: #ffd966;
        }

        .image-upload-box p {
            color: #5f7d92;
            margin: 0;
            font-size: 0.7rem;
            line-height: 1.3;
        }

        .image-upload-box p small {
            color: #9bb2c5;
            font-size: 0.6rem;
        }

        .image-preview-wrapper {
            flex: 1;
            min-width: 150px;
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
            transition: all 0.3s ease;
        }

        .image-preview-item:hover {
            border-color: #ffd966;
            transform: scale(1.02);
        }

        .image-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-preview-item .remove-image {
            position: absolute;
            top: 4px;
            right: 4px;
            background: rgba(231, 76, 94, 0.92);
            color: white;
            border: none;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            transition: all 0.2s ease;
            opacity: 0;
        }

        .image-preview-item:hover .remove-image {
            opacity: 1;
        }

        .image-preview-item .remove-image:hover {
            transform: scale(1.15);
            background: #dc3545;
        }

        .image-preview-empty {
            color: #9bb2c5;
            font-size: 0.8rem;
            padding: 0.5rem 0;
        }

        /* ============================================
           FEATURES BADGE
           ============================================ */
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

        /* ============================================
           MEMBERS BADGE
           ============================================ */
        .member-badge {
            display: inline-flex;
            align-items: center;
            background: rgba(18, 59, 79, 0.08);
            color: #123b4f;
            padding: 4px 12px 4px 10px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 500;
            gap: 8px;
            transition: all 0.2s ease;
            border: 1px solid rgba(18, 59, 79, 0.06);
            animation: badgeIn 0.3s ease;
        }

        .member-badge:hover {
            background: rgba(18, 59, 79, 0.12);
            transform: translateY(-1px);
        }

        .member-badge .member-label {
            font-weight: 600;
            color: #123b4f;
        }

        .member-badge .member-count {
            background: rgba(255, 215, 100, 0.3);
            padding: 0 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 700;
            color: #b8860b;
        }

        .member-badge .remove-badge {
            cursor: pointer;
            color: #dc3545;
            font-weight: 700;
            font-size: 1rem;
            line-height: 1;
            padding: 0 2px;
            transition: all 0.2s ease;
        }

        .member-badge .remove-badge:hover {
            transform: scale(1.3);
        }

        /* ============================================
           ITINERARY
           ============================================ */
        .itinerary-day {
            background: rgba(255, 255, 255, 0.5);
            border: 2px solid #e8edf3;
            border-radius: 12px;
            padding: 1.2rem;
            margin-bottom: 12px;
            transition: all 0.3s ease;
            animation: slideDown 0.3s ease;
        }

        .itinerary-day:hover {
            border-color: #d5dce6;
        }

        .itinerary-day .day-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .itinerary-day .day-header .day-label {
            font-weight: 600;
            color: #123b4f;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .itinerary-day .day-header .day-label .day-number {
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

        .itinerary-day .day-header .remove-day {
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

        .itinerary-day .day-header .remove-day:hover {
            opacity: 1;
            background: rgba(220, 53, 69, 0.08);
        }

        .itinerary-day .day-title-input {
            margin-bottom: 8px;
        }

        .itinerary-day .day-title-input .form-control {
            font-weight: 600;
            color: #123b4f;
        }

        /* ============================================
           FEATURE ICON UPLOAD BOX
           ============================================ */
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

        /* ============================================
           ANIMATIONS
           ============================================ */
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

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 992px) {
            .row-2col {
                grid-template-columns: 1fr !important;
                gap: 10px !important;
            }
        }

        @media (max-width: 768px) {
            .page-wrapper {
                padding: 10px;
            }

            .form-container {
                padding: 1rem;
                border-radius: 16px;
            }

            .form-actions {
                flex-direction: column;
            }

            .form-actions .btn {
                width: 100%;
                justify-content: center;
            }

            .image-upload-box {
                flex: 0 0 100%;
                min-height: 100px;
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

            .image-preview-item {
                width: 65px;
                height: 65px;
            }

            .feature-icon-upload-box {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .form-container {
                padding: 0.75rem;
            }

            .section-title {
                font-size: 0.9rem;
            }

            .form-label {
                font-size: 0.75rem;
            }

            .form-control,
            .form-select {
                font-size: 0.8rem;
                padding: 0.45rem 0.7rem;
            }

            .itinerary-day {
                padding: 0.8rem;
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
                <small>Add Tour Package</small>
            </div>
        </div>

        <div class="page-wrapper">
            <a href="tour-packages.php" class="back-link">
                <i class="bi bi-arrow-left me-1"></i> Back to Packages
            </a>

            <div class="page-header">
                <h4><i class="bi bi-plus-circle me-2" style="color:#f5b342;"></i>Add Tour Package</h4>
                <p>Create a new tour package with all details, itinerary, and features</p>
            </div>

            <div class="form-container">
                <form id="tourPackageForm" enctype="multipart/form-data">

                    <!-- ==========================================
                    BASIC INFORMATION
                    ========================================== -->
                    <div class="section-title">
                        <i class="bi bi-info-circle" style="color:#f5b342;"></i> Basic Information
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Package Name <span class="required">*</span></label>
                            <input type="text" class="form-control" id="packageName" placeholder="Enter package name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Package Type</label>
                            <select class="form-select" id="packageType">
                                <option value="">Select Type</option>
                                <option value="Adventure">🏔️ Adventure</option>
                                <option value="Beach">🏖️ Beach</option>
                                <option value="Cultural">🏛️ Cultural</option>
                                <option value="Wildlife">🦁 Wildlife</option>
                                <option value="City Break">🏙️ City Break</option>
                                <option value="Luxury">✨ Luxury</option>
                                <option value="Family">👨‍👩‍👧‍👦 Family</option>
                                <option value="Honeymoon">❤️ Honeymoon</option>
                                <option value="Group">👥 Group</option>
                            </select>
                        </div>
                    </div>

                    <!-- ==========================================
                    DAYS & MEMBERS
                    ========================================== -->
                    <div class="section-title">
                        <i class="bi bi-people" style="color:#f5b342;"></i> Days & Members
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Days <span class="required">*</span></label>
                            <input type="number" class="form-control" id="daysCount" value="1" min="1" required>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label">Members <span class="text-muted">(Badge Style)</span></label>
                            <div class="badge-input-wrapper">
                                <div class="badge-input-row">
                                    <input type="text" class="form-control" id="memberLabel" placeholder="e.g., Adults, Children, Seniors">
                                    <input type="number" class="form-control" id="memberCount" placeholder="Count" min="0" value="0" style="max-width:120px;">
                                    <button type="button" class="btn-sm-primary" onclick="addMember()">
                                        <i class="bi bi-plus-circle"></i> Add
                                    </button>
                                </div>
                                <div class="badges-container" id="membersList">
                                    <div class="empty-badges">No members added yet</div>
                                </div>
                                <input type="hidden" id="members" name="members" value="">
                                <small class="text-muted" style="font-size:0.7rem;">Enter member type and count, then click Add</small>
                            </div>
                        </div>
                    </div>

                    <!-- ==========================================
                    PRICE (Single Price Only)
                    ========================================== -->
                    <div class="section-title">
                        <i class="bi bi-tag" style="color:#f5b342;"></i> Pricing
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Price <span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text" style="border-radius:10px 0 0 10px;border:2px solid #e8edf3;border-right:none;background:rgba(255,255,255,0.6);font-weight:600;">
                                    <?= htmlspecialchars($currencySymbol) ?>
                                </span>
                                <input type="number" class="form-control" id="price" placeholder="0.00" step="0.01" required style="border-radius:0 10px 10px 0;">
                            </div>
                        </div>
                    </div>

                    <!-- ==========================================
                    STATUS
                    ========================================== -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="status">
                                <option value="active">✅ Active</option>
                                <option value="inactive">❌ Inactive</option>
                                <option value="upcoming">⏳ Upcoming</option>
                            </select>
                        </div>
                    </div>

                    <!-- ==========================================
                    DESCRIPTIONS
                    ========================================== -->
                    <div class="section-title">
                        <i class="bi bi-file-text" style="color:#f5b342;"></i> Descriptions
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Short Description <span class="required">*</span></label>
                        <textarea class="form-control" id="shortDescription" rows="2" placeholder="Brief description of the package (1-2 sentences)" required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Full Description</label>
                        <textarea class="form-control" id="description" rows="4" placeholder="Detailed description of the package"></textarea>
                    </div>

                    <!-- ==========================================
                    ITINERARY (with Day Title & Description)
                    ========================================== -->
                    <div class="section-title">
                        <i class="bi bi-calendar-event" style="color:#f5b342;"></i> Itinerary
                        <span class="badge-count" id="dayCount">1 Day</span>
                    </div>

                    <div id="itineraryContainer" class="mb-3">
                        <!-- Days will be added by JavaScript -->
                    </div>

                    <button type="button" class="btn-add-day" onclick="addDay()">
                        <i class="bi bi-plus-circle me-2"></i> Add Day
                    </button>

                    <!-- ==========================================
                    FEATURES (with Icon Upload - FIXED)
                    ========================================== -->
                    <div class="section-title mt-4">
                        <i class="bi bi-star" style="color:#f5b342;"></i> Features
                        <span class="badge-count" id="featureCount">0 Features</span>
                    </div>

                    <div class="mb-4">
                        <div class="badge-input-wrapper">
                            <div class="badge-input-row">
                                <input type="text" class="form-control" id="featureInput" placeholder="Enter feature name" style="flex:1;">

                                <!-- Feature Icon Upload - Using LABEL for better click handling -->
                                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                                    <label for="featureIcon" class="feature-icon-upload-box" id="featureIconBox">
                                        <i class="bi bi-image" style="font-size:1rem;color:#9bb2c5;"></i>
                                        <span id="featureIconLabel" style="font-size:0.7rem;color:#5f7d92;white-space:nowrap;">Upload Icon</span>
                                    </label>
                                    <input type="file" id="featureIcon" accept="image/*" style="display:none;">
                                    <button type="button" class="btn-sm-primary" onclick="addFeature()">
                                        <i class="bi bi-plus-circle"></i> Add
                                    </button>
                                </div>
                            </div>

                            <!-- Feature Icon Preview -->
                            <div id="featureIconPreview" class="mt-2" style="display:none;">
                                <span style="background:rgba(40,167,69,0.1);color:#28a745;padding:0.2rem 0.8rem;border-radius:12px;font-size:0.75rem;display:inline-flex;align-items:center;gap:6px;">
                                    <img id="featureIconPreviewImg" src="" style="width:18px;height:18px;object-fit:contain;border-radius:4px;">
                                    <span id="featureIconPreviewName"></span>
                                    <button type="button" onclick="removeFeatureIconPreview()" style="background:none;border:none;color:#dc3545;cursor:pointer;font-size:1rem;padding:0 4px;">×</button>
                                </span>
                            </div>

                            <!-- Features Container -->
                            <div class="badges-container" id="featuresContainer">
                                <div class="empty-badges">No features added yet</div>
                            </div>
                        </div>
                        <small class="text-muted" style="font-size:0.7rem;">Add features with optional icons (JPG, PNG, WebP up to 1MB)</small>
                    </div>

                    <!-- ==========================================
                    IMAGES
                    ========================================== -->
                    <div class="section-title">
                        <i class="bi bi-images" style="color:#f5b342;"></i> Images
                    </div>

                    <!-- Main Image -->
                    <div class="mb-4">
                        <label class="form-label">Main Image <span class="required">*</span></label>
                        <div class="image-upload-wrapper">
                            <div class="image-upload-box" id="mainImageBox">
                                <i class="bi bi-cloud-upload"></i>
                                <p>Upload Main Image<br><small>JPG, PNG, WebP</small></p>
                                <input type="file" id="mainImage" name="main_image" accept="image/*" style="display:none;">
                            </div>
                            <div class="image-preview-wrapper">
                                <div id="mainImagePreview" class="image-preview">
                                    <div class="image-preview-empty">No image selected</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gallery Images -->
                    <div class="mb-4">
                        <label class="form-label">Gallery Images</label>
                        <div class="image-upload-wrapper">
                            <div class="image-upload-box" id="galleryImagesBox">
                                <i class="bi bi-images"></i>
                                <p>Upload Gallery<br><small>Multiple images</small></p>
                                <input type="file" id="galleryImages" name="gallery_images[]" accept="image/*" multiple style="display:none;">
                            </div>
                            <div class="image-preview-wrapper">
                                <div id="galleryImagesPreview" class="image-preview">
                                    <div class="image-preview-empty">No images selected</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==========================================
                    FORM ACTIONS
                    ========================================== -->
                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="window.location.href='tour-packages.php'">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <span id="submitText"><i class="bi bi-check2 me-2"></i>Create Package</span>
                            <span id="submitSpinner" class="spinner-border spinner-border-sm" style="display:none;"></span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>
    <script src="<?= APP_URL ?>javascript/main.js"></script>
    <script src="<?= APP_URL ?>javascript/add-tour-package.js"></script>
</body>

</html>