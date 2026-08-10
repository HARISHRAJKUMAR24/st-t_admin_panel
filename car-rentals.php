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

// Fetch car rentals
$stmt = $pdo->query("SELECT * FROM car_rentals ORDER BY created_at DESC");
$carRentals = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>Car Rentals · Tour Admin</title>
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

        .car-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 20, 30, 0.04);
            transition: all 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.6);
            height: 100%;
        }

        .car-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 20, 30, 0.08);
            border-color: #ffd966;
        }

        .car-card .car-image {
            width: 100%;
            height: 110px;
            object-fit: contain;
            background: #f4f7fc;
        }

        .car-card .car-body {
            padding: 0.6rem 0.7rem 0.7rem;
        }

        .car-card .car-name {
            font-weight: 700;
            color: #0b2a3e;
            font-size: 0.85rem;
            margin-bottom: 0.15rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .car-card .car-details {
            color: #5f7d92;
            font-size: 0.65rem;
            display: flex;
            flex-wrap: wrap;
            gap: 4px 8px;
            margin-top: 2px;
        }

        .car-card .car-details span {
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .car-card .car-details i {
            font-size: 0.6rem;
            color: #9bb2c5;
        }

        .car-card .car-price {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 0.4rem;
            padding-top: 0.4rem;
            border-top: 1px solid #f0f5fa;
        }

        .car-card .price {
            font-weight: 700;
            color: #0b2a3e;
            font-size: 0.9rem;
        }

        .car-card .price .currency-symbol {
            font-weight: 700;
            margin-right: 1px;
        }

        .car-card .price small {
            font-weight: 400;
            font-size: 0.55rem;
            color: #5f7d92;
        }

        .car-card .status-badge {
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

        .car-card .car-actions {
            display: flex;
            gap: 4px;
            margin-top: 0.4rem;
            padding-top: 0.4rem;
            border-top: 1px solid #f0f5fa;
        }

        .car-card .car-actions .btn-sm {
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

            .car-card .car-image {
                height: 90px;
            }

            .car-card .car-name {
                font-size: 0.75rem;
            }

            .car-card .price {
                font-size: 0.8rem;
            }

            .car-card .car-details {
                font-size: 0.6rem;
            }
        }

        @media (max-width: 576px) {
            .car-card .car-image {
                height: 80px;
            }

            .car-card .car-name {
                font-size: 0.7rem;
            }

            .car-card .price {
                font-size: 0.75rem;
            }

            .car-card .car-details {
                font-size: 0.55rem;
                gap: 2px 4px;
            }

            .car-card .car-body {
                padding: 0.4rem 0.5rem 0.5rem;
            }

            .car-card .car-actions .btn-sm {
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
                <small>Car Rentals</small>
            </div>
        </div>

        <div class="page-wrapper">
            <div class="section-header">
                <h4><i class="bi bi-car-front-fill me-2" style="color:#f5b342;"></i>Car Rentals</h4>
                <div>
                    <span class="badge-count"><i class="bi bi-car-front me-1"></i><?= count($carRentals) ?></span>
                    <a href="add-car-rental.php" class="btn-add ms-2">
                        <i class="bi bi-plus-circle"></i> Add New
                    </a>
                </div>
            </div>

            <?php if (empty($carRentals)): ?>
                <div class="empty-state">
                    <i class="bi bi-car-front"></i>
                    <h5>No cars added yet</h5>
                    <p>Click "Add New" to add your first car rental.</p>
                    <a href="add-car-rental.php" class="btn-add-empty">
                        <i class="bi bi-plus-circle me-2"></i>Add New Car
                    </a>
                </div>
            <?php else: ?>
                <div class="row g-2">
                    <?php foreach ($carRentals as $car): ?>
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                            <div class="car-card">
                                <img src="<?= APP_URL . $car['car_image'] ?>"
                                    alt="<?= htmlspecialchars($car['car_name']) ?>"
                                    class="car-image"
                                    onerror="this.src='https://via.placeholder.com/300x110/123b4f/ffffff?text=Car'">
                                <div class="car-body">
                                    <div class="car-name" title="<?= htmlspecialchars($car['car_name']) ?>">
                                        <?= htmlspecialchars($car['car_name']) ?>
                                    </div>
                                    <div class="car-details">
                                        <?php if ($car['car_brand']): ?>
                                            <span><i class="bi bi-building"></i><?= htmlspecialchars($car['car_brand']) ?></span>
                                        <?php endif; ?>
                                        <?php if ($car['car_model']): ?>
                                            <span><i class="bi bi-tag"></i><?= htmlspecialchars($car['car_model']) ?></span>
                                        <?php endif; ?>
                                        <?php if ($car['seating_capacity'] > 0): ?>
                                            <span><i class="bi bi-people"></i><?= $car['seating_capacity'] ?></span>
                                        <?php endif; ?>
                                        <?php if ($car['fuel_type']): ?>
                                            <span><i class="bi bi-fuel-pump"></i><?= htmlspecialchars(substr($car['fuel_type'], 0, 3)) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="car-price">
                                        <span class="price">
                                            <span class="currency-symbol"><?= htmlspecialchars($currencySymbol) ?></span>
                                            <?= number_format($car['per_day_amount'], 0) ?>
                                            <small>/d</small>
                                        </span>
                                        <span class="status-badge status-<?= $car['status'] ?>"><?= substr($car['status'], 0, 4) ?></span>
                                    </div>
                                    <div class="car-actions">
                                        <button class="btn-sm btn-edit" onclick="editCar(<?= $car['id'] ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn-sm btn-delete" onclick="deleteCar(<?= $car['id'] ?>)">
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
        function editCar(id) {
            window.location.href = 'edit-car-rental.php?id=' + id;
        }

        function deleteCar(id) {
            Swal.fire({
                title: 'Delete Car?',
                text: 'Are you sure you want to delete this car? This action cannot be undone.',
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
                        didOpen: () => { Swal.showLoading(); }
                    });

                    const formData = new FormData();
                    formData.append('id', id);

                    fetch('ajax/delete-car-rental.php', {
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
</body>

</html>