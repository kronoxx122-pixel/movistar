<?php
// god/audit.php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/../pagos/db.php';

$msg = '';
$msgType = '';

// Desbloquear IP manually
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unblock_ip'])) {
    csrf_verify();
    $ip = $_POST['unblock_ip'];
    try {
        $conn->prepare("DELETE FROM login_attempts WHERE ip = ?")->execute([$ip]);
        $msg = "IP <strong>$ip</strong> desbloqueada.";
        $msgType = 'success';
    } catch (Exception $e) {
        $msg = "Error al desbloquear.";
        $msgType = 'error';
    }
}

// Obtener intentos de login
$attempts = $conn->query("
    SELECT ip, success, attempted_at 
    FROM login_attempts 
    ORDER BY attempted_at DESC 
    LIMIT 100
")->fetchAll(PDO::FETCH_ASSOC);

// Obtener IPs actualmente bloqueadas
$blocked_ips = $conn->query("
    SELECT ip, COUNT(*) as failures, MAX(attempted_at) as last_attempt
    FROM login_attempts
    WHERE success = FALSE
      AND attempted_at > NOW() - INTERVAL '" . BLOCK_MINUTES . " minutes'
    GROUP BY ip
    HAVING COUNT(*) >= " . MAX_ATTEMPTS . "
")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>GodEye · Auditoría de Seguridad</title>
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
  .container { max-width: 1000px; margin: 36px auto; padding: 0 16px; }
  h2 { font-size: 1.3rem; margin-bottom: 24px; color: #fff; display: flex; align-items: center; gap: 10px; }

  .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
  @media (max-width: 800px) { .grid { grid-template-columns: 1fr; } }

  /* CARD */
  .card { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 24px; margin-bottom: 24px; }
  .card h3 { font-size: 1rem; color: var(--accent); margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }

  /* TABLE */
  table { width: 100%; border-collapse: collapse; font-size: .85rem; }
  th { color: var(--muted); text-align: left; padding: 8px 12px; border-bottom: 1px solid var(--border); text-transform: uppercase; font-size: .75rem; }
  td { padding: 10px 12px; border-bottom: 1px solid var(--border); }
  
  .badge { padding: 2px 8px; border-radius: 4px; font-size: .7rem; font-weight: bold; }
  .badge-success { background: rgba(63,185,80,.15); color: var(--success); border: 1px solid var(--success); }
  .badge-danger { background: rgba(248,81,73,.15); color: var(--danger); border: 1px solid var(--danger); }

  .btn-small { padding: 4px 8px; border-radius: 4px; border: 1px solid var(--border); background: var(--bg); color: var(--text); cursor: pointer; font-size: .75rem; }
  .btn-small:hover { border-color: var(--accent); color: var(--accent); }

  .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: .9rem; }
  .alert.success { background: rgba(63,185,80,.15); border: 1px solid var(--success); color: var(--success); }
</style>
</head>
<body>

<nav class="nav">
  <span class="brand"><i class="fas fa-eye"></i> GodEye Audit</span>
  <a href="dashboard.php"><i class="fas fa-chart-bar"></i> Dashboard</a>
  <a href="users.php"><i class="fas fa-users"></i> Usuarios</a>
  <a href="audit.php" class="active"><i class="fas fa-shield-alt"></i> Seguridad</a>
  <a href="index.php?logout=1"><i class="fas fa-sign-out-alt"></i> Salir</a>
</nav>

<div class="container">
  <h2><i class="fas fa-shield-virus"></i> Auditoría de Seguridad y Accesos</h2>

  <?php if ($msg): ?>
  <div class="alert success"><?= $msg ?></div>
  <?php endif; ?>

  <div class="grid">
    <!-- IPs Bloqueadas -->
    <div class="card">
      <h3><i class="fas fa-user-slash"></i> IPs Bloqueadas (Fuerza Bruta)</h3>
      <?php if (empty($blocked_ips)): ?>
        <p style="color:var(--muted); font-size:.9rem;">No hay IPs bloqueadas actualmente.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>IP</th>
              <th>Fallos</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($blocked_ips as $b): ?>
            <tr>
              <td><code><?= $b['ip'] ?></code></td>
              <td><span class="badge badge-danger"><?= $b['failures'] ?></span></td>
              <td>
                <form method="POST">
                  <?= csrf_field() ?>
                  <input type="hidden" name="unblock_ip" value="<?= $b['ip'] ?>">
                  <button type="submit" class="btn-small">Desbloquear</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

    <!-- Últimos Intentos -->
    <div class="card">
      <h3><i class="fas fa-history"></i> Últimos 100 intentos de Login</h3>
      <div style="max-height: 500px; overflow-y: auto;">
        <table>
          <thead>
            <tr>
              <th>IP</th>
              <th>Resultado</th>
              <th>Fecha/Hora</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($attempts as $a): ?>
            <tr>
              <td><code><?= $a['ip'] ?></code></td>
              <td>
                <?php if ($a['success']): ?>
                  <span class="badge badge-success">EXITOSO</span>
                <?php else: ?>
                  <span class="badge badge-danger">FALLIDO</span>
                <?php endif; ?>
              </td>
              <td style="color:var(--muted); font-size:.75rem;"><?= date('H:i:s d/m', strtotime($a['attempted_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

</body>
</html>
