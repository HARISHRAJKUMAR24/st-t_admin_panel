<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['found' => false]);
    exit;
}

$mobileNumber = isset($_POST['mobile_number']) ? trim($_POST['mobile_number']) : '';

if (empty($mobileNumber) || !preg_match('/^[0-9]{10}$/', $mobileNumber)) {
    echo json_encode(['found' => false]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT customer_name
        FROM customer_tour_bookings
        WHERE mobile_number = ?
        ORDER BY created_at DESC
        LIMIT 1
    ");
    $stmt->execute([$mobileNumber]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && !empty($row['customer_name'])) {
        echo json_encode([
            'found' => true,
            'name'  => $row['customer_name']
        ]);
    } else {
        echo json_encode(['found' => false]);
    }

} catch (PDOException $e) {
    echo json_encode(['found' => false]);
}
?>