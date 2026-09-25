<?php
/**
 * Good Car Imports — Admin Stock Inward
 */

$pageTitle = 'Stock Inward & Logistics Pipeline';
$pageSubtitle = 'Manage vehicles from auction win to showroom delivery';

require_once __DIR__ . '/includes/admin-header.php';

// ── Filters & Pagination ──
$stageFilter = sanitize($_GET['stage'] ?? 'all');
$page = max(1, (int)($_GET['page'] ?? 1));

// Build WHERE
$where = "1=1";
$params = [];
if ($stageFilter !== 'all') {
    if ($stageFilter === 'awaiting_vessel') {
        $where .= " AND current_stage IN ('auction_won', 'japan_yard')";
    } else {
        $where .= " AND current_stage = ?";
        $params[] = $stageFilter;
    }
}

// KPI Counts
$totalInward = dbCount('stock_inward');
$seaTransit = dbCount('stock_inward', "current_stage = 'sea_transit'");
$ctgCustoms = dbCount('stock_inward', "current_stage = 'ctg_customs'");
$japanYard = dbCount('stock_inward', "current_stage = 'japan_yard'");
$awaitingVessel = dbCount('stock_inward', "current_stage IN ('auction_won', 'japan_yard')");

$totalValueResult = dbFetchOne("SELECT SUM(bdt_equivalent) as total FROM stock_inward");
$totalValue = $totalValueResult['total'] ?? 0;

// Fetch Data
$pagination = getPagination(dbCount('stock_inward', $where, $params), ITEMS_PER_PAGE, $page);
$records = dbFetchAll(
    "SELECT * FROM stock_inward WHERE $where ORDER BY created_at DESC LIMIT ? OFFSET ?",
    array_merge($params, [$pagination['per_page'], $pagination['offset']])
);
?>

