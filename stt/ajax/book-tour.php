<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$packageId    = isset($_POST['package_id'])    ? trim($_POST['package_id'])    : '';
$packageName  = isset($_POST['package_name'])  ? trim($_POST['package_name'])  : '';
$customerName = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
$mobileNumber = isset($_POST['mobile_number']) ? trim($_POST['mobile_number']) : '';

if (empty($packageId) || empty($customerName) || empty($mobileNumber)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if (strlen($customerName) < 2 || strlen($customerName) > 100) {
    echo json_encode(['success' => false, 'message' => 'Enter a valid name (2-100 characters).']);
    exit;
}

if (!preg_match('/^[0-9]{10}$/', $mobileNumber)) {
    echo json_encode(['success' => false, 'message' => 'Enter a valid 10-digit mobile number.']);
    exit;
}

try {
    // Generate unique booking ID
    $bookingId = 'TOUR-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    
    $stmt = $pdo->prepare("
        INSERT INTO customer_tour_bookings (booking_id, package_id, package_name, customer_name, mobile_number, status, created_at)
        VALUES (?, ?, ?, ?, ?, 'pending', NOW())
    ");
    $stmt->execute([$bookingId, $packageId, $packageName, $customerName, $mobileNumber]);

    echo json_encode([
        'success'    => true,
        'message'    => 'Booking submitted! We will contact you shortly.',
        'booking_id' => $bookingId
    ]);

} catch (PDOException $e) {
    // Log the error for debugging
    error_log("Booking error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Booking failed. Please try again or contact us directly.'
    ]);
}
?>