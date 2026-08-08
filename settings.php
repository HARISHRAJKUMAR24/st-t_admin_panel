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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .settings-wrapper {
            padding: 20px;
        }

        .settings-header {
            margin-bottom: 30px;
        }

        .settings-header h4 {
            font-weight: 600;
            color: #123b4f;
            margin-bottom: 5px;
        }

        .settings-header p {
            color: #5f7d92;
            font-size: 0.95rem;
        }

        .settings-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(4px);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 24px rgba(0, 20, 30, 0.04);
            cursor: pointer;
            transition: all 0.3s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .settings-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 20, 30, 0.08);
            border-color: #ffd966;
        }

        .settings-card .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1rem;
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

        .settings-card .card-title {
            font-weight: 600;
            color: #123b4f;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .settings-card .card-description {
            color: #5f7d92;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .settings-card .card-arrow {
            position: absolute;
            bottom: 1.5rem;
            right: 1.5rem;
            color: #9bb2c5;
            transition: all 0.3s ease;
        }

        .settings-card:hover .card-arrow {
            color: #ffd966;
            transform: translateX(5px);
        }

        .settings-card .card-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 215, 100, 0.2);
            color: #b8860b;
            font-size: 0.65rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* ============================================
           MODAL STYLES
           ============================================ */
        .settings-modal .modal-content {
            border-radius: 24px;
            border: none;
            box-shadow: 0 20px 60px rgba(0, 20, 30, 0.15);
        }

        .settings-modal .modal-header {
            border-bottom: 2px solid #f0f3f7;
            padding: 1.5rem 2rem;
        }

        .settings-modal .modal-header .modal-title {
            font-weight: 600;
            color: #123b4f;
        }

        .settings-modal .modal-body {
            padding: 2rem;
        }

        .settings-modal .modal-footer {
            border-top: 2px solid #f0f3f7;
            padding: 1.5rem 2rem;
        }

        .settings-modal .form-label {
            font-weight: 500;
            color: #123b4f;
            font-size: 0.85rem;
        }

        .settings-modal .form-control,
        .settings-modal .form-select {
            border-radius: 12px;
            padding: 0.6rem 0.9rem;
            border: 2px solid #e8edf3;
            background: rgba(255, 255, 255, 0.6);
            font-weight: 500;
            transition: all 0.2s;
            font-size: 0.9rem;
        }

        .settings-modal .form-control:focus,
        .settings-modal .form-select:focus {
            border-color: #ffd966;
            box-shadow: 0 0 0 4px rgba(255, 215, 100, 0.15);
            background: white;
        }

        .settings-modal .btn-submit {
            background: linear-gradient(145deg, #0b2a3e 0%, #123b4f 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 0.7rem 2rem;
            font-weight: 600;
            transition: all 0.3s;
            min-width: 120px;
        }

        .settings-modal .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(11, 42, 62, 0.2);
            color: #ffd966;
        }

        .settings-modal .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
        }

        .settings-modal .btn-secondary {
            border-radius: 12px;
            padding: 0.7rem 1.8rem;
            font-weight: 500;
        }

        .social-input-group {
            display: flex;
            align-items: center;
            gap: 10px;
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
        }

        .social-input-group .social-icon.facebook {
            background: #1877f2;
            color: white;
        }

        .social-input-group .social-icon.twitter {
            background: #000;
            color: white;
        }

        .social-input-group .social-icon.instagram {
            background: linear-gradient(45deg, #405de6, #5851db, #833ab4, #c13584, #e1306c, #fd1d1d);
            color: white;
        }

        .social-input-group .social-icon.youtube {
            background: #ff0000;
            color: white;
        }

        .social-input-group .social-icon.linkedin {
            background: #0a66c2;
            color: white;
        }

        .social-input-group .social-icon.whatsapp {
            background: #25d366;
            color: white;
        }

        @media (max-width: 768px) {
            .settings-wrapper {
                padding: 10px;
            }

            .settings-card {
                padding: 1.5rem;
            }

            .settings-modal .modal-header {
                padding: 1rem 1.25rem;
            }

            .settings-modal .modal-body {
                padding: 1.25rem;
            }

            .settings-modal .modal-footer {
                padding: 1rem 1.25rem;
                flex-direction: column;
            }

            .settings-modal .modal-footer .btn {
                width: 100%;
            }

            .social-input-group {
                flex-wrap: wrap;
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
                <p>Manage your account settings and preferences</p>
            </div>

            <!-- Settings Cards -->
            <div class="row g-4">
                <!-- Card 1: General Settings -->
                <div class="col-md-4">
                    <div class="settings-card" onclick="openSettingsModal('general')">
                        <span class="card-badge">Profile</span>
                        <div class="card-icon general">
                            <i class="bi bi-person-gear"></i>
                        </div>
                        <h5 class="card-title">General Settings</h5>
                        <p class="card-description">Update your name, email, and other account details</p>
                        <div class="card-arrow">
                            <i class="bi bi-chevron-right fs-4"></i>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Social Links -->
                <div class="col-md-4">
                    <div class="settings-card" onclick="openSettingsModal('social')">
                        <span class="card-badge">Connect</span>
                        <div class="card-icon social">
                            <i class="bi bi-share"></i>
                        </div>
                        <h5 class="card-title">Social Links</h5>
                        <p class="card-description">Add your social media profiles and contact links</p>
                        <div class="card-arrow">
                            <i class="bi bi-chevron-right fs-4"></i>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Password Change -->
                <div class="col-md-4">
                    <div class="settings-card" onclick="openSettingsModal('password')">
                        <span class="card-badge">Security</span>
                        <div class="card-icon password">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <h5 class="card-title">Change Password</h5>
                        <p class="card-description">Update your password to keep your account secure</p>
                        <div class="card-arrow">
                            <i class="bi bi-chevron-right fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ============================================
    MODALS
    ============================================ -->

    <!-- Modal 1: General Settings -->
    <div class="modal fade settings-modal" id="generalModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="generalSettingsForm">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-person-gear me-2" style="color:#f5b342;"></i>General Settings</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="settingsName" value="<?= htmlspecialchars($currentUser['name'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="settingsEmail" value="<?= htmlspecialchars($currentUser['email'] ?? '') ?>" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="settingsPhone" value="<?= htmlspecialchars($currentUser['phone'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-submit" id="generalSubmitBtn">
                            <span id="generalSubmitText"><i class="bi bi-check2 me-2"></i>Save Changes</span>
                            <span id="generalSubmitSpinner" class="spinner-border spinner-border-sm" style="display:none;"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 2: Social Links -->
    <div class="modal fade settings-modal" id="socialModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="socialSettingsForm">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-share me-2" style="color:#f5b342;"></i>Social Links</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Facebook</label>
                            <div class="social-input-group">
                                <div class="social-icon facebook"><i class="bi bi-facebook"></i></div>
                                <input type="url" class="form-control" id="socialFacebook" placeholder="https://facebook.com/yourpage" value="<?= htmlspecialchars($currentUser['facebook'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Twitter / X</label>
                            <div class="social-input-group">
                                <div class="social-icon twitter"><i class="bi bi-twitter-x"></i></div>
                                <input type="url" class="form-control" id="socialTwitter" placeholder="https://twitter.com/yourhandle" value="<?= htmlspecialchars($currentUser['twitter'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Instagram</label>
                            <div class="social-input-group">
                                <div class="social-icon instagram"><i class="bi bi-instagram"></i></div>
                                <input type="url" class="form-control" id="socialInstagram" placeholder="https://instagram.com/yourprofile" value="<?= htmlspecialchars($currentUser['instagram'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">YouTube</label>
                            <div class="social-input-group">
                                <div class="social-icon youtube"><i class="bi bi-youtube"></i></div>
                                <input type="url" class="form-control" id="socialYoutube" placeholder="https://youtube.com/@yourchannel" value="<?= htmlspecialchars($currentUser['youtube'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">LinkedIn</label>
                            <div class="social-input-group">
                                <div class="social-icon linkedin"><i class="bi bi-linkedin"></i></div>
                                <input type="url" class="form-control" id="socialLinkedin" placeholder="https://linkedin.com/in/yourprofile" value="<?= htmlspecialchars($currentUser['linkedin'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">WhatsApp</label>
                            <div class="social-input-group">
                                <div class="social-icon whatsapp"><i class="bi bi-whatsapp"></i></div>
                                <input type="text" class="form-control" id="socialWhatsapp" placeholder="+1234567890" value="<?= htmlspecialchars($currentUser['whatsapp'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-submit" id="socialSubmitBtn">
                            <span id="socialSubmitText"><i class="bi bi-check2 me-2"></i>Save Changes</span>
                            <span id="socialSubmitSpinner" class="spinner-border spinner-border-sm" style="display:none;"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal 3: Change Password -->
    <div class="modal fade settings-modal" id="passwordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="passwordSettingsForm">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-shield-lock me-2" style="color:#f5b342;"></i>Change Password</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Current Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="currentPassword" placeholder="Enter current password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('currentPassword', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="newPassword" placeholder="Enter new password" required minlength="6">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('newPassword', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted">Password must be at least 6 characters long</small>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm new password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirmPassword', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-submit" id="passwordSubmitBtn">
                            <span id="passwordSubmitText"><i class="bi bi-check2 me-2"></i>Update Password</span>
                            <span id="passwordSubmitSpinner" class="spinner-border spinner-border-sm" style="display:none;"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>
    <script src="<?= APP_URL ?>javascript/main.js"></script>

</body>

</html>