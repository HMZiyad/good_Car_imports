<?php
/**
 * Good Car Imports — Pre-Order Page
 */

$pageTitle = 'Pre-Order Your Dream Car';
$pageDescription = 'Source your exact vehicle directly from Japanese auction houses. We handle bidding, shipping, customs, and delivery to Dhaka.';

require_once __DIR__ . '/includes/header.php';
?>

<!-- ===== HERO SECTION ===== -->
<section class="preorder-hero">
  <div class="container">
    <div class="hero-label">DIRECT IMPORT SERVICE</div>
    <h1>Your Dream Car,<br><span class="highlight">Exactly As You Want It.</span></h1>
    <p>Don't settle for what's available on the showroom floor. Tell us exactly what you want, and we'll source it directly from Japan's largest auction houses.</p>
  </div>
</section>

<!-- ===== BENEFITS ===== -->
<section class="benefits-section">
  <div class="container">
    <h2>Why Pre-Order With Us?</h2>
    <p>The smartest way to buy your next premium vehicle.</p>
    
    <div class="benefits-grid">
      <div class="benefit-card">
        <div class="benefit-icon">
          <span class="material-symbols-outlined">payments</span>
        </div>
        <h3>Maximum Cost Savings</h3>
        <p>By bypassing local middlemen and importing directly, you save anywhere from 5% to 15% compared to local ready stock.</p>
        <span class="benefit-tag">Save Money</span>
      </div>
      <div class="benefit-card">
        <div class="benefit-icon">
          <span class="material-symbols-outlined">tune</span>
        </div>
        <h3>Exact Specifications</h3>
        <p>Choose your preferred color, interior package, specific trim level, and precise mileage. Don't compromise.</p>
        <span class="benefit-tag">Zero Compromise</span>
      </div>
      <div class="benefit-card">
        <div class="benefit-icon">
          <span class="material-symbols-outlined">verified</span>
        </div>
        <h3>Verified Auction Sheets</h3>
        <p>Review the authentic Japanese auction sheet before bidding. Know the true condition and grade of the car.</p>
        <span class="benefit-tag">100% Transparent</span>
      </div>
    </div>
  </div>
</section>

