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
$pageTitle = "Revenue";

// Get currency symbol
$currencyCode = getCurrencyCode($pdo);
$currencySymbol = getCurrencySymbol($currencyCode);

// =============================================
// GET REVENUE DATA
// =============================================

// 1. Tour Bookings Revenue
$tourQuery = "SELECT 
    COUNT(*) as total_count,
    SUM(CASE WHEN ct.status = 'confirmed' THEN tp.price ELSE 0 END) as confirmed_revenue,
    SUM(CASE WHEN ct.status = 'pending' THEN tp.price ELSE 0 END) as pending_revenue,
    SUM(CASE WHEN ct.status = 'cancelled' THEN tp.price ELSE 0 END) as cancelled_revenue,
    SUM(tp.price) as total_revenue
FROM customer_tour_bookings ct
LEFT JOIN tour_packages tp ON ct.package_id = tp.id";

$tourRevenue = $pdo->query($tourQuery)->fetch();

// 2. Travel Bookings Revenue
$travelQuery = "SELECT 
    COUNT(*) as total_count,
    SUM(CASE WHEN status = 'confirmed' THEN total_price ELSE 0 END) as confirmed_revenue,
    SUM(CASE WHEN status = 'pending' THEN total_price ELSE 0 END) as pending_revenue,
    SUM(CASE WHEN status = 'cancelled' THEN total_price ELSE 0 END) as cancelled_revenue,
    SUM(CASE WHEN status = 'completed' THEN total_price ELSE 0 END) as completed_revenue,
    SUM(total_price) as total_revenue
FROM customer_travel_bookings";

$travelRevenue = $pdo->query($travelQuery)->fetch();

// 3. Car Rentals Revenue
$carQuery = "SELECT 
    COUNT(*) as total_count
FROM customer_car_rentals_bookings";

$carRevenue = $pdo->query($carQuery)->fetch();

// Get car rental prices for revenue calculation
$carPriceQuery = "SELECT 
    ccb.id,
    ccb.status,
    cr.per_day_amount
FROM customer_car_rentals_bookings ccb
LEFT JOIN car_rentals cr ON ccb.car_id = cr.id";

$carBookings = $pdo->query($carPriceQuery)->fetchAll();

$carConfirmedRevenue = 0;
$carPendingRevenue = 0;
$carCancelledRevenue = 0;
$carCompletedRevenue = 0;
$carTotalRevenue = 0;

foreach ($carBookings as $booking) {
    $price = floatval($booking['per_day_amount'] ?? 0);
    $status = $booking['status'] ?? 'pending';
    
    if ($status == 'confirmed') $carConfirmedRevenue += $price;
    elseif ($status == 'pending') $carPendingRevenue += $price;
    elseif ($status == 'cancelled') $carCancelledRevenue += $price;
    elseif ($status == 'completed') $carCompletedRevenue += $price;
    $carTotalRevenue += $price;
}

// GRAND TOTAL = ONLY CONFIRMED REVENUE
$grandTotal = ($tourRevenue['confirmed_revenue'] ?? 0) + ($travelRevenue['confirmed_revenue'] ?? 0) + $carConfirmedRevenue;

$totalTourCount = $tourRevenue['total_count'] ?? 0;
$totalTravelCount = $travelRevenue['total_count'] ?? 0;
$totalCarCount = $carRevenue['total_count'] ?? 0;
$totalBookings = $totalTourCount + $totalTravelCount + $totalCarCount;

// Status counts
$tourPending = $pdo->query("SELECT COUNT(*) FROM customer_tour_bookings WHERE status = 'pending'")->fetchColumn();
$tourConfirmed = $pdo->query("SELECT COUNT(*) FROM customer_tour_bookings WHERE status = 'confirmed'")->fetchColumn();
$tourCancelled = $pdo->query("SELECT COUNT(*) FROM customer_tour_bookings WHERE status = 'cancelled'")->fetchColumn();

$travelPending = $pdo->query("SELECT COUNT(*) FROM customer_travel_bookings WHERE status = 'pending'")->fetchColumn();
$travelConfirmed = $pdo->query("SELECT COUNT(*) FROM customer_travel_bookings WHERE status = 'confirmed'")->fetchColumn();
$travelCancelled = $pdo->query("SELECT COUNT(*) FROM customer_travel_bookings WHERE status = 'cancelled'")->fetchColumn();
$travelCompleted = $pdo->query("SELECT COUNT(*) FROM customer_travel_bookings WHERE status = 'completed'")->fetchColumn();

