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
$pageTitle = "Tour Packages";

// Get currency code and symbol
$currencyCode = getCurrencyCode($pdo);
$currencySymbol = getCurrencySymbol($currencyCode);

// Fetch all tour packages
$stmt = $pdo->query("SELECT * FROM tour_packages ORDER BY created_at DESC");
$packages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>Tour Packages · Tour Admin</title>
    <style>
        .page-wrapper {
            padding: 20px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-header h4 {
            font-weight: 600;
            color: #123b4f;
            margin-bottom: 0;
            font-size: 1.2rem;
        }

        .page-header p {
            color: #5f7d92;
            margin-bottom: 0;
            font-size: 0.8rem;
        }

        .btn-add {
            background: linear-gradient(145deg, #0b2a3e 0%, #123b4f 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.4rem 1.2rem;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(11, 42, 62, 0.2);
            color: #ffd966;
        }

        .package-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
            border-radius: 12px;
            padding: 0.9rem;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 4px 16px rgba(0, 20, 30, 0.04);
            transition: all 0.3s ease;
            height: 100%;
        }

        .package-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 20, 30, 0.08);
            border-color: #ffd966;
        }

        .package-card .package-image {
            width: 100%;
            height: 140px;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 0.6rem;
            background: #f0f3f7;
        }

        .package-card .package-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .package-card .package-id {
            font-size: 0.6rem;
            color: #9bb2c5;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .package-card .package-name {
            font-weight: 600;
            color: #123b4f;
            font-size: 0.95rem;
            margin: 0.15rem 0;
        }

        .package-card .package-meta {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin: 0.3rem 0;
        }

        .package-card .package-meta .badge-custom {
            background: rgba(255, 215, 100, 0.2);
            color: #b8860b;
            padding: 0.1rem 0.5rem;
            border-radius: 16px;
            font-size: 0.6rem;
            font-weight: 600;
        }

        .package-card .package-meta .badge-days {
            background: rgba(18, 59, 79, 0.1);
            color: #123b4f;
            padding: 0.1rem 0.5rem;
            border-radius: 16px;
            font-size: 0.6rem;
            font-weight: 600;
        }

        .package-card .package-meta .badge-status {
            padding: 0.1rem 0.5rem;
            border-radius: 16px;
            font-size: 0.6rem;
            font-weight: 600;
        }

        .badge-status.active {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }

        .badge-status.inactive {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }

        .badge-status.upcoming {
            background: rgba(255, 193, 7, 0.15);
            color: #ffc107;
        }

        .package-card .package-price {
            font-weight: 700;
            color: #123b4f;
            font-size: 1rem;
        }

        .package-card .package-price .currency-symbol {
            font-weight: 700;
            margin-right: 2px;
        }

        .package-card .package-actions {
            display: flex;
            gap: 6px;
            margin-top: 0.6rem;
            padding-top: 0.6rem;
            border-top: 1px solid #e8edf3;
        }

        .package-card .package-actions .btn-action {
            padding: 0.2rem 0.7rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 500;
            border: none;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
        }

        .btn-action.btn-edit {
            background: rgba(18, 59, 79, 0.1);
            color: #123b4f;
        }

        .btn-action.btn-edit:hover {
            background: #123b4f;
            color: #fff;
        }

        .btn-action.btn-delete {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .btn-action.btn-delete:hover {
            background: #dc3545;
            color: #fff;
        }

        .btn-action.btn-view {
            background: rgba(255, 215, 100, 0.2);
            color: #b8860b;
        }

        .btn-action.btn-view:hover {
            background: #ffd966;
            color: #123b4f;
        }

        .feature-tag {
            display: inline-block;
            background: rgba(18, 59, 79, 0.05);
            color: #123b4f;
            padding: 0.1rem 0.4rem;
            border-radius: 10px;
            font-size: 0.55rem;
            margin: 1px;
        }

        .feature-tag img {
            width: 12px;
            height: 12px;
            object-fit: contain;
            display: inline;
            margin-right: 3px;
            vertical-align: middle;
        }

        .member-tag {
            display: inline-block;
            background: rgba(18, 59, 79, 0.08);
            color: #123b4f;
            padding: 0.1rem 0.4rem;
            border-radius: 10px;
            font-size: 0.55rem;
            margin: 1px;
            font-weight: 600;
        }

        .member-tag .member-count {
            background: rgba(255, 215, 100, 0.3);
            padding: 0 4px;
            border-radius: 8px;
            margin-left: 2px;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
        }

        .empty-state i {
            font-size: 3rem;
            color: #e8edf3;
        }

        .empty-state h5 {
            color: #123b4f;
            margin-top: 0.8rem;
            font-size: 1.1rem;
        }

        .empty-state p {
            color: #5f7d92;
            font-size: 0.85rem;
        }

        .btn-add-empty {
            background: linear-gradient(145deg, #0b2a3e 0%, #123b4f 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            margin-top: 0.8rem;
            font-size: 0.85rem;
        }

        .btn-add-empty:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(11, 42, 62, 0.2);
            color: #ffd966;
        }

        @media (max-width: 768px) {
            .page-wrapper {
                padding: 10px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .page-header .btn-add {
                width: 100%;
                justify-content: center;
            }

            .package-card .package-image {
                height: 110px;
            }

            .package-card .package-name {
                font-size: 0.85rem;
            }

            .package-card .package-price {
                font-size: 0.9rem;
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
                <small>Tour Packages</small>
            </div>
        </div>

        <div class="page-wrapper">
            <div class="page-header">
                <div>
                    <h4><i class="bi bi-suitcase me-2" style="color:#f5b342;"></i>Tour Packages</h4>
                    <p>Manage your tour packages</p>
                </div>
                <a href="add-tour-package.php" class="btn-add">
                    <i class="bi bi-plus-circle"></i> Add New
                </a>
            </div>

            <?php if (empty($packages)): ?>
                <div class="empty-state">
                    <i class="bi bi-suitcase"></i>
                    <h5>No Tour Packages Yet</h5>
                    <p>Create your first tour package to get started.</p>
                    <a href="add-tour-package.php" class="btn-add-empty">
                        <i class="bi bi-plus-circle me-2"></i>Add New Package
                    </a>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($packages as $package): ?>
                        <?php
                        // Decode JSON data
                        $gallery = json_decode($package['gallery_images'], true) ?: [];
                        $features = json_decode($package['features'], true) ?: [];
                        $members = json_decode($package['members'], true) ?: [];
                        ?>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="package-card">
                                <div class="package-image">
                                    <?php if (!empty($package['main_image'])): ?>
                                        <img src="<?= APP_URL . $package['main_image'] ?>" alt="<?= htmlspecialchars($package['package_name']) ?>">
                                    <?php else: ?>
                                        <img src="<?= APP_URL ?>assets/images/no-image.jpg" alt="No image">
                                    <?php endif; ?>
                                </div>

                                <div class="package-id"><?= htmlspecialchars($package['package_id']) ?></div>
                                <h5 class="package-name"><?= htmlspecialchars($package['package_name']) ?></h5>

                                <div class="package-meta">
                                    <span class="badge-days"><i class="bi bi-calendar3 me-1"></i><?= $package['days_count'] ?> Days</span>
                                    
                                    <!-- Display members from JSON -->
                                    <?php foreach ($members as $member): ?>
                                        <span class="member-tag">
                                            <?= htmlspecialchars($member['label']) ?>
                                            <span class="member-count"><?= $member['count'] ?></span>
                                        </span>
                                    <?php endforeach; ?>
                                    
                                    <span class="badge-status <?= $package['status'] ?>"><?= ucfirst($package['status']) ?></span>
                                </div>

                                <?php if (!empty($features)): ?>
                                    <div style="margin: 0.3rem 0;">
                                        <?php foreach (array_slice($features, 0, 3) as $feature): ?>
                                            <span class="feature-tag">
                                                <?php if (!empty($feature['icon'])): ?>
                                                    <?php 
                                                    // Check if icon is a URL or local path
                                                    $iconPath = $feature['icon'];
                                                    if (strpos($iconPath, 'http') !== 0 && strpos($iconPath, 'uploads/') !== 0) {
                                                        $iconPath = APP_URL . $iconPath;
                                                    } elseif (strpos($iconPath, 'http') !== 0) {
                                                        $iconPath = APP_URL . $iconPath;
                                                    }
                                                    ?>
                                                    <img src="<?= htmlspecialchars($iconPath) ?>" alt="">
                                                <?php else: ?>
                                                    <i class="bi bi-check-circle-fill" style="font-size:0.5rem;"></i>
                                                <?php endif; ?>
                                                <?= htmlspecialchars($feature['name']) ?>
                                            </span>
                                        <?php endforeach; ?>
                                        <?php if (count($features) > 3): ?>
                                            <span class="feature-tag">+<?= count($features) - 3 ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="package-price">
                                    <span class="currency-symbol"><?= htmlspecialchars($currencySymbol) ?></span>
                                    <?= number_format($package['price'], 2) ?>
                                </div>

                                <div class="package-actions">
                                    <a href="edit-tour-package.php?package_id=<?= $package['package_id'] ?>" class="btn-action btn-edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn-action btn-delete" onclick="deletePackage(<?= $package['id'] ?>, '<?= $package['package_id'] ?>')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function deletePackage(id, packageId) {
            Swal.fire({
                title: 'Delete Package?',
                text: 'Are you sure you want to delete package ' + packageId + '? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Deleting...',
                        text: 'Please wait while we delete the package.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const formData = new FormData();
                    formData.append('id', id);

                    fetch('ajax/delete-tour-package.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
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
        }
    </script>
    <script src="<?= APP_URL ?>javascript/main.js"></script>
</body>

</html>