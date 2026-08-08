<?php
require_once './config/config.php';

// If already logged in, redirect to dashboard
if (isLoggedIn() && verifyToken($pdo)) {
    header("Location: " . APP_URL . "index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tour & Travel · Login</title>
    <!-- Bootstrap 5 + Icons + Google Font -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f4f7fc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper {
            width: 100%;
            max-width: 440px;
            padding: 1rem;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 36px;
            padding: 0.8rem 1.8rem 2rem 1.8rem;
            box-shadow: 0 24px 56px rgba(11, 42, 62, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.6);
            transition: all 0.3s;
        }

        .login-card:hover {
            box-shadow: 0 32px 72px rgba(11, 42, 62, 0.16);
        }

        .login-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 0.5rem;
            padding: 0.2rem;
        }

        .login-logo .logo-image {
            width: 100%;
            max-width: 200px;
            height: auto;
            border-radius: 16px;
            padding: 0.2rem;
            transition: all 0.3s;
        }

        .login-logo .logo-image img {
            width: 100%;
            height: auto;
            border-radius: 12px;
            display: block;
            object-fit: contain;
        }

        .login-logo .logo-image .fallback-icon {
            display: none;
            font-size: 3.5rem;
            color: #0b2a3e;
            padding: 0.5rem;
            text-align: center;
        }

        .login-logo .logo-image img:not([src])+.fallback-icon,
        .login-logo .logo-image img[src=""]+.fallback-icon {
            display: block;
        }

        .login-title {
            font-weight: 700;
            font-size: 1.4rem;
            color: #0b2a3e;
            margin-bottom: 0.1rem;
            text-align: center;
        }

        .login-subtitle {
            font-size: 0.85rem;
            color: #5f7d92;
            text-align: center;
            margin-bottom: 1.2rem;
        }

        .form-control {
            border-radius: 14px;
            padding: 0.7rem 1rem;
            border: 2px solid #e8edf3;
            background: rgba(255, 255, 255, 0.6);
            font-weight: 500;
            transition: all 0.2s;
            font-size: 0.9rem;
        }

        .form-control:focus {
            border-color: #ffd966;
            box-shadow: 0 0 0 4px rgba(255, 215, 100, 0.15);
            background: white;
        }

        .form-control::placeholder {
            color: #9bb2c5;
            font-weight: 400;
        }

        .input-group-text {
            background: rgba(255, 255, 255, 0.6);
            border: 2px solid #e8edf3;
            border-right: none;
            border-radius: 14px 0 0 14px;
            color: #5f7d92;
            font-size: 1rem;
            padding: 0 0.8rem;
        }

        .input-group .form-control {
            border-radius: 0 14px 14px 0;
            border-left: none;
        }

        .input-group .form-control:focus {
            border-left: none;
        }

        .input-group {
            margin-bottom: 0.8rem;
        }

        .password-toggle {
            background: rgba(255, 255, 255, 0.6);
            border: 2px solid #e8edf3;
            border-left: none;
            border-radius: 0 14px 14px 0;
            color: #5f7d92;
            cursor: pointer;
            padding: 0 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            font-size: 1rem;
        }

        .password-toggle:hover {
            color: #0b2a3e;
            background: rgba(255, 255, 255, 0.8);
        }

        .btn-login {
            background: linear-gradient(145deg, #0b2a3e 0%, #123b4f 100%);
            color: #fff;
            border: none;
            border-radius: 14px;
            padding: 0.8rem;
            font-weight: 600;
            font-size: 0.95rem;
            width: 100%;
            transition: all 0.3s;
            box-shadow: 0 8px 20px rgba(11, 42, 62, 0.15);
            letter-spacing: 0.5px;
            margin-top: 0.3rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(11, 42, 62, 0.25);
            background: linear-gradient(145deg, #123b4f 0%, #0b2a3e 100%);
            color: #ffd966;
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
        }

        .login-footer {
            margin-top: 1.2rem;
            text-align: center;
            font-size: 0.75rem;
            color: #5f7d92;
            opacity: 0.7;
        }

        .login-footer i {
            color: #ffd966;
            opacity: 0.5;
        }

        /* Toast Notification Styles */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 400px;
            width: 100%;
        }

        .toast-notification {
            padding: 1rem 1.2rem;
            border-radius: 16px;
            color: #fff;
            font-weight: 500;
            font-size: 0.95rem;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
            transform: translateX(120%);
            animation: slideIn 0.5s ease forwards;
            display: flex;
            align-items: center;
            gap: 12px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .toast-notification.hide {
            animation: slideOut 0.5s ease forwards;
        }

        .toast-notification .toast-icon {
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .toast-notification .toast-close {
            margin-left: auto;
            background: transparent;
            border: none;
            color: inherit;
            opacity: 0.7;
            cursor: pointer;
            font-size: 1.2rem;
            padding: 0 4px;
            transition: 0.2s;
        }

        .toast-notification .toast-close:hover {
            opacity: 1;
            transform: scale(1.1);
        }

        .toast-success {
            background: linear-gradient(135deg, #1a7a55, #34b07e);
            border-left: 4px solid #ffd966;
        }

        .toast-error {
            background: linear-gradient(135deg, #721c24, #e74c5e);
            border-left: 4px solid #ff6b6b;
        }

        .toast-info {
            background: linear-gradient(135deg, #1f5777, #3a8bb9);
            border-left: 4px solid #74b9ff;
        }

        @keyframes slideIn {
            0% {
                transform: translateX(120%);
                opacity: 0;
            }

            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            0% {
                transform: translateX(0);
                opacity: 1;
            }

            100% {
                transform: translateX(120%);
                opacity: 0;
            }
        }

        @media (max-width: 576px) {
            .login-wrapper {
                padding: 0.8rem;
            }

            .login-card {
                padding: 1.5rem 1.2rem;
                border-radius: 28px;
            }

            .login-logo .logo-image {
                max-width: 160px;
            }

            .login-title {
                font-size: 1.2rem;
            }

            .login-subtitle {
                font-size: 0.8rem;
                margin-bottom: 1rem;
            }

            .form-control {
                padding: 0.6rem 0.8rem;
                font-size: 0.85rem;
            }

            .btn-login {
                padding: 0.7rem;
                font-size: 0.9rem;
            }

            .input-group {
                margin-bottom: 0.6rem;
            }

            .toast-container {
                max-width: 90%;
                right: 10px;
                top: 10px;
            }
        }

        @media (max-width: 400px) {
            .login-card {
                padding: 1.2rem 1rem;
            }

            .login-logo .logo-image {
                max-width: 140px;
            }

            .login-title {
                font-size: 1.1rem;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>

<body>

    <div class="login-wrapper">
        <div class="login-card">

            <div class="login-logo">
                <div class="logo-image">
                    <img src="<?= APP_URL ?>uploads/logo.png"
                        alt="Tours & Travels Logo"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block';" />
                    <div class="fallback-icon">
                        <i class="bi bi-compass-fill"></i>
                    </div>
                </div>
            </div>

            <!-- REMOVED inline alert - now using toast -->
            <form id="loginForm">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" class="form-control" id="emailInput" placeholder="Email address" required autofocus />
                </div>

                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" class="form-control" id="passwordInput" placeholder="Password" required />
                    <span class="password-toggle" id="passwordToggle" onclick="togglePassword()">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </span>
                </div>

                <button type="submit" class="btn-login" id="loginBtn">
                    <span id="loginText">Sign In</span>
                    <span id="loginSpinner" class="spinner-border spinner-border-sm" style="display:none;" role="status"></span>
                </button>
            </form>

            <div class="login-footer">
                <i class="bi bi-shield-lock me-1"></i> Secure login · v2.0
            </div>

        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
    </script>
    <!-- Custom JS for AJAX login -->
    <script src="<?= APP_URL ?>javascript/login.js"></script>
</body>

</html>