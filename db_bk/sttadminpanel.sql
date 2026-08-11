-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 11, 2026 at 08:39 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sttadminpanel`
--

-- --------------------------------------------------------

--
-- Table structure for table `car_rentals`
--

CREATE TABLE `car_rentals` (
  `id` int(11) NOT NULL,
  `car_name` varchar(100) NOT NULL,
  `car_model` varchar(100) DEFAULT NULL,
  `car_brand` varchar(100) DEFAULT NULL,
  `car_type` varchar(50) DEFAULT NULL,
  `car_image` varchar(255) DEFAULT NULL,
  `additional_images` text DEFAULT NULL,
  `per_day_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `per_km_charge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fuel_type` varchar(50) DEFAULT NULL,
  `transmission` varchar(50) DEFAULT NULL,
  `seating_capacity` int(11) DEFAULT 0,
  `ac_available` tinyint(1) DEFAULT 1,
  `description` text DEFAULT NULL,
  `status` enum('available','booked','maintenance') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `car_rentals`
--

INSERT INTO `car_rentals` (`id`, `car_name`, `car_model`, `car_brand`, `car_type`, `car_image`, `additional_images`, `per_day_amount`, `per_km_charge`, `fuel_type`, `transmission`, `seating_capacity`, `ac_available`, `description`, `status`, `created_at`, `updated_at`) VALUES
(11, '11', '11', '11', '', 'uploads/car-rental/454480/2026-08-09/1786301263_ac85027b7b716901.png', NULL, 11.00, 11.00, '', '', 4, 1, 'x xd', 'available', '2026-08-09 18:47:43', '2026-08-09 18:47:43');

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `id` int(11) NOT NULL,
  `offer_code` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL DEFAULT 'percentage',
  `discount_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tour_packages` longtext DEFAULT NULL,
  `main_image` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('active','inactive','expired') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`id`, `offer_code`, `title`, `description`, `discount_type`, `discount_value`, `tour_packages`, `main_image`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`) VALUES
(3, 'OFF001', 'efasfa', '', 'fixed', 11.00, '[\"3\"]', 'uploads/offers/682987/2026-08-09/1786301409_88376bc7de5cd5d3.png', '2026-08-10', '2026-08-26', 'active', '2026-08-09 18:50:09', '2026-08-11 18:39:28');

--
-- Triggers `offers`
--
DELIMITER $$
CREATE TRIGGER `before_insert_offers` BEFORE INSERT ON `offers` FOR EACH ROW BEGIN
    DECLARE next_id INT;
    IF NEW.offer_code IS NULL OR NEW.offer_code = '' THEN
        SELECT IFNULL(MAX(CAST(SUBSTRING(offer_code, 4) AS UNSIGNED)), 0) + 1 INTO next_id 
        FROM offers;
        SET NEW.offer_code = CONCAT('OFF', LPAD(next_id, 3, '0'));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `package_type_images`
--

CREATE TABLE `package_type_images` (
  `id` int(11) NOT NULL,
  `type_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `package_type_images`
--

INSERT INTO `package_type_images` (`id`, `type_id`, `name`, `image`, `created_at`, `updated_at`) VALUES
(2, 'PKT002', 'Beach', 'uploads/package-types/568334/2026-08-11/1786463275_fd9168c07b1672f2.jpeg', '2026-08-11 14:20:00', '2026-08-11 15:47:55'),
(3, 'PKT003', 'Cultural', NULL, '2026-08-11 14:20:00', '2026-08-11 14:20:00'),
(4, 'PKT004', 'Wildlife', NULL, '2026-08-11 14:20:00', '2026-08-11 14:20:00'),
(5, 'PKT005', 'City Break', NULL, '2026-08-11 14:20:00', '2026-08-11 14:20:00'),
(6, 'PKT006', 'Luxury', NULL, '2026-08-11 14:20:00', '2026-08-11 14:20:00'),
(7, 'PKT007', 'Family', NULL, '2026-08-11 14:20:00', '2026-08-11 14:20:00'),
(8, 'PKT008', 'Honeymoon', NULL, '2026-08-11 14:20:00', '2026-08-11 14:20:00'),
(9, 'PKT009', 'Group', NULL, '2026-08-11 14:20:00', '2026-08-11 14:20:00');

