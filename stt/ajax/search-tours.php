<?php
/**
 * AJAX Search Endpoint for Tours
 */

require_once '../config/config.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

 $query = isset($_POST['query']) ? trim($_POST['query']) : '';

if (strlen($query) < 2) {
    echo json_encode(['results' => [], 'count' => 0, 'query' => $query]);
    exit;
}

try {
    $like = '%' . $query . '%';

    $sql = "
        SELECT 
            id,
            package_id,
            package_name,
            package_type,
            short_description,
            main_image,
            price,
            days_count
        FROM tour_packages 
        WHERE status = 'active' 
          AND (
              package_name LIKE :q1 
              OR short_description LIKE :q2
              OR package_type LIKE :q3
          )
        ORDER BY 
            CASE 
                WHEN package_name LIKE :q4 THEN 1
                WHEN package_type LIKE :q5 THEN 2
                ELSE 3
            END
        LIMIT 8
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':q1', $like, PDO::PARAM_STR);
    $stmt->bindValue(':q2', $like, PDO::PARAM_STR);
    $stmt->bindValue(':q3', $like, PDO::PARAM_STR);
    $stmt->bindValue(':q4', $like, PDO::PARAM_STR);
    $stmt->bindValue(':q5', $like, PDO::PARAM_STR);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as &$tour) {
        if (!empty($tour['main_image'])) {
            $tour['image_url'] = (strpos($tour['main_image'], 'http') === 0)
                ? $tour['main_image']
                : ADMIN_URL . $tour['main_image'];
        } else {
            $tour['image_url'] = SITE_URL . 'assets/images/default-tour.jpg';
        }

        $days = (int)($tour['days_count'] ?? 1);
        $nights = max(0, $days - 1);
        $tour['duration'] = $days . 'D / ' . $nights . 'N';

        $price = (float)($tour['price'] ?? 0);
        $tour['formatted_price'] = $price > 0
            ? '₹' . number_format($price, 2)
            : 'Contact for Price';
    }

    echo json_encode([
        'results' => $results,
        'count'   => count($results),
        'query'   => $query
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'results' => [],
        'count'   => 0,
        'query'   => $query,
        'error'   => $e->getMessage()
    ]);
}

exit;