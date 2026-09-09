<?php
/**
 * Good Car Imports — About Us Page
 */

$pageTitle = 'About Good Car Imports';
$pageDescription = 'Redefining car imports in Bangladesh. With 15+ years of experience, we provide transparent, high-quality Japanese and European vehicle imports.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- ===== HERO SECTION ===== -->
<section class="about-hero">
  <div class="container">
    <div class="about-hero-grid">
      <div>
        <div class="about-hero-label">OUR HERITAGE</div>
        <h1>Redefining Car Imports in Bangladesh</h1>
        <p>Good Car Imports was founded with a singular mission: to bring absolute transparency to the vehicle import process. No hidden fees, no tampered mileage, and no fake auction sheets.</p>
        
        <div class="about-stats">
          <div>
            <div class="about-stat-value">15+</div>
            <div class="about-stat-label">Years Experience</div>
          </div>
          <div>
            <div class="about-stat-value">500+</div>
            <div class="about-stat-label">Cars Delivered</div>
          </div>
          <div>
            <div class="about-stat-value">100%</div>
            <div class="about-stat-label">Verified Sheets</div>
          </div>
        </div>
      </div>
      <div class="about-hero-image">
        <img src="<?= ASSETS_URL ?>/images/uploads/hero-car.jpg" alt="Good Car Imports Showroom">
      </div>
    </div>
  </div>
</section>

<!-- ===== VALUES ===== -->
<section class="values-section">
  <div class="container">
    <h2>Driven by <span class="highlight">Transparency</span></h2>
    
    <div class="values-grid">
      <div class="value-card">
        <div class="value-icon">
          <span class="material-symbols-outlined">visibility</span>
        </div>
        <h3>Total Transparency</h3>
        <p>We provide original auction sheets and authentic JAAI/JUMVEA certificates for every single vehicle. What you see is exactly what you get.</p>
      </div>
      <div class="value-card">
        <div class="value-icon">
          <span class="material-symbols-outlined">workspace_premium</span>
        </div>
        <h3>Quality Obsessed</h3>
        <p>We strictly import vehicles graded 4.0 and above. Every car undergoes a rigorous physical inspection at the Japan yard before export.</p>
      </div>
      <div class="value-card">
        <div class="value-icon">
          <span class="material-symbols-outlined">local_shipping</span>
        </div>
        <h3>Expert Logistics</h3>
        <p>From winning the auction in Tokyo to handing you the keys in Dhaka, we manage the entire complex supply chain so you don't have to.</p>
      </div>
    </div>
  </div>
</section>

<!-- ===== SHOWROOM GALLERY ===== -->
<section class="showroom-section">
  <div class="container">
    <div class="section-header">
      <div>
        <h2>Our Baridhara Showroom</h2>
        <p>Visit us to experience our premium inventory in person.</p>
      </div>
      <a href="<?= SITE_URL ?>/contact.php" class="showroom-link">
        Get Directions <span class="material-symbols-outlined" style="font-size:18px;">arrow_forward</span>
      </a>
    </div>

    <div class="showroom-gallery">
      <div class="showroom-gallery-item large">
        <img src="<?= ASSETS_URL ?>/images/uploads/lc300.jpg" alt="Showroom Main Area">
        <span class="showroom-gallery-label">Main Display Area</span>
      </div>
      <div class="showroom-gallery-item">
        <img src="<?= ASSETS_URL ?>/images/uploads/cross.jpg" alt="Client Lounge">
        <span class="showroom-gallery-label">Client Lounge</span>
      </div>
      <div class="showroom-gallery-item">
        <img src="<?= ASSETS_URL ?>/images/uploads/vezel.jpg" alt="Delivery Bay">
        <span class="showroom-gallery-label">Delivery Bay</span>
      </div>
    </div>
  </div>
</section>

<!-- ===== TEAM ===== -->
<section class="team-section">
  <div class="container">
    <h2>Leadership Team</h2>
    <p>Meet the experts who ensure your import process is seamless and secure.</p>

    <div class="team-grid">
      <div class="team-card">
        <div class="team-card-image"></div>
        <h3>M. Rahman</h3>
        <p>Managing Director</p>
      </div>
      <div class="team-card">
        <div class="team-card-image"></div>
        <h3>A. Siddique</h3>
        <p>Head of Operations</p>
      </div>
      <div class="team-card">
        <div class="team-card-image"></div>
        <h3>K. Tanaka</h3>
        <p>Japan Procurement Lead</p>
      </div>
      <div class="team-card">
        <div class="team-card-image"></div>
        <h3>S. Ahmed</h3>
        <p>Client Relations Manager</p>
      </div>
    </div>
  </div>
</section>

<!-- ===== CTA ===== -->
<section class="cta-section" >
    <h2>Ready to Find Your Perfect Drive?</h2>
    <p>Whether you're looking for ready stock at our showroom or want to pre-order directly from Japan, we're here to help.</p>
    <div class="cta-buttons">
      <a href="<?= SITE_URL ?>/inventory.php" class="btn-cta-primary">Browse Inventory</a>
      <a href="<?= SITE_URL ?>/contact.php" class="btn-cta-secondary">Contact Us</a>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
