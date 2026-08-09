<?php
// =============================================
// DELETE TOUR PACKAGE - AJAX HANDLER
// =============================================

// Disable error display
error_reporting(0);
ini_set('display_errors', 0);

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
    $id = intval($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid package ID'
        ]);
        exit();
    }

    // Get package details
    $stmt = $pdo->prepare("SELECT * FROM tour_packages WHERE id = ?");
    $stmt->execute([$id]);
    $package = $stmt->fetch();

    if (!$package) {
        echo json_encode([
            'success' => false,
            'message' => 'Package not found'
        ]);
        exit();
    }

    // Start transaction
    $pdo->beginTransaction();

    // =============================================
    // DELETE MAIN IMAGE
    // =============================================
    if (!empty($package['main_image'])) {
        $mainImagePath = '../' . $package['main_image'];
        $mainImagePath = str_replace('\\', '/', $mainImagePath);

        // Delete the file
        if (file_exists($mainImagePath)) {
            @unlink($mainImagePath);
        }

        // Get the directory path (uploads/tour-packages/main/123456/2024-01-15)
        $mainImageDir = dirname($mainImagePath);

        // Delete the date folder
        if (is_dir($mainImageDir)) {
            $files = scandir($mainImageDir);
            $files = array_diff($files, ['.', '..']);
            foreach ($files as $file) {
                $filePath = $mainImageDir . '/' . $file;
                if (is_file($filePath)) {
                    @unlink($filePath);
                }
            }
            @rmdir($mainImageDir);
        }

        // Delete the number folder (123456)
        $numberFolder = dirname($mainImageDir);
        if (is_dir($numberFolder)) {
            $files = scandir($numberFolder);
            $files = array_diff($files, ['.', '..']);
            if (empty($files)) {
                @rmdir($numberFolder);
            }
        }

        // Delete the type folder (main)
        $typeFolder = dirname($numberFolder);
        if (is_dir($typeFolder)) {
            $files = scandir($typeFolder);
            $files = array_diff($files, ['.', '..']);
            if (empty($files)) {
                @rmdir($typeFolder);
            }
        }
    }

    // =============================================
    // DELETE GALLERY IMAGES
    // =============================================
    $galleryImages = json_decode($package['gallery_images'], true) ?: [];

    if (!empty($galleryImages)) {
        $galleryPaths = [];

        foreach ($galleryImages as $img) {
            $imgPath = '../' . $img;
            $imgPath = str_replace('\\', '/', $imgPath);

            // Delete the file
            if (file_exists($imgPath)) {
                @unlink($imgPath);
            }

            // Store parent directory to delete later
            $imgDir = dirname($imgPath);
            if (!in_array($imgDir, $galleryPaths)) {
                $galleryPaths[] = $imgDir;
            }
        }

        // Delete the date folder
        foreach ($galleryPaths as $path) {
            if (is_dir($path)) {
                $files = scandir($path);
                $files = array_diff($files, ['.', '..']);
                foreach ($files as $file) {
                    $filePath = $path . '/' . $file;
                    if (is_file($filePath)) {
                        @unlink($filePath);
                    }
                }
                @rmdir($path);
            }
        }

        // Delete the number folder for gallery
        if (!empty($galleryPaths)) {
            $firstPath = $galleryPaths[0];
            $numberFolder = dirname($firstPath);
            if (is_dir($numberFolder)) {
                $files = scandir($numberFolder);
                $files = array_diff($files, ['.', '..']);
                if (empty($files)) {
                    @rmdir($numberFolder);
                }
            }

            // Delete the type folder (gallery)
            $typeFolder = dirname($numberFolder);
            if (is_dir($typeFolder)) {
                $files = scandir($typeFolder);
                $files = array_diff($files, ['.', '..']);
                if (empty($files)) {
                    @rmdir($typeFolder);
                }
            }
        }
    }

    // =============================================
    // DELETE FEATURE ICONS
    // =============================================
    $features = json_decode($package['features'], true) ?: [];

    if (!empty($features)) {
        $featureIconPaths = [];

        foreach ($features as $feature) {
            if (!empty($feature['icon'])) {
                $iconPath = '../' . $feature['icon'];
                $iconPath = str_replace('\\', '/', $iconPath);

                // Delete the file
                if (file_exists($iconPath)) {
                    @unlink($iconPath);
                }

                // Store parent directory
                $iconDir = dirname($iconPath);
                if (!in_array($iconDir, $featureIconPaths)) {
                    $featureIconPaths[] = $iconDir;
                }
            }
        }

        // Delete feature icon folders
        foreach ($featureIconPaths as $path) {
            if (is_dir($path)) {
                $files = scandir($path);
                $files = array_diff($files, ['.', '..']);
                foreach ($files as $file) {
                    $filePath = $path . '/' . $file;
                    if (is_file($filePath)) {
                        @unlink($filePath);
                    }
                }
                @rmdir($path);
            }
        }

        // Delete the number folder for features
        if (!empty($featureIconPaths)) {
            $firstPath = $featureIconPaths[0];
            $numberFolder = dirname($firstPath);
            if (is_dir($numberFolder)) {
                $files = scandir($numberFolder);
                $files = array_diff($files, ['.', '..']);
                if (empty($files)) {
                    @rmdir($numberFolder);
                }
            }

            // Delete the type folder (features)
            $typeFolder = dirname($numberFolder);
            if (is_dir($typeFolder)) {
                $files = scandir($typeFolder);
                $files = array_diff($files, ['.', '..']);
                if (empty($files)) {
                    @rmdir($typeFolder);
                }
            }
        }
    }

    // =============================================
    // DELETE TOUR PACKAGES PARENT FOLDER
    // =============================================
    $tourPackagesFolder = '../uploads/tour-packages';
    $tourPackagesFolder = str_replace('\\', '/', $tourPackagesFolder);

    if (is_dir($tourPackagesFolder)) {
        $subFolders = scandir($tourPackagesFolder);
        $subFolders = array_diff($subFolders, ['.', '..']);

        // Check if all subfolders are empty
        $allEmpty = true;
        foreach ($subFolders as $folder) {
            $folderPath = $tourPackagesFolder . '/' . $folder;
            if (is_dir($folderPath)) {
                $files = scandir($folderPath);
                $files = array_diff($files, ['.', '..']);
                if (!empty($files)) {
                    $allEmpty = false;
                    break;
                }
            }
        }

        if ($allEmpty && empty($subFolders)) {
            @rmdir($tourPackagesFolder);
        }
    }

    // =============================================
    // DELETE FROM DATABASE
    // =============================================
    $stmt = $pdo->prepare("DELETE FROM tour_packages WHERE id = ?");
    $stmt->execute([$id]);

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Package and all associated images deleted successfully!'
    ]);
} catch (PDOException $e) {
    // Rollback transaction on error
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Database error in delete-tour-package.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Error in delete-tour-package.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
