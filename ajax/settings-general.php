<?php
require_once '../config/config.php';
requireLogin();
require_once '../config/function.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    // Get form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $timezone = trim($_POST['timezone'] ?? 'Asia/Kolkata');
    $userId = $_SESSION['user_id'];

    // Validate
    if (empty($name) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Name and email are required']);
        exit();
    }

    if (empty($timezone)) {
        echo json_encode(['success' => false, 'message' => 'Timezone is required']);
        exit();
    }

    // Check if email exists for other users
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $userId]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        exit();
    }

    // Start transaction
    $pdo->beginTransaction();

    // Update user profile
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
    $stmt->execute([$name, $email, $phone, $userId]);

    // Update timezone
    $stmt = $pdo->prepare("UPDATE settings SET timezone = ? WHERE id = 1");
    $stmt->execute([$timezone]);

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

    $uploadedFiles = [];

    // Process each upload type
    foreach ($uploadTypes as $type => $config) {
        if (isset($_FILES[$type]) && $_FILES[$type]['error'] === UPLOAD_ERR_OK) {
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
        $stmt = $pdo->prepare("UPDATE settings SET $key = ? WHERE id = 1");
        $stmt->execute([$value]);
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'All settings updated successfully!'
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>