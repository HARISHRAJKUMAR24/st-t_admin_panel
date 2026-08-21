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
    $name = trim($_POST['name'] ?? '');
    $testimonial = trim($_POST['testimonial'] ?? '');
    $status = trim($_POST['status'] ?? 'publish');

    if (empty($name) || empty($testimonial)) {
        echo json_encode(['success' => false, 'message' => 'Name and testimonial are required']);
        exit();
    }

    // Handle logo upload
    $logoPath = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $uploadFolder = createUploadFolder('../uploads', 'testimonials');
        if (!$uploadFolder) {
            throw new Exception('Failed to create upload folder');
        }
        $uploadedPath = uploadImage($_FILES['logo'], $uploadFolder);
        if ($uploadedPath) {
            $logoPath = str_replace('../', '', $uploadedPath);
        }
    }

    $stmt = $pdo->prepare("INSERT INTO testimonials (name, logo, testimonial, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $logoPath, $testimonial, $status]);

    echo json_encode([
        'success' => true,
        'message' => 'Testimonial added successfully!'
    ]);
} catch (PDOException $e) {
    error_log('Database error in add-testimonial.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log('Error in add-testimonial.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
