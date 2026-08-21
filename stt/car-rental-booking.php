<?php
require_once './config/config.php';

// Get all car rentals
$allCarRentals = getCarRentals($pdo, 100); // Get all cars

$pageTitle = 'Car Rentals - ' . getSiteName($pdo);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/x-icon" href="<?= SITE_URL; ?>assets/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <title><?= htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="<?= SITE_URL; ?>assets/css/navbar.css">
    <style>
        /* =========================================================
           CAR RENTALS LISTING PAGE
        ========================================================= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f7f5;
            color: #1a1a1a;
        }

        .car-rentals-page {
            padding: 120px 20px 60px;
            max-width: 1300px;
            margin: 0 auto;
        }

        /* ===== HEADER ===== */
        .page-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .page-header .eyebrow {
            display: inline-block;
            padding: 8px 20px;
            border: 1px solid #e8e4df;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            color: #e9a88d;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: #fff;
            margin-bottom: 16px;
        }

        .page-header h1 {
            font-size: 42px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 12px;
        }

        .page-header h1 em {
            font-family: "Playfair Display", Georgia, serif;
            font-style: italic;
            color: #e9a88d;
        }

        .page-header p {
            color: #888;
            font-size: 17px;
            max-width: 500px;
            margin: 0 auto;
        }

        /* ===== FILTER / SEARCH BAR ===== */
        .filter-bar {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 40px;
            align-items: center;
            justify-content: space-between;
            background: #fff;
            padding: 16px 24px;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            border: 1px solid #f0efed;
        }

        .filter-bar .search-box {
            flex: 1;
            min-width: 200px;
            position: relative;
        }

        .filter-bar .search-box input {
            width: 100%;
            padding: 10px 16px 10px 42px;
            border: 1.5px solid #e8e4df;
            border-radius: 12px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            background: #fcfcfb;
            transition: all 0.2s;
            color: #1a1a1a;
        }

        .filter-bar .search-box input:focus {
            outline: none;
            border-color: #e9a88d;
            box-shadow: 0 0 0 4px rgba(233, 168, 141, 0.08);
        }

        .filter-bar .search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #bbb;
            font-size: 16px;
        }

        .filter-bar .filter-options {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-bar .filter-options select {
            padding: 10px 16px;
            border: 1.5px solid #e8e4df;
            border-radius: 12px;
            font-size: 13px;
            font-family: 'Inter', sans-serif;
            background: #fcfcfb;
            color: #555;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-bar .filter-options select:focus {
            outline: none;
            border-color: #e9a88d;
        }

        .filter-bar .results-count {
            font-size: 14px;
            color: #888;
            white-space: nowrap;
        }

        /* ===== CAR GRID ===== */
        .car-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
        }

        /* ===== CAR CARD ===== */
        .car-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #f0efed;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .car-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.06);
            border-color: #ddd;
        }

        .car-card .image-wrapper {
            position: relative;
            height: 220px;
            overflow: hidden;
            background: #e8e4df;
        }

        .car-card .image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .car-card:hover .image-wrapper img {
            transform: scale(1.05);
        }

        .car-card .image-wrapper .badge {
            position: absolute;
            top: 16px;
            left: 16px;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #22c55e;
            color: #fff;
        }

        .car-card .image-wrapper .badge.unavailable {
            background: #ef4444;
        }

        .car-card .car-info {
            padding: 20px 22px 22px;
        }

        .car-card .car-info .brand {
            font-size: 12px;
            font-weight: 600;
            color: #e9a88d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .car-card .car-info .name {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0 0 12px;
        }

        .car-card .car-info .meta {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f0efed;
        }

        .car-card .car-info .meta span {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #888;
        }

        .car-card .car-info .meta span i {
            color: #ccc;
            font-size: 14px;
        }

        .car-card .car-info .bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .car-card .car-info .bottom .price {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .car-card .car-info .bottom .price small {
            font-size: 13px;
            font-weight: 400;
            color: #888;
        }

        .car-card .car-info .bottom .btn-book-now {
            padding: 10px 22px;
            border: none;
            border-radius: 50px;
            background: #1a1a1a;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .car-card .car-info .bottom .btn-book-now:hover {
            background: #333;
            transform: scale(1.03);
        }

        .car-card .car-info .bottom .btn-book-now i {
            font-size: 14px;
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
        }

        .empty-state i {
            font-size: 56px;
            color: #ddd;
            margin-bottom: 20px;
            display: block;
        }

        .empty-state h3 {
            font-size: 24px;
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
        }

        .empty-state p {
            color: #888;
            font-size: 15px;
        }

        /* ===== MODAL / POPUP ===== */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: #fff;
            border-radius: 24px;
            max-width: 520px;
            width: 100%;
            padding: 40px 36px;
            position: relative;
            animation: modalIn 0.3s ease;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        @keyframes modalIn {
            from {
                transform: scale(0.95) translateY(20px);
                opacity: 0;
            }

            to {
                transform: scale(1) translateY(0);
                opacity: 1;
            }
        }

        .modal-close {
            position: absolute;
            top: 16px;
            right: 20px;
            background: none;
            border: none;
            font-size: 28px;
            color: #bbb;
            cursor: pointer;
            transition: color 0.2s;
            padding: 4px 8px;
        }

        .modal-close:hover {
            color: #555;
        }

        .modal-content .modal-car-summary {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px;
            background: #f8f7f5;
            border-radius: 16px;
            margin-bottom: 24px;
        }

        .modal-content .modal-car-summary img {
            width: 72px;
            height: 72px;
            border-radius: 12px;
            object-fit: cover;
            background: #e8e4df;
        }

        .modal-content .modal-car-summary .info {
            flex: 1;
        }

        .modal-content .modal-car-summary .info .name {
            font-weight: 600;
            font-size: 17px;
            color: #1a1a1a;
        }

        .modal-content .modal-car-summary .info .brand {
            font-size: 13px;
            color: #888;
        }

        .modal-content .modal-car-summary .info .price {
            font-weight: 700;
            font-size: 20px;
            color: #1a1a1a;
        }

        .modal-content .modal-car-summary .info .price small {
            font-size: 13px;
            font-weight: 400;
            color: #888;
        }

        .modal-content .form-group {
            margin-bottom: 16px;
        }

        .modal-content label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #555;
            margin-bottom: 6px;
        }

        .modal-content input {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #e8e4df;
            border-radius: 12px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
            background: #fcfcfb;
            color: #1a1a1a;
        }

        .modal-content input:focus {
            outline: none;
            border-color: #e9a88d;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(233, 168, 141, 0.08);
        }

        .modal-content input::placeholder {
            color: #bbb;
        }

        .modal-content .btn-submit {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 14px;
            background: #1a1a1a;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 4px;
        }

        .modal-content .btn-submit:hover {
            background: #333;
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.12);
        }

        .modal-content .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        .modal-content .btn-submit i {
            font-size: 18px;
        }

        /* ===== TOAST ===== */
        .toast-container {
            position: fixed;
            top: 100px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 400px;
            width: 100%;
        }

        .toast-message {
            padding: 16px 20px;
            border-radius: 16px;
            background: #1a1a1a;
            color: #fff;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            animation: slideInRight 0.4s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .toast-message.success {
            background: #22c55e;
        }

        .toast-message.error {
            background: #ef4444;
        }

        .toast-message i {
            font-size: 20px;
            flex-shrink: 0;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* =========================================================
           RESPONSIVE
        ========================================================= */
        @media (max-width: 768px) {
            .car-rentals-page {
                padding: 100px 16px 40px;
            }

            .page-header h1 {
                font-size: 32px;
            }

            .filter-bar {
                flex-direction: column;
                align-items: stretch;
                padding: 16px;
            }

            .filter-bar .search-box {
                min-width: auto;
            }

            .filter-bar .filter-options {
                flex-wrap: wrap;
            }

            .filter-bar .filter-options select {
                flex: 1;
                min-width: 120px;
            }

            .car-grid {
                grid-template-columns: 1fr 1fr;
                gap: 16px;
            }

            .car-card .image-wrapper {
                height: 160px;
            }

            .car-card .car-info {
                padding: 14px 16px 18px;
            }

            .car-card .car-info .name {
                font-size: 17px;
            }

            .car-card .car-info .bottom .price {
                font-size: 18px;
            }

            .car-card .car-info .bottom .btn-book-now {
                padding: 8px 16px;
                font-size: 12px;
            }

            .modal-content {
                padding: 30px 24px;
            }
        }

        @media (max-width: 480px) {
            .car-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .car-card .image-wrapper {
                height: 200px;
            }

            .modal-content {
                padding: 24px 18px;
            }

            .modal-content .modal-car-summary {
                flex-direction: column;
                text-align: center;
            }

            .toast-container {
                right: 12px;
                left: 12px;
                max-width: none;
            }
        }
    </style>
</head>

<body>

    <!-- ====== NAVBAR ====== -->
    <?php include './includes/navbar.php'; ?>

    <!-- ====== MAIN CONTENT ====== -->
    <main class="car-rentals-page">

        <!-- HEADER -->
        <div class="page-header">
            <span class="eyebrow">✦ DRIVE YOUR WAY</span>
            <h1>Rent Your <em>Perfect</em> Car</h1>
            <p>Comfortable, reliable cars for your everyday travel.</p>
        </div>

        <!-- FILTER BAR -->
        <div class="filter-bar">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchCars" placeholder="Search cars..." />
            </div>
            <div class="filter-options">
                <select id="filterBrand">
                    <option value="">All Brands</option>
                    <?php
                    $brands = [];
                    foreach ($allCarRentals as $car) {
                        if (!empty($car['car_brand']) && !in_array($car['car_brand'], $brands)) {
                            $brands[] = $car['car_brand'];
                        }
                    }
                    sort($brands);
                    foreach ($brands as $brand) {
                        echo '<option value="' . htmlspecialchars($brand) . '">' . htmlspecialchars($brand) . '</option>';
                    }
                    ?>
                </select>
                <select id="filterFuel">
                    <option value="">All Fuel</option>
                    <option value="Petrol">Petrol</option>
                    <option value="Diesel">Diesel</option>
                    <option value="Electric">Electric</option>
                </select>
                <select id="filterSeats">
                    <option value="">All Seats</option>
                    <option value="4">4 Seats</option>
                    <option value="6">6 Seats</option>
                    <option value="8">8 Seats</option>
                    <option value="10">10+ Seats</option>
                </select>
            </div>
            <span class="results-count" id="resultsCount"><?= count($allCarRentals); ?> cars</span>
        </div>

        <!-- CAR GRID -->
        <div class="car-grid" id="carGrid">
            <?php if (!empty($allCarRentals)): ?>
                <?php foreach ($allCarRentals as $car): ?>
                    <div class="car-card" data-car-id="<?= (int)$car['id']; ?>">
                        <div class="image-wrapper">
                            <img src="<?= htmlspecialchars($car['image_url']); ?>" alt="<?= htmlspecialchars($car['car_name']); ?>" loading="lazy">
                            <span class="badge <?= ($car['status'] ?? '') === 'available' ? '' : 'unavailable'; ?>">
                                <?= ($car['status'] ?? '') === 'available' ? 'Available' : 'Unavailable'; ?>
                            </span>
                        </div>
                        <div class="car-info">
                            <div class="brand"><?= htmlspecialchars($car['car_brand'] ?: 'Car'); ?></div>
                            <h3 class="name"><?= htmlspecialchars($car['car_name']); ?></h3>
                            <div class="meta">
                                <span><i class="bi bi-people"></i> <?= (int)$car['seating_capacity']; ?> Seats</span>
                                <span><i class="bi bi-fuel-pump"></i> <?= htmlspecialchars($car['fuel_type'] ?: 'N/A'); ?></span>
                                <span><i class="bi bi-gear"></i> <?= htmlspecialchars($car['transmission'] ?: 'Auto'); ?></span>
                            </div>
                            <div class="bottom">
                                <div class="price">
                                    ₹<?= number_format($car['per_day_amount'], 0); ?>
                                    <small>/ day</small>
                                </div>
                                <?php if (($car['status'] ?? '') === 'available'): ?>
                                    <button class="btn-book-now" data-car-id="<?= (int)$car['id']; ?>">
                                        <i class="bi bi-calendar-check"></i> Book Now
                                    </button>
                                <?php else: ?>
                                    <span style="font-size:13px;color:#aaa;font-weight:500;">Unavailable</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state" style="grid-column:1/-1;">
                    <i class="bi bi-car-front"></i>
                    <h3>No cars available</h3>
                    <p>Please check back later for new cars.</p>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <!-- ====== TOAST CONTAINER ====== -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- ====== BOOKING MODAL ====== -->
    <div class="modal-overlay" id="bookingModal">
        <div class="modal-content">
            <button class="modal-close" id="modalClose">&times;</button>
            <h3 style="margin-bottom:8px;font-size:22px;font-weight:700;">Book This Car</h3>
            <p style="color:#888;font-size:14px;margin-bottom:20px;">Fill in your details to book this car.</p>

            <div class="modal-car-summary" id="modalCarSummary">
                <img src="" alt="Car" id="modalCarImage">
                <div class="info">
                    <div class="name" id="modalCarName">Car Name</div>
                    <div class="brand" id="modalCarBrand">Brand</div>
                    <div class="price" id="modalCarPrice">₹0 <small>/ day</small></div>
                </div>
            </div>

            <form id="bookingForm">
                <input type="hidden" name="car_id" id="modalCarId">

                <div class="form-group">
                    <label for="modalCustomerName">Your Name</label>
                    <input type="text" id="modalCustomerName" name="customer_name" placeholder="Enter your full name" required>
                </div>

                <div class="form-group">
                    <label for="modalMobile">Mobile Number</label>
                    <input type="tel" id="modalMobile" name="mobile" placeholder="Enter your mobile number" required>
                </div>

                <button type="submit" class="btn-submit" id="modalSubmitBtn">
                    <i class="bi bi-calendar-check"></i> Submit Booking
                </button>
            </form>
        </div>
    </div>
  <!-- ====== FOOTER ====== -->
  <?php include './includes/footer.php'; ?>
    <!-- ====== SCRIPTS ====== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const carGrid = document.getElementById('carGrid');
            const searchInput = document.getElementById('searchCars');
            const filterBrand = document.getElementById('filterBrand');
            const filterFuel = document.getElementById('filterFuel');
            const filterSeats = document.getElementById('filterSeats');
            const resultsCount = document.getElementById('resultsCount');

            // ===== MODAL ELEMENTS =====
            const modal = document.getElementById('bookingModal');
            const modalClose = document.getElementById('modalClose');
            const modalCarId = document.getElementById('modalCarId');
            const modalCarName = document.getElementById('modalCarName');
            const modalCarBrand = document.getElementById('modalCarBrand');
            const modalCarPrice = document.getElementById('modalCarPrice');
            const modalCarImage = document.getElementById('modalCarImage');
            const bookingForm = document.getElementById('bookingForm');
            const submitBtn = document.getElementById('modalSubmitBtn');
            const toastContainer = document.getElementById('toastContainer');

            let currentCarId = null;

            // ===== FILTER FUNCTION =====
            function filterCars() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                const brand = filterBrand.value.toLowerCase();
                const fuel = filterFuel.value.toLowerCase();
                const seats = filterSeats.value;

                const cards = carGrid.querySelectorAll('.car-card');
                let visibleCount = 0;

                cards.forEach(card => {
                    const name = card.querySelector('.name')?.textContent?.toLowerCase() || '';
                    const carBrand = card.querySelector('.brand')?.textContent?.toLowerCase() || '';
                    const meta = card.querySelectorAll('.meta span');
                    let carFuel = '';
                    let carSeats = 0;

                    meta.forEach(span => {
                        const text = span.textContent;
                        if (text.includes('Seats')) {
                            carSeats = parseInt(text) || 0;
                        }
                        if (text.includes('Petrol') || text.includes('Diesel') || text.includes(
                                'Electric')) {
                            carFuel = text.trim();
                        }
                    });

                    let matches = true;

                    if (searchTerm && !name.includes(searchTerm) && !carBrand.includes(searchTerm)) {
                        matches = false;
                    }

                    if (brand && carBrand !== brand) {
                        matches = false;
                    }

                    if (fuel) {
                        const fuelLower = carFuel.toLowerCase();
                        if (!fuelLower.includes(fuel)) {
                            matches = false;
                        }
                    }

                    if (seats) {
                        const seatNum = parseInt(seats);
                        if (seatNum === 10 && carSeats < 10) {
                            matches = false;
                        } else if (seatNum < 10 && carSeats !== seatNum) {
                            matches = false;
                        }
                    }

                    card.style.display = matches ? '' : 'none';
                    if (matches) visibleCount++;
                });

                resultsCount.textContent = visibleCount + ' car' + (visibleCount !== 1 ? 's' : '');
            }

            // ===== FILTER EVENT LISTENERS =====
            searchInput.addEventListener('input', filterCars);
            filterBrand.addEventListener('change', filterCars);
            filterFuel.addEventListener('change', filterCars);
            filterSeats.addEventListener('change', filterCars);

            // ===== BOOK NOW BUTTONS =====
            document.querySelectorAll('.btn-book-now').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const carId = this.getAttribute('data-car-id');
                    openBookingModal(carId);
                });
            });

            // ===== CLICK ON CARD (to view details) =====
            document.querySelectorAll('.car-card').forEach(card => {
                card.addEventListener('click', function(e) {
                    // Don't trigger if clicking on button
                    if (e.target.closest('.btn-book-now')) return;
                    const carId = this.getAttribute('data-car-id');
                    window.location.href = '<?= SITE_URL; ?>car-rental.php?id=' + carId;
                });
            });

            // ===== OPEN MODAL =====
            function openBookingModal(carId) {
                // Find the car card
                const card = document.querySelector(`.car-card[data-car-id="${carId}"]`);
                if (!card) return;

                const name = card.querySelector('.name')?.textContent || '';
                const brand = card.querySelector('.brand')?.textContent || '';
                const price = card.querySelector('.price')?.textContent || '₹0';
                const img = card.querySelector('.image-wrapper img')?.src || '';

                // Set modal data
                modalCarId.value = carId;
                modalCarName.textContent = name;
                modalCarBrand.textContent = brand;
                modalCarPrice.innerHTML = price;
                modalCarImage.src = img;
                currentCarId = carId;

                // Reset form
                bookingForm.reset();
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-calendar-check"></i> Submit Booking';

                // Show modal
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            // ===== CLOSE MODAL =====
            function closeModal() {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }

            modalClose.addEventListener('click', closeModal);
            modal.addEventListener('click', function(e) {
                if (e.target === this) closeModal();
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.classList.contains('active')) {
                    closeModal();
                }
            });

            // ===== TOAST =====
            function showToast(message, type = 'success') {
                const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill';
                const toast = document.createElement('div');
                toast.className = `toast-message ${type}`;
                toast.innerHTML = `<i class="bi ${icon}"></i> ${message}`;
                toastContainer.appendChild(toast);

                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(100px)';
                    setTimeout(() => toast.remove(), 300);
                }, 4000);
            }

            // ===== FORM SUBMISSION =====
            bookingForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const name = document.getElementById('modalCustomerName').value.trim();
                const mobile = document.getElementById('modalMobile').value.trim();
                const carId = modalCarId.value;

                if (!name) {
                    showToast('Please enter your name.', 'error');
                    document.getElementById('modalCustomerName').focus();
                    return;
                }

                if (!mobile || mobile.length < 7) {
                    showToast('Please enter a valid mobile number.', 'error');
                    document.getElementById('modalMobile').focus();
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-spinner-border bi-spin"></i> Submitting...';

                const formData = new FormData();
                formData.append('car_id', carId);
                formData.append('customer_name', name);
                formData.append('mobile', mobile);

                fetch('<?= SITE_URL; ?>ajax/car-rental-booking.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="bi bi-calendar-check"></i> Submit Booking';

                        if (data.success) {
                            closeModal();
                            showToast('Booking submitted successfully! We\'ll contact you shortly.',
                                'success');
                            bookingForm.reset();
                        } else {
                            showToast(data.message || 'Something went wrong. Please try again.', 'error');
                        }
                    })
                    .catch(error => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="bi bi-calendar-check"></i> Submit Booking';
                        showToast('Network error. Please try again.', 'error');
                        console.error('Error:', error);
                    });
            });

        });
    </script>
</body>

</html>