<?php
/**
 * Good Car Imports — Vehicle Inventory Page
 */

$pageTitle = 'Vehicle Inventory';
$pageDescription = 'Browse our collection of high-quality imported vehicles. Authentic auction sheets and transparent pricing on every car.';

require_once __DIR__ . '/includes/header.php';

// ── Filters ──
$search  = sanitize($_GET['search'] ?? '');
$brand   = sanitize($_GET['brand'] ?? '');
$body    = $_GET['body'] ?? [];
$priceMin = (int) ($_GET['price_min'] ?? 0);
$priceMax = (int) ($_GET['price_max'] ?? 0);
$grades  = $_GET['grade'] ?? [];
$sort    = sanitize($_GET['sort'] ?? 'latest');
$page    = max(1, (int) ($_GET['page'] ?? 1));

// Build WHERE clause
$where = "status IN ('available', 'port_clearance', 'vessel_transit', 'pre_booked')";
$params = [];

if ($search) {
    $where .= " AND (car_name LIKE ? OR package_trim LIKE ? OR chassis_code LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($brand) {
    $where .= " AND brand = ?";
    $params[] = $brand;
}

if (!empty($body)) {
    $bodyPlaceholders = implode(',', array_fill(0, count($body), '?'));
    $where .= " AND body_type IN ($bodyPlaceholders)";
    foreach ($body as $b) $params[] = sanitize($b);
}

if ($priceMin > 0) {
    $where .= " AND price_bdt >= ?";
    $params[] = $priceMin * 10000000; // Convert Lakh to paisa
}

if ($priceMax > 0) {
    $where .= " AND price_bdt <= ?";
    $params[] = $priceMax * 10000000;
}

if (!empty($grades)) {
    $gradePlaceholders = implode(',', array_fill(0, count($grades), '?'));
    $where .= " AND auction_grade IN ($gradePlaceholders)";
    foreach ($grades as $g) $params[] = sanitize($g);
}

// Sort
$orderBy = match ($sort) {
    'price_low'  => 'price_bdt ASC',
    'price_high' => 'price_bdt DESC',
    'oldest'     => 'created_at ASC',
    'mileage'    => 'mileage_km ASC',
    default      => 'created_at DESC',
};

// Pagination
$totalVehicles = dbCount('vehicles', $where, $params);
$pagination = getPagination($totalVehicles, FRONTEND_ITEMS_PER_PAGE, $page);

// Fetch vehicles
$vehicles = dbFetchAll(
    "SELECT * FROM vehicles WHERE $where ORDER BY $orderBy LIMIT ? OFFSET ?",
    array_merge($params, [$pagination['per_page'], $pagination['offset']])
);

// Get all brands for dropdown
$allBrands = dbFetchAll("SELECT DISTINCT brand FROM vehicles WHERE brand IS NOT NULL ORDER BY brand ASC");
?>

