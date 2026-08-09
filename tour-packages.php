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
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-header h4 {
            font-weight: 600;
            color: #123b4f;
            margin-bottom: 0;
        }

        .page-header p {
            color: #5f7d92;
            margin-bottom: 0;
            font-size: 0.9rem;
        }

        .btn-add {
            background: linear-gradient(145deg, #0b2a3e 0%, #123b4f 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 0.6rem 1.8rem;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(11, 42, 62, 0.2);
            color: #ffd966;
        }

        .package-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
            border-radius: 16px;
            padding: 1.25rem;
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
            height: 180px;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 1rem;
            background: #f0f3f7;
        }

        .package-card .package-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .package-card .package-id {
            font-size: 0.7rem;
            color: #9bb2c5;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .package-card .package-name {
            font-weight: 600;
            color: #123b4f;
            font-size: 1.1rem;
            margin: 0.25rem 0;
        }

        .package-card .package-meta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin: 0.5rem 0;
        }

        .package-card .package-meta .badge-custom {
            background: rgba(255, 215, 100, 0.2);
            color: #b8860b;
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .package-card .package-meta .badge-days {
            background: rgba(18, 59, 79, 0.1);
            color: #123b4f;
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .package-card .package-meta .badge-status {
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
            font-size: 0.7rem;
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
            font-size: 1.2rem;
        }

        .package-card .package-price .discount {
            color: #dc3545;
            font-size: 0.9rem;
            text-decoration: line-through;
            margin-left: 8px;
            font-weight: 400;
        }

        .package-card .package-actions {
            display: flex;
            gap: 8px;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e8edf3;
        }

        .package-card .package-actions .btn-action {
            padding: 0.3rem 1rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 500;
            border: none;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
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

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state i {
            font-size: 4rem;
            color: #e8edf3;
        }

        .empty-state h5 {
            color: #123b4f;
            margin-top: 1rem;
        }

        .empty-state p {
            color: #5f7d92;
        }

        @media (max-width: 768px) {
            .page-wrapper {
                padding: 10px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-header .btn-add {
                width: 100%;
                text-align: center;
            }

            .package-card .package-image {
                height: 140px;
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
                    <p>Manage all your tour packages</p>
                </div>
                <a href="tour-package-add.php" class="btn-add">
                    <i class="bi bi-plus-circle me-2"></i>Add New Package
                </a>
            </div>

            <?php if (empty($packages)): ?>
                <div class="empty-state">
                    <i class="bi bi-suitcase"></i>
                    <h5>No Tour Packages Yet</h5>
                    <p>Create your first tour package to get started.</p>
                    <a href="tour-package-add.php" class="btn-add mt-3" style="display:inline-block;">
                        <i class="bi bi-plus-circle me-2"></i>Add New Package
                    </a>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($packages as $package): ?>
                        <?php 
                            $gallery = json_decode($package['gallery_images'], true) ?: [];
                            $features = json_decode($package['features'], true) ?: [];
                        ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="package-card">
                                <div class="package-image">
                                    <?php if (!empty($package['main_image'])): ?>
                                        <img src="<?= APP_URL . $package['main_image'] ?>" alt="<?= htmlspecialchars($package['package_name']) ?>">
                                    <?php else: ?>
                                        <img src="<?= APP_URL ?>assets/images/no-image.jpg" alt="No image">
                                    <?php endif; ?>
                                </div>
                                
                                <div class="package-id"><?= $package['package_id'] ?></div>
                                <h5 class="package-name"><?= htmlspecialchars($package['package_name']) ?></h5>
                                
                                <div class="package-meta">
                                    <span class="badge-days"><i class="bi bi-calendar3 me-1"></i><?= $package['days_count'] ?> Days</span>
                                    <?php if ($package['adults'] > 0): ?>
                                        <span class="badge-custom"><i class="bi bi-person me-1"></i><?= $package['adults'] ?> Adults</span>
                                    <?php endif; ?>
                                    <?php if ($package['children'] > 0): ?>
                                        <span class="badge-custom"><i class="bi bi-person me-1"></i><?= $package['children'] ?> Children</span>
                                    <?php endif; ?>
                                    <?php if ($package['infants'] > 0): ?>
                                        <span class="badge-custom"><i class="bi bi-person me-1"></i><?= $package['infants'] ?> Infants</span>
                                    <?php endif; ?>
                                    <span class="badge-status <?= $package['status'] ?>"><?= ucfirst($package['status']) ?></span>
                                </div>
                                
                                <?php if (!empty($features)): ?>
                                    <div style="margin: 0.5rem 0;">
                                        <?php foreach (array_slice($features, 0, 3) as $feature): ?>
                                            <span class="badge-custom" style="background:rgba(18,59,79,0.05);color:#123b4f;font-size:0.65rem;">
                                                <?php if (!empty($feature['icon'])): ?>
                                                    <img src="<?= APP_URL . $feature['icon'] ?>" style="width:14px;height:14px;object-fit:contain;display:inline;margin-right:4px;">
                                                <?php else: ?>
                                                    <i class="bi bi-check-circle-fill" style="font-size:0.6rem;"></i>
                                                <?php endif; ?>
                                                <?= htmlspecialchars($feature['name']) ?>
                                            </span>
                                        <?php endforeach; ?>
                                        <?php if (count($features) > 3): ?>
                                            <span class="badge-custom" style="background:rgba(18,59,79,0.05);color:#123b4f;font-size:0.65rem;">+<?= count($features) - 3 ?> more</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="package-price">
                                    $<?= number_format($package['price'], 2) ?>
                                    <?php if (!empty($package['discount_price'])): ?>
                                        <span class="discount">$<?= number_format($package['discount_price'], 2) ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="package-actions">
                                    <a href="tour-package-edit.php?id=<?= $package['id'] ?>" class="btn-action btn-edit">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="tour-package-view.php?id=<?= $package['id'] ?>" class="btn-action btn-view">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <button class="btn-action btn-delete" onclick="deletePackage(<?= $package['id'] ?>, '<?= $package['package_id'] ?>')">
                                        <i class="bi bi-trash"></i> Delete
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
                    const formData = new FormData();
                    formData.append('id', id);

                    fetch('ajax/delete-tour-package.php', {
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
                                text: data.message
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An error occurred. Please try again.'
                        });
                    });
                }
            });
        }
    </script>
</body>

</html>