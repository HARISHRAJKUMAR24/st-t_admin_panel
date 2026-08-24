<?php
// =============================================
// AJAX UPDATE VEHICLE - WITH IMAGE DELETE
// =============================================

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

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
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $vehicleName = isset($_POST['vehicle_name']) ? trim($_POST['vehicle_name']) : '';
    $vehicleModel = isset($_POST['vehicle_model']) ? trim($_POST['vehicle_model']) : '';
    $vehicleBrand = isset($_POST['vehicle_brand']) ? trim($_POST['vehicle_brand']) : '';

    // Get vehicle types as JSON
    $vehicleTypesJson = isset($_POST['vehicle_type']) ? $_POST['vehicle_type'] : '[]';
    $vehicleTypesArray = json_decode($vehicleTypesJson, true) ?: [];
    $vehicleTypesArray = array_filter($vehicleTypesArray, function ($val) {
        return $val !== '' && $val !== null;
    });
    $vehicleType = json_encode(array_values($vehicleTypesArray));

    // Pricing fields
    $pricingType = isset($_POST['pricing_type']) ? trim($_POST['pricing_type']) : 'perday';
    $perDayAmount = isset($_POST['per_day_amount']) && !empty($_POST['per_day_amount']) ? floatval($_POST['per_day_amount']) : 0;
    $perKmCharge = isset($_POST['per_km_charge']) && !empty($_POST['per_km_charge']) ? floatval($_POST['per_km_charge']) : 0;
    $packageDays = isset($_POST['package_days']) && !empty($_POST['package_days']) ? intval($_POST['package_days']) : null;
    $packagePrice = isset($_POST['package_price']) && !empty($_POST['package_price']) ? floatval($_POST['package_price']) : null;
    $packageKmLimit = isset($_POST['package_km_limit']) && !empty($_POST['package_km_limit']) ? intval($_POST['package_km_limit']) : null;
    $extraKmCharge = isset($_POST['extra_km_charge']) && !empty($_POST['extra_km_charge']) ? floatval($_POST['extra_km_charge']) : null;

    $fuelType = isset($_POST['fuel_type']) ? trim($_POST['fuel_type']) : '';
    $transmission = isset($_POST['transmission']) ? trim($_POST['transmission']) : '';
    $seatingCapacity = isset($_POST['seating_capacity']) ? intval($_POST['seating_capacity']) : 4;
    $acAvailable = isset($_POST['ac_available']) ? intval($_POST['ac_available']) : 1;
    $status = isset($_POST['status']) ? trim($_POST['status']) : 'available';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $deletedImagesJson = isset($_POST['deleted_images']) ? $_POST['deleted_images'] : '[]';
    $deleteMainImage = isset($_POST['delete_main_image']) ? intval($_POST['delete_main_image']) : 0;

    $deletedImages = json_decode($deletedImagesJson, true) ?: [];

    // Validate required fields
    if ($id <= 0 || empty($vehicleName) || empty($vehicleTypesArray) || $seatingCapacity <= 0) {
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

    // Get existing vehicle data
    $stmt = $pdo->prepare("SELECT vehicle_image, additional_images FROM vehicles WHERE id = ?");
    $stmt->execute([$id]);
    $existingVehicle = $stmt->fetch();

    if (!$existingVehicle) {
        echo json_encode([
            'success' => false,
            'message' => 'Vehicle not found'
        ]);
        exit();
    }

    $mainImagePath = $existingVehicle['vehicle_image'];
    $additionalImages = json_decode($existingVehicle['additional_images'], true) ?: [];

    // Handle main image delete
    if ($deleteMainImage == 1 && $mainImagePath) {
        $oldPath = '../' . $mainImagePath;
        if (file_exists($oldPath)) {
            @unlink($oldPath);
        }
        $mainImagePath = null;
    }

    // Handle main image upload
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        if ($mainImagePath) {
            $oldPath = '../' . $mainImagePath;
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        if (!empty($additionalImages)) {
            foreach ($additionalImages as $img) {
                $oldPath = '../' . $img;
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $additionalImages = [];
        }

        if ($existingVehicle['vehicle_image']) {
            $fullImagePath = '../' . $existingVehicle['vehicle_image'];
            $imageDir = dirname($fullImagePath);
            $folderPath = dirname($imageDir);
            if (file_exists($folderPath)) {
                deleteFolder($folderPath);
            }
        }

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
    } else {
        // Handle deleted images
        if (!empty($deletedImages)) {
            foreach ($deletedImages as $imgPath) {
                $key = array_search($imgPath, $additionalImages);
                if ($key !== false) {
                    unset($additionalImages[$key]);
                }

                $fullPath = '../' . $imgPath;
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }
            $additionalImages = array_values($additionalImages);
        }

        if (isset($_FILES['additional_images']) && !empty($_FILES['additional_images']['name'][0])) {
            if ($mainImagePath) {
                $fullImagePath = '../' . $mainImagePath;
                $imageDir = dirname($fullImagePath);
                $folderPath = $imageDir;
                $additionalFolder = $folderPath . '/additional';
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
        }
    }

    $additionalImagesJson = !empty($additionalImages) ? json_encode($additionalImages) : null;

    // Update database with pricing fields
    $stmt = $pdo->prepare("UPDATE vehicles SET 
        vehicle_name = ?, vehicle_model = ?, vehicle_brand = ?, vehicle_type = ?, 
        vehicle_image = ?, additional_images = ?,
        per_day_amount = ?, per_km_charge = ?, pricing_type = ?,
        package_days = ?, package_price = ?, package_km_limit = ?, extra_km_charge = ?,
        fuel_type = ?, transmission = ?, seating_capacity = ?, ac_available = ?, 
        description = ?, status = ? WHERE id = ?");

    $stmt->execute([
        $vehicleName,
        $vehicleModel,
        $vehicleBrand,
        $vehicleType,
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
        $status,
        $id
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Vehicle updated successfully!'
    ]);
} catch (PDOException $e) {
    error_log('Database error in edit-vehicle.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
} catch (Exception $e) {
    error_log('Error in edit-vehicle.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred'
    ]);
}
