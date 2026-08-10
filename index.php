<?php
include_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
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

            </div>
        </div>

        <!-- ====== CARDS ====== -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card-stats d-flex align-items-center">
                    <div class="stat-icon blue me-3"><i class="bi bi-airplane-fill"></i></div>
                    <div>
                        <div class="stat-number">1,284</div>
                        <div class="stat-label">Total tours</div>
                        <span class="trend-up"><i class="bi bi-arrow-up-short"></i> +12.4%</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card-stats d-flex align-items-center">
                    <div class="stat-icon green me-3"><i class="bi bi-person-check-fill"></i></div>
                    <div>
                        <div class="stat-number">348</div>
                        <div class="stat-label">Active bookings</div>
                        <span class="trend-up"><i class="bi bi-arrow-up-short"></i> +8.1%</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card-stats d-flex align-items-center">
                    <div class="stat-icon orange me-3"><i class="bi bi-star-fill"></i></div>
                    <div>
                        <div class="stat-number">4.9</div>
                        <div class="stat-label">Avg. rating</div>
                        <span class="trend-up"><i class="bi bi-arrow-up-short"></i> +0.3</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                <div class="card-stats d-flex align-items-center">
                    <div class="stat-icon purple me-3"><i class="bi bi-cash-coin"></i></div>
                    <div>
                        <div class="stat-number">$92.4k</div>
                        <div class="stat-label">Revenue (MTD)</div>
                        <span class="trend-up"><i class="bi bi-arrow-up-short"></i> +23%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====== TABLE CARD ====== -->
        <div class="card-glass">
            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
                <h5 class="card-title mb-0"><i class="bi bi-clock-history me-2" style="color:#f5b342;"></i>Recent tour
                    activity</h5>
                <span class="badge-tour"><i class="bi bi-eye me-1"></i> 9 new</span>
            </div>
            <div class="table-responsive">
                <table class="table table-modern align-middle">
                    <thead>
                        <tr>
                            <th>Tour</th>
                            <th>Destination</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><i class="bi bi-pin-map-fill me-2" style="color:#3a8bb9;"></i> Island Escape</td>
                            <td>Bali, ID</td>
                            <td>Aug 12, 2026</td>
                            <td><span
                                    class="badge bg-success bg-opacity-10 text-success px-3 py-1 rounded-pill">Confirmed</span>
                            </td>
                            <td><strong>$1,240</strong></td>
                        </tr>
                        <tr>
                            <td><i class="bi bi-pin-map-fill me-2" style="color:#34b07e;"></i> Alpine Trek</td>
                            <td>Swiss Alps</td>
                            <td>Aug 18, 2026</td>
                            <td><span
                                    class="badge bg-warning bg-opacity-10 text-warning px-3 py-1 rounded-pill">Pending</span>
                            </td>
                            <td><strong>$870</strong></td>
                        </tr>
                        <tr>
                            <td><i class="bi bi-pin-map-fill me-2" style="color:#f3a261;"></i> City Lights</td>
                            <td>Tokyo, JP</td>
                            <td>Aug 22, 2026</td>
                            <td><span class="badge bg-info bg-opacity-10 text-info px-3 py-1 rounded-pill">In
                                    progress</span></td>
                            <td><strong>$2,310</strong></td>
                        </tr>
                        <tr>
                            <td><i class="bi bi-pin-map-fill me-2" style="color:#b47bd5;"></i> Safari Adventure</td>
                            <td>Kenya</td>
                            <td>Sep 2, 2026</td>
                            <td><span
                                    class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1 rounded-pill">Draft</span>
                            </td>
                            <td><strong>$3,450</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-1">
                <span class="badge-tour"><i class="bi bi-arrow-right-circle me-1"></i> View all </span>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS for AJAX login -->
    <script src="<?= APP_URL ?>javascript/main.js"></script>
</body>

</html>