<div class="admin-content">

  <!-- KPI Grid -->
  <div class="kpi-grid">
    <div class="kpi-card">
      <div class="kpi-header">
        <span class="kpi-title">TOTAL INWARD PIPELINE</span>
        <div class="kpi-icon"><span class="material-symbols-outlined">account_tree</span></div>
      </div>
      <div class="kpi-value"><?= $totalInward ?> <span style="font-size:16px; font-weight:600; color:var(--secondary);">Units</span></div>
      <div style="font-size:13px; font-weight:600; color:var(--tertiary); margin-top:4px;">
        <?= formatBDT((int)$totalValue, true) ?> Value
      </div>
    </div>
    
    <div class="kpi-card">
      <div class="kpi-header">
        <span class="kpi-title">SEA TRANSIT</span>
        <div class="kpi-icon"><span class="material-symbols-outlined">directions_boat</span></div>
      </div>
      <div class="kpi-value"><?= $seaTransit ?> <span style="font-size:16px; font-weight:600; color:var(--secondary);">Afloat</span></div>
      <div style="font-size:12px; color:var(--secondary); margin-top:8px;">
        Next ETA: <?= date('M d', strtotime('+12 days')) ?>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-header">
        <span class="kpi-title">CHITTAGONG CUSTOMS</span>
        <div class="kpi-icon"><span class="material-symbols-outlined">warehouse</span></div>
      </div>
      <div class="kpi-value"><?= $ctgCustoms ?> <span style="font-size:16px; font-weight:600; color:var(--secondary);">In Yard</span></div>
      <div style="font-size:12px; color:var(--secondary); margin-top:8px;">
        Awaiting assessment
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-header">
        <span class="kpi-title">JAPAN YARD</span>
        <div class="kpi-icon"><span class="material-symbols-outlined">flag</span></div>
      </div>
      <div class="kpi-value"><?= $japanYard ?> <span style="font-size:16px; font-weight:600; color:var(--secondary);">Pre-export</span></div>
      <div style="font-size:12px; color:var(--secondary); margin-top:8px;">
        Pending JAAI inspection
      </div>
    </div>
  </div>

  <!-- Lifecycle Stepper -->
  <div class="pipeline-stepper">
    <div class="pipeline-step completed">
      <div class="step-icon"><span class="material-symbols-outlined">gavel</span></div>
      <div class="step-label">Auction Won</div>
    </div>
    <div class="pipeline-connector active"></div>
    
    <div class="pipeline-step completed">
      <div class="step-icon"><span class="material-symbols-outlined">flag</span></div>
      <div class="step-label">Japan Export Yard</div>
    </div>
    <div class="pipeline-connector active"></div>
    
    <div class="pipeline-step active">
      <div class="step-icon"><span class="material-symbols-outlined">directions_boat</span></div>
      <div class="step-label">Sea Transit</div>
    </div>
    <div class="pipeline-connector"></div>
    
    <div class="pipeline-step">
      <div class="step-icon"><span class="material-symbols-outlined">warehouse</span></div>
      <div class="step-label">Chittagong Customs</div>
    </div>
    <div class="pipeline-connector"></div>
    
    <div class="pipeline-step">
      <div class="step-icon"><span class="material-symbols-outlined">storefront</span></div>
      <div class="step-label">Tejgaon Hub Handover</div>
    </div>
  </div>

  <!-- Table Container -->
  <div class="table-container">
    <div class="table-toolbar">
      <!-- Filter Tabs -->
      <div style="display:flex; gap:16px; font-size:13px; font-weight:600;">
        <a href="stock-inward.php?stage=all" class="<?= $stageFilter==='all' ? 'text-primary' : 'text-secondary' ?>" style="<?= $stageFilter==='all' ? 'color:var(--primary);' : 'color:var(--secondary);' ?>">All Inward (<?= $totalInward ?>)</a>
        <a href="stock-inward.php?stage=sea_transit" class="<?= $stageFilter==='sea_transit' ? 'text-primary' : 'text-secondary' ?>" style="<?= $stageFilter==='sea_transit' ? 'color:var(--primary);' : 'color:var(--secondary);' ?>">Sea Transit (<?= $seaTransit ?>)</a>
        <a href="stock-inward.php?stage=ctg_customs" class="<?= $stageFilter==='ctg_customs' ? 'text-primary' : 'text-secondary' ?>" style="<?= $stageFilter==='ctg_customs' ? 'color:var(--primary);' : 'color:var(--secondary);' ?>">Chittagong Customs (<?= $ctgCustoms ?>)</a>
        <a href="stock-inward.php?stage=awaiting_vessel" class="<?= $stageFilter==='awaiting_vessel' ? 'text-primary' : 'text-secondary' ?>" style="<?= $stageFilter==='awaiting_vessel' ? 'color:var(--primary);' : 'color:var(--secondary);' ?>">Awaiting Vessel (<?= $awaitingVessel ?>)</a>
      </div>
      
      <div style="display:flex; gap:12px;">
        <button class="btn btn-outline" style="padding:6px 12px; font-size:13px;">
          <span class="material-symbols-outlined" style="font-size:16px;">receipt_long</span> Export B/L Manifest
        </button>
        <button class="btn btn-primary" onclick="openModal('stockInwardModal')">
          <span class="material-symbols-outlined" style="font-size:18px;">add</span> Add Stock Inward
        </button>
      </div>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
      <thead>
        <tr>
          <th>Vehicle & Chassis</th>
          <th>Auction Source</th>
          <th>Valuation (JPY / BDT)</th>
          <th>Logistics Status</th>
          <th>Documents</th>
          <th style="text-align:right;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($records as $r): ?>
        <tr>
          <td>
            <div style="font-weight:600; color:var(--on-surface); margin-bottom:2px;">
              <?php
                $words = explode(' ', sanitize($r['car_name']));
                $firstLine = implode(' ', array_slice($words, 0, 3));
                $secondLine = implode(' ', array_slice($words, 3));
                echo $firstLine . ($secondLine ? '<br>' . $secondLine : '');
              ?>
            </div>
            <div style="font-size:12px; color:var(--secondary); margin-bottom:6px;"><?= (int)$r['year_of_manufacture'] ?> • <?= sanitize($r['color_name']) ?> • <?= sanitize($r['package_trim']) ?></div>
            <span class="chassis-pill"><?= sanitize($r['chassis_code']) ?></span>
          </td>
          <td>
            <div style="font-weight:500;"><?= sanitize($r['auction_house']) ?></div>
            <div style="font-size:12px; color:var(--secondary);">Lot: <?= sanitize($r['auction_lot']) ?></div>
            <div style="font-size:12px; color:var(--secondary); margin-top:2px;">Grade: <?= sanitize($r['auction_grade']) ?></div>
          </td>
          <td>
            <div style="font-weight:600; color:var(--primary);">¥<?= number_format((int)$r['won_price_jpy']) ?></div>
            <div style="font-size:12px; color:var(--secondary); margin-top:2px;">≈ ৳<?= number_format((int)$r['bdt_equivalent']) ?></div>
          </td>
          <td>
            <div style="margin-bottom:6px;">
              <?= getInwardStageBadge($r['current_stage']) ?>
            </div>
            <?php if ($r['vessel_name']): ?>
              <div style="font-size:11px; color:var(--secondary);">
                <span class="material-symbols-outlined" style="font-size:12px; vertical-align:middle;">directions_boat</span> <?= sanitize($r['vessel_name']) ?>
                <br>ETA: <?= $r['eta_date'] ? date('M d, Y', strtotime($r['eta_date'])) : 'TBA' ?>
              </div>
            <?php endif; ?>
          </td>
          <td>
            <div style="display:flex; flex-direction:column; gap:4px; font-size:11px; font-weight:600; color:var(--secondary);">
              <?php if($r['jaai_cert']): ?><span style="color:var(--tertiary);"><span class="material-symbols-outlined" style="font-size:12px; vertical-align:middle;">check_circle</span> JAAI Certificate</span><?php else: ?><span><span class="material-symbols-outlined" style="font-size:12px; vertical-align:middle;">cancel</span> JAAI Pending</span><?php endif; ?>
              <?php if($r['bill_of_lading']): ?><span style="color:var(--tertiary);"><span class="material-symbols-outlined" style="font-size:12px; vertical-align:middle;">check_circle</span> Bill of Lading</span><?php else: ?><span><span class="material-symbols-outlined" style="font-size:12px; vertical-align:middle;">cancel</span> B/L Pending</span><?php endif; ?>
            </div>
          </td>
          <td style="text-align:right;">
            <?php if ($r['current_stage'] === 'sea_transit'): ?>
              <button class="btn btn-outline" style="padding:4px 10px; font-size:12px; margin-bottom:4px; width:130px; justify-content:center;">Track AIS</button>
            <?php elseif ($r['current_stage'] === 'ctg_customs'): ?>
              <button class="btn btn-primary" style="padding:4px 10px; font-size:12px; margin-bottom:4px; width:130px; justify-content:center; background:var(--error); border-color:var(--error);">Process Duty</button>
            <?php elseif ($r['current_stage'] === 'auction_won' || $r['current_stage'] === 'japan_yard'): ?>
              <button class="btn btn-outline" style="padding:4px 10px; font-size:12px; margin-bottom:4px; width:130px; justify-content:center;">Export Docs</button>
            <?php endif; ?>
            <button type="button" class="btn btn-outline" onclick="openEditStockInwardModal(<?= $r['id'] ?>)" style="padding:4px 10px; font-size:12px; border:none; color:var(--primary); width:130px; justify-content:center;">Edit Record</button>
          </td>
        </tr>
        <?php endforeach; ?>

        <?php if (empty($records)): ?>
        <tr>
          <td colspan="6" style="text-align:center; padding:48px 0; color:var(--secondary);">
            No records found for the selected stage.
          </td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
    </div>

    <!-- Table Footer / Pagination -->
    <div style="display:flex; justify-content:space-between; align-items:center; padding:16px 24px; border-top:1px solid var(--surface-container-high);">
      <div style="font-size:13px; color:var(--secondary);">
        Showing <?= min($pagination['per_page'], $pagination['total']) ?> of <?= $pagination['total'] ?> pipeline records
      </div>
      
      <?php if($pagination['total_pages'] > 1): ?>
      <div style="display:flex; gap:8px;">
        <a href="stock-inward.php?stage=<?= $stageFilter ?>&page=<?= $pagination['current_page'] - 1 ?>" 
           class="btn btn-outline" style="padding:6px 12px; <?= !$pagination['has_prev'] ? 'pointer-events:none; opacity:0.5;' : '' ?>">
          Previous
        </a>
        <a href="stock-inward.php?stage=<?= $stageFilter ?>&page=<?= $pagination['current_page'] + 1 ?>" 
           class="btn btn-outline" style="padding:6px 12px; <?= !$pagination['has_next'] ? 'pointer-events:none; opacity:0.5;' : '' ?>">
          Next
        </a>
      </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<!-- Stock Inward Modal -->
