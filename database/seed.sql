-- ============================================================
-- GOOD CAR IMPORTS — Seed Data
-- Sample data matching the design screenshots
-- ============================================================

USE `good_car_imports`;

-- ==================== Admin User ====================
-- Password: admin@gci2024 (bcrypt hash)
INSERT INTO `users` (`name`, `email`, `password_hash`, `role`, `title`, `avatar_initials`) VALUES
('M. Rahman', 'admin@goodcarimports.com', '$2y$12$LJ3m2xD1TfP6K5rQz5w8YeQ1bLN7v5dMxJg9h5KjZcN0pVz3KwXGm', 'admin', 'Managing Director', 'MR');


-- ==================== Sample Vehicles ====================
INSERT INTO `vehicles` (
  `stock_id`, `car_name`, `package_trim`, `year_of_manufacture`, `reiwa_year`,
  `color_name`, `color_code`, `color_hex`, `auction_grade`, `interior_grade`,
  `mileage_km`, `chassis_code`, `transmission`, `engine_spec`, `engine_cc`,
  `fuel_type`, `seats`, `drive_train`, `body_type`, `brand`,
  `status`, `price_bdt`, `cover_photo`, `description`,
  `safety_features`, `interior_features`, `exterior_features`,
  `is_featured`, `is_new_arrival`, `badge_text`, `showroom_location`, `slug`
) VALUES
-- 1. Toyota Land Cruiser 300
(
  'GCI-1001', 'Toyota Land Cruiser 300', 'ZX Modellista', 2024, 'Reiwa 6',
  'Pearl White', '070', '#F5F5F0', '5.0', 'A',
  4200, 'VJA300-0019482', '10-Speed AT', '3.5L Twin Turbo V6', 3500,
  'Petrol', 7, '4WD', 'SUV', 'Toyota',
  'available', 8500000000, NULL,
  'The 2024 Toyota Land Cruiser 300 ZX Modellista represents the pinnacle of luxury SUV engineering. Imported directly from Japan, it features the latest twin-turbo V6 powerplant delivering exceptional performance and refinement.',
  '["Toyota Safety Sense 3.0", "Pre-Collision System", "Lane Departure Alert", "Adaptive Cruise Control", "Blind Spot Monitor", "Parking Assist Sensors", "Multi-Terrain Monitor"]',
  '["Premium Leather Interior", "Power Adjustable Seats", "Heated & Ventilated Front Seats", "10.1-inch Touchscreen", "JBL Premium Audio", "Wireless Apple CarPlay", "Tri-Zone Climate Control"]',
  '["Modellista Body Kit", "LED Headlamps with AHS", "Power Tailgate", "20-inch Alloy Wheels", "Chrome Exterior Accents", "Privacy Glass"]',
  1, 1, 'NEW ARRIVAL', 'Baridhara Showroom', 'toyota-land-cruiser-300-zx-modellista-2024'
),

-- 2. Toyota Corolla Cross Z Hybrid
(
  'GCI-8829', 'Toyota Corolla Cross', 'Z Package', 2023, 'Reiwa 5',
  'Attitude Black', '218', '#1C1C1C', '4.5', 'A',
  12400, 'ZVG11-204812', 'e-CVT', '1.8L Hybrid 2WD', 1800,
  'Hybrid', 5, '2WD', 'Crossover', 'Toyota',
  'port_clearance', 4500000000, NULL,
  'This 2023 Toyota Corolla Cross Z Hybrid represents the pinnacle of compact SUV engineering. Imported directly from Japan, it features the latest hybrid synergy drive technology, offering exceptional fuel efficiency without compromising on performance.',
  '["Toyota Safety Sense 2.5", "Pre-Collision System", "Lane Departure Alert", "Adaptive Cruise Control", "Blind Spot Monitor (BSM)", "Parking Assist Sensors"]',
  '["Premium Synthetic Leather", "Power Adjustable Driver Seat", "9-inch Touchscreen", "Wireless Apple CarPlay", "Dual-Zone Climate Control"]',
  '["LED Headlamps", "Chrome Exterior Accents", "18-inch Alloy Wheels", "Shark Fin Antenna", "Privacy Glass"]',
  1, 1, 'NEW ARRIVAL', 'Baridhara Showroom', 'toyota-corolla-cross-z-package-2023'
),

