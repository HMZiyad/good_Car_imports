<?php
/**
 * Good Car Imports — Admin Auth Middleware
 * Include this at the top of every protected admin page
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/functions.php';

// Ensure user is authenticated
requireAuth();

// Get current user details for the header
$currentUser = getCurrentUser();
