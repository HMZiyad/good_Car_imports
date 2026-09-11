<?php
/**
 * Good Car Imports — Admin Live Inventory
 */

$pageTitle = 'Live Inventory & Fleet';
$pageSubtitle = 'Manage current stock and vehicle details';

require_once __DIR__ . '/includes/admin-header.php';

// ── Filters & Pagination ──
$statusFilter = sanitize($_GET['status'] ?? 'all');
$page = max(1, (int)($_GET['page'] ?? 1));

// Build WHERE
$where = "1=1";
$params = [];
if ($statusFilter !== 'all') {
    if ($statusFilter === 'sold_reserved') {
        $where .= " AND status IN ('sold', 'reserved')";
    } else {
        $where .= " AND status = ?";
        $params[] = $statusFilter;
    }
}

// KPI Counts
$totalUnits = dbCount('vehicles');
$showroomUnits = dbCount('vehicles', "status = 'available'");
$portUnits = dbCount('vehicles', "status = 'port_clearance'");
$vesselUnits = dbCount('vehicles', "status = 'vessel_transit'");
$soldReservedUnits = dbCount('vehicles', "status IN ('sold', 'reserved')");

$totalValueResult = dbFetchOne("SELECT SUM(price_bdt) as total FROM vehicles WHERE status IN ('available', 'port_clearance', 'vessel_transit')");
$totalValue = $totalValueResult['total'] ?? 0;

// Fetch Data
$pagination = getPagination(dbCount('vehicles', $where, $params), ITEMS_PER_PAGE, $page);
$vehicles = dbFetchAll(
    "SELECT * FROM vehicles WHERE $where ORDER BY created_at DESC LIMIT ? OFFSET ?",
    array_merge($params, [$pagination['per_page'], $pagination['offset']])
);
?>

