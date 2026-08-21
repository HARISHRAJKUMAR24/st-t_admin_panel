<?php
require_once './config/config.php';

// Get the hero image URL directly
$heroImage = getHeroImage($pdo);
$siteName = getSiteName($pdo);
$siteTitle = getSiteTitle($pdo);
$footerText = getFooterText($pdo);
$logo = getWebsiteLogo($pdo);
$favicon = getFavicon($pdo);
$travelPackages = getTravelPackages($pdo, 6);
$allTourNames = getAllTourNames($pdo);
$carRentals = getCarRentals($pdo, 6);

// Get all travel bookings/packages
$travelPackagesList = getTravelPackages($pdo, 100);

// Booking success
$bookingSuccess = isset($_GET['booking_success']) && $_GET['booking_success'] == '1';
$bookingId = $_GET['booking'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/x-icon" href="<?= $favicon; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <title>Travel Booking - <?= htmlspecialchars($siteTitle); ?></title>
    <link rel="stylesheet" href="<?= SITE_URL; ?>assets/css/navbar.css">
    <link rel="stylesheet" href="<?= SITE_URL; ?>assets/css/styles.css">
    <style>
        /* =========================================================
           TRAVEL BOOKING PAGE
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

        .travel-booking-page {
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

        /* ===== BOOKING GRID ===== */
        .booking-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }

        /* ===== BOOKING CARD ===== */
        .booking-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #f0efed;
            transition: all 0.3s ease;
        }

        .booking-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.06);
            border-color: #ddd;
        }

        .booking-card .image-wrapper {
            position: relative;
            height: 200px;
            overflow: hidden;
            background: #e8e4df;
        }

        .booking-card .image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .booking-card:hover .image-wrapper img {
            transform: scale(1.05);
        }

        .booking-card .image-wrapper .badge {
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

        .booking-card .booking-info {
            padding: 20px 22px 22px;
        }

        .booking-card .booking-info .car-type {
            font-size: 12px;
            font-weight: 600;
            color: #e9a88d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .booking-card .booking-info .route {
            font-size: 16px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0 0 8px;
        }

        .booking-card .booking-info .route i {
            color: #ccc;
            font-size: 14px;
            margin: 0 6px;
        }

        .booking-card .booking-info .meta {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 14px;
            padding-bottom: 14px;
            border-bottom: 1px solid #f0efed;
        }

        .booking-card .booking-info .meta span {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #888;
        }

        .booking-card .booking-info .meta span i {
            color: #ccc;
            font-size: 14px;
        }

        .booking-card .booking-info .bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .booking-card .booking-info .bottom .price {
            font-size: 22px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .booking-card .booking-info .bottom .price small {
            font-size: 13px;
            font-weight: 400;
            color: #888;
        }

        .booking-card .booking-info .bottom .btn-book-now {
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

        .booking-card .booking-info .bottom .btn-book-now:hover {
            background: #333;
            transform: scale(1.03);
        }

        .booking-card .booking-info .bottom .btn-book-now i {
            font-size: 14px;
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            grid-column: 1/-1;
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

        /* ===== MODAL ===== */
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

        .modal-content .modal-summary {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px;
            background: #f8f7f5;
            border-radius: 16px;
            margin-bottom: 24px;
        }

        .modal-content .modal-summary .info {
            flex: 1;
        }

        .modal-content .modal-summary .info .route-text {
            font-weight: 600;
            font-size: 16px;
            color: #1a1a1a;
        }

        .modal-content .modal-summary .info .details {
            font-size: 13px;
            color: #888;
        }

        .modal-content .modal-summary .info .price {
            font-weight: 700;
            font-size: 20px;
            color: #1a1a1a;
        }

        .modal-content .modal-summary .info .price small {
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

        .modal-content input,
        .modal-content select {
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

        .modal-content input:focus,
        .modal-content select:focus {
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
            .travel-booking-page {
                padding: 100px 16px 40px;
            }

            .page-header h1 {
                font-size: 32px;
            }

            .booking-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .modal-content {
                padding: 30px 24px;
            }

            .toast-container {
                right: 12px;
                left: 12px;
                max-width: none;
            }
        }

        @media (max-width: 480px) {
            .modal-content {
                padding: 24px 18px;
            }

            .modal-content .modal-summary {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>

<body>

    <!-- ====== NAVBAR ====== -->
    <?php include './includes/navbar.php'; ?>

    <!-- ====== MAIN CONTENT ====== -->
    <main class="travel-booking-page">

        <!-- HEADER -->
        <div class="page-header">
            <span class="eyebrow">✦ TRAVEL YOUR WAY</span>
            <h1>Book Your <em>Travel</em> Package</h1>
            <p>Choose from our comfortable travel packages and book your journey.</p>
        </div>

        <!-- BOOKING GRID -->
        <div class="booking-grid">

            <?php if (!empty($travelPackagesList)): ?>
                <?php foreach ($travelPackagesList as $package): ?>
                    <?php
                    // Decode stops
                    $stops = $package['stops'] ?? [];
                    if (is_string($stops)) {
                        $stops = json_decode($stops, true) ?: [];
                    }
                    $pickup = $stops[0]['pickup'] ?? 'Pickup';
                    $drop = $stops[0]['drop'] ?? 'Destination';
                    ?>
                    <div class="booking-card" data-package-id="<?= (int)$package['id']; ?>">
                        <div class="image-wrapper">
                            <img src="<?= htmlspecialchars($package['car_image'] ?? 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=800&q=90'); ?>" alt="<?= htmlspecialchars($package['car_name'] ?? 'Travel'); ?>" loading="lazy">
                            <span class="badge">Available</span>
                        </div>
                        <div class="booking-info">
                            <div class="car-type"><?= htmlspecialchars($package['car_type'] ?? 'Travel Vehicle'); ?></div>
                            <div class="route">
                                <?= htmlspecialchars($pickup); ?>
                                <i class="bi bi-arrow-right"></i>
                                <?= htmlspecialchars($drop); ?>
                            </div>
                            <div class="meta">
                                <span><i class="bi bi-people"></i> <?= (int)$package['seat_count']; ?> Seats</span>
                                <span><i class="bi bi-clock"></i> <?= (int)$package['days']; ?> Days</span>
                                <span><i class="bi bi-rulers"></i> <?= number_format((float)$package['total_distance'] ?? 0, 0); ?> km</span>
                            </div>
                            <div class="bottom">
                                <div class="price">
                                    ₹<?= number_format((float)$package['total_price'] ?? 0, 0); ?>
                                    <small>/ trip</small>
                                </div>
                                <button class="btn-book-now" data-package-id="<?= (int)$package['id']; ?>">
                                    <i class="bi bi-calendar-check"></i> Book Now
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-car-front"></i>
                    <h3>No travel packages available</h3>
                    <p>Please check back later or contact us for custom travel arrangements.</p>
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
            <h3 style="margin-bottom:8px;font-size:22px;font-weight:700;">Book Travel Package</h3>
            <p style="color:#888;font-size:14px;margin-bottom:20px;">Fill in your details to book this travel package.</p>

            <div class="modal-summary" id="modalSummary">
                <div class="info">
                    <div class="route-text" id="modalRoute">Pickup → Destination</div>
                    <div class="details" id="modalDetails">4 Seats • 2 Days • 150 km</div>
                    <div class="price" id="modalPrice">₹0 <small>/ trip</small></div>
                </div>
            </div>

            <form id="bookingForm">
                <input type="hidden" name="package_id" id="modalPackageId">

                <div class="form-group">
                    <label for="modalName">Your Name</label>
                    <input type="text" id="modalName" name="name" placeholder="Enter your full name" required>
                </div>

                <div class="form-group">
                    <label for="modalPhone">Phone Number</label>
                    <input type="tel" id="modalPhone" name="phone" placeholder="Enter your mobile number" required>
                </div>

                <div class="form-group">
                    <label for="modalTravelDate">Travel Date</label>
                    <input type="date" id="modalTravelDate" name="travel_date" min="<?= date('Y-m-d'); ?>" required>
                </div>

                <button type="submit" class="btn-submit" id="modalSubmitBtn">
                    <i class="bi bi-check2-circle"></i> Request Booking
                </button>
            </form>
        </div>
    </div>

    <!-- ====== SUCCESS MODAL ====== -->
    <div class="modal-overlay" id="successModal">
        <div class="modal-content" style="text-align:center;">
            <button class="modal-close" id="successClose">&times;</button>
            <div style="width:72px;height:72px;border-radius:50%;background:#eef7ef;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                <div style="width:52px;height:52px;border-radius:50%;background:#2d8a50;color:#fff;display:flex;align-items:center;justify-content:center;font-size:27px;">
                    <i class="bi bi-check-lg"></i>
                </div>
            </div>
            <h3 style="font-size:24px;font-weight:700;margin-bottom:8px;">Booking Submitted!</h3>
            <p style="color:#888;font-size:14px;margin-bottom:20px;">Your travel booking request has been sent successfully. We'll contact you shortly.</p>
            <div style="background:#f7f6f3;border-radius:14px;padding:15px 17px;margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;">
                <span style="color:#999;font-size:10px;font-weight:600;text-transform:uppercase;">Booking ID</span>
                <strong style="font-size:15px;" id="successBookingId">TRV0001</strong>
            </div>
            <button class="btn-submit" id="successDone" style="width:100%;padding:14px;border:none;border-radius:14px;background:#1a1a1a;color:#fff;font-size:16px;font-weight:600;cursor:pointer;transition:all 0.3s;font-family:'Inter',sans-serif;">
                Done
            </button>
        </div>
    </div>
  <!-- ====== FOOTER ====== -->
  <?php include './includes/footer.php'; ?>
    <!-- ====== SCRIPTS ====== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ===== ELEMENTS =====
            const modal = document.getElementById('bookingModal');
            const modalClose = document.getElementById('modalClose');
            const modalPackageId = document.getElementById('modalPackageId');
            const modalRoute = document.getElementById('modalRoute');
            const modalDetails = document.getElementById('modalDetails');
            const modalPrice = document.getElementById('modalPrice');
            const bookingForm = document.getElementById('bookingForm');
            const submitBtn = document.getElementById('modalSubmitBtn');
            const toastContainer = document.getElementById('toastContainer');

            const successModal = document.getElementById('successModal');
            const successClose = document.getElementById('successClose');
            const successDone = document.getElementById('successDone');
            const successBookingId = document.getElementById('successBookingId');

            let currentPackageId = null;

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

            // ===== BOOK NOW BUTTONS =====
            document.querySelectorAll('.btn-book-now').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const packageId = this.getAttribute('data-package-id');
                    openBookingModal(packageId);
                });
            });

            // ===== OPEN MODAL =====
            function openBookingModal(packageId) {
                // Find the package card
                const card = document.querySelector(`.booking-card[data-package-id="${packageId}"]`);
                if (!card) return;

                const route = card.querySelector('.route')?.textContent?.trim() || 'Pickup → Destination';
                const meta = card.querySelectorAll('.meta span');
                let seats = '0 Seats',
                    days = '0 Days',
                    distance = '0 km';
                meta.forEach(span => {
                    const text = span.textContent;
                    if (text.includes('Seats')) seats = text.trim();
                    if (text.includes('Days')) days = text.trim();
                    if (text.includes('km')) distance = text.trim();
                });
                const priceText = card.querySelector('.price')?.textContent?.trim() || '₹0';

                // Set modal data
                modalPackageId.value = packageId;
                modalRoute.textContent = route;
                modalDetails.textContent = `${seats} • ${days} • ${distance}`;
                modalPrice.innerHTML = priceText;
                currentPackageId = packageId;

                // Reset form
                bookingForm.reset();
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-check2-circle"></i> Request Booking';

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

            // ===== CLOSE SUCCESS =====
            function closeSuccess() {
                successModal.classList.remove('active');
                document.body.style.overflow = '';
            }

            successClose.addEventListener('click', closeSuccess);
            successDone.addEventListener('click', closeSuccess);
            successModal.addEventListener('click', function(e) {
                if (e.target === this) closeSuccess();
            });

            // ===== FORM SUBMISSION =====
            bookingForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const name = document.getElementById('modalName').value.trim();
                const phone = document.getElementById('modalPhone').value.trim();
                const travelDate = document.getElementById('modalTravelDate').value;
                const packageId = modalPackageId.value;

                if (!name) {
                    showToast('Please enter your name.', 'error');
                    document.getElementById('modalName').focus();
                    return;
                }

                if (!phone || phone.length < 7) {
                    showToast('Please enter a valid phone number.', 'error');
                    document.getElementById('modalPhone').focus();
                    return;
                }

                if (!travelDate) {
                    showToast('Please select a travel date.', 'error');
                    document.getElementById('modalTravelDate').focus();
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bi bi-spinner-border bi-spin"></i> Submitting...';

                const formData = new FormData();
                formData.append('package_id', packageId);
                formData.append('name', name);
                formData.append('phone', phone);
                formData.append('travel_date', travelDate);
                formData.append('pickup', 'Pickup');
                formData.append('destination', 'Destination');

                fetch('<?= SITE_URL; ?>ajax/travel-booking-submit.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="bi bi-check2-circle"></i> Request Booking';

                        if (data.success) {
                            closeModal();
                            // Show success modal with booking ID
                            successBookingId.textContent = data.booking_id || 'TRV0001';
                            successModal.classList.add('active');
                            document.body.style.overflow = 'hidden';
                            bookingForm.reset();
                        } else {
                            showToast(data.message || 'Something went wrong. Please try again.', 'error');
                        }
                    })
                    .catch(error => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="bi bi-check2-circle"></i> Request Booking';
                        showToast('Network error. Please try again.', 'error');
                        console.error('Error:', error);
                    });
            });

        });
    </script>
</body>

</html>