<?php
// =============================================
// ADD OFFER - AJAX HANDLER
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
    $title = trim($_POST['title'] ?? '');
    $discountType = trim($_POST['discount_type'] ?? 'percentage');
    $discountValue = floatval($_POST['discount_value'] ?? 0);
    $packageIds = json_decode($_POST['package_ids'] ?? '[]', true);
    $startDate = trim($_POST['start_date'] ?? '');
    $endDate = trim($_POST['end_date'] ?? '');
    $status = trim($_POST['status'] ?? 'active');
    $description = trim($_POST['description'] ?? '');

    // Validate
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

    // Handle main image upload
    $mainImagePath = null;
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $uploadFolder = createUploadFolder('../uploads', 'offers');
        if (!$uploadFolder) {
            throw new Exception('Failed to create upload folder');
        }
        $uploadedPath = uploadImage($_FILES['main_image'], $uploadFolder);
        if (!$uploadedPath) {
            throw new Exception('Failed to upload main image');
        }
        $mainImagePath = str_replace('../', '', $uploadedPath);
    } else {
        echo json_encode(['success' => false, 'message' => 'Main image is required']);
        exit();
    }

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO offers (
        title, description, discount_type, discount_value, 
        tour_packages, main_image, start_date, end_date, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

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
        $status
    ]);

    $offerId = $pdo->lastInsertId();

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Offer created successfully!',
        'id' => $offerId
    ]);

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Database error in add-offer.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Error in add-offer.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>