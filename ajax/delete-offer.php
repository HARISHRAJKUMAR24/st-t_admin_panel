<?php
// =============================================
// DELETE OFFER - AJAX HANDLER
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
        echo json_encode(['success' => false, 'message' => 'Invalid offer ID']);
        exit();
    }

    // Get offer details
    $stmt = $pdo->prepare("SELECT * FROM offers WHERE id = ?");
    $stmt->execute([$id]);
    $offer = $stmt->fetch();

    if (!$offer) {
        echo json_encode(['success' => false, 'message' => 'Offer not found']);
        exit();
    }

    // Start transaction
    $pdo->beginTransaction();

    // Delete main image
    if (!empty($offer['main_image'])) {
        $path = '../' . $offer['main_image'];
        $path = str_replace('\\', '/', $path);
        if (file_exists($path)) {
            @unlink($path);
        }
        // Delete folder if empty
        $dir = dirname($path);
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

    // Delete from database
    $stmt = $pdo->prepare("DELETE FROM offers WHERE id = ?");
    $stmt->execute([$id]);

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Offer deleted successfully!'
    ]);
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Database error in delete-offer.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Error in delete-offer.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