<!-- ===== MULTI-STEP FORM ===== -->
<section class="preorder-form-section">
  <div class="container">
    <div class="preorder-form-layout">
      <!-- Stepper Indicator -->
      <div class="form-stepper">
        <div class="step-indicator active" id="indicator-1">
          <div class="step-number">1</div>
          <div class="step-text">
            <h4>Vehicle Details</h4>
            <p>Make, model & specs</p>
          </div>
        </div>
        <div class="step-indicator" id="indicator-2">
          <div class="step-number">2</div>
          <div class="step-text">
            <h4>Budget & Timeline</h4>
            <p>Financial parameters</p>
          </div>
        </div>
        <div class="step-indicator" id="indicator-3">
          <div class="step-number">3</div>
          <div class="step-text">
            <h4>Contact Info</h4>
            <p>How we reach you</p>
          </div>
        </div>
      </div>

      <!-- Form Container -->
      <div class="form-container">
        <form id="preorder-form" onsubmit="submitPreOrder(event)">
          <!-- STEP 1: Vehicle Details -->
          <div class="form-step" id="step-1">
            <div class="form-step-info">
              <h2>What are you looking for?</h2>
              <p>Tell us the specifics of the vehicle you want to import.</p>
            </div>
            
            <div class="form-fields">
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Region of Origin</label>
                  <select name="region" class="form-select" required>
                    <option value="Japan">Japan (JDM)</option>
                    <option value="UK">United Kingdom</option>
                    <option value="Europe">Europe</option>
                  </select>
                </div>
                <div class="form-group">
                  <label class="form-label">Body Style</label>
                  <select name="body_style" class="form-select" required>
                    <option value="">Select Body Style</option>
                    <option value="SUV">SUV</option>
                    <option value="Sedan">Sedan</option>
                    <option value="Crossover">Crossover</option>
                    <option value="MPV">MPV / Minivan</option>
                    <option value="Hatchback">Hatchback</option>
                  </select>
                </div>
              </div>
              
              <div class="form-group">
                <label class="form-label">Make & Model</label>
                <input type="text" name="make_model" class="form-input" placeholder="e.g. Toyota Land Cruiser 300 ZX" required>
              </div>
              
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Year Range (From)</label>
                  <select name="year_from" class="form-select">
                    <option value="">Any</option>
                    <?php for($y = date('Y'); $y >= 2018; $y--): ?>
                      <option value="<?= $y ?>"><?= $y ?></option>
                    <?php endfor; ?>
                  </select>
                </div>
                <div class="form-group">
                  <label class="form-label">Color Preference</label>
                  <input type="text" name="color" class="form-input" placeholder="e.g. Pearl White, Black">
                </div>
              </div>

              <button type="button" class="btn-primary form-nav-btn" onclick="nextStep(2)">
                Continue to Budget <span class="material-symbols-outlined" style="font-size:18px;">arrow_forward</span>
              </button>
            </div>
          </div>

          <!-- STEP 2: Budget & Timeline -->
          <div class="form-step" id="step-2" style="display:none;">
            <div class="form-step-info">
              <h2>Budget & Timeline</h2>
              <p>Help us narrow down the right options within your financial parameters.</p>
            </div>
            
            <div class="form-fields">
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Budget Range (Lakh BDT)</label>
                  <select name="budget" class="form-select" required>
                    <option value="">Select Budget</option>
                    <option value="30-40">30 - 40 Lakhs</option>
                    <option value="40-60">40 - 60 Lakhs</option>
                    <option value="60-80">60 - 80 Lakhs</option>
                    <option value="80-100">80 - 100 Lakhs</option>
                    <option value="100-150">1 - 1.5 Crores</option>
                    <option value="150+">1.5 Crores +</option>
                  </select>
                </div>
                <div class="form-group">
                  <label class="form-label">Expected Delivery Timeline</label>
                  <select name="timeline" class="form-select" required>
                    <option value="As soon as possible">As soon as possible</option>
                    <option value="Within 1 month">Within 1 month</option>
                    <option value="1-3 months">1-3 months</option>
                    <option value="Just browsing/Researching">Just researching</option>
                  </select>
                </div>
              </div>

              <div class="form-group">
                <label class="form-label">Additional Requirements</label>
                <textarea name="notes" class="form-textarea" placeholder="Specific trim packages, features (e.g. Sunroof, Leather seats), or any other requirements..."></textarea>
              </div>

              <div style="display:flex; gap:16px;">
                <button type="button" class="btn-outline form-nav-btn" onclick="prevStep(1)">
                  Back
                </button>
                <button type="button" class="btn-primary form-nav-btn" onclick="nextStep(3)">
                  Continue to Contact
                </button>
              </div>
            </div>
          </div>

          <!-- STEP 3: Contact Info -->
          <div class="form-step" id="step-3" style="display:none;">
            <div class="form-step-info">
              <h2>Your Details</h2>
              <p>Where should we send the auction options and quotes?</p>
            </div>
            
            <div class="form-fields">
              <div id="preorder-feedback" class="form-message" style="display:none;"></div>

              <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-input" required>
              </div>
              
              <div class="form-row">
                <div class="form-group">
                  <label class="form-label">Phone Number</label>
                  <input type="tel" name="phone" class="form-input" required placeholder="+880...">
                </div>
                <div class="form-group">
                  <label class="form-label">Email Address</label>
                  <input type="email" name="email" class="form-input">
                </div>
              </div>

              <div class="form-group" style="margin-bottom: 24px;">
                <label class="filter-checkbox">
                  <input type="checkbox" name="has_whatsapp" value="1" checked>
                  Same number for WhatsApp
                </label>
              </div>

              <div style="display:flex; gap:16px;">
                <button type="button" class="btn-outline form-nav-btn" onclick="prevStep(2)">
                  Back
                </button>
                <button type="submit" class="btn-primary form-nav-btn" id="submit-btn">
                  Submit Pre-Order Request
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- ===== VERIFICATION SECTION ===== -->
<section class="verification-section">
  <div class="container">
    <div class="verification-layout">
      <div class="verification-image">
        <img src="<?= ASSETS_URL ?>/images/placeholder-car.svg" style="background:var(--surface-container); padding:40px;" alt="Auction Sheet Example">
      </div>
      <div class="verification-content">
        <div class="label">100% TRANSPARENCY</div>
        <h2>Never Guess. Always Verify.</h2>
        <p>We provide the original Japanese auction sheet for every vehicle we source. You'll know exactly what you're buying before placing a bid.</p>
        
        <ul class="verification-list">
          <li><span class="material-symbols-outlined">check_circle</span> Verified Odometer / Mileage</li>
          <li><span class="material-symbols-outlined">check_circle</span> Detailed Exterior & Interior Grading</li>
          <li><span class="material-symbols-outlined">check_circle</span> Inspector's Handwritten Notes Translated</li>
          <li><span class="material-symbols-outlined">check_circle</span> Accident & Repair History Check</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<script>
// Multi-step form logic
function nextStep(step) {
  // Hide all
  document.querySelectorAll('.form-step').forEach(el => el.style.display = 'none');
  // Show target
  document.getElementById('step-' + step).style.display = 'block';
  
  // Update indicators
  document.querySelectorAll('.step-indicator').forEach(el => el.classList.remove('active'));
  for(let i=1; i<=step; i++) {
    document.getElementById('indicator-' + i).classList.add('active');
  }
}

function prevStep(step) {
  nextStep(step);
  // Re-adjust indicators to just current step
  document.querySelectorAll('.step-indicator').forEach(el => el.classList.remove('active'));
  document.getElementById('indicator-' + step).classList.add('active');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
