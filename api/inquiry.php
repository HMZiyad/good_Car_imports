<?php
/**
 * Good Car Imports — Public API: Handle Inquiry/Pre-Order Submissions
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
}

// Ensure session is started for basic CSRF/Rate limiting if needed later
session_start();

$type = sanitize($_POST['type'] ?? '');

if ($type === 'contact' || $type === 'booking') {
    // ── Handle Contact / Booking Form ──
    $fullName = sanitize($_POST['full_name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $interestedIn = sanitize($_POST['interested_in'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    $vehicleId = (int) ($_POST['vehicle_id'] ?? 0);

    if (empty($fullName) || empty($phone)) {
        jsonResponse(['success' => false, 'message' => 'Name and Phone are required.']);
    }

    try {
        dbInsert('inquiries', [
            'type'          => $type,
            'vehicle_id'    => $vehicleId ?: null,
            'full_name'     => $fullName,
            'phone'         => $phone,
            'email'         => $email,
            'interested_in' => $interestedIn,
            'message'       => $message,
            'status'        => 'new'
        ]);

        // Send email notification to Admin
        $adminEmail = getSetting('company_email', 'goodcarimports.bd@gmail.com');
        $subject = "New Inquiry: $interestedIn";
        $body = "
            <h2>New Contact Inquiry</h2>
            <p><strong>Name:</strong> $fullName</p>
            <p><strong>Phone:</strong> $phone</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Interested In:</strong> $interestedIn</p>
            <p><strong>Message:</strong><br/>".nl2br($message)."</p>
        ";
        sendEmail($adminEmail, $subject, $body, $email ?: null);

        jsonResponse([
            'success' => true, 
            'message' => 'Thank you! Your inquiry has been received. Our team will contact you shortly.'
        ]);
    } catch (Exception $e) {
        if (DEBUG_MODE) {
            jsonResponse(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        } else {
            jsonResponse(['success' => false, 'message' => 'An error occurred while saving your inquiry.']);
        }
    }

} elseif ($type === 'pre_order') {
    // ── Handle Pre-Order Form ──
    $region = sanitize($_POST['region'] ?? '');
    $bodyStyle = sanitize($_POST['body_style'] ?? '');
    $makeModel = sanitize($_POST['make_model'] ?? '');
    $yearFrom = (int) ($_POST['year_from'] ?? 0);
    $color = sanitize($_POST['color'] ?? '');
    $budget = sanitize($_POST['budget'] ?? '');
    $timeline = sanitize($_POST['timeline'] ?? '');
    $notes = sanitize($_POST['notes'] ?? '');
    
    $fullName = sanitize($_POST['full_name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $hasWhatsapp = !empty($_POST['has_whatsapp']) ? $phone : '';

    if (empty($makeModel) || empty($fullName) || empty($phone) || empty($budget)) {
        jsonResponse(['success' => false, 'message' => 'Please fill in all required fields.']);
    }

    // Convert budget range to numbers for DB (Lakh BDT -> paisa)
    $budgetMin = null;
    $budgetMax = null;
    if (str_contains($budget, '-')) {
        [$min, $max] = explode('-', $budget);
        $budgetMin = (int)$min * 10000000;
        $budgetMax = (int)$max * 10000000;
    } elseif ($budget === '100-150') {
        $budgetMin = 1000000000; // 1 Cr
        $budgetMax = 1500000000; // 1.5 Cr
    } elseif ($budget === '150+') {
        $budgetMin = 1500000000;
    }

    try {
        dbInsert('pre_orders', [
            'region_of_origin' => $region,
            'body_style'       => $bodyStyle,
            'make_model'       => $makeModel,
            'year_from'        => $yearFrom ?: null,
            'color_preference' => $color,
            'budget_min_bdt'   => $budgetMin,
            'budget_max_bdt'   => $budgetMax,
            'timeline'         => $timeline,
            'additional_notes' => $notes,
            'full_name'        => $fullName,
            'phone'            => $phone,
            'email'            => $email,
            'whatsapp'         => $hasWhatsapp,
            'status'           => 'new'
        ]);

        // Send email notification to Admin
        $adminEmail = getSetting('company_email', 'goodcarimports.bd@gmail.com');
        $subject = "New Pre-Order: $makeModel";
        $body = "
            <h2>New Pre-Order Request</h2>
            <p><strong>Name:</strong> $fullName</p>
            <p><strong>Phone:</strong> $phone</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>WhatsApp:</strong> " . ($hasWhatsapp ? 'Yes' : 'No') . "</p>
            <hr>
            <p><strong>Car:</strong> $makeModel</p>
            <p><strong>Region:</strong> $region</p>
            <p><strong>Body Style:</strong> $bodyStyle</p>
            <p><strong>Year (From):</strong> $yearFrom</p>
            <p><strong>Color:</strong> $color</p>
            <p><strong>Budget:</strong> $budget Lakh BDT</p>
            <p><strong>Timeline:</strong> $timeline</p>
            <p><strong>Notes:</strong><br/>".nl2br($notes)."</p>
        ";
        sendEmail($adminEmail, $subject, $body, $email ?: null);

        jsonResponse([
            'success' => true, 
            'message' => 'Your pre-order request has been received! Our sourcing experts will contact you soon.'
        ]);
    } catch (Exception $e) {
        if (DEBUG_MODE) {
            jsonResponse(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        } else {
            jsonResponse(['success' => false, 'message' => 'An error occurred while submitting your pre-order.']);
        }
    }
} else {
    jsonResponse(['success' => false, 'message' => 'Invalid submission type.']);
}
