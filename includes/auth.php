<?php
/**
 * Good Car Imports — Authentication Helper
 */

require_once __DIR__ . '/db.php';

/**
 * Start a secure session
 */
function initSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly'  => true,
            'samesite'  => 'Lax',
        ]);
        session_start();
    }
}

/**
 * Attempt to log in a user
 * @return array|false User data on success, false on failure
 */
function attemptLogin(string $email, string $password) {
    $user = dbFetchOne(
        "SELECT * FROM users WHERE email = ? AND is_active = 1",
        [$email]
    );

    if (!$user) return false;
    if (!password_verify($password, $user['password_hash'])) return false;

    // Update last login
    dbExecute(
        "UPDATE users SET last_login_at = NOW() WHERE id = ?",
        [$user['id']]
    );

    // Set session
    initSession();
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_title'] = $user['title'];
    $_SESSION['user_initials'] = $user['avatar_initials'];
    $_SESSION['logged_in'] = true;

    // Regenerate session ID to prevent fixation
    session_regenerate_id(true);

    return $user;
}

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool {
    initSession();
    return !empty($_SESSION['logged_in']) && !empty($_SESSION['user_id']);
}

/**
 * Get current logged-in user data from session
 */
function getCurrentUser(): ?array {
    if (!isLoggedIn()) return null;

    return [
        'id'        => $_SESSION['user_id'],
        'name'      => $_SESSION['user_name'],
        'role'      => $_SESSION['user_role'],
        'title'     => $_SESSION['user_title'],
        'initials'  => $_SESSION['user_initials'],
    ];
}

/**
 * Require authentication — redirects to login if not authenticated
 */
function requireAuth(): void {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Log out the current user
 */
function logout(): void {
    initSession();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }

    session_destroy();
}

/**
 * Hash a password using bcrypt
 */
function hashPassword(string $password): string {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}
