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
    $status = trim($_POST['status'] ?? '');

    if ($id <= 0 || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
        exit();
    }

    if (!in_array($status, ['publish', 'unpublish'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit();
    }

    $stmt = $pdo->prepare("UPDATE testimonials SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    echo json_encode([
        'success' => true,
        'message' => 'Status updated successfully!'
    ]);

} catch (PDOException $e) {
    error_log('Database error in toggle-testimonial-status.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log('Error in toggle-testimonial-status.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>