<div class="admin-content">
  <!-- Sub Navigation -->
  <div class="admin-tabs">
    <a href="#" class="admin-tab active">Live Inventory</a>
  </div>

  <!-- KPI Grid -->
  <div class="kpi-grid">
    <div class="kpi-card">
      <div class="kpi-header">
        <span class="kpi-title">TOTAL STOCK UNITS</span>
        <div class="kpi-icon"><span class="material-symbols-outlined">directions_car</span></div>
      </div>
      <div class="kpi-value"><?= $totalUnits ?></div>
    </div>
    
    <div class="kpi-card">
      <div class="kpi-header">
        <span class="kpi-title">TOTAL STOCK VALUE</span>
        <div class="kpi-icon"><span class="material-symbols-outlined">payments</span></div>
      </div>
      <div class="kpi-value"><?= formatBDT((int)$totalValue, true) ?></div>
    </div>

    <div class="kpi-card">
      <div class="kpi-header">
        <span class="kpi-title">SHOWROOM READY</span>
        <div class="kpi-icon"><span class="material-symbols-outlined">storefront</span></div>
      </div>
      <div class="kpi-value"><?= $showroomUnits ?> <span style="font-size:16px; font-weight:600; color:var(--secondary);">Units</span></div>
      <div style="font-size:12px; color:var(--secondary); margin-top:8px;">
        All at Baridhara Showroom
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-header">
        <span class="kpi-title">IN CUSTOMS & PORT</span>
        <div class="kpi-icon"><span class="material-symbols-outlined">directions_boat</span></div>
      </div>
      <div class="kpi-value"><?= $portUnits + $vesselUnits ?> <span style="font-size:16px; font-weight:600; color:var(--secondary);">Units</span></div>
      <div style="font-size:12px; color:var(--secondary); margin-top:8px;">
        <?= $portUnits ?> Chittagong / <?= $vesselUnits ?> Sea Transit
      </div>
    </div>
  </div>

  <!-- Table Container -->
  <div class="table-container">
    <div class="table-toolbar">
      <!-- Filter Tabs -->
      <div style="display:flex; gap:16px; font-size:13px; font-weight:600;">
        <a href="inventory.php?status=all" class="<?= $statusFilter==='all' ? 'text-primary' : 'text-secondary' ?>" style="<?= $statusFilter==='all' ? 'color:var(--primary);' : 'color:var(--secondary);' ?>">All Stocks (<?= $totalUnits ?>)</a>
        <a href="inventory.php?status=available" class="<?= $statusFilter==='available' ? 'text-primary' : 'text-secondary' ?>" style="<?= $statusFilter==='available' ? 'color:var(--primary);' : 'color:var(--secondary);' ?>">Showroom Ready (<?= $showroomUnits ?>)</a>
        <a href="inventory.php?status=port_clearance" class="<?= $statusFilter==='port_clearance' ? 'text-primary' : 'text-secondary' ?>" style="<?= $statusFilter==='port_clearance' ? 'color:var(--primary);' : 'color:var(--secondary);' ?>">Chittagong Port (<?= $portUnits ?>)</a>
        <a href="inventory.php?status=vessel_transit" class="<?= $statusFilter==='vessel_transit' ? 'text-primary' : 'text-secondary' ?>" style="<?= $statusFilter==='vessel_transit' ? 'color:var(--primary);' : 'color:var(--secondary);' ?>">Vessel Transit (<?= $vesselUnits ?>)</a>
        <a href="inventory.php?status=sold_reserved" class="<?= $statusFilter==='sold_reserved' ? 'text-primary' : 'text-secondary' ?>" style="<?= $statusFilter==='sold_reserved' ? 'color:var(--primary);' : 'color:var(--secondary);' ?>">Sold/Reserved (<?= $soldReservedUnits ?>)</a>
      </div>
      
      <div style="display:flex; gap:12px;">
        <button class="btn btn-outline">
          <span class="material-symbols-outlined" style="font-size:18px;">edit_document</span> Batch Update
        </button>
        <button class="btn btn-primary" onclick="openModal('addVehicleModal')">
          <span class="material-symbols-outlined" style="font-size:18px;">add</span> Add New Vehicle
        </button>
      </div>
    </div>

    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 250px;">Car Name & Spec</th>
          <th>Package / Trim</th>
          <th>YOM</th>
          <th>Color</th>
          <th>Grade</th>
          <th>Mileage (KM)</th>
          <th>Chassis Code</th>
          <th>Trans.</th>
          <th>Status</th>
          <th style="text-align:right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($vehicles as $v): ?>
        <tr>
          <td>
            <div class="td-car">
              <img src="<?= getUploadUrl($v['cover_photo']) ?>" alt="Car" class="td-car-img" onerror="this.onerror=null; this.src='<?= ASSETS_URL ?>/images/placeholder-car.svg'">
              <div class="td-car-info">
                <span class="td-car-name"><?= sanitize($v['car_name']) ?></span>
                <span class="td-car-spec"><?= sanitize($v['engine_spec'] ?? $v['engine_cc'].'cc') ?></span>
              </div>
            </div>
          </td>
          <td style="font-weight:500;"><?= sanitize($v['package_trim'] ?? '-') ?></td>
          <td>
            <div style="font-weight:600;"><?= (int)$v['year_of_manufacture'] ?></div>
            <?php if($v['reiwa_year']): ?>
              <div style="font-size:11px; color:var(--secondary);"><?= sanitize($v['reiwa_year']) ?></div>
            <?php endif; ?>
          </td>
          <td>
            <div style="display:flex; align-items:center; gap:6px;">
              <?php if($v['color_hex']): ?>
                <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background-color:<?= sanitize($v['color_hex']) ?>; border:1px solid var(--surface-container-high);"></span>
              <?php endif; ?>
              <?= sanitize($v['color_name'] ?? '-') ?>
            </div>
          </td>
          <td>
            <span class="grade-pill" style="font-size:11px; padding:2px 8px; border-radius:4px; font-weight:700; background:var(--surface-container-high);"><?= sanitize($v['auction_grade'] ?? '-') ?></span>
          </td>
          <td style="font-family:monospace;"><?= number_format((int)$v['mileage_km']) ?></td>
          <td>
            <span class="chassis-pill"><?= sanitize($v['chassis_code']) ?></span>
          </td>
          <td style="font-size:13px;"><?= sanitize($v['transmission'] ?? '-') ?></td>
          <td><?= getStatusBadge($v['status']) ?></td>
          <td style="text-align:right;">
            <div style="display:flex; justify-content:flex-end; gap:4px;">
              <button type="button" class="icon-btn" onclick="openEditVehicleModal(<?= $v['id'] ?>)" title="Edit Vehicle">
                <span class="material-symbols-outlined">edit</span>
              </button>
              <a href="<?= SITE_URL ?>/vehicle.php?slug=<?= urlencode($v['slug']) ?>" target="_blank" class="icon-btn" title="View Frontend">
                <span class="material-symbols-outlined">visibility</span>
              </a>
              <button type="button" class="icon-btn" onclick="deleteVehicle(<?= $v['id'] ?>)" title="Delete Vehicle" style="color:var(--error);">
                <span class="material-symbols-outlined">delete</span>
              </button>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>

        <?php if (empty($vehicles)): ?>
        <tr>
          <td colspan="10" style="text-align:center; padding:48px 0; color:var(--secondary);">
            No vehicles found matching the selected criteria.
          </td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <!-- Table Footer / Pagination -->
    <div style="display:flex; justify-content:space-between; align-items:center; padding:16px 24px; border-top:1px solid var(--surface-container-high);">
      <div style="font-size:13px; color:var(--secondary);">
        Showing <?= min($pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?> imported vehicles
      </div>
      
      <?php if($pagination['total_pages'] > 1): ?>
      <div style="display:flex; gap:8px;">
        <a href="inventory.php?status=<?= $statusFilter ?>&page=<?= $pagination['current_page'] - 1 ?>" 
           class="btn btn-outline" style="padding:6px 12px; <?= !$pagination['has_prev'] ? 'pointer-events:none; opacity:0.5;' : '' ?>">
          Previous
        </a>
        <a href="inventory.php?status=<?= $statusFilter ?>&page=<?= $pagination['current_page'] + 1 ?>" 
           class="btn btn-outline" style="padding:6px 12px; <?= !$pagination['has_next'] ? 'pointer-events:none; opacity:0.5;' : '' ?>">
          Next
        </a>
      </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
