<?php

/**
 * Functions file for database operations
 * Include this file in your pages to access database functions
 */

// Include database configuration
require_once __DIR__ . '/config.php';

/**
 * Get all settings from the database
 * @param PDO $pdo Database connection
 * @return array Settings data
 */
function getSettings($pdo)
{
    try {
        $stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching settings: " . $e->getMessage());
        return [];
    }
}

/**
 * Get hero image URL
 * The image is stored in admin panel uploads folder
 * @param PDO $pdo Database connection
 * @return string Full hero image URL
 */
function getHeroImage($pdo)
{
    $settings = getSettings($pdo);

    if (!empty($settings['hero_image'])) {
        // The path is stored as: uploads/settings/hero/469955/2026-08-11/filename.jpg
        // We need to prepend the admin URL
        return ADMIN_URL . $settings['hero_image'];
    }

    // Default fallback image (in the frontend)
    return SITE_URL . 'assets/images/default-hero.jpg';
}

/**
 * Get website logo URL
 * @param PDO $pdo Database connection
 * @return string Logo URL
 */
function getWebsiteLogo($pdo)
{
    $settings = getSettings($pdo);

    if (!empty($settings['website_logo'])) {
        return ADMIN_URL . $settings['website_logo'];
    }

    return SITE_URL . 'assets/images/default-logo.png';
}

/**
 * Get favicon URL
 * @param PDO $pdo Database connection
 * @return string Favicon URL
 */
function getFavicon($pdo)
{
    $settings = getSettings($pdo);

    if (!empty($settings['favicon'])) {
        return ADMIN_URL . $settings['favicon'];
    }

    return SITE_URL . 'assets/images/favicon.ico';
}

/**
 * Get panel logo URL
 * @param PDO $pdo Database connection
 * @return string Panel logo URL
 */
function getPanelLogo($pdo)
{
    $settings = getSettings($pdo);

    if (!empty($settings['panel_logo'])) {
        return ADMIN_URL . $settings['panel_logo'];
    }

    return SITE_URL . 'assets/images/default-panel-logo.png';
}

/**
 * Get image URL from admin panel
 * @param PDO $pdo Database connection
 * @param string $imagePath The image path from database
 * @param string $default Default image path (optional)
 * @return string Full image URL
 */
function getImageUrl($pdo, $imagePath, $default = null)
{
    if (!empty($imagePath)) {
        return ADMIN_URL . $imagePath;
    }

    if ($default) {
        return SITE_URL . $default;
    }

    return '';
}

/**
 * Get site name
 * @param PDO $pdo Database connection
 * @return string Site name
 */
function getSiteName($pdo)
{
    $settings = getSettings($pdo);
    return !empty($settings['site_name']) ? $settings['site_name'] : 'Travelio';
}

/**
 * Get site tagline
 * @param PDO $pdo Database connection
 * @return string Site tagline
 */
function getSiteTagline($pdo)
{
    $settings = getSettings($pdo);
    return !empty($settings['site_tagline']) ? $settings['site_tagline'] : '';
}

/**
 * Get contact email
 * @param PDO $pdo Database connection
 * @return string Contact email
 */
function getContactEmail($pdo)
{
    $settings = getSettings($pdo);
    return !empty($settings['contact_email']) ? $settings['contact_email'] : 'admin@example.com';
}

/**
 * Get contact phone
 * @param PDO $pdo Database connection
 * @return string Contact phone
 */
function getContactPhone($pdo)
{
    $settings = getSettings($pdo);
    return !empty($settings['contact_phone']) ? $settings['contact_phone'] : '+1234567890';
}

/**
 * Get address
 * @param PDO $pdo Database connection
 * @return string Address
 */
function getAddress($pdo)
{
    $settings = getSettings($pdo);
    return !empty($settings['address']) ? $settings['address'] : '';
}

/**
 * Get currency
 * @param PDO $pdo Database connection
 * @return string Currency
 */
function getCurrency($pdo)
{
    $settings = getSettings($pdo);
    return !empty($settings['currency']) ? $settings['currency'] : 'USD';
}

