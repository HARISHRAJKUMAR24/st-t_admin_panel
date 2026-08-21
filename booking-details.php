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
$pageTitle = "Booking Details";

// Get parameters
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$table = isset($_GET['table']) ? trim($_GET['table']) : '';

if (empty($type) || $id <= 0 || empty($table)) {
    header("Location: bookings.php");
    exit();
}

// Validate table name
$allowedTables = [
    'customer_tour_bookings',
    'customer_travel_bookings',
    'customer_car_rentals_bookings',
    'cta_messages'
];

if (!in_array($table, $allowedTables)) {
    header("Location: bookings.php");
    exit();
}

// Fetch booking details
$stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
$stmt->execute([$id]);
$booking = $stmt->fetch();

if (!$booking) {
    header("Location: bookings.php");
    exit();
}

// Get currency symbol
$currencyCode = getCurrencyCode($pdo);
$currencySymbol = getCurrencySymbol($currencyCode);

// Define type labels and icons
$typeLabels = [
    'tour' => ['label' => 'Tour Booking', 'icon' => 'bi bi-suitcase'],
    'travel' => ['label' => 'Travel Booking', 'icon' => 'bi bi-geo-alt'],
    'car' => ['label' => 'Car Booking', 'icon' => 'bi bi-car-front'],
    'cta' => ['label' => 'CTA Message', 'icon' => 'bi bi-chat-dots']
];

$typeInfo = $typeLabels[$type] ?? ['label' => 'Booking', 'icon' => 'bi bi-calendar'];