$carPending = $pdo->query("SELECT COUNT(*) FROM customer_car_rentals_bookings WHERE status = 'pending'")->fetchColumn();
$carConfirmed = $pdo->query("SELECT COUNT(*) FROM customer_car_rentals_bookings WHERE status = 'confirmed'")->fetchColumn();
$carCancelled = $pdo->query("SELECT COUNT(*) FROM customer_car_rentals_bookings WHERE status = 'cancelled'")->fetchColumn();
$carCompleted = $pdo->query("SELECT COUNT(*) FROM customer_car_rentals_bookings WHERE status = 'completed'")->fetchColumn();

$totalPending = $tourPending + $travelPending + $carPending;
$totalConfirmed = $tourConfirmed + $travelConfirmed + $carConfirmed;
$totalCancelled = $tourCancelled + $travelCancelled + $carCancelled;
$totalCompleted = $travelCompleted + $carCompleted;

// Monthly Revenue (Only Confirmed)
$currentMonth = date('Y-m-01');
$nextMonth = date('Y-m-01', strtotime('+1 month'));

$monthlyTour = $pdo->prepare("SELECT SUM(tp.price) FROM customer_tour_bookings ct LEFT JOIN tour_packages tp ON ct.package_id = tp.id WHERE ct.status = 'confirmed' AND ct.created_at >= ? AND ct.created_at < ?");
$monthlyTour->execute([$currentMonth, $nextMonth]);
$monthlyTourRevenue = $monthlyTour->fetchColumn();

$monthlyTravel = $pdo->prepare("SELECT SUM(total_price) FROM customer_travel_bookings WHERE status = 'confirmed' AND created_at >= ? AND created_at < ?");
$monthlyTravel->execute([$currentMonth, $nextMonth]);
$monthlyTravelRevenue = $monthlyTravel->fetchColumn();

$monthlyCarRevenue = 0;
$monthlyCarBookings = $pdo->prepare("SELECT ccb.*, cr.per_day_amount FROM customer_car_rentals_bookings ccb LEFT JOIN car_rentals cr ON ccb.car_id = cr.id WHERE ccb.status = 'confirmed' AND ccb.created_at >= ? AND ccb.created_at < ?");
$monthlyCarBookings->execute([$currentMonth, $nextMonth]);
while ($row = $monthlyCarBookings->fetch()) {
    $monthlyCarRevenue += floatval($row['per_day_amount'] ?? 0);
}

