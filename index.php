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
$pageTitle = "Dashboard";

// =============================================
// GET COUNTS FROM DATABASE
// =============================================

// Total Tours (Tour Packages)
$stmt = $pdo->query("SELECT COUNT(*) as count FROM tour_packages");
$totalTours = $stmt->fetchColumn();

// Total Travel Bookings
$stmt = $pdo->query("SELECT COUNT(*) as count FROM customer_travel_bookings");
$totalTravelBookings = $stmt->fetchColumn();

// Total Car Rentals
$stmt = $pdo->query("SELECT COUNT(*) as count FROM car_rentals");
$totalCarRentals = $stmt->fetchColumn();

// Total Car Bookings
$stmt = $pdo->query("SELECT COUNT(*) as count FROM customer_car_rentals_bookings");
$totalCarBookings = $stmt->fetchColumn();

// Total Customers (from all customer tables - unique)
$stmt = $pdo->query("SELECT COUNT(DISTINCT customer_name) as count FROM customer_tour_bookings");
$tourCustomers = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(DISTINCT customer_name) as count FROM customer_travel_bookings");
$travelCustomers = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(DISTINCT customer_name) as count FROM customer_car_rentals_bookings");
$carCustomers = $stmt->fetchColumn();

$totalCustomers = $tourCustomers + $travelCustomers + $carCustomers;

// Total Enquiries (CTA Messages)
$stmt = $pdo->query("SELECT COUNT(*) as count FROM cta_messages");
$totalEnquiries = $stmt->fetchColumn();

// Total Tour Bookings
$stmt = $pdo->query("SELECT COUNT(*) as count FROM customer_tour_bookings");
$totalTourBookings = $stmt->fetchColumn();

// Total Offers
$stmt = $pdo->query("SELECT COUNT(*) as count FROM offers WHERE status = 'active'");
$totalActiveOffers = $stmt->fetchColumn();

// Today's bookings
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM customer_tour_bookings WHERE DATE(created_at) = ?");
$stmt->execute([$today]);
$todayTourBookings = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM customer_travel_bookings WHERE DATE(created_at) = ?");
$stmt->execute([$today]);
$todayTravelBookings = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM customer_car_rentals_bookings WHERE DATE(created_at) = ?");
$stmt->execute([$today]);
$todayCarBookings = $stmt->fetchColumn();

$todayBookings = $todayTourBookings + $todayTravelBookings + $todayCarBookings;

// Pending Bookings
$stmt = $pdo->query("SELECT COUNT(*) as count FROM customer_tour_bookings WHERE status = 'pending'");
$pendingTour = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as count FROM customer_travel_bookings WHERE status = 'pending'");
$pendingTravel = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as count FROM customer_car_rentals_bookings WHERE status = 'pending'");
$pendingCar = $stmt->fetchColumn();

