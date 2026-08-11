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
$pageTitle = "Settings";

// Get website settings
$stmt = $pdo->prepare("SELECT * FROM settings WHERE id = 1");
$stmt->execute();
$websiteSettings = $stmt->fetch();

if (!$websiteSettings) {
    $stmt = $pdo->prepare("INSERT INTO settings (id) VALUES (1)");
    $stmt->execute();
    $websiteSettings = ['id' => 1, 'hero_image' => null, 'site_name' => 'Tour Admin', 'site_tagline' => 'Your Travel Partner'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>Settings · Tour Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .settings-wrapper {
            padding: 20px;
        }

        .settings-header {
            margin-bottom: 25px;
        }

        .settings-header h4 {
            font-weight: 600;
            color: #123b4f;
            margin-bottom: 5px;
            font-size: 1.2rem;
        }

        .settings-header p {
            color: #5f7d92;
            font-size: 0.85rem;
            margin-bottom: 0;
        }

        .settings-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
            border-radius: 14px;
            padding: 1.2rem 1.2rem 1rem 1.2rem;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 4px 16px rgba(0, 20, 30, 0.04);
            cursor: pointer;
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            display: block;
            color: inherit;
        }

        .settings-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 20, 30, 0.08);
            border-color: #ffd966;
            text-decoration: none;
            color: inherit;
        }

        .settings-card .card-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 0.6rem;
            transition: all 0.3s ease;
        }

        .settings-card:hover .card-icon {
            transform: scale(1.05);
        }

        .settings-card .card-icon.general {
            background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
            color: #2e7d32;
        }

        .settings-card .card-icon.social {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            color: #1565c0;
        }

        .settings-card .card-icon.password {
            background: linear-gradient(135deg, #fce4ec, #f8bbd0);
            color: #c62828;
        }

        .settings-card .card-icon.website {
            background: linear-gradient(135deg, #fff3e0, #ffe0b2);
            color: #e65100;
        }

        .settings-card .card-title {
            font-weight: 600;
            color: #123b4f;
            font-size: 0.95rem;
            margin-bottom: 0.2rem;
        }

        .settings-card .card-description {
            color: #5f7d92;
            font-size: 0.75rem;
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .settings-card .card-arrow {
            color: #9bb2c5;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .settings-card:hover .card-arrow {
            color: #ffd966;
            transform: translateX(4px);
        }

        .settings-card .card-badge {
            position: absolute;
            top: 0.6rem;
            right: 0.6rem;
            background: rgba(255, 215, 100, 0.2);
            color: #b8860b;
            font-size: 0.55rem;
            padding: 0.15rem 0.6rem;
            border-radius: 16px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .row.gap-3 {
            --bs-gutter-y: 1rem;
        }

        @media (max-width: 768px) {
            .settings-wrapper {
                padding: 10px;
            }

            .settings-card {
                padding: 1rem;
            }

            .settings-card .card-icon {
                width: 38px;
                height: 38px;
                font-size: 1.2rem;
            }

            .settings-card .card-title {
                font-size: 0.85rem;
            }

            .settings-card .card-description {
                font-size: 0.7rem;
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
                <small>Settings</small>
            </div>
        </div>

        <!-- ====== SETTINGS ====== -->
        <div class="settings-wrapper">
            <!-- Header -->
            <div class="settings-header">
                <h4><i class="bi bi-gear me-2" style="color:#f5b342;"></i>Settings</h4>
                
            </div>

            <!-- Settings Cards - Compact Grid -->
            <div class="row g-3">
                <!-- Card 1: General Settings -->
                <div class="col-md-6 col-lg-3">
                    <a href="settings-general.php" class="settings-card">
                        <span class="card-badge">Profile</span>
                        <div class="card-icon general">
                            <i class="bi bi-person-gear"></i>
                        </div>
                        <h5 class="card-title">General Settings</h5>
                        <p class="card-description">Update your name, email &amp; account details</p>
                        <div class="card-arrow">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </a>
                </div>

                <!-- Card 2: Website Settings -->
                <div class="col-md-6 col-lg-3">
                    <a href="settings-website.php" class="settings-card">
                        <span class="card-badge">Website</span>
                        <div class="card-icon website">
                            <i class="bi bi-globe2"></i>
                        </div>
                        <h5 class="card-title">Website Settings</h5>
                        <p class="card-description">Manage hero image, site name &amp; more</p>
                        <div class="card-arrow">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </a>
                </div>

                <!-- Card 3: Social Links -->
                <div class="col-md-6 col-lg-3">
                    <a href="settings-social.php" class="settings-card">
                        <span class="card-badge">Connect</span>
                        <div class="card-icon social">
                            <i class="bi bi-share"></i>
                        </div>
                        <h5 class="card-title">Social Links</h5>
                        <p class="card-description">Add your social media profiles &amp; links</p>
                        <div class="card-arrow">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </a>
                </div>

                <!-- Card 4: Password Change -->
                <div class="col-md-6 col-lg-3">
                    <a href="settings-password.php" class="settings-card">
                        <span class="card-badge">Security</span>
                        <div class="card-icon password">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <h5 class="card-title">Change Password</h5>
                        <p class="card-description">Update your password to keep it secure</p>
                        <div class="card-arrow">
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>
    <script src="<?= APP_URL ?>javascript/main.js"></script>

</body>

</html>