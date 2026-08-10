<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

require_once '../config/config.php';
require_once '../config/function.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    $siteTitle = trim($_POST['site_title'] ?? '');
    $footerText = trim($_POST['footer_text'] ?? '');
    $deleteHeroImage = intval($_POST['delete_hero_image'] ?? 0);

    if (empty($siteTitle)) {
        echo json_encode(['success' => false, 'message' => 'Site title is required']);
        exit();
    }

    // Get current settings
    $stmt = $pdo->prepare("SELECT hero_image FROM settings WHERE id = 1");
    $stmt->execute();
    $settings = $stmt->fetch();

    $heroImagePath = $settings['hero_image'] ?? null;

    // Delete hero image if requested
    if ($deleteHeroImage == 1 && $heroImagePath) {
        $fullPath = '../' . $heroImagePath;
        if (file_exists($fullPath)) {
            @unlink($fullPath);
            $dir = dirname($fullPath);
            if (is_dir($dir)) {
                $files = scandir($dir);
                if (count($files) <= 2) {
                    @rmdir($dir);
                    $parentDir = dirname($dir);
                    if (is_dir($parentDir) && count(scandir($parentDir)) <= 2) {
                        @rmdir($parentDir);
                    }
                }
            }
        }
        $heroImagePath = null;
    }

    // Upload new hero image
    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK) {
        if ($heroImagePath && $deleteHeroImage != 1) {
            $fullPath = '../' . $heroImagePath;
            if (file_exists($fullPath)) {
                @unlink($fullPath);
                $dir = dirname($fullPath);
                if (is_dir($dir) && count(scandir($dir)) <= 2) {
                    @rmdir($dir);
                    $parentDir = dirname($dir);
                    if (is_dir($parentDir) && count(scandir($parentDir)) <= 2) {
                        @rmdir($parentDir);
                    }
                }
            }
        }

        $uploadFolder = createUploadFolder('../uploads', 'settings/hero');
        if (!$uploadFolder) {
            throw new Exception('Failed to create upload folder');
        }
        
        $uploadedPath = uploadImage($_FILES['hero_image'], $uploadFolder);
        if ($uploadedPath) {
            $heroImagePath = str_replace('../', '', $uploadedPath);
        }
    }

    // Update database
    $stmt = $pdo->prepare("UPDATE settings SET 
        site_title = ?, 
        footer_text = ?, 
        hero_image = ?, 
        updated_at = CURRENT_TIMESTAMP
        WHERE id = 1");

    $stmt->execute([
        $siteTitle,
        $footerText,
        $heroImagePath
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Website settings updated successfully!'
    ]);

} catch (PDOException $e) {
    error_log('Database error in update-website-settings.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log('Error in update-website-settings.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>