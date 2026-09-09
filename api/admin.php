<?php
/**
 * Good Car Imports — Admin API Endpoints
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Ensure user is authenticated
if (!isLoggedIn()) {
    jsonResponse(['success' => false, 'message' => 'Unauthorized access'], 401);
}

$action = sanitize($_GET['action'] ?? '');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $action !== 'get_vehicle') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
}

if ($action === 'add_vehicle') {
    // ── Add Vehicle to Live Inventory ──
    $chassis = sanitize($_POST['chassis_code'] ?? '');
    $carName = sanitize($_POST['car_name'] ?? '');
    
    if (empty($chassis) || empty($carName)) {
        jsonResponse(['success' => false, 'message' => 'Car Name and Chassis Code are required.']);
    }

    // Check if chassis already exists
    $exists = dbFetchOne("SELECT id FROM vehicles WHERE chassis_code = ?", [$chassis]);
    if ($exists) {
        jsonResponse(['success' => false, 'message' => 'A vehicle with this chassis code already exists in the inventory.']);
    }

    // Generate slug
    $slug = generateSlug($carName . '-' . substr(preg_replace('/[^a-zA-Z0-9]/', '', $chassis), -6));

    // Handle File Uploads
    $coverPhoto = null;
    $photoPaths = [];
    
    if (!empty($_FILES['photos']['name'][0])) {
        foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
            $file = [
                'name' => $_FILES['photos']['name'][$key],
                'type' => $_FILES['photos']['type'][$key],
                'tmp_name' => $tmp_name,
                'error' => $_FILES['photos']['error'][$key],
                'size' => $_FILES['photos']['size'][$key],
            ];
            
            $uploadedPath = handleFileUpload($file, 'vehicles/' . date('Y-m'));
            if ($uploadedPath) {
                $photoPaths[] = $uploadedPath;
                if ($coverPhoto === null) {
                    $coverPhoto = $uploadedPath; // First image is cover
                }
            }
        }
    }

    try {
        dbExecute('START TRANSACTION');

        $vehicleId = dbInsert('vehicles', [
            'slug'                => $slug,
            'chassis_code'        => $chassis,
            'car_name'            => $carName,
            'brand'               => explode(' ', $carName)[0] ?? null, // Simple brand extraction
            'package_trim'        => sanitize($_POST['package_trim'] ?? null),
            'year_of_manufacture' => (int)($_POST['year_of_manufacture'] ?? date('Y')),
            'color_name'          => sanitize($_POST['color_name'] ?? null),
            'mileage_km'          => (int)($_POST['mileage_km'] ?? 0),
            'auction_grade'       => sanitize($_POST['auction_grade'] ?? null),
            'transmission'        => sanitize($_POST['transmission'] ?? null),
            'body_type'           => sanitize($_POST['body_type'] ?? 'SUV'),
            'fuel_type'           => sanitize($_POST['fuel_type'] ?? 'Petrol'),
            'status'              => sanitize($_POST['status'] ?? 'available'),
            'price_bdt'           => !empty($_POST['price_bdt']) ? (int)$_POST['price_bdt'] : null,
            'cover_photo'         => $coverPhoto
        ]);

        // Insert gallery photos
        if (!empty($photoPaths)) {
            foreach ($photoPaths as $index => $path) {
                dbInsert('vehicle_photos', [
                    'vehicle_id' => $vehicleId,
                    'file_path'  => $path,
                    'is_cover'   => ($index === 0 ? 1 : 0),
                    'sort_order' => $index
                ]);
            }
        }

        dbExecute('COMMIT');
        jsonResponse(['success' => true, 'message' => 'Vehicle successfully added to inventory.']);

    } catch (Exception $e) {
        dbExecute('ROLLBACK');
        error_log('Add Vehicle Error: ' . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'A database error occurred. Please try again.']);
    }

} elseif ($action === 'add_stock_inward') {
    // ── Add Stock Inward (Auction Win Pipeline) ──
    $chassis = sanitize($_POST['chassis_code'] ?? '');
    $carName = sanitize($_POST['car_name'] ?? '');
    $wonPriceJpy = (int)($_POST['won_price_jpy'] ?? 0);
    
    if (empty($chassis) || empty($carName) || $wonPriceJpy <= 0) {
        jsonResponse(['success' => false, 'message' => 'Car Name, Chassis Code, and Won Price (JPY) are mandatory.']);
    }

    try {
        dbInsert('stock_inward', [
            'chassis_code'        => $chassis,
            'car_name'            => $carName,
            'package_trim'        => sanitize($_POST['package_trim'] ?? null),
            'year_of_manufacture' => (int)($_POST['year_of_manufacture'] ?? date('Y')),
            'color_name'          => sanitize($_POST['color_name'] ?? null),
            'mileage_km'          => (int)($_POST['mileage_km'] ?? 0),
            'auction_grade'       => sanitize($_POST['auction_grade'] ?? null),
            'auction_house'       => sanitize($_POST['auction_house'] ?? null),
            'auction_lot'         => sanitize($_POST['auction_lot'] ?? null),
            'won_price_jpy'       => $wonPriceJpy,
            'bdt_equivalent'      => (int)($_POST['bdt_equivalent'] ?? 0),
            'current_stage'       => sanitize($_POST['current_stage'] ?? 'auction_won')
        ]);

        jsonResponse(['success' => true, 'message' => 'Vehicle successfully added to inward pipeline.']);
    } catch (Exception $e) {
        error_log('Add Stock Inward Error: ' . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'A database error occurred. Ensure the chassis code is unique in the inward pipeline.']);
    }

} elseif ($action === 'get_vehicle') {
    $id = (int)($_GET['id'] ?? 0);
    $vehicle = dbFetchOne("SELECT * FROM vehicles WHERE id = ?", [$id]);
    if ($vehicle) {
        jsonResponse(['success' => true, 'data' => $vehicle]);
    } else {
        jsonResponse(['success' => false, 'message' => 'Vehicle not found']);
    }
} elseif ($action === 'edit_vehicle') {
    $id = (int)($_POST['vehicle_id'] ?? 0);
    if (!$id) jsonResponse(['success' => false, 'message' => 'Vehicle ID missing']);
    
    try {
        dbExecute('START TRANSACTION');
        
        $status = sanitize($_POST['status'] ?? 'available');
        $priceBdt = !empty($_POST['price_bdt']) ? (int)$_POST['price_bdt'] : null;
        $carName = sanitize($_POST['car_name'] ?? '');
        $chassis = sanitize($_POST['chassis_code'] ?? '');

        dbUpdate('vehicles', [
            'car_name'            => $carName,
            'package_trim'        => sanitize($_POST['package_trim'] ?? null),
            'year_of_manufacture' => (int)($_POST['year_of_manufacture'] ?? date('Y')),
            'color_name'          => sanitize($_POST['color_name'] ?? null),
            'chassis_code'        => $chassis,
            'mileage_km'          => (int)($_POST['mileage_km'] ?? 0),
            'auction_grade'       => sanitize($_POST['auction_grade'] ?? null),
            'transmission'        => sanitize($_POST['transmission'] ?? null),
            'body_type'           => sanitize($_POST['body_type'] ?? 'SUV'),
            'fuel_type'           => sanitize($_POST['fuel_type'] ?? 'Petrol'),
            'status'              => $status,
            'price_bdt'           => $priceBdt,
        ], "id = ?", [$id]);
        
        if ($status === 'sold') {
            $exists = dbFetchOne("SELECT id FROM sales WHERE vehicle_id = ?", [$id]);
            if (!$exists) {
                dbInsert('sales', [
                    'vehicle_id'      => $id,
                    'car_name'        => $carName,
                    'chassis_code'    => $chassis,
                    'sale_price_bdt'  => $priceBdt,
                    'client_area'     => 'Direct Sale',
                    'payment_status'  => 'cleared',
                    'delivery_status' => 'delivered',
                    'sale_date'       => date('Y-m-d')
                ]);
            }
        } else {
            dbExecute("DELETE FROM sales WHERE vehicle_id = ?", [$id]);
        }
        
        dbExecute('COMMIT');
        
        jsonResponse(['success' => true, 'message' => 'Vehicle updated successfully.']);
    } catch (Exception $e) {
        dbExecute('ROLLBACK');
        error_log('Edit Vehicle Error: ' . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'A database error occurred.']);
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Invalid API action.']);
}
