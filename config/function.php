<?php

/**
 * Common Functions File
 * Contains reusable functions for file uploads, etc.
 */

// =============================================
// FILE UPLOAD FUNCTIONS
// =============================================

/**
 * Create folder structure for uploads
 * @param string $basePath - Base path for uploads
 * @param string $folderName - Folder name (e.g., 'car-rental', 'tour')
 * @return string|false - Created folder path or false on error
 */
function createUploadFolder($basePath = 'uploads', $folderName = 'car-rental')
{
    // Generate random number
    $randomNumber = rand(100000, 999999);

    // Get current date
    $date = date('Y-m-d');

    // Build folder path
    $folderPath = $basePath . '/' . $folderName . '/' . $randomNumber . '/' . $date;

    // Create folder recursively
    if (!file_exists($folderPath)) {
        if (mkdir($folderPath, 0777, true)) {
            return $folderPath;
        }
        return false;
    }
    return $folderPath;
}

/**
 * Upload single image
 * @param array $file - $_FILES array for the image
 * @param string $uploadPath - Destination path
 * @param string $fileName - Name for the file (optional)
 * @return string|false - Uploaded file path or false on error
 */
function uploadImage($file, $uploadPath, $fileName = null)
{
    // Check if file was uploaded successfully
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        return false;
    }

    // Validate file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return false;
    }

    // Generate file name
    if ($fileName === null) {
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
    }

    // Ensure upload path exists
    if (!file_exists($uploadPath)) {
        if (!mkdir($uploadPath, 0777, true)) {
            return false;
        }
    }

    // Full path
    $fullPath = $uploadPath . '/' . $fileName;

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $fullPath)) {
        return $fullPath;
    }

    return false;
}

/**
 * Upload multiple images
 * @param array $files - $_FILES array for multiple images
 * @param string $uploadPath - Destination path
 * @return array|false - Array of uploaded file paths or false on error
 */
function uploadMultipleImages($files, $uploadPath)
{
    $uploadedFiles = [];

    // Check if files array is valid
    if (!isset($files) || empty($files['name'][0])) {
        return false;
    }

    // Ensure upload path exists
    if (!file_exists($uploadPath)) {
        if (!mkdir($uploadPath, 0777, true)) {
            return false;
        }
    }

    // Process each file
    $fileCount = count($files['name']);
    for ($i = 0; $i < $fileCount; $i++) {
        // Check for errors
        if ($files['error'][$i] !== UPLOAD_ERR_OK) {
            continue;
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($files['type'][$i], $allowedTypes)) {
            continue;
        }

        // Validate file size (max 5MB)
        if ($files['size'][$i] > 5 * 1024 * 1024) {
            continue;
        }

        // Generate file name
        $extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
        $fileName = time() . '_' . bin2hex(random_bytes(8)) . '_' . $i . '.' . $extension;

        // Full path
        $fullPath = $uploadPath . '/' . $fileName;

        // Move uploaded file
        if (move_uploaded_file($files['tmp_name'][$i], $fullPath)) {
            $uploadedFiles[] = $fullPath;
        }
    }

    return !empty($uploadedFiles) ? $uploadedFiles : false;
}

/**
 * Get file path relative to uploads folder
 * @param string $fullPath - Full file path
 * @param string $basePath - Base path to remove
 * @return string - Relative path
 */
function getRelativePath($fullPath, $basePath = 'uploads')
{
    $basePath = rtrim($basePath, '/');
    $fullPath = str_replace('\\', '/', $fullPath);
    $basePath = str_replace('\\', '/', $basePath);

    if (strpos($fullPath, $basePath) !== false) {
        $relativePath = substr($fullPath, strpos($fullPath, $basePath));
        return $relativePath;
    }
    return $fullPath;
}

/**
 * Delete a folder and all its contents recursively
 * @param string $folderPath - Path to folder
 * @return bool - Success or failure
 */
function deleteFolder($folderPath) {
    if (!file_exists($folderPath)) {
        return false;
    }
    
    $files = array_diff(scandir($folderPath), ['.', '..']);
    foreach ($files as $file) {
        $filePath = $folderPath . '/' . $file;
        if (is_dir($filePath)) {
            deleteFolder($filePath);
        } else {
            @unlink($filePath);
        }
    }
    return @rmdir($folderPath);
}


// =============================================
// CURRENCY FUNCTIONS
// =============================================

/**
 * Get currency symbol based on currency code
 * @param string $currencyCode - Currency code (USD, EUR, INR, etc.)
 * @return string - Currency symbol
 */
function getCurrencySymbol($currencyCode = 'USD') {
    $symbols = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'INR' => '₹',
        'AUD' => 'A$',
        'CAD' => 'C$',
        'JPY' => '¥',
        'CNY' => '¥',
        'KRW' => '₩',
        'RUB' => '₽',
        'BRL' => 'R$',
        'ZAR' => 'R',
        'AED' => 'د.إ',
        'SAR' => '﷼',
        'SGD' => 'S$',
        'MYR' => 'RM',
        'THB' => '฿',
        'VND' => '₫',
        'PHP' => '₱',
        'IDR' => 'Rp',
        'PKR' => '₨',
        'BDT' => '৳',
        'LKR' => 'Rs',
        'NPR' => 'Rs',
        'EGP' => 'E£',
        'NGN' => '₦',
        'KES' => 'KSh',
        'TZS' => 'TSh',
        'UGX' => 'USh',
        'GHS' => 'GH₵',
        'MXN' => '$',
        'COP' => '$',
        'ARS' => '$',
        'CLP' => '$',
        'PEN' => 'S/',
        'BOB' => 'Bs',
        'UYU' => '$U',
        'PYG' => '₲',
        'VES' => 'Bs.S',
    ];
    
    return $symbols[$currencyCode] ?? '$';
}

/**
 * Format price with currency symbol
 * @param float $price - Price amount
 * @param string $currencyCode - Currency code (USD, EUR, INR, etc.)
 * @param bool $showDecimals - Show decimal places
 * @return string - Formatted price with currency
 */
function formatPrice($price, $currencyCode = 'USD', $showDecimals = true) {
    $symbol = getCurrencySymbol($currencyCode);
    $decimals = $showDecimals ? 2 : 0;
    $formatted = number_format($price, $decimals);
    return $symbol . $formatted;
}

/**
 * Get currency code from settings
 * @param PDO $pdo - Database connection
 * @return string - Currency code
 */
function getCurrencyCode($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT currency FROM settings WHERE id = 1");
        $stmt->execute();
        $settings = $stmt->fetch();
        return $settings['currency'] ?? 'USD';
    } catch (Exception $e) {
        return 'USD';
    }
}