-- 3. Honda Vezel Z
(
  'GCI-8830', 'Honda Vezel', 'e:HEV Play', 2021, 'Reiwa 3',
  'Sand Khaki', 'YR-650P', '#C4B396', '4.0', 'B',
  21300, 'RV5-103982', 'e-CVT', '1.5L Dual Motor e:HEV', 1500,
  'Hybrid', 5, '2WD', 'SUV', 'Honda',
  'available', 4200000000, NULL,
  'The 2021 Honda Vezel e:HEV Play delivers exceptional hybrid performance with Honda''s innovative dual-motor e:HEV system.',
  '["Honda SENSING", "Collision Mitigation Braking", "Lane Keeping Assist", "Adaptive Cruise Control"]',
  '["Fabric & Leather Combo Seats", "8-inch Display Audio", "Apple CarPlay", "LED Interior Lighting"]',
  '["Full LED Headlights", "17-inch Alloy Wheels", "Power Tailgate"]',
  1, 0, 'Top Grade', 'Baridhara Showroom', 'honda-vezel-ehev-play-2021'
),

-- 4. Mazda CX-60
(
  'GCI-8831', 'Mazda CX-60', 'Exclusive Mode', 2022, 'Reiwa 4',
  'Soul Red Crystal', '46V', '#9B1B30', '5.0', 'S',
  8900, 'KH3P-100934', '8-Speed AT', '3.3L Turbo Diesel Hybrid', 3300,
  'Diesel', 5, '4WD', 'SUV', 'Mazda',
  'vessel_transit', 6500000000, NULL,
  'The 2022 Mazda CX-60 Exclusive Mode is a masterclass in Japanese luxury engineering, featuring Mazda''s revolutionary inline-6 diesel with 48V mild hybrid technology.',
  '["i-ACTIVSENSE", "Smart Brake Support", "Cruising & Traffic Support", "Driver Monitoring System"]',
  '["Nappa Leather", "Driver Personalization System", "12.3-inch Center Display", "Bose 12-Speaker Audio"]',
  '["Soul Red Crystal Metallic", "20-inch Alloy Wheels", "Adaptive LED Headlights", "Frameless Rearview Mirror"]',
  0, 0, NULL, 'Baridhara Showroom', 'mazda-cx-60-exclusive-mode-2022'
),

-- 5. Toyota Harrier Z Leather
(
  'GCI-8832', 'Toyota Harrier', 'Z Leather Package', 2022, 'Reiwa 4',
  'Precious Black', '219', '#0D0D0D', '4.5', 'A',
  16200, 'AXUH85-004128', 'e-CVT', '2.5L Hybrid E-Four', 2500,
  'Hybrid', 5, '4WD', 'SUV', 'Toyota',
  'pre_booked', 5200000000, NULL,
  'The 2022 Toyota Harrier Z Leather Package combines premium luxury with hybrid efficiency.',
  '["Toyota Safety Sense 2.0", "Pre-Collision System", "Lane Departure Alert", "Adaptive Cruise Control"]',
  '["Premium Leather Seats", "JBL Premium Audio", "12.3-inch Display", "Panoramic Roof"]',
  '["Sequential LED Turn Signals", "19-inch Alloy Wheels", "Power Back Door"]',
  0, 0, NULL, 'Baridhara Showroom', 'toyota-harrier-z-leather-2022'
),

-- 6. Toyota Premio F
(
  'GCI-8833', 'Toyota Premio', 'F EX Package', 2021, 'Reiwa 3',
  'Silver Metallic', '1F7', '#C0C0C0', 'R', NULL,
  35000, 'NZT260-3189', 'CVT', '1.5L 1NZ-FE', 1500,
  'Petrol', 5, '2WD', 'Sedan', 'Toyota',
  'sold', 4200000000, NULL,
  'The Toyota Premio F EX Package is a reliable and fuel-efficient sedan.',
  '["ABS", "EBD", "Airbags"]', '["Fabric Seats", "Auto AC"]', '["Halogen Headlights"]',
  1, 0, 'Grade R', 'Baridhara Showroom', 'toyota-premio-f-ex-2021'
),

