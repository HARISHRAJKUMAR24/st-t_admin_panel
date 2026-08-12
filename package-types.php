<?php
require_once 'config/config.php';
require_once 'config/function.php';
requireLogin();

if (!verifyToken($pdo)) {
    header("Location: " . APP_URL . "login.php");
    exit();
}

$currentUser = getCurrentUser($pdo);
$pageTitle = "Package Types";

// Get edit ID if any
$editId = isset($_GET['edit']) ? trim($_GET['edit']) : '';
$editType = null;

if (!empty($editId)) {
    $stmt = $pdo->prepare("SELECT * FROM package_type_images WHERE type_id = ?");
    $stmt->execute([$editId]);
    $editType = $stmt->fetch();
}

// Fetch all package types
$stmt = $pdo->query("SELECT * FROM package_type_images ORDER BY name ASC");
$types = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>Package Types · Tour Admin</title>
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

        /* Edit Form */
        .edit-form-container {
            background: rgba(255, 215, 100, 0.08);
            border-radius: 16px;
            padding: 1.2rem 1.5rem;
            border: 2px solid #ffd966;
            margin-bottom: 1.5rem;
            display: <?= $editType ? 'block' : 'none' ?>;
        }

        .edit-form-container .edit-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.8rem;
        }

        .edit-form-container .edit-header h6 {
            font-weight: 600;
            color: #b8860b;
            margin: 0;
            font-size: 0.95rem;
        }

        .edit-form-container .edit-header .close-edit {
            background: none;
            border: none;
            color: #dc3545;
            font-size: 1.2rem;
            cursor: pointer;
        }

        .form-label {
            font-weight: 500;
            color: #123b4f;
            font-size: 0.8rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.5rem 0.8rem;
            border: 2px solid #e8edf3;
            background: rgba(255, 255, 255, 0.6);
            font-weight: 500;
            transition: all 0.25s ease;
            font-size: 0.85rem;
            color: #123b4f;
        }

        .form-control:focus {
            border-color: #ffd966;
            box-shadow: 0 0 0 4px rgba(255, 215, 100, 0.15);
            background: white;
        }

        .form-control[readonly] {
            background: rgba(255, 255, 255, 0.3);
            cursor: not-allowed;
        }

        .edit-row {
            display: flex;
            gap: 15px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .edit-row .form-group {
            flex: 1;
            min-width: 180px;
        }

        .edit-row .form-group-sm {
            flex: 0 0 auto;
            min-width: 120px;
        }

        .btn-submit {
            background: linear-gradient(145deg, #0b2a3e 0%, #123b4f 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 0.85rem;
            min-width: 100px;
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

        .btn-cancel-edit {
            border-radius: 10px;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            font-size: 0.85rem;
            border: 2px solid #e8edf3;
            background: transparent;
            color: #5f7d92;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn-cancel-edit:hover {
            background: #e8edf3;
            color: #123b4f;
            border-color: #d5dce6;
        }

        /* Image Upload - Compact */
        .image-upload-wrapper {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .image-upload-box {
            flex: 0 0 80px;
            border: 2px dashed #e8edf3;
            border-radius: 10px;
            padding: 0.4rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.4);
            min-height: 60px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
        }

        .image-upload-box:hover {
            border-color: #ffd966;
            background: rgba(255, 215, 100, 0.05);
        }

        .image-upload-box i {
            font-size: 1.2rem;
            color: #9bb2c5;
        }

        .image-upload-box p {
            color: #5f7d92;
            margin: 0;
            font-size: 0.5rem;
            line-height: 1.2;
        }

        .image-preview-wrapper {
            flex: 1;
            min-width: 80px;
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
            background: rgba(231, 76, 94, 0.92);
            color: white;
            border: none;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.5rem;
            opacity: 0;
        }

        .image-preview-item:hover .remove-image {
            opacity: 1;
        }

        .image-preview-empty {
            color: #9bb2c5;
            font-size: 0.65rem;
            padding: 0.2rem 0;
        }

        /* Current Image - Compact */
        .current-image-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 0.3rem;
        }

        .current-image-item {
            position: relative;
            width: 80px;
            height: 60px;
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
            opacity: 0;
        }

        .current-image-item:hover .delete-image-btn {
            opacity: 1;
        }

        /* Gallery Grid */
        .types-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 16px;
        }

        .type-item {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
            border-radius: 14px;
            padding: 0.8rem;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 4px 16px rgba(0, 20, 30, 0.04);
            transition: all 0.3s ease;
            text-align: center;
        }

        .type-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0, 20, 30, 0.08);
            border-color: #ffd966;
        }

        .type-item .type-img {
            width: 100%;
            height: 110px;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 0.4rem;
            background: #f0f3f7;
        }

        .type-item .type-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .type-item .type-img .no-img {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #9bb2c5;
            font-size: 1.5rem;
            background: #f7f9fc;
        }

        .type-item .type-id {
            font-size: 0.55rem;
            color: #9bb2c5;
            font-weight: 600;
        }

        .type-item .type-name {
            font-weight: 600;
            color: #123b4f;
            font-size: 0.85rem;
            margin: 0.1rem 0;
        }

        .type-item .type-actions {
            display: flex;
            gap: 6px;
            justify-content: center;
            margin-top: 0.4rem;
            padding-top: 0.4rem;
            border-top: 1px solid #e8edf3;
        }

        .type-item .type-actions .btn-sm {
            padding: 0.15rem 0.6rem;
            border-radius: 6px;
            font-size: 0.65rem;
            font-weight: 500;
            border: none;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
        }

        .btn-edit-sm {
            background: rgba(18, 59, 79, 0.1);
            color: #123b4f;
        }

        .btn-edit-sm:hover {
            background: #123b4f;
            color: #fff;
        }

        .btn-delete-sm {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .btn-delete-sm:hover {
            background: #dc3545;
            color: #fff;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
        }

        .empty-state i {
            font-size: 2.5rem;
            color: #e8edf3;
        }

        .empty-state p {
            color: #5f7d92;
            margin-top: 0.3rem;
            font-size: 0.85rem;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 0.8rem;
            padding-top: 0.8rem;
            border-top: 2px solid #f0f3f7;
        }

        .type-name-display {
            font-weight: 600;
            color: #b8860b;
            font-size: 0.9rem;
            padding: 0.3rem 0.5rem;
            background: rgba(255, 215, 100, 0.1);
            border-radius: 8px;
            border: 1px solid rgba(255, 215, 100, 0.2);
        }

        @media (max-width: 768px) {
            .page-wrapper {
                padding: 10px;
            }

            .edit-form-container {
                padding: 1rem;
            }

            .edit-row {
                flex-direction: column;
                gap: 10px;
            }

            .edit-row .form-group {
                min-width: 100%;
                width: 100%;
            }

            .edit-row .form-group-sm {
                min-width: 100%;
                width: 100%;
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
                min-height: 60px;
            }

            .current-image-item {
                width: 100%;
                height: 55px;
            }

            .types-grid {
                grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
                gap: 12px;
            }

            .type-item .type-img {
                height: 90px;
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
                <small>Package Types</small>
            </div>
        </div>

        <div class="page-wrapper">

            <div class="page-header">
                <h4><i class="bi bi-tags me-2" style="color:#f5b342;"></i>Package Types</h4>
            </div>

            <!-- ==========================================
            EDIT FORM (Only shows when editing)
            ========================================== -->
            <div class="edit-form-container" id="editFormContainer">
                <form id="packageTypeForm" enctype="multipart/form-data">
                    <input type="hidden" id="editId" value="<?= $editType['id'] ?? '' ?>">
                    <input type="hidden" id="editTypeId" value="<?= $editType['type_id'] ?? '' ?>">
                    <input type="hidden" id="typeName" value="<?= htmlspecialchars($editType['name'] ?? '') ?>">

                    <div class="edit-header">
                        <h6><i class="bi bi-pencil-square me-2"></i>Editing: <span class="type-name-display"><?= htmlspecialchars($editType['name'] ?? '') ?></span></h6>
                        <button type="button" class="close-edit" onclick="cancelEdit()">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>

                    <div class="edit-row">
                        <div class="form-group">
                            <label class="form-label">Type Name (Default)</label>
                            <input type="text" class="form-control"
                                value="<?= htmlspecialchars($editType['name'] ?? '') ?>"
                                readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Image</label>

                            <?php if ($editType && !empty($editType['image'])): ?>
                                <div class="current-image-wrapper">
                                    <div class="current-image-item">
                                        <img src="<?= APP_URL . $editType['image'] ?>" alt="">
                                        <button type="button" class="delete-image-btn" onclick="deleteCurrentImage()">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" id="deleteImage" value="0">
                            <?php endif; ?>

                            <div class="image-upload-wrapper">
                                <div class="image-upload-box" id="imageBox">
                                    <i class="bi bi-cloud-upload"></i>
                                    <p>Upload</p>
                                    <input type="file" id="typeImage" name="image" accept="image/*" style="display:none;">
                                </div>
                                <div class="image-preview-wrapper">
                                    <div id="imagePreview" class="image-preview">
                                        <div class="image-preview-empty">No image</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-cancel-edit" onclick="cancelEdit()">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <span id="submitText"><i class="bi bi-check2 me-2"></i>Update Image</span>
                            <span id="submitSpinner" class="spinner-border spinner-border-sm" style="display:none;"></span>
                        </button>
                    </div>

                </form>
            </div>

            <!-- ==========================================
            TYPES GALLERY
            ========================================== -->
            <div class="section-title">
                <i class="bi bi-grid" style="color:#f5b342;"></i> Total Package
                <span class="badge-count"><?= count($types) ?></span>
            </div>

            <?php if (empty($types)): ?>
                <div class="empty-state">
                    <i class="bi bi-tags"></i>
                    <p>No package types yet</p>
                </div>
            <?php else: ?>
                <div class="types-grid">
                    <?php foreach ($types as $type): ?>
                        <div class="type-item" id="type-<?= $type['id'] ?>">
                            <div class="type-img">
                                <?php if (!empty($type['image'])): ?>
                                    <img src="<?= APP_URL . $type['image'] ?>" alt="<?= htmlspecialchars($type['name']) ?>">
                                <?php else: ?>
                                    <div class="no-img">
                                        <i class="bi bi-image"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="type-id"><?= htmlspecialchars($type['type_id']) ?></div>
                            <div class="type-name"><?= htmlspecialchars($type['name']) ?></div>
                            <div class="type-actions">
                                <button class="btn-sm btn-edit-sm" onclick="editType('<?= $type['type_id'] ?>')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn-sm btn-delete-sm" onclick="deleteTypeImage(<?= $type['id'] ?>, '<?= htmlspecialchars($type['name']) ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>
    <script src="<?= APP_URL ?>javascript/main.js"></script>
    <script>
        // =============================================
        // PACKAGE TYPES - JAVASCRIPT
        // =============================================

        // =============================================
        // IMAGE UPLOAD
        // =============================================

        function setupImageUpload(inputId, previewId, boxId) {
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

                            const removeBtn = document.createElement('button');
                            removeBtn.className = 'remove-image';
                            removeBtn.innerHTML = '<i class="bi bi-x"></i>';
                            removeBtn.onclick = function(e) {
                                e.stopPropagation();
                                div.remove();
                                input.value = '';
                                if (preview.children.length === 0) {
                                    preview.innerHTML = '<div class="image-preview-empty">No image</div>';
                                }
                            };
                            div.appendChild(removeBtn);
                            preview.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    } else {
                        preview.innerHTML = '<div class="image-preview-empty">No image</div>';
                    }
                });
            }
        }

        // =============================================
        // DELETE CURRENT IMAGE
        // =============================================

        window.deleteCurrentImage = function() {
            const imgWrapper = document.querySelector('.current-image-item');
            if (imgWrapper) {
                imgWrapper.remove();
            }
            document.getElementById('deleteImage').value = '1';
        };

        // =============================================
        // EDIT TYPE
        // =============================================

        window.editType = function(typeId) {
            window.location.href = 'package-types.php?edit=' + typeId;
        };

        // =============================================
        // CANCEL EDIT
        // =============================================

        window.cancelEdit = function() {
            window.location.href = 'package-types.php';
        };

        // =============================================
        // DELETE TYPE IMAGE ONLY (NOT THE TYPE)
        // =============================================

        window.deleteTypeImage = function(id, name) {
            Swal.fire({
                title: 'Delete Image for ' + name + '?',
                text: 'This will delete the image only. The type will remain.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete image!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Deleting image...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const formData = new FormData();
                    formData.append('id', id);

                    fetch('ajax/delete-package-type-image.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: data.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
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
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'An error occurred. Please try again.',
                                confirmButtonColor: '#123b4f'
                            });
                        });
                }
            });
        };

        // =============================================
        // FORM SUBMISSION
        // =============================================

        document.addEventListener('DOMContentLoaded', function() {
            setupImageUpload('typeImage', 'imagePreview', 'imageBox');

            const form = document.getElementById('packageTypeForm');

            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const typeName = document.getElementById('typeName');
                    const typeImage = document.getElementById('typeImage');
                    const editId = document.getElementById('editId');
                    const deleteImage = document.getElementById('deleteImage');

                    const hasImage = typeImage && typeImage.files && typeImage.files[0];
                    const isDelete = deleteImage && deleteImage.value === '1';

                    if (!hasImage && !isDelete) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No Change',
                            text: 'Please upload a new image or click X to remove the current image.',
                            confirmButtonColor: '#123b4f'
                        });
                        return;
                    }

                    const submitBtn = document.getElementById('submitBtn');
                    const submitText = document.getElementById('submitText');
                    const submitSpinner = document.getElementById('submitSpinner');
                    submitBtn.disabled = true;
                    submitText.style.display = 'none';
                    submitSpinner.style.display = 'inline-block';

                    const formData = new FormData();
                    formData.append('id', editId.value);
                    formData.append('type_id', document.getElementById('editTypeId').value);
                    formData.append('name', typeName ? typeName.value : '');

                    if (deleteImage) {
                        formData.append('delete_image', deleteImage.value);
                    }

                    if (typeImage && typeImage.files[0]) {
                        formData.append('image', typeImage.files[0]);
                    }

                    fetch('ajax/update-package-type.php', {
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
                                    title: 'Updated!',
                                    text: data.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = 'package-types.php';
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
    </script>
</body>

</html>