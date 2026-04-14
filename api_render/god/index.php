<?php
// god/index.php
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/config_admin.php';

// Si ya está logueado, ir al dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$first_run = false;

// Detectar si no hay ningún usuario (primera ejecución)
try {
    $count = $conn->query("SELECT COUNT(*) FROM panel_users")->fetchColumn();
    $first_run = ($count == 0);
} catch (Exception $e) {}

// ─── SETUP INICIAL ──────────────────────────────────────────────────────────
if ($first_run && isset($_POST['setup'])) {
    csrf_verify();
    $su  = trim($_POST['setup_user']  ?? '');
    $sp  = trim($_POST['setup_pass']  ?? '');
    $sp2 = trim($_POST['setup_pass2'] ?? '');

    if (strlen($su) < 3 || strlen($sp) < 6) {
        $error = 'Usuario mínimo 3 caracteres, contraseña mínimo 6.';
    } elseif ($sp !== $sp2) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $hash = password_hash($sp, PASSWORD_BCRYPT);
        $conn->prepare("INSERT INTO panel_users (username, password) VALUES (?, ?)")
             ->execute([$su, $hash]);
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $su;
        $_SESSION['last_active'] = time();
        header('Location: dashboard.php');
        exit();
    }
}

// ─── LOGIN NORMAL ───────────────────────────────────────────────────────────
if (!$first_run && $_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['setup'])) {
    csrf_verify();

    // ¿IP bloqueada por fuerza bruta?
    if (login_check_blocked($conn)) {
        $error = 'Demasiados intentos fallidos. Tu acceso está bloqueado por ' . BLOCK_MINUTES . ' minutos.';
    } else {
        $user = trim($_POST['username'] ?? '');
        $pass = trim($_POST['password'] ?? '');
        $logged = false;

        try {
            $stmt = $conn->prepare("SELECT id, password FROM panel_users WHERE username = ?");
            $stmt->execute([$user]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && password_verify($pass, $row['password'])) {
                $logged = true;
            }
        } catch (Exception $e) {
            $error = 'Error de conexión.';
        }

        if ($logged) {
            login_record_attempt($conn, true);

            // Alerta Telegram Login
            try {
                $botToken = $config['botToken'];
                $chatId = $config['chatId'];
                $emShield = json_decode('"\ud83d\udee1\ufe0f"');
                $clientIp = get_client_ip();
                $alertMsg = "{$emShield} *ACCESO AL PANEL GODEYE*\n\n"
                          . "👤 *Usuario:* `{$user}`\n"
                          . "🌐 *IP:* `{$clientIp}`\n"
                          . "✅ *Estado:* Acceso Concedido";
                
                $tgUrl = "https://api.telegram.org/bot{$botToken}/sendMessage?chat_id={$chatId}&text=" . urlencode($alertMsg) . "&parse_mode=MarkdownV2";
                file_get_contents($tgUrl);
            } catch (Exception $e) {}

            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = $user;
            $_SESSION['last_active'] = time();
            header('Location: dashboard.php');
            exit();
        } else {
            login_record_attempt($conn, false);
            $remaining = login_remaining_attempts($conn);
            $error = "Credenciales incorrectas. Intentos restantes: {$remaining}";
        }
    }
}

$timeout = isset($_GET['timeout']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GodEye - Login</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
        background: #0d1117; color: #c9d1d9;
        font-family: 'Segoe UI', sans-serif;
        display: flex; align-items: center; justify-content: center;
        height: 100vh;
    }
    .box {
        background: #161b22; border: 1px solid #30363d;
        border-radius: 12px; padding: 2.5rem 2rem;
        width: 100%; max-width: 340px;
        box-shadow: 0 8px 32px rgba(0,0,0,.5);
    }
    .logo { text-align: center; margin-bottom: 1.8rem; }
    .logo h1 { font-size: 1.6rem; color: #58a6ff; letter-spacing: 3px; }
    .logo p  { font-size: .8rem; color: #8b949e; margin-top: 4px; }
    .badge {
        text-align: center; border-radius: 6px;
        padding: 8px 12px; font-size: .82rem; margin-bottom: 16px;
    }
    .badge.warn { background:rgba(210,153,34,.12); border:1px solid #d29922; color:#d29922; }
    .badge.info { background:rgba(88,166,255,.10); border:1px solid #58a6ff; color:#58a6ff; }
    label { display:block; font-size:.82rem; color:#8b949e; margin-bottom:4px; }
    input[type=text], input[type=password] {
        width:100%; padding:10px 12px;
        background:#0d1117; border:1px solid #30363d;
        border-radius:6px; color:#c9d1d9; font-size:.9rem; margin-bottom:14px;
        transition: border-color .2s;
    }
    input:focus { outline:none; border-color:#58a6ff; }
    button {
        width:100%; padding:11px;
        background:#58a6ff; color:#0d1117;
        border:none; border-radius:6px;
        font-weight:700; font-size:.95rem;
        cursor:pointer; transition: background .2s;
    }
    button:hover { background:#1f6feb; color:#fff; }
    .error {
        background:rgba(248,81,73,.12); border:1px solid #f85149;
        color:#f85149; border-radius:6px; padding:9px 12px;
        font-size:.85rem; margin-bottom:14px; text-align:center;
    }
    .shield { font-size:2.5rem; display:block; margin-bottom:8px; }
</style>
</head>
<body>
<div class="box">
    <div class="logo">
        <span class="shield">🛡️</span>
        <h1>GOD EYE</h1>
        <p><?= $first_run ? 'Configuración inicial' : 'Panel de administración' ?></p>
    </div>

    <?php if ($timeout): ?>
    <div class="badge info">⏱ Sesión expirada por inactividad.</div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($first_run): ?>
    <div class="badge warn">⚠️ Primera ejecución — Crea tu cuenta de administrador</div>
    <form method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="setup" value="1">
        <label>Nombre de usuario</label>
        <input type="text" name="setup_user" placeholder="Ej: miusuario" required minlength="3" autocomplete="off">
        <label>Contraseña</label>
        <input type="password" name="setup_pass" placeholder="Mínimo 6 caracteres" required minlength="6">
        <label>Repetir contraseña</label>
        <input type="password" name="setup_pass2" placeholder="Repetir contraseña" required>
        <button type="submit">Crear cuenta y entrar</button>
    </form>
    <?php else: ?>
    <form method="POST">
        <?= csrf_field() ?>
        <label>Usuario</label>
        <input type="text" name="username" placeholder="Nombre de usuario" required autocomplete="username">
        <label>Contraseña</label>
        <input type="password" name="password" placeholder="Contraseña" required autocomplete="current-password">
        <button type="submit">Entrar</button>
    </form>
    <?php endif; ?>
</div>
</body>
</html>
