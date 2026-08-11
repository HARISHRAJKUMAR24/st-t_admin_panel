<?php
// =============================================
// DELETE PACKAGE TYPE - AJAX HANDLER
// =============================================

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
    $id = intval($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit();
    }

    // Get type details
    $stmt = $pdo->prepare("SELECT * FROM package_type_images WHERE id = ?");
    $stmt->execute([$id]);
    $type = $stmt->fetch();

    if (!$type) {
        echo json_encode(['success' => false, 'message' => 'Type not found']);
        exit();
    }

    // Delete image if exists
    if (!empty($type['image'])) {
        $fullPath = '../' . $type['image'];
        $fullPath = str_replace('\\', '/', $fullPath);
        
        if (file_exists($fullPath)) {
            @unlink($fullPath);
            // Delete empty folders
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
    }

    // Delete from database
    $stmt = $pdo->prepare("DELETE FROM package_type_images WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode([
        'success' => true,
        'message' => 'Package type deleted successfully!'
    ]);

} catch (PDOException $e) {
    error_log('Database error in delete-package-type.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log('Error in delete-package-type.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>