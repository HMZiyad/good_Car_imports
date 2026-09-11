<?php
/**
 * Good Car Imports — Full Business Report (Print-to-PDF)
 * Generates a print-optimized HTML report of all financial data,
 * inventory, stock inward, and sales.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) {
    header('Location: ../admin/login.php');
    exit;
}

// ── Gather All Data ──

// KPIs
$totalRevenueResult = dbFetchOne("SELECT SUM(sale_price_bdt) as total FROM sales WHERE payment_status IN ('cleared', 'lc_settlement')");
$totalRevenue = $totalRevenueResult['total'] ?? 0;

$currentMonth = date('m');
$currentYear = date('Y');
$monthlySalesResult = dbFetchOne("SELECT COUNT(*) as units, SUM(sale_price_bdt) as total FROM sales WHERE MONTH(sale_date) = ? AND YEAR(sale_date) = ?", [$currentMonth, $currentYear]);
$monthlyUnits = $monthlySalesResult['units'] ?? 0;
$monthlyRevenue = $monthlySalesResult['total'] ?? 0;

$totalSales = dbCount('sales');
$totalInquiries = dbCount('inquiries');
$totalPreOrders = dbCount('pre_orders');

// Inventory
$vehicles = dbFetchAll("SELECT * FROM vehicles ORDER BY status ASC, created_at DESC");
$showroomCount = dbCount('vehicles', "status = 'available'");
$portCount = dbCount('vehicles', "status = 'port_clearance'");
$transitCount = dbCount('vehicles', "status = 'vessel_transit'");
$reservedCount = dbCount('vehicles', "status = 'reserved'");
$soldCount = dbCount('vehicles', "status = 'sold'");

// Stock Inward Pipeline
$stockInward = dbFetchAll("SELECT * FROM stock_inward ORDER BY created_at DESC");

// Sales
$sales = dbFetchAll("SELECT s.*, v.cover_photo FROM sales s LEFT JOIN vehicles v ON s.vehicle_id = v.id ORDER BY s.sale_date DESC");

// Inventory value (sum of asking prices for unsold vehicles)
$inventoryValueResult = dbFetchOne("SELECT SUM(price_bdt) as total FROM vehicles WHERE status != 'sold' AND price_bdt IS NOT NULL");
$inventoryValue = $inventoryValueResult['total'] ?? 0;

// Stock Inward total investment
$inwardInvestResult = dbFetchOne("SELECT SUM(bdt_equivalent) as total FROM stock_inward");
$inwardInvest = $inwardInvestResult['total'] ?? 0;

$reportDate = date('d M Y, h:i A');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Good Car Imports — Business Report (<?= date('d M Y') ?>)</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      font-size: 11px;
      color: #1a1a1a;
      line-height: 1.5;
      background: #fff;
      padding: 24px;
    }

    @media print {
      body { padding: 0; }
      .no-print { display: none !important; }
      .page-break { page-break-before: always; }
      table { page-break-inside: auto; }
      tr { page-break-inside: avoid; page-break-after: auto; }
    }

    .print-btn {
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 12px 24px;
      background: #c0392b;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      z-index: 1000;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .print-btn:hover { background: #a93226; }

    .report-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      border-bottom: 3px solid #1a1a1a;
      padding-bottom: 16px;
      margin-bottom: 24px;
    }
    .report-header h1 { font-size: 22px; font-weight: 800; }
    .report-header .subtitle { font-size: 12px; color: #666; margin-top: 4px; }
    .report-header .date { font-size: 11px; color: #666; text-align: right; }
    .report-header .confidential {
      font-size: 10px;
      color: #c0392b;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.1em;
    }

    .section-title {
      font-size: 14px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #c0392b;
      margin: 24px 0 12px;
      padding-bottom: 6px;
      border-bottom: 1px solid #ddd;
    }

    .kpi-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 12px;
      margin-bottom: 24px;
    }
    .kpi-box {
      border: 1px solid #ddd;
      border-radius: 6px;
      padding: 12px;
      text-align: center;
    }
    .kpi-box .label { font-size: 9px; color: #888; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; }
    .kpi-box .value { font-size: 18px; font-weight: 800; color: #1a1a1a; margin-top: 4px; }
    .kpi-box .sub { font-size: 10px; color: #666; margin-top: 2px; }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 24px;
      font-size: 10px;
    }
    table th {
      background: #f5f5f5;
      font-weight: 700;
      text-transform: uppercase;
      font-size: 9px;
      letter-spacing: 0.03em;
      padding: 8px 6px;
      text-align: left;
      border-bottom: 2px solid #ddd;
    }
    table td {
      padding: 7px 6px;
      border-bottom: 1px solid #eee;
    }
    table tr:nth-child(even) { background: #fafafa; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .fw-bold { font-weight: 700; }

    .status-pill {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 9px;
      font-weight: 700;
      text-transform: uppercase;
    }
    .s-available { background: #d4edda; color: #155724; }
    .s-port { background: #fff3cd; color: #856404; }
    .s-transit { background: #d1ecf1; color: #0c5460; }
    .s-reserved { background: #e2e3f1; color: #383d6e; }
    .s-sold { background: #f8d7da; color: #721c24; }
    .s-cleared { background: #d4edda; color: #155724; }

    .summary-row {
      display: flex;
      justify-content: space-between;
      padding: 6px 0;
      border-bottom: 1px dotted #ddd;
      font-size: 11px;
    }
    .summary-row .label { color: #666; }
    .summary-row .value { font-weight: 700; }

    .footer-note {
      margin-top: 32px;
      padding-top: 12px;
      border-top: 1px solid #ddd;
      font-size: 9px;
      color: #999;
      text-align: center;
    }
  </style>
</head>
<body>

<button class="print-btn no-print" onclick="window.print()">
  📄 Save as PDF / Print
</button>

<!-- Report Header -->
<div class="report-header">
  <div>
    <h1>Good Car Imports</h1>
    <div class="subtitle">Full Business Report — Financial Summary, Inventory & Stock Pipeline</div>
  </div>
  <div>
    <div class="date">Generated: <?= $reportDate ?></div>
    <div class="confidential">Confidential — Internal Use Only</div>
  </div>
</div>

<!-- KPIs -->
<div class="section-title">Key Performance Indicators</div>
<div class="kpi-grid">
  <div class="kpi-box">
    <div class="label">Total Gross Revenue</div>
    <div class="value"><?= formatBDT((int)$totalRevenue, true) ?></div>
    <div class="sub"><?= $totalSales ?> total sales</div>
  </div>
  <div class="kpi-box">
    <div class="label">Monthly Revenue (<?= date('M Y') ?>)</div>
    <div class="value"><?= formatBDT((int)$monthlyRevenue, true) ?></div>
    <div class="sub"><?= $monthlyUnits ?> units sold</div>
  </div>
  <div class="kpi-box">
    <div class="label">Inventory Value (Asking)</div>
    <div class="value"><?= formatBDT((int)$inventoryValue, true) ?></div>
    <div class="sub"><?= count($vehicles) - $soldCount ?> active units</div>
  </div>
  <div class="kpi-box">
    <div class="label">Inward Pipeline Investment</div>
    <div class="value"><?= formatBDT((int)$inwardInvest, true) ?></div>
    <div class="sub"><?= count($stockInward) ?> vehicles in pipeline</div>
  </div>
</div>

<div class="kpi-grid" style="grid-template-columns: repeat(5, 1fr);">
  <div class="kpi-box">
    <div class="label">Showroom</div>
    <div class="value"><?= $showroomCount ?></div>
  </div>
  <div class="kpi-box">
    <div class="label">Port Clearance</div>
    <div class="value"><?= $portCount ?></div>
  </div>
  <div class="kpi-box">
    <div class="label">In Transit</div>
    <div class="value"><?= $transitCount ?></div>
  </div>
  <div class="kpi-box">
    <div class="label">Reserved</div>
    <div class="value"><?= $reservedCount ?></div>
  </div>
  <div class="kpi-box">
    <div class="label">Total Sold</div>
    <div class="value"><?= $soldCount ?></div>
  </div>
</div>

<!-- Live Inventory -->
<div class="section-title">Live Inventory & Fleet</div>
<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Car Name</th>
      <th>Chassis Code</th>
      <th>YOM</th>
      <th>Mileage</th>
      <th>Grade</th>
      <th>Fuel</th>
      <th>Status</th>
      <th class="text-right">Asking Price (BDT)</th>
      <th class="text-center">Featured</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($vehicles as $i => $v): ?>
    <tr>
      <td><?= $i + 1 ?></td>
      <td class="fw-bold"><?= sanitize($v['car_name']) ?> <?= $v['package_trim'] ? '(' . sanitize($v['package_trim']) . ')' : '' ?></td>
      <td style="font-family:monospace;"><?= sanitize($v['chassis_code']) ?></td>
      <td><?= (int)$v['year_of_manufacture'] ?></td>
      <td><?= number_format((int)$v['mileage_km']) ?> km</td>
      <td><?= sanitize($v['auction_grade'] ?? 'N/A') ?></td>
      <td><?= sanitize($v['fuel_type'] ?? '') ?></td>
      <td>
        <?php
          $statusMap = ['available' => 's-available', 'port_clearance' => 's-port', 'vessel_transit' => 's-transit', 'reserved' => 's-reserved', 'sold' => 's-sold'];
          $statusLabels = ['available' => 'Available', 'port_clearance' => 'Port', 'vessel_transit' => 'Transit', 'reserved' => 'Reserved', 'sold' => 'Sold'];
          $cls = $statusMap[$v['status']] ?? '';
          $lbl = $statusLabels[$v['status']] ?? $v['status'];
        ?>
        <span class="status-pill <?= $cls ?>"><?= $lbl ?></span>
      </td>
      <td class="text-right fw-bold"><?= $v['price_bdt'] ? formatBDT((int)$v['price_bdt']) : '—' ?></td>
      <td class="text-center"><?= $v['is_featured'] ? '★' : '' ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<!-- Stock Inward Pipeline -->
<div class="page-break"></div>
<div class="section-title">Stock Inward Pipeline (Auction → Delivery)</div>
<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Car Name</th>
      <th>Chassis Code</th>
      <th>YOM</th>
      <th>Grade</th>
      <th>Won Price (JPY)</th>
      <th class="text-right">BDT Equivalent</th>
      <th>Stage</th>
      <th>Date Added</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($stockInward as $i => $si): ?>
    <tr>
      <td><?= $i + 1 ?></td>
      <td class="fw-bold"><?= sanitize($si['car_name']) ?> <?= $si['package_trim'] ? '(' . sanitize($si['package_trim']) . ')' : '' ?></td>
      <td style="font-family:monospace;"><?= sanitize($si['chassis_code']) ?></td>
      <td><?= (int)$si['year_of_manufacture'] ?></td>
      <td><?= sanitize($si['auction_grade'] ?? 'N/A') ?></td>
      <td>¥<?= number_format((int)$si['won_price_jpy']) ?></td>
      <td class="text-right fw-bold"><?= $si['bdt_equivalent'] ? formatBDT((int)$si['bdt_equivalent']) : '—' ?></td>
      <td>
        <?php
          $stageLabels = ['auction_won' => 'Auction Won', 'japan_yard' => 'Export Yard', 'sea_transit' => 'Sea Transit', 'ctg_customs' => 'CTG Customs', 'dhaka_handover' => 'Handover'];
          echo $stageLabels[$si['current_stage']] ?? $si['current_stage'];
        ?>
      </td>
      <td><?= date('d M Y', strtotime($si['created_at'])) ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<!-- Sales Register -->
<div class="section-title">Sales Register — All Delivered & Cleared</div>
<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Car Name</th>
      <th>Chassis Code</th>
      <th>Client Area</th>
      <th>Sale Date</th>
      <th>Payment</th>
      <th class="text-right">Sale Price (BDT)</th>
    </tr>
  </thead>
  <tbody>
    <?php $totalSalesValue = 0; ?>
    <?php foreach ($sales as $i => $s): ?>
    <?php $totalSalesValue += (int)$s['sale_price_bdt']; ?>
    <tr>
      <td><?= $i + 1 ?></td>
      <td class="fw-bold"><?= sanitize($s['car_name']) ?></td>
      <td style="font-family:monospace;"><?= sanitize($s['chassis_code'] ?? 'N/A') ?></td>
      <td><?= sanitize($s['client_area'] ?? 'Direct Sale') ?></td>
      <td><?= date('d M Y', strtotime($s['sale_date'])) ?></td>
      <td><span class="status-pill s-cleared"><?= strtoupper(sanitize($s['payment_status'] ?? 'pending')) ?></span></td>
      <td class="text-right fw-bold"><?= formatBDT((int)$s['sale_price_bdt']) ?></td>
    </tr>
    <?php endforeach; ?>
    <tr style="border-top: 2px solid #1a1a1a; background: #f5f5f5;">
      <td colspan="6" class="text-right fw-bold" style="font-size: 11px;">TOTAL SALES VALUE</td>
      <td class="text-right fw-bold" style="font-size: 12px;"><?= formatBDT($totalSalesValue) ?></td>
    </tr>
  </tbody>
</table>

<!-- Inquiries & Pre-Orders Summary -->
<div class="section-title">Leads Summary</div>
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
  <div>
    <div class="summary-row">
      <span class="label">Total Contact Inquiries</span>
      <span class="value"><?= $totalInquiries ?></span>
    </div>
    <div class="summary-row">
      <span class="label">Total Pre-Order Requests</span>
      <span class="value"><?= $totalPreOrders ?></span>
    </div>
    <div class="summary-row">
      <span class="label">Combined Leads</span>
      <span class="value"><?= $totalInquiries + $totalPreOrders ?></span>
    </div>
  </div>
  <div>
    <div class="summary-row">
      <span class="label">Active Inventory Units</span>
      <span class="value"><?= count($vehicles) - $soldCount ?></span>
    </div>
    <div class="summary-row">
      <span class="label">Inward Pipeline Units</span>
      <span class="value"><?= count($stockInward) ?></span>
    </div>
    <div class="summary-row">
      <span class="label">Total Vehicles Managed</span>
      <span class="value"><?= count($vehicles) + count($stockInward) ?></span>
    </div>
  </div>
</div>

<!-- Footer -->
<div class="footer-note">
  This report was auto-generated by Good Car Imports Executive Portal on <?= $reportDate ?>.<br>
  This document is confidential and intended for internal use only. Do not distribute without authorization.
</div>

</body>
</html>
