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
$pageTitle = "Add Offer";

// Get currency from settings
$currencyCode = getCurrencyCode($pdo);
$currencySymbol = getCurrencySymbol($currencyCode);

// Fetch all tour packages for dropdown
$stmt = $pdo->query("SELECT id, package_id, package_name FROM tour_packages WHERE status = 'active' ORDER BY package_name");
$packages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>Add Offer · Tour Admin</title>
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

        .form-container {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 32px rgba(0, 20, 30, 0.06);
            max-width: 900px;
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

        /* Multi-select styling */
        .multi-select-wrapper {
            position: relative;
        }

        .multi-select-wrapper .form-control {
            cursor: pointer;
        }

        .multi-select-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 2px solid #e8edf3;
            border-radius: 10px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            padding: 0.5rem;
            margin-top: 4px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        .multi-select-dropdown.show {
            display: block;
        }

        .multi-select-dropdown .option-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .multi-select-dropdown .option-item:hover {
            background: rgba(255, 215, 100, 0.1);
        }

        .multi-select-dropdown .option-item input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .multi-select-dropdown .option-item .option-label {
            font-size: 0.85rem;
            color: #123b4f;
        }

        .selected-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 8px;
        }

        .selected-tag {
            display: inline-flex;
            align-items: center;
            background: rgba(255, 215, 100, 0.2);
            color: #b8860b;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 500;
            gap: 4px;
        }

        .selected-tag .remove-tag {
            cursor: pointer;
            color: #dc3545;
            font-weight: 700;
            font-size: 0.9rem;
            line-height: 1;
        }

        .selected-tag .remove-tag:hover {
            transform: scale(1.2);
        }

        /* Image Upload */
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

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 2px solid #f0f3f7;
        }

        /* Discount type indicator */
        .discount-type-indicator {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .discount-type-indicator.percentage {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }

        .discount-type-indicator.fixed {
            background: rgba(255, 193, 7, 0.15);
            color: #b8860b;
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

            .image-upload-box {
                flex: 0 0 100%;
                min-height: 100px;
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
                <small>Add Offer</small>
            </div>
        </div>

        <div class="page-wrapper">

            <div class="page-header">
                <h4><i class="bi bi-plus-circle me-2" style="color:#f5b342;"></i>Add New Offer</h4>
                <p>Create a new promotional offer</p>
            </div>

            <div class="form-container">
                <form id="offerForm" enctype="multipart/form-data">
                    <!-- Offer Title -->
                    <div class="section-title">
                        <i class="bi bi-tag" style="color:#f5b342;"></i> Offer Details
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Offer Title <span class="required">*</span></label>
                        <input type="text" class="form-control" id="offerTitle" placeholder="Enter offer title" required>
                    </div>

                    <!-- Discount Type & Value -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Discount Type <span class="required">*</span></label>
                            <select class="form-select" id="discountType" required>
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount (<?= htmlspecialchars($currencySymbol) ?>)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Discount Value <span class="required">*</span></label>
                            <input type="number" class="form-control" id="discountValue" placeholder="0.00" step="0.01" min="0" required>
                            <small class="text-muted" id="discountTypeLabel">Enter percentage value</small>
                        </div>
                    </div>

                    <!-- Tour Packages Multi-Select -->
                    <div class="mb-3">
                        <label class="form-label">Select Tour Packages <span class="required">*</span></label>
                        <div class="multi-select-wrapper">
                            <input type="text" class="form-control" id="packageSearch" placeholder="Click to select packages..." readonly onclick="toggleDropdown()">
                            <div class="multi-select-dropdown" id="packageDropdown">
                                <?php foreach ($packages as $pkg): ?>
                                    <div class="option-item">
                                        <input type="checkbox" id="pkg_<?= $pkg['id'] ?>" value="<?= $pkg['id'] ?>" onchange="updateSelectedPackages()">
                                        <label class="option-label" for="pkg_<?= $pkg['id'] ?>">
                                            <?= htmlspecialchars($pkg['package_name']) ?> (<?= htmlspecialchars($pkg['package_id']) ?>)
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (empty($packages)): ?>
                                    <div style="padding:10px;text-align:center;color:#9bb2c5;">No active packages found</div>
                                <?php endif; ?>
                            </div>
                            <div class="selected-tags" id="selectedPackages"></div>
                            <input type="hidden" id="selectedPackageIds" name="package_ids" value="">
                        </div>
                        <small class="text-muted">Select one or more tour packages for this offer</small>
                    </div>

                    <!-- Date Range -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="startDate">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" id="endDate">
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="description" rows="3" placeholder="Enter offer description"></textarea>
                    </div>

                    <!-- Main Image -->
                    <div class="section-title">
                        <i class="bi bi-image" style="color:#f5b342;"></i> Offer Image
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Main Image <span class="required">*</span></label>
                        <div class="image-upload-wrapper">
                            <div class="image-upload-box" id="mainImageBox">
                                <i class="bi bi-cloud-upload"></i>
                                <p>Upload Image<br><small>JPG, PNG, WebP</small></p>
                                <input type="file" id="mainImage" name="main_image" accept="image/*" style="display:none;">
                            </div>
                            <div class="image-preview-wrapper">
                                <div id="mainImagePreview" class="image-preview">
                                    <div class="image-preview-empty">No image selected</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="window.location.href='offers.php'">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <span id="submitText"><i class="bi bi-check2 me-2"></i>Create Offer</span>
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
    <script src="<?= APP_URL ?>javascript/add-offer.js"></script>
</body>

</html>