<div class="modal-container" id="stockInwardModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); z-index:1001; max-width:700px; width:100%; box-shadow:var(--shadow-modal);">
  <form id="stockInwardForm" onsubmit="submitStockInward(event)">
    <div class="modal-header">
      <div class="modal-title">
        <h2>Inward Vehicle from Japan Auction</h2>
        <span class="modal-badge">Step 1/5</span>
      </div>
      <button type="button" class="icon-btn" onclick="closeModal('stockInwardModal')">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    
    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
      <div id="stockInwardFeedback" class="form-message" style="display:none;"></div>

      <div class="form-group mandatory-field">
        <label class="form-label">Pipeline Stage</label>
        <select name="current_stage" class="form-select" required>
          <option value="auction_won">1. Auction Won (Awaiting Transport)</option>
          <option value="japan_yard">2. Japan Export Yard</option>
          <option value="sea_transit">3. Sea Transit Afloat</option>
        </select>
      </div>

      <div class="form-section-title" style="margin-top: 24px;">Vehicle Identification</div>
      <div class="form-group mandatory-field highlight-field">
        <label class="form-label">Chassis Code (MANDATORY & CIRCLED)</label>
        <input type="text" name="chassis_code" class="form-input" required placeholder="e.g. GDJ250-001928">
      </div>

      <div class="grid-2-col">
        <div class="form-group mandatory-field">
          <label class="form-label">Car Name</label>
          <input type="text" name="car_name" class="form-input" required placeholder="e.g. Toyota Land Cruiser 250">
        </div>
        <div class="form-group">
          <label class="form-label">Package/Trim</label>
          <input type="text" name="package_trim" class="form-input" placeholder="e.g. VX Package">
        </div>
        <div class="form-group mandatory-field">
          <label class="form-label">Year of Manufacture</label>
          <input type="number" name="year_of_manufacture" class="form-input" required min="2000">
        </div>
        <div class="form-group">
          <label class="form-label">Color Name</label>
          <input type="text" name="color_name" class="form-input" placeholder="e.g. Pearl White">
        </div>
        <div class="form-group">
          <label class="form-label">Mileage (KM)</label>
          <input type="number" name="mileage_km" class="form-input" min="0">
        </div>
        <div class="form-group">
          <label class="form-label">Auction Grade</label>
          <input type="text" name="auction_grade" class="form-input" placeholder="e.g. 5.0">
        </div>
      </div>

      <div class="form-section-title">Auction Win & Commercial Valuation Details</div>
      <div class="grid-2-col">
        <div class="form-group mandatory-field">
          <label class="form-label">Auction House</label>
          <select name="auction_house" class="form-select" required>
            <option value="">Select House</option>
            <option value="USS Tokyo">USS Tokyo</option>
            <option value="USS Nagoya">USS Nagoya</option>
            <option value="TAA Chubu">TAA Chubu</option>
            <option value="JU Yokohama">JU Yokohama</option>
            <option value="Other">Other</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Lot Number</label>
          <input type="text" name="auction_lot" class="form-input" placeholder="e.g. #84102">
        </div>
        <div class="form-group mandatory-field">
          <label class="form-label">Won Price (JPY)</label>
          <input type="number" name="won_price_jpy" id="wonPriceJpy" class="form-input" required oninput="calculateBdt(this.value)">
        </div>
        <div class="form-group">
          <label class="form-label">BDT Equivalent (Auto-calc @ ¥1 = ৳<?= number_format(getForexRate(), 2) ?>)</label>
          <input type="number" name="bdt_equivalent" id="bdtEquivalent" class="form-input" readonly style="background:var(--surface-container-low);">
        </div>
      </div>
      
      <input type="hidden" id="currentForexRate" value="<?= getForexRate() ?>">

    </div>
    
    <div class="modal-footer">
      <div class="modal-footer-note">
        <span class="material-symbols-outlined" style="color:var(--tertiary);font-size:16px;">verified_user</span>
        All 8 mandatory ledger fields verified
      </div>
      <div class="modal-actions">
        <button type="button" class="btn btn-outline" onclick="closeModal('stockInwardModal')">Cancel</button>
        <button type="submit" class="btn btn-primary" id="stockInwardSubmitBtn">Confirm Stock Inward</button>
      </div>
    </div>
  </form>
