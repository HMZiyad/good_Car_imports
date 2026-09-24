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

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !in_array($action, ['get_vehicle', 'get_stock_inward', 'global_search', 'get_inquiry', 'get_pre_order'])) {
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
    $photoPaths = [];
    $coverIndex = isset($_POST['cover_index']) ? (int)$_POST['cover_index'] : 0;
    
    if (!empty($_FILES['photos']['name'][0])) {
        foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
            $file = [
                'name' => $_FILES['photos']['name'][$key],
                'type' => $_FILES['photos']['type'][$key],
                'tmp_name' => $tmp_name,
                'error' => $_FILES['photos']['error'][$key],
                'size' => $_FILES['photos']['size'][$key],
            ];
            
            $uploadedPath = uploadImage($file, 'vehicles/' . date('Y-m'));
            if ($uploadedPath) {
                $photoPaths[] = $uploadedPath;
            }
        }
    }

    $coverPhoto = $photoPaths[$coverIndex] ?? ($photoPaths[0] ?? null);

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
            'engine_cc'           => !empty($_POST['engine_cc']) ? (int)$_POST['engine_cc'] : null,
            'body_type'           => sanitize($_POST['body_type'] ?? 'SUV'),
            'fuel_type'           => sanitize($_POST['fuel_type'] ?? 'Petrol'),
            'status'              => sanitize($_POST['status'] ?? 'available'),
            'price_bdt'           => !empty($_POST['price_bdt']) ? (int)$_POST['price_bdt'] * 100 : null,
            'is_featured'         => !empty($_POST['is_featured']) ? 1 : 0,
            'cover_photo'         => $coverPhoto
        ]);

        // Insert gallery photos
        if (!empty($photoPaths)) {
            foreach ($photoPaths as $index => $path) {
                dbInsert('vehicle_photos', [
                    'vehicle_id' => $vehicleId,
                    'file_path'  => $path,
                    'is_cover'   => ($index === $coverIndex ? 1 : 0),
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
        if (!empty($vehicle['price_bdt'])) {
            $vehicle['price_bdt'] = (int)$vehicle['price_bdt'] / 100;
        }
        $photos = dbFetchAll("SELECT file_path, is_cover, sort_order FROM vehicle_photos WHERE vehicle_id = ? ORDER BY sort_order ASC", [$id]);
        foreach ($photos as &$p) {
            $p['url'] = getUploadUrl($p['file_path']);
        }
        $vehicle['photos'] = $photos;
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
        $priceBdt = !empty($_POST['price_bdt']) ? (int)$_POST['price_bdt'] * 100 : null;
        $carName = sanitize($_POST['car_name'] ?? '');
        $chassis = sanitize($_POST['chassis_code'] ?? '');

        // Handle File Uploads
        $photoPaths = [];
        $coverIndex = isset($_POST['cover_index']) ? (int)$_POST['cover_index'] : 0;
        
        if (!empty($_FILES['photos']['name'][0])) {
            foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
                $file = [
                    'name' => $_FILES['photos']['name'][$key],
                    'type' => $_FILES['photos']['type'][$key],
                    'tmp_name' => $tmp_name,
                    'error' => $_FILES['photos']['error'][$key],
                    'size' => $_FILES['photos']['size'][$key],
                ];
                
                $uploadedPath = uploadImage($file, 'vehicles/' . date('Y-m'));
                if ($uploadedPath) {
                    $photoPaths[] = $uploadedPath;
                }
            }
        }
        
        $coverPhoto = $photoPaths[$coverIndex] ?? ($photoPaths[0] ?? null);

        $updateData = [
            'car_name'            => $carName,
            'package_trim'        => sanitize($_POST['package_trim'] ?? null),
            'year_of_manufacture' => (int)($_POST['year_of_manufacture'] ?? date('Y')),
            'color_name'          => sanitize($_POST['color_name'] ?? null),
            'chassis_code'        => $chassis,
            'mileage_km'          => (int)($_POST['mileage_km'] ?? 0),
            'auction_grade'       => sanitize($_POST['auction_grade'] ?? null),
            'transmission'        => sanitize($_POST['transmission'] ?? null),
            'engine_cc'           => !empty($_POST['engine_cc']) ? (int)$_POST['engine_cc'] : null,
            'body_type'           => sanitize($_POST['body_type'] ?? 'SUV'),
            'fuel_type'           => sanitize($_POST['fuel_type'] ?? 'Petrol'),
            'status'              => $status,
            'price_bdt'           => $priceBdt,
            'is_featured'         => !empty($_POST['is_featured']) ? 1 : 0,
        ];
        
        $updateData['cover_photo'] = $coverPhoto;
        
        // Delete old gallery
        dbExecute("DELETE FROM vehicle_photos WHERE vehicle_id = ?", [$id]);
        
        // Insert new gallery
        if (!empty($photoPaths)) {
            foreach ($photoPaths as $index => $path) {
                dbInsert('vehicle_photos', [
                    'vehicle_id' => $id,
                    'file_path'  => $path,
                    'is_cover'   => ($index === $coverIndex ? 1 : 0),
                    'sort_order' => $index
                ]);
            }
        }

        dbUpdate('vehicles', $updateData, "id = ?", [$id]);
        
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
} elseif ($action === 'delete_vehicle') {
    $id = (int)($_POST['vehicle_id'] ?? 0);
    if (!$id) jsonResponse(['success' => false, 'message' => 'Vehicle ID missing']);
    
    try {
        dbExecute('START TRANSACTION');
        
        // Delete the vehicle (DB foreign keys will handle ON DELETE CASCADE for photos and SET NULL for sales/pipeline)
        dbExecute("DELETE FROM vehicles WHERE id = ?", [$id]);
        
        dbExecute('COMMIT');
        jsonResponse(['success' => true, 'message' => 'Vehicle deleted successfully.']);
    } catch (Exception $e) {
        dbExecute('ROLLBACK');
        error_log('Delete Vehicle Error: ' . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'A database error occurred.']);
    }
} elseif ($action === 'get_stock_inward') {
    $id = (int)($_GET['id'] ?? 0);
    $inward = dbFetchOne("SELECT * FROM stock_inward WHERE id = ?", [$id]);
    if ($inward) {
        jsonResponse(['success' => true, 'data' => $inward]);
    } else {
        jsonResponse(['success' => false, 'message' => 'Record not found']);
    }
} elseif ($action === 'edit_stock_inward') {
    $id = (int)($_POST['inward_id'] ?? 0);
    if (!$id) jsonResponse(['success' => false, 'message' => 'Inward ID missing']);
    
    try {
        dbExecute('START TRANSACTION');

        $stage = sanitize($_POST['current_stage'] ?? 'auction_won');
        $chassis = sanitize($_POST['chassis_code'] ?? '');
        $carName = sanitize($_POST['car_name'] ?? '');

        dbUpdate('stock_inward', [
            'car_name'            => $carName,
            'package_trim'        => sanitize($_POST['package_trim'] ?? null),
            'year_of_manufacture' => (int)($_POST['year_of_manufacture'] ?? date('Y')),
            'color_name'          => sanitize($_POST['color_name'] ?? null),
            'chassis_code'        => $chassis,
            'mileage_km'          => (int)($_POST['mileage_km'] ?? 0),
            'auction_grade'       => sanitize($_POST['auction_grade'] ?? null),
            'auction_house'       => sanitize($_POST['auction_house'] ?? null),
            'auction_lot'         => sanitize($_POST['auction_lot'] ?? null),
            'won_price_jpy'       => (int)($_POST['won_price_jpy'] ?? 0),
            'bdt_equivalent'      => (int)($_POST['bdt_equivalent'] ?? 0),
            'current_stage'       => $stage
        ], "id = ?", [$id]);

        // Auto-transfer to live inventory if reaching CTG Customs or Dhaka Handover
        if (in_array($stage, ['ctg_customs', 'dhaka_handover'])) {
            $inwardRecord = dbFetchOne("SELECT * FROM stock_inward WHERE id = ?", [$id]);
            
            // Check if it already exists in vehicles
            $vehicle = dbFetchOne("SELECT id, status FROM vehicles WHERE chassis_code = ?", [$chassis]);
            
            $targetStatus = ($stage === 'ctg_customs') ? 'port_clearance' : 'available';

            if (!$vehicle) {
                // Insert new vehicle
                $slug = generateSlug($carName . '-' . substr(preg_replace('/[^a-zA-Z0-9]/', '', $chassis), -6));
                
                $vehicleId = dbInsert('vehicles', [
                    'slug'                => $slug,
                    'chassis_code'        => $chassis,
                    'car_name'            => $carName,
                    'brand'               => explode(' ', $carName)[0] ?? null,
                    'package_trim'        => sanitize($_POST['package_trim'] ?? null),
                    'year_of_manufacture' => (int)($_POST['year_of_manufacture'] ?? date('Y')),
                    'color_name'          => sanitize($_POST['color_name'] ?? null),
                    'mileage_km'          => (int)($_POST['mileage_km'] ?? 0),
                    'auction_grade'       => sanitize($_POST['auction_grade'] ?? null),
                    'status'              => $targetStatus,
                ]);

                // Link them
                dbUpdate('stock_inward', ['vehicle_id' => $vehicleId], "id = ?", [$id]);
            } else {
                // If it already exists, just update the status if it's not already sold/reserved
                if (!in_array($vehicle['status'], ['sold', 'reserved'])) {
                    dbUpdate('vehicles', ['status' => $targetStatus], "id = ?", [$vehicle['id']]);
                }
                // Ensure it's linked
                if (!$inwardRecord['vehicle_id']) {
                    dbUpdate('stock_inward', ['vehicle_id' => $vehicle['id']], "id = ?", [$id]);
                }
            }
        }
        
        dbExecute('COMMIT');
        jsonResponse(['success' => true, 'message' => 'Stock inward record updated successfully.']);
    } catch (Exception $e) {
        dbExecute('ROLLBACK');
        error_log('Edit Stock Inward Error: ' . $e->getMessage());
        jsonResponse(['success' => false, 'message' => 'A database error occurred.']);
    }
} elseif ($action === 'global_search') {
    $q = sanitize($_GET['q'] ?? '');
    if (strlen($q) < 2) {
        jsonResponse(['success' => true, 'data' => []]);
    }
    
    $term = "%$q%";
    $results = [];
    
    // Search Live Inventory
    $inventory = dbFetchAll("SELECT id, car_name, chassis_code, status FROM vehicles WHERE chassis_code LIKE ? OR car_name LIKE ? OR brand LIKE ? LIMIT 5", [$term, $term, $term]);
    foreach ($inventory as $v) {
        $results[] = [
            'type' => 'Inventory',
            'title' => sanitize($v['car_name']),
            'subtitle' => sanitize($v['chassis_code']),
            'badge' => sanitize(str_replace('_', ' ', $v['status'])),
            'link' => 'inventory.php?edit_vehicle=' . $v['id']
        ];
    }
    
    // Search Stock Inward
    $inward = dbFetchAll("SELECT id, car_name, chassis_code, current_stage FROM stock_inward WHERE chassis_code LIKE ? OR car_name LIKE ? LIMIT 5", [$term, $term]);
    foreach ($inward as $v) {
        $results[] = [
            'type' => 'Pipeline',
            'title' => sanitize($v['car_name']),
            'subtitle' => sanitize($v['chassis_code']),
            'badge' => sanitize(str_replace('_', ' ', $v['current_stage'])),
            'link' => 'stock-inward.php?edit_inward=' . $v['id']
        ];
    }
    
    // Search Sales
    $sales = dbFetchAll("SELECT id, car_name, chassis_code, payment_status FROM sales WHERE chassis_code LIKE ? OR car_name LIKE ? LIMIT 5", [$term, $term]);
    foreach ($sales as $v) {
        $results[] = [
            'type' => 'Sale',
            'title' => sanitize($v['car_name']),
            'subtitle' => sanitize($v['chassis_code']),
            'badge' => sanitize(str_replace('_', ' ', $v['payment_status'])),
            'link' => 'sales-register.php' // No edit modal for sales yet
        ];
    }
    
    jsonResponse(['success' => true, 'data' => $results]);

} elseif ($action === 'get_inquiry') {
    $id = (int)($_GET['id'] ?? 0);
    $data = dbFetchOne("SELECT * FROM inquiries WHERE id = ?", [$id]);
    if ($data) {
        if ($data['status'] === 'new') {
            dbExecute("UPDATE inquiries SET status = 'contacted' WHERE id = ?", [$id]);
        }
        jsonResponse(['success' => true, 'data' => $data]);
    } else {
        jsonResponse(['success' => false, 'message' => 'Inquiry not found']);
    }
} elseif ($action === 'delete_inquiry') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
        dbExecute("DELETE FROM inquiries WHERE id = ?", [$id]);
        jsonResponse(['success' => true, 'message' => 'Inquiry deleted successfully.']);
    } else {
        jsonResponse(['success' => false, 'message' => 'Invalid ID']);
    }
} elseif ($action === 'get_pre_order') {
    $id = (int)($_GET['id'] ?? 0);
    $data = dbFetchOne("SELECT * FROM pre_orders WHERE id = ?", [$id]);
    if ($data) {
        if ($data['status'] === 'new') {
            dbExecute("UPDATE pre_orders SET status = 'sourcing' WHERE id = ?", [$id]);
        }
        jsonResponse(['success' => true, 'data' => $data]);
    } else {
        jsonResponse(['success' => false, 'message' => 'Pre-order not found']);
    }
} elseif ($action === 'delete_pre_order') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
        dbExecute("DELETE FROM pre_orders WHERE id = ?", [$id]);
        jsonResponse(['success' => true, 'message' => 'Pre-order deleted successfully.']);
    } else {
        jsonResponse(['success' => false, 'message' => 'Invalid ID']);
    }
} elseif ($action === 'read_notifications') {
    $_SESSION['last_notif_read'] = date('Y-m-d H:i:s');
    jsonResponse(['success' => true]);
} else {
    jsonResponse(['success' => false, 'message' => 'Invalid API action.']);
}
