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

    if ($carId <= 0 || empty($carName) || $seatCount <= 0 || $days <= 0 || empty($stops)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
        exit();
    }

    // =============================================
    // UPLOAD PROVIDE ICONS
    // =============================================
    $uploadedIcons = [];
    if (isset($_FILES['provide_icons']) && !empty($_FILES['provide_icons']['name'][0])) {
        $iconFolder = createUploadFolder('../uploads', 'travel-bookings/provide-icons');
        if (!$iconFolder) {
            throw new Exception('Failed to create upload folder for icons');
        }
        
        $uploadedIcons = uploadMultipleImages($_FILES['provide_icons'], $iconFolder);
        
        if ($uploadedIcons) {
            $iconNames = $_POST['provide_icon_names'] ?? [];
            $iconPaths = [];
            foreach ($uploadedIcons as $index => $iconPath) {
                $iconPath = str_replace('../', '', $iconPath);
                $featureName = $iconNames[$index] ?? 'icon_' . $index;
                $iconPaths[$featureName] = $iconPath;
            }
            
            // Update what_we_provide with icon paths
            foreach ($whatWeProvide as &$item) {
                $itemName = $item['name'] ?? '';
                if (!empty($itemName) && isset($iconPaths[$itemName])) {
                    $item['icon'] = $iconPaths[$itemName];
                }
            }
            unset($item);
        }
    }

    $stmt = $pdo->prepare("INSERT INTO travel_bookings (
        user_id, car_id, car_name, car_type, seat_count, days, 
        per_day_price, per_km_charge, total_price, total_distance,
        stops, what_we_provide, booking_date, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'pending')");

    $stopsJson = json_encode($stops, JSON_UNESCAPED_SLASHES);
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