/**
 * Get site title
 * @param PDO $pdo Database connection
 * @return string Site title
 */
function getSiteTitle($pdo)
{
    $settings = getSettings($pdo);
    return !empty($settings['site_title']) ? $settings['site_title'] : 'Tour Admin Panel';
}

/**
 * Get footer text
 * @param PDO $pdo Database connection
 * @return string Footer text
 */
function getFooterText($pdo)
{
    $settings = getSettings($pdo);
    return !empty($settings['footer_text']) ? $settings['footer_text'] : '© 2024 Tour Admin. All rights reserved.';
}

/**
 * Get timezone
 * @param PDO $pdo Database connection
 * @return string Timezone
 */
function getTimezone($pdo)
{
    $settings = getSettings($pdo);
    return !empty($settings['timezone']) ? $settings['timezone'] : 'Asia/Kolkata';
}

/**
 * Get social links
 * @param PDO $pdo Database connection
 * @return array Social links
 */
function getSocialLinks($pdo)
{
    $settings = getSettings($pdo);

    if (!empty($settings['social_links'])) {
        $links = json_decode($settings['social_links'], true);
        if (is_array($links)) {
            return $links;
        }
    }

    return [
        'facebook' => '#',
        'twitter' => '#',
        'instagram' => '#',
        'linkedin' => '#'
    ];
}

/**
 * Get a specific setting value
 * @param PDO $pdo Database connection
 * @param string $key Setting key
 * @param mixed $default Default value
 * @return mixed Setting value
 */
function getSetting($pdo, $key, $default = null)
{
    $settings = getSettings($pdo);
    return isset($settings[$key]) ? $settings[$key] : $default;
}

/**
 * Get all tour packages
 * @param PDO $pdo Database connection
 * @param int $limit Optional limit
 * @param string $status Optional status filter
 * @return array Tour packages data
 */
function getTourPackages($pdo, $limit = null, $status = 'active')
{
    try {
        $sql = "SELECT * FROM tour_packages WHERE status = :status ORDER BY created_at DESC";
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['status' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching tour packages: " . $e->getMessage());
        return [];
    }
}

/**
 * Get single tour package by ID or package_id
 * @param PDO $pdo Database connection
 * @param string|int $id Package ID or package_id
 * @return array|null Tour package data
 */
function getTourPackage($pdo, $id)
{
    try {
        $sql = "SELECT * FROM tour_packages WHERE id = :id OR package_id = :package_id LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id, 'package_id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching tour package: " . $e->getMessage());
        return null;
    }
}

/**
 * Get tour packages by category
 * @param PDO $pdo Database connection
 * @param string $category Category name
 * @param int $limit Optional limit
 * @return array Tour packages data
 */
