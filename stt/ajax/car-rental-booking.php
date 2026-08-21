<?php

require_once '../config/config.php';

header('Content-Type: application/json; charset=utf-8');


/* =========================================================
   ONLY POST REQUESTS
========================================================= */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);

    exit;
}


/* =========================================================
   GET FORM DATA
========================================================= */

$carId = isset($_POST['car_id'])
    ? (int)$_POST['car_id']
    : 0;

$customerName = trim(
    $_POST['customer_name'] ?? ''
);

$mobile = trim(
    $_POST['mobile'] ?? ''
);


/* =========================================================
   VALIDATION
========================================================= */

if ($carId <= 0) {

    echo json_encode([
        'success' => false,
        'message' => 'Invalid car selected.'
    ]);

    exit;
}


if ($customerName === '') {

    echo json_encode([
        'success' => false,
        'message' => 'Please enter your name.'
    ]);

    exit;
}


if ($mobile === '') {

    echo json_encode([
        'success' => false,
        'message' => 'Please enter your mobile number.'
    ]);

    exit;
}


/* =========================================================
   MOBILE VALIDATION
========================================================= */

if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $mobile)) {

    echo json_encode([
        'success' => false,
        'message' => 'Please enter a valid mobile number.'
    ]);

    exit;
}


/* =========================================================
   VERIFY CAR
========================================================= */

try {

    $stmt = $pdo->prepare("
        SELECT
            id,
            car_name
        FROM car_rentals
        WHERE id = :id
          AND status = 'available'
        LIMIT 1
    ");

    $stmt->execute([
        ':id' => $carId
    ]);

    $car = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$car) {

        echo json_encode([
            'success' => false,
            'message' => 'This car is currently unavailable.'
        ]);

        exit;
    }


    /* =====================================================
       INSERT BOOKING
    ===================================================== */

    $insert = $pdo->prepare("
        INSERT INTO customer_car_rentals_bookings
        (
            car_id,
            car_name,
            customer_name,
            mobile,
            status,
            created_at,
            updated_at
        )
        VALUES
        (
            :car_id,
            :car_name,
            :customer_name,
            :mobile,
            'pending',
            NOW(),
            NOW()
        )
    ");


    $insert->execute([

        ':car_id' =>
            (int)$car['id'],

        ':car_name' =>
            $car['car_name'],

        ':customer_name' =>
            $customerName,

        ':mobile' =>
            $mobile

    ]);


    /* =====================================================
       SUCCESS
    ===================================================== */

    echo json_encode([

        'success' => true,

        'message' =>
            'Your car rental request has been submitted successfully.'

    ]);

    exit;


} catch (PDOException $e) {

    error_log(
        'Car rental booking error: ' .
        $e->getMessage()
    );


    http_response_code(500);

    echo json_encode([

        'success' => false,

        'message' =>
            'Unable to submit your booking right now. Please try again.'

    ]);

    exit;
}