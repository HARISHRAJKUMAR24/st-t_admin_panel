<?php
// =============================================
// AJAX DELETE CAR RENTAL - WITH FOLDER DELETE
// =============================================

header('Content-Type: application/json');

// Include config
require_once '../config/config.php';
requireLogin();

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$carId = isset($data['id']) ? intval($data['id']) : 0;

if ($carId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid car ID'
    ]);
    exit();
}

try {
    // Get car data
    $stmt = $pdo->prepare("SELECT car_image, additional_images FROM car_rentals WHERE id = ?");
    $stmt->execute([$carId]);
    $car = $stmt->fetch();

    if (!$car) {
        echo json_encode([
            'success' => false,
            'message' => 'Car not found'
        ]);
        exit();
    }

    // Get the folder path from the main image
    $folderPath = null;
    if ($car['car_image']) {
        // Get the full path
        $fullImagePath = '../' . $car['car_image'];

        // Get the directory containing the image
        // This will give us: uploads/car-rental/191963/2026-08-08/
        $imageDir = dirname($fullImagePath);

        // The parent folder (random number folder)
        // uploads/car-rental/191963/
        $folderPath = dirname($imageDir);
    }

    // Delete main image
    if ($car['car_image']) {
        $imagePath = '../' . $car['car_image'];
        if (file_exists($imagePath)) {
            @unlink($imagePath);
        }
    }

    // Delete additional images
    if ($car['additional_images']) {
        $additionalImages = json_decode($car['additional_images'], true);
        if ($additionalImages) {
            foreach ($additionalImages as $img) {
                $imagePath = '../' . $img;
                if (file_exists($imagePath)) {
                    @unlink($imagePath);
                }
            }
        }
    }

    // Delete the entire folder (random number folder) and all its contents
    if ($folderPath && file_exists($folderPath)) {
        $deleted = deleteFolder($folderPath);
        error_log("Deleted folder: " . $folderPath . " - Success: " . ($deleted ? 'Yes' : 'No'));
    } else {
        error_log("Folder not found: " . $folderPath);
    }

    // Delete from database
    $stmt = $pdo->prepare("DELETE FROM car_rentals WHERE id = ?");
    $stmt->execute([$carId]);

    echo json_encode([
        'success' => true,
        'message' => 'Rental Car deleted successfully!'
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

// =============================================
// HELPER FUNCTION TO DELETE FOLDER
// =============================================

function deleteFolder($folderPath)
{
    if (!file_exists($folderPath)) {
        return false;
    }

    // Convert to proper path format
    $folderPath = str_replace('\\', '/', $folderPath);

    // Get all files and folders
    $items = scandir($folderPath);
    if ($items === false) {
        return false;
    }

    foreach ($items as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        $itemPath = $folderPath . '/' . $item;
        $itemPath = str_replace('\\', '/', $itemPath);

        if (is_dir($itemPath)) {
            // Recursively delete subfolder
            deleteFolder($itemPath);
        } else {
            // Delete file
            @unlink($itemPath);
        }
    }

    // Delete the empty folder
    return @rmdir($folderPath);
}
