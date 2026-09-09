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
            <?php if ($activeStockCount > 0): ?>
              <span class="sidebar-nav-badge"><?= $activeStockCount ?></span>
            <?php endif; ?>
          </a>
        </li>
        <li class="sidebar-nav-item">
          <a href="stock-inward.php" class="sidebar-nav-link <?= $currentPage === 'stock-inward' ? 'active' : '' ?>">
            <span class="material-symbols-outlined">local_shipping</span>
            Stock Inward
            <?php if ($pipelineCount > 0): ?>
              <div class="sidebar-nav-dot"></div>
            <?php endif; ?>
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
          <a href="#" class="sidebar-nav-link">
            <span class="material-symbols-outlined">person_add</span>
            Inquiries
            <?php if ($totalNotifications > 0): ?>
              <span class="sidebar-nav-badge" style="background:var(--error);color:white;"><?= $totalNotifications ?></span>
            <?php endif; ?>
          </a>
        </li>
      </ul>
    </nav>
    
    <div class="sidebar-footer">
      <ul class="sidebar-nav-list" style="margin-bottom: 16px;">
        <li class="sidebar-nav-item">
          <a href="#" class="sidebar-nav-link">
            <span class="material-symbols-outlined">settings</span>
            Portal Settings
          </a>
        </li>
        <li class="sidebar-nav-item">
          <a href="api/auth.php?action=logout" class="sidebar-nav-link" style="color: var(--error);">
            <span class="material-symbols-outlined">logout</span>
            Logout
          </a>
        </li>
      </ul>
      
      <div class="user-profile-chip">
        <div class="user-avatar"><?= sanitize($currentUser['initials'] ?? 'GCI') ?></div>
        <div class="user-info">
          <div class="user-name"><?= sanitize($currentUser['name']) ?></div>
          <div class="user-title"><?= sanitize($currentUser['title'] ?? 'Staff') ?></div>
        </div>
      </div>
    </div>
  </aside>

  <!-- ===== MAIN CONTENT ===== -->
  <main class="admin-main">
    
    <!-- Top Navbar -->
    <header class="admin-topbar">
      <div class="topbar-left">
        <div class="page-title">
          <h1><?= sanitize($pageTitle ?? 'Dashboard') ?></h1>
          <div class="page-subtitle"><?= sanitize($pageSubtitle ?? 'Good Car Imports Executive Portal') ?></div>
        </div>
        
        <div class="global-search">
          <span class="material-symbols-outlined">search</span>
          <input type="text" placeholder="Global Search (VIN, Chassis, Model)...">
          <span class="search-shortcut">⌘K</span>
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
        
        <div class="topbar-actions">
          <button class="icon-btn" title="Download Reports">
            <span class="material-symbols-outlined">download</span>
          </button>
          <button class="icon-btn" title="Notifications">
            <span class="material-symbols-outlined">notifications</span>
            <?php if ($totalNotifications > 0): ?>
              <div class="notification-badge"></div>
            <?php endif; ?>
          </button>
        </div>
      </div>
    </header>
