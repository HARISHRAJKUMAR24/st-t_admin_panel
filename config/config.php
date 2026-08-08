<?php

/**
 * config.php - Simple DB Connection & Authentication
 * 
 * Establishes database connection and redirects to login if not logged in
 */

// =============================================
// 1. APPLICATION CONFIG
// =============================================
define('APP_URL', 'http://localhost/st&t_admin_panel/');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =============================================
// 2. DATABASE CONFIGURATION
// =============================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'sttadminpanel');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// =============================================
// 3. DATABASE CONNECTION
// =============================================
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// =============================================
// 4. AUTHENTICATION FUNCTIONS
// =============================================

// Check if user is logged in
function isLoggedIn()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Redirect to login if not logged in
function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: " . APP_URL . "login.php");
        exit();
    }
}

// Verify token from database - if no token or mismatch, logout
function verifyToken($pdo)
{
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_token'])) {
        logoutUser();
        return false;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT token FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        // If no user found or token is NULL or token doesn't match
        if (!$user || $user['token'] === null || $user['token'] !== $_SESSION['user_token']) {
            logoutUser();
            return false;
        }
        return true;
    } catch (PDOException $e) {
        logoutUser();
        return false;
    }
}

// Logout user - clear session and token from database
function logoutUser()
{
    // Clear token from database if user is logged in
    if (isset($_SESSION['user_id'])) {
        try {
            global $pdo;
            $stmt = $pdo->prepare("UPDATE users SET token = NULL WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
        } catch (PDOException $e) {
            // Silent fail
        }
    }
    
    // Clear session
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

// Get current user data
function getCurrentUser($pdo)
{
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

// Pages that don't require login
$publicPages = ['login.php', 'register.php', 'forgot-password.php'];

// Auto-check: if current page is not public, require login and verify token
$currentPage = basename($_SERVER['SCRIPT_NAME']);
if (!in_array($currentPage, $publicPages)) {
    requireLogin();
    // Verify token for authenticated pages
    if (!verifyToken($pdo)) {
        // Token invalid - already logged out by verifyToken
        header("Location: " . APP_URL . "login.php");
        exit();
    }
}

// =============================================
// 5. GET CURRENT USER DATA
// =============================================
$currentUser = null;
if (isLoggedIn()) {
    try {
        $stmt = $pdo->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $currentUser = $stmt->fetch();
    } catch (PDOException $e) {
        // Silent fail - user data not critical
    }
}

// Variables used in your existing code
$userId = $currentUser['id'] ?? 0;

// =============================================
// 6. CSRF TOKEN FUNCTIONS (Optional but recommended)
// =============================================

// Generate CSRF token
function generateCsrfToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCsrfToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>