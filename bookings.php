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
$pageTitle = "Bookings";

// Get filter parameters
$statusFilter = isset($_GET['status']) ? trim($_GET['status']) : '';
$typeFilter = isset($_GET['type']) ? trim($_GET['type']) : '';
$dateFilter = isset($_GET['date']) ? trim($_GET['date']) : '';

// =============================================
// FETCH ALL BOOKINGS FROM ALL TABLES
// =============================================

// 1. Tour Bookings - customer_tour_bookings
$tourQuery = "SELECT 
    id as ref_id, 
    booking_id, 
    package_name as item_name,
    customer_name, 
    mobile_number as mobile, 
    status, 
    created_at,
    'tour' as booking_type,
    'customer_tour_bookings' as table_name
FROM customer_tour_bookings 
WHERE 1=1";

if ($statusFilter && $statusFilter != 'all') {
    $tourQuery .= " AND status = '" . addslashes($statusFilter) . "'";
}
if ($dateFilter) {
    $tourQuery .= " AND DATE(created_at) = '" . addslashes($dateFilter) . "'";
}
$tourQuery .= " ORDER BY created_at DESC";

if (empty($typeFilter) || $typeFilter == 'tour') {
    $tourBookings = $pdo->query($tourQuery)->fetchAll();
} else {
    $tourBookings = [];
}

// 2. Customer Travel Bookings - customer_travel_bookings
$travelQuery = "SELECT 
    id as ref_id, 
    booking_id, 
    CONCAT(car_name, ' - Travel') as item_name,
    customer_name, 
    customer_phone as mobile, 
    status, 
    created_at,
    'travel' as booking_type,
    'customer_travel_bookings' as table_name
FROM customer_travel_bookings 
WHERE 1=1";

if ($statusFilter && $statusFilter != 'all') {
    $travelQuery .= " AND status = '" . addslashes($statusFilter) . "'";
}
if ($dateFilter) {
    $travelQuery .= " AND DATE(created_at) = '" . addslashes($dateFilter) . "'";
}
$travelQuery .= " ORDER BY created_at DESC";

if (empty($typeFilter) || $typeFilter == 'travel') {
    $travelBookings = $pdo->query($travelQuery)->fetchAll();
} else {
    $travelBookings = [];
}

// 3. Customer Car Rentals Bookings - customer_car_rentals_bookings
$customerCarQuery = "SELECT 
    id as ref_id, 
    NULL as booking_id,
    CONCAT(car_name, ' - Car Rental') as item_name,
    customer_name, 
    mobile, 
    status, 
    created_at,
    'car' as booking_type,
    'customer_car_rentals_bookings' as table_name
FROM customer_car_rentals_bookings 
WHERE 1=1";

if ($statusFilter && $statusFilter != 'all') {
    $customerCarQuery .= " AND status = '" . addslashes($statusFilter) . "'";
}
if ($dateFilter) {
    $customerCarQuery .= " AND DATE(created_at) = '" . addslashes($dateFilter) . "'";
}
$customerCarQuery .= " ORDER BY created_at DESC";

if (empty($typeFilter) || $typeFilter == 'car') {
    $customerCarBookings = $pdo->query($customerCarQuery)->fetchAll();
} else {
    $customerCarBookings = [];
}

// 4. CTA Messages - cta_messages
$ctaQuery = "SELECT 
    id as ref_id, 
    NULL as booking_id,
    CONCAT(SUBSTRING(message, 1, 40), IF(LENGTH(message) > 40, '...', '')) as item_name,
    full_name as customer_name, 
    phone_number as mobile, 
    status, 
    created_at,
    'cta' as booking_type,
    'cta_messages' as table_name
FROM cta_messages 
WHERE 1=1";

if ($statusFilter && $statusFilter != 'all') {
    $ctaQuery .= " AND status = '" . addslashes($statusFilter) . "'";
}
if ($dateFilter) {
    $ctaQuery .= " AND DATE(created_at) = '" . addslashes($dateFilter) . "'";
}
$ctaQuery .= " ORDER BY created_at DESC";

