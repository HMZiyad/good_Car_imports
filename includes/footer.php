<?php
/**
 * Good Car Imports — Frontend Footer Partial
 */

$whatsappNumber = getSetting('whatsapp_number', '+8801992424492');
$companyPhone1 = getSetting('company_phone_1', '+880 19 9242 4492');
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
            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $whatsappNumber) ?>" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp" class="social-icon">
              <span class="material-symbols-outlined">call</span>
            </a>
            <a href="mailto:goodcarimports.bd@gmail.com" aria-label="Email" class="social-icon">
              <span class="material-symbols-outlined">mail</span>
            </a>
            <a href="#" aria-label="Share" class="social-icon" onclick="event.preventDefault(); navigator.clipboard.writeText(window.location.href).then(() => alert('Website link copied to clipboard!'));">
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
            <li><a href="https://docs.google.com/document/d/1Fqx_vl7sMP8jyfp4x6pYzmHDwhBSvaDvqjdTAbK03nI/edit?usp=sharing" target="_blank" rel="noopener">Privacy Policy</a></li>
            <li><a href="https://docs.google.com/document/d/1dvv8cqIU6Ee7CXW6PNRLviD_EvROJNdwj7Q3l212oHU/edit?usp=sharing" target="_blank" rel="noopener">Terms of Service</a></li>
            <li><a href="https://www.google.com/maps/search/Eastern+Trade+Center+56+VIP+Rd+Dhaka+1205" target="_blank" rel="noopener">Locate Us</a></li>
          </ul>
        </div>

        <!-- Visit Us -->
        <div class="footer-col">
          <h4 class="footer-heading">Visit Us</h4>
          <p class="footer-address">Eastern Trade Center<br>56 VIP Rd, Dhaka 1205<br>Bangladesh</p>
          <div class="footer-social-labels" style="display:flex; gap:16px;">
            <a href="https://www.facebook.com/share/1GgiR3HrNP/?mibextid=wwXIfr" target="_blank" rel="noopener" aria-label="Facebook">
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-3 7h-1.924c-.615 0-1.076.252-1.076.889v1.111h3l-.238 3h-2.762v8h-3v-8h-2v-3h2v-1.923c0-2.022 1.064-3.077 3.461-3.077h2.539v3z"/></svg>
            </a>
            <a href="https://www.instagram.com/goodcarimports/" target="_blank" rel="noopener" aria-label="Instagram">
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M15.233 5.488c-.843-.038-1.097-.046-3.233-.046s-2.389.008-3.232.046c-2.17.099-3.181 1.127-3.279 3.279-.039.844-.048 1.097-.048 3.233s.009 2.389.047 3.233c.099 2.148 1.106 3.18 3.279 3.279.843.038 1.097.047 3.233.047s2.39-.009 3.233-.047c2.173-.099 3.18-1.129 3.279-3.279.038-.844.046-1.097.046-3.233s-.008-2.389-.046-3.232c-.099-2.153-1.111-3.182-3.279-3.281zm-3.233 10.62c-2.269 0-4.108-1.839-4.108-4.108 0-2.269 1.84-4.108 4.108-4.108s4.108 1.839 4.108 4.108c0 2.269-1.839 4.108-4.108 4.108zm4.271-7.418c-.53 0-.96-.43-.96-.96s.43-.96.96-.96.96.43.96.96-.43.96-.96.96zm-1.604 3.31c0 1.473-1.194 2.667-2.667 2.667s-2.667-1.194-2.667-2.667c0-1.473 1.194-2.667 2.667-2.667s2.667 1.194 2.667 2.667zm4.333-12h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-1.802 16.516c-.365 1.034-1.168 1.837-2.202 2.202-1.157.408-3.882.314-5.198.314s-4.041.094-5.198-.314c-1.034-.365-1.837-1.168-2.202-2.202-.408-1.157-.314-3.882-.314-5.198s-.094-4.041.314-5.198c.365-1.034 1.168-1.837 2.202-2.202 1.157-.408 3.882-.314 5.198-.314s4.041-.094 5.198.314c1.034.365 1.837 1.168 2.202 2.202.408 1.157.314 3.882.314 5.198s.094 4.041-.314 5.198z"/></svg>
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Copyright Bar -->
    <div class="footer-copyright">
      <div class="container footer-copyright-inner">
        <p>&copy; <?= date('Y') ?> Good Car Imports. All rights reserved. Dhaka, Bangladesh.</p>
        <div class="footer-bottom-links">
          <a href="https://docs.google.com/document/d/1dvv8cqIU6Ee7CXW6PNRLviD_EvROJNdwj7Q3l212oHU/edit?usp=sharing" target="_blank" rel="noopener">Terms of Service</a>
          <a href="https://docs.google.com/document/d/1Fqx_vl7sMP8jyfp4x6pYzmHDwhBSvaDvqjdTAbK03nI/edit?usp=sharing" target="_blank" rel="noopener">Privacy Policy</a>
          <a href="<?= SITE_URL ?>/contact.php">Contact Us</a>
          <a href="https://www.google.com/maps/search/Eastern+Trade+Center+56+VIP+Rd+Dhaka+1205" target="_blank" rel="noopener">Locate Us</a>
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
