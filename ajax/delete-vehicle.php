<?php
// =============================================
// DELETE VEHICLE - AJAX HANDLER
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
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $id = isset($input['id']) ? intval($input['id']) : 0;

    if ($id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid vehicle ID'
        ]);
        exit();
    }

    // Get vehicle data
    $stmt = $pdo->prepare("SELECT vehicle_image, additional_images FROM vehicles WHERE id = ?");
    $stmt->execute([$id]);
    $vehicle = $stmt->fetch();

    if (!$vehicle) {
        echo json_encode([
            'success' => false,
            'message' => 'Vehicle not found'
        ]);
        exit();
    }

    // Delete main image
    if (!empty($vehicle['vehicle_image'])) {
        $mainImagePath = '../' . $vehicle['vehicle_image'];
        if (file_exists($mainImagePath)) {
            @unlink($mainImagePath);
        }
        // Delete folder if empty
        $mainDir = dirname($mainImagePath);
        if (is_dir($mainDir)) {
            $files = scandir($mainDir);
            if (count($files) <= 2) {
                @rmdir($mainDir);
            }
        }
    }

    // Delete additional images
    if (!empty($vehicle['additional_images'])) {
        $additionalImages = json_decode($vehicle['additional_images'], true) ?: [];
        foreach ($additionalImages as $img) {
            $imgPath = '../' . $img;
            if (file_exists($imgPath)) {
                @unlink($imgPath);
            }
        }
        // Delete additional folder
        $additionalDir = dirname('../' . ($additionalImages[0] ?? ''));
        if (is_dir($additionalDir)) {
            $files = scandir($additionalDir);
            if (count($files) <= 2) {
                @rmdir($additionalDir);
            }
        }
    }

    // Delete the main folder if empty
    if (!empty($vehicle['vehicle_image'])) {
        $mainImagePath = '../' . $vehicle['vehicle_image'];
        $mainDir = dirname($mainImagePath);
        $parentDir = dirname($mainDir);
        if (is_dir($parentDir)) {
            $files = scandir($parentDir);
            if (count($files) <= 2) {
                @rmdir($parentDir);
            }
        }
    }

    // Delete from database
    $stmt = $pdo->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode([
        'success' => true,
        'message' => 'Vehicle deleted successfully'
    ]);
} catch (PDOException $e) {
    error_log('Database error in delete-vehicle.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
} catch (Exception $e) {
    error_log('Error in delete-vehicle.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred'
    ]);
}
