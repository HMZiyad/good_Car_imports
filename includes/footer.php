<?php
/**
 * Good Car Imports — Frontend Footer Partial
 */

$whatsappNumber = getSetting('whatsapp_number', '+8801711000000');
$companyPhone1 = getSetting('company_phone_1', '+880 1711 000 000');
?>
  </main>

  <!-- ===== Footer ===== -->
  <footer class="site-footer">
    <div class="footer-main container">
      <div class="footer-grid">
        <!-- Company Info -->
        <div class="footer-col footer-brand">
          <img src="<?= ASSETS_URL ?>/images/logo-transparent.png" alt="Good Car Imports" class="footer-logo" width="100" height="36">
          <p class="footer-tagline">Your trusted partner for authentic Japanese and European vehicle imports in Bangladesh.</p>
          <div class="footer-social">
            <a href="#" aria-label="Phone" class="social-icon">
              <span class="material-symbols-outlined">call</span>
            </a>
            <a href="mailto:info@goodcarimports.com" aria-label="Email" class="social-icon">
              <span class="material-symbols-outlined">mail</span>
            </a>
            <a href="#" aria-label="Share" class="social-icon">
              <span class="material-symbols-outlined">share</span>
            </a>
          </div>
        </div>

        <!-- Company Links -->
        <div class="footer-col">
          <h4 class="footer-heading">Company</h4>
          <ul class="footer-links">
            <li><a href="<?= SITE_URL ?>/about.php">About Us</a></li>
            <li><a href="<?= SITE_URL ?>/pre-order.php">Pre-Order Guide</a></li>
            <li><a href="<?= SITE_URL ?>/inventory.php">Inventory</a></li>
          </ul>
        </div>

        <!-- Support Links -->
        <div class="footer-col">
          <h4 class="footer-heading">Support</h4>
          <ul class="footer-links">
            <li><a href="<?= SITE_URL ?>/contact.php">Contact Us</a></li>
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="#">Terms of Service</a></li>
            <li><a href="#">Locate Showroom</a></li>
          </ul>
        </div>

        <!-- Visit Us -->
        <div class="footer-col">
          <h4 class="footer-heading">Visit Us</h4>
          <p class="footer-address">Plot 12, Road 4, Block J<br>Baridhara, Dhaka 1212<br>Bangladesh</p>
          <div class="footer-social-labels">
            <a href="#">Facebook</a>
            <a href="#">Instagram</a>
            <a href="#">LinkedIn</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Copyright Bar -->
    <div class="footer-copyright">
      <div class="container footer-copyright-inner">
        <p>&copy; <?= date('Y') ?> Good Car Imports. All rights reserved. Dhaka, Bangladesh.</p>
        <div class="footer-bottom-links">
          <a href="#">Terms of Service</a>
          <a href="#">Privacy Policy</a>
          <a href="<?= SITE_URL ?>/contact.php">Contact Us</a>
          <a href="#">Locate Showroom</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- ===== WhatsApp Floating Action Button ===== -->
  <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $whatsappNumber) ?>"
     target="_blank"
     rel="noopener noreferrer"
     class="whatsapp-fab"
     aria-label="Chat on WhatsApp"
     id="whatsapp-fab">
    <span class="material-symbols-outlined filled">chat</span>
    <span class="whatsapp-fab-label">Start Chat</span>
  </a>

  <!-- Scripts -->
  <script src="<?= ASSETS_URL ?>/js/frontend.js"></script>
</body>
</html>
