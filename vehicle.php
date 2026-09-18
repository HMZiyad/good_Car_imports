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
        <div class="spec-bar-label">YEAR of Manufacture</div>
        <div class="spec-bar-value"><?= (int)$vehicle['year_of_manufacture'] ?></div>
      </div>
      <div class="spec-bar-item">
        <span class="material-symbols-outlined spec-icon">star</span>
        <div class="spec-bar-label">GRADE</div>
        <div class="spec-bar-value"><?= sanitize($vehicle['auction_grade'] ?? 'N/A') ?></div>
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
    </div>

    <!-- Content Grid -->
    <div class="detail-content">
      <!-- MAIN COLUMN -->
      <div class="detail-main">
        <!-- Gallery -->
        <div class="gallery-grid">
          <div class="gallery-main" style="cursor:pointer;" onclick="openLightbox(0)">
            <img id="main-gallery-img" src="<?= !empty($photos) ? getUploadUrl($photos[0]['file_path']) : ASSETS_URL . '/images/placeholder-car.svg' ?>" alt="<?= sanitize($pageTitle) ?>">
            <?php if (count($photos) > 1): ?>
              <div class="gallery-counter">
                <span class="material-symbols-outlined" style="font-size:16px;">photo_library</span>
                <span>1/<?= count($photos) ?> Photos</span>
              </div>
            <?php endif; ?>
          </div>
          <div class="gallery-side gallery-side-1" style="cursor:pointer;" onclick="openLightbox(1)">
            <img src="<?= count($photos) > 1 ? getUploadUrl($photos[1]['file_path']) : ASSETS_URL . '/images/placeholder-car.svg' ?>" alt="<?= sanitize($pageTitle) ?> angle 2">
          </div>
          <div class="gallery-side gallery-side-2" style="cursor:pointer;" onclick="openLightbox(2)">
            <img src="<?= count($photos) > 2 ? getUploadUrl($photos[2]['file_path']) : ASSETS_URL . '/images/placeholder-car.svg' ?>" alt="<?= sanitize($pageTitle) ?> angle 3">
          </div>
        </div>

        <!-- Overview -->
        <div class="detail-overview">
          <h2>Vehicle Overview</h2>
          <p></p>
        </div>

        <!-- Spec Table -->
        <dl class="spec-table">
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
            <dt>Body Type</dt>
            <dd><?= sanitize($vehicle['body_type']) ?></dd>
          </div>
          <div class="spec-table-item">
            <dt>Status</dt>
            <dd><?= $statusBadgeHtml ?></dd>
          </div>
        </dl>

        <!-- Full Thumbnail Gallery -->
        <?php if (!empty($photos)): ?>
        <div class="detail-gallery-thumbnails" style="margin-top:40px;">
          <h3 style="font-size:18px; margin-bottom:16px;">All Photos</h3>
          <div style="display:flex; gap:12px; overflow-x:auto; padding-bottom:12px; scrollbar-width:thin;">
            <?php foreach ($photos as $index => $photo): ?>
              <img src="<?= getUploadUrl($photo['file_path']) ?>" 
                   alt="<?= sanitize($pageTitle) ?> photo <?= $index + 1 ?>"
                   style="width:120px; height:80px; object-fit:cover; border-radius:var(--radius-sm); cursor:pointer; border:2px solid transparent; transition:border-color 0.2s; background:var(--surface-container);"
                   onclick="openLightbox(<?= $index ?>)"
                   onmouseover="this.style.borderColor='var(--primary)'"
                   onmouseout="this.style.borderColor='transparent'">
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>


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
            <!-- Map iframe for Office -->
            <iframe src="https://maps.google.com/maps?q=56+Inner+Circular+Road+Eastern+Trade+Center+Dhaka&t=&z=15&ie=UTF8&iwloc=&output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>
          <div class="location-card-address">
            <span class="material-symbols-outlined">location_on</span>
            <span>
              <strong>Our Office</strong><br>
              <?= nl2br(sanitize("56 Inner Circular Road\nEastern Trade Center (6 Floor)\nPurana Paltan Line Dhaka-1000")) ?>
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

