-- ============================================================
-- GOOD CAR IMPORTS — Database Schema
-- MySQL 5.7+ / MariaDB 10.3+
-- ============================================================

CREATE DATABASE IF NOT EXISTS `good_car_imports`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `good_car_imports`;

-- ==================== 1. USERS (Admin Authentication) ====================
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'manager', 'staff') NOT NULL DEFAULT 'staff',
  `title` VARCHAR(100) DEFAULT NULL,
  `avatar_initials` VARCHAR(5) DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `last_login_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- ==================== 2. VEHICLES (Master Inventory) ====================
CREATE TABLE IF NOT EXISTS `vehicles` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `stock_id` VARCHAR(30) DEFAULT NULL,
  `car_name` VARCHAR(150) NOT NULL,
  `package_trim` VARCHAR(100) DEFAULT NULL,
  `year_of_manufacture` SMALLINT UNSIGNED NOT NULL,
  `reiwa_year` VARCHAR(20) DEFAULT NULL,
  `color_name` VARCHAR(80) DEFAULT NULL,
  `color_code` VARCHAR(30) DEFAULT NULL,
  `color_hex` VARCHAR(10) DEFAULT NULL,
  `auction_grade` VARCHAR(20) DEFAULT NULL,
  `interior_grade` VARCHAR(10) DEFAULT NULL,
  `mileage_km` INT UNSIGNED DEFAULT 0,
  `chassis_code` VARCHAR(50) NOT NULL,
  `transmission` VARCHAR(50) DEFAULT NULL,
  `engine_spec` VARCHAR(100) DEFAULT NULL,
  `engine_cc` INT UNSIGNED DEFAULT NULL,
  `fuel_type` ENUM('Petrol', 'Diesel', 'Hybrid', 'PHEV', 'Electric', 'Octane') DEFAULT 'Petrol',
  `seats` TINYINT UNSIGNED DEFAULT 5,
  `drive_train` VARCHAR(20) DEFAULT '2WD',
  `body_type` ENUM('SUV', 'Sedan', 'Hatchback', 'MPV', 'Crossover', 'Wagon', 'Coupe', 'Pickup') DEFAULT 'SUV',
  `brand` VARCHAR(50) DEFAULT NULL,
  `status` ENUM('available', 'port_clearance', 'vessel_transit', 'pre_booked', 'sold', 'reserved') NOT NULL DEFAULT 'available',
  `price_bdt` BIGINT UNSIGNED DEFAULT NULL COMMENT 'Price in BDT paisa (divide by 100 for display)',
  `landed_cost` BIGINT UNSIGNED DEFAULT NULL COMMENT 'Landed cost in BDT paisa',
  `cover_photo` VARCHAR(500) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `safety_features` JSON DEFAULT NULL,
  `interior_features` JSON DEFAULT NULL,
  `exterior_features` JSON DEFAULT NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_new_arrival` TINYINT(1) NOT NULL DEFAULT 0,
  `badge_text` VARCHAR(50) DEFAULT NULL,
  `showroom_location` VARCHAR(100) DEFAULT 'Baridhara Showroom',
  `slug` VARCHAR(200) DEFAULT NULL,
  `views_count` INT UNSIGNED DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_chassis` (`chassis_code`),
  INDEX `idx_status` (`status`),
  INDEX `idx_brand` (`brand`),
  INDEX `idx_body_type` (`body_type`),
  INDEX `idx_featured` (`is_featured`),
  INDEX `idx_price` (`price_bdt`),
  INDEX `idx_slug` (`slug`)
) ENGINE=InnoDB;


-- ==================== 3. VEHICLE PHOTOS ====================
CREATE TABLE IF NOT EXISTS `vehicle_photos` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `vehicle_id` INT UNSIGNED NOT NULL,
  `file_path` VARCHAR(500) NOT NULL,
  `label` VARCHAR(50) DEFAULT NULL COMMENT 'e.g. Front 3/4, Rear Angle, Interior Cabin',
  `file_size_bytes` INT UNSIGNED DEFAULT NULL,
  `is_cover` TINYINT(1) NOT NULL DEFAULT 0,
  `sort_order` TINYINT UNSIGNED DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles`(`id`) ON DELETE CASCADE,
  INDEX `idx_vehicle` (`vehicle_id`)
) ENGINE=InnoDB;


