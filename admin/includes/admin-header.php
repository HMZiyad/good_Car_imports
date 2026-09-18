<?php
/**
 * Good Car Imports — Admin Header Partial
 */

require_once __DIR__ . '/auth-check.php';

$currentPage = basename($_SERVER['SCRIPT_NAME'], '.php');

// Live Forex Rate
$forexRate = getForexRate();
$activeStockCount = dbCount('vehicles', "status IN ('available', 'port_clearance', 'vessel_transit')");

// Notification counts
$pipelineCount = dbCount('stock_inward', "current_stage != 'dhaka_handover'");
$newInquiriesCount = dbCount('inquiries', "status = 'new'");
$newPreOrdersCount = dbCount('pre_orders', "status = 'new'");
$totalNotifications = $newInquiriesCount + $newPreOrdersCount;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' | Executive Portal' : 'Executive Portal | Good Car Imports' ?></title>
  
  <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/design-tokens.css">
  <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/admin.css">
</head>
<body>

<div class="admin-layout">
  
  <!-- Sidebar Overlay (mobile) -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>
  
  <!-- ===== SIDEBAR ===== -->
  <aside class="admin-sidebar">
    <div class="sidebar-header">
      <img src="<?= ASSETS_URL ?>/images/logo.png" alt="Good Car Imports" class="sidebar-logo">
      <div class="sidebar-brand-name">Good Car Imports</div>
      <div class="sidebar-portal-label">Executive Portal</div>
    </div>
    
    <div class="sidebar-cta">
      <button class="btn-sidebar-cta" onclick="openModal('addVehicleModal')">
        <span class="material-symbols-outlined">add</span>
        Add New Vehicle
      </button>
    </div>
    
    <nav class="sidebar-nav">
      <ul class="sidebar-nav-list">
        <li class="sidebar-nav-item">
          <a href="dashboard.php" class="sidebar-nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
            <span class="material-symbols-outlined">grid_view</span>
            Dashboard Overview
          </a>
        </li>
        <li class="sidebar-nav-item">
          <a href="inventory.php" class="sidebar-nav-link <?= $currentPage === 'inventory' ? 'active' : '' ?>">
            <span class="material-symbols-outlined">directions_car</span>
            Live Inventory & Fleet
          </a>
        </li>
        <li class="sidebar-nav-item">
          <a href="stock-inward.php" class="sidebar-nav-link <?= $currentPage === 'stock-inward' ? 'active' : '' ?>">
            <span class="material-symbols-outlined">local_shipping</span>
            Stock Inward
          </a>
        </li>
        <li class="sidebar-nav-item" style="margin-top: 24px;">
          <div style="padding: 0 12px 8px; font-size: 11px; font-weight: 600; color: var(--secondary); text-transform: uppercase;">Sales & Leads</div>
        </li>
        <li class="sidebar-nav-item">
          <a href="sales-register.php" class="sidebar-nav-link <?= $currentPage === 'sales-register' ? 'active' : '' ?>">
            <span class="material-symbols-outlined">receipt_long</span>
            Sales Register
          </a>
        </li>
        <li class="sidebar-nav-item">
          <a href="inquiries.php" class="sidebar-nav-link <?= $currentPage === 'inquiries' ? 'active' : '' ?>">
            <span class="material-symbols-outlined">person_add</span>
            Inquiries
            <?php if ($totalNotifications > 0): ?>
              <span class="badge" style="background:var(--error); color:var(--on-error); border-radius:12px; padding:2px 6px; font-size:10px; font-weight:700; margin-left:auto;"><?= $totalNotifications ?></span>
            <?php endif; ?>
          </a>
        </li>
      </ul>
    </nav>
    
    <div class="sidebar-footer">
      <ul class="sidebar-nav-list" style="margin-bottom: 16px;">
        <li class="sidebar-nav-item">
          <a href="logout.php" class="sidebar-nav-link" style="color: var(--error);">
            <span class="material-symbols-outlined">logout</span>
            Logout
          </a>
        </li>
      </ul>
      
      <div class="user-profile-chip">
        <div class="user-avatar">MA</div>
        <div class="user-info">
          <div class="user-name">Master Admin</div>
          <div class="user-title"><?= sanitize($currentUser['title'] ?? 'Managing Director') ?></div>
        </div>
      </div>
    </div>
  </aside>

  <!-- ===== MAIN CONTENT ===== -->
  <main class="admin-main">
    
    <!-- Top Navbar -->
    <header class="admin-topbar">
      <div class="topbar-left">
        <button class="admin-hamburger" id="adminHamburger" aria-label="Toggle sidebar menu">
          <span class="material-symbols-outlined">menu</span>
        </button>
        <div class="page-title">
          <h1><?= sanitize($pageTitle ?? 'Dashboard') ?></h1>
          <div class="page-subtitle"><?= sanitize($pageSubtitle ?? 'Good Car Imports Executive Portal') ?></div>
        </div>
        
        <div class="global-search" style="position:relative;">
          <span class="material-symbols-outlined">search</span>
          <input type="text" id="globalSearchInput" placeholder="Global Search (VIN, Chassis, Model)..." autocomplete="off">
          <span class="search-shortcut">⌘K</span>
          
          <div id="globalSearchResults" class="search-results-dropdown" style="display:none; position:absolute; top:100%; left:0; width:100%; background:var(--surface-container-lowest); box-shadow:var(--shadow-modal); border-radius:var(--radius-md); margin-top:8px; z-index:1000; max-height:400px; overflow-y:auto; border:1px solid var(--surface-container-high);">
          </div>
        </div>
      </div>
      
      <div class="topbar-right">
        <div class="status-pill" style="border-color: var(--tertiary-fixed-dim); background: var(--tertiary-container); color: var(--on-tertiary-container);">
          <div class="status-dot pulse"></div>
          Live Forex: ¥1 = ৳<?= number_format($forexRate, 2) ?>
        </div>
        
        <div class="status-pill">
          <?= $activeStockCount ?> Active Stock
        </div>
        
        <div class="topbar-actions" style="position:relative;">
          <a href="<?= SITE_URL ?>/admin/report.php" target="_blank" class="icon-btn" title="Download Full Business Report">
            <span class="material-symbols-outlined">download</span>
          </a>
          
          <?php
          $lastNotifRead = $_SESSION['last_notif_read'] ?? '2000-01-01 00:00:00';
          
          // Count unread activities
          $unreadRes = dbFetchOne("
            SELECT SUM(cnt) as total_unread FROM (
              SELECT COUNT(*) as cnt FROM inquiries WHERE created_at > ?
              UNION ALL
              SELECT COUNT(*) as cnt FROM pre_orders WHERE created_at > ?
              UNION ALL
              SELECT COUNT(*) as cnt FROM vehicles WHERE status = 'sold' AND updated_at > ?
              UNION ALL
              SELECT COUNT(*) as cnt FROM stock_inward WHERE updated_at > ?
            ) as t
          ", [$lastNotifRead, $lastNotifRead, $lastNotifRead, $lastNotifRead]);
          $unreadActivities = $unreadRes['total_unread'] ?? 0;
          
          // Fetch latest 10 activities
          $recentActivitiesList = dbFetchAll("
            (SELECT 'inquiry' as type, full_name as title, interested_in as subtitle, created_at as timestamp FROM inquiries)
            UNION ALL
            (SELECT 'pre_order' as type, full_name as title, make_model as subtitle, created_at as timestamp FROM pre_orders)
            UNION ALL
            (SELECT 'vehicle_sold' as type, car_name as title, status as subtitle, updated_at as timestamp FROM vehicles WHERE status = 'sold')
            UNION ALL
            (SELECT 'pipeline' as type, car_name as title, current_stage as subtitle, updated_at as timestamp FROM stock_inward)
            ORDER BY timestamp DESC LIMIT 10
          ");
          ?>
          <button class="icon-btn" title="Notifications" id="notificationBtn">
            <span class="material-symbols-outlined">notifications</span>
            <?php if ($unreadActivities > 0): ?>
              <div class="notification-badge" id="notificationBadge"><?= $unreadActivities ?></div>
            <?php endif; ?>
          </button>
          
          <div id="notificationDropdown" style="display:none; position:absolute; top:100%; right:0; width:300px; max-width:calc(100vw - 32px); background:var(--surface-container-lowest); box-shadow:var(--shadow-modal); border-radius:var(--radius-md); margin-top:8px; z-index:1000; border:1px solid var(--surface-container-high);">
            <div style="padding:12px 16px; border-bottom:1px solid var(--surface-container-high); font-weight:600; font-size:14px; display:flex; justify-content:space-between; align-items:center;">
              Recent Activities
            </div>
            <div style="max-height:350px; overflow-y:auto;">
              <?php if(empty($recentActivitiesList)): ?>
                <div style="padding:16px; text-align:center; color:var(--secondary); font-size:13px;">No recent activities.</div>
              <?php else: ?>
                <?php foreach($recentActivitiesList as $act): ?>
                  <div style="padding:12px 16px; border-bottom:1px solid var(--surface-container); font-size:13px; cursor:pointer;" onclick="window.location.href='inquiries.php'">
                    <div style="font-weight:600; margin-bottom:4px;"><?= sanitize($act['title']) ?></div>
                    <div style="color:var(--secondary); margin-bottom:4px;">
                      <?php if($act['type'] === 'inquiry'): ?>
                        New Inquiry: <?= sanitize($act['subtitle'] ?? 'General Inquiry') ?>
                      <?php elseif($act['type'] === 'pre_order'): ?>
                        <span style="color:var(--primary); font-weight:600;">Pre-Order: <?= sanitize($act['subtitle']) ?></span>
                      <?php elseif($act['type'] === 'vehicle_sold'): ?>
                        <span style="color:var(--error); font-weight:600;">Vehicle Sold</span>
                      <?php elseif($act['type'] === 'pipeline'): ?>
                        Pipeline Updated: <?= sanitize(str_replace('_', ' ', $act['subtitle'])) ?>
                      <?php endif; ?>
                    </div>
                    <div style="font-size:11px; color:var(--tertiary);"><?= date('M j, g:i a', strtotime($act['timestamp'])) ?></div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </header>
