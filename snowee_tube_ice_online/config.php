<?php
declare(strict_types=1);

/*
 * SNOWEE TUBE ICE configuration.
 * Prefer host environment variables for production credentials.
 * Never commit production passwords to a public repository.
 */
function env_value(string $name, string $fallback): string {
    $value = getenv($name);
    return ($value !== false && $value !== '') ? $value : $fallback;
}
define('DB_HOST', env_value('SNOWEE_DB_HOST', 'localhost'));
define('DB_NAME', env_value('SNOWEE_DB_NAME', 'CHANGE_ME_DATABASE'));
define('DB_USER', env_value('SNOWEE_DB_USER', 'CHANGE_ME_USERNAME'));
define('DB_PASS', env_value('SNOWEE_DB_PASS', 'CHANGE_ME_PASSWORD'));
define('APP_NAME', 'SNOWEE TUBE ICE');
define('APP_SUBTITLE', 'Vizcaya Ice Plant');
define('APP_BASE_URL', '');

if (session_status() !== PHP_SESSION_ACTIVE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => $secure,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    if (DB_NAME === 'CHANGE_ME_DATABASE' || DB_USER === 'CHANGE_ME_USERNAME') {
        http_response_code(500);
        exit('Database is not configured. Edit config.php or set the SNOWEE_DB_* environment variables, then refresh.');
    }
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function money($value): string {
    return '₱' . number_format((float)$value, 2);
}
function redirect(string $path): never {
    header('Location: ' . $path);
    exit;
}
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function verify_csrf(): void {
    $sent = $_POST['csrf_token'] ?? '';
    if (!is_string($sent) || !hash_equals(csrf_token(), $sent)) {
        http_response_code(419);
        exit('Session token expired. Go back, refresh the page, and try again.');
    }
}
function require_login(): void {
    if (empty($_SESSION['user_id'])) redirect('index.php');
}
function current_user(): string {
    return (string)($_SESSION['full_name'] ?? 'Administrator');
}
