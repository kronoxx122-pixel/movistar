<?php
// god/users.php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/../pagos/db.php';

// Crear tabla si no existe
try {
    $conn->exec("CREATE TABLE IF NOT EXISTS panel_users (
        id SERIAL PRIMARY KEY,
        username VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT NOW()
    )");
} catch (Exception $e) {}

$msg = '';
$msgType = '';

// Crear usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    csrf_verify();
    $action = $_POST['action'];

    if ($action === 'create') {
        $newUser = trim($_POST['new_username'] ?? '');
        $newPass = trim($_POST['new_password'] ?? '');

        if (strlen($newUser) < 3 || strlen($newPass) < 6) {
            $msg = 'Usuario mínimo 3 caracteres, contraseña mínimo 6.';
            $msgType = 'error';
        } else {
            try {
                $hash = password_hash($newPass, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("INSERT INTO panel_users (username, password) VALUES (:u, :p)");
                $stmt->execute(['u' => $newUser, 'p' => $hash]);
                $msg = "Usuario <strong>$newUser</strong> creado con éxito.";
                $msgType = 'success';
            } catch (Exception $e) {
                $msg = 'Error: el usuario ya existe.';
                $msgType = 'error';
            }
        }
    }

    if ($action === 'delete') {
        $delId = intval($_POST['del_id'] ?? 0);
        // No permitir borrar al usuario actualmente logueado
        $meUser = $_SESSION['admin_user'] ?? '';
        $me = $conn->prepare("SELECT username FROM panel_users WHERE id = ?");
        $me->execute([$delId]);
        $meRow = $me->fetch(PDO::FETCH_ASSOC);
        if ($meRow && $meRow['username'] === $meUser) {
            $msg = 'No puedes eliminar tu propio usuario.';
            $msgType = 'error';
        } else {
            $conn->prepare("DELETE FROM panel_users WHERE id = ?")->execute([$delId]);
            $msg = 'Usuario eliminado.';
            $msgType = 'success';
        }
    }

    if ($action === 'change_pass') {
        $uid = intval($_POST['uid'] ?? 0);
        $np  = trim($_POST['np'] ?? '');
        if (strlen($np) < 6) {
            $msg = 'La contraseña debe tener al menos 6 caracteres.';
            $msgType = 'error';
        } else {
            $hash = password_hash($np, PASSWORD_BCRYPT);
            $conn->prepare("UPDATE panel_users SET password = ? WHERE id = ?")->execute([$hash, $uid]);
            $msg = 'Contraseña actualizada.';
            $msgType = 'success';
        }
    }
}