$pendingBookings = $pendingTour + $pendingTravel + $pendingCar;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>Dashboard · Tour Admin</title>
    <style>
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            flex-shrink: 0;
        }

        .stat-icon.blue {
            background: linear-gradient(135deg, #3a8bb9, #1f5777);
        }

        .stat-icon.green {
            background: linear-gradient(135deg, #34b07e, #1a7a55);
        }

        .stat-icon.orange {
            background: linear-gradient(135deg, #f3a261, #d97d36);
        }

        .stat-icon.purple {
            background: linear-gradient(135deg, #b47bd5, #7f4d9e);
        }

        .stat-icon.red {
            background: linear-gradient(135deg, #e74c6f, #c0392b);
        }

        .stat-icon.teal {
            background: linear-gradient(135deg, #1abc9c, #16a085);
        }

        .stat-icon.pink {
            background: linear-gradient(135deg, #fd79a8, #e84393);
        }

        .stat-icon.indigo {
            background: linear-gradient(135deg, #6c5ce7, #4a00e0);
        }

        .stat-number {
            font-weight: 700;
            font-size: 1.6rem;
            letter-spacing: -0.2px;
            color: #0a1a2b;
            line-height: 1.2;
        }

        .stat-label {
            font-weight: 500;
            color: #6a7f8f;
            font-size: 0.78rem;
        }

        .trend-up {
            color: #1a7a55;
            background: #dff0e8;
            padding: 0.1rem 0.6rem;
            border-radius: 30px;
            font-size: 0.6rem;
            font-weight: 600;
            display: inline-block;
            margin-top: 2px;
        }

        .trend-down {
            color: #c0392b;
            background: #fde8e8;
            padding: 0.1rem 0.6rem;
            border-radius: 30px;
            font-size: 0.6rem;
            font-weight: 600;
            display: inline-block;
            margin-top: 2px;
        }

        .card-stats {
            background: white;
            border: none;
            border-radius: 20px;
            padding: 1.2rem 1.2rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
            transition: all 0.2s;
            border: 1px solid rgba(255, 255, 255, 0.5);
            height: 100%;
        }

        .card-stats:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.06);
        }

        .card-glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 215, 100, 0.15);
            border-radius: 24px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
            padding: 1.5rem 1.5rem;
        }

        .card-glass .card-title {
            font-weight: 600;
            color: #0a1a2b;
            font-size: 1rem;
        }

        .badge-tour {
            background: rgba(255, 215, 100, 0.15);
            color: #b8860b;
            padding: 0.2rem 0.8rem;
            border-radius: 30px;
            font-weight: 500;
            font-size: 0.7rem;
        }

        .badge-tour i {
            color: #f5b342;
        }

        .table-modern td,
        .table-modern th {
            border-color: rgba(0, 0, 0, 0.03);
            padding: 0.6rem 0.3rem;
            font-weight: 500;
            color: #1d3f53;
            font-size: 0.82rem;
        }

        .table-modern thead th {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #6a7f8f;
            font-weight: 600;
        }

        .status-badge {
            padding: 0.2rem 0.7rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
            display: inline-block;
        }

        .status-badge.confirmed {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }

        .status-badge.pending {
            background: rgba(255, 193, 7, 0.15);
            color: #ffc107;
        }

        .status-badge.cancelled {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }

        .status-badge.completed {
            background: rgba(23, 162, 184, 0.15);
            color: #17a2b8;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #f0f3f7;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-item .activity-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .activity-item .activity-icon.booking {
            background: rgba(255, 215, 100, 0.15);
            color: #b8860b;
        }

        .activity-item .activity-icon.enquiry {
            background: rgba(111, 66, 193, 0.15);
            color: #6f42c1;
        }

        .activity-item .activity-icon.car {
            background: rgba(0, 123, 255, 0.15);
            color: #007bff;
        }

        .activity-item .activity-icon.tour {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }

        .activity-item .activity-content {
            flex: 1;
        }

        .activity-item .activity-content .activity-title {
            font-weight: 500;
            color: #123b4f;
            font-size: 0.85rem;
        }

        .activity-item .activity-content .activity-time {
            font-size: 0.65rem;
            color: #9bb2c5;
        }

        @media (max-width: 768px) {
            .stat-number {
                font-size: 1.2rem;
            }

            .card-stats {
                padding: 0.8rem;
            }

            .card-glass {
                padding: 1rem;
            }

            .table-modern td,
            .table-modern th {
                font-size: 0.7rem;
                padding: 0.4rem 0.2rem;
            }
        }

        @media (max-width: 576px) {
            .stat-number {
                font-size: 1rem;
            }

            .stat-icon {
                width: 38px;
                height: 38px;
                font-size: 1.1rem;
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
                <small>Dashboard</small>
            </div>
        </div>

        <!-- ====== STATS CARDS ====== -->
        <div class="row g-3 mb-4">
            <!-- Total Tours -->
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card-stats d-flex align-items-center">
                    <div class="stat-icon blue me-3"><i class="bi bi-suitcase-fill"></i></div>
                    <div>
                        <div class="stat-number"><?= number_format($totalTours) ?></div>
                        <div class="stat-label">Total Tours</div>
                        <span class="trend-up"><i class="bi bi-arrow-up-short"></i> Active</span>
                    </div>
                </div>
            </div>

            <!-- Total Travel Bookings -->
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card-stats d-flex align-items-center">
                    <div class="stat-icon green me-3"><i class="bi bi-geo-alt-fill"></i></div>
                    <div>
                        <div class="stat-number"><?= number_format($totalTravelBookings) ?></div>
                        <div class="stat-label">Travel Bookings</div>
                        <span class="trend-up"><i class="bi bi-arrow-up-short"></i> <?= $todayTravelBookings ?> today</span>
                    </div>
                </div>
            </div>

            <!-- Total Car Rentals -->
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card-stats d-flex align-items-center">
                    <div class="stat-icon orange me-3"><i class="bi bi-car-front-fill"></i></div>
                    <div>
                        <div class="stat-number"><?= number_format($totalCarRentals) ?></div>
                        <div class="stat-label">Car Rentals</div>
                        <span class="trend-up"><i class="bi bi-arrow-up-short"></i> <?= $totalCarBookings ?> bookings</span>
                    </div>
                </div>
            </div>

            <!-- Total Customers -->
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card-stats d-flex align-items-center">
                    <div class="stat-icon purple me-3"><i class="bi bi-people-fill"></i></div>
                    <div>
                        <div class="stat-number"><?= number_format($totalCustomers) ?></div>
                        <div class="stat-label">Total Customers</div>
                        <span class="trend-up"><i class="bi bi-arrow-up-short"></i> Unique</span>
                    </div>
                </div>
            </div>

            <!-- Total Enquiries -->
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card-stats d-flex align-items-center">
                    <div class="stat-icon red me-3"><i class="bi bi-chat-dots-fill"></i></div>
                    <div>
                        <div class="stat-number"><?= number_format($totalEnquiries) ?></div>
                        <div class="stat-label">Total Enquiries</div>
                        <span class="trend-up"><i class="bi bi-arrow-up-short"></i> CTA Messages</span>
                    </div>
                </div>
            </div>

            <!-- Total Tour Bookings -->
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card-stats d-flex align-items-center">
                    <div class="stat-icon teal me-3"><i class="bi bi-calendar-check-fill"></i></div>
                    <div>
                        <div class="stat-number"><?= number_format($totalTourBookings) ?></div>
                        <div class="stat-label">Tour Bookings</div>
                        <span class="trend-up"><i class="bi bi-arrow-up-short"></i> <?= $todayTourBookings ?> today</span>
                    </div>
                </div>
            </div>

            <!-- Today's Bookings -->
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card-stats d-flex align-items-center">
                    <div class="stat-icon pink me-3"><i class="bi bi-calendar2-check"></i></div>
                    <div>
                        <div class="stat-number"><?= number_format($todayBookings) ?></div>
                        <div class="stat-label">Today's Bookings</div>
                        <span class="trend-up"><i class="bi bi-arrow-up-short"></i> New</span>
                    </div>
                </div>
            </div>

            <!-- Pending Bookings -->
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card-stats d-flex align-items-center">
                    <div class="stat-icon indigo me-3"><i class="bi bi-clock-fill"></i></div>
                    <div>
                        <div class="stat-number"><?= number_format($pendingBookings) ?></div>
                        <div class="stat-label">Pending Bookings</div>
                        <span class="trend-down"><i class="bi bi-arrow-down-short"></i> Needs action</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====== RECENT ACTIVITY ====== -->
        <div class="row g-4">
            <!-- Recent Bookings -->
            <div class="col-lg-8">
                <div class="card-glass">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                        <h5 class="card-title mb-0"><i class="bi bi-clock-history me-2" style="color:#f5b342;"></i>Recent Bookings</h5>
                        <a href="bookings.php" class="badge-tour" style="text-decoration:none;">
                            <i class="bi bi-eye me-1"></i> View All
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-modern align-middle">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Get recent bookings from all tables
                                $recentBookings = [];

                                // Tour bookings
                                $stmt = $pdo->query("SELECT customer_name, 'Tour' as type, created_at, status FROM customer_tour_bookings ORDER BY created_at DESC LIMIT 5");
                                while ($row = $stmt->fetch()) {
                                    $recentBookings[] = $row;
                                }

                                // Travel bookings
                                $stmt = $pdo->query("SELECT customer_name, 'Travel' as type, created_at, status FROM customer_travel_bookings ORDER BY created_at DESC LIMIT 5");
                                while ($row = $stmt->fetch()) {
                                    $recentBookings[] = $row;
                                }

                                // Car bookings
                                $stmt = $pdo->query("SELECT customer_name, 'Car' as type, created_at, status FROM customer_car_rentals_bookings ORDER BY created_at DESC LIMIT 5");
                                while ($row = $stmt->fetch()) {
                                    $recentBookings[] = $row;
                                }

                                // Sort by created_at descending
                                usort($recentBookings, function ($a, $b) {
                                    return strtotime($b['created_at']) - strtotime($a['created_at']);
                                });

                                // Get latest 5
                                $recentBookings = array_slice($recentBookings, 0, 5);

                                if (empty($recentBookings)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">No recent bookings</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recentBookings as $booking): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($booking['customer_name']) ?></strong></td>
                                            <td>
                                                <span class="badge-type <?= strtolower($booking['type']) ?>">
                                                    <?= $booking['type'] ?>
                                                </span>
                                            </td>
                                            <td><?= date('M d, Y h:i A', strtotime($booking['created_at'])) ?></td>
                                            <td>
                                                <span class="status-badge <?= $booking['status'] ?>">
                                                    <?= ucfirst($booking['status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Enquiries -->
            <div class="col-lg-4">
                <div class="card-glass">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                        <h5 class="card-title mb-0"><i class="bi bi-chat-dots me-2" style="color:#f5b342;"></i>Recent Enquiries</h5>
                        <a href="bookings.php?type=cta" class="badge-tour" style="text-decoration:none;">
                            <i class="bi bi-eye me-1"></i> View All
                        </a>
                    </div>
                    <div>
                        <?php
                        $stmt = $pdo->query("SELECT full_name, message, created_at, status FROM cta_messages ORDER BY created_at DESC LIMIT 5");
                        $enquiries = $stmt->fetchAll();

                        if (empty($enquiries)): ?>
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-chat-dots" style="font-size:2rem;color:#e8edf3;"></i>
                                <p class="mt-2" style="font-size:0.85rem;">No enquiries yet</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($enquiries as $enquiry): ?>
                                <div class="activity-item">
                                    <div class="activity-icon enquiry">
                                        <i class="bi bi-envelope"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title"><?= htmlspecialchars($enquiry['full_name']) ?></div>
                                        <div style="font-size:0.75rem;color:#5f7d92;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:180px;">
                                            <?= htmlspecialchars(substr($enquiry['message'], 0, 40)) ?>...
                                        </div>
                                        <div class="activity-time">
                                            <?= date('M d, Y h:i A', strtotime($enquiry['created_at'])) ?>
                                            <span class="status-badge <?= $enquiry['status'] ?>" style="font-size:0.5rem;padding:0.1rem 0.4rem;margin-left:6px;">
                                                <?= ucfirst($enquiry['status']) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====== QUICK STATS ROW ====== -->
        <div class="row g-3 mt-2">
            <div class="col-md-3 col-6">
                <div class="card-stats text-center py-3">
                    <div style="font-size:1.8rem;color:#f5b342;">
                        <i class="bi bi-tags"></i>
                    </div>
                    <div class="stat-number" style="font-size:1.2rem;"><?= $totalActiveOffers ?></div>
                    <div class="stat-label">Active Offers</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card-stats text-center py-3">
                    <div style="font-size:1.8rem;color:#28a745;">
                        <i class="bi bi-star"></i>
                    </div>
                    <div class="stat-number" style="font-size:1.2rem;"><?= $totalTours + $totalCarRentals ?></div>
                    <div class="stat-label">Total Services</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card-stats text-center py-3">
                    <div style="font-size:1.8rem;color:#007bff;">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stat-number" style="font-size:1.2rem;"><?= $totalCustomers ?></div>
                    <div class="stat-label">Customers</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card-stats text-center py-3">
                    <div style="font-size:1.8rem;color:#6f42c1;">
                        <i class="bi bi-envelope"></i>
                    </div>
                    <div class="stat-number" style="font-size:1.2rem;"><?= $totalEnquiries ?></div>
                    <div class="stat-label">Enquiries</div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= APP_URL ?>javascript/main.js"></script>
</body>

</html>