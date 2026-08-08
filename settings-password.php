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
$pageTitle = "Change Password";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>Change Password · Tour Admin</title>
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
            max-width: 600px;
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

        .form-control.is-valid {
            border-color: #28a745;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }

        .input-group .btn-outline-secondary {
            border-radius: 0 10px 10px 0;
            border: 2px solid #e8edf3;
            border-left: none;
            background: rgba(255, 255, 255, 0.6);
            color: #5f7d92;
            transition: all 0.2s;
        }

        .input-group .btn-outline-secondary:hover {
            background: #ffd966;
            border-color: #ffd966;
            color: #123b4f;
        }

        .input-group .btn-outline-secondary:focus {
            box-shadow: none;
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

        .password-strength-container {
            margin-top: 8px;
        }

        .password-strength {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
            background: #e8edf3;
            width: 0%;
        }

        .password-strength.weak {
            background: #dc3545;
            width: 25%;
        }
        .password-strength.medium {
            background: #ffc107;
            width: 50%;
        }
        .password-strength.strong {
            background: #28a745;
            width: 75%;
        }
        .password-strength.very-strong {
            background: #28a745;
            width: 100%;
        }

        .password-strength-text {
            font-size: 0.75rem;
            color: #5f7d92;
            margin-top: 4px;
        }

        .password-strength-text.weak {
            color: #dc3545;
        }
        .password-strength-text.medium {
            color: #ffc107;
        }
        .password-strength-text.strong {
            color: #28a745;
        }
        .password-strength-text.very-strong {
            color: #28a745;
        }

        .password-requirements {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }

        .password-requirements .req {
            font-size: 0.7rem;
            color: #5f7d92;
            padding: 2px 8px;
            border-radius: 12px;
            border: 1px solid #e8edf3;
            background: rgba(255, 255, 255, 0.4);
            transition: all 0.3s;
        }

        .password-requirements .req.met {
            color: #28a745;
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.1);
        }

        .password-requirements .req i {
            margin-right: 4px;
        }

        .btn-generate {
            background: #ffd966;
            color: #123b4f;
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1.2rem;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.3s;
            cursor: pointer;
        }

        .btn-generate:hover {
            background: #f5c842;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .settings-wrapper {
                padding: 10px;
            }

            .settings-container {
                padding: 1rem;
            }

            .form-actions {
                flex-direction: column;
            }

            .form-actions .btn {
                width: 100%;
            }

            .password-requirements {
                gap: 4px;
            }

            .password-requirements .req {
                font-size: 0.65rem;
                padding: 2px 6px;
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
                <small>Change Password</small>
            </div>
        </div>

        <div class="settings-wrapper">
           

            <div class="settings-header">
                <h4><i class="bi bi-shield-lock me-2" style="color:#f5b342;"></i>Change Password</h4>
               
            </div>

            <div class="settings-container">
                <form id="passwordForm">
                    <h6 class="section-title">Security</h6>

                    <!-- Current Password -->
                    <div class="mb-3">
                        <label class="form-label">Current Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="currentPassword" placeholder="Enter current password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('currentPassword', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div id="currentPasswordError" class="text-danger" style="font-size:0.75rem; display:none;">
                            <i class="bi bi-exclamation-circle me-1"></i> Incorrect current password
                        </div>
                    </div>

                    <!-- New Password -->
                    <div class="mb-3">
                        <label class="form-label">New Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="newPassword" placeholder="Enter new password" required minlength="8">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('newPassword', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-generate" type="button" onclick="generatePassword()">
                                <i class="bi bi-dice-5 me-1"></i> Generate
                            </button>
                        </div>

                        <!-- Password Strength -->
                        <div class="password-strength-container">
                            <div class="password-strength" id="passwordStrength"></div>
                            <div class="password-strength-text" id="passwordStrengthText">Password must be at least 8 characters</div>
                        </div>

                        <!-- Password Requirements -->
                        <div class="password-requirements">
                            <span class="req" id="reqLength"><i class="bi bi-check-lg"></i> 8+ characters</span>
                            <span class="req" id="reqUppercase"><i class="bi bi-check-lg"></i> Uppercase</span>
                            <span class="req" id="reqLowercase"><i class="bi bi-check-lg"></i> Lowercase</span>
                            <span class="req" id="reqNumber"><i class="bi bi-check-lg"></i> Number</span>
                            <span class="req" id="reqSpecial"><i class="bi bi-check-lg"></i> Special character</span>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm new password" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirmPassword', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div id="confirmPasswordError" class="text-danger" style="font-size:0.75rem; display:none;">
                            <i class="bi bi-exclamation-circle me-1"></i> Passwords do not match
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='settings.php'">Cancel</button>
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <span id="submitText">Update Password</span>
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
    <script src="<?= APP_URL ?>javascript/settings-password.js"></script>
</body>

</html>