// Obtener todos los usuarios
$users = $conn->query("SELECT id, username, created_at FROM panel_users ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>GodEye · Gestión de Usuarios</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
  :root {
    --bg: #0d1117;
    --surface: #161b22;
    --border: #30363d;
    --accent: #58a6ff;
    --danger: #f85149;
    --success: #3fb950;
    --warning: #d29922;
    --text: #c9d1d9;
    --muted: #8b949e;
  }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { background: var(--bg); color: var(--text); font-family: 'Segoe UI', sans-serif; min-height: 100vh; }

  /* NAV */
  .nav { background: var(--surface); border-bottom: 1px solid var(--border); padding: 12px 24px; display: flex; align-items: center; gap: 16px; }
  .nav a { color: var(--muted); text-decoration: none; font-size: .9rem; transition: color .2s; }
  .nav a:hover, .nav a.active { color: var(--accent); }
  .nav .brand { font-weight: 700; color: var(--accent); margin-right: auto; font-size: 1.05rem; }

  /* LAYOUT */
  .container { max-width: 860px; margin: 36px auto; padding: 0 16px; }
  h2 { font-size: 1.3rem; margin-bottom: 24px; color: #fff; display: flex; align-items: center; gap: 10px; }

  /* CARD */
  .card { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 24px; margin-bottom: 24px; }
  .card h3 { font-size: 1rem; color: var(--accent); margin-bottom: 16px; }

  /* FORM */
  .form-row { display: flex; gap: 10px; flex-wrap: wrap; }
  .form-row input { flex: 1; min-width: 160px; padding: 9px 12px; background: var(--bg); border: 1px solid var(--border); border-radius: 6px; color: var(--text); font-size: .9rem; }
  .form-row input:focus { outline: none; border-color: var(--accent); }
  .btn { padding: 9px 18px; border: none; border-radius: 6px; cursor: pointer; font-size: .875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: opacity .2s; }
  .btn:hover { opacity: .85; }
  .btn-primary { background: var(--accent); color: #0d1117; }
  .btn-danger  { background: var(--danger); color: #fff; }
  .btn-warning { background: var(--warning); color: #0d1117; }

  /* TABLE */
  table { width: 100%; border-collapse: collapse; font-size: .9rem; }
  th { color: var(--muted); text-align: left; padding: 8px 12px; border-bottom: 1px solid var(--border); font-weight: 600; font-size: .8rem; text-transform: uppercase; letter-spacing: .05em; }
  td { padding: 12px 12px; border-bottom: 1px solid var(--border); vertical-align: middle; }
  tr:last-child td { border-bottom: none; }
  tr:hover td { background: rgba(88,166,255,.04); }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: .75rem; background: rgba(88,166,255,.15); color: var(--accent); }

  /* ALERT */
  .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: .9rem; }
  .alert.success { background: rgba(63,185,80,.15); border: 1px solid var(--success); color: var(--success); }
  .alert.error   { background: rgba(248,81,73,.15);  border: 1px solid var(--danger);  color: var(--danger); }

  /* INLINE FORM */
  .inline-form { display: flex; gap: 6px; align-items: center; }
  .inline-form input { padding: 6px 10px; background: var(--bg); border: 1px solid var(--border); border-radius: 6px; color: var(--text); font-size: .8rem; width: 130px; }
</style>
</head>
<body>

<nav class="nav">
  <span class="brand"><i class="fas fa-eye"></i> GodEye</span>
  <a href="dashboard.php"><i class="fas fa-chart-bar"></i> Dashboard</a>
  <a href="users.php" class="active"><i class="fas fa-users"></i> Usuarios</a>
  <a href="audit.php"><i class="fas fa-shield-alt"></i> Seguridad</a>
  <a href="index.php?logout=1"><i class="fas fa-sign-out-alt"></i> Salir</a>
</nav>

<div class="container">

  <h2><i class="fas fa-users-cog"></i> Gestión de Usuarios del Panel</h2>

  <?php if ($msg): ?>
  <div class="alert <?= $msgType ?>">
    <i class="fas <?= $msgType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
    <?= $msg ?>
  </div>
  <?php endif; ?>

  <!-- Crear usuario -->
  <div class="card">
    <h3><i class="fas fa-user-plus"></i> Crear nuevo usuario</h3>
    <form method="POST">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="create">
      <div class="form-row">
        <input type="text" name="new_username" placeholder="Nombre de usuario" required minlength="3">
        <input type="password" name="new_password" placeholder="Contraseña (mín. 6 caracteres)" required minlength="6">
        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Crear</button>
      </div>
    </form>
  </div>

  <!-- Lista de usuarios -->
  <div class="card">
    <h3><i class="fas fa-list"></i> Usuarios activos (<?= count($users) ?>)</h3>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Usuario</th>
          <th>Creado</th>
          <th>Nueva Contraseña</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td><span class="badge"><?= $u['id'] ?></span></td>
          <td><i class="fas fa-user" style="color:var(--muted);margin-right:6px"></i><?= htmlspecialchars($u['username']) ?></td>
          <td style="color:var(--muted);font-size:.8rem"><?= date('d/m/Y H:i', strtotime($u['created_at'])) ?></td>
          <td>
            <form method="POST" class="inline-form">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="change_pass">
              <input type="hidden" name="uid" value="<?= $u['id'] ?>">
              <input type="password" name="np" placeholder="Nueva clave" minlength="6">
              <button type="submit" class="btn btn-warning" title="Cambiar contraseña"><i class="fas fa-key"></i></button>
            </form>
          </td>
          <td>
            <form method="POST" onsubmit="return confirm('¿Eliminar usuario <?= htmlspecialchars($u['username']) ?>?')">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="del_id" value="<?= $u['id'] ?>">
              <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div>
</body>
</html>
