<?php
/**
 * Good Car Imports — Vehicle Details Page
 */

require_once __DIR__ . '/includes/header.php';

$slug = sanitize($_GET['slug'] ?? '');
if (empty($slug)) {
    header('Location: ' . SITE_URL . '/inventory.php');
    exit;
}

// Fetch vehicle
$vehicle = dbFetchOne("SELECT * FROM vehicles WHERE slug = ?", [$slug]);

if (!$vehicle) {
    header('Location: ' . SITE_URL . '/inventory.php');
    exit;
}

// Update view count
dbExecute("UPDATE vehicles SET views_count = views_count + 1 WHERE id = ?", [$vehicle['id']]);

// Fetch photos
$photos = dbFetchAll("SELECT * FROM vehicle_photos WHERE vehicle_id = ? ORDER BY is_cover DESC, sort_order ASC", [$vehicle['id']]);
if (empty($photos) && $vehicle['cover_photo']) {
    // Fallback if no gallery photos but cover exists
    $photos[] = ['file_path' => $vehicle['cover_photo'], 'label' => 'Cover', 'is_cover' => 1];
}

// Fetch similar vehicles (same brand or body type)
$similarVehicles = dbFetchAll(
    "SELECT * FROM vehicles WHERE id != ? AND (brand = ? OR body_type = ?) AND status IN ('available', 'port_clearance') ORDER BY created_at DESC LIMIT 3",
    [$vehicle['id'], $vehicle['brand'], $vehicle['body_type']]
);

// Decode features
$safetyFeatures = json_decode($vehicle['safety_features'] ?? '[]', true) ?: [];
$interiorFeatures = json_decode($vehicle['interior_features'] ?? '[]', true) ?: [];
$exteriorFeatures = json_decode($vehicle['exterior_features'] ?? '[]', true) ?: [];

$pageTitle = $vehicle['car_name'] . ($vehicle['package_trim'] ? ' ' . $vehicle['package_trim'] : '');
$pageDescription = truncate($vehicle['description'] ?? "View details for {$pageTitle}. Imported directly from Japan.", 160);

// Status strings
$statusBadgeHtml = getStatusBadge($vehicle['status']);
?>

