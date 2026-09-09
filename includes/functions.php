<?php
/**
 * Good Car Imports — Shared Utility Functions
 */

/**
 * Sanitize user input to prevent XSS
 */
function sanitize(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Format a number as BDT currency
 * Stored as paisa (multiply by 100), display in Lakh/Crore notation
 * e.g. 4500000000 (paisa) => ৳45,00,000 => ৳45.0L
 */
function formatBDT(int $paisa, bool $short = false): string {
    $amount = $paisa / 100;

    if ($short) {
        if ($amount >= 10000000) {
            return '৳' . number_format($amount / 10000000, 2) . ' Cr';
        } elseif ($amount >= 100000) {
            return '৳' . number_format($amount / 100000, 1) . 'L';
        }
    }

    // Bangladeshi number formatting (Indian/Bangladeshi comma system)
    return '৳' . formatBDNumber($amount);
}

/**
 * Format number in Bangladeshi comma system
 * e.g. 4500000 => 45,00,000
 */
function formatBDNumber($number): string {
    $number = (int) $number;
    $isNegative = $number < 0;
    $number = abs($number);

    $str = (string) $number;
    $len = strlen($str);

    if ($len <= 3) return ($isNegative ? '-' : '') . $str;

    // Last 3 digits
    $result = substr($str, -3);
    $remaining = substr($str, 0, $len - 3);

    // Group remaining digits in pairs
    while (strlen($remaining) > 2) {
        $result = substr($remaining, -2) . ',' . $result;
        $remaining = substr($remaining, 0, -2);
    }
    if ($remaining) {
        $result = $remaining . ',' . $result;
    }

    return ($isNegative ? '-' : '') . $result;
}

/**
 * Format mileage
 */
function formatMileage(int $km): string {
    if ($km >= 1000) {
        return number_format($km / 1000, 0) . 'k KM';
    }
    return number_format($km) . ' KM';
}

/**
 * Format mileage (long form)
 */
function formatMileageLong(int $km): string {
    return number_format($km) . ' KM';
}

/**
 * Get CSS class for auction grade badge
 */
function getGradeClass(string $grade): string {
    $grade = strtoupper(trim($grade));
    if (in_array($grade, ['5.0', 'S', '6.0'])) return 'grade-excellent';
    if (in_array($grade, ['4.5', 'A'])) return 'grade-good';
    if (in_array($grade, ['4.0', 'B'])) return 'grade-standard';
    if (in_array($grade, ['R', '3.5'])) return 'grade-fair';
    return 'grade-standard';
}

/**
 * Get status badge HTML
 */
function getStatusBadge(string $status): string {
    $badges = [
        'available'      => ['Available — Showroom', 'status-available'],
        'port_clearance'  => ['Port Clearance (CTG)', 'status-port'],
        'vessel_transit'  => ['On Vessel', 'status-vessel'],
        'pre_booked'     => ['Pre-Booked', 'status-prebooked'],
        'sold'           => ['Sold', 'status-sold'],
        'reserved'       => ['Reserved', 'status-reserved'],
    ];

    $badge = $badges[$status] ?? ['Unknown', 'status-default'];
    return sprintf('<span class="status-badge %s">%s</span>', $badge[1], $badge[0]);
}

/**
 * Get inward stage badge
 */
function getInwardStageBadge(string $stage): string {
    $stages = [
        'auction_won'    => ['Auction Won', 'stage-won'],
        'japan_yard'     => ['Nagoya Export Yard', 'stage-yard'],
        'sea_transit'    => ['Sea Transit', 'stage-transit'],
        'ctg_customs'    => ['CTG Customs', 'stage-customs'],
        'dhaka_handover' => ['Dhaka Handover', 'stage-handover'],
    ];

    $badge = $stages[$stage] ?? ['Unknown', ''];
    return sprintf('<span class="stage-badge %s">%s</span>', $badge[1], $badge[0]);
}

/**
 * Generate URL-friendly slug
 */
function generateSlug(string $text): string {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

/**
 * Handle file upload and return the file path
 */
function uploadImage(array $file, string $subdir = ''): ?string {
    // Validate upload
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    if ($file['size'] > MAX_UPLOAD_SIZE) return null;

    $mime = mime_content_type($file['tmp_name']);
    if (!in_array($mime, ALLOWED_IMAGE_TYPES)) return null;

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_IMAGE_EXTENSIONS)) return null;

    // Generate unique filename
    $filename = uniqid('gci_', true) . '.' . $ext;

    // Create subdirectory if needed
    $uploadDir = UPLOAD_PATH;
    if ($subdir) {
        $uploadDir .= '/' . $subdir;
    }
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $targetPath = $uploadDir . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Return relative path from uploads directory
        $relativePath = ($subdir ? $subdir . '/' : '') . $filename;
        return $relativePath;
    }

    return null;
}

