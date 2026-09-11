<?php
/**
 * Good Car Imports — Landing Page
 */

$pageTitle = 'Premium Vehicle Importers in Bangladesh';
$pageDescription = 'Drive your dream. Authentic auction sheets, hassle-free registration, and guaranteed best prices for the finest Japanese and European vehicles imported directly to Bangladesh.';

require_once __DIR__ . '/includes/header.php';

// Fetch featured vehicles
$featuredVehicles = dbFetchAll(
    "SELECT * FROM vehicles WHERE is_featured = 1 AND status != 'sold' ORDER BY created_at DESC LIMIT 3"
);
?>

<!-- ===== HERO SECTION ===== -->
<section class="hero">
  <div class="container">
    <div class="hero-video-wrapper">
      <video class="hero-video-bg" autoplay loop muted playsinline>
        <source src="<?= SITE_URL ?>/assets/images/uploads/home.mp4" type="video/mp4">
      </video>
      <div class="hero-video-overlay"></div>
    </div>
    <div class="hero-content">
      <h1 class="hero-title">
        Drive Your Dream.<br>
        <span class="highlight">Imported Directly</span> to Bangladesh.
      </h1>
      <p class="hero-subtitle">
        Authentic auction sheets, hassle-free registration, and guaranteed best prices for the finest Japanese and European vehicles.
      </p>
      <div class="hero-actions">
        <a href="<?= SITE_URL ?>/inventory.php" class="btn-primary">
          Browse Inventory
        </a>
        <a href="<?= SITE_URL ?>/pre-order.php" class="btn-outline">
          Request a Specific Car
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ===== TRUST BADGES ===== -->
<section class="trust-section">
  <div class="container">
    <div class="trust-grid">
      <div class="trust-card">
        <div class="trust-icon">
          <span class="material-symbols-outlined">verified</span>
        </div>
        <h3>Verified Auction Sheets</h3>
        <p>100% genuine inspection reports directly from Japanese auction houses.</p>
      </div>
      <div class="trust-card">
        <div class="trust-icon">
          <span class="material-symbols-outlined">account_balance</span>
        </div>
        <h3>Loan Assistance</h3>
        <p>Expert guidance for bank loan and insurance processing with local partners.</p>
      </div>
      <div class="trust-card">
        <div class="trust-icon">
          <span class="material-symbols-outlined">description</span>
        </div>
        <h3>BRTA Support</h3>
        <p>We handle all the paperwork for registration and fitness certificates.</p>
      </div>
      <div class="trust-card">
        <div class="trust-icon">
          <span class="material-symbols-outlined">local_shipping</span>
        </div>
        <h3>Port-to-Door Delivery</h3>
        <p>Safe and reliable transport from Chittagong port to your home.</p>
      </div>
    </div>
  </div>
</section>

<!-- ===== FEATURED INVENTORY ===== -->
<section class="featured-section">
  <div class="container">
    <div class="section-header">
      <div>
        <h2>Featured Inventory</h2>
        <p>Our hand-picked selection of high-grade imports.</p>
      </div>
      <a href="<?= SITE_URL ?>/inventory.php" class="section-link">
        View All <span class="material-symbols-outlined">arrow_forward</span>
      </a>
    </div>

    <div class="featured-grid">
      <?php foreach ($featuredVehicles as $v): ?>
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

      <?php if (empty($featuredVehicles)): ?>
        <p style="grid-column: 1 / -1; text-align: center; color: var(--secondary); padding: 48px 0;">
          No featured vehicles at the moment. Check back soon!
        </p>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ===== PRE-ORDER PROCESS ===== -->
<section class="process-section">
  <div class="container">
    <h2>Our Seamless Pre-Order Process</h2>
    <p>Getting your dream car is simpler than you think.</p>

    <div class="process-grid">
      <div class="process-step">
        <div class="process-step-number">1</div>
        <h3>Choose Car</h3>
        <p>Select from live Japanese auction listings.</p>
      </div>
      <div class="process-step">
        <div class="process-step-number">2</div>
        <h3>Auction/Bidding</h3>
        <p>We secure the car at the best possible price.</p>
      </div>
      <div class="process-step">
        <div class="process-step-number">3</div>
        <h3>Shipping</h3>
        <p>Global shipping with full tracking to BD.</p>
      </div>
      <div class="process-step">
        <div class="process-step-number">4</div>
        <h3>Delivery</h3>
        <p>Cleared at port and delivered to Dhaka.</p>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
