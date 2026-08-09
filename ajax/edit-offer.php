<?php
// =============================================
// EDIT OFFER - AJAX HANDLER
// =============================================

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
    // Get form data
    $id = intval($_POST['id'] ?? 0);
    $offerCode = trim($_POST['offer_code'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $discountType = trim($_POST['discount_type'] ?? 'percentage');
    $discountValue = floatval($_POST['discount_value'] ?? 0);
    $packageIds = json_decode($_POST['package_ids'] ?? '[]', true);
    $startDate = trim($_POST['start_date'] ?? '');
    $endDate = trim($_POST['end_date'] ?? '');
    $status = trim($_POST['status'] ?? 'active');
    $description = trim($_POST['description'] ?? '');
    $deleteMainImage = intval($_POST['delete_main_image'] ?? 0);

    // Validate
    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid offer ID']);
        exit();
    }

    if (empty($title)) {
        echo json_encode(['success' => false, 'message' => 'Offer title is required']);
        exit();
    }

    if ($discountValue <= 0) {
        echo json_encode(['success' => false, 'message' => 'Discount value must be greater than 0']);
        exit();
    }

    // Validate percentage (max 100%)
    if ($discountType === 'percentage' && $discountValue > 100) {
        echo json_encode(['success' => false, 'message' => 'Percentage discount cannot exceed 100%']);
        exit();
    }

    if (empty($packageIds)) {
        echo json_encode(['success' => false, 'message' => 'Please select at least one tour package']);
        exit();
    }

    // Validate package IDs exist
    $placeholders = implode(',', array_fill(0, count($packageIds), '?'));
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tour_packages WHERE id IN ($placeholders)");
    $stmt->execute($packageIds);
    $count = $stmt->fetchColumn();
    if ($count != count($packageIds)) {
        echo json_encode(['success' => false, 'message' => 'One or more selected packages are invalid']);
        exit();
    }

    // Start transaction
    $pdo->beginTransaction();

    // Get existing offer data
    $stmt = $pdo->prepare("SELECT * FROM offers WHERE id = ?");
    $stmt->execute([$id]);
    $existingOffer = $stmt->fetch();

    if (!$existingOffer) {
        echo json_encode(['success' => false, 'message' => 'Offer not found']);
        exit();
    }

    // Handle main image
    $mainImagePath = $existingOffer['main_image'];

    // Delete main image if requested
    if ($deleteMainImage == 1 && $mainImagePath) {
        $oldPath = '../' . $mainImagePath;
        if (file_exists($oldPath)) {
            @unlink($oldPath);
            // Delete folder if empty
            $dir = dirname($oldPath);
            if (is_dir($dir) && count(scandir($dir)) <= 2) {
                @rmdir($dir);
                $parentDir = dirname($dir);
                if (is_dir($parentDir) && count(scandir($parentDir)) <= 2) {
                    @rmdir($parentDir);
                }
            }
        }
        $mainImagePath = null;
    }

    // Upload new main image if provided
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        // Delete old main image if exists and not already deleted
        if ($mainImagePath && $deleteMainImage != 1) {
            $oldPath = '../' . $mainImagePath;
            if (file_exists($oldPath)) {
                @unlink($oldPath);
                $dir = dirname($oldPath);
                if (is_dir($dir) && count(scandir($dir)) <= 2) {
                    @rmdir($dir);
                    $parentDir = dirname($dir);
                    if (is_dir($parentDir) && count(scandir($parentDir)) <= 2) {
                        @rmdir($parentDir);
                    }
                }
            }
        }

        $uploadFolder = createUploadFolder('../uploads', 'offers');
        if (!$uploadFolder) {
            throw new Exception('Failed to create upload folder');
        }
        $uploadedPath = uploadImage($_FILES['main_image'], $uploadFolder);
        if (!$uploadedPath) {
            throw new Exception('Failed to upload main image');
        }
        $mainImagePath = str_replace('../', '', $uploadedPath);
    }

    // Update database
    $stmt = $pdo->prepare("UPDATE offers SET 
        title = ?, description = ?, discount_type = ?, discount_value = ?,
        tour_packages = ?, main_image = ?, start_date = ?, end_date = ?, status = ?
        WHERE id = ?");

    $packageIdsJson = json_encode($packageIds, JSON_UNESCAPED_SLASHES);

    $stmt->execute([
        $title,
        $description,
        $discountType,
        $discountValue,
        $packageIdsJson,
        $mainImagePath,
        !empty($startDate) ? $startDate : null,
        !empty($endDate) ? $endDate : null,
        $status,
        $id
    ]);

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Offer updated successfully!'
    ]);

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Database error in edit-offer.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Error in edit-offer.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>