/**
 * Get the full URL for an uploaded image
 */
function getUploadUrl(?string $path): string {
    if (empty($path)) return ASSETS_URL . '/images/placeholder-car.svg';
    if (strpos($path, 'http') === 0) return $path;
    return UPLOAD_URL . '/' . $path;
}

/**
 * Format a date for display
 */
function formatDate(string $date, string $format = 'M d, Y'): string {
    return date($format, strtotime($date));
}

/**
 * Get pagination data
 */
function getPagination(int $total, int $perPage, int $currentPage = 1): array {
    $totalPages = max(1, ceil($total / $perPage));
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;

    return [
        'total'        => $total,
        'per_page'     => $perPage,
        'current_page' => $currentPage,
        'total_pages'  => $totalPages,
        'offset'       => $offset,
        'has_prev'     => $currentPage > 1,
        'has_next'     => $currentPage < $totalPages,
    ];
}

/**
 * Render pagination HTML
 */
function renderPagination(array $pagination, string $baseUrl): string {
    if ($pagination['total_pages'] <= 1) return '';

    $html = '<nav class="pagination" aria-label="Pagination">';

    // Previous
    if ($pagination['has_prev']) {
        $html .= sprintf(
            '<a href="%s?page=%d" class="pagination-btn">&lsaquo;</a>',
            $baseUrl, $pagination['current_page'] - 1
        );
    } else {
        $html .= '<span class="pagination-btn disabled">&lsaquo;</span>';
    }

    // Page numbers
    for ($i = 1; $i <= $pagination['total_pages']; $i++) {
        $active = ($i === $pagination['current_page']) ? ' active' : '';
        $html .= sprintf(
            '<a href="%s?page=%d" class="pagination-btn%s">%d</a>',
            $baseUrl, $i, $active, $i
        );
    }

    // Next
    if ($pagination['has_next']) {
        $html .= sprintf(
            '<a href="%s?page=%d" class="pagination-btn">&rsaquo;</a>',
            $baseUrl, $pagination['current_page'] + 1
        );
    } else {
        $html .= '<span class="pagination-btn disabled">&rsaquo;</span>';
    }

    $html .= '</nav>';
    return $html;
}

/**
 * Fetch live forex rate from API with caching
 */
function getForexRate(): float {
    $cacheFile = ROOT_PATH . '/assets/forex_cache.json';

    // Check cache
    if (file_exists($cacheFile)) {
        $cache = json_decode(file_get_contents($cacheFile), true);
        if ($cache && (time() - $cache['timestamp']) < FOREX_CACHE_DURATION) {
            return (float) $cache['rate'];
        }
    }

    // Try API
    try {
        $context = stream_context_create([
            'http' => ['timeout' => 5]
        ]);
        $response = @file_get_contents(FOREX_API_URL, false, $context);

        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['rates']['BDT'])) {
                $rate = (float) $data['rates']['BDT'];

                // Save to cache
                file_put_contents($cacheFile, json_encode([
                    'rate'      => $rate,
                    'timestamp' => time(),
                    'source'    => 'api',
                ]));

                // Also update database setting
                setSetting('forex_jpy_bdt', (string) $rate);

                return $rate;
            }
        }
    } catch (Exception $e) {
        // Silently fail, fall back to DB setting
    }

    // Fallback to manual setting from database
    return (float) getSetting('forex_jpy_bdt', 0.79);
}

/**
 * Send JSON response (for API endpoints)
 */
function jsonResponse(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Get current page name for navigation highlighting
 */
function getCurrentPage(): string {
    $script = basename($_SERVER['SCRIPT_NAME'], '.php');
    return $script === 'index' ? 'home' : $script;
}

/**
 * Check if string is valid JSON
 */
function isJSON(string $string): bool {
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

/**
 * Truncate text to a specified length
 */
function truncate(string $text, int $length = 150, string $suffix = '...'): string {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length - strlen($suffix)) . $suffix;
}
