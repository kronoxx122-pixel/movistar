<?php
// god/security.php — Capa de seguridad central del panel

// ─── 1. HEADERS DE SEGURIDAD HTTP ──────────────────────────────────────────
header('X-Frame-Options: DENY');                          // Anti-clickjacking
header('X-Content-Type-Options: nosniff');                // Anti-MIME sniffing
header('X-XSS-Protection: 1; mode=block');               // Anti-XSS viejo
header('Referrer-Policy: no-referrer');                   // No filtra URL al salir
header("Content-Security-Policy: default-src 'self' https://fonts.googleapis.com https://fonts.gstatic.com https://cdnjs.cloudflare.com https://assets.mixkit.co; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; img-src 'self' data:;");
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

// ─── 2. CONFIGURACIÓN SEGURA DE SESIÓN ─────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);      // JS no puede leer la cookie
    ini_set('session.cookie_samesite', 'Strict'); // Sin envío cross-site
    ini_set('session.use_strict_mode', 1);      // No acepta IDs externos
    ini_set('session.gc_maxlifetime', 7200);    // Sesión expira en 2h
    // En producción con HTTPS activar:
    // ini_set('session.cookie_secure', 1);
    session_start();
}

// ─── 3. CSRF TOKEN ─────────────────────────────────────────────────────────
function csrf_generate(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_generate()) . '">';
}

function csrf_verify(): void {
    $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Acción bloqueada: token de seguridad inválido.');
    }
}

// ─── 4. BRUTE FORCE PROTECTION ─────────────────────────────────────────────
define('MAX_ATTEMPTS', 5);       // Intentos antes de bloquear
define('BLOCK_MINUTES', 30);     // Minutos de bloqueo

function get_client_ip(): string {
    foreach (['HTTP_CF_CONNECTING_IP','HTTP_X_FORWARDED_FOR','REMOTE_ADDR'] as $k) {
        if (!empty($_SERVER[$k])) {
            return explode(',', $_SERVER[$k])[0];
        }
    }
    return '0.0.0.0';
}

function login_check_blocked(PDO $conn): bool {
    $ip = get_client_ip();
    try {
        $conn->exec("CREATE TABLE IF NOT EXISTS login_attempts (
            id SERIAL PRIMARY KEY,
            ip VARCHAR(45) NOT NULL,
            attempted_at TIMESTAMP DEFAULT NOW(),
            success BOOLEAN DEFAULT FALSE
        )");
        $stmt = $conn->prepare("
            SELECT COUNT(*) FROM login_attempts
            WHERE ip = ?
              AND success = FALSE
              AND attempted_at > NOW() - INTERVAL '" . BLOCK_MINUTES . " minutes'
        ");
        $stmt->execute([$ip]);
        return (int)$stmt->fetchColumn() >= MAX_ATTEMPTS;
    } catch (Exception $e) {
        return false;
    }
}

function login_record_attempt(PDO $conn, bool $success): void {
    $ip = get_client_ip();
    try {
        if ($success) {
            // Limpiar intentos fallidos anteriores de esta IP al lograr éxito
            $conn->prepare("DELETE FROM login_attempts WHERE ip = ? AND success = FALSE")
                 ->execute([$ip]);
            $conn->prepare("INSERT INTO login_attempts (ip, success) VALUES (?, TRUE)")
                 ->execute([$ip]);
        } else {
            $conn->prepare("INSERT INTO login_attempts (ip, success) VALUES (?, FALSE)")
                 ->execute([$ip]);
        }
        // Limpiar registros viejos (>24h)
        $conn->exec("DELETE FROM login_attempts WHERE attempted_at < NOW() - INTERVAL '24 hours'");
    } catch (Exception $e) {}
}

function login_remaining_attempts(PDO $conn): int {
    $ip = get_client_ip();
    try {
        $stmt = $conn->prepare("
            SELECT COUNT(*) FROM login_attempts
            WHERE ip = ?
              AND success = FALSE
              AND attempted_at > NOW() - INTERVAL '" . BLOCK_MINUTES . " minutes'
        ");
        $stmt->execute([$ip]);
        $count = (int)$stmt->fetchColumn();
        return max(0, MAX_ATTEMPTS - $count);
    } catch (Exception $e) {
        return MAX_ATTEMPTS;
    }
}

// ─── 5. VERIFICAR SESIÓN ACTIVA (para auth.php) ────────────────────────────
function require_auth(): void {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: index.php');
        exit();
    }

    // Regenerar ID de sesión cada 15 minutos (anti-session fixation)
    if (!isset($_SESSION['last_regen']) || time() - $_SESSION['last_regen'] > 900) {
        session_regenerate_id(true);
        $_SESSION['last_regen'] = time();
    }

    // Verificar inactividad (timeout 2h)
    if (isset($_SESSION['last_active']) && time() - $_SESSION['last_active'] > 7200) {
        session_destroy();
        header('Location: index.php?timeout=1');
        exit();
    }
    $_SESSION['last_active'] = time();
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit();
}
?>
