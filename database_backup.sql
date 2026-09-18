-- MySQL dump 10.13  Distrib 8.0.46, for Linux (aarch64)
--
-- Host: localhost    Database: good_car_imports
-- ------------------------------------------------------
-- Server version	8.0.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `inquiries`
--

DROP TABLE IF EXISTS `inquiries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inquiries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('contact','booking','general') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'contact',
  `vehicle_id` int unsigned DEFAULT NULL,
  `full_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interested_in` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `status` enum('new','contacted','converted','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vehicle_id` (`vehicle_id`),
  KEY `idx_status` (`status`),
  KEY `idx_type` (`type`),
  CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inquiries`
--

LOCK TABLES `inquiries` WRITE;
/*!40000 ALTER TABLE `inquiries` DISABLE KEYS */;
INSERT INTO `inquiries` VALUES (5,'contact',NULL,'HM  Ziyad','01718700120','ziyadhm399@gmail.com','General Inquiry','this is a test message','contacted',NULL,'2026-09-11 09:22:12','2026-09-11 09:22:22'),(6,'contact',NULL,'HM Ziyad','01718700120','ziyadhm399@gmail.com','Pre-Order / Import','sldjhglsdhggsdougfsdjgbkjusdbvolsdhfoiuwhgfvlibsdvklbskjvbskjbvcskljdbcvksdjbvksjdbvksbjvkjsfbvlosjdbvlsbvlsdkbvlsdbvlsbdvlsjbvlkjsjbvlkjsdbvlshbfgosdflkjsdbvlksjdbvljsbvslv lskdnflsidhdgoisdhglsdnbgflsdnf,sjdbvkjsfbvlfisdhgpisfdhgprwihtoadvbgsdlvbfsdl;khgl;sfhjgfwpeiohglisdhbglsdfbgvlksfhglisdhgliwrhsglisfhglhf','contacted',NULL,'2026-09-11 09:29:26','2026-09-11 09:29:37');
/*!40000 ALTER TABLE `inquiries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pre_orders`
--

DROP TABLE IF EXISTS `pre_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pre_orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `region_of_origin` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `body_style` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `make_model` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year_from` smallint unsigned DEFAULT NULL,
  `year_to` smallint unsigned DEFAULT NULL,
  `budget_min_bdt` bigint unsigned DEFAULT NULL,
  `budget_max_bdt` bigint unsigned DEFAULT NULL,
  `timeline` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_preference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `additional_notes` text COLLATE utf8mb4_unicode_ci,
  `full_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('new','sourcing','quoted','confirmed','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `admin_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pre_orders`
--

LOCK TABLES `pre_orders` WRITE;
/*!40000 ALTER TABLE `pre_orders` DISABLE KEYS */;
INSERT INTO `pre_orders` VALUES (1,'UK','Sedan','Toyota prius',NULL,NULL,400000000,600000000,'1-3 months','black','This is for testing purpose','HM Ziyad','01718700120','ziyadhm399@gmail.com','01718700120','sourcing',NULL,'2026-09-11 09:02:02','2026-09-11 09:25:50');
/*!40000 ALTER TABLE `pre_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_id` int unsigned DEFAULT NULL,
  `car_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chassis_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sale_price_bdt` bigint unsigned DEFAULT NULL,
  `client_area` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` enum('pending','partial','cleared','lc_settlement') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `delivery_status` enum('pending','delivered','brta_pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `sale_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vehicle_id` (`vehicle_id`),
  CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
INSERT INTO `sales` VALUES (13,2,'Toyota Corolla Cross','ZVG11-204812',45000000,'Direct Sale','cleared','delivered','2026-09-10',NULL,'2026-09-10 16:30:47','2026-09-10 16:34:27'),(14,13,'Toyota Alphard','AGH40-002194',2000000000,'Direct Sale','cleared','delivered','2026-09-10',NULL,'2026-09-10 17:35:27','2026-09-10 17:35:27'),(15,1,'Toyota Land Cruiser 300','VJA300-0019482',85000,'Direct Sale','cleared','delivered','2026-09-10',NULL,'2026-09-10 17:38:23','2026-09-10 17:38:23'),(16,11,'Toyota Prius Hybrid','MXWH60-201884',NULL,'Direct Sale','cleared','delivered','2026-09-10',NULL,'2026-09-10 17:38:39','2026-09-10 17:38:39');
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `setting_type` enum('string','number','boolean','json') COLLATE utf8mb4_unicode_ci DEFAULT 'string',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'forex_jpy_bdt','0.798','number','Live JPY to BDT exchange rate','2026-09-11 09:03:38'),(2,'company_name','Good Car Imports','string','Company display name','2026-09-09 13:00:42'),(3,'company_phone_1','019 9242 4492','string','Primary phone number','2026-09-10 18:29:45'),(4,'company_phone_2','','string','Secondary phone number','2026-09-10 18:29:45'),(5,'company_email','goodcarimports.bd@gmail.com','string','Company email','2026-09-10 18:29:45'),(6,'company_address_1','56 Inner Circular Road\nEastern Trade Center (6 Floor)\nPurana Paltan Line Dhaka-1000','string','Baridhara Showroom address','2026-09-10 18:32:57'),(7,'company_address_2','','string','Tejgaon Service Center address','2026-09-10 18:32:57'),(8,'whatsapp_number','+8801992424492','string','WhatsApp contact number','2026-09-10 18:08:16'),(9,'business_hours','Sat - Thu: 10:00 AM - 8:00 PM','string','Operating hours','2026-09-09 13:00:42'),(10,'business_hours_note','Friday: Closed (By Appointment Only)','string','Additional hours note','2026-09-09 13:00:42'),(11,'smtp_host','smtp.gmail.com','string','SMTP server hostname','2026-09-10 18:08:16'),(12,'smtp_port','587','number','SMTP server port','2026-09-09 13:00:42'),(13,'smtp_username','goodcarimports.bd@gmail.com','string','SMTP username','2026-09-10 18:08:16'),(14,'smtp_password','amfkvuburuusilqo','string','SMTP password','2026-09-11 08:53:25'),(15,'smtp_from_email','goodcarimports.bd@gmail.com','string','SMTP from email address','2026-09-10 18:08:16'),(16,'smtp_from_name','Good Car Imports','string','SMTP from name','2026-09-09 13:00:42');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_inward`
--

DROP TABLE IF EXISTS `stock_inward`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_inward` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_id` int unsigned DEFAULT NULL,
  `car_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `package_trim` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year_of_manufacture` smallint unsigned DEFAULT NULL,
  `color_name` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_hex` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auction_grade` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interior_grade` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mileage_km` int unsigned DEFAULT '0',
  `chassis_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transmission` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `engine_spec` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_stage` enum('auction_won','japan_yard','sea_transit','ctg_customs','dhaka_handover') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'auction_won',
  `auction_house` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auction_lot` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `won_price_jpy` bigint unsigned DEFAULT NULL,
  `bdt_equivalent` bigint unsigned DEFAULT NULL,
  `vessel_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eta_date` date DEFAULT NULL,
  `duty_amount` bigint unsigned DEFAULT NULL COMMENT 'In BDT paisa',
  `duty_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bill_of_lading` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jaai_cert` tinyint(1) DEFAULT '0',
  `jumvea_cert` tinyint(1) DEFAULT '0',
  `radiation_clear` tinyint(1) DEFAULT '0',
  `customs_status` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carrier_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vehicle_id` (`vehicle_id`),
  KEY `idx_stage` (`current_stage`),
  KEY `idx_chassis` (`chassis_code`),
  CONSTRAINT `stock_inward_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_inward`
--

LOCK TABLES `stock_inward` WRITE;
/*!40000 ALTER TABLE `stock_inward` DISABLE KEYS */;
INSERT INTO `stock_inward` VALUES (1,15,'Toyota Land Cruiser 250','VX Package',2024,'Pearl White',NULL,'5.0','A',1200,'GDJ250-001928','8-Speed AT',NULL,'ctg_customs','USS Tokyo','#84102',845000000,667500000,'MV Glovis Corona','2024-11-18',NULL,NULL,NULL,0,0,0,NULL,NULL,NULL,'2026-09-09 13:00:42','2026-09-10 19:35:50'),(2,11,'Toyota Prius Hybrid','2.0 Z Grade',2023,'Attitude Black',NULL,'4.5','B',8400,'MXWH60-201884','e-CVT',NULL,'japan_yard','TAA Chubu','#6721',320000000,252800000,NULL,NULL,NULL,NULL,NULL,0,0,0,NULL,NULL,NULL,'2026-09-09 13:00:42','2026-09-10 17:39:57'),(3,14,'Toyota Harrier','2.5 Z Leather Package',2022,'Precious Black',NULL,'5.0','S',14300,'AXUH80-109482','e-CVT',NULL,'ctg_customs','USS Tokyo','#91203',520000000,410800000,'MV Morning Chorus','2024-11-24',NULL,NULL,NULL,0,0,0,NULL,NULL,NULL,'2026-09-09 13:00:42','2026-09-10 17:39:48'),(4,12,'Lexus RX350h','F-Sport AWD',2023,'Sonic Titanium',NULL,'5.0','A',6100,'TALA10-003418','Direct Shift CVT',NULL,'dhaka_handover','USS Nagoya','#45102',680000000,537200000,NULL,NULL,NULL,NULL,NULL,0,0,0,NULL,NULL,NULL,'2026-09-09 13:00:42','2026-09-10 17:03:09'),(5,13,'Toyota Alphard','Executive Lounge 4WD',2023,'Luxury White Pearl',NULL,'6.0','S',25,'AGH40-002194','Super CVT-i',NULL,'dhaka_handover','JU Yokohama','#1203',945000000,746550000,NULL,NULL,NULL,NULL,NULL,0,0,0,NULL,NULL,NULL,'2026-09-09 13:00:42','2026-09-10 16:50:34');
/*!40000 ALTER TABLE `stock_inward` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','manager','staff') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'staff',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_initials` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'M. Rahman','admin@goodcarimports.com','$2y$12$RdzdriDVNaKvQsS5jLNJAu.e047hEpraqQJAViM4qbzmJh7mUPuii','admin','Managing Director','MR',1,'2026-09-10 17:01:39','2026-09-09 13:00:42','2026-09-10 17:01:39');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicle_photos`
--

DROP TABLE IF EXISTS `vehicle_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicle_photos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `vehicle_id` int unsigned NOT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'e.g. Front 3/4, Rear Angle, Interior Cabin',
  `file_size_bytes` int unsigned DEFAULT NULL,
  `is_cover` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` tinyint unsigned DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_vehicle` (`vehicle_id`),
  CONSTRAINT `vehicle_photos_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicle_photos`
--

LOCK TABLES `vehicle_photos` WRITE;
/*!40000 ALTER TABLE `vehicle_photos` DISABLE KEYS */;
INSERT INTO `vehicle_photos` VALUES (2,14,'vehicles/2026-09/gci_6aa3e8e5cddb10.18450362.jpeg',NULL,NULL,1,0,'2026-09-11 11:41:25'),(3,14,'vehicles/2026-09/gci_6aa3e8e5ce6576.80317709.jpeg',NULL,NULL,0,1,'2026-09-11 11:41:25'),(4,15,'vehicles/2026-09/gci_6aa3f98986e028.77305017.webp',NULL,NULL,1,0,'2026-09-11 12:52:25');
/*!40000 ALTER TABLE `vehicle_photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `stock_id` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `car_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `package_trim` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year_of_manufacture` smallint unsigned NOT NULL,
  `reiwa_year` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_name` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_code` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_hex` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auction_grade` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `interior_grade` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mileage_km` int unsigned DEFAULT '0',
  `chassis_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transmission` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `engine_spec` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `engine_cc` int unsigned DEFAULT NULL,
  `fuel_type` enum('Petrol','Diesel','Hybrid','PHEV','Electric','Octane') COLLATE utf8mb4_unicode_ci DEFAULT 'Petrol',
  `seats` tinyint unsigned DEFAULT '5',
  `drive_train` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '2WD',
  `body_type` enum('SUV','Sedan','Hatchback','MPV','Crossover','Wagon','Coupe','Pickup') COLLATE utf8mb4_unicode_ci DEFAULT 'SUV',
  `brand` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('available','port_clearance','vessel_transit','pre_booked','sold','reserved') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `price_bdt` bigint unsigned DEFAULT NULL COMMENT 'Price in BDT paisa (divide by 100 for display)',
  `landed_cost` bigint unsigned DEFAULT NULL COMMENT 'Landed cost in BDT paisa',
  `cover_photo` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `safety_features` json DEFAULT NULL,
  `interior_features` json DEFAULT NULL,
  `exterior_features` json DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_new_arrival` tinyint(1) NOT NULL DEFAULT '0',
  `badge_text` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `showroom_location` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Baridhara Showroom',
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `views_count` int unsigned DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_chassis` (`chassis_code`),
  KEY `idx_status` (`status`),
  KEY `idx_brand` (`brand`),
  KEY `idx_body_type` (`body_type`),
  KEY `idx_featured` (`is_featured`),
  KEY `idx_price` (`price_bdt`),
  KEY `idx_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicles`
--

LOCK TABLES `vehicles` WRITE;
/*!40000 ALTER TABLE `vehicles` DISABLE KEYS */;
INSERT INTO `vehicles` VALUES (1,'GCI-1001','Toyota Land Cruiser 300','ZX Modellista',2024,'Reiwa 6','Pearl White','070','#F5F5F0','5.0','A',4200,'VJA300-0019482','10-Speed AT','3.5L Twin Turbo V6',3500,'Petrol',7,'4WD','SUV','Toyota','sold',85000,NULL,'lc300.jpg','The 2024 Toyota Land Cruiser 300 ZX Modellista represents the pinnacle of luxury SUV engineering. Imported directly from Japan, it features the latest twin-turbo V6 powerplant delivering exceptional performance and refinement.','[\"Toyota Safety Sense 3.0\", \"Pre-Collision System\", \"Lane Departure Alert\", \"Adaptive Cruise Control\", \"Blind Spot Monitor\", \"Parking Assist Sensors\", \"Multi-Terrain Monitor\"]','[\"Premium Leather Interior\", \"Power Adjustable Seats\", \"Heated & Ventilated Front Seats\", \"10.1-inch Touchscreen\", \"JBL Premium Audio\", \"Wireless Apple CarPlay\", \"Tri-Zone Climate Control\"]','[\"Modellista Body Kit\", \"LED Headlamps with AHS\", \"Power Tailgate\", \"20-inch Alloy Wheels\", \"Chrome Exterior Accents\", \"Privacy Glass\"]',1,1,'NEW ARRIVAL','Baridhara Showroom','toyota-land-cruiser-300-zx-modellista-2024',11,'2026-09-09 13:00:42','2026-09-10 17:38:23'),(2,'GCI-8829','Toyota Corolla Cross','Z Package',2023,'Reiwa 5','Attitude Black','218','#1C1C1C','4.5','A',12400,'ZVG11-204812','e-CVT','1.8L Hybrid 2WD',1800,'Hybrid',5,'2WD','Crossover','Toyota','sold',45000000,NULL,'cross.jpg','This 2023 Toyota Corolla Cross Z Hybrid represents the pinnacle of compact SUV engineering. Imported directly from Japan, it features the latest hybrid synergy drive technology, offering exceptional fuel efficiency without compromising on performance.','[\"Toyota Safety Sense 2.5\", \"Pre-Collision System\", \"Lane Departure Alert\", \"Adaptive Cruise Control\", \"Blind Spot Monitor (BSM)\", \"Parking Assist Sensors\"]','[\"Premium Synthetic Leather\", \"Power Adjustable Driver Seat\", \"9-inch Touchscreen\", \"Wireless Apple CarPlay\", \"Dual-Zone Climate Control\"]','[\"LED Headlamps\", \"Chrome Exterior Accents\", \"18-inch Alloy Wheels\", \"Shark Fin Antenna\", \"Privacy Glass\"]',1,1,'NEW ARRIVAL','Baridhara Showroom','toyota-corolla-cross-z-package-2023',1,'2026-09-09 13:00:42','2026-09-10 16:34:27'),(3,'GCI-8830','Honda Vezel','e:HEV Play',2021,'Reiwa 3','Sand Khaki','YR-650P','#C4B396','4.0','B',21300,'RV5-103982','e-CVT','1.5L Dual Motor e:HEV',1500,'Hybrid',5,'2WD','SUV','Honda','available',4200000000,NULL,'vezel.jpg','The 2021 Honda Vezel e:HEV Play delivers exceptional hybrid performance with Honda\'s innovative dual-motor e:HEV system.','[\"Honda SENSING\", \"Collision Mitigation Braking\", \"Lane Keeping Assist\", \"Adaptive Cruise Control\"]','[\"Fabric & Leather Combo Seats\", \"8-inch Display Audio\", \"Apple CarPlay\", \"LED Interior Lighting\"]','[\"Full LED Headlights\", \"17-inch Alloy Wheels\", \"Power Tailgate\"]',0,0,'Top Grade','Baridhara Showroom','honda-vezel-ehev-play-2021',5,'2026-09-09 13:00:42','2026-09-11 12:13:48'),(4,'GCI-8831','Mazda CX-60','Exclusive Mode',2022,'Reiwa 4','Soul Red Crystal','46V','#9B1B30','5.0','S',8900,'KH3P-100934','8-Speed AT','3.3L Turbo Diesel Hybrid',3300,'Diesel',5,'4WD','SUV','Mazda','available',6500000000,NULL,'lc300.jpg','The 2022 Mazda CX-60 Exclusive Mode is a masterclass in Japanese luxury engineering, featuring Mazda\'s revolutionary inline-6 diesel with 48V mild hybrid technology.','[\"i-ACTIVSENSE\", \"Smart Brake Support\", \"Cruising & Traffic Support\", \"Driver Monitoring System\"]','[\"Nappa Leather\", \"Driver Personalization System\", \"12.3-inch Center Display\", \"Bose 12-Speaker Audio\"]','[\"Soul Red Crystal Metallic\", \"20-inch Alloy Wheels\", \"Adaptive LED Headlights\", \"Frameless Rearview Mirror\"]',0,0,NULL,'Baridhara Showroom','mazda-cx-60-exclusive-mode-2022',0,'2026-09-09 13:00:42','2026-09-10 16:25:00'),(5,'GCI-8832','Toyota Harrier','Z Leather Package',2022,'Reiwa 4','Precious Black','219','#0D0D0D','4.5','A',16200,'AXUH85-004128','e-CVT','2.5L Hybrid E-Four',2500,'Hybrid',5,'4WD','SUV','Toyota','available',5200000000,NULL,'cross.jpg','The 2022 Toyota Harrier Z Leather Package combines premium luxury with hybrid efficiency.','[\"Toyota Safety Sense 2.0\", \"Pre-Collision System\", \"Lane Departure Alert\", \"Adaptive Cruise Control\"]','[\"Premium Leather Seats\", \"JBL Premium Audio\", \"12.3-inch Display\", \"Panoramic Roof\"]','[\"Sequential LED Turn Signals\", \"19-inch Alloy Wheels\", \"Power Back Door\"]',1,0,NULL,'Baridhara Showroom','toyota-harrier-z-leather-2022',1,'2026-09-09 13:00:42','2026-09-11 11:17:35'),(6,'GCI-8833','Toyota Premio','F EX Package',2021,'Reiwa 3','Silver Metallic','1F7','#C0C0C0','R',NULL,35000,'NZT260-3189','CVT','1.5L 1NZ-FE',1500,'Petrol',5,'2WD','Sedan','Toyota','available',4200000000,NULL,'vezel.jpg','The Toyota Premio F EX Package is a reliable and fuel-efficient sedan.','[\"ABS\", \"EBD\", \"Airbags\"]','[\"Fabric Seats\", \"Auto AC\"]','[\"Halogen Headlights\"]',1,0,'Grade R','Baridhara Showroom','toyota-premio-f-ex-2021',1,'2026-09-09 13:00:42','2026-09-10 16:25:06'),(7,'GCI-8834','Toyota Axio','G WXB',2019,'Reiwa 1','White Pearl','070','#F5F5F0','5.0',NULL,34000,'NKE165-1078342','e-CVT','1.5L Hybrid',1500,'Hybrid',5,'2WD','Sedan','Toyota','available',2450000000,NULL,'lc300.jpg','The Toyota Axio G WXB is a practical hybrid sedan with excellent fuel economy.','[\"Toyota Safety Sense C\", \"Pre-Collision System\"]','[\"Half Leather\", \"7-inch Display\"]','[\"LED Headlights\"]',0,0,'BEST VALUE','Baridhara Showroom','toyota-axio-g-wxb-2019',0,'2026-09-09 13:00:42','2026-09-10 16:25:11'),(8,'GCI-8835','Mazda CX-5','25S L-Package',2020,'Reiwa 2','Machine Grey','46G','#686868','4.0',NULL,21000,'KF5P-390821','6-Speed AT','2.5L Skyactiv-G',2500,'Petrol',5,'2WD','SUV','Mazda','available',3980000000,NULL,'cross.jpg','The Mazda CX-5 25S L-Package offers premium driving dynamics and refined interior.','[\"i-ACTIVSENSE\", \"Smart Brake Support\"]','[\"Leather Seats\", \"Bose Audio\"]','[\"LED Headlights\", \"19-inch Alloys\"]',0,0,NULL,'Baridhara Showroom','mazda-cx-5-25s-l-package-2020',1,'2026-09-09 13:00:42','2026-09-10 16:25:17'),(9,'GCI-8836','Mitsubishi Outlander','G-Package',2021,'Reiwa 3','Red Diamond',NULL,'#8B0000','4.5',NULL,15000,'GF8W-040218','8-Speed CVT','2.4L PHEV',2400,'PHEV',7,'4WD','SUV','Mitsubishi','available',5500000000,NULL,'vezel.jpg','The Mitsubishi Outlander PHEV G-Package is a versatile plug-in hybrid SUV.','[\"Forward Collision Mitigation\", \"Lane Departure Warning\"]','[\"Leather Seats\", \"8-inch Display\"]','[\"LED Headlights\", \"18-inch Alloys\"]',0,0,NULL,'Baridhara Showroom','mitsubishi-outlander-g-package-2021',0,'2026-09-09 13:00:42','2026-09-10 16:25:23'),(10,'GCI-8837','Toyota Noah','SI WXB',2018,'Heisei 30','Black','202','#0D0D0D','4.0',NULL,56000,'ZWR80G-0142938','e-CVT','1.8L Hybrid',2000,'Hybrid',7,'2WD','MPV','Toyota','available',3450000000,NULL,'lc300.jpg','The Toyota Noah SI WXB is a spacious 7-seater MPV with hybrid efficiency.','[\"Toyota Safety Sense C\"]','[\"Fabric Seats\", \"9-inch Display\"]','[\"LED Headlights\"]',0,0,NULL,'Baridhara Showroom','toyota-noah-si-wxb-2018',0,'2026-09-09 13:00:42','2026-09-10 16:25:29'),(11,NULL,'Toyota Prius Hybrid','2.0 Z Grade',2023,NULL,'Attitude Black',NULL,NULL,'4.5',NULL,8400,'MXWH60-201884','cvt',NULL,NULL,'Petrol',5,'2WD','SUV','Toyota','sold',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,'Baridhara Showroom','toyota-prius-hybrid-201884',0,'2026-09-10 16:49:19','2026-09-10 17:38:39'),(12,NULL,'Lexus RX350h','F-Sport AWD',2023,NULL,'Sonic Titanium',NULL,NULL,'5.0',NULL,6100,'TALA10-003418','CVT',NULL,NULL,'Petrol',5,'2WD','SUV','Lexus','available',2000000000,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,'Baridhara Showroom','lexus-rx350h-003418',0,'2026-09-10 16:50:28','2026-09-10 17:16:02'),(13,NULL,'Toyota Alphard','Executive Lounge 4WD',2023,NULL,'Luxury White Pearl',NULL,NULL,'6.0',NULL,25,'AGH40-002194','AT',NULL,NULL,'Petrol',5,'2WD','SUV','Toyota','sold',2000000000,NULL,NULL,NULL,NULL,NULL,NULL,0,0,NULL,'Baridhara Showroom','toyota-alphard-002194',0,'2026-09-10 16:50:34','2026-09-10 17:35:27'),(14,NULL,'Toyota Harrier','2.5 Z Leather Package',2022,NULL,'Precious Black',NULL,NULL,'5.0',NULL,14300,'AXUH80-109482','AT',NULL,NULL,'Petrol',5,'2WD','SUV','Toyota','available',400000000,NULL,'vehicles/2026-09/gci_6aa3e8e5cddb10.18450362.jpeg',NULL,NULL,NULL,NULL,1,0,NULL,'Baridhara Showroom','toyota-harrier-109482',3,'2026-09-10 17:39:48','2026-09-11 12:10:54'),(15,NULL,'Toyota Land Cruiser 250','VX Package',2024,NULL,'Pearl White',NULL,NULL,'5.0',NULL,1200,'GDJ250-001928','CVT',NULL,NULL,'Petrol',5,'2WD','SUV','Toyota','available',1900,NULL,'vehicles/2026-09/gci_6aa3f98986e028.77305017.webp',NULL,NULL,NULL,NULL,0,0,NULL,'Baridhara Showroom','toyota-land-cruiser-250-001928',15,'2026-09-10 19:35:50','2026-09-11 13:10:17');
/*!40000 ALTER TABLE `vehicles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-11 13:24:02