function getTourPackagesByCategory($pdo, $category, $limit = null)
{
    try {
        $sql = "SELECT * FROM tour_packages WHERE package_type = :category AND status = 'active' ORDER BY created_at DESC";
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['category' => $category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching tour packages by category: " . $e->getMessage());
        return [];
    }
}

/**
 * Get tour package main image URL
 * @param PDO $pdo Database connection
 * @param string $imagePath Image path from database
 * @return string Full image URL
 */
function getTourMainImage($pdo, $imagePath)
{
    if (!empty($imagePath)) {
        return ADMIN_URL . $imagePath;
    }
    return SITE_URL . 'assets/images/default-tour.jpg';
}

/**
 * Get tour package gallery images
 * @param PDO $pdo Database connection
 * @param string $galleryJson JSON string of gallery images
 * @return array Array of full image URLs
 */
function getTourGalleryImages($pdo, $galleryJson)
{
    $images = [];
    if (!empty($galleryJson)) {
        $galleryArray = json_decode($galleryJson, true);
        if (is_array($galleryArray)) {
            foreach ($galleryArray as $image) {
                $images[] = ADMIN_URL . $image;
            }
        }
    }
    return $images;
}

/**
 * Get tour features
 * @param PDO $pdo Database connection
 * @param string $featuresJson JSON string of features
 * @return array Array of features with icons
 */
function getTourFeatures($pdo, $featuresJson)
{
    $features = [];
    if (!empty($featuresJson)) {
        $featuresArray = json_decode($featuresJson, true);
        if (is_array($featuresArray)) {
            foreach ($featuresArray as $feature) {
                $feature['icon_url'] = !empty($feature['icon']) ? ADMIN_URL . $feature['icon'] : '';
                $features[] = $feature;
            }
        }
    }
    return $features;
}

/**
 * Get tour itinerary
 * @param PDO $pdo Database connection
 * @param string $itineraryJson JSON string of itinerary
 * @return array Array of itinerary days
 */
function getTourItinerary($pdo, $itineraryJson)
{
    $itinerary = [];
    if (!empty($itineraryJson)) {
        $itineraryArray = json_decode($itineraryJson, true);
        if (is_array($itineraryArray)) {
            $itinerary = $itineraryArray;
        }
    }
    return $itinerary;
}

/**
 * Get formatted tour price
 * @param float $price Tour price
 * @param string $currency Currency symbol
 * @return string Formatted price
 */
function formatTourPrice($price, $currency = '₹')
{
    if (empty($price)) return 'Contact for Price';
    return $currency . number_format($price, 2);
}

/**
 * Get tour duration text
 * @param int $days Number of days
 * @param int $nights Number of nights (optional)
 * @return string Duration text
 */
function getTourDuration($days, $nights = null)
{
    if ($nights === null) {
        $nights = $days - 1;
    }
    return $days . 'D / ' . $nights . 'N';
}


/**
 * Get all package type images
 * @param PDO $pdo Database connection
 * @return array Package type images data
 */
function getPackageTypeImages($pdo)
{
    try {
        $stmt = $pdo->query("SELECT * FROM package_type_images ORDER BY created_at ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching package type images: " . $e->getMessage());
        return [];
    }
}

/**
 * Get package type image URL
 * @param PDO $pdo Database connection
 * @param string $imagePath Image path from database
 * @return string Full image URL
 */
function getPackageTypeImageUrl($pdo, $imagePath)
{
    if (!empty($imagePath)) {
        return ADMIN_URL . $imagePath;
    }
    // Return empty string if no image - don't use default
    return '';
}

/**
 * Get categories with images from package_type_images table
 * Only returns categories that have actual images uploaded
 * @param PDO $pdo Database connection
 * @param int $limit Optional limit
 * @return array Categories with full image URLs (only those with images)
 */
function getCategoriesWithPackageImages($pdo, $limit = null)
{
    try {
        // Only fetch categories that have images (image is NOT NULL and NOT empty)
        $sql = "SELECT * FROM package_type_images WHERE image IS NOT NULL AND image != '' ORDER BY created_at ASC";

        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }

        $stmt = $pdo->query($sql);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Process categories - only those with images will be here
        foreach ($categories as &$category) {
            $category['image_url'] = ADMIN_URL . $category['image'];
            $category['slug'] = strtolower(str_replace(' ', '-', $category['name']));
        }

        return $categories;
    } catch (PDOException $e) {
        error_log("Error fetching package type images: " . $e->getMessage());
        return []; // Return empty array, NOT fallback
    }
}

/**
 * Get default image URL for category based on name
 * @param string $categoryName Category name
 * @return string Default image URL
 */
function getDefaultCategoryImage($categoryName)
{
    // Map category names to default images
    $defaultImages = [
        'Adventure' => 'https://images.unsplash.com/photo-1530549387789-4c1017266635?w=800&h=1100&fit=crop',
        'Beach' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&h=1100&fit=crop',
        'Cultural' => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=800&h=1100&fit=crop',
        'Wildlife' => 'https://images.unsplash.com/photo-1518186285589-2f7649de83e0?w=800&h=1100&fit=crop',
        'City Break' => 'https://images.unsplash.com/photo-1480714378408-67cf0d13bc1b?w=800&h=1100&fit=crop',
        'Luxury' => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=800&h=1100&fit=crop',
        'Family' => 'https://images.unsplash.com/photo-1531973576160-7127cd2471d4?w=800&h=1100&fit=crop',
        'Honeymoon' => 'https://images.unsplash.com/photo-1510414842594-a61c69b5ae57?w=800&h=1100&fit=crop',
        'Group' => 'https://images.unsplash.com/photo-1539635278303-d4002c07eae3?w=800&h=1100&fit=crop'
    ];

    return isset($defaultImages[$categoryName]) ? $defaultImages[$categoryName] : 'https://picsum.photos/seed/' . strtolower($categoryName) . '/800/1100';
}

