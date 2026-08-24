<?php
require_once 'config/config.php';
require_once 'config/function.php';
requireLogin();

if (!verifyToken($pdo)) {
    header("Location: " . APP_URL . "login.php");
    exit();
}

$currentUser = getCurrentUser($pdo);
$pageTitle = "Travel Bookings";

$currencyCode = getCurrencyCode($pdo);
$currencySymbol = getCurrencySymbol($currencyCode);

$stmt = $pdo->query("SELECT tb.*, u.name as user_name FROM travel_bookings tb LEFT JOIN users u ON tb.user_id = u.id ORDER BY tb.created_at DESC");
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>Travel Bookings · Tour Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        .booking-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
            border-radius: 12px;
            padding: 0.9rem;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 4px 16px rgba(0, 20, 30, 0.04);
            transition: all 0.3s ease;
            height: 100%;
        }

        .booking-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 20, 30, 0.08);
            border-color: #ffd966;
        }

        .booking-card .booking-id {
            font-size: 0.6rem;
            color: #9bb2c5;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .booking-card .booking-car {
            font-weight: 600;
            color: #123b4f;
            font-size: 0.95rem;
            margin: 0.15rem 0;
        }

        .booking-card .booking-car .car-type {
            font-weight: 400;
            font-size: 0.7rem;
            color: #5f7d92;
        }

        .booking-card .booking-meta {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin: 0.3rem 0;
        }

        .booking-card .booking-meta .badge-custom {
            background: rgba(255, 215, 100, 0.2);
            color: #b8860b;
            padding: 0.1rem 0.5rem;
            border-radius: 16px;
            font-size: 0.6rem;
            font-weight: 600;
        }

        .booking-card .booking-meta .badge-days {
            background: rgba(18, 59, 79, 0.1);
            color: #123b4f;
            padding: 0.1rem 0.5rem;
            border-radius: 16px;
            font-size: 0.6rem;
            font-weight: 600;
        }

        .booking-card .booking-meta .badge-seats {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            padding: 0.1rem 0.5rem;
            border-radius: 16px;
            font-size: 0.6rem;
            font-weight: 600;
        }

        .booking-card .booking-meta .badge-status {
            padding: 0.1rem 0.5rem;
            border-radius: 16px;
            font-size: 0.6rem;
            font-weight: 600;
        }

        .badge-status.pending {
            background: rgba(255, 193, 7, 0.15);
            color: #ffc107;
        }
        .badge-status.confirmed {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }
        .badge-status.cancelled {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }
        .badge-status.completed {
            background: rgba(23, 162, 184, 0.15);
            color: #17a2b8;
        }

        .booking-card .booking-price {
            font-weight: 700;
            color: #123b4f;
            font-size: 1rem;
        }

        .booking-card .booking-price .price-breakdown {
            font-size: 0.6rem;
            color: #5f7d92;
            font-weight: 400;
            display: block;
        }

        .booking-card .booking-stops {
            margin: 0.3rem 0;
        }

        .booking-card .booking-stops .stop-tag {
            display: inline-block;
            background: rgba(18, 59, 79, 0.05);
            color: #123b4f;
            padding: 0.05rem 0.4rem;
            border-radius: 10px;
            font-size: 0.55rem;
            margin: 1px;
        }

        .booking-card .booking-provide {
            margin: 0.3rem 0;
        }

        .booking-card .booking-provide .provide-tag {
            display: inline-block;
            background: rgba(40, 167, 69, 0.08);
            color: #28a745;
            padding: 0.05rem 0.4rem;
            border-radius: 10px;
            font-size: 0.55rem;
            margin: 1px;
        }

        .booking-card .booking-provide .provide-tag img {
            width: 12px;
            height: 12px;
            object-fit: contain;
            margin-right: 3px;
            vertical-align: middle;
        }

        .booking-card .booking-actions {
            display: flex;
            gap: 6px;
            margin-top: 0.6rem;
            padding-top: 0.6rem;
            border-top: 1px solid #e8edf3;
        }

        .booking-card .booking-actions .btn-action {
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

            .booking-card .booking-car {
                font-size: 0.85rem;
            }

            .booking-card .booking-price {
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
                <small>Travel Bookings</small>
            </div>
        </div>

        <div class="page-wrapper">
            <div class="page-header">
                <div>
                    <h4><i class="bi bi-car-front me-2" style="color:#f5b342;"></i>Travel Bookings</h4>
                </div>
                <a href="add-travel-booking.php" class="btn-add">
                    <i class="bi bi-plus-circle"></i> Add New
                </a>
            </div>

            <?php if (empty($bookings)): ?>
                <div class="empty-state">
                    <i class="bi bi-car-front"></i>
                    <h5>No Bookings Yet</h5>
                    <p>Create your first travel booking to get started.</p>
                    <a href="add-travel-booking.php" class="btn-add-empty">Add New Booking</a>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($bookings as $booking):
                        $stops = json_decode($booking['stops'], true) ?: [];
                        $provide = json_decode($booking['what_we_provide'], true) ?: [];
                        
                        // Handle both old format (with icons) and new format (simple strings)
                        $provideItems = [];
                        if (!empty($provide)) {
                            foreach ($provide as $item) {
                                if (is_array($item)) {
                                    // Old format: {'name': 'Item', 'icon': 'path'}
                                    $provideItems[] = [
                                        'name' => $item['name'] ?? 'Unknown',
                                        'icon' => $item['icon'] ?? null
                                    ];
                                } else {
                                    // New format: just a string
                                    $provideItems[] = [
                                        'name' => $item,
                                        'icon' => null
                                    ];
                                }
                            }
                        }
                    ?>
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="booking-card">
                                <div class="booking-id">
                                    <?= htmlspecialchars($booking['booking_id']) ?>
                                    <span style="font-weight:400;color:#9bb2c5;font-size:0.5rem;">by <?= htmlspecialchars($booking['user_name'] ?? 'Unknown') ?></span>
                                </div>
                                <div class="booking-car">
                                    <?= htmlspecialchars($booking['car_name']) ?>
                                    <span class="car-type">(<?= htmlspecialchars($booking['car_type'] ?? 'Sedan') ?>)</span>
                                </div>

                                <div class="booking-meta">
                                    <span class="badge-days"><i class="bi bi-calendar3 me-1"></i><?= $booking['days'] ?>d</span>
                                    <span class="badge-seats"><i class="bi bi-person me-1"></i><?= $booking['seat_count'] ?></span>
                                    <span class="badge-status <?= $booking['status'] ?>"><?= ucfirst($booking['status']) ?></span>
                                </div>

                                <div class="booking-price">
                                    <?= htmlspecialchars($currencySymbol) ?><?= number_format($booking['total_price'], 2) ?>
                                    <span class="price-breakdown">
                                        <?= htmlspecialchars($currencySymbol) ?><?= number_format($booking['per_day_price'], 2) ?>/d × <?= $booking['days'] ?> + <?= htmlspecialchars($currencySymbol) ?><?= number_format($booking['per_km_charge'], 2) ?>/km
                                    </span>
                                </div>

                                <?php if (!empty($stops)): ?>
                                    <div class="booking-stops">
                                        <?php foreach (array_slice($stops, 0, 2) as $stop): ?>
                                            <span class="stop-tag">
                                                <i class="bi bi-geo-alt me-1"></i>
                                                <?= htmlspecialchars($stop['pickup'] ?? '') ?> → <?= htmlspecialchars($stop['drop'] ?? '') ?>
                                                <?= isset($stop['distance']) ? number_format($stop['distance'], 1) : '--' ?>km
                                            </span>
                                        <?php endforeach; ?>
                                        <?php if (count($stops) > 2): ?>
                                            <span class="stop-tag">+<?= count($stops) - 2 ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($provideItems)): ?>
                                    <div class="booking-provide">
                                        <?php foreach (array_slice($provideItems, 0, 2) as $item): ?>
                                            <span class="provide-tag">
                                                <?php if (!empty($item['icon'])): ?>
                                                    <img src="<?= APP_URL . $item['icon'] ?>" alt="">
                                                <?php else: ?>
                                                    <i class="bi bi-check-circle-fill" style="font-size:0.5rem;"></i>
                                                <?php endif; ?>
                                                <?= htmlspecialchars($item['name']) ?>
                                            </span>
                                        <?php endforeach; ?>
                                        <?php if (count($provideItems) > 2): ?>
                                            <span class="provide-tag">+<?= count($provideItems) - 2 ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="booking-actions">
                                    <a href="edit-travel-booking.php?id=<?= $booking['id'] ?>" class="btn-action btn-edit">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <button class="btn-action btn-delete" onclick="deleteBooking(<?= $booking['id'] ?>, '<?= $booking['booking_id'] ?>')">
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
        function deleteBooking(id, bookingId) {
            Swal.fire({
                title: 'Delete Booking?',
                text: 'Are you sure you want to delete booking ' + bookingId + '? This action cannot be undone.',
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

                    fetch('ajax/delete-travel-booking.php', {
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