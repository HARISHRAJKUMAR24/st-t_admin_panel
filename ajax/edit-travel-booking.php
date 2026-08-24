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
    $carId = intval($_POST['car_id'] ?? 0);
    $carName = trim($_POST['car_name'] ?? '');
    $carType = trim($_POST['car_type'] ?? '');
    $seatCount = intval($_POST['seat_count'] ?? 0);
    $days = intval($_POST['days'] ?? 1);
    $perDayPrice = floatval($_POST['per_day_price'] ?? 0);
    $perKmCharge = floatval($_POST['per_km_charge'] ?? 0);
    $totalPrice = floatval($_POST['total_price'] ?? 0);
    $totalDistance = floatval($_POST['total_distance'] ?? 0);
    $stops = json_decode($_POST['stops'] ?? '[]', true);
    $whatWeProvide = json_decode($_POST['what_we_provide'] ?? '[]', true);
    $userId = $_SESSION['user_id'];

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
        exit();
    }

    // Get existing booking data
    $stmt = $pdo->prepare("SELECT * FROM travel_bookings WHERE id = ?");
    $stmt->execute([$id]);
    $existingBooking = $stmt->fetch();

    if (!$existingBooking) {
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        exit();
    }

    // Validate
    if (empty($carName) || $seatCount <= 0 || $days <= 0 || empty($stops)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
        exit();
    }

    if ($perDayPrice <= 0 || $perKmCharge <= 0) {
        echo json_encode(['success' => false, 'message' => 'Please enter valid pricing details']);
        exit();
    }

    foreach ($stops as $stop) {
        if (empty($stop['pickup']) || empty($stop['drop'])) {
            echo json_encode(['success' => false, 'message' => 'Please fill all pickup and drop locations']);
            exit();
        }
        if (empty($stop['distance']) || $stop['distance'] <= 0) {
            echo json_encode(['success' => false, 'message' => 'Please enter distance for all stops']);
            exit();
        }
    }

    // Update database - what_we_provide is now a simple array of strings
    $stmt = $pdo->prepare("UPDATE travel_bookings SET 
        car_id = ?, car_name = ?, car_type = ?, seat_count = ?, 
        days = ?, per_day_price = ?, per_km_charge = ?, 
        total_price = ?, total_distance = ?, stops = ?, 
        what_we_provide = ? WHERE id = ?");

    $stmt->execute([
        $carId,
        $carName,
        $carType,
        $seatCount,
        $days,
        $perDayPrice,
        $perKmCharge,
        $totalPrice,
        $totalDistance,
        json_encode($stops, JSON_UNESCAPED_SLASHES),
        json_encode($whatWeProvide, JSON_UNESCAPED_SLASHES),
        $id
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Travel booking updated successfully!'
    ]);
} catch (PDOException $e) {
    error_log('Database error in edit-travel-booking.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log('Error in edit-travel-booking.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>