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

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM travel_bookings WHERE id = ?");
    $stmt->execute([$id]);
    $booking = $stmt->fetch();

    if (!$booking) {
        echo json_encode(['success' => false, 'message' => 'Booking not found']);
        exit();
    }

    $pdo->beginTransaction();

    // =============================================
    // DELETE PROVIDE ICONS
    // =============================================
    $whatWeProvide = json_decode($booking['what_we_provide'], true) ?: [];
    
    if (!empty($whatWeProvide)) {
        foreach ($whatWeProvide as $item) {
            if (!empty($item['icon'])) {
                // Clean the path - remove APP_URL if present
                $iconPath = str_replace(APP_URL, '', $item['icon']);
                $iconPath = str_replace('http://localhost/st&t_admin_panel/', '', $iconPath);
                $fullPath = '../' . $iconPath;
                
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }
        }
        
        // Delete empty folders for provide icons
        $provideFolder = '../uploads/travel-bookings/provide-icons';
        if (is_dir($provideFolder)) {
            $subFolders = scandir($provideFolder);
            $subFolders = array_diff($subFolders, ['.', '..']);
            
            foreach ($subFolders as $folder) {
                $folderPath = $provideFolder . '/' . $folder;
                if (is_dir($folderPath)) {
                    $dateFolders = scandir($folderPath);
                    $dateFolders = array_diff($dateFolders, ['.', '..']);
                    foreach ($dateFolders as $dateFolder) {
                        $dateFolderPath = $folderPath . '/' . $dateFolder;
                        if (is_dir($dateFolderPath)) {
                            $files = scandir($dateFolderPath);
                            $files = array_diff($files, ['.', '..']);
                            foreach ($files as $file) {
                                $filePath = $dateFolderPath . '/' . $file;
                                if (is_file($filePath)) {
                                    @unlink($filePath);
                                }
                            }
                            @rmdir($dateFolderPath);
                        }
                    }
                    @rmdir($folderPath);
                }
            }
            @rmdir($provideFolder);
        }
    }

    // =============================================
    // DELETE FROM DATABASE
    // =============================================
    $stmt = $pdo->prepare("DELETE FROM travel_bookings WHERE id = ?");
    $stmt->execute([$id]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Booking and all associated images deleted successfully!'
    ]);

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Database error in delete-travel-booking.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Error in delete-travel-booking.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>