<section class="detail-page">
  <div class="container">
    <!-- Breadcrumb -->
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="<?= SITE_URL ?>/inventory.php">Inventory</a>
      <span class="material-symbols-outlined" style="font-size:16px;">chevron_right</span>
      <a href="<?= SITE_URL ?>/inventory.php?brand=<?= urlencode($vehicle['brand']) ?>"><?= sanitize($vehicle['brand']) ?></a>
      <span class="material-symbols-outlined" style="font-size:16px;">chevron_right</span>
      <span class="current"><?= sanitize($vehicle['car_name']) ?></span>
    </nav>

    <!-- Title Bar -->
    <div class="detail-title-bar">
      <div class="detail-title-left">
        <div class="detail-title-badges">
          <?php if ($vehicle['badge_text']): ?>
            <span class="detail-badge <?= $vehicle['badge_text'] === 'NEW ARRIVAL' ? 'new-arrival' : '' ?>"><?= sanitize($vehicle['badge_text']) ?></span>
          <?php endif; ?>
          <?php if ($vehicle['auction_grade']): ?>
            <span class="detail-badge grade">AUCTION GRADE <?= sanitize($vehicle['auction_grade']) ?></span>
          <?php endif; ?>
        </div>
        <h1><?= sanitize($pageTitle) ?></h1>
      </div>
      <?php if ($vehicle['price_bdt']): ?>
      <div class="detail-price">
        <div class="detail-price-label">ESTIMATED PRICE</div>
        <div class="detail-price-value"><?= formatBDT($vehicle['price_bdt']) ?></div>
      </div>
      <?php endif; ?>
    </div>

    <!-- Specs Bar -->
    <div class="specs-bar">
      <div class="spec-bar-item">
        <span class="material-symbols-outlined spec-icon">calendar_today</span>
        <div class="spec-bar-label">YEAR</div>
        <div class="spec-bar-value"><?= (int)$vehicle['year_of_manufacture'] ?></div>
      </div>
      <div class="spec-bar-item">
        <span class="material-symbols-outlined spec-icon">speed</span>
        <div class="spec-bar-label">MILEAGE</div>
        <div class="spec-bar-value"><?= formatMileageLong($vehicle['mileage_km']) ?></div>
      </div>
      <div class="spec-bar-item">
        <span class="material-symbols-outlined spec-icon">settings</span>
        <div class="spec-bar-label">ENGINE CC</div>
        <div class="spec-bar-value"><?= number_format((int)$vehicle['engine_cc']) ?> CC</div>
      </div>
      <div class="spec-bar-item">
        <span class="material-symbols-outlined spec-icon">local_gas_station</span>
        <div class="spec-bar-label">FUEL</div>
        <div class="spec-bar-value"><?= sanitize($vehicle['fuel_type']) ?></div>
      </div>
      <div class="spec-bar-item">
        <span class="material-symbols-outlined spec-icon">star</span>
        <div class="spec-bar-label">GRADE</div>
        <div class="spec-bar-value"><?= sanitize($vehicle['auction_grade'] ?? 'N/A') ?></div>
      </div>
    </div>

    <!-- Content Grid -->
    <div class="detail-content">
      <!-- MAIN COLUMN -->
      <div class="detail-main">
        <!-- Gallery -->
        <div class="gallery-grid">
          <div class="gallery-main">
            <img src="<?= !empty($photos) ? getUploadUrl($photos[0]['file_path']) : ASSETS_URL . '/images/placeholder-car.svg' ?>" alt="<?= sanitize($pageTitle) ?>">
            <?php if (count($photos) > 1): ?>
              <div class="gallery-counter">
                <span class="material-symbols-outlined" style="font-size:16px;">photo_library</span>
                <span>1/<?= count($photos) ?> Photos</span>
              </div>
            <?php endif; ?>
          </div>
          <div class="gallery-side gallery-side-1">
            <img src="<?= count($photos) > 1 ? getUploadUrl($photos[1]['file_path']) : ASSETS_URL . '/images/placeholder-car.svg' ?>" alt="<?= sanitize($pageTitle) ?> angle 2">
          </div>
          <div class="gallery-side gallery-side-2">
            <img src="<?= count($photos) > 2 ? getUploadUrl($photos[2]['file_path']) : ASSETS_URL . '/images/placeholder-car.svg' ?>" alt="<?= sanitize($pageTitle) ?> angle 3">
          </div>
        </div>

        <!-- Overview -->
        <div class="detail-overview">
          <h2>Vehicle Overview</h2>
          <p><?= nl2br(sanitize($vehicle['description'] ?? 'No description available for this vehicle.')) ?></p>
        </div>

        <!-- Spec Table -->
        <dl class="spec-table">
          <div class="spec-table-item">
            <dt>Stock ID</dt>
            <dd><?= sanitize($vehicle['stock_id'] ?? 'N/A') ?></dd>
          </div>
          <div class="spec-table-item">
            <dt>Chassis Code</dt>
            <dd><?= sanitize($vehicle['chassis_code']) ?></dd>
          </div>
          <div class="spec-table-item">
            <dt>Color</dt>
            <dd>
              <div style="display:flex; align-items:center; gap:6px;">
                <?php if ($vehicle['color_hex']): ?>
                  <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:<?= sanitize($vehicle['color_hex']) ?>; border:1px solid #ccc;"></span>
                <?php endif; ?>
                <?= sanitize($vehicle['color_name'] ?? 'N/A') ?>
              </div>
            </dd>
          </div>
          <div class="spec-table-item">
            <dt>Transmission</dt>
            <dd><?= sanitize($vehicle['transmission'] ?? 'N/A') ?></dd>
          </div>
          <div class="spec-table-item">
            <dt>Drive Train</dt>
            <dd><?= sanitize($vehicle['drive_train'] ?? 'N/A') ?></dd>
          </div>
          <div class="spec-table-item">
            <dt>Seats</dt>
            <dd><?= (int)$vehicle['seats'] ?> Seats</dd>
          </div>
          <div class="spec-table-item">
            <dt>Body Type</dt>
            <dd><?= sanitize($vehicle['body_type']) ?></dd>
          </div>
          <div class="spec-table-item">
            <dt>Status</dt>
            <dd><?= $statusBadgeHtml ?></dd>
          </div>
        </dl>

        <!-- Features -->
        <div class="feature-tabs">
          <div class="feature-tab-nav">
            <button class="feature-tab-btn active" onclick="switchTab('safety')">Safety</button>
            <button class="feature-tab-btn" onclick="switchTab('interior')">Interior</button>
            <button class="feature-tab-btn" onclick="switchTab('exterior')">Exterior</button>
          </div>
          
          <div id="tab-safety" class="feature-tab-content">
            <?php if (!empty($safetyFeatures)): ?>
              <ul class="feature-list">
                <?php foreach ($safetyFeatures as $feature): ?>
                  <li><span class="material-symbols-outlined">check_circle</span> <?= sanitize($feature) ?></li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p style="color:var(--secondary); padding:16px 0;">No safety features listed.</p>
            <?php endif; ?>
          </div>

          <div id="tab-interior" class="feature-tab-content" style="display:none;">
            <?php if (!empty($interiorFeatures)): ?>
              <ul class="feature-list">
                <?php foreach ($interiorFeatures as $feature): ?>
                  <li><span class="material-symbols-outlined">check_circle</span> <?= sanitize($feature) ?></li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p style="color:var(--secondary); padding:16px 0;">No interior features listed.</p>
            <?php endif; ?>
          </div>

          <div id="tab-exterior" class="feature-tab-content" style="display:none;">
            <?php if (!empty($exteriorFeatures)): ?>
              <ul class="feature-list">
                <?php foreach ($exteriorFeatures as $feature): ?>
                  <li><span class="material-symbols-outlined">check_circle</span> <?= sanitize($feature) ?></li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p style="color:var(--secondary); padding:16px 0;">No exterior features listed.</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Auction Sheet Section -->
        <div class="auction-sheet">
          <div class="auction-sheet-header">
            <h3>Verified Auction Sheet</h3>
            <button class="auction-download-btn">
              <span class="material-symbols-outlined">download</span> Download Original
            </button>
          </div>
          <div class="auction-sheet-content">
            <div class="auction-sheet-thumbnail">
              <!-- Placeholder for auction sheet image -->
              <img src="<?= ASSETS_URL ?>/images/placeholder-car.svg" style="opacity:0.3; padding:20px; background:var(--surface-container);" alt="Auction Sheet">
            </div>
            <div class="inspection-notes">
              <div class="inspection-note">
                <h4>Auction Grade</h4>
                <p><?= sanitize($vehicle['auction_grade'] ?? 'Pending') ?></p>
              </div>
              <div class="inspection-note">
                <h4>Interior Grade</h4>
                <p><?= sanitize($vehicle['interior_grade'] ?? 'Pending') ?></p>
              </div>
              <div class="inspection-note">
                <h4>Authenticity</h4>
                <p>Mileage verified by Japanese auction house.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- SIDEBAR -->
      <aside class="detail-sidebar">
        <!-- Booking Form -->
        <div class="booking-form-card">
          <h3>Booking & Inquiry</h3>
          <form id="inquiry-form" onsubmit="submitInquiry(event)">
            <input type="hidden" name="vehicle_id" value="<?= $vehicle['id'] ?>">
            <input type="hidden" name="type" value="booking">
            
            <div id="form-feedback" class="form-message" style="display:none;"></div>

            <div class="form-group">
              <label class="form-label">Full Name</label>
              <input type="text" name="full_name" class="form-input" required placeholder="Enter your name">
            </div>
            <div class="form-group">
              <label class="form-label">Phone Number</label>
              <input type="tel" name="phone" class="form-input" required placeholder="+880...">
            </div>
            <div class="form-group">
              <label class="form-label">Message (Optional)</label>
              <textarea name="message" class="form-textarea" placeholder="I would like to inspect this vehicle..."></textarea>
            </div>
            <button type="submit" class="form-submit-btn">Book Inspection</button>
          </form>

          <div class="form-divider">OR CONNECT DIRECTLY</div>

          <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $whatsappNumber) ?>?text=Hello, I am interested in the <?= urlencode($pageTitle) ?> (<?= urlencode($vehicle['chassis_code']) ?>)" target="_blank" class="whatsapp-btn">
            <span class="material-symbols-outlined filled">chat</span>
            Chat on WhatsApp
          </a>
        </div>

        <!-- Location -->
        <div class="location-card">
          <h3>Vehicle Location</h3>
          <div class="location-card-map">
            <!-- Simple iframe map placeholder for Baridhara -->
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14602.700311894985!2d90.40798995000001!3d23.79458205!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c7a0f70deb73%3A0x30c36498f90fe23!2sBaridhara%2C%20Dhaka!5e0!3m2!1sen!2sbd!4v1715000000000!5m2!1sen!2sbd" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>
          <div class="location-card-address">
            <span class="material-symbols-outlined">location_on</span>
            <span>
              <strong><?= sanitize($vehicle['showroom_location']) ?></strong><br>
              <?= sanitize(getSetting('company_address_1', 'Dhaka, Bangladesh')) ?>
            </span>
          </div>
        </div>
      </aside>
    </div>
  </div>
