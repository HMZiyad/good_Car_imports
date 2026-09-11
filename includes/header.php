<?php
/**
 * Good Car Imports — Frontend Header Partial
 * Glassmorphism navigation bar matching the design
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$currentPage = getCurrentPage();
$whatsappNumber = getSetting('whatsapp_number', '+8801992424492');
$companyPhone = getSetting('company_phone_1', '+880 19 9242 4492');
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth" data-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' | Good Car Imports' : 'Good Car Imports | Premium Vehicle Importers in Bangladesh' ?></title>
  <meta name="description" content="<?= isset($pageDescription) ? sanitize($pageDescription) : 'Premium Japanese and European vehicle imports in Bangladesh. Verified auction sheets, hassle-free registration, and guaranteed best prices.' ?>">

  <!-- CSS -->
  <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/design-tokens.css?v=2">
  <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/frontend.css?v=7">

  <!-- Favicon -->
  <link rel="icon" href="<?= ASSETS_URL ?>/images/logo-transparent.png" type="image/png">

  <!-- Loading Screen Styles -->
  <style>
    #site-loader {
      position: fixed;
      inset: 0;
      background-color: #ffffff;
      z-index: 999999;
      display: flex;
      justify-content: center;
      align-items: center;
      transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
    }
    #site-loader.hidden {
      opacity: 0;
      visibility: hidden;
    }
    #site-loader img {
      width: 50%;
      max-width: 200px;
      height: auto;
    }
  </style>
  <script>
    // Only block scrolling if we're going to show the loader
    if (!sessionStorage.getItem('siteLoaderShown')) {
      document.write('<style>body { overflow: hidden !important; }</style>');
    }
  </script>

  <?php if (isset($extraHead)) echo $extraHead; ?>
</head>
<body>
  <!-- ===== Site Loader ===== -->
  <div id="site-loader" class="hidden">
    <img src="<?= ASSETS_URL ?>/images/car-loader.gif" alt="Loading...">
  </div>
  <script>
    if (!sessionStorage.getItem('siteLoaderShown')) {
      const loader = document.getElementById('site-loader');
      loader.classList.remove('hidden');
      sessionStorage.setItem('siteLoaderShown', 'true');
      
      const startTime = Date.now();
      
      window.addEventListener('load', () => {
        const elapsedTime = Date.now() - startTime;
        const remainingTime = Math.max(0, 2000 - elapsedTime);
        
        setTimeout(() => {
          loader.classList.add('hidden');
          document.body.style.overflow = ''; 
        }, remainingTime);
      });
    }
  </script>

  <!-- ===== Skip Link ===== -->
  <a href="#main-content" class="sr-only">Skip to main content</a>

  <!-- ===== Top Navigation Bar ===== -->
  <header class="site-header" id="site-header">
    <nav class="nav-container container" aria-label="Main navigation">
      <!-- Logo -->
      <a href="<?= SITE_URL ?>/" class="nav-logo" aria-label="Good Car Imports - Home">
        <img src="<?= ASSETS_URL ?>/images/logo-transparent.png" alt="Good Car Imports" width="120" height="40">
      </a>

      <!-- Desktop Nav Links -->
      <ul class="nav-links" id="nav-links">
        <li><a href="<?= SITE_URL ?>/" class="nav-link <?= $currentPage === 'home' ? 'active' : '' ?>">Home</a></li>
        <li><a href="<?= SITE_URL ?>/inventory.php" class="nav-link <?= $currentPage === 'inventory' ? 'active' : '' ?>">Stock</a></li>
        <li><a href="<?= SITE_URL ?>/pre-order.php" class="nav-link <?= $currentPage === 'pre-order' ? 'active' : '' ?>">Pre-Order</a></li>
        <li><a href="<?= SITE_URL ?>/about.php" class="nav-link <?= $currentPage === 'about' ? 'active' : '' ?>">About</a></li>
        <li><a href="<?= SITE_URL ?>/contact.php" class="nav-link <?= $currentPage === 'contact' ? 'active' : '' ?>">Contact</a></li>
      </ul>

      <!-- Actions -->
      <div class="nav-actions">
        <button class="theme-toggle-btn desktop-theme-toggle" aria-label="Toggle dark mode">
          <span class="material-symbols-outlined light-icon">light_mode</span>
          <span class="material-symbols-outlined dark-icon">dark_mode</span>
        </button>
        <a href="tel:<?= str_replace(' ', '', $companyPhone) ?>" class="nav-cta">
          Call Now
        </a>
      </div>

      <!-- Mobile Hamburger Button -->
      <button class="nav-hamburger" id="nav-hamburger" aria-label="Toggle navigation menu" aria-expanded="false">
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
        <span class="hamburger-line"></span>
      </button>
    </nav>
  </header>

  <!-- Mobile Menu Overlay (outside header to avoid backdrop-filter containing block) -->
  <div class="mobile-menu" id="mobile-menu" aria-hidden="true">
    <div style="display: flex; justify-content: center; margin-bottom: 24px;">
      <button class="theme-toggle-btn mobile-theme-toggle" aria-label="Toggle dark mode">
        <span class="material-symbols-outlined light-icon">light_mode</span>
        <span class="material-symbols-outlined dark-icon">dark_mode</span>
      </button>
    </div>
    <ul class="mobile-nav-links">
      <li><a href="<?= SITE_URL ?>/" class="mobile-nav-link <?= $currentPage === 'home' ? 'active' : '' ?>">Home</a></li>
      <li><a href="<?= SITE_URL ?>/inventory.php" class="mobile-nav-link <?= $currentPage === 'inventory' ? 'active' : '' ?>">Stock</a></li>
      <li><a href="<?= SITE_URL ?>/pre-order.php" class="mobile-nav-link <?= $currentPage === 'pre-order' ? 'active' : '' ?>">Pre-Order</a></li>
      <li><a href="<?= SITE_URL ?>/about.php" class="mobile-nav-link <?= $currentPage === 'about' ? 'active' : '' ?>">About</a></li>
      <li><a href="<?= SITE_URL ?>/contact.php" class="mobile-nav-link <?= $currentPage === 'contact' ? 'active' : '' ?>">Contact</a></li>
    </ul>
    <a href="tel:<?= str_replace(' ', '', $companyPhone) ?>" class="mobile-cta-btn">
      <span class="material-symbols-outlined">call</span>
      Call Now
    </a>
  </div>

  <!-- ===== Main Content ===== -->
  <main id="main-content">
