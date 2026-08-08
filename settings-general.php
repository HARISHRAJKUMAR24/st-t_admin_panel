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
$pageTitle = "General Settings";

// Get current settings
$stmt = $pdo->prepare("SELECT * FROM settings WHERE id = 1");
$stmt->execute();
$settings = $stmt->fetch();

if (!$settings) {
    $stmt = $pdo->prepare("INSERT INTO settings (id, timezone, address) VALUES (1, 'Asia/Kolkata', '')");
    $stmt->execute();
    $settings = [
        'id' => 1,
        'website_logo' => null,
        'favicon' => null,
        'panel_logo' => null,
        'timezone' => 'Asia/Kolkata',
        'address' => '',
        'site_name' => 'Tour Admin',
        'contact_email' => '',
        'contact_phone' => '',
        'currency' => 'INR',
        'site_title' => 'Tour Admin Panel'
    ];
}

// Timezone list
$timezones = [
    'UTC' => 'UTC',
    'Asia/Kolkata' => 'Asia/Kolkata (IST)',
    'Asia/Dubai' => 'Asia/Dubai (GST)',
    'Asia/Singapore' => 'Asia/Singapore (SGT)',
    'Asia/Tokyo' => 'Asia/Tokyo (JST)',
    'America/New_York' => 'America/New York (EST)',
    'America/Chicago' => 'America/Chicago (CST)',
    'America/Denver' => 'America/Denver (MST)',
    'America/Los_Angeles' => 'America/Los Angeles (PST)',
    'Europe/London' => 'Europe/London (GMT)',
    'Europe/Paris' => 'Europe/Paris (CET)',
    'Australia/Sydney' => 'Australia/Sydney (AEST)',
    'Pacific/Auckland' => 'Pacific/Auckland (NZST)',
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>General Settings · Tour Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .settings-wrapper {
            padding: 20px;
        }

        .settings-header {
            margin-bottom: 25px;
        }

        .settings-header h4 {
            font-weight: 600;
            color: #123b4f;
            margin-bottom: 5px;
            font-size: 1.3rem;
        }

        .settings-header p {
            color: #5f7d92;
            font-size: 0.85rem;
        }

        .settings-container {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 4px 16px rgba(0, 20, 30, 0.04);
            max-width: 1100px;
            margin: 0 auto;
        }

        .settings-container .section-title {
            font-weight: 600;
            color: #123b4f;
            font-size: 1rem;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #ffd966;
        }

        .form-label {
            font-weight: 500;
            color: #123b4f;
            font-size: 0.8rem;
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

        textarea.form-control {
            resize: vertical;
            min-height: 60px;
        }

        .btn-submit {
            background: linear-gradient(145deg, #0b2a3e 0%, #123b4f 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.6rem 2.5rem;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 0.9rem;
            min-width: 160px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(11, 42, 62, 0.2);
            color: #ffd966;
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
        }

        .btn-secondary {
            border-radius: 10px;
            padding: 0.6rem 1.8rem;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .btn-danger-sm {
            border-radius: 10px;
            padding: 0.3rem 1rem;
            font-weight: 500;
            font-size: 0.75rem;
            background: #dc3545;
            color: white;
            border: none;
            transition: all 0.3s;
        }

        .btn-danger-sm:hover {
            background: #c82333;
            color: white;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e8edf3;
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

        /* Left Right Layout */
        .settings-row {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .settings-col-left {
            flex: 0 0 45%;
            min-width: 280px;
        }

        .settings-col-right {
            flex: 0 0 45%;
            min-width: 280px;
        }

        /* Image Upload */
        .image-upload-wrapper {
            display: flex;
            gap: 15px;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .image-upload-box {
            flex: 0 0 120px;
            border: 2px dashed #e8edf3;
            border-radius: 10px;
            padding: 0.8rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: rgba(255, 255, 255, 0.4);
            min-height: 80px;
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
            font-size: 1.5rem;
            color: #9bb2c5;
        }

        .image-upload-box p {
            color: #5f7d92;
            margin-top: 0.2rem;
            font-size: 0.65rem;
            margin-bottom: 0;
        }

        .image-preview-wrapper {
            flex: 1;
            min-width: 100px;
        }

        .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .image-preview-item {
            position: relative;
            width: 60px;
            height: 60px;
            border-radius: 8px;
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
            top: 2px;
            right: 2px;
            background: rgba(231, 76, 94, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            transition: 0.2s;
        }

        .image-preview-item .remove-image:hover {
            transform: scale(1.1);
        }

        .image-preview-empty {
            color: #9bb2c5;
            font-size: 0.75rem;
            padding: 0.3rem 0;
        }

        .logo-preview {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 8px;
            border: 2px dashed #e8edf3;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .logo-preview img {
            max-height: 50px;
            max-width: 120px;
            object-fit: contain;
        }

        .logo-preview .logo-info {
            flex: 1;
            min-width: 100px;
        }

        .logo-preview .logo-info small {
            color: #5f7d92;
            font-size: 0.7rem;
        }

        .favicon-preview {
            width: 28px;
            height: 28px;
            object-fit: contain;
        }

        /* 3x3 Image Grid at Bottom */
        .logo-grid {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 2px solid #e8edf3;
        }

        .logo-grid .grid-title {
            font-weight: 600;
            color: #123b4f;
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }

        .logo-grid .row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .logo-grid .col {
            flex: 0 0 calc(33.333% - 15px);
            min-width: 180px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 10px;
            padding: 1rem;
            border: 1px solid #e8edf3;
        }

        .logo-grid .col .logo-label {
            font-size: 0.75rem;
            color: #5f7d92;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .logo-grid .col .logo-display {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .logo-grid .col .logo-display img {
            max-height: 50px;
            max-width: 120px;
            object-fit: contain;
        }

        .logo-grid .col .logo-display .favicon-display {
            width: 28px;
            height: 28px;
            object-fit: contain;
        }

        @media (max-width: 768px) {
            .settings-wrapper {
                padding: 10px;
            }

            .settings-container {
                padding: 1rem;
            }

            .settings-col-left,
            .settings-col-right {
                flex: 0 0 100%;
                min-width: 100%;
            }

            .form-actions {
                flex-direction: column;
            }

            .form-actions .btn {
                width: 100%;
            }

            .logo-preview {
                flex-direction: column;
                text-align: center;
            }

            .image-upload-box {
                flex: 0 0 100%;
            }

            .logo-grid .col {
                flex: 0 0 100%;
                min-width: 100%;
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
                <small>General Settings</small>
            </div>
        </div>

        <div class="settings-wrapper">

            <div class="settings-header">
                <h4><i class="bi bi-gear me-2" style="color:#f5b342;"></i>General Settings</h4>
             
            </div>

            <div class="settings-container">
                <form id="settingsForm" enctype="multipart/form-data">
                    <!-- Left Right Row -->
                    <div class="settings-row">
                        <!-- Left Column -->
                        <div class="settings-col-left">
                            <h6 class="section-title">Profile Information</h6>

                            <div class="mb-3">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="settingsName" value="<?= htmlspecialchars($currentUser['name'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="settingsEmail" value="<?= htmlspecialchars($currentUser['email'] ?? '') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="settingsPhone" value="<?= htmlspecialchars($currentUser['phone'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" id="settingsAddress" rows="2" placeholder="Enter your address"><?= htmlspecialchars($settings['address'] ?? '') ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Timezone <span class="text-danger">*</span></label>
                                <select class="form-select" id="timezone" required>
                                    <?php foreach ($timezones as $value => $label): ?>
                                        <option value="<?= $value ?>" <?= ($settings['timezone'] == $value) ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Set your server timezone for accurate date/time display</small>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="settings-col-right">
                            <h6 class="section-title">Upload Images</h6>

                            <!-- Website Logo -->
                            <div class="mb-3">
                                <label class="form-label">Website Logo</label>
                                <div class="image-upload-wrapper">
                                    <div class="image-upload-box" id="websiteLogoBox">
                                        <i class="bi bi-cloud-upload"></i>
                                        <p>Upload Logo</p>
                                        <input type="file" id="websiteLogo" name="website_logo" accept="image/*" style="display:none;">
                                    </div>
                                    <div class="image-preview-wrapper">
                                        <div id="websiteLogoPreview" class="image-preview">
                                            <div class="image-preview-empty">No new image</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Favicon -->
                            <div class="mb-3">
                                <label class="form-label">Favicon</label>
                                <div class="image-upload-wrapper">
                                    <div class="image-upload-box" id="faviconBox">
                                        <i class="bi bi-cloud-upload"></i>
                                        <p>Upload Favicon</p>
                                        <input type="file" id="favicon" name="favicon" accept="image/*" style="display:none;">
                                    </div>
                                    <div class="image-preview-wrapper">
                                        <div id="faviconPreview" class="image-preview">
                                            <div class="image-preview-empty">No new image</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Panel Logo -->
                            <div class="mb-3">
                                <label class="form-label">Admin Panel Logo</label>
                                <div class="image-upload-wrapper">
                                    <div class="image-upload-box" id="panelLogoBox">
                                        <i class="bi bi-cloud-upload"></i>
                                        <p>Upload Panel Logo</p>
                                        <input type="file" id="panelLogo" name="panel_logo" accept="image/*" style="display:none;">
                                    </div>
                                    <div class="image-preview-wrapper">
                                        <div id="panelLogoPreview" class="image-preview">
                                            <div class="image-preview-empty">No new image</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3x3 Image Grid at Bottom -->
                    <div class="logo-grid">
                        <div class="grid-title">Current Images</div>
                        <div class="row">
                            <!-- Website Logo -->
                            <div class="col">
                                <div class="logo-label">Website Logo</div>
                                <div class="logo-display">
                                    <?php if (!empty($settings['website_logo'])): ?>
                                        <img src="<?= APP_URL . $settings['website_logo'] ?>" alt="Website Logo">
                                        <button type="button" class="btn btn-danger-sm" onclick="deleteLogo('website_logo')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size:0.8rem;">No logo uploaded</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Favicon -->
                            <div class="col">
                                <div class="logo-label">Favicon</div>
                                <div class="logo-display">
                                    <?php if (!empty($settings['favicon'])): ?>
                                        <img src="<?= APP_URL . $settings['favicon'] ?>" alt="Favicon" class="favicon-display">
                                        <button type="button" class="btn btn-danger-sm" onclick="deleteLogo('favicon')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size:0.8rem;">No favicon uploaded</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Panel Logo -->
                            <div class="col">
                                <div class="logo-label">Panel Logo</div>
                                <div class="logo-display">
                                    <?php if (!empty($settings['panel_logo'])): ?>
                                        <img src="<?= APP_URL . $settings['panel_logo'] ?>" alt="Panel Logo">
                                        <button type="button" class="btn btn-danger-sm" onclick="deleteLogo('panel_logo')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size:0.8rem;">No panel logo uploaded</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Single Save Button -->
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='settings.php'">Cancel</button>
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <span id="submitText">Save All Settings</span>
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
    <script src="<?= APP_URL ?>javascript/settings-general.js"></script>
</body>

</html>