-- ==================== 4. STOCK INWARD (Import Pipeline) ====================
CREATE TABLE IF NOT EXISTS `stock_inward` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `vehicle_id` INT UNSIGNED DEFAULT NULL,
  `car_name` VARCHAR(150) NOT NULL,
  `package_trim` VARCHAR(100) DEFAULT NULL,
  `year_of_manufacture` SMALLINT UNSIGNED DEFAULT NULL,
  `color_name` VARCHAR(80) DEFAULT NULL,
  `color_hex` VARCHAR(10) DEFAULT NULL,
  `auction_grade` VARCHAR(20) DEFAULT NULL,
  `interior_grade` VARCHAR(10) DEFAULT NULL,
  `mileage_km` INT UNSIGNED DEFAULT 0,
  `chassis_code` VARCHAR(50) NOT NULL,
  `transmission` VARCHAR(50) DEFAULT NULL,
  `engine_spec` VARCHAR(100) DEFAULT NULL,
  `current_stage` ENUM('auction_won', 'japan_yard', 'sea_transit', 'ctg_customs', 'dhaka_handover') NOT NULL DEFAULT 'auction_won',
  `auction_house` VARCHAR(100) DEFAULT NULL,
  `auction_lot` VARCHAR(50) DEFAULT NULL,
  `won_price_jpy` BIGINT UNSIGNED DEFAULT NULL,
  `bdt_equivalent` BIGINT UNSIGNED DEFAULT NULL,
  `vessel_name` VARCHAR(100) DEFAULT NULL,
  `eta_date` DATE DEFAULT NULL,
  `duty_amount` BIGINT UNSIGNED DEFAULT NULL COMMENT 'In BDT paisa',
  `duty_status` VARCHAR(50) DEFAULT NULL,
  `bill_of_lading` VARCHAR(255) DEFAULT NULL,
  `jaai_cert` TINYINT(1) DEFAULT 0,
  `jumvea_cert` TINYINT(1) DEFAULT 0,
  `radiation_clear` TINYINT(1) DEFAULT 0,
  `customs_status` VARCHAR(100) DEFAULT NULL,
  `carrier_number` VARCHAR(50) DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles`(`id`) ON DELETE SET NULL,
  INDEX `idx_stage` (`current_stage`),
  INDEX `idx_chassis` (`chassis_code`)
) ENGINE=InnoDB;


-- ==================== 5. INQUIRIES (Contact Form & Booking) ====================
CREATE TABLE IF NOT EXISTS `inquiries` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `type` ENUM('contact', 'booking', 'general') NOT NULL DEFAULT 'contact',
  `vehicle_id` INT UNSIGNED DEFAULT NULL,
  `full_name` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(30) DEFAULT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `interested_in` VARCHAR(100) DEFAULT NULL,
  `message` TEXT DEFAULT NULL,
  `status` ENUM('new', 'contacted', 'converted', 'closed') NOT NULL DEFAULT 'new',
  `admin_notes` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles`(`id`) ON DELETE SET NULL,
  INDEX `idx_status` (`status`),
  INDEX `idx_type` (`type`)
) ENGINE=InnoDB;


-- ==================== 6. PRE-ORDERS ====================
CREATE TABLE IF NOT EXISTS `pre_orders` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `region_of_origin` VARCHAR(100) DEFAULT NULL,
  `body_style` VARCHAR(50) DEFAULT NULL,
  `make_model` VARCHAR(150) DEFAULT NULL,
  `year_from` SMALLINT UNSIGNED DEFAULT NULL,
  `year_to` SMALLINT UNSIGNED DEFAULT NULL,
  `budget_min_bdt` BIGINT UNSIGNED DEFAULT NULL,
  `budget_max_bdt` BIGINT UNSIGNED DEFAULT NULL,
  `timeline` VARCHAR(100) DEFAULT NULL,
  `color_preference` VARCHAR(100) DEFAULT NULL,
  `additional_notes` TEXT DEFAULT NULL,
  `full_name` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `whatsapp` VARCHAR(30) DEFAULT NULL,
  `status` ENUM('new', 'sourcing', 'quoted', 'confirmed', 'closed') NOT NULL DEFAULT 'new',
  `admin_notes` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB;


-- ==================== 7. SALES (Realized Sales Records) ====================
CREATE TABLE IF NOT EXISTS `sales` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `vehicle_id` INT UNSIGNED DEFAULT NULL,
  `car_name` VARCHAR(150) NOT NULL,
  `chassis_code` VARCHAR(50) DEFAULT NULL,
  `sale_price_bdt` BIGINT UNSIGNED DEFAULT NULL,
  `client_area` VARCHAR(100) DEFAULT NULL,
  `payment_status` ENUM('pending', 'partial', 'cleared', 'lc_settlement') NOT NULL DEFAULT 'pending',
  `delivery_status` ENUM('pending', 'delivered', 'brta_pending') NOT NULL DEFAULT 'pending',
  `sale_date` DATE DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;


-- ==================== 8. SETTINGS (Key-Value Store) ====================
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT DEFAULT NULL,
  `setting_type` ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
  `description` VARCHAR(255) DEFAULT NULL,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
