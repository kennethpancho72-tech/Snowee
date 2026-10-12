<?php
require_once 'config.php';
$error = ''; $success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        $pdo = db();
        $sql = [
"CREATE TABLE IF NOT EXISTS users (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, full_name VARCHAR(120) NOT NULL, username VARCHAR(60) NOT NULL UNIQUE, password_hash VARCHAR(255) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
"CREATE TABLE IF NOT EXISTS agents (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL, phone VARCHAR(40) NULL, address VARCHAR(255) NULL, status ENUM('Active','Inactive') NOT NULL DEFAULT 'Active', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
"CREATE TABLE IF NOT EXISTS inventory_releases (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, agent_id INT UNSIGNED NOT NULL, package_kg DECIMAL(8,2) NOT NULL, quantity INT UNSIGNED NOT NULL, released_at DATE NOT NULL, notes VARCHAR(255) NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, CONSTRAINT fk_release_agent FOREIGN KEY (agent_id) REFERENCES agents(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
"CREATE TABLE IF NOT EXISTS deliveries (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, agent_id INT UNSIGNED NOT NULL, customer_name VARCHAR(120) NOT NULL, customer_phone VARCHAR(40) NULL, package_kg DECIMAL(8,2) NOT NULL, quantity INT UNSIGNED NOT NULL, price_per_kg DECIMAL(10,2) NOT NULL, delivery_date DATE NOT NULL, status ENUM('Delivered','Pending','Cancelled') NOT NULL DEFAULT 'Delivered', notes VARCHAR(255) NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, CONSTRAINT fk_delivery_agent FOREIGN KEY (agent_id) REFERENCES agents(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
"CREATE TABLE IF NOT EXISTS remittances (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, agent_id INT UNSIGNED NOT NULL, amount DECIMAL(12,2) NOT NULL, remittance_date DATE NOT NULL, reference_no VARCHAR(100) NULL, notes VARCHAR(255) NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, CONSTRAINT fk_remittance_agent FOREIGN KEY (agent_id) REFERENCES agents(id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        ];
        foreach ($sql as $query) $pdo->exec($query);
        $count = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        if ($count > 0) {
            $error = 'Setup is already complete because an administrator exists. Delete setup.php from your server.';
        } else {
            $full = trim((string)($_POST['full_name'] ?? ''));
            $username = trim((string)($_POST['username'] ?? ''));
            $password = (string)($_POST['password'] ?? '');
            if ($full === '' || !preg_match('/^[A-Za-z0-9_.-]{3,60}$/', $username) || strlen($password) < 12) {
                $error = 'Enter your name, a username (3–60 letters/numbers/._-), and a password of at least 12 characters.';
            } else {
                $stmt = $pdo->prepare('INSERT INTO users (full_name, username, password_hash) VALUES (?, ?, ?)');
                $stmt->execute([$full, $username, password_hash($password, PASSWORD_DEFAULT)]);
                $success = true;
            }
        }
    } catch (Throwable $ex) {
        $error = 'Setup failed. Check database credentials and make sure the database user has permission to create tables.';
    }
}
$page_title = 'First-time Setup';
require 'includes/header.php';
?>
<div class="login-page">
<section class="login-art"><a class="brand" href="index.php"><img class="brand-logo" src="assets/logo.png" alt="SNOWEE TUBE ICE logo"><span><strong>SNOWEE</strong><small>TUBE ICE · VIZCAYA ICE PLANT</small></span></a><div class="eyebrow" style="color:#53dfcc">FIRST-TIME SETUP</div><h1>Prepare your<br>online workspace.</h1><p>This creates tables in the database configured in config.php and sets up your first administrator.</p></section>
<section class="login-form-wrap"><div class="login-form">
<?php if ($success): ?>
<div class="notice">Administrator created and database tables are ready.</div><p><strong>Next step:</strong> Delete <code>setup.php</code> from your web host now, then sign in.</p><a class="btn" href="index.php">Go to login →</a>
<?php else: ?>
<h2>Install SNOWEE</h2><p>Use a strong password with at least 12 characters.</p>
<?php if ($error): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?>
<form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
<div class="field"><label>Administrator full name</label><input name="full_name" required maxlength="120"></div>
<div class="field" style="margin-top:15px"><label>Username</label><input name="username" required minlength="3" maxlength="60" pattern="[A-Za-z0-9_.-]+"></div>
<div class="field" style="margin-top:15px"><label>Password (12+ characters)</label><input name="password" type="password" required minlength="12" autocomplete="new-password"></div>
<button class="btn" style="width:100%;margin-top:20px">Create administrator →</button></form>
<?php endif; ?>
</div></section></div></body></html>
