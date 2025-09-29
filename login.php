<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');

// -- Configuration --
$dbFile = 'admin.db';
$minPinLen = 4;
$maxPinLen = 6;
$maxAttempts = 5;         // attempts before temporary lockout
$lockoutSeconds = 900;    // 15 minutes lockout

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// simple attempt tracking in session
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['login_block_until'] = 0;
}

// helper
function jsonErr(string $msg, int $code = 400) {
    http_response_code($code);
    echo json_encode(['status' => 'error', 'message' => $msg]);
    exit;
}
function jsonOk(array $payload = []) {
    echo json_encode(array_merge(['status' => 'success'], $payload));
    exit;
}

// create / open DB and table
try {
    $db = new SQLite3($dbFile);
    $db->busyTimeout(5000);
    $db->exec('PRAGMA journal_mode = WAL;');
    $create = <<<SQL
CREATE TABLE IF NOT EXISTS admins (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    pin_hash TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
SQL;
    $db->exec($create);
} catch (Exception $e) {
    jsonErr('Unable to open or create database: ' . $e->getMessage(), 500);
}

// ensure POST
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_POST['action'] ?? ($_GET['action'] ?? null);

// allow GET to check session status
if ($method === 'GET' && $action === 'status') {
    $isLogged = !empty($_SESSION['admin_user']);
    jsonOk(['logged_in' => $isLogged, 'user' => $isLogged ? $_SESSION['admin_user'] : null]);
}

if ($method !== 'POST') {
    jsonErr('Only POST requests allowed for this endpoint', 405);
}

// Rate limit / lockout check
$now = time();
if (!empty($_SESSION['login_block_until']) && $now < (int)$_SESSION['login_block_until']) {
    $wait = (int)$_SESSION['login_block_until'] - $now;
    jsonErr("Too many attempts. Try again in {$wait} seconds.", 429);
}

if (!$action) jsonErr('Missing action parameter (register, login, logout)');

$action = strtolower($action);

if ($action === 'register') {
    // Register new admin. Use only if you want to create account from this endpoint.
    $username = trim((string)($_POST['username'] ?? ''));
    $pin = trim((string)($_POST['pin'] ?? ''));

    if ($username === '') jsonErr('Username is required');
    if (!preg_match('/^[A-Za-z0-9_\-]{3,32}$/', $username)) jsonErr('Username must be 3-32 chars (letters, numbers, _ or -)');

    if (!preg_match('/^\d{' . $minPinLen . ',' . $maxPinLen . '}$/', $pin)) {
        jsonErr("PIN must be numeric and {$minPinLen}-{$maxPinLen} digits");
    }

    $pinHash = password_hash($pin, PASSWORD_DEFAULT);
    if ($pinHash === false) jsonErr('Failed to hash PIN', 500);

    // insert
    $stmt = $db->prepare('INSERT INTO admins (username, pin_hash) VALUES (:username, :pin_hash)');
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $stmt->bindValue(':pin_hash', $pinHash, SQLITE3_TEXT);
    $res = @$stmt->execute();

    if ($res === false) {
        // likely unique constraint
        $err = $db->lastErrorMsg();
        if (strpos($err, 'UNIQUE') !== false) {
            jsonErr('Username already exists');
        }
        jsonErr('Failed to create account: ' . $err, 500);
    }

    jsonOk(['message' => 'Admin account created']);
}

// LOGIN
if ($action === 'login') {
    $username = trim((string)($_POST['username'] ?? ''));
    $pin = trim((string)($_POST['pin'] ?? ''));

    if ($username === '' || $pin === '') jsonErr('Username and PIN required');

    if (!preg_match('/^\d{' . $minPinLen . ',' . $maxPinLen . '}$/', $pin)) {
        jsonErr("PIN must be numeric and {$minPinLen}-{$maxPinLen} digits");
    }

    // fetch user
    $stmt = $db->prepare('SELECT id, username, pin_hash FROM admins WHERE username = :username LIMIT 1');
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $row = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

    if (!$row) {
        // increment attempts
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        if ($_SESSION['login_attempts'] >= $maxAttempts) {
            $_SESSION['login_block_until'] = time() + $lockoutSeconds;
            jsonErr('Too many attempts. Try again later.', 429);
        }
        jsonErr('Invalid username or PIN', 401);
    }

    $hash = $row['pin_hash'];
    if (!password_verify($pin, $hash)) {
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        if ($_SESSION['login_attempts'] >= $maxAttempts) {
            $_SESSION['login_block_until'] = time() + $lockoutSeconds;
            jsonErr('Too many attempts. Try again later.', 429);
        }
        jsonErr('Invalid username or PIN', 401);
    }

    // success: reset attempts, set session
    $_SESSION['login_attempts'] = 0;
    $_SESSION['login_block_until'] = 0;
    session_regenerate_id(true);
    $_SESSION['admin_user'] = $row['username'];
    $_SESSION['admin_id'] = (int)$row['id'];

    jsonOk(['message' => 'Logged in', 'user' => $row['username']]);
}

// LOGOUT
if ($action === 'logout') {
    session_unset();
    session_destroy();
    jsonOk(['message' => 'Logged out']);
}

jsonErr('Unknown action');
