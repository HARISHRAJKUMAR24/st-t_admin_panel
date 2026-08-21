<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'sttadminpanel');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site URL - This is the frontend site URL
define('SITE_URL', 'http://localhost/stt/');

// Admin Panel URL - Where images are uploaded
define('ADMIN_URL', 'http://localhost/st&t_admin_panel/');

// Base upload path (relative to admin panel)
define('UPLOAD_PATH', 'uploads/');

// Create PDO connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Load functions
require_once __DIR__ . '/function.php';
?>