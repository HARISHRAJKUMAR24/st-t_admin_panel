<?php
require_once 'config/config.php';
require_once 'config/function.php';
requireLogin();

// Verify token
if (!verifyToken($pdo)) {
    header("Location: " . APP_URL . "login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: testimonials.php");
    exit();
}

// Fetch testimonial data
$stmt = $pdo->prepare("SELECT * FROM testimonials WHERE id = ?");
$stmt->execute([$id]);
$testimonial = $stmt->fetch();

if (!$testimonial) {
    header("Location: testimonials.php");
    exit();
}

$currentUser = getCurrentUser($pdo);
$pageTitle = "Edit Testimonial";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>Edit Testimonial · Tour Admin</title>
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
            text-decoration: none;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 32px rgba(0, 20, 30, 0.06);
            max-width: 800px;
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

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .btn-submit {
            background: linear-gradient(145deg, #0b2a3e 0%, #123b4f 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.65rem 2.5rem;
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

        /* Current Image */
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
            opacity: 0;
        }

        .current-image-item:hover .delete-image-btn {
            opacity: 1;
        }

        .current-image-item .delete-image-btn:hover {
            transform: scale(1.1);
            background: #dc3545;
        }

        .current-image-label {
            font-size: 0.75rem;
            color: #5f7d92;
            margin-bottom: 0.3rem;
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

            .current-image-item {
                width: 100%;
                height: 70px;
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
                <small>Edit Testimonial</small>
            </div>
        </div>

        <div class="page-wrapper">
            <a href="testimonials.php" class="back-link">
                <i class="bi bi-arrow-left me-1"></i> Back to Testimonials
            </a>

            <div class="page-header">
                <h4><i class="bi bi-pencil-square me-2" style="color:#f5b342;"></i>Edit Testimonial</h4>
                <p>Update testimonial</p>
            </div>

            <div class="form-container">
                <form id="testimonialForm" enctype="multipart/form-data">
                    <input type="hidden" id="testimonialId" value="<?= $testimonial['id'] ?>">
                    <input type="hidden" id="deleteLogo" value="0">

                    <div class="section-title">
                        <i class="bi bi-person" style="color:#f5b342;"></i> Testimonial Details
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Name <span class="required">*</span></label>
                        <input type="text" class="form-control" id="name" value="<?= htmlspecialchars($testimonial['name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Logo / Image</label>

                        <?php if (!empty($testimonial['logo'])): ?>
                            <div class="mb-2">
                                <div class="current-image-label">Current Image:</div>
                                <div class="current-image-wrapper">
                                    <div class="current-image-item">
                                        <img src="<?= APP_URL . $testimonial['logo'] ?>" alt="Current logo">
                                        <button type="button" class="delete-image-btn" onclick="deleteCurrentLogo()">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="image-upload-wrapper">
                            <div class="image-upload-box" id="logoBox">
                                <i class="bi bi-cloud-upload"></i>
                                <p>Change Image<br><small>JPG, PNG, WebP</small></p>
                                <input type="file" id="logo" name="logo" accept="image/*" style="display:none;">
                            </div>
                            <div class="image-preview-wrapper">
                                <div id="logoPreview" class="image-preview">
                                    <div class="image-preview-empty">No new image selected</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Testimonial <span class="required">*</span></label>
                        <textarea class="form-control" id="testimonial" rows="4" required><?= htmlspecialchars($testimonial['testimonial']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="status">
                            <option value="publish" <?= $testimonial['status'] == 'publish' ? 'selected' : '' ?>>Publish</option>
                            <option value="unpublish" <?= $testimonial['status'] == 'unpublish' ? 'selected' : '' ?>>Unpublish</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" onclick="window.location.href='testimonials.php'">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <span id="submitText">Update Testimonial</span>
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
    <script src="<?= APP_URL ?>javascript/edit-testimonial.js"></script>
</body>

</html>