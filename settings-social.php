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
$pageTitle = "Social Links";

// Get current settings
$stmt = $pdo->prepare("SELECT * FROM settings WHERE id = 1");
$stmt->execute();
$settings = $stmt->fetch();

if (!$settings) {
    $stmt = $pdo->prepare("INSERT INTO settings (id, timezone) VALUES (1, 'Asia/Kolkata')");
    $stmt->execute();
    $settings = ['id' => 1];
}

// Decode social links if exists
$socialLinks = [];
if (!empty($settings['social_links'])) {
    $socialLinks = json_decode($settings['social_links'], true);
    if (!is_array($socialLinks)) {
        $socialLinks = [];
    }
}

// Define social platforms with icons
$socialPlatforms = [
    'facebook' => ['label' => 'Facebook', 'icon' => 'bi-facebook', 'color' => '#1877f2', 'placeholder' => 'https://facebook.com/yourpage'],
    'twitter' => ['label' => 'Twitter / X', 'icon' => 'bi-twitter-x', 'color' => '#000000', 'placeholder' => 'https://twitter.com/yourhandle'],
    'instagram' => ['label' => 'Instagram', 'icon' => 'bi-instagram', 'color' => '#e4405f', 'placeholder' => 'https://instagram.com/yourprofile'],
    'youtube' => ['label' => 'YouTube', 'icon' => 'bi-youtube', 'color' => '#ff0000', 'placeholder' => 'https://youtube.com/@yourchannel'],
    'linkedin' => ['label' => 'LinkedIn', 'icon' => 'bi-linkedin', 'color' => '#0a66c2', 'placeholder' => 'https://linkedin.com/in/yourprofile'],
    'whatsapp' => ['label' => 'WhatsApp', 'icon' => 'bi-whatsapp', 'color' => '#25d366', 'placeholder' => '+1234567890'],
    'tiktok' => ['label' => 'TikTok', 'icon' => 'bi-tiktok', 'color' => '#000000', 'placeholder' => 'https://tiktok.com/@yourhandle'],
    'snapchat' => ['label' => 'Snapchat', 'icon' => 'bi-snapchat', 'color' => '#fffc00', 'placeholder' => 'https://snapchat.com/add/yourusername'],
    'pinterest' => ['label' => 'Pinterest', 'icon' => 'bi-pinterest', 'color' => '#e60023', 'placeholder' => 'https://pinterest.com/yourprofile'],
    'reddit' => ['label' => 'Reddit', 'icon' => 'bi-reddit', 'color' => '#ff4500', 'placeholder' => 'https://reddit.com/user/yourusername'],
    'telegram' => ['label' => 'Telegram', 'icon' => 'bi-telegram', 'color' => '#0088cc', 'placeholder' => 'https://t.me/yourusername'],
    'discord' => ['label' => 'Discord', 'icon' => 'bi-discord', 'color' => '#5865f2', 'placeholder' => 'https://discord.gg/yourinvite'],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>Social Links · Tour Admin</title>
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
            font-size: 1.3rem;
        }

        .settings-header p {
            color: #5f7d92;
            font-size: 0.85rem;
        }

        .settings-container {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 4px 16px rgba(0, 20, 30, 0.04);
            max-width: 900px;
            margin: 0 auto;
        }

        .settings-container .section-title {
            font-weight: 600;
            color: #123b4f;
            font-size: 1rem;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #ffd966;
        }

        .form-label {
            font-weight: 500;
            color: #123b4f;
            font-size: 0.8rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.5rem 0.8rem;
            border: 2px solid #e8edf3;
            background: rgba(255, 255, 255, 0.6);
            font-weight: 500;
            transition: all 0.2s;
            font-size: 0.85rem;
        }

        .form-control:focus {
            border-color: #ffd966;
            box-shadow: 0 0 0 4px rgba(255, 215, 100, 0.15);
            background: white;
        }

        .btn-submit {
            background: linear-gradient(145deg, #0b2a3e 0%, #123b4f 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.6rem 2.5rem;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 0.9rem;
            min-width: 160px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(11, 42, 62, 0.2);
            color: #ffd966;
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
        }

        .btn-secondary {
            border-radius: 10px;
            padding: 0.6rem 1.8rem;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e8edf3;
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

        /* Social Input Groups */
        .social-input-group {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 10px;
            border: 2px solid #e8edf3;
            transition: all 0.3s ease;
        }

        .social-input-group:hover {
            border-color: #ffd966;
            background: rgba(255, 255, 255, 0.6);
        }

        .social-input-group:focus-within {
            border-color: #ffd966;
            box-shadow: 0 0 0 4px rgba(255, 215, 100, 0.1);
            background: white;
        }

        .social-input-group .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
            color: white;
            transition: all 0.3s ease;
        }

        .social-input-group .social-icon:hover {
            transform: scale(1.05);
        }

        .social-input-group .form-control {
            border: none;
            background: transparent;
            padding: 0.4rem 0.6rem;
            flex: 1;
        }

        .social-input-group .form-control:focus {
            border: none;
            box-shadow: none;
            background: transparent;
        }

        .social-input-group .social-label {
            font-size: 0.7rem;
            color: #5f7d92;
            font-weight: 500;
            min-width: 40px;
        }

        .social-input-group .social-clear {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            transition: all 0.2s;
            font-size: 0.9rem;
            opacity: 0.6;
        }

        .social-input-group .social-clear:hover {
            opacity: 1;
            background: rgba(220, 53, 69, 0.1);
        }

        .social-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        @media (max-width: 768px) {
            .settings-wrapper {
                padding: 10px;
            }

            .settings-container {
                padding: 1rem;
            }

            .social-grid {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .form-actions .btn {
                width: 100%;
            }

            .social-input-group {
                flex-wrap: wrap;
                padding: 10px;
            }

            .social-input-group .social-label {
                min-width: 30px;
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
                <small>Social Links</small>
            </div>
        </div>

        <div class="settings-wrapper">


            <div class="settings-header">
                <h4><i class="bi bi-share me-2" style="color:#f5b342;"></i>Social Links</h4>
                
            </div>

            <div class="settings-container">
                <form id="socialSettingsForm">
                    <h6 class="section-title">Social Media Profiles</h6>

                    <div class="social-grid">
                        <?php foreach ($socialPlatforms as $key => $platform): ?>
                            <div class="social-input-group">
                                <div class="social-icon" style="background: <?= $platform['color'] ?>;">
                                    <i class="bi <?= $platform['icon'] ?>"></i>
                                </div>
                                <input type="text" 
                                       class="form-control" 
                                       id="social_<?= $key ?>" 
                                       name="social_<?= $key ?>" 
                                       placeholder="<?= $platform['placeholder'] ?>"
                                       value="<?= htmlspecialchars($socialLinks[$key] ?? '') ?>">
                                <button type="button" class="social-clear" onclick="clearSocial('<?= $key ?>')" title="Clear field">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Single Save Button -->
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='settings.php'">Cancel</button>
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <span id="submitText">Save Social Links</span>
                            <span id="submitSpinner" class="spinner-border spinner-border-sm" style="display:none;"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>
    <script src="<?= APP_URL ?>javascript/main.js"></script>
    <script src="<?= APP_URL ?>javascript/settings-social.js"></script>
</body>

</html>