/**
 * Get single package type by name or ID
 * @param PDO $pdo Database connection
 * @param string $name Category name
 * @return array|null Category data
 */
function getPackageTypeByName($pdo, $name)
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM package_type_images WHERE name = :name OR type_id = :type_id LIMIT 1");
        $stmt->execute(['name' => $name, 'type_id' => $name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching package type: " . $e->getMessage());
        return null;
    }
}

/**
 * Get tours by package type (category)
 * @param PDO $pdo Database connection
 * @param string $category Category name
 * @param int $limit Optional limit
 * @return array Tour packages
 */
function getToursByPackageType($pdo, $category, $limit = null)
{
    try {
        $sql = "SELECT * FROM tour_packages WHERE package_type = :category AND status = 'active' ORDER BY created_at DESC";
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['category' => $category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching tours by package type: " . $e->getMessage());
        return [];
    }
}

/**
 * Get all active offers (not expired)
 * @param PDO $pdo Database connection
 * @return array Active offers
 */
function getActiveOffers($pdo)
{
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM offers 
            WHERE status = 'active' 
              AND end_date >= CURDATE() 
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching offers: " . $e->getMessage());
        return [];
    }
}

/**
 * Get offer discount display text
 * @param array $offer Single offer row
 * @return string Formatted discount text
 */
function getOfferDiscountText($offer)
{
    $currency = '$';
    if ($offer['discount_type'] === 'fixed') {
        return $currency . number_format((float)$offer['discount_value'], 0) . ' OFF';
    }
    return number_format((float)$offer['discount_value'], 0) . '% OFF';
}

/**
 * Get offer main image URL
 * @param PDO $pdo Database connection
 * @param string $imagePath Image path from database
 * @return string Full image URL
 */
function getOfferMainImage($pdo, $imagePath)
{
    if (!empty($imagePath)) {
        return ADMIN_URL . $imagePath;
    }
    return SITE_URL . 'assets/images/default-offer.jpg';
}

/**
 * Get tour package names for an offer
 * @param PDO $pdo Database connection
 * @param string $tourPackagesJson JSON array of tour package IDs
 * @return array Tour package names
 */
function getOfferTourNames($pdo, $tourPackagesJson)
{
    $names = [];
    if (!empty($tourPackagesJson)) {
        $ids = json_decode($tourPackagesJson, true);
        if (is_array($ids)) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            try {
                $stmt = $pdo->prepare("SELECT title FROM tour_packages WHERE id IN ($placeholders) AND status = 'active'");
                $stmt->execute(array_values($ids));
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    $names[] = $row['title'];
                }
            } catch (PDOException $e) {
                error_log("Error fetching offer tour names: " . $e->getMessage());
            }
        }
    }
    return $names;
}


/**
 * Get active offer applicable to a specific tour package
 *
 * @param PDO $pdo
 * @param int|string $tourId
 * @return array|null
 */
function getTourOffer($pdo, $tourId)
{
    try {
        $offers = getActiveOffers($pdo);

        foreach ($offers as $offer) {

            if (empty($offer['tour_packages'])) {
                continue;
            }

            $tourIds = json_decode($offer['tour_packages'], true);

            if (!is_array($tourIds)) {
                continue;
            }

            // Match either database id or package_id
            if (
                in_array((string)$tourId, array_map('strval', $tourIds), true)
            ) {
                return $offer;
            }
        }

        return null;

    } catch (Exception $e) {
        error_log("Error finding tour offer: " . $e->getMessage());
        return null;
    }
}


/**
 * Calculate final tour price after offer
 *
 * @param float $price
 * @param array|null $offer
 * @return array
 */
