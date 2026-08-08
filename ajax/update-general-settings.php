<?php
// =============================================
// UPDATE GENERAL SETTINGS - AJAX HANDLER
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
    // Get form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $timezone = trim($_POST['timezone'] ?? 'Asia/Kolkata');
    $userId = $_SESSION['user_id'];

    // Validate
    if (empty($name) || empty($email)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Name and email are required'
        ]);
        exit();
    }

    if (empty($timezone)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Timezone is required'
        ]);
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid email format'
        ]);
        exit();
    }

    // Check if email exists for other users
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $userId]);
    if ($stmt->fetch()) {
        echo json_encode([
            'success' => false, 
            'message' => 'Email already exists'
        ]);
        exit();
    }

    // Start transaction
    $pdo->beginTransaction();

    // Update user profile
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
    $stmt->execute([$name, $email, $phone, $userId]);

    // Update settings (timezone and address)
    try {
        // Check if address column exists
        $stmt = $pdo->prepare("SHOW COLUMNS FROM settings LIKE 'address'");
        $stmt->execute();
        $columnExists = $stmt->fetch();
        
        if ($columnExists) {
            // Update with address
            $stmt = $pdo->prepare("UPDATE settings SET timezone = ?, address = ? WHERE id = 1");
            $stmt->execute([$timezone, $address]);
        } else {
            // Update without address
            $stmt = $pdo->prepare("UPDATE settings SET timezone = ? WHERE id = 1");
            $stmt->execute([$timezone]);
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
        $stmt = $pdo->prepare("INSERT INTO settings (id, timezone, address) VALUES (1, ?, ?) ON DUPLICATE KEY UPDATE timezone = ?, address = ?");
        $stmt->execute([$timezone, $address, $timezone, $address]);
    }

    // Update session timezone
    $_SESSION['timezone'] = $timezone;
    date_default_timezone_set($timezone);

    // Handle logo uploads
    $uploadTypes = [
        'website_logo' => ['field' => 'website_logo', 'folder' => 'logo'],
        'favicon' => ['field' => 'favicon', 'folder' => 'favicon'],
        'panel_logo' => ['field' => 'panel_logo', 'folder' => 'panel-logo']
    ];

    // Get existing settings
    $stmt = $pdo->prepare("SELECT * FROM settings WHERE id = 1");
    $stmt->execute();
    $settings = $stmt->fetch();

    if (!$settings) {
        $settings = ['id' => 1, 'website_logo' => null, 'favicon' => null, 'panel_logo' => null];
    }

    $uploadedFiles = [];

    // Process each upload type
    foreach ($uploadTypes as $type => $config) {
        if (isset($_FILES[$type]) && $_FILES[$type]['error'] === UPLOAD_ERR_OK) {
            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml', 'image/x-icon'];
            if (!in_array($_FILES[$type]['type'], $allowedTypes)) {
                throw new Exception('Invalid file type for ' . $type . '. Allowed: JPG, PNG, GIF, WebP, SVG, ICO');
            }

            // Validate file size (max 2MB)
            if ($_FILES[$type]['size'] > 2 * 1024 * 1024) {
                throw new Exception('File too large for ' . $type . '. Max size: 2MB');
            }

            // Delete old file if exists
            if (!empty($settings[$type])) {
                $oldPath = '../' . $settings[$type];
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
                // Delete folder if empty
                $oldDir = dirname($oldPath);
                if (is_dir($oldDir)) {
                    $files = scandir($oldDir);
                    if (count($files) <= 2) {
                        @rmdir($oldDir);
                    }
                }
            }

            // Create upload folder
            $uploadFolder = createUploadFolder('../uploads', 'settings/' . $config['folder']);
            if (!$uploadFolder) {
                throw new Exception('Failed to create upload folder for ' . $type);
            }

            // Upload file
            $uploadedPath = uploadImage($_FILES[$type], $uploadFolder);
            if (!$uploadedPath) {
                throw new Exception('Failed to upload ' . $type);
            }

            $uploadedFiles[$type] = str_replace('../', '', $uploadedPath);
        }
    }

    // Update logo paths in database
    foreach ($uploadedFiles as $key => $value) {
        try {
            $stmt = $pdo->prepare("UPDATE settings SET $key = ? WHERE id = 1");
            $stmt->execute([$value]);
        } catch (PDOException $e) {
            // If column doesn't exist, add it
            $pdo->exec("ALTER TABLE settings ADD COLUMN $key VARCHAR(255) DEFAULT NULL");
            $stmt = $pdo->prepare("UPDATE settings SET $key = ? WHERE id = 1");
            $stmt->execute([$value]);
        }
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'All settings updated successfully!'
    ]);

} catch (PDOException $e) {
    // Rollback transaction on error
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Log error
    error_log('Database error in update-general-settings.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Database error occurred. Please try again.'
    ]);
} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Log error
    error_log('Error in update-general-settings.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>