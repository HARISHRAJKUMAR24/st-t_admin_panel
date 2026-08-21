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

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit();
    }

    // Get testimonial details
    $stmt = $pdo->prepare("SELECT * FROM testimonials WHERE id = ?");
    $stmt->execute([$id]);
    $testimonial = $stmt->fetch();

    if (!$testimonial) {
        echo json_encode(['success' => false, 'message' => 'Testimonial not found']);
        exit();
    }

    // Delete logo if exists
    if (!empty($testimonial['logo'])) {
        $fullPath = '../' . $testimonial['logo'];
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

    $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode([
        'success' => true,
        'message' => 'Testimonial deleted successfully!'
    ]);
} catch (PDOException $e) {
    error_log('Database error in delete-testimonial.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log('Error in delete-testimonial.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
