<?php
/**
 * Good Car Imports — Admin Login
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $user = attemptLogin($email, $password);
        if ($user) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Executive Portal Login | Good Car Imports</title>
  
  <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/design-tokens.css">
  
  <style>
    body {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      background: var(--surface-container-low);
      font-family: var(--font-body);
    }
    .login-container {
      width: 100%;
      max-width: 420px;
      padding: var(--unit-lg);
    }
    .login-card {
      background: var(--surface-container-lowest);
      border: 1px solid var(--surface-container-high);
      border-radius: var(--radius-xl);
      padding: var(--unit-xl) var(--unit-lg);
      box-shadow: var(--shadow-modal);
    }
    .login-header {
      text-align: center;
      margin-bottom: var(--unit-xl);
    }
    .login-logo {
      height: 48px;
      margin-bottom: var(--unit-md);
    }
    .login-title {
      font-family: var(--font-headline);
      font-size: 24px;
      font-weight: 700;
      color: var(--on-surface);
    }
    .login-subtitle {
      font-size: 14px;
      color: var(--secondary);
      margin-top: 4px;
    }
    .form-group {
      margin-bottom: var(--unit-md);
    }
    .form-label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: var(--on-surface);
      margin-bottom: 6px;
    }
    .form-input {
      width: 100%;
      padding: 12px;
      border: 1px solid var(--surface-container-high);
      border-radius: var(--radius-lg);
      font-size: 15px;
      transition: border-color var(--transition-fast);
    }
    .form-input:focus {
      outline: none;
      border-color: var(--primary);
    }
    .btn-login {
      display: block;
      width: 100%;
      padding: 14px;
      background: var(--primary);
      color: var(--on-primary);
      font-size: 15px;
      font-weight: 600;
      border: none;
      border-radius: var(--radius-xl);
      cursor: pointer;
      margin-top: var(--unit-lg);
      transition: background var(--transition-fast);
    }
    .btn-login:hover {
      background: var(--primary-container);
    }
    .error-msg {
      padding: 12px;
      background: var(--error-container);
      color: var(--on-error-container);
      border-radius: var(--radius-lg);
      font-size: 14px;
      margin-bottom: var(--unit-md);
      text-align: center;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <img src="<?= ASSETS_URL ?>/images/logo.png" alt="Good Car Imports" class="login-logo" onerror="this.src='<?= ASSETS_URL ?>/images/placeholder-car.svg'; this.style.opacity='0.2'">
        <h1 class="login-title">Executive Portal</h1>
        <p class="login-subtitle">Secure access for authorized personnel</p>
      </div>

      <?php if ($error): ?>
        <div class="error-msg"><?= sanitize($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="login.php">
        <div class="form-group">
          <label class="form-label" for="email">Email Address</label>
          <input type="email" id="email" name="email" class="form-input" required autofocus value="<?= sanitize($_POST['email'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <input type="password" id="password" name="password" class="form-input" required>
        </div>
        <button type="submit" class="btn-login">Sign In</button>
      </form>
    </div>
  </div>

</body>
</html>
