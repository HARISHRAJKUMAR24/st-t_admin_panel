<?php
// =============================================
// AJAX ADD VEHICLE HANDLER
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
    $vehicleName = isset($_POST['vehicle_name']) ? trim($_POST['vehicle_name']) : '';
    $vehicleModel = isset($_POST['vehicle_model']) ? trim($_POST['vehicle_model']) : '';
    $vehicleBrand = isset($_POST['vehicle_brand']) ? trim($_POST['vehicle_brand']) : '';
    $vehicleTypesJson = isset($_POST['vehicle_types']) ? $_POST['vehicle_types'] : '[]';
    $pricingType = isset($_POST['pricing_type']) ? trim($_POST['pricing_type']) : 'perday';

    // Pricing fields
    $perDayAmount = isset($_POST['per_day_amount']) && !empty($_POST['per_day_amount']) ? floatval($_POST['per_day_amount']) : 0;
    $perKmCharge = isset($_POST['per_km_charge']) && !empty($_POST['per_km_charge']) ? floatval($_POST['per_km_charge']) : 0;
    $packageDays = isset($_POST['package_days']) && !empty($_POST['package_days']) ? intval($_POST['package_days']) : null;
    $packagePrice = isset($_POST['package_price']) && !empty($_POST['package_price']) ? floatval($_POST['package_price']) : null;
    $packageKmLimit = isset($_POST['package_km_limit']) && !empty($_POST['package_km_limit']) ? intval($_POST['package_km_limit']) : null;
    $extraKmCharge = isset($_POST['extra_km_charge']) && !empty($_POST['extra_km_charge']) ? floatval($_POST['extra_km_charge']) : null;

    $fuelType = isset($_POST['fuel_type']) ? trim($_POST['fuel_type']) : '';
    $transmission = isset($_POST['transmission']) ? trim($_POST['transmission']) : '';
    $seatingCapacity = isset($_POST['seating_capacity']) ? intval($_POST['seating_capacity']) : 0;
    $acAvailable = isset($_POST['ac_available']) ? intval($_POST['ac_available']) : 1;
    $status = isset($_POST['status']) ? trim($_POST['status']) : 'available';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';

    // Decode vehicle types
    $vehicleTypes = json_decode($vehicleTypesJson, true);
    if (!is_array($vehicleTypes)) {
        $vehicleTypes = [];
    }

    // Validate required fields
    if (empty($vehicleName) || empty($vehicleTypes) || $seatingCapacity <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Please fill in all required fields'
        ]);
        exit();
    }

    // Validate pricing based on type
    if ($pricingType === 'perday') {
        if ($perDayAmount <= 0 || $perKmCharge <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Please enter valid per day amount and per KM charge'
            ]);
            exit();
        }
    } else {
        if ($packageDays <= 0 || $packagePrice <= 0 || $packageKmLimit <= 0 || $extraKmCharge <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Please enter valid package details'
            ]);
            exit();
        }
    }

    // Handle main image upload
    $mainImagePath = '';
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $uploadFolder = createUploadFolder('../uploads', 'vehicle');
        if (!$uploadFolder) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create upload folder'
            ]);
            exit();
        }

        $mainImagePath = uploadImage($_FILES['main_image'], $uploadFolder);
        if (!$mainImagePath) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to upload main image'
            ]);
            exit();
        }
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
        $additionalFolder = $uploadFolder . '/additional';
        if (!file_exists($additionalFolder)) {
            mkdir($additionalFolder, 0777, true);
        }

        $uploadedAdditional = uploadMultipleImages($_FILES['additional_images'], $additionalFolder);
        if ($uploadedAdditional) {
            foreach ($uploadedAdditional as $img) {
                $additionalImages[] = str_replace('../', '', $img);
            }
        }
    }

    $vehicleTypesJsonString = json_encode($vehicleTypes);
    $additionalImagesJson = !empty($additionalImages) ? json_encode($additionalImages) : null;

    // Insert into database with pricing fields
    $stmt = $pdo->prepare("INSERT INTO vehicles 
        (vehicle_name, vehicle_model, vehicle_brand, vehicle_type, vehicle_image, additional_images, 
         per_day_amount, per_km_charge, pricing_type, package_days, package_price, package_km_limit, extra_km_charge,
         fuel_type, transmission, seating_capacity, ac_available, description, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $vehicleName,
        $vehicleModel,
        $vehicleBrand,
        $vehicleTypesJsonString,
        $mainImagePath,
        $additionalImagesJson,
        $perDayAmount,
        $perKmCharge,
        $pricingType,
        $packageDays,
        $packagePrice,
        $packageKmLimit,
        $extraKmCharge,
        $fuelType,
        $transmission,
        $seatingCapacity,
        $acAvailable,
        $description,
        $status
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Vehicle added successfully!',
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