function calculateTourOfferPrice($price, $offer = null)
{
    $price = (float)$price;

    $result = [
        'original_price' => $price,
        'discount_amount' => 0,
        'final_price' => $price,
        'discount_text' => '',
        'has_offer' => false
    ];

    if ($price <= 0 || empty($offer)) {
        return $result;
    }

    $discountType  = $offer['discount_type'] ?? '';
    $discountValue = (float)($offer['discount_value'] ?? 0);

    if ($discountValue <= 0) {
        return $result;
    }

    if ($discountType === 'percentage') {

        $discountAmount = ($price * $discountValue) / 100;

        $result['discount_amount'] = $discountAmount;
        $result['final_price'] = max(0, $price - $discountAmount);
        $result['discount_text'] = number_format($discountValue, 0) . '% OFF';
        $result['has_offer'] = true;

    } elseif ($discountType === 'fixed') {

        $discountAmount = min($price, $discountValue);

        $result['discount_amount'] = $discountAmount;
        $result['final_price'] = max(0, $price - $discountAmount);
        $result['discount_text'] = '₹' . number_format($discountAmount, 0) . ' OFF';
        $result['has_offer'] = true;
    }

    return $result;
}

/**
 * Get travel packages with car details
 */
function getTravelPackages($pdo, $limit = 6)
{
    try {

        $limit = (int) $limit;

        if ($limit <= 0) {
            $limit = 6;
        }

        $sql = "
            SELECT
                tb.id,
                tb.booking_id,

                tb.car_id,

                tb.car_name,
                tb.car_type,
                tb.seat_count,

                tb.days,
                tb.per_day_price,
                tb.per_km_charge,
                tb.total_price,
                tb.total_distance,

                tb.stops,
                tb.what_we_provide,
                tb.status,

                /* CAR MASTER DETAILS */
                cr.car_image,
                cr.car_model,
                cr.car_brand,
                cr.fuel_type,
                cr.transmission,
                cr.seating_capacity,
                cr.ac_available

            FROM travel_bookings tb

            LEFT JOIN car_rentals cr
                ON cr.id = tb.car_id

            ORDER BY tb.created_at DESC

            LIMIT $limit
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {

        error_log(
            "Travel packages error: " .
            $e->getMessage()
        );

        return [];
    }
}

/**
 * Get published testimonials
 *
 * @param PDO $pdo
 * @param int $limit
 * @return array
 */
function getTestimonials($pdo, $limit = 9)
{
    try {

        $limit = (int) $limit;

        if ($limit <= 0) {
            $limit = 9;
        }

        $sql = "
            SELECT
                id,
                name,
                logo,
                testimonial,
                status,
                created_at
            FROM testimonials
            WHERE status = 'publish'
            ORDER BY created_at DESC
            LIMIT $limit
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);

        /*
         * Add full image URL
         */
        foreach ($testimonials as &$testimonial) {

            if (!empty($testimonial['logo'])) {
                $testimonial['logo_url'] =
                    ADMIN_URL . $testimonial['logo'];
            } else {
                $testimonial['logo_url'] =
                    SITE_URL . 'assets/images/default-user.png';
            }
        }

        return $testimonials;

    } catch (PDOException $e) {

        error_log(
            "Testimonials error: " .
            $e->getMessage()
        );

        return [];
    }
}

/**
 * Search tour packages by name, description, or type
 */
