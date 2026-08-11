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

    // Get current hero image
    $stmt = $pdo->prepare("SELECT hero_image FROM settings WHERE id = 1");
    $stmt->execute();
    $settings = $stmt->fetch();

    $heroImagePath = $settings['hero_image'] ?? null;

    // =============================================
    // DELETE OLD HERO IMAGE AND FOLDERS
    // =============================================
    
    // Check if we're uploading a new image
    $isUploadingNew = isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK;
    
    // If deleting or uploading new, remove old image and folders
    if (($deleteHeroImage == 1 || $isUploadingNew) && $heroImagePath) {
        $fullPath = '../' . $heroImagePath;
        $fullPath = str_replace('\\', '/', $fullPath);
        
        // Delete the image file
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
        
        // Get folder structure
        // Example: ../uploads/settings/hero/123456/2024-01-15/image.jpg
        $dateFolder = dirname($fullPath);      // ../uploads/settings/hero/123456/2024-01-15
        $numberFolder = dirname($dateFolder);   // ../uploads/settings/hero/123456
        $typeFolder = dirname($numberFolder);   // ../uploads/settings/hero
        
        // Delete date folder (2024-01-15)
        if (is_dir($dateFolder)) {
            $files = scandir($dateFolder);
            $files = array_diff($files, ['.', '..']);
            // Delete any remaining files in date folder
            foreach ($files as $file) {
                $filePath = $dateFolder . '/' . $file;
                if (is_file($filePath)) {
                    @unlink($filePath);
                }
            }
            @rmdir($dateFolder);
        }
        
        // Delete number folder (123456)
        if (is_dir($numberFolder)) {
            $files = scandir($numberFolder);
            $files = array_diff($files, ['.', '..']);
            if (empty($files)) {
                @rmdir($numberFolder);
            }
        }
        
        // Delete type folder (hero) if empty
        if (is_dir($typeFolder)) {
            $files = scandir($typeFolder);
            $files = array_diff($files, ['.', '..']);
            if (empty($files)) {
                @rmdir($typeFolder);
            }
        }
        
        // Delete settings folder if empty
        $settingsFolder = dirname($typeFolder); // ../uploads/settings
        if (is_dir($settingsFolder)) {
            $files = scandir($settingsFolder);
            $files = array_diff($files, ['.', '..']);
            if (empty($files)) {
                @rmdir($settingsFolder);
            }
        }
        
        $heroImagePath = null;
    }

    // =============================================
    // UPLOAD NEW HERO IMAGE
    // =============================================
    if ($isUploadingNew) {
        // Create upload folder
        $uploadFolder = createUploadFolder('../uploads', 'settings/hero');
        if (!$uploadFolder) {
            throw new Exception('Failed to create upload folder');
        }
        
        // Get file info
        $file = $_FILES['hero_image'];
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $fullPath = $uploadFolder . '/' . $fileName;
        $fullPath = str_replace('\\', '/', $fullPath);
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $fullPath)) {
            $heroImagePath = str_replace('../', '', $fullPath);
        } else {
            throw new Exception('Failed to upload image');
        }
    }

    // =============================================
    // UPDATE DATABASE
    // =============================================
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
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
} catch (Exception $e) {
    error_log('Error in update-website-settings.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>