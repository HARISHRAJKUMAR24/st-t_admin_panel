<?php
// =============================================
// UPDATE SOCIAL SETTINGS - AJAX HANDLER
// =============================================

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON header
header('Content-Type: application/json');

// Include config and functions
require_once '../config/config.php';
require_once '../config/function.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized - Please login'
    ]);
    exit();
}

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit();
}

try {
    // Define all social platforms
    $socialPlatforms = [
        'facebook', 'twitter', 'instagram', 'youtube', 
        'linkedin', 'whatsapp', 'tiktok', 'snapchat', 
        'pinterest', 'reddit', 'telegram', 'discord'
    ];

    $socialLinks = [];

    // Collect all social links from POST
    foreach ($socialPlatforms as $platform) {
        $key = 'social_' . $platform;
        $value = trim($_POST[$key] ?? '');
        
        // Only store non-empty values
        if (!empty($value)) {
            $socialLinks[$platform] = $value;
        }
    }

    // Convert to JSON
    $socialLinksJson = !empty($socialLinks) ? json_encode($socialLinks, JSON_UNESCAPED_SLASHES) : null;

    // Check if settings table exists
    try {
        // Check if social_links column exists
        $stmt = $pdo->prepare("SHOW COLUMNS FROM settings LIKE 'social_links'");
        $stmt->execute();
        $columnExists = $stmt->fetch();
        
        if (!$columnExists) {
            // Add social_links column if not exists
            $pdo->exec("ALTER TABLE settings ADD COLUMN social_links LONGTEXT DEFAULT NULL");
        }
    } catch (PDOException $e) {
        // If settings table doesn't exist, create it
        $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
            id INT(11) NOT NULL DEFAULT 1,
            site_name VARCHAR(255) DEFAULT 'Tour Admin',
            contact_email VARCHAR(255) DEFAULT NULL,
            contact_phone VARCHAR(50) DEFAULT NULL,
            address TEXT DEFAULT NULL,
            currency VARCHAR(10) DEFAULT 'USD',
            social_links LONGTEXT DEFAULT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            timezone VARCHAR(50) DEFAULT 'Asia/Kolkata',
            site_title VARCHAR(100) DEFAULT 'Tour Admin Panel',
            website_logo VARCHAR(255) DEFAULT NULL,
            favicon VARCHAR(255) DEFAULT NULL,
            panel_logo VARCHAR(255) DEFAULT NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        
        // Insert default record
        $stmt = $pdo->prepare("INSERT INTO settings (id) VALUES (1) ON DUPLICATE KEY UPDATE id = id");
        $stmt->execute();
    }

    // Update social links
    $stmt = $pdo->prepare("UPDATE settings SET social_links = ? WHERE id = 1");
    $stmt->execute([$socialLinksJson]);

    echo json_encode([
        'success' => true,
        'message' => 'Social links updated successfully!'
    ]);

} catch (PDOException $e) {
    // Log error
    error_log('Database error in update-social-settings.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Database error occurred. Please try again.'
    ]);
} catch (Exception $e) {
    // Log error
    error_log('Error in update-social-settings.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>