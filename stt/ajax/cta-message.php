<?php
require_once '../config/config.php';

header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$fullName = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$phoneNumber = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Log received data for debugging
error_log("CTA Message - Name: $fullName, Phone: $phoneNumber");

// Validation
if (empty($fullName)) {
    echo json_encode(['success' => false, 'message' => 'Please enter your full name.']);
    exit;
}

if (strlen($fullName) < 2 || strlen($fullName) > 100) {
    echo json_encode(['success' => false, 'message' => 'Name must be between 2 and 100 characters.']);
    exit;
}

if (!preg_match('/^[a-zA-Z\s.\'-]{2,100}$/', $fullName)) {
    echo json_encode(['success' => false, 'message' => 'Name can only contain letters, spaces, dots, hyphens, and apostrophes.']);
    exit;
}

if (empty($phoneNumber)) {
    echo json_encode(['success' => false, 'message' => 'Please enter your phone number.']);
    exit;
}

// Remove any spaces
$phoneNumber = preg_replace('/\s/', '', $phoneNumber);

if (strlen($phoneNumber) !== 10) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid 10-digit mobile number.']);
    exit;
}

if (!preg_match('/^[6-9][0-9]{9}$/', $phoneNumber)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid Indian mobile number (starts with 6-9).']);
    exit;
}

if (empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Please enter your message.']);
    exit;
}

if (strlen($message) < 5) {
    echo json_encode(['success' => false, 'message' => 'Message must be at least 5 characters.']);
    exit;
}

if (strlen($message) > 500) {
    echo json_encode(['success' => false, 'message' => 'Message cannot exceed 500 characters.']);
    exit;
}

try {
    // Check if table exists
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'cta_messages'");
    if ($tableCheck->rowCount() == 0) {
        error_log("CTA Error: cta_messages table does not exist");
        throw new Exception("Table 'cta_messages' does not exist");
    }

    $stmt = $pdo->prepare("
        INSERT INTO cta_messages (full_name, phone_number, message, status, created_at)
        VALUES (?, ?, ?, 'pending', NOW())
    ");
    
    $result = $stmt->execute([$fullName, $phoneNumber, $message]);
    
    if ($result) {
        $lastId = $pdo->lastInsertId();
        error_log("CTA Message saved successfully - ID: $lastId");
        echo json_encode([
            'success' => true,
            'message' => 'Thank you! Our travel expert will contact you shortly.',
            'id' => $lastId
        ]);
    } else {
        error_log("CTA Error: Failed to insert message");
        throw new Exception("Failed to save message");
    }

} catch (PDOException $e) {
    error_log("CTA PDO Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error. Please try again later.'
    ]);
} catch (Exception $e) {
    error_log("CTA General Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to send message. Please try again.'
    ]);
}
?>