// Decode JSON data if exists
$stops = isset($booking['stops']) ? json_decode($booking['stops'], true) : [];
$whatWeProvide = isset($booking['what_we_provide']) ? json_decode($booking['what_we_provide'], true) : [];
$members = isset($booking['members']) ? json_decode($booking['members'], true) : [];
$itinerary = isset($booking['itinerary']) ? json_decode($booking['itinerary'], true) : [];
$features = isset($booking['features']) ? json_decode($booking['features'], true) : [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>Booking Details · Tour Admin</title>
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

        .details-container {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 32px rgba(0, 20, 30, 0.06);
            max-width: 900px;
            margin: 0 auto;
        }

        .details-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #ffd966;
            flex-wrap: wrap;
            gap: 10px;
        }

        .details-header .booking-id {
            font-size: 0.85rem;
            color: #5f7d92;
        }

        .details-header .booking-id strong {
            color: #123b4f;
        }

        .details-header .type-badge {
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .type-badge.tour {
            background: rgba(255, 215, 100, 0.2);
            color: #b8860b;
        }
        .type-badge.travel {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }
        .type-badge.car {
            background: rgba(0, 123, 255, 0.15);
            color: #007bff;
        }
        .type-badge.cta {
            background: rgba(111, 66, 193, 0.15);
            color: #6f42c1;
        }

        .detail-section {
            margin-bottom: 1.5rem;
        }

        .detail-section .section-title {
            font-weight: 600;
            color: #123b4f;
            font-size: 0.9rem;
            margin-bottom: 0.8rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e8edf3;
        }

        .detail-row {
            display: flex;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f5f7fa;
        }

        .detail-row .label {
            font-weight: 500;
            color: #5f7d92;
            width: 180px;
            flex-shrink: 0;
            font-size: 0.85rem;
        }

        .detail-row .value {
            color: #123b4f;
            font-weight: 500;
            font-size: 0.85rem;
            word-break: break-word;
        }

        .detail-row .value .badge-status {
            padding: 0.2rem 0.7rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
            display: inline-block;
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
        .badge-status.read {
            background: rgba(0, 123, 255, 0.15);
            color: #007bff;
        }
        .badge-status.replied {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }
        .badge-status.available {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }
        .badge-status.booked {
            background: rgba(255, 193, 7, 0.15);
            color: #ffc107;
        }
        .badge-status.maintenance {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }

        .stops-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .stop-item {
            background: rgba(255, 255, 255, 0.5);
            padding: 0.8rem 1rem;
            border-radius: 10px;
            border: 1px solid #e8edf3;
        }

        .stop-item .stop-route {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .stop-item .stop-route .arrow {
            color: #f5b342;
            font-weight: 700;
        }

        .stop-item .stop-route .location {
            font-weight: 500;
            color: #123b4f;
        }

        .stop-item .stop-route .distance {
            font-size: 0.75rem;
            color: #5f7d92;
        }

        .features-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .feature-tag {
            display: inline-flex;
            align-items: center;
            background: rgba(18, 59, 79, 0.05);
            color: #123b4f;
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-size: 0.7rem;
            gap: 4px;
        }

        .feature-tag img {
            width: 16px;
            height: 16px;
            object-fit: contain;
        }

        .feature-tag i {
            font-size: 0.6rem;
        }

        .member-tag {
            display: inline-flex;
            align-items: center;
            background: rgba(255, 215, 100, 0.15);
            color: #b8860b;
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-size: 0.7rem;
            gap: 4px;
        }

        .member-tag .count {
            background: rgba(255, 255, 255, 0.5);
            padding: 0 4px;
            border-radius: 8px;
            font-weight: 700;
        }

        .btn-actions {
            display: flex;
            gap: 10px;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 2px solid #f0f3f7;
            flex-wrap: wrap;
        }

        .btn-actions .btn {
            padding: 0.5rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.85rem;
            border: none;
            transition: all 0.3s;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-edit {
            background: linear-gradient(145deg, #0b2a3e 0%, #123b4f 100%);
            color: #fff;
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(11, 42, 62, 0.2);
            color: #ffd966;
        }

        .btn-back {
            background: #e8edf3;
            color: #5f7d92;
        }

        .btn-back:hover {
            background: #d5dce6;
            color: #123b4f;
        }

        .btn-delete {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .btn-delete:hover {
            background: #dc3545;
            color: #fff;
        }

        .status-change {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-change select {
            padding: 0.3rem 0.8rem;
            border-radius: 8px;
            border: 2px solid #e8edf3;
            font-size: 0.8rem;
            outline: none;
            transition: all 0.2s;
        }

        .status-change select:focus {
            border-color: #ffd966;
            box-shadow: 0 0 0 3px rgba(255, 215, 100, 0.15);
        }

        .status-change .btn-update {
            background: #ffd966;
            color: #123b4f;
            border: none;
            border-radius: 8px;
            padding: 0.3rem 1rem;
            font-weight: 600;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .status-change .btn-update:hover {
            background: #f5c842;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .page-wrapper {
                padding: 10px;
            }

            .details-container {
                padding: 1rem;
            }

            .detail-row {
                flex-direction: column;
                padding: 0.4rem 0;
            }

            .detail-row .label {
                width: 100%;
                font-size: 0.75rem;
                margin-bottom: 2px;
            }

            .detail-row .value {
                font-size: 0.8rem;
            }

            .details-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .btn-actions {
                flex-direction: column;
            }

            .btn-actions .btn {
                width: 100%;
                justify-content: center;
            }

            .status-change {
                flex-wrap: wrap;
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
                <small>Booking Details</small>
            </div>
        </div>

        <div class="page-wrapper">

            <div class="page-header">
                <h4><i class="bi bi-eye me-2" style="color:#f5b342;"></i>Booking Details</h4>
                
            </div>

            <div class="details-container">
                <!-- Header -->
                <div class="details-header">
                    <div>
                        <div style="font-size:1.1rem;font-weight:600;color:#123b4f;">
                            <?= htmlspecialchars($typeInfo['label']) ?>
                        </div>
                        <div class="booking-id">
                            Booking ID: <strong><?= htmlspecialchars($booking['booking_id'] ?? 'N/A') ?></strong>
                        </div>
                    </div>
                    <div>
                        <span class="type-badge <?= $type ?>">
                            <i class="<?= $typeInfo['icon'] ?>"></i>
                            <?= ucfirst($type) ?>
                        </span>
                    </div>
                </div>

                <!-- Status Change -->
                <div class="detail-section">
                    <div class="status-change">
                        <span style="font-weight:600;color:#123b4f;font-size:0.85rem;">Status:</span>
                        <span class="badge-status <?= $booking['status'] ?? 'pending' ?>">
                            <?= ucfirst($booking['status'] ?? 'Pending') ?>
                        </span>
                        <select id="statusSelect">
                            <?php
                            $statusOptions = [];
                            if ($type == 'tour') {
                                $statusOptions = ['pending', 'confirmed', 'cancelled'];
                            } elseif ($type == 'travel') {
                                $statusOptions = ['pending', 'confirmed', 'cancelled', 'completed'];
                            } elseif ($type == 'car') {
                                $statusOptions = ['pending', 'confirmed', 'cancelled', 'completed'];
                            } elseif ($type == 'cta') {
                                $statusOptions = ['pending', 'read', 'replied'];
                            }
                            foreach ($statusOptions as $opt):
                            ?>
                                <option value="<?= $opt ?>" <?= ($booking['status'] ?? 'pending') == $opt ? 'selected' : '' ?>>
                                    <?= ucfirst($opt) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn-update" onclick="updateStatus()">
                           Update
                        </button>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="detail-section">
                    <div class="section-title">Customer Information</div>
                    <div class="detail-row">
                        <span class="label">Name</span>
                        <span class="value"><?= htmlspecialchars($booking['customer_name'] ?? $booking['full_name'] ?? 'N/A') ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="label">Mobile Number</span>
                        <span class="value"><?= htmlspecialchars($booking['mobile_number'] ?? $booking['customer_phone'] ?? $booking['phone_number'] ?? $booking['mobile'] ?? 'N/A') ?></span>
                    </div>
                    <?php if (!empty($booking['travel_date'])): ?>
                        <div class="detail-row">
                            <span class="label">Travel Date</span>
                            <span class="value"><?= date('M d, Y', strtotime($booking['travel_date'])) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($booking['booking_date'])): ?>
                        <div class="detail-row">
                            <span class="label">Booking Date</span>
                            <span class="value"><?= date('M d, Y h:i A', strtotime($booking['booking_date'])) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="detail-row">
                        <span class="label">Created At</span>
                        <span class="value"><?= date('M d, Y h:i A', strtotime($booking['created_at'])) ?></span>
                    </div>
                </div>

                <!-- Booking Details -->
                <?php if ($type == 'tour'): ?>
                    <!-- Tour Details -->
                    <div class="detail-section">
                        <div class="section-title">Tour Details</div>
                        <div class="detail-row">
                            <span class="label">Package Name</span>
                            <span class="value"><?= htmlspecialchars($booking['package_name'] ?? 'N/A') ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Package ID</span>
                            <span class="value"><?= htmlspecialchars($booking['package_id'] ?? 'N/A') ?></span>
                        </div>
                        <?php if (!empty($members)): ?>
                            <div class="detail-row">
                                <span class="label">Members</span>
                                <span class="value">
                                    <?php foreach ($members as $member): ?>
                                        <span class="member-tag">
                                            <?= htmlspecialchars($member['label']) ?>
                                            <span class="count"><?= $member['count'] ?></span>
                                        </span>
                                    <?php endforeach; ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($booking['days_count'])): ?>
                            <div class="detail-row">
                                <span class="label">Days</span>
                                <span class="value"><?= $booking['days_count'] ?> Days</span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($booking['price'])): ?>
                            <div class="detail-row">
                                <span class="label">Price</span>
                                <span class="value"><?= htmlspecialchars($currencySymbol) ?><?= number_format($booking['price'], 2) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($itinerary)): ?>
                        <div class="detail-section">
                            <div class="section-title">Itinerary</div>
                            <?php foreach ($itinerary as $day => $data): ?>
                                <div style="background:rgba(255,255,255,0.4);padding:0.8rem;border-radius:10px;margin-bottom:8px;border:1px solid #e8edf3;">
                                    <div style="font-weight:600;color:#123b4f;font-size:0.85rem;">
                                        <?= ucfirst(str_replace('day', 'Day ', $day)) ?>
                                        <?php if (!empty($data['title'])): ?>
                                            - <?= htmlspecialchars($data['title']) ?>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($data['description'])): ?>
                                        <div style="font-size:0.8rem;color:#5f7d92;margin-top:4px;white-space:pre-wrap;">
                                            <?= htmlspecialchars($data['description']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                <?php elseif ($type == 'travel' || $type == 'car'): ?>
                    <!-- Travel / Car Details -->
                    <div class="detail-section">
                        <div class="section-title">Vehicle Details</div>
                        <div class="detail-row">
                            <span class="label">Car Name</span>
                            <span class="value"><?= htmlspecialchars($booking['car_name'] ?? 'N/A') ?></span>
                        </div>
                        <?php if (!empty($booking['car_type'])): ?>
                            <div class="detail-row">
                                <span class="label">Car Type</span>
                                <span class="value"><?= htmlspecialchars($booking['car_type']) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($booking['car_id'])): ?>
                            <div class="detail-row">
                                <span class="label">Car ID</span>
                                <span class="value"><?= htmlspecialchars($booking['car_id']) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="detail-row">
                            <span class="label">Seat Count</span>
                            <span class="value"><?= $booking['seat_count'] ?? 'N/A' ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Days</span>
                            <span class="value"><?= $booking['days'] ?? '1' ?> Day(s)</span>
                        </div>
                        <?php if (!empty($booking['per_day_price'])): ?>
                            <div class="detail-row">
                                <span class="label">Per Day Price</span>
                                <span class="value"><?= htmlspecialchars($currencySymbol) ?><?= number_format($booking['per_day_price'], 2) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($booking['per_km_charge'])): ?>
                            <div class="detail-row">
                                <span class="label">Per KM Charge</span>
                                <span class="value"><?= htmlspecialchars($currencySymbol) ?><?= number_format($booking['per_km_charge'], 2) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($booking['total_price'])): ?>
                            <div class="detail-row">
                                <span class="label">Total Price</span>
                                <span class="value" style="font-size:1.1rem;color:#28a745;"><?= htmlspecialchars($currencySymbol) ?><?= number_format($booking['total_price'], 2) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($booking['total_distance'])): ?>
                            <div class="detail-row">
                                <span class="label">Total Distance</span>
                                <span class="value"><?= number_format($booking['total_distance'], 2) ?> KM</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($stops)): ?>
                        <div class="detail-section">
                            <div class="section-title">Stops</div>
                            <div class="stops-list">
                                <?php foreach ($stops as $index => $stop): ?>
                                    <div class="stop-item">
                                        <div class="stop-route">
                                            <span class="location">📍 <?= htmlspecialchars($stop['pickup'] ?? 'N/A') ?></span>
                                            <span class="arrow">➜</span>
                                            <span class="location">📍 <?= htmlspecialchars($stop['drop'] ?? 'N/A') ?></span>
                                            <?php if (isset($stop['distance'])): ?>
                                                <span class="distance">(<?= number_format($stop['distance'], 1) ?> km)</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($whatWeProvide)): ?>
                        <div class="detail-section">
                            <div class="section-title">What We Provide</div>
                            <div class="features-grid">
                                <?php foreach ($whatWeProvide as $item): ?>
                                    <span class="feature-tag">
                                        <?php if (!empty($item['icon'])): ?>
                                            <img src="<?= APP_URL . $item['icon'] ?>" alt="">
                                        <?php else: ?>
                                            <i class="bi bi-check-circle-fill" style="color:#28a745;"></i>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($item['name']) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php elseif ($type == 'cta'): ?>
                    <!-- CTA Message Details -->
                    <div class="detail-section">
                        <div class="section-title">Message Details</div>
                        <div class="detail-row">
                            <span class="label">Full Name</span>
                            <span class="value"><?= htmlspecialchars($booking['full_name'] ?? 'N/A') ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="label">Phone Number</span>
                            <span class="value"><?= htmlspecialchars($booking['phone_number'] ?? 'N/A') ?></span>
                        </div>
                        <div class="detail-row" style="flex-direction:column;align-items:flex-start;">
                            <span class="label">Message</span>
                            <span class="value" style="white-space:pre-wrap;background:#f8fafc;padding:0.8rem;border-radius:8px;width:100%;margin-top:4px;">
                                <?= htmlspecialchars($booking['message'] ?? 'No message') ?>
                            </span>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Features (if any) -->
                <?php if (!empty($features)): ?>
                    <div class="detail-section">
                        <div class="section-title">Features</div>
                        <div class="features-grid">
                            <?php foreach ($features as $feature): ?>
                                <span class="feature-tag">
                                    <?php if (!empty($feature['icon'])): ?>
                                        <img src="<?= APP_URL . $feature['icon'] ?>" alt="">
                                    <?php else: ?>
                                        <i class="bi bi-check-circle-fill" style="color:#f5b342;"></i>
                                    <?php endif; ?>
                                    <?= htmlspecialchars($feature['name']) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="btn-actions">
                    <a href="bookings.php" class="btn btn-back">
                        <i class="bi bi-arrow-left"></i> Back to Bookings
                    </a>
                   
                    <button class="btn btn-delete" onclick="deleteBooking()">
                        <i class="bi bi-trash"></i> Delete Booking
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>
    <script src="<?= APP_URL ?>javascript/main.js"></script>
    <script>
        // =============================================
        // UPDATE STATUS
        // =============================================

        function updateStatus() {
            const status = document.getElementById('statusSelect').value;
            const id = <?= $id ?>;
            const table = '<?= $table ?>';

            Swal.fire({
                title: 'Update Status?',
                text: 'Are you sure you want to change the status?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#123b4f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Updating...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const formData = new FormData();
                    formData.append('id', id);
                    formData.append('table', table);
                    formData.append('status', status);

                    fetch('ajax/update-booking-status.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Updated!',
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

        // =============================================
        // DELETE BOOKING
        // =============================================

        function deleteBooking() {
            Swal.fire({
                title: 'Delete Booking?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Deleting...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const formData = new FormData();
                    formData.append('id', <?= $id ?>);
                    formData.append('table', '<?= $table ?>');

                    fetch('ajax/delete-booking.php', {
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
                                    window.location.href = 'bookings.php';
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