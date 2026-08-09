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
$pageTitle = "Offers";

// Get currency from settings
$currencyCode = getCurrencyCode($pdo);
$currencySymbol = getCurrencySymbol($currencyCode);

// Fetch all offers
$stmt = $pdo->query("SELECT * FROM offers ORDER BY created_at DESC");
$offers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>Offers · Tour Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            text-decoration: none;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(11, 42, 62, 0.2);
            color: #ffd966;
        }

        .offer-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
            border-radius: 16px;
            padding: 1.25rem;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 4px 16px rgba(0, 20, 30, 0.04);
            transition: all 0.3s ease;
            height: 100%;
        }

        .offer-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 20, 30, 0.08);
            border-color: #ffd966;
        }

        .offer-card .offer-image {
            width: 100%;
            height: 160px;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 1rem;
            background: #f0f3f7;
        }

        .offer-card .offer-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .offer-card .offer-code {
            font-size: 0.7rem;
            color: #9bb2c5;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .offer-card .offer-title {
            font-weight: 600;
            color: #123b4f;
            font-size: 1.1rem;
            margin: 0.25rem 0;
        }

        .offer-card .offer-discount {
            font-size: 1.5rem;
            font-weight: 700;
            color: #dc3545;
        }

        .offer-card .offer-discount .discount-type {
            font-size: 0.8rem;
            font-weight: 500;
            color: #5f7d92;
        }

        .offer-card .offer-meta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin: 0.5rem 0;
        }

        .offer-card .offer-meta .badge-custom {
            background: rgba(255, 215, 100, 0.2);
            color: #b8860b;
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .offer-card .offer-meta .badge-date {
            background: rgba(18, 59, 79, 0.1);
            color: #123b4f;
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .offer-card .offer-meta .badge-status {
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

        .badge-status.expired {
            background: rgba(255, 193, 7, 0.15);
            color: #ffc107;
        }

        .offer-card .offer-packages {
            margin: 0.5rem 0;
        }

        .offer-card .offer-packages .package-tag {
            display: inline-block;
            background: rgba(18, 59, 79, 0.05);
            color: #123b4f;
            padding: 0.1rem 0.6rem;
            border-radius: 12px;
            font-size: 0.65rem;
            margin: 2px;
        }

        .offer-card .offer-actions {
            display: flex;
            gap: 8px;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e8edf3;
        }

        .offer-card .offer-actions .btn-action {
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

            .offer-card .offer-image {
                height: 120px;
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
                <small>Offers</small>
            </div>
        </div>

        <div class="page-wrapper">
            <div class="page-header">
                <div>
                    <h4><i class="bi bi-tags me-2" style="color:#f5b342;"></i>Offers</h4>
                    <p>Manage all your promotional offers</p>
                </div>
                <a href="add-offer.php" class="btn-add">
                    <i class="bi bi-plus-circle me-2"></i>Add New Offer
                </a>
            </div>

            <?php if (empty($offers)): ?>
                <div class="empty-state">
                    <i class="bi bi-tags"></i>
                    <h5>No Offers Yet</h5>
                    <p>Create your first promotional offer to get started.</p>
                   
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($offers as $offer):
                        $packageIds = json_decode($offer['tour_packages'], true) ?: [];
                        $packageNames = [];
                        if (!empty($packageIds)) {
                            $placeholders = implode(',', array_fill(0, count($packageIds), '?'));
                            $stmt = $pdo->prepare("SELECT package_id, package_name FROM tour_packages WHERE id IN ($placeholders)");
                            $stmt->execute($packageIds);
                            $packages = $stmt->fetchAll();
                            foreach ($packages as $pkg) {
                                $packageNames[] = $pkg['package_name'] . ' (' . $pkg['package_id'] . ')';
                            }
                        }
                    ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="offer-card">
                                <div class="offer-image">
                                    <?php if (!empty($offer['main_image'])): ?>
                                        <img src="<?= APP_URL . $offer['main_image'] ?>" alt="<?= htmlspecialchars($offer['title']) ?>">
                                    <?php else: ?>
                                        <img src="<?= APP_URL ?>assets/images/no-image.jpg" alt="No image">
                                    <?php endif; ?>
                                </div>

                                <div class="offer-code"><?= htmlspecialchars($offer['offer_code']) ?></div>
                                <h5 class="offer-title"><?= htmlspecialchars($offer['title']) ?></h5>

                                <div class="offer-discount">
                                    <?php if ($offer['discount_type'] == 'percentage'): ?>
                                        <?= number_format($offer['discount_value'], 0) ?>%
                                    <?php else: ?>
                                        <?= htmlspecialchars($currencySymbol) ?><?= number_format($offer['discount_value'], 2) ?>
                                    <?php endif; ?>
                                    <span class="discount-type"><?= ucfirst($offer['discount_type']) ?></span>
                                </div>

                                <div class="offer-meta">
                                    <?php if (!empty($offer['start_date'])): ?>
                                        <span class="badge-date"><i class="bi bi-calendar3 me-1"></i><?= date('M d, Y', strtotime($offer['start_date'])) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($offer['end_date'])): ?>
                                        <span class="badge-date"><i class="bi bi-calendar3 me-1"></i>Until <?= date('M d, Y', strtotime($offer['end_date'])) ?></span>
                                    <?php endif; ?>
                                    <span class="badge-status <?= $offer['status'] ?>"><?= ucfirst($offer['status']) ?></span>
                                </div>

                                <?php if (!empty($packageNames)): ?>
                                    <div class="offer-packages">
                                        <?php foreach (array_slice($packageNames, 0, 3) as $name): ?>
                                            <span class="package-tag"><?= htmlspecialchars($name) ?></span>
                                        <?php endforeach; ?>
                                        <?php if (count($packageNames) > 3): ?>
                                            <span class="package-tag">+<?= count($packageNames) - 3 ?> more</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($offer['description'])): ?>
                                    <p style="font-size:0.8rem;color:#5f7d92;margin:0.5rem 0;"><?= htmlspecialchars(substr($offer['description'], 0, 80)) ?>...</p>
                                <?php endif; ?>

                                <div class="offer-actions">
                                    <a href="edit-offer.php?offer_id=<?= $offer['offer_code'] ?>" class="btn-action btn-edit">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <button class="btn-action btn-delete" onclick="deleteOffer(<?= $offer['id'] ?>, '<?= $offer['offer_code'] ?>')">
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

    <script>
        function deleteOffer(id, offerCode) {
            Swal.fire({
                title: 'Delete Offer?',
                text: 'Are you sure you want to delete offer ' + offerCode + '? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Deleting...',
                        text: 'Please wait while we delete the offer.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const formData = new FormData();
                    formData.append('id', id);

                    fetch('ajax/delete-offer.php', {
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
        }
    </script>
    <script src="<?= APP_URL ?>javascript/main.js"></script>
</body>

</html>