--
-- Triggers `package_type_images`
--
DELIMITER $$
CREATE TRIGGER `before_insert_package_type_images` BEFORE INSERT ON `package_type_images` FOR EACH ROW BEGIN
    DECLARE next_id INT;
    IF NEW.type_id IS NULL OR NEW.type_id = '' THEN
        SELECT IFNULL(MAX(CAST(SUBSTRING(type_id, 4) AS UNSIGNED)), 0) + 1 INTO next_id 
        FROM package_type_images;
        SET NEW.type_id = CONCAT('PKT', LPAD(next_id, 3, '0'));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL DEFAULT 1,
  `site_name` varchar(255) DEFAULT 'Tour Admin',
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `currency` varchar(10) DEFAULT 'USD',
  `social_links` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`social_links`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `timezone` varchar(50) DEFAULT 'Asia/Kolkata',
  `site_title` varchar(100) DEFAULT 'Tour Admin Panel',
  `website_logo` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `panel_logo` varchar(255) DEFAULT NULL,
  `hero_image` varchar(255) DEFAULT NULL,
  `site_tagline` varchar(255) DEFAULT NULL,
  `footer_text` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `site_name`, `contact_email`, `contact_phone`, `address`, `currency`, `social_links`, `created_at`, `updated_at`, `timezone`, `site_title`, `website_logo`, `favicon`, `panel_logo`, `hero_image`, `site_tagline`, `footer_text`) VALUES
(1, 'Tour Admin', 'admin@example.com', '+1234567890', '4/44 kadai theru rediplayam\r\nRamanathapuram Addl talk Thanjavur', 'INR', NULL, '2026-08-08 17:30:12', '2026-08-11 12:10:04', 'Asia/Kolkata', 'saran tours and travels', 'uploads/settings/logo/366025/2026-08-11/1786450204_b222c890144a7b05.png', 'uploads/settings/favicon/419185/2026-08-10/1786338297_9719c3854ef4d02f.png', 'uploads/settings/panel-logo/731156/2026-08-10/1786339225_8b6f91d8ff5ff440.png', 'uploads/settings/hero/389910/2026-08-11/1786449852_175fef25c90c6f3e.jpeg', NULL, '© 2024 Tour Admin. All rights reserved.');

-- --------------------------------------------------------

--
-- Table structure for table `tour_packages`
--

