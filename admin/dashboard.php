<?php
/**
 * Good Car Imports — Admin Dashboard (Executive Overview)
 */

$pageTitle = 'Executive Overview';
$pageSubtitle = 'Real-time KPIs and business intelligence';

require_once __DIR__ . '/includes/admin-header.php';

// Calculate KPIs
// 1. Total Revenue (Sales)
$totalRevenueResult = dbFetchOne("SELECT SUM(sale_price_bdt) as total FROM sales WHERE payment_status IN ('cleared', 'lc_settlement')");
$totalRevenue = $totalRevenueResult['total'] ?? 0;

// 2. Monthly Sales
$currentMonth = date('m');
$currentYear = date('Y');
$monthlySalesResult = dbFetchOne("SELECT COUNT(*) as units, SUM(sale_price_bdt) as total FROM sales WHERE MONTH(sale_date) = ? AND YEAR(sale_date) = ?", [$currentMonth, $currentYear]);
$monthlyUnits = $monthlySalesResult['units'] ?? 0;
$monthlyRevenue = $monthlySalesResult['total'] ?? 0;

// 3. Live Inventory Stock
$stockCount = dbCount('vehicles', "status IN ('available', 'port_clearance', 'vessel_transit')");
$showroomCount = dbCount('vehicles', "status = 'available'");
$portCount = dbCount('vehicles', "status = 'port_clearance'");
$transitCount = dbCount('vehicles', "status = 'vessel_transit'");

// 4. Web Traffic & Inquiries
$totalInquiries = dbCount('inquiries');
$totalPreOrders = dbCount('pre_orders');

// Recent Sales Widget
$recentSales = dbFetchAll("SELECT * FROM sales ORDER BY sale_date DESC LIMIT 3");

// Pending Auction Inward
$pendingInward = dbFetchAll("SELECT * FROM stock_inward WHERE current_stage IN ('auction_won', 'japan_yard') ORDER BY created_at DESC LIMIT 3");

?>