// Lightbox Logic
const galleryPhotos = <?= json_encode(array_map(function($p) { return getUploadUrl($p['file_path']); }, $photos)) ?>;
let currentLightboxIndex = 0;

function openLightbox(index) {
  if (galleryPhotos.length === 0 || index >= galleryPhotos.length) return;
  currentLightboxIndex = index;
  document.getElementById('lightbox-modal').style.display = 'flex';
  updateLightbox();
}

function closeLightbox() {
  document.getElementById('lightbox-modal').style.display = 'none';
}

function changeLightbox(dir) {
  currentLightboxIndex += dir;
  if (currentLightboxIndex < 0) currentLightboxIndex = galleryPhotos.length - 1;
  if (currentLightboxIndex >= galleryPhotos.length) currentLightboxIndex = 0;
  updateLightbox();
}

function updateLightbox() {
  document.getElementById('lightbox-img').src = galleryPhotos[currentLightboxIndex];
  document.getElementById('lightbox-counter').innerText = (currentLightboxIndex + 1) + " / " + galleryPhotos.length;
}

// Keyboard navigation for lightbox
document.addEventListener('keydown', function(e) {
  if (document.getElementById('lightbox-modal').style.display === 'flex') {
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft') changeLightbox(-1);
    if (e.key === 'ArrowRight') changeLightbox(1);
  }
});
</script>

<!-- Lightbox Modal -->
<style>
  .lightbox-modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.95);
    z-index: 99999;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    user-select: none;
  }
  .lightbox-close {
    position: absolute;
    top: 12px;
    right: 16px;
    color: white;
    cursor: pointer;
    padding: 8px;
    z-index: 2;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
  }
  .lightbox-close .material-symbols-outlined {
    font-size: 28px;
  }
  .lightbox-nav {
    position: absolute;
    color: white;
    cursor: pointer;
    padding: 12px;
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    border: none;
    width: 48px;
    height: 48px;
    transition: background 0.2s;
  }
  .lightbox-nav:hover {
    background: rgba(255, 255, 255, 0.25);
  }
  .lightbox-nav.prev { left: 12px; }
  .lightbox-nav.next { right: 12px; }
  .lightbox-nav .material-symbols-outlined {
    font-size: 32px;
  }
  .lightbox-img {
    max-width: 90%;
    max-height: 80vh;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
  }
  .lightbox-counter {
    color: var(--secondary);
    margin-top: 16px;
    font-size: 15px;
    font-weight: 500;
  }
  @media (max-width: 768px) {
    .lightbox-nav {
      width: 40px;
      height: 40px;
      padding: 8px;
    }
    .lightbox-nav .material-symbols-outlined {
      font-size: 24px;
    }
    .lightbox-nav.prev { left: 6px; }
    .lightbox-nav.next { right: 6px; }
    .lightbox-close {
      top: 8px;
      right: 8px;
      width: 40px;
      height: 40px;
    }
    .lightbox-close .material-symbols-outlined {
      font-size: 24px;
    }
    .lightbox-img {
      max-width: 96%;
      max-height: 75vh;
      border-radius: 4px;
    }
  }
  @media (max-width: 480px) {
    .lightbox-nav {
      width: 36px;
      height: 36px;
    }
    .lightbox-nav .material-symbols-outlined {
      font-size: 20px;
    }
    .lightbox-img {
      max-width: 100%;
      max-height: 70vh;
      border-radius: 0;
    }
  }
</style>

<div id="lightbox-modal" class="lightbox-modal">
  <button class="lightbox-close" onclick="closeLightbox()" aria-label="Close lightbox">
    <span class="material-symbols-outlined">close</span>
  </button>
  
  <button class="lightbox-nav prev" onclick="changeLightbox(-1)" aria-label="Previous photo">
    <span class="material-symbols-outlined">chevron_left</span>
  </button>
  
  <img id="lightbox-img" class="lightbox-img" src="" alt="Vehicle photo">
  
  <button class="lightbox-nav next" onclick="changeLightbox(1)" aria-label="Next photo">
    <span class="material-symbols-outlined">chevron_right</span>
  </button>

  <div id="lightbox-counter" class="lightbox-counter"></div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
