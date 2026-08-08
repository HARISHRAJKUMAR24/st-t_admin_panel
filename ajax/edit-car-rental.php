<?php
// =============================================
// AJAX UPDATE CAR RENTAL - WITH IMAGE DELETE
// =============================================

// Disable error display
error_reporting(0);
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
    $carName = isset($_POST['car_name']) ? trim($_POST['car_name']) : '';
    $carModel = isset($_POST['car_model']) ? trim($_POST['car_model']) : '';
    $carBrand = isset($_POST['car_brand']) ? trim($_POST['car_brand']) : '';
    $carType = isset($_POST['car_type']) ? trim($_POST['car_type']) : '';
    $perDayAmount = isset($_POST['per_day_amount']) ? floatval($_POST['per_day_amount']) : 0;
    $perKmCharge = isset($_POST['per_km_charge']) ? floatval($_POST['per_km_charge']) : 0;
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
    if ($id <= 0 || empty($carName) || $perDayAmount <= 0 || $perKmCharge <= 0 || $seatingCapacity <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Please fill in all required fields'
        ]);
        exit();
    }

    // Get existing car data
    $stmt = $pdo->prepare("SELECT car_image, additional_images FROM car_rentals WHERE id = ?");
    $stmt->execute([$id]);
    $existingCar = $stmt->fetch();

    if (!$existingCar) {
        echo json_encode([
            'success' => false,
            'message' => 'Car not found'
        ]);
        exit();
    }

    $mainImagePath = $existingCar['car_image'];
    $additionalImages = json_decode($existingCar['additional_images'], true) ?: [];

    // Handle main image delete
    if ($deleteMainImage == 1 && $mainImagePath) {
        // Delete main image file
        $oldPath = '../' . $mainImagePath;
        if (file_exists($oldPath)) {
            @unlink($oldPath);
        }
        $mainImagePath = null;
    }

    // Handle main image upload
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        // If main image exists, delete it
        if ($mainImagePath) {
            $oldPath = '../' . $mainImagePath;
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        // Delete old additional images
        if (!empty($additionalImages)) {
            foreach ($additionalImages as $img) {
                $oldPath = '../' . $img;
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $additionalImages = [];
        }

        // Delete old folder if it exists
        if ($existingCar['car_image']) {
            $fullImagePath = '../' . $existingCar['car_image'];
            $imageDir = dirname($fullImagePath);
            $folderPath = dirname($imageDir);
            if (file_exists($folderPath)) {
                deleteFolder($folderPath);
            }
        }

        // Create new upload folder
        $uploadFolder = createUploadFolder('../uploads', 'car-rental');
        if (!$uploadFolder) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to create upload folder'
            ]);
            exit();
        }

        // Upload new main image
        $mainImagePath = uploadImage($_FILES['main_image'], $uploadFolder);
        if (!$mainImagePath) {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to upload main image'
            ]);
            exit();
        }
        $mainImagePath = str_replace('../', '', $mainImagePath);

        // Handle additional images upload
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
                // Remove from additional images array
                $key = array_search($imgPath, $additionalImages);
                if ($key !== false) {
                    unset($additionalImages[$key]);
                }

                // Delete file
                $fullPath = '../' . $imgPath;
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }
            $additionalImages = array_values($additionalImages);
        }

        // Handle additional images upload
        if (isset($_FILES['additional_images']) && !empty($_FILES['additional_images']['name'][0])) {
            // Get the folder path from existing main image
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

    // Update database
    $stmt = $pdo->prepare("UPDATE car_rentals SET 
        car_name = ?, car_model = ?, car_brand = ?, car_type = ?, 
        car_image = ?, additional_images = ?,
        per_day_amount = ?, per_km_charge = ?, fuel_type = ?, 
        transmission = ?, seating_capacity = ?, ac_available = ?, 
        description = ?, status = ? WHERE id = ?");

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
        $status,
        $id
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Car rental updated successfully!'
    ]);
} catch (PDOException $e) {
    error_log('Database error in edit-car-rental.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
} catch (Exception $e) {
    error_log('Error in edit-car-rental.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred'
    ]);
}