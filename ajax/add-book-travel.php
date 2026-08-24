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

    // Helper function to generate random filename with format: {timestamp}_{unique_hash}_{index}.{extension}
    function generateRandomFilename($originalName, $index = 0) {
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);
        $timestamp = time();
        $uniqueHash = bin2hex(random_bytes(8));
        return $timestamp . '_' . $uniqueHash . '_' . $index . '.' . $ext;
    }

    // Helper function to create folder structure: {ID}/{YYYY-MM-DD}/
    function createBookingFolder($basePath, $bookingId = null) {
        $dateFolder = date('Y-m-d');
        $idFolder = $bookingId ? $bookingId : 'temp';
        $fullPath = $basePath . '/' . $idFolder . '/' . $dateFolder;
        
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0777, true);
        }
        return $fullPath;
    }

    // First, insert the booking to get the ID
    $stmt = $pdo->prepare("INSERT INTO travel_bookings (
        user_id, car_id, car_name, car_type, seat_count, days, 
        per_day_price, per_km_charge, total_price, total_distance,
        stops, what_we_provide, booking_date, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'pending')");

    $stopsJson = json_encode($stops, JSON_UNESCAPED_SLASHES);
    
    // Store whatWeProvide as simple array of strings (text only)
    $provideJson = json_encode($whatWeProvide, JSON_UNESCAPED_SLASHES);

    $stmt->execute([
        $userId,
        $carId,
        $carName,
        $carType,
        $seatCount,
        $days,
        $perDayPrice,
        $perKmCharge,
        $totalPrice,
        $totalDistance,
        $stopsJson,
        $provideJson
    ]);

    $bookingId = $pdo->lastInsertId();

    // Now handle vehicle image upload only
    $vehicleImagePath = null;

    // Create base upload folders
    $vehicleBaseFolder = '../uploads/travel-bookings/vehicles';
    
    if (!file_exists($vehicleBaseFolder)) {
        mkdir($vehicleBaseFolder, 0777, true);
    }

    // Handle vehicle image upload
    if (isset($_FILES['vehicle_image']) && $_FILES['vehicle_image']['error'] === UPLOAD_ERR_OK) {
        $vehicleFolder = createBookingFolder($vehicleBaseFolder, $bookingId);
        $originalName = $_FILES['vehicle_image']['name'];
        $randomName = generateRandomFilename($originalName, 0);
        $targetPath = $vehicleFolder . '/' . $randomName;
        
        if (move_uploaded_file($_FILES['vehicle_image']['tmp_name'], $targetPath)) {
            // Store relative path with booking ID and date
            $vehicleImagePath = 'uploads/travel-bookings/vehicles/' . $bookingId . '/' . date('Y-m-d') . '/' . $randomName;
        }
    }

    // Update the booking with vehicle image if uploaded
    if ($vehicleImagePath) {
        $updateStmt = $pdo->prepare("UPDATE travel_bookings SET vehicle_image = ? WHERE id = ?");
        $updateStmt->execute([$vehicleImagePath, $bookingId]);
    }

    // Get booking ID
    $stmt = $pdo->prepare("SELECT booking_id FROM travel_bookings WHERE id = ?");
    $stmt->execute([$bookingId]);
    $booking = $stmt->fetch();

    echo json_encode([
        'success' => true,
        'message' => 'Your travel has been booked successfully!',
        'booking_id' => $booking['booking_id'] ?? 'TRV' . str_pad($bookingId, 4, '0', STR_PAD_LEFT)
    ]);
} catch (PDOException $e) {
    error_log('Database error in add-book-travel.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log('Error in add-book-travel.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>