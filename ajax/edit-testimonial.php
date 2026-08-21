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
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $testimonial = trim($_POST['testimonial'] ?? '');
    $status = trim($_POST['status'] ?? 'publish');
    $deleteLogo = intval($_POST['delete_logo'] ?? 0);

    if ($id <= 0 || empty($name) || empty($testimonial)) {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
        exit();
    }

    // Get existing testimonial
    $stmt = $pdo->prepare("SELECT * FROM testimonials WHERE id = ?");
    $stmt->execute([$id]);
    $existing = $stmt->fetch();

    if (!$existing) {
        echo json_encode(['success' => false, 'message' => 'Testimonial not found']);
        exit();
    }

    $logoPath = $existing['logo'];

    // Delete logo if requested
    if ($deleteLogo == 1 && $logoPath) {
        $fullPath = '../' . $logoPath;
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
        $logoPath = null;
    }

    // Upload new logo
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        // Delete old logo if exists
        if ($logoPath && $deleteLogo != 1) {
            $fullPath = '../' . $logoPath;
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

        $uploadFolder = createUploadFolder('../uploads', 'testimonials');
        if (!$uploadFolder) {
            throw new Exception('Failed to create upload folder');
        }
        $uploadedPath = uploadImage($_FILES['logo'], $uploadFolder);
        if ($uploadedPath) {
            $logoPath = str_replace('../', '', $uploadedPath);
        }
    }

    $stmt = $pdo->prepare("UPDATE testimonials SET name = ?, logo = ?, testimonial = ?, status = ? WHERE id = ?");
    $stmt->execute([$name, $logoPath, $testimonial, $status, $id]);

    echo json_encode([
        'success' => true,
        'message' => 'Testimonial updated successfully!'
    ]);

} catch (PDOException $e) {
    error_log('Database error in update-testimonial.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log('Error in update-testimonial.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>