if (empty($typeFilter) || $typeFilter == 'cta') {
    $ctaMessages = $pdo->query($ctaQuery)->fetchAll();
} else {
    $ctaMessages = [];
}

// =============================================
// MERGE ALL BOOKINGS
// =============================================
$allBookings = array_merge($tourBookings, $travelBookings, $customerCarBookings, $ctaMessages);

// Sort by created_at descending
usort($allBookings, function ($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

// =============================================
// CALCULATE STATS
// =============================================

// Total counts - always get total counts for stats regardless of filter
$tourTotal = $pdo->query("SELECT COUNT(*) as count FROM customer_tour_bookings")->fetchColumn();
$travelTotal = $pdo->query("SELECT COUNT(*) as count FROM customer_travel_bookings")->fetchColumn();
$customerCarTotal = $pdo->query("SELECT COUNT(*) as count FROM customer_car_rentals_bookings")->fetchColumn();
$ctaTotal = $pdo->query("SELECT COUNT(*) as count FROM cta_messages")->fetchColumn();

$totalBookings = count($allBookings);

// Today's bookings
$today = date('Y-m-d');
$todayCount = 0;
foreach ($allBookings as $b) {
    if (date('Y-m-d', strtotime($b['created_at'])) == $today) {
        $todayCount++;
    }
}

// Function to build filter URL
function buildFilterUrl($params = [])
{
    $currentParams = $_GET;
    foreach ($params as $key => $value) {
        if ($value === '') {
            unset($currentParams[$key]);
        } else {
            $currentParams[$key] = $value;
        }
    }
    $queryString = http_build_query($currentParams);
    return $queryString ? '?' . $queryString : '';
}

// Format date function
function formatBookingTime($datetime)
{
    $date = new DateTime($datetime);
    $now = new DateTime();
    $diff = $now->diff($date);

    if ($diff->days == 0) {
        return 'Today at ' . $date->format('h:i A');
    } elseif ($diff->days == 1) {
        return 'Yesterday at ' . $date->format('h:i A');
    } elseif ($diff->days < 7) {
        return $diff->days . ' days ago at ' . $date->format('h:i A');
    } else {
        return $date->format('M d, Y') . ' at ' . $date->format('h:i A');
    }
}

function getStatusBadgeClass($status)
{
    $classes = [
        'pending' => 'badge-pending',
        'confirmed' => 'badge-confirmed',
        'cancelled' => 'badge-cancelled',
        'completed' => 'badge-completed',
        'read' => 'badge-read',
        'replied' => 'badge-replied'
    ];
    return $classes[$status] ?? 'badge-pending';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>Bookings · Tour Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* ... [All existing styles remain the same] ... */
        .page-wrapper {
            padding: 20px;
        }
        .page-header {
            margin-bottom: 20px;
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
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px;
            margin-bottom: 25px;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(4px);
            border-radius: 14px;
            padding: 1.2rem 1rem;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 4px 16px rgba(0, 20, 30, 0.04);
            text-align: center;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 20, 30, 0.08);
            border-color: #ffd966;
        }
        .stat-card .stat-icon {
            font-size: 1.8rem;
            margin-bottom: 4px;
        }
        .stat-card .stat-number {
            font-weight: 700;
            font-size: 1.5rem;
            color: #123b4f;
        }
        .stat-card .stat-label {
            font-size: 0.75rem;
            color: #5f7d92;
            font-weight: 500;
        }
        .stat-card .stat-icon.tour { color: #f5b342; }
        .stat-card .stat-icon.travel { color: #28a745; }
        .stat-card .stat-icon.car { color: #007bff; }
        .stat-card .stat-icon.total { color: #dc3545; }
        .stat-card .stat-icon.cta { color: #6f42c1; }
        .stat-card.today { border-color: #ffd966; background: rgba(255, 215, 100, 0.08); }
        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(4px);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            align-items: center;
        }
        .filter-bar .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .filter-bar .filter-group label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #123b4f;
            margin: 0;
        }
        .filter-bar .filter-group select,
        .filter-bar .filter-group input {
            border-radius: 8px;
            padding: 0.3rem 0.7rem;
            border: 2px solid #e8edf3;
            background: rgba(255, 255, 255, 0.6);
            font-size: 0.8rem;
            color: #123b4f;
            outline: none;
            transition: all 0.2s;
        }
        .filter-bar .filter-group select:focus,
        .filter-bar .filter-group input:focus {
            border-color: #ffd966;
            box-shadow: 0 0 0 3px rgba(255, 215, 100, 0.15);
        }
        .filter-bar .btn-filter {
            background: linear-gradient(145deg, #0b2a3e 0%, #123b4f 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.3rem 1.2rem;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.3s;
            cursor: pointer;
        }
        .filter-bar .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(11, 42, 62, 0.2);
            color: #ffd966;
        }
        .filter-bar .btn-reset {
            background: transparent;
            border: 2px solid #e8edf3;
            border-radius: 8px;
            padding: 0.3rem 1.2rem;
            font-weight: 500;
            font-size: 0.8rem;
            color: #5f7d92;
            transition: all 0.3s;
            cursor: pointer;
        }
        .filter-bar .btn-reset:hover {
            background: #e8edf3;
            color: #123b4f;
        }
        .quick-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 15px;
        }
        .quick-filters .btn-quick {
            padding: 0.25rem 1rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 500;
            border: 2px solid #e8edf3;
            background: transparent;
            color: #5f7d92;
            transition: all 0.3s;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .quick-filters .btn-quick:hover {
            border-color: #ffd966;
            color: #123b4f;
            background: rgba(255, 215, 100, 0.05);
        }
        .quick-filters .btn-quick.active {
            border-color: #ffd966;
            background: rgba(255, 215, 100, 0.1);
            color: #b8860b;
        }
        .table-container {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 4px 16px rgba(0, 20, 30, 0.04);
            overflow-x: auto;
        }
        .table-custom {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }
        .table-custom thead th {
            background: rgba(18, 59, 79, 0.04);
            color: #123b4f;
            font-weight: 600;
            padding: 0.8rem 0.8rem;
            text-align: left;
            border-bottom: 2px solid #ffd966;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }
        .table-custom tbody td {
            padding: 0.7rem 0.8rem;
            border-bottom: 1px solid #f0f3f7;
            color: #123b4f;
            vertical-align: middle;
        }
        .table-custom tbody tr:hover {
            background: rgba(255, 215, 100, 0.04);
        }
        .table-custom tbody tr:last-child td {
            border-bottom: none;
        }
        .badge-type {
            padding: 0.2rem 0.7rem;
            border-radius: 20px;
            font-size: 0.6rem;
            font-weight: 600;
            display: inline-block;
            text-transform: uppercase;
        }
        .badge-type.tour { background: rgba(255, 215, 100, 0.2); color: #b8860b; }
        .badge-type.travel { background: rgba(40, 167, 69, 0.15); color: #28a745; }
        .badge-type.car { background: rgba(0, 123, 255, 0.15); color: #007bff; }
        .badge-type.cta { background: rgba(111, 66, 193, 0.15); color: #6f42c1; }
        .badge-status {
            padding: 0.2rem 0.7rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
            display: inline-block;
            cursor: pointer;
            transition: all 0.3s;
        }
        .badge-status:hover {
            transform: scale(1.05);
        }
        .badge-status.badge-pending { background: rgba(255, 193, 7, 0.15); color: #ffc107; }
        .badge-status.badge-confirmed { background: rgba(40, 167, 69, 0.15); color: #28a745; }
        .badge-status.badge-cancelled { background: rgba(220, 53, 69, 0.15); color: #dc3545; }
        .badge-status.badge-completed { background: rgba(23, 162, 184, 0.15); color: #17a2b8; }
        .badge-status.badge-read { background: rgba(0, 123, 255, 0.15); color: #007bff; }
        .badge-status.badge-replied { background: rgba(40, 167, 69, 0.15); color: #28a745; }
        .status-dropdown {
            position: relative;
            display: inline-block;
        }
        .status-dropdown .status-options {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            min-width: 120px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
            border: 1px solid #e8edf3;
            z-index: 1000;
            padding: 4px 0;
            margin-top: 4px;
        }
        .status-dropdown .status-options.show {
            display: block;
        }
        .status-dropdown .status-options .status-option {
            padding: 0.3rem 1rem;
            font-size: 0.7rem;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            color: #123b4f;
        }
        .status-dropdown .status-options .status-option:hover {
            background: rgba(255, 215, 100, 0.1);
        }
        .status-dropdown .status-options .status-option .dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 6px;
        }
        .btn-action {
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
            font-size: 0.65rem;
            font-weight: 500;
            border: none;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            cursor: pointer;
        }
        .btn-view { background: rgba(255, 215, 100, 0.15); color: #b8860b; }
        .btn-view:hover { background: #ffd966; color: #123b4f; }
        .btn-delete { background: rgba(220, 53, 69, 0.1); color: #dc3545; }
        .btn-delete:hover { background: #dc3545; color: #fff; }
        .booking-time { font-size: 0.7rem; color: #5f7d92; }
        .booking-details { font-size: 0.8rem; color: #5f7d92; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .empty-state { text-align: center; padding: 3rem 1.5rem; }
        .empty-state i { font-size: 3rem; color: #e8edf3; }
        .empty-state h5 { color: #123b4f; margin-top: 0.8rem; font-size: 1.1rem; }
        .empty-state p { color: #5f7d92; font-size: 0.85rem; }
        .col-customer { min-width: 140px; }
        .col-mobile { min-width: 120px; }
        .col-details { min-width: 200px; }
        .col-type { width: 90px; }
        .col-status { min-width: 120px; }
        .col-date { min-width: 130px; }
        .col-actions { width: 80px; }
        .status-toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #123b4f;
            color: #fff;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 0.85rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            display: none;
            z-index: 9999;
            animation: slideUp 0.3s ease;
        }
        .status-toast.show { display: block; }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @media (max-width: 768px) {
            .page-wrapper { padding: 10px; }
            .stats-grid { grid-template-columns: repeat(3, 1fr); gap: 10px; }
            .stat-card { padding: 0.8rem; }
            .stat-card .stat-number { font-size: 1.2rem; }
            .filter-bar { flex-direction: column; align-items: stretch; }
            .filter-bar .filter-group { flex-wrap: wrap; }
            .table-container { padding: 0.8rem; border-radius: 12px; }
            .table-custom { font-size: 0.75rem; }
            .table-custom thead th, .table-custom tbody td { padding: 0.4rem 0.4rem; }
            .col-customer { min-width: 100px; }
            .col-mobile { min-width: 90px; }
            .col-details { min-width: 100px; }
            .col-date { min-width: 100px; }
            .badge-status { font-size: 0.55rem; padding: 0.1rem 0.5rem; }
            .badge-type { font-size: 0.5rem; padding: 0.1rem 0.5rem; }
            .btn-action { font-size: 0.55rem; padding: 0.15rem 0.4rem; }
            .booking-details { max-width: 80px; }
        }
        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
            .stat-card .stat-number { font-size: 1rem; }
            .stat-card .stat-label { font-size: 0.65rem; }
            .table-custom { font-size: 0.65rem; }
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
                <small>Bookings</small>
            </div>
        </div>

        <div class="page-wrapper">
            <div class="page-header">
                <h4><i class="bi bi-calendar-event me-2" style="color:#f5b342;"></i>Bookings</h4>
                <p>Manage all bookings from tours, travel, car rentals, and CTA messages</p>
            </div>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon total"><i class="bi bi-calendar-check"></i></div>
                    <div class="stat-number"><?= $totalBookings ?></div>
                    <div class="stat-label">Total Bookings</div>
                </div>
                <div class="stat-card today">
                    <div class="stat-icon total"><i class="bi bi-calendar2-check"></i></div>
                    <div class="stat-number"><?= $todayCount ?></div>
                    <div class="stat-label">Today's Bookings</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon tour"><i class="bi bi-suitcase"></i></div>
                    <div class="stat-number"><?= $tourTotal ?></div>
                    <div class="stat-label">Tour Bookings</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon travel"><i class="bi bi-geo-alt"></i></div>
                    <div class="stat-number"><?= $travelTotal ?></div>
                    <div class="stat-label">Travel Bookings</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon car"><i class="bi bi-car-front"></i></div>
                    <div class="stat-number"><?= $customerCarTotal ?></div>
                    <div class="stat-label">Car Bookings</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon cta"><i class="bi bi-chat-dots"></i></div>
                    <div class="stat-number"><?= $ctaTotal ?></div>
                    <div class="stat-label">CTA Messages</div>
                </div>
            </div>

            <!-- Quick Filters -->
            <div class="quick-filters">
                <a href="<?= APP_URL ?>bookings<?= buildFilterUrl(['date' => '']) ?>" class="btn-quick <?= !$dateFilter ? 'active' : '' ?>">All</a>
                <a href="<?= APP_URL ?>bookings<?= buildFilterUrl(['date' => date('Y-m-d')]) ?>" class="btn-quick <?= $dateFilter == date('Y-m-d') ? 'active' : '' ?>">Today</a>
                <a href="<?= APP_URL ?>bookings<?= buildFilterUrl(['date' => date('Y-m-d', strtotime('-1 day'))]) ?>" class="btn-quick <?= $dateFilter == date('Y-m-d', strtotime('-1 day')) ? 'active' : '' ?>">Yesterday</a>
                <a href="<?= APP_URL ?>bookings<?= buildFilterUrl(['date' => date('Y-m-d', strtotime('-7 days'))]) ?>" class="btn-quick <?= $dateFilter == date('Y-m-d', strtotime('-7 days')) ? 'active' : '' ?>">Last 7 Days</a>
                <a href="<?= APP_URL ?>bookings<?= buildFilterUrl(['date' => date('Y-m-d', strtotime('-30 days'))]) ?>" class="btn-quick <?= $dateFilter == date('Y-m-d', strtotime('-30 days')) ? 'active' : '' ?>">Last 30 Days</a>
            </div>

            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="filter-group">
                    <label>Status:</label>
                    <select id="statusFilter" onchange="applyFilters()">
                        <option value="">All Status</option>
                        <option value="pending" <?= $statusFilter == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="confirmed" <?= $statusFilter == 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                        <option value="cancelled" <?= $statusFilter == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        <option value="completed" <?= $statusFilter == 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="read" <?= $statusFilter == 'read' ? 'selected' : '' ?>>Read</option>
                        <option value="replied" <?= $statusFilter == 'replied' ? 'selected' : '' ?>>Replied</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Date:</label>
                    <input type="date" id="dateFilter" value="<?= $dateFilter ?>" onchange="applyFilters()">
                </div>
                <div class="filter-group">
                    <label>Type:</label>
                    <select id="typeFilter" onchange="applyFilters()">
                        <option value="">All Types</option>
                        <option value="tour" <?= $typeFilter == 'tour' ? 'selected' : '' ?>>Tour</option>
                        <option value="travel" <?= $typeFilter == 'travel' ? 'selected' : '' ?>>Travel</option>
                        <option value="car" <?= $typeFilter == 'car' ? 'selected' : '' ?>>Car</option>
                        <option value="cta" <?= $typeFilter == 'cta' ? 'selected' : '' ?>>CTA</option>
                    </select>
                </div>
                <button class="btn-filter" onclick="applyFilters()">
                    <i class="bi bi-search me-1"></i> Apply
                </button>
                <button class="btn-reset" onclick="resetFilters()">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                </button>
            </div>

            <!-- Table -->
            <div class="table-container">
                <?php if (empty($allBookings)): ?>
                    <div class="empty-state">
                        <i class="bi bi-calendar-event"></i>
                        <h5>No Bookings Found</h5>
                        <p>No bookings match your filters</p>
                    </div>
                <?php else: ?>
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th class="col-customer">Customer</th>
                                <th class="col-mobile">Mobile</th>
                                <th class="col-details">Booking Details</th>
                                <th class="col-type">Type</th>
                                <th class="col-status">Status</th>
                                <th class="col-date">Date &amp; Time</th>
                                <th class="col-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allBookings as $booking):
                                $type = $booking['booking_type'] ?? 'tour';
                                $badgeClass = $type;
                                $typeLabel = ucfirst($type);

                                $customerName = $booking['customer_name'] ?? 'Unknown';
                                $mobile = $booking['mobile'] ?? 'N/A';
                                $status = $booking['status'] ?? 'pending';
                                $refId = $booking['ref_id'] ?? 0;
                                $tableName = $booking['table_name'] ?? '';
                                $itemName = $booking['item_name'] ?? 'N/A';

                                $formattedTime = formatBookingTime($booking['created_at'] ?? 'now');
                                $statusClass = getStatusBadgeClass($status);

                                $statusOptions = [];
                                if ($type == 'tour') {
                                    $statusOptions = ['pending', 'confirmed', 'cancelled'];
                                } elseif ($type == 'travel') {
                                    $statusOptions = ['pending', 'confirmed', 'cancelled', 'completed'];
                                } elseif ($type == 'car') {
                                    $statusOptions = ['pending', 'confirmed', 'cancelled', 'completed'];
                                } elseif ($type == 'cta') {
                                    $statusOptions = ['pending', 'read', 'replied'];
                                } else {
                                    $statusOptions = ['pending', 'confirmed', 'cancelled', 'completed'];
                                }
                            ?>
                                <tr>
                                    <td>
                                        <div style="font-weight:600;color:#123b4f;">
                                            <?= htmlspecialchars($customerName) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size:0.8rem;color:#5f7d92;">
                                            <i class="bi bi-phone me-1"></i>
                                            <?= htmlspecialchars($mobile) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="booking-details" title="<?= htmlspecialchars($itemName) ?>">
                                            <i class="bi bi-info-circle me-1"></i>
                                            <?= htmlspecialchars($itemName) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-type <?= $badgeClass ?>">
                                            <?php if ($type == 'tour'): ?>
                                                <i class="bi bi-suitcase me-1"></i>
                                            <?php elseif ($type == 'travel'): ?>
                                                <i class="bi bi-geo-alt me-1"></i>
                                            <?php elseif ($type == 'car'): ?>
                                                <i class="bi bi-car-front me-1"></i>
                                            <?php else: ?>
                                                <i class="bi bi-chat-dots me-1"></i>
                                            <?php endif; ?>
                                            <?= $typeLabel ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="status-dropdown">
                                            <span class="badge-status <?= $statusClass ?>" onclick="toggleStatusDropdown(this)">
                                                <?= ucfirst($status) ?>
                                                <i class="bi bi-chevron-down" style="font-size:0.5rem;margin-left:4px;"></i>
                                            </span>
                                            <div class="status-options">
                                                <?php foreach ($statusOptions as $option): ?>
                                                    <button class="status-option" onclick="changeStatus(<?= $refId ?>, '<?= $tableName ?>', '<?= $option ?>', this)">
                                                        <span class="dot" style="background:<?= getStatusColor($option) ?>;"></span>
                                                        <?= ucfirst($option) ?>
                                                    </button>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="booking-time">
                                            <i class="bi bi-clock me-1"></i>
                                            <?= $formattedTime ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                            <a href="booking-details.php?type=<?= $type ?>&id=<?= $refId ?>&table=<?= $tableName ?>" class="btn-action btn-view">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button class="btn-action btn-delete" onclick="deleteBooking('<?= $tableName ?>', <?= $refId ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Status Toast -->
    <div class="status-toast" id="statusToast">
        <i class="bi bi-check-circle-fill me-2" style="color:#28a745;"></i>
        <span id="statusToastMessage">Status updated successfully!</span>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>
    <script src="<?= APP_URL ?>javascript/main.js"></script>
    <script>
        // =============================================
        // GLOBAL DELETE BOOKING FUNCTION
        // =============================================

        function deleteBooking(table, id) {
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
                    formData.append('id', id);
                    formData.append('table', table);

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
        // FILTER FUNCTIONS
        // =============================================

        function applyFilters() {
            const status = document.getElementById('statusFilter').value;
            const date = document.getElementById('dateFilter').value;
            const type = document.getElementById('typeFilter').value;

            let url = window.location.pathname + '?';
            let params = [];

            if (status) params.push('status=' + encodeURIComponent(status));
            if (date) params.push('date=' + encodeURIComponent(date));
            if (type) params.push('type=' + encodeURIComponent(type));

            if (params.length === 0) {
                window.location.href = window.location.pathname;
            } else {
                window.location.href = url + params.join('&');
            }
        }

        function resetFilters() {
            window.location.href = window.location.pathname;
        }

        // =============================================
        // STATUS DROPDOWN
        // =============================================

        function toggleStatusDropdown(element) {
            const dropdown = element.closest('.status-dropdown');
            const options = dropdown.querySelector('.status-options');

            document.querySelectorAll('.status-options.show').forEach(el => {
                if (el !== options) el.classList.remove('show');
            });

            options.classList.toggle('show');
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.status-dropdown')) {
                document.querySelectorAll('.status-options.show').forEach(el => {
                    el.classList.remove('show');
                });
            }
        });

        // =============================================
        // CHANGE STATUS
        // =============================================

        function changeStatus(id, table, status, element) {
            const dropdown = element.closest('.status-options');
            if (dropdown) dropdown.classList.remove('show');

            const badge = element.closest('.status-dropdown').querySelector('.badge-status');
            const originalText = badge.textContent.trim();
            badge.innerHTML = '<i class="bi bi-arrow-repeat spinner-border-sm" style="font-size:0.6rem;"></i>';

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
                        const statusClass = getStatusBadgeClass(status);
                        badge.className = 'badge-status ' + statusClass;
                        badge.innerHTML = ucfirst(status) + ' <i class="bi bi-chevron-down" style="font-size:0.5rem;margin-left:4px;"></i>';

                        showToast('Status updated to ' + ucfirst(status) + ' successfully!');
                    } else {
                        badge.innerHTML = originalText + ' <i class="bi bi-chevron-down" style="font-size:0.5rem;margin-left:4px;"></i>';
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message,
                            confirmButtonColor: '#123b4f'
                        });
                    }
                })
                .catch(error => {
                    badge.innerHTML = originalText + ' <i class="bi bi-chevron-down" style="font-size:0.5rem;margin-left:4px;"></i>';
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred. Please try again.',
                        confirmButtonColor: '#123b4f'
                    });
                });
        }

        // =============================================
        // TOAST NOTIFICATION
        // =============================================

        function showToast(message) {
            const toast = document.getElementById('statusToast');
            const msg = document.getElementById('statusToastMessage');
            msg.textContent = message;
            toast.classList.add('show');

            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // =============================================
        // UTILITY FUNCTIONS
        // =============================================

        function ucfirst(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function getStatusBadgeClass(status) {
            const classes = {
                'pending': 'badge-pending',
                'confirmed': 'badge-confirmed',
                'cancelled': 'badge-cancelled',
                'completed': 'badge-completed',
                'read': 'badge-read',
                'replied': 'badge-replied'
            };
            return classes[status] || 'badge-pending';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const active = document.activeElement;
                if (active && (active.id === 'statusFilter' || active.id === 'dateFilter' || active.id === 'typeFilter')) {
                    applyFilters();
                }
            }
        });
    </script>
</body>

</html>
<?php
// =============================================
// PHP Function for Status Color
// =============================================
function getStatusColor($status)
{
    $colors = [
        'pending' => '#ffc107',
        'confirmed' => '#28a745',
        'cancelled' => '#dc3545',
        'completed' => '#17a2b8',
        'read' => '#007bff',
        'replied' => '#28a745'
    ];
    return $colors[$status] ?? '#6c757d';
}
?>