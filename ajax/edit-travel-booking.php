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
    $id = intval($_POST['id'] ?? 0);
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
    $status = trim($_POST['status'] ?? 'pending');
    $deletedProvideIcons = json_decode($_POST['deleted_provide_icons'] ?? '[]', true);

    if ($id <= 0 || $carId <= 0 || empty($carName) || $seatCount <= 0 || $days <= 0 || empty($stops)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
        exit();
    }

    // =============================================
    // DELETE REMOVED ICONS
    // =============================================
    foreach ($deletedProvideIcons as $iconPath) {
        if (!empty($iconPath)) {
            $cleanPath = str_replace(APP_URL, '', $iconPath);
            $fullPath = '../' . $cleanPath;

            if (file_exists($fullPath)) {
                @unlink($fullPath);
                $dir = dirname($fullPath);
                if (is_dir($dir)) {
                    $files = scandir($dir);
                    if (count($files) <= 2) {
                        @rmdir($dir);
                        $parentDir = dirname($dir);
                        if (is_dir($parentDir) && count(scandir($parentDir)) <= 2) {
                            @rmdir($parentDir);
                        }
                    }
                }
            }
        }
    }

    // =============================================
    // UPLOAD NEW PROVIDE ICONS
    // =============================================
    $iconPaths = [];

    if (isset($_FILES['provide_icons']) && !empty($_FILES['provide_icons']['name'][0])) {
        $iconFolder = createUploadFolder('../uploads', 'travel-bookings/provide-icons');
        if (!$iconFolder) {
            throw new Exception('Failed to create upload folder for icons');
        }

        $fileCount = count($_FILES['provide_icons']['name']);
        $iconNames = $_POST['provide_icon_names'] ?? [];

        for ($i = 0; $i < $fileCount; $i++) {
            if ($_FILES['provide_icons']['error'][$i] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $_FILES['provide_icons']['name'][$i],
                    'type' => $_FILES['provide_icons']['type'][$i],
                    'tmp_name' => $_FILES['provide_icons']['tmp_name'][$i],
                    'error' => $_FILES['provide_icons']['error'][$i],
                    'size' => $_FILES['provide_icons']['size'][$i]
                ];

                $uploadedPath = uploadImage($file, $iconFolder);
                if ($uploadedPath) {
                    $uploadedPath = str_replace('../', '', $uploadedPath);
                    $featureName = $iconNames[$i] ?? 'icon_' . $i;
                    $iconPaths[$featureName] = $uploadedPath;
                }
            }
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

    // =============================================
    // UPDATE DATABASE
    // =============================================
    $stmt = $pdo->prepare("UPDATE travel_bookings SET 
        car_id = ?, car_name = ?, car_type = ?, seat_count = ?, days = ?,
        per_day_price = ?, per_km_charge = ?, total_price = ?, total_distance = ?,
        stops = ?, what_we_provide = ?, status = ?
        WHERE id = ?");

    $stopsJson = json_encode($stops, JSON_UNESCAPED_SLASHES);
    $provideJson = json_encode($whatWeProvide, JSON_UNESCAPED_SLASHES);

    $result = $stmt->execute([
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
        $provideJson,
        $status,
        $id
    ]);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Booking updated successfully!'
        ]);
    } else {
        throw new Exception('Failed to update booking');
    }
} catch (PDOException $e) {
    error_log('Database error in update-travel-booking.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log('Error in update-travel-booking.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
