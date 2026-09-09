<?php
/**
 * Good Car Imports — Sales Register
 */

$pageTitle = 'Sales Register';
$pageSubtitle = 'View all realized sales and delivered vehicles';

require_once __DIR__ . '/includes/admin-header.php';

// Pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;

$totalSales = dbCount('sales');
$pagination = getPagination($totalSales, $perPage, $page);

// Fetch Data
$sales = dbFetchAll(
    "SELECT s.*, v.slug, v.cover_photo FROM sales s LEFT JOIN vehicles v ON s.vehicle_id = v.id ORDER BY s.sale_date DESC LIMIT ? OFFSET ?",
    [$pagination['per_page'], $pagination['offset']]
);
?>

<div class="admin-content">
  <!-- Table Container -->
  <div class="table-container">
    <div class="table-toolbar">
      <div style="font-size:16px; font-weight:600; color:var(--on-surface);">
        All Delivered & Cleared Sales (<?= $totalSales ?>)
      </div>
      
      <div style="display:flex; gap:12px;">
        <button class="btn btn-outline">
          <span class="material-symbols-outlined" style="font-size:18px;">download</span> Export CSV
        </button>
      </div>
    </div>

    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 250px;">Car Name</th>
          <th>Chassis Code</th>
          <th>Client Area</th>
          <th>Sale Date</th>
          <th>Payment Status</th>
          <th style="text-align:right;">Sale Price (BDT)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($sales as $s): ?>
        <tr>
          <td>
            <div class="td-car">
              <?php if ($s['cover_photo']): ?>
                <img src="<?= getUploadUrl($s['cover_photo']) ?>" alt="Car" class="td-car-img" onerror="this.src='<?= ASSETS_URL ?>/images/placeholder-car.svg'">
              <?php else: ?>
                <img src="<?= ASSETS_URL ?>/images/placeholder-car.svg" alt="Car" class="td-car-img">
              <?php endif; ?>
              <div class="td-car-info">
                <span class="td-car-name"><?= sanitize($s['car_name']) ?></span>
                <?php if ($s['slug']): ?>
                <a href="<?= SITE_URL ?>/vehicle.php?slug=<?= urlencode($s['slug']) ?>" target="_blank" style="font-size:11px; color:var(--primary);">View Details</a>
                <?php endif; ?>
              </div>
            </div>
          </td>
          <td><span class="chassis-pill"><?= sanitize($s['chassis_code'] ?? 'N/A') ?></span></td>
          <td><?= sanitize($s['client_area'] ?? 'Direct Sale') ?></td>
          <td style="font-weight:500;"><?= date('d M Y', strtotime($s['sale_date'])) ?></td>
          <td>
            <span class="grade-pill" style="font-size:11px; padding:2px 8px; border-radius:4px; font-weight:700; background:var(--tertiary-container); color:var(--on-tertiary-container);">
              <?= strtoupper(sanitize($s['payment_status'] ?? 'pending')) ?>
            </span>
          </td>
          <td style="text-align:right; font-weight:700; color:var(--on-surface);">
            <?= formatBDT((int)$s['sale_price_bdt']) ?>
          </td>
        </tr>
        <?php endforeach; ?>

        <?php if (empty($sales)): ?>
        <tr>
          <td colspan="6" style="text-align:center; padding:48px 0; color:var(--secondary);">
            No sales records found.
          </td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <!-- Pagination -->
    <div style="display:flex; justify-content:space-between; align-items:center; padding:16px 24px; border-top:1px solid var(--surface-container-high);">
      <div style="font-size:13px; color:var(--secondary);">
        Showing <?= count($sales) ?> of <?= $totalSales ?> sales
      </div>
      
      <?php if($pagination['total_pages'] > 1): ?>
      <div style="display:flex; gap:8px;">
        <a href="sales-register.php?page=<?= $pagination['current_page'] - 1 ?>" 
           class="btn btn-outline" style="padding:6px 12px; <?= !$pagination['has_prev'] ? 'pointer-events:none; opacity:0.5;' : '' ?>">
          Previous
        </a>
        <a href="sales-register.php?page=<?= $pagination['current_page'] + 1 ?>" 
           class="btn btn-outline" style="padding:6px 12px; <?= !$pagination['has_next'] ? 'pointer-events:none; opacity:0.5;' : '' ?>">
          Next
        </a>
      </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