CREATE TABLE `tour_packages` (
  `id` int(11) NOT NULL,
  `package_id` varchar(20) NOT NULL,
  `package_name` varchar(255) NOT NULL,
  `package_type` varchar(100) DEFAULT NULL,
  `days_count` int(3) NOT NULL DEFAULT 1,
  `members` longtext DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `itinerary` longtext DEFAULT NULL,
  `features` longtext DEFAULT NULL,
  `main_image` varchar(255) DEFAULT NULL,
  `gallery_images` longtext DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `status` enum('active','inactive','upcoming') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_packages`
--

INSERT INTO `tour_packages` (`id`, `package_id`, `package_name`, `package_type`, `days_count`, `members`, `short_description`, `description`, `itinerary`, `features`, `main_image`, `gallery_images`, `price`, `discount_price`, `status`, `created_at`, `updated_at`) VALUES
(3, 'TRP001', 'asdvsdvsd', 'Beach', 1, '[{\"label\":\"qwsw2\",\"count\":10},{\"label\":\"test\",\"count\":13}]', 'fc', 'dv dfvsf', '{\"day1\":{\"title\":\"fb df\",\"description\":\"fdb df\"}}', '[{\"name\":\"111eedgvsd\",\"icon\":\"http://localhost/st&t_admin_panel/uploads/tour-packages/features/909924/2026-08-11/1786470595_9c2fa9b29a3a7d59_0.png\"}]', 'uploads/tour-packages/main/351794/2026-08-11/1786470595_f89da767666cf969.png', NULL, 299.00, NULL, 'active', '2026-08-11 17:49:55', '2026-08-11 18:37:31');

--
-- Triggers `tour_packages`
--
DELIMITER $$
CREATE TRIGGER `before_insert_tour_packages` BEFORE INSERT ON `tour_packages` FOR EACH ROW BEGIN
    DECLARE next_id INT;
    IF NEW.package_id IS NULL OR NEW.package_id = '' THEN
        SELECT IFNULL(MAX(CAST(SUBSTRING(package_id, 4) AS UNSIGNED)), 0) + 1 INTO next_id 
        FROM tour_packages;
        SET NEW.package_id = CONCAT('TRP', LPAD(next_id, 3, '0'));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `travel_bookings`
--

CREATE TABLE `travel_bookings` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `car_name` varchar(255) NOT NULL,
  `car_type` varchar(100) DEFAULT NULL,
  `seat_count` int(11) NOT NULL DEFAULT 0,
  `days` int(11) NOT NULL DEFAULT 1,
  `per_day_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `per_km_charge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_distance` decimal(10,2) DEFAULT NULL,
  `stops` longtext DEFAULT NULL,
  `what_we_provide` longtext DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `booking_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `travel_bookings`
--

INSERT INTO `travel_bookings` (`id`, `booking_id`, `user_id`, `car_id`, `car_name`, `car_type`, `seat_count`, `days`, `per_day_price`, `per_km_charge`, `total_price`, `total_distance`, `stops`, `what_we_provide`, `status`, `booking_date`, `created_at`, `updated_at`) VALUES
(3, 'TRV0001', 1, 11, '11', 'Sedan', 4, 1, 11.00, 11.00, 7934.21, 720.29, '[{\"pickup\":\"Thanjavur\",\"drop\":\"Kerala, India\",\"pickup_lat\":10.7860267,\"pickup_lng\":79.1381497,\"drop_lat\":10.3528744,\"drop_lng\":76.5120396,\"distance\":291.06673056579,\"price\":3201.73403622369},{\"pickup\":\"\\u0b95\\u0bcb\\u0baf\\u0bae\\u0bcd\\u0baa\\u0bc1\\u0ba4\\u0bcd\\u0ba4\\u0bc2\\u0bb0\\u0bcd \\u0bae\\u0bbe\\u0bb5\\u0b9f\\u0bcd\\u0b9f\\u0bae\\u0bcd, Tamil Nadu, India\",\"drop\":\"Chennai, \\u0b9a\\u0bc6\\u0ba9\\u0bcd\\u0ba9\\u0bc8 \\u0bae\\u0bbe\\u0bb5\\u0b9f\\u0bcd\\u0b9f\\u0bae\\u0bcd, Tamil Nadu, India\",\"pickup_lat\":10.8124445,\"pickup_lng\":77.0796084,\"drop_lat\":13.0836939,\"drop_lng\":80.270186,\"distance\":429.22544706780843,\"price\":4721.479917745893}]', '[{\"name\":\"knln\",\"icon\":\"uploads/travel-bookings/provide-icons/201628/2026-08-11/1786431643_f80af07ce7beba96.png\"},{\"name\":\"2w2wsw\",\"icon\":\"uploads/travel-bookings/provide-icons/974137/2026-08-11/1786431677_869e4f049076cbc0.png\"}]', 'pending', '2026-08-11 12:08:23', '2026-08-11 06:38:23', '2026-08-11 07:01:17'),
(4, 'TRV0002', 1, 11, '11', 'Sedan', 4, 1, 11.00, 11.00, 5615.53, 509.50, '[{\"pickup\":\"Chennai, \\u0b9a\\u0bc6\\u0ba9\\u0bcd\\u0ba9\\u0bc8 \\u0bae\\u0bbe\\u0bb5\\u0b9f\\u0bcd\\u0b9f\\u0bae\\u0bcd, Tamil Nadu, India\",\"drop\":\"Kerala, India\",\"pickup_lat\":13.0836939,\"pickup_lng\":80.270186,\"drop_lat\":10.3528744,\"drop_lng\":76.5120396,\"distance\":509.5027517383436,\"price\":5604.530269121779}]', '[]', 'pending', '2026-08-11 12:12:45', '2026-08-11 06:42:45', '2026-08-11 06:42:45');

--
-- Triggers `travel_bookings`
--
DELIMITER $$
CREATE TRIGGER `before_insert_travel_bookings` BEFORE INSERT ON `travel_bookings` FOR EACH ROW BEGIN
    DECLARE next_id INT;
    IF NEW.booking_id IS NULL OR NEW.booking_id = '' THEN
        SELECT IFNULL(MAX(CAST(SUBSTRING(booking_id, 4) AS UNSIGNED)), 0) + 1 INTO next_id 
        FROM travel_bookings;
        SET NEW.booking_id = CONCAT('TRV', LPAD(next_id, 4, '0'));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'admin',
  `token` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `phone` varchar(50) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `username`, `password`, `role`, `token`, `last_login`, `created_at`, `updated_at`, `phone`, `facebook`, `twitter`, `instagram`, `youtube`, `linkedin`, `whatsapp`) VALUES
(1, 'Admin', 'sarantravels1908@gmail.com', 'admin', '$2y$10$DaJq4tiR2gB6MMKaKwFGV.8lo29jS9f.g5k4nQk76NpS9fMWC.40S', 'admin', '02bd4b165a541039d91629ec7bcaa8a33ce584bb53da2b050471ee214d38282c', '2026-08-12 00:00:38', '2026-08-08 08:37:54', '2026-08-11 18:30:38', '', NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `car_rentals`
--
ALTER TABLE `car_rentals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `offer_code` (`offer_code`);

--
-- Indexes for table `package_type_images`
--
ALTER TABLE `package_type_images`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_id` (`type_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tour_packages`
--
ALTER TABLE `tour_packages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `package_id` (`package_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `travel_bookings`
--
ALTER TABLE `travel_bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `car_id` (`car_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_token` (`token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `car_rentals`
--
ALTER TABLE `car_rentals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `package_type_images`
--
ALTER TABLE `package_type_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tour_packages`
--
ALTER TABLE `tour_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `travel_bookings`
--
ALTER TABLE `travel_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