-- 7. Toyota Axio G WXB
(
  'GCI-8834', 'Toyota Axio', 'G WXB', 2019, 'Reiwa 1',
  'White Pearl', '070', '#F5F5F0', '5.0', NULL,
  34000, 'NKE165-1078342', 'e-CVT', '1.5L Hybrid', 1500,
  'Hybrid', 5, '2WD', 'Sedan', 'Toyota',
  'available', 2450000000, NULL,
  'The Toyota Axio G WXB is a practical hybrid sedan with excellent fuel economy.',
  '["Toyota Safety Sense C", "Pre-Collision System"]', '["Half Leather", "7-inch Display"]', '["LED Headlights"]',
  0, 0, 'BEST VALUE', 'Baridhara Showroom', 'toyota-axio-g-wxb-2019'
),

-- 8. Mazda CX-5
(
  'GCI-8835', 'Mazda CX-5', '25S L-Package', 2020, 'Reiwa 2',
  'Machine Grey', '46G', '#686868', '4.0', NULL,
  21000, 'KF5P-390821', '6-Speed AT', '2.5L Skyactiv-G', 2500,
  'Octane', 5, '2WD', 'SUV', 'Mazda',
  'available', 3980000000, NULL,
  'The Mazda CX-5 25S L-Package offers premium driving dynamics and refined interior.',
  '["i-ACTIVSENSE", "Smart Brake Support"]', '["Leather Seats", "Bose Audio"]', '["LED Headlights", "19-inch Alloys"]',
  0, 0, NULL, 'Baridhara Showroom', 'mazda-cx-5-25s-l-package-2020'
),

-- 9. Mitsubishi Outlander G-Package
(
  'GCI-8836', 'Mitsubishi Outlander', 'G-Package', 2021, 'Reiwa 3',
  'Red Diamond', NULL, '#8B0000', '4.5', NULL,
  15000, 'GF8W-040218', '8-Speed CVT', '2.4L PHEV', 2400,
  'PHEV', 7, '4WD', 'SUV', 'Mitsubishi',
  'available', 5500000000, NULL,
  'The Mitsubishi Outlander PHEV G-Package is a versatile plug-in hybrid SUV.',
  '["Forward Collision Mitigation", "Lane Departure Warning"]', '["Leather Seats", "8-inch Display"]', '["LED Headlights", "18-inch Alloys"]',
  0, 0, NULL, 'Baridhara Showroom', 'mitsubishi-outlander-g-package-2021'
),

-- 10. Toyota Noah SI WXB
(
  'GCI-8837', 'Toyota Noah', 'SI WXB', 2018, 'Heisei 30',
  'Black', '202', '#0D0D0D', '4.0', NULL,
  56000, 'ZWR80G-0142938', 'e-CVT', '1.8L Hybrid', 2000,
  'Hybrid', 7, '2WD', 'MPV', 'Toyota',
  'available', 3450000000, NULL,
  'The Toyota Noah SI WXB is a spacious 7-seater MPV with hybrid efficiency.',
  '["Toyota Safety Sense C"]', '["Fabric Seats", "9-inch Display"]', '["LED Headlights"]',
  0, 0, NULL, 'Baridhara Showroom', 'toyota-noah-si-wxb-2018'
);


