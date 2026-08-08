<?php
// =============================================
// DELETE LOGO - AJAX HANDLER
// =============================================

// Disable error display
error_reporting(0);
ini_set('display_errors', 0);

// Set JSON header
header('Content-Type: application/json');

// Include config and functions
require_once '../config/config.php';
requireLogin();
require_once '../config/function.php';

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
    $deleteType = $_POST['delete_type'] ?? '';

    // Validate delete type
    if (!in_array($deleteType, ['website_logo', 'favicon', 'panel_logo'])) {
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid delete type'
        ]);
        exit();
    }

    // Get current settings
    $stmt = $pdo->prepare("SELECT $deleteType FROM settings WHERE id = 1");
    $stmt->execute();
    $settings = $stmt->fetch();

    if (empty($settings[$deleteType])) {
        echo json_encode([
            'success' => false, 
            'message' => 'No logo found to delete'
        ]);
        exit();
    }

    // Get the file path
    $filePath = '../' . $settings[$deleteType];
    $filePath = str_replace('\\', '/', $filePath);
    
    // Get folder structure
    // Example: ../uploads/settings/logo/123456/2024-01-15/image.jpg
    // We need to delete: ../uploads/settings/logo/123456/2024-01-15/
    // Then delete: ../uploads/settings/logo/123456/
    // Then delete: ../uploads/settings/logo/ (if empty)
    
    $fileDir = dirname($filePath); // ../uploads/settings/logo/123456/2024-01-15
    $parentDir = dirname($fileDir); // ../uploads/settings/logo/123456
    $grandParentDir = dirname($parentDir); // ../uploads/settings/logo
    
    // Delete the image file
    if (file_exists($filePath)) {
        @unlink($filePath);
    }

    // Delete the date folder (2024-01-15)
    if (is_dir($fileDir)) {
        $files = scandir($fileDir);
        $files = array_diff($files, ['.', '..']);
        
        // If folder is empty or only has the image we deleted
        if (empty($files)) {
            @rmdir($fileDir);
        } else {
            // If there are other files, try to delete them too
            foreach ($files as $file) {
                $subFilePath = $fileDir . '/' . $file;
                if (is_file($subFilePath)) {
                    @unlink($subFilePath);
                }
            }
            // Try to remove the folder again
            @rmdir($fileDir);
        }
    }

    // Delete the number folder (123456)
    if (is_dir($parentDir)) {
        $files = scandir($parentDir);
        $files = array_diff($files, ['.', '..']);
        
        // If folder is empty, delete it
        if (empty($files)) {
            @rmdir($parentDir);
        }
    }

    // Delete the type folder (logo, favicon, panel-logo)
    if (is_dir($grandParentDir)) {
        $files = scandir($grandParentDir);
        $files = array_diff($files, ['.', '..']);
        
        // If folder is empty, delete it
        if (empty($files)) {
            @rmdir($grandParentDir);
        }
    }

    // Delete the settings folder if empty
    $settingsDir = dirname($grandParentDir); // ../uploads/settings
    if (is_dir($settingsDir)) {
        $files = scandir($settingsDir);
        $files = array_diff($files, ['.', '..']);
        
        // If folder is empty, delete it
        if (empty($files)) {
            @rmdir($settingsDir);
        }
    }

    // Update database
    $stmt = $pdo->prepare("UPDATE settings SET $deleteType = NULL WHERE id = 1");
    $stmt->execute();

    echo json_encode([
        'success' => true, 
        'message' => 'Logo removed successfully'
    ]);

} catch (PDOException $e) {
    error_log('Database error in delete-logo.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Database error occurred'
    ]);
} catch (Exception $e) {
    error_log('Error in delete-logo.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred'
    ]);
}
?>