<div class="admin-content">
  <!-- Sub Navigation -->
  <div class="admin-tabs">
    <a href="#" class="admin-tab active">Inventory Status</a>
    <a href="#" class="admin-tab">Auction Sheet Audit</a>
    <a href="#" class="admin-tab">BDT Forex Rates <span class="status-dot" style="display:inline-block; margin-left:4px;"></span></a>
  </div>

  <!-- KPI Grid -->
  <div class="kpi-grid">
    <!-- Total Gross Revenue -->
    <div class="kpi-card">
      <div class="kpi-header">
        <span class="kpi-title">TOTAL GROSS REVENUE</span>
        <div class="kpi-icon"><span class="material-symbols-outlined">payments</span></div>
      </div>
      <div class="kpi-value"><?= formatBDT((int)$totalRevenue, true) ?></div>
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <span class="kpi-trend positive">
          <span class="material-symbols-outlined">trending_up</span> +14.2%
        </span>
        <span style="font-size:12px; color:var(--secondary);">vs last year</span>
      </div>
      <div class="kpi-progress-bar">
        <div class="kpi-progress-fill" style="width: 78%;"></div>
      </div>
    </div>

    <!-- Monthly Sales -->
    <div class="kpi-card">
      <div class="kpi-header">
        <span class="kpi-title">MONTHLY SALES (<?= date('M') ?>)</span>
        <div class="kpi-icon"><span class="material-symbols-outlined">calendar_today</span></div>
      </div>
      <div class="kpi-value"><?= formatBDT((int)$monthlyRevenue, true) ?></div>
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <span class="kpi-trend positive">
          <span class="material-symbols-outlined">trending_up</span> +8.5%
        </span>
        <span style="font-size:12px; color:var(--secondary);"><?= $monthlyUnits ?> Units Sold</span>
      </div>
      <div class="kpi-progress-bar">
        <div class="kpi-progress-fill" style="width: 65%;"></div>
      </div>
    </div>

    <!-- Live Inventory Stock -->
    <div class="kpi-card">
      <div class="kpi-header">
        <span class="kpi-title">LIVE INVENTORY STOCK</span>
        <div class="kpi-icon"><span class="material-symbols-outlined">directions_car</span></div>
      </div>
      <div class="kpi-value"><?= $stockCount ?> <span style="font-size:16px; font-weight:600; color:var(--secondary);">Units</span></div>
      <div style="font-size:12px; color:var(--secondary); margin-bottom:12px;">
        <?= $portCount ?> in Port / <?= $transitCount ?> in Transit
      </div>
      <div class="kpi-progress-bar">
        <div class="kpi-progress-multi">
          <div style="width: <?= ($showroomCount/$stockCount)*100 ?>%; background: var(--primary);" title="Showroom: <?= $showroomCount ?>"></div>
          <div style="width: <?= ($portCount/$stockCount)*100 ?>%; background: var(--tertiary);" title="Port: <?= $portCount ?>"></div>
          <div style="width: <?= ($transitCount/$stockCount)*100 ?>%; background: var(--secondary);" title="Transit: <?= $transitCount ?>"></div>
        </div>
      </div>
    </div>

    <!-- Web Traffic -->
    <div class="kpi-card">
      <div class="kpi-header">
        <span class="kpi-title">INQUIRIES & PRE-ORDERS</span>
        <div class="kpi-icon"><span class="material-symbols-outlined">monitoring</span></div>
      </div>
      <div class="kpi-value"><?= $totalPreOrders ?> <span style="font-size:16px; font-weight:600; color:var(--secondary);">Leads</span></div>
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <span class="kpi-trend positive">
          <span class="material-symbols-outlined">trending_up</span> +28.4%
        </span>
        <span style="font-size:12px; color:var(--secondary);"><?= $totalInquiries ?> Gen. Inquiries</span>
      </div>
      <div class="kpi-progress-bar">
        <div class="kpi-progress-fill" style="width: 85%;"></div>
      </div>
    </div>
  </div>

  <!-- Main Dashboard Widgets (Removed Dummy Data) -->

  <!-- Bottom Data Widgets -->
  <div class="dashboard-grid" style="grid-template-columns: 1fr 1fr;">
    
    <!-- Recent Delivered Sales -->
    <div class="widget-card">
      <div class="widget-header">
        <h3 class="widget-title">Recent Delivered Sales</h3>
        <a href="sales-register.php" style="font-size:13px; color:var(--primary); font-weight:600;">View All</a>
      </div>
      
      <div style="display:flex; flex-direction:column; gap:16px;">
        <?php if(!empty($recentSales)): foreach($recentSales as $sale): ?>
          <div style="display:flex; align-items:center; gap:16px; padding-bottom:16px; border-bottom:1px solid var(--surface-container-high);">
            <div style="width:40px; height:40px; border-radius:50%; background:var(--tertiary-container); color:var(--tertiary); display:flex; align-items:center; justify-content:center;">
              <span class="material-symbols-outlined" style="font-size:20px;">verified</span>
            </div>
            <div style="flex:1;">
              <div style="font-weight:600; font-size:14px; margin-bottom:2px;"><?= sanitize($sale['car_name']) ?></div>
              <div style="font-size:12px; color:var(--secondary);">
                <?= sanitize($sale['client_area']) ?> • <?= sanitize($sale['chassis_code']) ?>
              </div>
            </div>
            <div style="text-align:right;">
              <div style="font-family:var(--font-headline); font-weight:700; color:var(--on-surface);"><?= formatBDT((int)$sale['sale_price_bdt']) ?></div>
              <div style="font-size:11px; font-weight:600; color:var(--tertiary); text-transform:uppercase;">LC Cleared</div>
            </div>
          </div>
        <?php endforeach; else: ?>
          <p style="font-size:14px; color:var(--secondary);">No recent sales found.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Pending Japanese Auction Inward -->
    <div class="widget-card">
      <div class="widget-header">
        <h3 class="widget-title">Pending Japanese Auction Inward</h3>
        <a href="stock-inward.php" style="font-size:13px; color:var(--primary); font-weight:600;">View Pipeline</a>
      </div>

      <div style="display:flex; flex-direction:column; gap:16px;">
        <?php if(!empty($pendingInward)): foreach($pendingInward as $inward): ?>
          <div style="display:flex; align-items:center; gap:16px; padding-bottom:16px; border-bottom:1px solid var(--surface-container-high);">
            <div style="width:48px; height:36px; border-radius:4px; background:var(--surface-container); overflow:hidden;">
              <img src="<?= ASSETS_URL ?>/images/placeholder-car.svg" alt="Car" style="width:100%; height:100%; object-fit:cover; opacity:0.5;">
            </div>
            <div style="flex:1;">
              <div style="font-weight:600; font-size:14px; margin-bottom:2px;"><?= sanitize($inward['car_name']) ?></div>
              <div style="font-size:12px; color:var(--secondary);">
                Lot <?= sanitize($inward['auction_lot']) ?> • <?= sanitize($inward['auction_house']) ?>
              </div>
            </div>
            <div style="text-align:right;">
              <div style="font-family:var(--font-headline); font-weight:700; color:var(--primary); margin-bottom:4px;">
                ¥<?= number_format((int)$inward['won_price_jpy']) ?>
              </div>
              <?= getInwardStageBadge($inward['current_stage']) ?>
            </div>
          </div>
        <?php endforeach; else: ?>
          <p style="font-size:14px; color:var(--secondary);">No pending auction inward.</p>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