<section class="inventory-page">
  <div class="container">
    <!-- Mobile Filter Toggle -->
    <button class="filter-toggle-btn" id="filter-toggle">
      <span class="material-symbols-outlined">tune</span>
      Filters
    </button>

    <div class="inventory-layout">
      <!-- ===== FILTER SIDEBAR ===== -->
      <aside class="filter-sidebar" id="filter-sidebar">
        <form method="GET" action="inventory.php" id="filter-form">
          <div class="filter-header">
            <h2>Filters</h2>
            <a href="inventory.php" class="filter-reset">RESET</a>
          </div>

          <!-- Quick Search -->
          <div class="filter-group">
            <label>Quick Search</label>
            <div class="filter-search-wrapper">
              <span class="material-symbols-outlined">search</span>
              <input type="text" name="search" class="filter-input"
                     placeholder="Model name..." value="<?= $search ?>">
            </div>
          </div>

          <!-- Brand -->
          <div class="filter-group">
            <label>Brand</label>
            <select name="brand" class="filter-select">
              <option value="">All Brands</option>
              <?php foreach ($allBrands as $b): ?>
                <option value="<?= sanitize($b['brand']) ?>" <?= $brand === $b['brand'] ? 'selected' : '' ?>>
                  <?= sanitize($b['brand']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Body Type -->
          <div class="filter-group">
            <label>Body Type</label>
            <div class="filter-checkboxes">
              <?php foreach (['SUV', 'Sedan', 'Hatchback', 'MPV'] as $type): ?>
                <label class="filter-checkbox">
                  <input type="checkbox" name="body[]" value="<?= $type ?>"
                         <?= in_array($type, (array)$body) ? 'checked' : '' ?>>
                  <?= $type ?>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Price Range -->
          <div class="filter-group">
            <label>Price (Lakh BDT)</label>
            <div class="filter-price-row">
              <input type="number" name="price_min" class="filter-input"
                     placeholder="Min" value="<?= $priceMin ?: '' ?>">
              <span>—</span>
              <input type="number" name="price_max" class="filter-input"
                     placeholder="Max" value="<?= $priceMax ?: '' ?>">
            </div>
          </div>

          <!-- Auction Grade -->
          <div class="filter-group">
            <label>Auction Grade</label>
            <div class="filter-grade-pills">
              <?php foreach (['4.0', '4.5', '5.0', 'S'] as $g): ?>
                <label class="grade-pill <?= in_array($g, (array)$grades) ? 'active' : '' ?>">
                  <input type="checkbox" name="grade[]" value="<?= $g ?>" hidden
                         <?= in_array($g, (array)$grades) ? 'checked' : '' ?>>
                  <?= $g ?>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Sort (hidden, synced from header) -->
          <input type="hidden" name="sort" value="<?= $sort ?>" id="filter-sort">

          <button type="submit" class="filter-apply-btn">Apply Filters</button>
        </form>
      </aside>

      <!-- ===== INVENTORY CONTENT ===== -->
      <div class="inventory-content">
        <div class="inventory-header">
          <div>
            <h1>Current Inventory</h1>
            <p class="subtitle">Showing <?= $totalVehicles ?> high-quality imported vehicles</p>
          </div>
          <div class="sort-dropdown">
            <span>Sort by:</span>
            <select id="sort-select" onchange="document.getElementById('filter-sort').value=this.value; document.getElementById('filter-form').submit();">
              <option value="latest" <?= $sort === 'latest' ? 'selected' : '' ?>>Latest Arrivals</option>
              <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
              <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
              <option value="mileage" <?= $sort === 'mileage' ? 'selected' : '' ?>>Lowest Mileage</option>
            </select>
          </div>
        </div>

        <!-- Vehicle Grid -->
        <div class="vehicle-grid">
          <?php foreach ($vehicles as $v): ?>
          <div class="vehicle-card">
            <a href="<?= SITE_URL ?>/vehicle.php?slug=<?= urlencode($v['slug']) ?>" style="display:block;">
              <div class="vehicle-card-image">
                <img src="<?= getUploadUrl($v['cover_photo']) ?>"
                     alt="<?= sanitize($v['car_name']) ?>"
                     loading="lazy"
                     onerror="this.src='<?= ASSETS_URL ?>/images/placeholder-car.svg';">
                <?php if ($v['badge_text']): ?>
                  <span class="vehicle-badge <?= $v['badge_text'] === 'BEST VALUE' ? 'best-value' : ($v['badge_text'] === 'Top Grade' ? 'top-grade' : '') ?>">
                    <?= sanitize($v['badge_text']) ?>
                  </span>
                <?php endif; ?>
                <?php if ($v['auction_grade']): ?>
                  <span class="vehicle-grade">
                    ☆ <?= sanitize($v['auction_grade']) ?> Grade
                  </span>
                <?php endif; ?>
              </div>
            </a>
            <div class="vehicle-card-body">
              <div class="vehicle-card-header-row">
                <p class="vehicle-card-meta"><?= (int)$v['year_of_manufacture'] ?> • <?= sanitize($v['package_trim'] ?? '') ?></p>
                <?php if ($v['price_bdt']): ?>
                  <div class="vehicle-card-price"><?= formatBDT($v['price_bdt'], true) ?></div>
                <?php endif; ?>
              </div>
              <h3 class="vehicle-card-name"><?= sanitize($v['car_name']) ?></h3>
              
              <div class="vehicle-card-specs">
                <?php if ($v['engine_cc']): ?>
                  <span>
                    <span class="material-symbols-outlined">settings</span>
                    <?= number_format($v['engine_cc']) ?>cc
                  </span>
                <?php endif; ?>
                <span>
                  <span class="material-symbols-outlined">local_gas_station</span>
                  <?= sanitize($v['fuel_type']) ?>
                </span>
                <span>
                  <span class="material-symbols-outlined">speed</span>
                  <?= formatMileage($v['mileage_km']) ?>
                </span>
              </div>
              
              <a href="<?= SITE_URL ?>/vehicle.php?slug=<?= urlencode($v['slug']) ?>" class="vehicle-card-action">
                View Details <span class="material-symbols-outlined" style="font-size:18px;">arrow_forward</span>
              </a>
            </div>
          </div>
          <?php endforeach; ?>

          <?php if (empty($vehicles)): ?>
            <p style="grid-column: 1 / -1; text-align: center; color: var(--secondary); padding: 64px 0;">
              No vehicles match your filters. Try adjusting your search criteria.
            </p>
          <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?= renderPagination($pagination, 'inventory.php') ?>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
