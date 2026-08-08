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

// Fetch car rentals
$stmt = $pdo->query("SELECT * FROM car_rentals ORDER BY created_at DESC");
$carRentals = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>Car Rentals · Tour Admin</title>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .car-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 20, 30, 0.06);
            transition: all 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.6);
            height: 100%;
        }

        .car-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 32px rgba(0, 20, 30, 0.1);
        }

        .car-card .car-image {
            width: 100%;
            height: 140px;
            object-fit: cover;
            background: #f4f7fc;
        }

        .car-card .car-body {
            padding: 0.8rem 1rem 1rem;
        }

        .car-card .car-name {
            font-weight: 700;
            color: #0b2a3e;
            font-size: 0.95rem;
            margin-bottom: 0.2rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .car-card .car-details {
            color: #5f7d92;
            font-size: 0.75rem;
            display: flex;
            flex-wrap: wrap;
            gap: 8px 12px;
            margin-top: 4px;
        }

        .car-card .car-details span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .car-card .car-details i {
            font-size: 0.7rem;
            color: #9bb2c5;
        }

        .car-card .car-price {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 0.6rem;
            padding-top: 0.6rem;
            border-top: 1px solid #f0f5fa;
        }

        .car-card .price {
            font-weight: 700;
            color: #0b2a3e;
            font-size: 1rem;
        }

        .car-card .price small {
            font-weight: 400;
            font-size: 0.65rem;
            color: #5f7d92;
        }

        .car-card .status-badge {
            padding: 0.2rem 0.6rem;
            border-radius: 30px;
            font-size: 0.6rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-available {
            background: #d4edda;
            color: #155724;
        }

        .status-booked {
            background: #f8d7da;
            color: #721c24;
        }

        .status-maintenance {
            background: #fff3cd;
            color: #856404;
        }

        .car-card .car-actions {
            display: flex;
            gap: 6px;
            margin-top: 0.6rem;
            padding-top: 0.6rem;
            border-top: 1px solid #f0f5fa;
        }

        .car-card .car-actions .btn-sm {
            padding: 0.2rem 0.6rem;
            font-size: 0.7rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-edit {
            background: #e8edf3;
            color: #5f7d92;
        }

        .btn-edit:hover {
            background: #d5dce6;
            color: #0b2a3e;
        }

        .btn-delete {
            background: #f8d7da;
            color: #721c24;
        }

        .btn-delete:hover {
            background: #f5c6cb;
            color: #721c24;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 10px;
        }

        .section-header h4 {
            font-weight: 600;
            color: #123b4f;
            margin: 0;
        }

        .section-header .badge-count {
            background: #fff7009b;
            color: #000000;
            padding: 0.2rem 0.8rem;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #5f7d92;
        }

        .empty-state i {
            font-size: 3rem;
            color: #d5dce6;
            margin-bottom: 1rem;
        }

        .empty-state h5 {
            color: #123b4f;
        }

        @media (max-width: 576px) {
            .car-card .car-image {
                height: 120px;
            }

            .car-card .car-name {
                font-size: 0.85rem;
            }

            .car-card .car-details {
                font-size: 0.7rem;
                gap: 4px 8px;
            }
        }
    </style>
</head>

<body>
    <!-- overlay mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ========== SIDE NAV ========== -->
    <?php include_once 'includes/nav.php'; ?>

    <!-- ========== MAIN ========== -->
    <div class="main-wrapper">

        <!-- TOP BAR compact -->
        <div class="top-bar">
            <button class="burger-btn" id="burgerBtn" aria-label="Toggle navigation">
                <i class="bi bi-list"></i>
            </button>
            <div class="greeting-center">
                Welcome back, <strong><?= htmlspecialchars($currentUser['name'] ?? 'Admin') ?></strong>
                <small>Manage car rentals</small>
            </div>
        </div>

        <!-- ====== CAR RENTALS LIST ====== -->
        <div class="section-header">
            <h4><i class="bi bi-car-front-fill me-2" style="color:#f5b342;"></i>Car Rentals</h4>
            <div>
                <span class="badge-count"><i class="bi bi-car-front me-1"></i><?= count($carRentals) ?> cars</span>
                <a href="add-car-rental.php" class="btn btn-primary ms-2" style="border-radius:12px; background:linear-gradient(145deg,#0b2a3e,#123b4f); border:none; padding:0.4rem 1.2rem; font-size:0.85rem;">
                    <i class="bi bi-plus-circle me-1"></i>Add New
                </a>
            </div>
        </div>

        <?php if (empty($carRentals)): ?>
            <div class="empty-state">
                <i class="bi bi-car-front"></i>
                <h5>No cars added yet</h5>
                <p class="text-muted">Click "Add New" to add your first car rental.</p>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($carRentals as $car): ?>
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                        <div class="car-card">
                            <img src="<?= APP_URL . $car['car_image'] ?>"
                                alt="<?= htmlspecialchars($car['car_name']) ?>"
                                class="car-image"
                                onerror="this.src='https://via.placeholder.com/300x140/123b4f/ffffff?text=Car'">
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
                                        <span><i class="bi bi-people"></i><?= $car['seating_capacity'] ?> seats</span>
                                    <?php endif; ?>
                                    <?php if ($car['fuel_type']): ?>
                                        <span><i class="bi bi-fuel-pump"></i><?= htmlspecialchars($car['fuel_type']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="car-price">
                                    <span class="price">$<?= number_format($car['per_day_amount'], 2) ?> <small>/ day</small></span>
                                    <span class="status-badge status-<?= $car['status'] ?>"><?= $car['status'] ?></span>
                                </div>
                                <div class="car-actions">
                                    <button class="btn-sm btn-edit" onclick="editCar(<?= $car['id'] ?>)">
                                        <i class="bi bi-pencil"></i> Edit
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>
    <script src="<?= APP_URL ?>javascript/main.js"></script>
    <script src="<?= APP_URL ?>javascript/car-rentals.js"></script>
</body>

</html>