$monthlyRevenue = ($monthlyTourRevenue ?? 0) + ($monthlyTravelRevenue ?? 0) + $monthlyCarRevenue;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>Revenue · Tour Admin</title>
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 16px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            border: 1px solid #e8edf3;
            text-align: center;
            transition: all 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }
        .stat-card .number {
            font-size: 1.4rem;
            font-weight: 700;
            color: #123b4f;
        }
        .stat-card .label {
            font-size: 0.7rem;
            color: #5f7d92;
            margin-top: 2px;
        }
        .stat-card .icon {
            font-size: 1.2rem;
            margin-bottom: 4px;
        }

        .revenue-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .revenue-card {
            background: white;
            border-radius: 16px;
            padding: 1.2rem;
            border: 1px solid #e8edf3;
            transition: all 0.2s;
        }
        .revenue-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        }

        .revenue-card .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.8rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #f0f3f7;
        }
        .revenue-card .header .title {
            font-weight: 600;
            color: #123b4f;
            font-size: 0.9rem;
        }
        .revenue-card .header .count {
            font-size: 0.7rem;
            color: #5f7d92;
            background: #f0f3f7;
            padding: 0.1rem 0.6rem;
            border-radius: 20px;
        }

        .revenue-card .total {
            font-size: 1.6rem;
            font-weight: 700;
            color: #123b4f;
            margin-bottom: 0.8rem;
        }
        .revenue-card .total .currency {
            font-size: 0.9rem;
            color: #5f7d92;
        }

        .revenue-card .breakdown {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px;
        }
        .revenue-card .breakdown .item {
            display: flex;
            justify-content: space-between;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            background: #f8fafc;
        }
        .revenue-card .breakdown .item .label {
            color: #5f7d92;
        }
        .revenue-card .breakdown .item .value {
            font-weight: 600;
        }

        .grand-total {
            background: linear-gradient(135deg, #0b2a3e, #123b4f);
            border-radius: 16px;
            padding: 1.5rem 2rem;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .grand-total .label {
            font-size: 0.85rem;
            opacity: 0.7;
        }
        .grand-total .amount {
            font-size: 2rem;
            font-weight: 700;
        }
        .grand-total .monthly {
            text-align: right;
        }
        .grand-total .monthly .label {
            font-size: 0.75rem;
            opacity: 0.7;
        }
        .grand-total .monthly .amount {
            font-size: 1.2rem;
            color: #ffd966;
        }

        .badge-confirmed {
            display: inline-block;
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            padding: 0.1rem 0.5rem;
            border-radius: 12px;
            font-size: 0.6rem;
            font-weight: 600;
            margin-left: 6px;
        }

        .status-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 4px;
        }

        @media (max-width: 1200px) {
            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 992px) {
            .revenue-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            .page-wrapper {
                padding: 10px;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            .stat-card .number {
                font-size: 1.1rem;
            }
            .revenue-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            .revenue-card .total {
                font-size: 1.3rem;
            }
            .grand-total {
                padding: 1rem;
                flex-direction: column;
                text-align: center;
            }
            .grand-total .monthly {
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
                gap: 8px;
            }
            .stat-card {
                padding: 0.6rem;
            }
            .stat-card .number {
                font-size: 0.9rem;
            }
            .stat-card .label {
                font-size: 0.6rem;
            }
            .revenue-card .breakdown {
                grid-template-columns: 1fr;
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
                <small>Revenue</small>
            </div>
        </div>

        <div class="page-wrapper">
            <div class="page-header">
                <h4><i class="bi bi-wallet2 me-2" style="color:#f5b342;"></i>Revenue Dashboard</h4>
                <p>Overview of revenue from all confirmed bookings</p>
            </div>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="icon">📊</div>
                    <div class="number"><?= number_format($totalBookings) ?></div>
                    <div class="label">Total Bookings</div>
                </div>
                <div class="stat-card">
                    <div class="icon">✅</div>
                    <div class="number"><?= number_format($totalConfirmed) ?></div>
                    <div class="label">Confirmed</div>
                </div>
                <div class="stat-card">
                    <div class="icon">⏳</div>
                    <div class="number"><?= number_format($totalPending) ?></div>
                    <div class="label">Pending</div>
                </div>
                <div class="stat-card">
                    <div class="icon">❌</div>
                    <div class="number"><?= number_format($totalCancelled) ?></div>
                    <div class="label">Cancelled</div>
                </div>
                <div class="stat-card">
                    <div class="icon">✔️</div>
                    <div class="number"><?= number_format($totalCompleted) ?></div>
                    <div class="label">Completed</div>
                </div>
                <div class="stat-card">
                    <div class="icon">💰</div>
                    <div class="number"><?= htmlspecialchars($currencySymbol) ?><?= number_format($monthlyRevenue, 0) ?></div>
                    <div class="label">Monthly Revenue</div>
                </div>
            </div>

            <!-- Revenue Cards -->
            <div class="revenue-grid">
                <!-- Tour -->
                <div class="revenue-card">
                    <div class="header">
                        <span class="title">🏖️ Tour Bookings</span>
                        <span class="count"><?= $totalTourCount ?></span>
                    </div>
                    <div class="total">
                        <span class="currency"><?= htmlspecialchars($currencySymbol) ?></span>
                        <?= number_format($tourRevenue['confirmed_revenue'] ?? 0, 2) ?>
                        <span class="badge-confirmed">Confirmed</span>
                    </div>
                    <div class="breakdown">
                        <div class="item">
                            <span class="label"><span class="status-dot" style="background:#28a745;"></span>Confirmed</span>
                            <span class="value" style="color:#28a745;"><?= htmlspecialchars($currencySymbol) ?><?= number_format($tourRevenue['confirmed_revenue'] ?? 0, 0) ?></span>
                        </div>
                        <div class="item">
                            <span class="label"><span class="status-dot" style="background:#ffc107;"></span>Pending</span>
                            <span class="value" style="color:#ffc107;"><?= htmlspecialchars($currencySymbol) ?><?= number_format($tourRevenue['pending_revenue'] ?? 0, 0) ?></span>
                        </div>
                        <div class="item">
                            <span class="label"><span class="status-dot" style="background:#dc3545;"></span>Cancelled</span>
                            <span class="value" style="color:#dc3545;"><?= htmlspecialchars($currencySymbol) ?><?= number_format($tourRevenue['cancelled_revenue'] ?? 0, 0) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Travel -->
                <div class="revenue-card">
                    <div class="header">
                        <span class="title">🚗 Travel Bookings</span>
                        <span class="count"><?= $totalTravelCount ?></span>
                    </div>
                    <div class="total">
                        <span class="currency"><?= htmlspecialchars($currencySymbol) ?></span>
                        <?= number_format($travelRevenue['confirmed_revenue'] ?? 0, 2) ?>
                        <span class="badge-confirmed">Confirmed</span>
                    </div>
                    <div class="breakdown">
                        <div class="item">
                            <span class="label"><span class="status-dot" style="background:#28a745;"></span>Confirmed</span>
                            <span class="value" style="color:#28a745;"><?= htmlspecialchars($currencySymbol) ?><?= number_format($travelRevenue['confirmed_revenue'] ?? 0, 0) ?></span>
                        </div>
                        <div class="item">
                            <span class="label"><span class="status-dot" style="background:#ffc107;"></span>Pending</span>
                            <span class="value" style="color:#ffc107;"><?= htmlspecialchars($currencySymbol) ?><?= number_format($travelRevenue['pending_revenue'] ?? 0, 0) ?></span>
                        </div>
                        <div class="item">
                            <span class="label"><span class="status-dot" style="background:#dc3545;"></span>Cancelled</span>
                            <span class="value" style="color:#dc3545;"><?= htmlspecialchars($currencySymbol) ?><?= number_format($travelRevenue['cancelled_revenue'] ?? 0, 0) ?></span>
                        </div>
                        <div class="item">
                            <span class="label"><span class="status-dot" style="background:#17a2b8;"></span>Completed</span>
                            <span class="value" style="color:#17a2b8;"><?= htmlspecialchars($currencySymbol) ?><?= number_format($travelRevenue['completed_revenue'] ?? 0, 0) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Car -->
                <div class="revenue-card">
                    <div class="header">
                        <span class="title">🚙 Car Rentals</span>
                        <span class="count"><?= $totalCarCount ?></span>
                    </div>
                    <div class="total">
                        <span class="currency"><?= htmlspecialchars($currencySymbol) ?></span>
                        <?= number_format($carConfirmedRevenue, 2) ?>
                        <span class="badge-confirmed">Confirmed</span>
                    </div>
                    <div class="breakdown">
                        <div class="item">
                            <span class="label"><span class="status-dot" style="background:#28a745;"></span>Confirmed</span>
                            <span class="value" style="color:#28a745;"><?= htmlspecialchars($currencySymbol) ?><?= number_format($carConfirmedRevenue, 0) ?></span>
                        </div>
                        <div class="item">
                            <span class="label"><span class="status-dot" style="background:#ffc107;"></span>Pending</span>
                            <span class="value" style="color:#ffc107;"><?= htmlspecialchars($currencySymbol) ?><?= number_format($carPendingRevenue, 0) ?></span>
                        </div>
                        <div class="item">
                            <span class="label"><span class="status-dot" style="background:#dc3545;"></span>Cancelled</span>
                            <span class="value" style="color:#dc3545;"><?= htmlspecialchars($currencySymbol) ?><?= number_format($carCancelledRevenue, 0) ?></span>
                        </div>
                        <div class="item">
                            <span class="label"><span class="status-dot" style="background:#17a2b8;"></span>Completed</span>
                            <span class="value" style="color:#17a2b8;"><?= htmlspecialchars($currencySymbol) ?><?= number_format($carCompletedRevenue, 0) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grand Total - Only Confirmed Revenue -->
            <div class="grand-total">
                <div>
                    <div class="label">Grand Total Revenue <span style="font-size:0.7rem;opacity:0.6;">(Confirmed only)</span></div>
                    <div class="amount"><?= htmlspecialchars($currencySymbol) ?><?= number_format($grandTotal, 2) ?></div>
                </div>
                <div class="monthly">
                    <div class="label">Monthly Revenue <span style="font-size:0.7rem;opacity:0.6;">(Confirmed)</span></div>
                    <div class="amount"><?= htmlspecialchars($currencySymbol) ?><?= number_format($monthlyRevenue, 2) ?></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>
    <script src="<?= APP_URL ?>javascript/main.js"></script>
</body>

</html>