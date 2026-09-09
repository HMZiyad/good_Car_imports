<?php
/**
 * Good Car Imports — Application Configuration
 * 
 * Update these values to match your cPanel MySQL credentials.
 * For the installation wizard, see /database/install.php
 */

// ── Database Configuration ──
// Modify these with your cPanel database credentials
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'your_cpanel_db_name');
define('DB_USER', getenv('DB_USER') ?: 'your_cpanel_db_user');
define('DB_PASS', getenv('DB_PASS') ?: 'your_cpanel_db_password');
define('DB_CHARSET', 'utf8mb4');

// ── Application URLs ──
// Update these to your actual domain after deployment
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
define('SITE_URL', $protocol . $host);
define('ADMIN_URL', $protocol . $host . '/admin');
define('ASSETS_URL', SITE_URL . '/assets');

// ── File Paths ──
define('ROOT_PATH', dirname(__DIR__));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('UPLOAD_PATH', ROOT_PATH . '/assets/images/uploads');
define('UPLOAD_URL', ASSETS_URL . '/images/uploads');

// ── Upload Limits ──
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);

// ── Session Configuration ──
define('SESSION_NAME', 'gci_session');
define('SESSION_LIFETIME', 86400); // 24 hours

// ── Forex API ──
// Free API for JPY/BDT conversion. Falls back to manual setting.
define('FOREX_API_URL', 'https://api.exchangerate-api.com/v4/latest/JPY');
define('FOREX_CACHE_DURATION', 3600); // Cache forex rate for 1 hour

// ── Pagination ──
define('ITEMS_PER_PAGE', 10);
define('FRONTEND_ITEMS_PER_PAGE', 6);

// ── Error Reporting ──
// Set to false in production
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ── Timezone ──
date_default_timezone_set('Asia/Dhaka');
