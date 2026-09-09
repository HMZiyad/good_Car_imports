<?php
/**
 * Good Car Imports — Contact Us Page
 */

$pageTitle = 'Contact Us';
$pageDescription = 'Get in touch with Good Car Imports. Visit our Baridhara showroom or contact our team for inquiries about imported vehicles.';

require_once __DIR__ . '/includes/header.php';

$companyPhone1 = getSetting('company_phone_1', '+880 1711 000 000');
$companyPhone2 = getSetting('company_phone_2', '+880 1822 111 222');
$companyEmail = getSetting('company_email', 'info@goodcarimports.com');
$address1 = getSetting('company_address_1', 'Plot 12, Road 4, Block J, Baridhara, Dhaka 1212');
$address2 = getSetting('company_address_2', 'Plot 89, Tejgaon Industrial Area, Dhaka 1208');
$businessHours = getSetting('business_hours', 'Sat - Thu: 10:00 AM - 8:00 PM');
$businessHoursNote = getSetting('business_hours_note', 'Friday: Closed (By Appointment Only)');
$whatsappNumber = getSetting('whatsapp_number', '+8801711000000');
?>

<section class="contact-page">
  <div class="container">
    <div class="contact-header">
      <h1>Get in Touch</h1>
      <p>Whether you have a question about our inventory, need assistance with a pre-order, or want to schedule an inspection, our team is ready to help.</p>
    </div>

    <div class="contact-grid">
      <!-- Contact Form -->
      <div class="contact-form-card">
        <h2>Send an Inquiry</h2>
        
        <div id="contact-feedback" class="form-message" style="display:none;"></div>

        <form id="contact-form" onsubmit="submitInquiry(event)">
          <input type="hidden" name="type" value="contact">
          
          <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-input" required placeholder="John Doe">
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Phone Number</label>
              <input type="tel" name="phone" class="form-input" required placeholder="+880...">
            </div>
            <div class="form-group">
              <label class="form-label">Email Address</label>
              <input type="email" name="email" class="form-input" placeholder="john@example.com">
            </div>
          </div>
          
          <div class="form-group">
            <label class="form-label">Interested In</label>
            <select name="interested_in" class="form-select">
              <option value="General Inquiry">General Inquiry</option>
              <option value="Ready Stock Vehicle">Ready Stock Vehicle</option>
              <option value="Pre-Order / Import">Pre-Order / Import</option>
              <option value="Trade-in Evaluation">Trade-in Evaluation</option>
            </select>
          </div>
          
          <div class="form-group" style="margin-bottom: 24px;">
            <label class="form-label">Message</label>
            <textarea name="message" class="form-textarea" required placeholder="How can we help you today?"></textarea>
          </div>
          
          <button type="submit" class="form-submit-btn">Send Inquiry</button>
        </form>
      </div>

      <!-- Contact Info -->
      <div class="contact-info-card">
        <!-- Map -->
        <div class="contact-map" style="position: relative;">
          <!-- Simple iframe map placeholder for Baridhara -->
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14602.700311894985!2d90.40798995000001!3d23.79458205!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c7a0f70deb73%3A0x30c36498f90fe23!2sBaridhara%2C%20Dhaka!5e0!3m2!1sen!2sbd!4v1715000000000!5m2!1sen!2sbd" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          <a href="https://maps.google.com/?q=Baridhara,Dhaka" target="_blank" class="contact-map-link">
            Open in Google Maps <span class="material-symbols-outlined" style="font-size: 16px;">open_in_new</span>
          </a>
        </div>

        <!-- Locations -->
        <div class="contact-locations">
          <div class="contact-location-item">
            <h3>Baridhara Showroom</h3>
            <p><?= sanitize($address1) ?></p>
          </div>
          <div class="contact-location-item">
            <h3>Tejgaon Service Center</h3>
            <p><?= sanitize($address2) ?></p>
          </div>
        </div>

        <!-- Quick Contacts -->
        <div style="display:flex; flex-direction:column; gap:16px;">
          <div class="contact-detail-row">
            <div class="contact-detail-icon">
              <span class="material-symbols-outlined">call</span>
            </div>
            <div>
              <div class="contact-detail-label">Call Us</div>
              <div class="contact-detail-value"><a href="tel:<?= str_replace(' ', '', $companyPhone1) ?>"><?= sanitize($companyPhone1) ?></a></div>
              <?php if ($companyPhone2): ?>
                <div class="contact-detail-sub"><a href="tel:<?= str_replace(' ', '', $companyPhone2) ?>"><?= sanitize($companyPhone2) ?></a></div>
              <?php endif; ?>
            </div>
          </div>

          <div class="contact-detail-row">
            <div class="contact-detail-icon">
              <span class="material-symbols-outlined">schedule</span>
            </div>
            <div>
              <div class="contact-detail-label">Business Hours</div>
              <div class="contact-detail-value"><?= sanitize($businessHours) ?></div>
              <div class="contact-detail-sub"><?= sanitize($businessHoursNote) ?></div>
            </div>
          </div>
          
          <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $whatsappNumber) ?>" target="_blank" class="contact-whatsapp-btn">
            <span class="material-symbols-outlined filled">chat</span>
            Chat on WhatsApp
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
