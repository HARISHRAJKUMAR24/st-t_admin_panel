<?php
// =============================================
// UPDATE PASSWORD - AJAX HANDLER
// =============================================

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON header
header('Content-Type: application/json');

// Include config and functions
require_once '../config/config.php';
require_once '../config/function.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized - Please login'
    ]);
    exit();
}

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
    $action = $_POST['action'] ?? '';
    $userId = $_SESSION['user_id'];

    // =============================================
    // VALIDATE CURRENT PASSWORD
    // =============================================
    
    if ($action === 'validate_current') {
        $currentPassword = $_POST['current_password'] ?? '';
        
        if (empty($currentPassword)) {
            echo json_encode(['valid' => false]);
            exit();
        }
        
        // Get user's current password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user) {
            echo json_encode(['valid' => false]);
            exit();
        }
        
        // Verify password
        $valid = password_verify($currentPassword, $user['password']);
        echo json_encode(['valid' => $valid]);
        exit();
    }

    // =============================================
    // UPDATE PASSWORD
    // =============================================
    
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    // Validate
    if (empty($currentPassword) || empty($newPassword)) {
        echo json_encode([
            'success' => false,
            'message' => 'All fields are required'
        ]);
        exit();
    }

    if (strlen($newPassword) < 8) {
        echo json_encode([
            'success' => false,
            'message' => 'New password must be at least 8 characters long'
        ]);
        exit();
    }

    // Check if new password is same as current
    if ($currentPassword === $newPassword) {
        echo json_encode([
            'success' => false,
            'message' => 'New password cannot be the same as current password'
        ]);
        exit();
    }

    // Get current user
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
        exit();
    }

    // Verify current password
    if (!password_verify($currentPassword, $user['password'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Current password is incorrect'
        ]);
        exit();
    }

    // Hash new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update password
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$hashedPassword, $userId]);

    echo json_encode([
        'success' => true,
        'message' => 'Password updated successfully!'
    ]);

} catch (PDOException $e) {
    error_log('Database error in update-password.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred. Please try again.'
    ]);
} catch (Exception $e) {
    error_log('Error in update-password.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>