<?php
// =============================================
// ADD TOUR PACKAGE - AJAX HANDLER (UPDATED)
// =============================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

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

    // Validate
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

    // Extract members counts
    $adults = 0;
    $children = 0;
    $infants = 0;
    foreach ($members as $member) {
        $label = strtolower($member['label'] ?? '');
        $count = intval($member['count'] ?? 0);
        if ($label === 'adults' || $label === 'adult') {
            $adults = $count;
        } elseif ($label === 'children' || $label === 'child') {
            $children = $count;
        } elseif ($label === 'infants' || $label === 'infant') {
            $infants = $count;
        }
    }

    // Start transaction
    $pdo->beginTransaction();

    // Handle main image upload
    $mainImagePath = null;
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $uploadFolder = createUploadFolder('../uploads', 'tour-packages/main');
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

    // Handle gallery images
    $galleryImages = [];
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

    // Handle feature icons
    $featureIcons = [];
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
        } else {
            $feature['icon'] = null;
        }
    }
    unset($feature);

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO tour_packages (
        package_name, package_type, days_count, adults, children, infants,
        price, status, short_description, description,
        itinerary, features, main_image, gallery_images
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $itineraryJson = !empty($itinerary) ? json_encode($itinerary, JSON_UNESCAPED_SLASHES) : null;
    $featuresJson = !empty($features) ? json_encode($features, JSON_UNESCAPED_SLASHES) : null;
    $galleryJson = !empty($galleryImages) ? json_encode($galleryImages, JSON_UNESCAPED_SLASHES) : null;

    $stmt->execute([
        $packageName,
        $packageType,
        $daysCount,
        $adults,
        $children,
        $infants,
        $price,
        $status,
        $shortDescription,
        $description,
        $itineraryJson,
        $featuresJson,
        $mainImagePath,
        $galleryJson
    ]);

    $packageId = $pdo->lastInsertId();

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Tour package created successfully!',
        'id' => $packageId
    ]);

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Database error in add-tour-package.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Error in add-tour-package.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}