</div>

<!-- Edit Stock Inward Modal -->
<div class="modal-container" id="editStockInwardModal" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); z-index:1001; max-width:700px; width:100%; box-shadow:var(--shadow-modal);">
  <form id="editStockInwardForm" onsubmit="submitEditStockInward(event)">
    <input type="hidden" name="inward_id" id="edit_inward_id">
    <div class="modal-header">
      <div class="modal-title">
        <h2>Edit Pipeline Record</h2>
      </div>
      <button type="button" class="icon-btn" onclick="closeModal('editStockInwardModal')">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    
    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
      <div id="editStockInwardFeedback" class="form-message" style="display:none;"></div>

      <div class="form-group mandatory-field">
        <label class="form-label">Pipeline Stage</label>
        <select name="current_stage" id="edit_inward_stage" class="form-select" required>
          <option value="auction_won">1. Auction Won (Awaiting Transport)</option>
          <option value="japan_yard">2. Japan Export Yard</option>
          <option value="sea_transit">3. Sea Transit Afloat</option>
          <option value="ctg_customs">4. Chittagong Customs</option>
          <option value="dhaka_handover">5. Dhaka Handover</option>
        </select>
      </div>

      <div class="form-section-title" style="margin-top: 24px;">Vehicle Identification</div>
      <div class="form-group mandatory-field highlight-field">
        <label class="form-label">Chassis Code (MANDATORY)</label>
        <input type="text" name="chassis_code" id="edit_inward_chassis" class="form-input" required>
      </div>

      <div class="grid-2-col">
        <div class="form-group mandatory-field">
          <label class="form-label">Car Name</label>
          <input type="text" name="car_name" id="edit_inward_car_name" class="form-input" required>
        </div>
        <div class="form-group">
          <label class="form-label">Package/Trim</label>
          <input type="text" name="package_trim" id="edit_inward_trim" class="form-input">
        </div>
        <div class="form-group mandatory-field">
          <label class="form-label">Year of Manufacture</label>
          <input type="number" name="year_of_manufacture" id="edit_inward_yom" class="form-input" required min="2000">
        </div>
        <div class="form-group">
          <label class="form-label">Color Name</label>
          <input type="text" name="color_name" id="edit_inward_color" class="form-input">
        </div>
        <div class="form-group">
          <label class="form-label">Mileage (KM)</label>
          <input type="number" name="mileage_km" id="edit_inward_mileage" class="form-input" min="0">
        </div>
        <div class="form-group">
          <label class="form-label">Auction Grade</label>
          <input type="text" name="auction_grade" id="edit_inward_grade" class="form-input">
        </div>
      </div>

      <div class="form-section-title">Auction Win & Commercial Valuation Details</div>
      <div class="grid-2-col">
        <div class="form-group mandatory-field">
          <label class="form-label">Auction House</label>
          <select name="auction_house" id="edit_inward_house" class="form-select" required>
            <option value="">Select House</option>
            <option value="USS Tokyo">USS Tokyo</option>
            <option value="USS Nagoya">USS Nagoya</option>
            <option value="TAA Chubu">TAA Chubu</option>
            <option value="JU Yokohama">JU Yokohama</option>
            <option value="Other">Other</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Lot Number</label>
          <input type="text" name="auction_lot" id="edit_inward_lot" class="form-input">
        </div>
        <div class="form-group mandatory-field">
          <label class="form-label">Won Price (JPY)</label>
          <input type="number" name="won_price_jpy" id="edit_inward_jpy" class="form-input" required oninput="calculateBdtEdit(this.value)">
        </div>
        <div class="form-group">
          <label class="form-label">BDT Equivalent</label>
          <input type="number" name="bdt_equivalent" id="edit_inward_bdt" class="form-input" readonly style="background:var(--surface-container-low);">
        </div>
      </div>
    </div>
    
    <div class="modal-footer">
      <div class="modal-footer-note">
        <span class="material-symbols-outlined" style="color:var(--tertiary);font-size:16px;">edit</span>
        Update inward pipeline record
      </div>
      <div class="modal-actions">
        <button type="button" class="btn btn-outline" onclick="closeModal('editStockInwardModal')">Cancel</button>
        <button type="submit" class="btn btn-primary" id="editStockInwardSubmitBtn">Save Changes</button>
      </div>
    </div>
  </form>
</div>

<script>
function calculateBdt(jpy) {
  const rate = parseFloat(document.getElementById('currentForexRate').value);
  const bdt = jpy * rate;
  document.getElementById('bdtEquivalent').value = isNaN(bdt) ? '' : Math.round(bdt);
}

function calculateBdtEdit(jpy) {
  const rate = parseFloat(document.getElementById('currentForexRate').value);
  const bdt = jpy * rate;
  document.getElementById('edit_inward_bdt').value = isNaN(bdt) ? '' : Math.round(bdt);
}
</script>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