-- ==================== Stock Inward Records ====================
INSERT INTO `stock_inward` (
  `vehicle_id`, `car_name`, `package_trim`, `year_of_manufacture`,
  `color_name`, `auction_grade`, `interior_grade`, `mileage_km`,
  `chassis_code`, `transmission`, `current_stage`,
  `auction_house`, `auction_lot`, `won_price_jpy`, `bdt_equivalent`,
  `vessel_name`, `eta_date`
) VALUES
-- 1. Toyota Land Cruiser 250 VX
(
  NULL, 'Toyota Land Cruiser 250', 'VX Package', 2024,
  'Pearl White', '5.0', 'A', 1200,
  'GDJ250-001928', '8-Speed AT', 'sea_transit',
  'USS Tokyo', '#84102', 845000000, 667500000,
  'MV Glovis Corona', '2024-11-18'
),
-- 2. Toyota Prius Hybrid
(
  NULL, 'Toyota Prius Hybrid', '2.0 Z Grade', 2023,
  'Attitude Black', '4.5', 'B', 8400,
  'MXWH60-201884', 'e-CVT', 'ctg_customs',
  'TAA Chubu', '#6721', 320000000, 252800000,
  NULL, NULL
),
-- 3. Toyota Harrier
(
  NULL, 'Toyota Harrier', '2.5 Z Leather Package', 2022,
  'Precious Black', '5.0', 'S', 14300,
  'AXUH80-109482', 'e-CVT', 'sea_transit',
  'USS Tokyo', '#91203', 520000000, 410800000,
  'MV Morning Chorus', '2024-11-24'
),
-- 4. Lexus RX350h
(
  NULL, 'Lexus RX350h', 'F-Sport AWD', 2023,
  'Sonic Titanium', '5.0', 'A', 6100,
  'TALA10-003418', 'Direct Shift CVT', 'ctg_customs',
  'USS Nagoya', '#45102', 680000000, 537200000,
  NULL, NULL
),
-- 5. Toyota Alphard
(
  NULL, 'Toyota Alphard', 'Executive Lounge 4WD', 2023,
  'Luxury White Pearl', '6.0', 'S', 25,
  'AGH40-002194', 'Super CVT-i', 'japan_yard',
  'JU Yokohama', '#1203', 945000000, 746550000,
  NULL, NULL
);


-- ==================== Sales Records ====================
INSERT INTO `sales` (`vehicle_id`, `car_name`, `chassis_code`, `sale_price_bdt`, `client_area`, `payment_status`, `delivery_status`, `sale_date`) VALUES
(6, '2021 Toyota Premio F EX Package', 'NZT260-3189', 5200000000, 'Gulshan', 'cleared', 'delivered', '2024-10-05'),
(NULL, '2023 Lexus NX 350h F-Sport', 'AAZH25-0012', 13500000000, 'Dhanmondi', 'lc_settlement', 'delivered', '2024-10-12'),
(NULL, '2022 Nissan X-Trail e-POWER 4WD', 'T33-010492', 6150000000, 'Uttara', 'pending', 'brta_pending', '2024-10-18');


-- ==================== Settings ====================
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('forex_jpy_bdt', '0.79', 'number', 'Live JPY to BDT exchange rate'),
('company_name', 'Good Car Imports', 'string', 'Company display name'),
('company_phone_1', '+880 19 9242 4492', 'string', 'Primary phone number'),
('company_phone_2', '', 'string', 'Secondary phone number'),
('company_email', 'goodcarimports.bd@gmail.com', 'string', 'Company email'),
('company_address_1', 'Eastern Trade Center, 56 VIP Rd, Dhaka 1205', 'string', 'Baridhara Showroom address'),
('company_address_2', '', 'string', 'Tejgaon Service Center address'),
('whatsapp_number', '+8801992424492', 'string', 'WhatsApp contact number'),
('business_hours', 'Sat - Thu: 10:00 AM - 8:00 PM', 'string', 'Operating hours'),
('business_hours_note', 'Friday: Closed (By Appointment Only)', 'string', 'Additional hours note'),
('smtp_host', '', 'string', 'SMTP server hostname'),
('smtp_port', '587', 'number', 'SMTP server port'),
('smtp_username', '', 'string', 'SMTP username'),
('smtp_password', '', 'string', 'SMTP password'),
('smtp_from_email', 'noreply@goodcarimports.com', 'string', 'SMTP from email address'),
('smtp_from_name', 'Good Car Imports', 'string', 'SMTP from name');
