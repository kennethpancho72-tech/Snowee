<?php
require_once 'config.php';
if (!empty($_SESSION['user_id'])) redirect('dashboard.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    try {
        $stmt = db()->prepare('SELECT id, full_name, username, password_hash FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            redirect('dashboard.php');
        }
        $error = 'Incorrect username or password.';
    } catch (Throwable $ex) {
        $error = 'Database is not ready. Configure config.php, then run setup.php once.';
    }
}
$page_title = 'Login';
require 'includes/header.php';
?>
<div class="login-page">
  <section class="login-art">
    <a class="brand" href="index.php"><img class="brand-logo" src="assets/logo.png" alt="SNOWEE TUBE ICE logo"><span><strong>SNOWEE</strong><small>TUBE ICE · VIZCAYA ICE PLANT</small></span></a>
    <div class="eyebrow" style="color:#53dfcc">ICE PLANT MANAGEMENT</div>
    <h1>Keep every kilo<br>moving forward.</h1>
    <p>Manage agent stock, customer deliveries, sales, and remittance in one organized workspace.</p>
  </section>
  <section class="login-form-wrap"><form class="login-form" method="post">
    <div class="eyebrow">WELCOME BACK</div><h2>Sign in</h2><p>Enter your administrator credentials to continue.</p>
    <?php if ($error): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?>
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="field"><label for="username">Username</label><input id="username" name="username" autocomplete="username" required autofocus></div>
    <div class="field"><label for="password">Password</label><input id="password" name="password" type="password" autocomplete="current-password" required></div>
    <button class="btn" type="submit">Sign in securely →</button>
    <div class="login-note">Authorized use only. Keep your login details private. If this is a new installation, complete the one-time setup first.</div>
  </form></section>
</div>
</body></html>
