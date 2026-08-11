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
    $packageName = trim($_POST['package_name'] ?? '');
    $packageType = trim($_POST['package_type'] ?? '');
    $daysCount = intval($_POST['days_count'] ?? 1);
    $members = json_decode($_POST['members'] ?? '[]', true);
    $price = floatval($_POST['price'] ?? 0);
    $status = trim($_POST['status'] ?? 'active');
    $shortDescription = trim($_POST['short_description'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $itinerary = json_decode($_POST['itinerary'] ?? '{}', true);
    $features = json_decode($_POST['features'] ?? '[]', true);
    $deletedFeatures = json_decode($_POST['deleted_features'] ?? '[]', true);
    $deletedGalleryImages = json_decode($_POST['deleted_gallery_images'] ?? '[]', true);
    $deleteMainImage = intval($_POST['delete_main_image'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid package ID']);
        exit();
    }

    if (empty($packageName)) {
        echo json_encode(['success' => false, 'message' => 'Package name is required']);
        exit();
    }

    if ($daysCount < 1) {
        echo json_encode(['success' => false, 'message' => 'Days must be at least 1']);
        exit();
    }

    if ($price <= 0) {
        echo json_encode(['success' => false, 'message' => 'Price must be greater than 0']);
        exit();
    }

    if (empty($shortDescription)) {
        echo json_encode(['success' => false, 'message' => 'Short description is required']);
        exit();
    }

    if (empty($members)) {
        echo json_encode(['success' => false, 'message' => 'At least one member type is required']);
        exit();
    }

    // Start transaction
    $pdo->beginTransaction();

    // Get existing package data
    $stmt = $pdo->prepare("SELECT * FROM tour_packages WHERE id = ?");
    $stmt->execute([$id]);
    $existingPackage = $stmt->fetch();

    if (!$existingPackage) {
        echo json_encode(['success' => false, 'message' => 'Package not found']);
        exit();
    }

    // =============================================
    // HANDLE MAIN IMAGE
    // =============================================
    $mainImagePath = $existingPackage['main_image'];

    if ($deleteMainImage == 1 && $mainImagePath) {
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
        $mainImagePath = null;
    }

    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
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
        $uploadFolder = createUploadFolder('../uploads', 'tour-packages/main');
        if (!$uploadFolder) {
            throw new Exception('Failed to create upload folder for main image');
        }
        $uploadedPath = uploadImage($_FILES['main_image'], $uploadFolder);
        if (!$uploadedPath) {
            throw new Exception('Failed to upload main image');
        }
        $mainImagePath = str_replace('../', '', $uploadedPath);
    }

    // =============================================
    // HANDLE GALLERY IMAGES
    // =============================================
    $galleryImages = json_decode($existingPackage['gallery_images'], true) ?: [];

    foreach ($deletedGalleryImages as $imgPath) {
        $key = array_search($imgPath, $galleryImages);
        if ($key !== false) {
            unset($galleryImages[$key]);
            $fullPath = '../' . $imgPath;
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
    }
    $galleryImages = array_values($galleryImages);

    if (isset($_FILES['gallery_images']) && !empty($_FILES['gallery_images']['name'][0])) {
        $galleryFolder = createUploadFolder('../uploads', 'tour-packages/gallery');
        if (!$galleryFolder) {
            throw new Exception('Failed to create gallery folder');
        }
        $uploadedGallery = uploadMultipleImages($_FILES['gallery_images'], $galleryFolder);
        if ($uploadedGallery) {
            foreach ($uploadedGallery as $img) {
                $galleryImages[] = str_replace('../', '', $img);
            }
        }
    }

    // =============================================
    // HANDLE FEATURE ICONS
    // =============================================
    $featureIcons = [];

    foreach ($deletedFeatures as $iconPath) {
        if (!empty($iconPath)) {
            $fullPath = '../' . $iconPath;
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
    }

    if (isset($_FILES['feature_icons']) && !empty($_FILES['feature_icons']['name'][0])) {
        $featureIconFolder = createUploadFolder('../uploads', 'tour-packages/features');
        if (!$featureIconFolder) {
            throw new Exception('Failed to create feature icons folder');
        }
        
        $uploadedIcons = uploadMultipleImages($_FILES['feature_icons'], $featureIconFolder);
        if ($uploadedIcons) {
            $iconNames = $_POST['feature_icon_names'] ?? [];
            foreach ($uploadedIcons as $index => $iconPath) {
                $iconPath = str_replace('../', '', $iconPath);
                $featureName = $iconNames[$index] ?? 'feature_' . $index;
                $featureIcons[$featureName] = $iconPath;
            }
        }
    }

    // Update features with icon paths
    foreach ($features as &$feature) {
        $featureName = $feature['name'] ?? '';
        if (!empty($featureName) && isset($featureIcons[$featureName])) {
            $feature['icon'] = $featureIcons[$featureName];
        } else if (empty($feature['icon']) || in_array($feature['icon'], $deletedFeatures)) {
            $feature['icon'] = null;
        }
    }
    unset($feature);

    // =============================================
    // UPDATE DATABASE
    // =============================================
    $stmt = $pdo->prepare("UPDATE tour_packages SET 
        package_name = ?, package_type = ?, days_count = ?, 
        members = ?, price = ?, status = ?, 
        short_description = ?, description = ?, 
        itinerary = ?, features = ?, main_image = ?, gallery_images = ?
        WHERE id = ?");

    $membersJson = json_encode($members, JSON_UNESCAPED_SLASHES);
    $itineraryJson = !empty($itinerary) ? json_encode($itinerary, JSON_UNESCAPED_SLASHES) : null;
    $featuresJson = !empty($features) ? json_encode($features, JSON_UNESCAPED_SLASHES) : null;
    $galleryJson = !empty($galleryImages) ? json_encode($galleryImages, JSON_UNESCAPED_SLASHES) : null;

    $stmt->execute([
        $packageName,
        $packageType,
        $daysCount,
        $membersJson,
        $price,
        $status,
        $shortDescription,
        $description,
        $itineraryJson,
        $featuresJson,
        $mainImagePath,
        $galleryJson,
        $id
    ]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Tour package updated successfully!'
    ]);

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Database error in edit-tour-package.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Error in edit-tour-package.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>