<?php
// =============================================
// UPDATE PACKAGE TYPE - AJAX HANDLER
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
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $deleteImage = intval($_POST['delete_image'] ?? 0);

    if ($id <= 0 || empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
        exit();
    }

    // Get current type
    $stmt = $pdo->prepare("SELECT * FROM package_type_images WHERE id = ?");
    $stmt->execute([$id]);
    $type = $stmt->fetch();

    if (!$type) {
        echo json_encode(['success' => false, 'message' => 'Type not found']);
        exit();
    }

    // Check if name already exists for other types
    $stmt = $pdo->prepare("SELECT id FROM package_type_images WHERE name = ? AND id != ?");
    $stmt->execute([$name, $id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Type name already exists']);
        exit();
    }

    $imagePath = $type['image'];

    // Delete image if requested
    if ($deleteImage == 1 && $imagePath) {
        $fullPath = '../' . $imagePath;
        $fullPath = str_replace('\\', '/', $fullPath);

        if (file_exists($fullPath)) {
            @unlink($fullPath);
            // Delete empty folders
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
        $imagePath = null;
    }

    // Upload new image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Delete old image if exists and not already deleted
        if ($imagePath && $deleteImage != 1) {
            $fullPath = '../' . $imagePath;
            $fullPath = str_replace('\\', '/', $fullPath);

            if (file_exists($fullPath)) {
                @unlink($fullPath);
                $dir = dirname($fullPath);
                if (is_dir($dir) && count(scandir($dir)) <= 2) {
                    @rmdir($dir);
                    $parentDir = dirname($dir);
                    if (is_dir($parentDir) && count(scandir($parentDir)) <= 2) {
                        @rmdir($parentDir);
                    }
                }
            }
        }

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
    }

    // Update
    $stmt = $pdo->prepare("UPDATE package_type_images SET name = ?, image = ? WHERE id = ?");
    $stmt->execute([$name, $imagePath, $id]);

    echo json_encode([
        'success' => true,
        'message' => 'Package type updated successfully!'
    ]);
} catch (PDOException $e) {
    error_log('Database error in update-package-type.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log('Error in update-package-type.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
