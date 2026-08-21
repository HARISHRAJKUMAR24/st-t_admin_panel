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
    $table = trim($_POST['table'] ?? '');
    $status = trim($_POST['status'] ?? '');

    if ($id <= 0 || empty($table) || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
        exit();
    }

    // Validate table name
    $allowedTables = [
        'customer_tour_bookings',
        'customer_travel_bookings',
        'customer_car_rentals_bookings',
        'car_rentals',
        'cta_messages'
    ];

    if (!in_array($table, $allowedTables)) {
        echo json_encode(['success' => false, 'message' => 'Invalid table']);
        exit();
    }

    // Check if record exists
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
    $stmt->execute([$id]);
    $record = $stmt->fetch();

    if (!$record) {
        echo json_encode(['success' => false, 'message' => 'Record not found']);
        exit();
    }

    // Update status
    $stmt = $pdo->prepare("UPDATE $table SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    echo json_encode([
        'success' => true,
        'message' => 'Status updated successfully!'
    ]);

} catch (PDOException $e) {
    error_log('Database error in update-booking-status.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log('Error in update-booking-status.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>