function searchTourPackages($pdo, $query, $limit = 10)
{
    try {
        $query = trim($query);
        if (empty($query)) {
            return [];
        }

        $sql = "
            SELECT 
                id,
                package_id,
                package_name,
                package_type,
                short_description,
                main_image,
                price,
                days_count,
                status
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
                END,
                created_at DESC
            LIMIT :lim
        ";

        $like = '%' . $query . '%';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':q1',  $like, PDO::PARAM_STR);
        $stmt->bindValue(':q2',  $like, PDO::PARAM_STR);
        $stmt->bindValue(':q3',  $like, PDO::PARAM_STR);
        $stmt->bindValue(':q4',  $like, PDO::PARAM_STR);
        $stmt->bindValue(':q5',  $like, PDO::PARAM_STR);
        $stmt->bindValue(':lim', (int) $limit, PDO::PARAM_INT);
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

            $tour['tour_link']       = SITE_URL . 'tour.php?id=' . $tour['id'];
            $tour['duration']        = getTourDuration($tour['days_count'] ?? 0);
            $tour['formatted_price'] = formatTourPrice($tour['price'] ?? 0, '₹');
        }

        return $results;

    } catch (PDOException $e) {
        error_log("Search error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get all active tour names (lightweight)
 */
function getAllTourNames($pdo)
{
    try {
        $stmt = $pdo->query("
            SELECT id, package_id, package_name, package_type
            FROM tour_packages 
            WHERE status = 'active'
            ORDER BY package_name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get available car rentals
 *
 * @param PDO $pdo
 * @param int|null $limit
 * @return array
 */
function getCarRentals($pdo, $limit = null)
{
    try {

        $sql = "
            SELECT
                id,
                car_name,
                car_model,
                car_brand,
                car_type,
                car_image,
                additional_images,
                per_day_amount,
                per_km_charge,
                fuel_type,
                transmission,
                seating_capacity,
                ac_available,
                description,
                status,
                created_at
            FROM car_rentals
            WHERE status = 'available'
            ORDER BY created_at DESC
        ";

        if ($limit !== null) {
            $limit = max(1, (int)$limit);
            $sql .= " LIMIT " . $limit;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($cars as &$car) {

            $car['image_url'] = !empty($car['car_image'])
                ? ADMIN_URL . $car['car_image']
                : SITE_URL . 'assets/images/default-car.jpg';

            /*
             * car_type can sometimes contain JSON like:
             * ["sedan"]
             */
            $carType = $car['car_type'] ?? '';

            if (!empty($carType)) {

                $decodedType = json_decode($carType, true);

                if (is_array($decodedType)) {
                    $car['display_type'] = implode(', ', $decodedType);
                } else {
                    $car['display_type'] = $carType;
                }

            } else {
                $car['display_type'] = 'Car';
            }

            $car['per_day_amount'] = (float)$car['per_day_amount'];
            $car['per_km_charge'] = (float)$car['per_km_charge'];

            $car['ac_available'] = (int)$car['ac_available'];
            $car['seating_capacity'] = (int)$car['seating_capacity'];
        }

        return $cars;

    } catch (PDOException $e) {

        error_log(
            "Car rentals error: " . $e->getMessage()
        );

        return [];
    }
}


/**
 * Get one available car rental
 *
 * @param PDO $pdo
 * @param int $id
 * @return array|null
 */
function getCarRental($pdo, $id)
{
    try {

        $id = (int)$id;

        if ($id <= 0) {
            return null;
        }

        $stmt = $pdo->prepare("
            SELECT
                id,
                car_name,
                car_model,
                car_brand,
                car_type,
                car_image,
                additional_images,
                per_day_amount,
                per_km_charge,
                fuel_type,
                transmission,
                seating_capacity,
                ac_available,
                description,
                status,
                created_at
            FROM car_rentals
            WHERE id = :id
              AND status = 'available'
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $id
        ]);

        $car = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$car) {
            return null;
        }

        $car['image_url'] = !empty($car['car_image'])
            ? ADMIN_URL . $car['car_image']
            : SITE_URL . 'assets/images/default-car.jpg';

        $carType = $car['car_type'] ?? '';

        $decodedType = json_decode($carType, true);

        if (is_array($decodedType)) {
            $car['display_type'] = implode(', ', $decodedType);
        } else {
            $car['display_type'] = $carType ?: 'Car';
        }

        $car['per_day_amount'] = (float)$car['per_day_amount'];
        $car['per_km_charge'] = (float)$car['per_km_charge'];
        $car['seating_capacity'] = (int)$car['seating_capacity'];
        $car['ac_available'] = (int)$car['ac_available'];

        return $car;

    } catch (PDOException $e) {

        error_log(
            "Single car rental error: " . $e->getMessage()
        );

        return null;
    }
}