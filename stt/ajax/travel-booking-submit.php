<?php
require_once '../config/config.php';

// Ensure we return JSON
header('Content-Type: application/json; charset=utf-8');

// Start session
session_start();

/*
|--------------------------------------------------------------------------
| LOGIN CHECK
|--------------------------------------------------------------------------
*/

if (empty($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Please login to book a travel package.'
    ]);
    exit;
}

$userId = (int) $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| CUSTOMER FORM DATA
|--------------------------------------------------------------------------
*/

$packageId = (int) ($_POST['package_id'] ?? 0);
$customerName = trim($_POST['name'] ?? '');
$customerPhone = trim($_POST['phone'] ?? '');
$pickup = trim($_POST['pickup'] ?? '');
$destination = trim($_POST['destination'] ?? '');
$travelDate = trim($_POST['travel_date'] ?? '');

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/

if ($packageId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid travel package ID.'
    ]);
    exit;
}

if ($customerName === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Customer name is required.'
    ]);
    exit;
}

if ($customerPhone === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Customer phone is required.'
    ]);
    exit;
}

if (strlen($customerPhone) < 7) {
    echo json_encode([
        'success' => false,
        'message' => 'Please enter a valid phone number.'
    ]);
    exit;
}

if ($pickup === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Pickup location is required.'
    ]);
    exit;
}

if ($destination === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Destination is required.'
    ]);
    exit;
}

if ($travelDate === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Travel date is required.'
    ]);
    exit;
}

try {
    /*
    |--------------------------------------------------------------------------
    | GET PACKAGE FROM travel_bookings
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT
            id,
            car_id,
            car_name,
            car_type,
            seat_count,
            days,
            per_day_price,
            per_km_charge,
            total_price,
            total_distance,
            stops,
            what_we_provide,
            status
        FROM travel_bookings
        WHERE id = :package_id
        LIMIT 1
    ");

    $stmt->execute([':package_id' => $packageId]);
    $package = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$package) {
        echo json_encode([
            'success' => false,
            'message' => 'Selected travel package was not found.'
        ]);
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE CUSTOMER BOOKING ID
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->query("
        SELECT booking_id
        FROM customer_travel_bookings
        WHERE booking_id LIKE 'TRV%'
        ORDER BY id DESC
        LIMIT 1
    ");

    $lastBooking = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($lastBooking && !empty($lastBooking['booking_id'])) {
        $lastNumber = (int) preg_replace('/[^0-9]/', '', $lastBooking['booking_id']);
        $nextNumber = $lastNumber + 1;
    } else {
        $nextNumber = 1;
    }

    $bookingId = 'TRV' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

    /*
    |--------------------------------------------------------------------------
    | CUSTOMER ROUTE
    |--------------------------------------------------------------------------
    */

    $stops = [
        [
            'pickup' => $pickup,
            'drop' => $destination
        ]
    ];

    $stopsJson = json_encode($stops, JSON_UNESCAPED_UNICODE);

    /*
    |--------------------------------------------------------------------------
    | PACKAGE DATA
    |--------------------------------------------------------------------------
    */

    $carId = (int) ($package['car_id'] ?? 0);
    $carName = trim($package['car_name'] ?? '');

    /*
    |--------------------------------------------------------------------------
    | CAR TYPE
    |--------------------------------------------------------------------------
    */

    $carType = $package['car_type'] ?? '';
    if (is_string($carType)) {
        $decodedCarType = json_decode($carType, true);
        if (is_array($decodedCarType)) {
            $carType = implode(', ', $decodedCarType);
        }
    }
    $carType = trim((string) $carType);

    $seatCount = (int) ($package['seat_count'] ?? 0);
    $days = (int) ($package['days'] ?? 1);
    $perDayPrice = (float) ($package['per_day_price'] ?? 0);
    $perKmCharge = (float) ($package['per_km_charge'] ?? 0);
    $totalPrice = (float) ($package['total_price'] ?? 0);
    $totalDistance = $package['total_distance'] !== null ? (float) $package['total_distance'] : null;
    $whatWeProvide = $package['what_we_provide'] ?? null;

    /*
    |--------------------------------------------------------------------------
    | INSERT CUSTOMER BOOKING
    |--------------------------------------------------------------------------
    */

    $sql = "
        INSERT INTO customer_travel_bookings (
            booking_id,
            user_id,
            customer_name,
            customer_phone,
            travel_date,
            package_id,
            car_id,
            car_name,
            car_type,
            seat_count,
            days,
            per_day_price,
            per_km_charge,
            total_price,
            total_distance,
            stops,
            what_we_provide,
            status,
            booking_date
        )
        VALUES (
            :booking_id,
            :user_id,
            :customer_name,
            :customer_phone,
            :travel_date,
            :package_id,
            :car_id,
            :car_name,
            :car_type,
            :seat_count,
            :days,
            :per_day_price,
            :per_km_charge,
            :total_price,
            :total_distance,
            :stops,
            :what_we_provide,
            'pending',
            NOW()
        )
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':booking_id' => $bookingId,
        ':user_id' => $userId,
        ':customer_name' => $customerName,
        ':customer_phone' => $customerPhone,
        ':travel_date' => $travelDate,
        ':package_id' => $packageId,
        ':car_id' => $carId,
        ':car_name' => $carName,
        ':car_type' => $carType,
        ':seat_count' => $seatCount,
        ':days' => $days,
        ':per_day_price' => $perDayPrice,
        ':per_km_charge' => $perKmCharge,
        ':total_price' => $totalPrice,
        ':total_distance' => $totalDistance,
        ':stops' => $stopsJson,
        ':what_we_provide' => $whatWeProvide
    ]);

    /*
    |--------------------------------------------------------------------------
    | SUCCESS RESPONSE
    |--------------------------------------------------------------------------
    */

    echo json_encode([
        'success' => true,
        'booking_id' => $bookingId,
        'message' => 'Booking submitted successfully!'
    ]);
    exit;
} catch (PDOException $e) {
    error_log('Travel booking error: ' . $e->getMessage());

    echo json_encode([
        'success' => false,
        'message' => 'Unable to submit your booking right now. Please try again.'
    ]);
    exit;
}
