<?php
// =============================================
// AJAX ADD CAR RENTAL HANDLER
// =============================================

// Set JSON header
header('Content-Type: application/json');

// Include config and functions
require_once '../config/config.php';
requireLogin();
require_once '../config/function.php';

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit();
}

try {
    // Get form data
    $carName = isset($_POST['car_name']) ? trim($_POST['car_name']) : '';
    $carModel = isset($_POST['car_model']) ? trim($_POST['car_model']) : '';
    $carBrand = isset($_POST['car_brand']) ? trim($_POST['car_brand']) : '';
    $carType = isset($_POST['car_type']) ? trim($_POST['car_type']) : '';
    $perDayAmount = isset($_POST['per_day_amount']) ? floatval($_POST['per_day_amount']) : 0;
    $perKmCharge = isset($_POST['per_km_charge']) ? floatval($_POST['per_km_charge']) : 0;
    $fuelType = isset($_POST['fuel_type']) ? trim($_POST['fuel_type']) : '';
    $transmission = isset($_POST['transmission']) ? trim($_POST['transmission']) : '';
    $seatingCapacity = isset($_POST['seating_capacity']) ? intval($_POST['seating_capacity']) : 0;
    $acAvailable = isset($_POST['ac_available']) ? intval($_POST['ac_available']) : 1;
    $status = isset($_POST['status']) ? trim($_POST['status']) : 'available';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';

    // Validate required fields
    if (empty($carName) || $perDayAmount <= 0 || $perKmCharge <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Please fill in all required fields'
        ]);
        exit();
    }

    // Handle main image upload
    $mainImagePath = '';
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        // Create upload folder using function - relative to ajax folder
        $uploadFolder = createUploadFolder('../uploads', 'car-rental');
        if (!$uploadFolder) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create upload folder'
            ]);
            exit();
        }

        // Upload main image
        $mainImagePath = uploadImage($_FILES['main_image'], $uploadFolder);
        if (!$mainImagePath) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to upload main image'
            ]);
            exit();
        }
        // Get relative path from uploads folder (remove '../' from path)
        $mainImagePath = str_replace('../', '', $mainImagePath);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Main image is required'
        ]);
        exit();
    }

    // Handle additional images
    $additionalImages = [];
    if (isset($_FILES['additional_images']) && !empty($_FILES['additional_images']['name'][0])) {
        // Create additional images folder
        $additionalFolder = $uploadFolder . '/additional';
        if (!file_exists($additionalFolder)) {
            mkdir($additionalFolder, 0777, true);
        }

        $uploadedAdditional = uploadMultipleImages($_FILES['additional_images'], $additionalFolder);
        if ($uploadedAdditional) {
            foreach ($uploadedAdditional as $img) {
                // Remove '../' from path
                $additionalImages[] = str_replace('../', '', $img);
            }
        }
    }

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO car_rentals 
        (car_name, car_model, car_brand, car_type, car_image, additional_images, 
         per_day_amount, per_km_charge, fuel_type, transmission, 
         seating_capacity, ac_available, description, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $additionalImagesJson = !empty($additionalImages) ? json_encode($additionalImages) : null;

    $stmt->execute([
        $carName,
        $carModel,
        $carBrand,
        $carType,
        $mainImagePath,
        $additionalImagesJson,
        $perDayAmount,
        $perKmCharge,
        $fuelType,
        $transmission,
        $seatingCapacity,
        $acAvailable,
        $description,
        $status
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Car rental added successfully!',
        'id' => $pdo->lastInsertId()
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
