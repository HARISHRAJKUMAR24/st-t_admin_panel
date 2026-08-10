<nav class="sidebar" id="sideNav">
    <button class="sidebar-close" id="sidebarClose" aria-label="Close sidebar">
        <i class="bi bi-x-lg"></i>
    </button>

    <!-- Brand: logo from database -->
    <div class="sidebar-brand">
        <?php
        // Get panel_logo from settings table
        $logo = getData('panel_logo', 'settings', 'id = 1');
        $logoPath = '';

        // If logo exists in database, use it, otherwise use empty
        if (!empty($logo)) {
            $logoPath = APP_URL . $logo;
        }
        ?>
        <?php if (!empty($logo)): ?>
            <img src="<?= $logoPath ?>" alt="Website Logo" class="brand-logo-img" />
        <?php endif; ?>
    </div>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' || basename($_SERVER['PHP_SELF']) == 'index' ? 'active' : '' ?>" href="index">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : '' ?>" href="#">
                <i class="bi bi-calendar-event-fill"></i> Bookings
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'customers.php' ? 'active' : '' ?>" href="customers.php">
                <i class="bi bi-people-fill"></i> Customers
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'customer-tracking.php' ? 'active' : '' ?>" href="customer-tracking">
                <i class="bi bi-person-bounding-box"></i> Customer Tracking
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'revenue.php' ? 'active' : '' ?>" href="#">
                <i class="bi bi-wallet2"></i> Revenue
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'car-rentals.php' || basename($_SERVER['PHP_SELF']) == 'add-car-rental.php' || basename($_SERVER['PHP_SELF']) == 'edit-car-rental.php' ? 'active' : '' ?>" href="car-rentals">
                <i class="bi bi-car-front-fill"></i> Car Rental
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'tour-packages.php' ? 'active' : '' ?>" href="tour-packages">
                <i class="bi bi-suitcase-fill"></i> Tours
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'offers.php' ? 'active' : '' ?>" href="offers">
                <i class="bi bi-tag-fill"></i> Offers
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'settings.php' || basename($_SERVER['PHP_SELF']) == 'settings-general.php' ? 'active' : '' ?>" href="settings">
                <i class="bi bi-sliders2"></i> Settings
            </a>
        </li>
    </ul>
</nav>