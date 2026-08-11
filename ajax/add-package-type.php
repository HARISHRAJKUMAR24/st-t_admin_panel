<?php
// =============================================
// ADD PACKAGE TYPE - AJAX HANDLER
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
    $name = trim($_POST['name'] ?? '');

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Type name is required']);
        exit();
    }

    // Check if name already exists
    $stmt = $pdo->prepare("SELECT id FROM package_type_images WHERE name = ?");
    $stmt->execute([$name]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Type name already exists']);
        exit();
    }

    // Upload image
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadFolder = createUploadFolder('../uploads', 'package-types');
        if (!$uploadFolder) {
            throw new Exception('Failed to create upload folder');
        }

        $file = $_FILES['image'];
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $fullPath = $uploadFolder . '/' . $fileName;
        $fullPath = str_replace('\\', '/', $fullPath);

        if (move_uploaded_file($file['tmp_name'], $fullPath)) {
            $imagePath = str_replace('../', '', $fullPath);
        } else {
            throw new Exception('Failed to upload image');
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Image is required']);
        exit();
    }

    // Insert
    $stmt = $pdo->prepare("INSERT INTO package_type_images (name, image) VALUES (?, ?)");
    $stmt->execute([$name, $imagePath]);

    echo json_encode([
        'success' => true,
        'message' => 'Package type added successfully!'
    ]);

} catch (PDOException $e) {
    error_log('Database error in add-package-type.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log('Error in add-package-type.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>