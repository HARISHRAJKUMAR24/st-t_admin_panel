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

// Get currency from settings
$currencyCode = getCurrencyCode($pdo);
$currencySymbol = getCurrencySymbol($currencyCode);

// Fetch Vehicles
$stmt = $pdo->query("SELECT * FROM vehicles ORDER BY created_at DESC");
$vehicles = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>Vehicles · Tour Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .page-wrapper {
            padding: 20px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .section-header h4 {
            font-weight: 600;
            color: #123b4f;
            margin: 0;
            font-size: 1.2rem;
        }

        .section-header .badge-count {
            background: rgba(255, 215, 100, 0.2);
            color: #b8860b;
            padding: 0.15rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .btn-add {
            background: linear-gradient(145deg, #0b2a3e 0%, #123b4f 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.35rem 1rem;
            font-size: 0.8rem;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(11, 42, 62, 0.2);
            color: #ffd966;
        }

        .vehicle-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 20, 30, 0.04);
            transition: all 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.6);
            height: 100%;
        }

        .vehicle-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 20, 30, 0.08);
            border-color: #ffd966;
        }

        .vehicle-card .vehicle-image {
            width: 100%;
            height: 110px;
            object-fit: contain;
            background: #f4f7fc;
        }

        .vehicle-card .vehicle-body {
            padding: 0.6rem 0.7rem 0.7rem;
        }

        .vehicle-card .vehicle-name {
            font-weight: 700;
            color: #0b2a3e;
            font-size: 0.85rem;
            margin-bottom: 0.15rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .vehicle-card .vehicle-details {
            color: #5f7d92;
            font-size: 0.65rem;
            display: flex;
            flex-wrap: wrap;
            gap: 4px 8px;
            margin-top: 2px;
        }

        .vehicle-card .vehicle-details span {
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .vehicle-card .vehicle-details i {
            font-size: 0.6rem;
            color: #9bb2c5;
        }

        .vehicle-card .vehicle-price {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 0.4rem;
            padding-top: 0.4rem;
            border-top: 1px solid #f0f5fa;
        }

        .vehicle-card .price {
            font-weight: 700;
            color: #0b2a3e;
            font-size: 0.9rem;
        }

        .vehicle-card .price .currency-symbol {
            font-weight: 700;
            margin-right: 1px;
        }

        .vehicle-card .price small {
            font-weight: 400;
            font-size: 0.55rem;
            color: #5f7d92;
        }

        .vehicle-card .pricing-badge {
            padding: 0.1rem 0.4rem;
            border-radius: 10px;
            font-size: 0.5rem;
            font-weight: 600;
            text-transform: uppercase;
            background: rgba(255, 215, 100, 0.2);
            color: #b8860b;
            margin-left: 4px;
        }

        .vehicle-card .pricing-badge.package {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }

        .vehicle-card .pricing-badge.perday {
            background: rgba(13, 110, 253, 0.15);
            color: #0d6efd;
        }

        .vehicle-card .status-badge {
            padding: 0.1rem 0.5rem;
            border-radius: 20px;
            font-size: 0.55rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-available {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }

        .status-booked {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }

        .status-maintenance {
            background: rgba(255, 193, 7, 0.15);
            color: #ffc107;
        }

        .vehicle-card .vehicle-actions {
            display: flex;
            gap: 4px;
            margin-top: 0.4rem;
            padding-top: 0.4rem;
            border-top: 1px solid #f0f5fa;
        }

        .vehicle-card .vehicle-actions .btn-sm {
            padding: 0.15rem 0.5rem;
            font-size: 0.65rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }

        .btn-edit {
            background: rgba(18, 59, 79, 0.08);
            color: #123b4f;
        }

        .btn-edit:hover {
            background: #123b4f;
            color: #fff;
        }

        .btn-delete {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .btn-delete:hover {
            background: #dc3545;
            color: #fff;
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

            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .section-header .btn-add {
                width: 100%;
                justify-content: center;
            }

            .vehicle-card .vehicle-image {
                height: 90px;
            }

            .vehicle-card .vehicle-name {
                font-size: 0.75rem;
            }

            .vehicle-card .price {
                font-size: 0.8rem;
            }

            .vehicle-card .vehicle-details {
                font-size: 0.6rem;
            }
        }

        @media (max-width: 576px) {
            .vehicle-card .vehicle-image {
                height: 80px;
            }

            .vehicle-card .vehicle-name {
                font-size: 0.7rem;
            }

            .vehicle-card .price {
                font-size: 0.75rem;
            }

            .vehicle-card .vehicle-details {
                font-size: 0.55rem;
                gap: 2px 4px;
            }

            .vehicle-card .vehicle-body {
                padding: 0.4rem 0.5rem 0.5rem;
            }

            .vehicle-card .vehicle-actions .btn-sm {
                font-size: 0.55rem;
                padding: 0.1rem 0.4rem;
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
                <small>Vehicles</small>
            </div>
        </div>

        <div class="page-wrapper">
            <div class="section-header">
                <h4><i class="bi bi-car-front-fill me-2" style="color:#f5b342;"></i>Vehicles</h4>
                <div>
                    <span class="badge-count"><i class="bi bi-car-front me-1"></i><?= count($vehicles) ?></span>
                    <a href="add-vehicle.php" class="btn-add ms-2">
                        <i class="bi bi-plus-circle"></i> Add New
                    </a>
                </div>
            </div>

            <?php if (empty($vehicles)): ?>
                <div class="empty-state">
                    <i class="bi bi-car-front"></i>
                    <h5>No vehicles added yet</h5>
                    <p>Click "Add New" to add your first vehicle.</p>
                   
                </div>
            <?php else: ?>
                <div class="row g-2">
                    <?php foreach ($vehicles as $vehicle): 
                        // Get pricing type label
                        $pricingType = $vehicle['pricing_type'] ?? 'perday';
                        $pricingLabel = $pricingType == 'package' ? 'Package' : 'Per Day';
                        $pricingClass = $pricingType == 'package' ? 'package' : 'perday';
                        
                        // Get price display
                        if ($pricingType == 'package') {
                            $priceDisplay = number_format($vehicle['package_price'] ?? 0, 0);
                            $priceSuffix = '/pkg';
                        } else {
                            $priceDisplay = number_format($vehicle['per_day_amount'], 0);
                            $priceSuffix = '/d';
                        }
                    ?>
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                            <div class="vehicle-card">
                                <img src="<?= APP_URL . $vehicle['vehicle_image'] ?>"
                                    alt="<?= htmlspecialchars($vehicle['vehicle_name']) ?>"
                                    class="vehicle-image">
                                <div class="vehicle-body">
                                    <div class="vehicle-name" title="<?= htmlspecialchars($vehicle['vehicle_name']) ?>">
                                        <?= htmlspecialchars($vehicle['vehicle_name']) ?>
                                    </div>
                                    <div class="vehicle-details">
                                        <?php if ($vehicle['vehicle_brand']): ?>
                                            <span><i class="bi bi-building"></i><?= htmlspecialchars($vehicle['vehicle_brand']) ?></span>
                                        <?php endif; ?>
                                        <?php if ($vehicle['vehicle_model']): ?>
                                            <span><i class="bi bi-tag"></i><?= htmlspecialchars($vehicle['vehicle_model']) ?></span>
                                        <?php endif; ?>
                                        <?php if ($vehicle['seating_capacity'] > 0): ?>
                                            <span><i class="bi bi-people"></i><?= $vehicle['seating_capacity'] ?></span>
                                        <?php endif; ?>
                                        <?php if ($vehicle['fuel_type']): ?>
                                            <span><i class="bi bi-fuel-pump"></i><?= htmlspecialchars(substr($vehicle['fuel_type'], 0, 3)) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="vehicle-price">
                                        <span class="price">
                                            <span class="currency-symbol"><?= htmlspecialchars($currencySymbol) ?></span>
                                            <?= $priceDisplay ?>
                                            <small><?= $priceSuffix ?></small>
                                            <span class="pricing-badge <?= $pricingClass ?>"><?= $pricingLabel ?></span>
                                        </span>
                                        <span class="status-badge status-<?= $vehicle['status'] ?>"><?= substr($vehicle['status'], 0, 4) ?></span>
                                    </div>
                                    <div class="vehicle-actions">
                                        <button class="btn-sm btn-edit" onclick="editVehicle(<?= $vehicle['id'] ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn-sm btn-delete" onclick="deleteVehicle(<?= $vehicle['id'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
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
        function editVehicle(id) {
            window.location.href = 'edit-vehicle.php?id=' + id;
        }

        function deleteVehicle(id) {
            Swal.fire({
                title: 'Delete Vehicle?',
                text: 'Are you sure you want to delete this vehicle? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Deleting...',
                        text: 'Please wait...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch('ajax/delete-vehicle.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                id: id
                            })
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
</body>

</html>