</section>

<!-- ===== SIMILAR VEHICLES ===== -->
<?php if (!empty($similarVehicles)): ?>
<section class="similar-section">
  <div class="container">
    <div class="section-header">
      <h2>Similar Vehicles</h2>
    </div>
    <div class="similar-grid">
      <?php foreach ($similarVehicles as $v): ?>
      <div class="vehicle-card">
        <a href="<?= SITE_URL ?>/vehicle.php?slug=<?= urlencode($v['slug']) ?>">
          <div class="vehicle-card-image">
            <img src="<?= getUploadUrl($v['cover_photo']) ?>"
                 alt="<?= sanitize($v['car_name']) ?>"
                 loading="lazy"
                 onerror="this.src='<?= ASSETS_URL ?>/images/placeholder-car.svg';">
            <?php if ($v['auction_grade']): ?>
              <span class="vehicle-grade">
                <span class="material-symbols-outlined">star</span>
                <?= sanitize($v['auction_grade']) ?> Grade
              </span>
            <?php endif; ?>
          </div>
        </a>
        <div class="vehicle-card-body">
          <p class="vehicle-card-meta"><?= (int)$v['year_of_manufacture'] ?> • <?= sanitize($v['package_trim'] ?? '') ?></p>
          <h3 class="vehicle-card-name"><?= sanitize($v['car_name']) ?></h3>
          <div class="vehicle-card-specs">
            <span>
              <span class="material-symbols-outlined">local_gas_station</span>
              <?= sanitize($v['fuel_type']) ?>
            </span>
            <span>
              <span class="material-symbols-outlined">speed</span>
              <?= formatMileage($v['mileage_km']) ?>
            </span>
          </div>
          <?php if ($v['price_bdt']): ?>
            <div class="vehicle-card-price"><?= formatBDT($v['price_bdt'], true) ?></div>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<script>
// Gallery Image Swapping
function updateGallery(element, src, current, total) {
  document.getElementById('main-image').src = src;
  document.getElementById('gallery-count').textContent = current + '/' + total + ' Photos';
  
  // Update active state on thumbs
  const thumbs = document.querySelectorAll('.gallery-thumb');
  thumbs.forEach(t => t.classList.remove('active'));
  element.classList.add('active');
}

// Tab Switching
function switchTab(tabId) {
  // Hide all contents
  document.querySelectorAll('.feature-tab-content').forEach(el => el.style.display = 'none');
  // Remove active from all buttons
  document.querySelectorAll('.feature-tab-btn').forEach(el => el.classList.remove('active'));
  
  // Show target content
  document.getElementById('tab-' + tabId).style.display = 'block';
  // Add active to clicked button (using event.target)
  event.target.classList.add('active');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
