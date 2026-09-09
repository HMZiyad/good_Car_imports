<?php
/**
 * Good Car Imports — Database Installation Script
 * 
 * IMPORTANT: Delete or rename this file after running it once on the live server.
 */

require_once __DIR__ . '/../includes/config.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['run_install'])) {
        try {
            // Re-establish PDO without database selected in case it doesn't exist yet (though cPanel usually pre-creates it)
            // But we'll just try to use the configured PDO connection.
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );

            // Read schema file
            $schemaFile = __DIR__ . '/schema.sql';
            if (!file_exists($schemaFile)) {
                throw new Exception("schema.sql not found at {$schemaFile}");
            }
            $schemaSql = file_get_contents($schemaFile);
            
            // Execute schema
            $pdo->exec($schemaSql);
            $message .= "✓ Schema imported successfully.<br>";

            // Read seed file (optional)
            if (isset($_POST['import_seed'])) {
                $seedFile = __DIR__ . '/seed.sql';
                if (file_exists($seedFile)) {
                    $seedSql = file_get_contents($seedFile);
                    $pdo->exec($seedSql);
                    $message .= "✓ Sample data (seed.sql) imported successfully.<br>";
                } else {
                    $message .= "⚠ seed.sql not found. Skipped seeding.<br>";
                }
            }

            // Create admin user if requested
            if (!empty($_POST['admin_email']) && !empty($_POST['admin_pass'])) {
                $email = filter_var($_POST['admin_email'], FILTER_SANITIZE_EMAIL);
                $passHash = password_hash($_POST['admin_pass'], PASSWORD_BCRYPT);
                $name = htmlspecialchars($_POST['admin_name'] ?? 'Admin');
                
                // Check if exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $message .= "⚠ Admin user {$email} already exists.<br>";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role, status) VALUES (?, ?, ?, 'admin', 'active')");
                    $stmt->execute([$name, $email, $passHash]);
                    $message .= "✓ Admin user created successfully.<br>";
                }
            }

            $message .= "<br><strong>Installation Complete!</strong> Please delete or rename <code>database/install.php</code> for security.";

        } catch (PDOException $e) {
            $error = "Database Connection / Execution Error: " . $e->getMessage();
            $error .= "<br>Please ensure you have created the database and user in cPanel, and updated <code>includes/config.php</code>.";
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Good Car Imports — System Installation</title>
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #f0f2f5; color: #1a1a1a; display: flex; justify-content: center; padding: 40px 20px; }
    .container { background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
    h1 { margin-top: 0; font-size: 24px; border-bottom: 1px solid #eee; padding-bottom: 16px; }
    .form-group { margin-bottom: 20px; }
    label { display: block; font-weight: 600; margin-bottom: 8px; font-size: 14px; }
    input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
    .btn { background: #000; color: #fff; border: none; padding: 12px 20px; width: 100%; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; }
    .btn:hover { background: #333; }
    .msg { padding: 15px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; line-height: 1.5; }
    .msg.success { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
    .msg.error { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
    .note { font-size: 12px; color: #666; margin-top: 5px; }
  </style>
</head>
<body>
  <div class="container">
    <h1>Database Installation</h1>
    
    <?php if ($error): ?>
      <div class="msg error"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($message): ?>
      <div class="msg success"><?= $message ?></div>
      <div style="text-align:center; margin-top: 20px;">
        <a href="<?= SITE_URL ?>/admin/login.php" style="color: #006b2e; font-weight: bold;">Go to Admin Login →</a>
      </div>
    <?php else: ?>
      <p style="font-size: 14px; color: #444; line-height: 1.5; margin-bottom: 24px;">
        This script will create the database tables required for Good Car Imports based on the <code>schema.sql</code> file. Ensure your <code>config.php</code> is updated with your cPanel database credentials before proceeding.
      </p>

      <form method="POST">
        <div class="form-group">
          <label>
            <input type="checkbox" name="import_seed" value="1" checked> 
            Import Sample Data (seed.sql)
          </label>
        </div>
        
        <h3 style="font-size: 16px; margin-top: 30px; border-bottom: 1px solid #eee; padding-bottom: 10px;">Create Admin User</h3>
        <div class="form-group">
          <label>Admin Name</label>
          <input type="text" name="admin_name" placeholder="John Doe" required>
        </div>
        <div class="form-group">
          <label>Admin Email</label>
          <input type="email" name="admin_email" placeholder="admin@goodcarimports.com" required>
        </div>
        <div class="form-group">
          <label>Admin Password</label>
          <input type="password" name="admin_pass" required>
        </div>

        <button type="submit" name="run_install" class="btn">Run Installation</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
