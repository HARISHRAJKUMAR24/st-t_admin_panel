<?php

require_once '../config/config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$customerName = trim($_POST['customer_name'] ?? '');
$mobile = trim($_POST['mobile'] ?? '');

if ($customerName === '') {
    echo json_encode(['success' => false, 'message' => 'Please enter your name.']);
    exit;
}

if ($mobile === '') {
    echo json_encode(['success' => false, 'message' => 'Please enter your mobile number.']);
    exit;
}

if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $mobile)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid mobile number.']);
    exit;
}

try {
    /* INSERT INTO customer_boat_bookings (Just Name, Mobile, and Time) */
    $insert = $pdo->prepare("
        INSERT INTO customer_boat_bookings
        (
            customer_name,
            mobile,
            created_at
        )
        VALUES
        (
            :customer_name,
            :mobile,
            NOW()
        )
    ");

    $insert->execute([
        ':customer_name' => $customerName,
        ':mobile'        => $mobile
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Your boat booking request has been submitted successfully!'
    ]);
    exit;
} catch (PDOException $e) {
    error_log('Boat booking error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unable to submit your booking right now. Please try again.'
    ]);
    exit;
}
