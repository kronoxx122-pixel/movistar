<?php
// Incluir el archivo de conexión a la base de datos
include '../conexion.php';

// Obtener la IP del cliente
$client_ip = $_SERVER['REMOTE_ADDR'];

// Detección del dispositivo
$deviceType = 'desktop';  // Valor por defecto

if (preg_match('/Mobi/i', $_SERVER['HTTP_USER_AGENT'])) {
    $deviceType = 'mobile';

    if (preg_match('/iPhone|iPad|iPod/i', $_SERVER['HTTP_USER_AGENT'])) {
        $deviceType = 'ios';
    } elseif (preg_match('/Android/i', $_SERVER['HTTP_USER_AGENT'])) {
        $deviceType = 'android';
    }
}

// Verificar si la IP está bloqueada en blacklist
$sql_check_ip = "SELECT * FROM blacklist WHERE ip_address = ?";
$stmt = $conn->prepare($sql_check_ip);
$stmt->bind_param("s", $client_ip);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Si la IP ya está bloqueada, redirigir a una página específica
    header("Location: https://google.com");
    exit();
}

// Redirigir solo si el dispositivo es de escritorio y no está bloqueado
if ($deviceType == 'desktop') {
    // Verificar si la IP ya está en blacklistbot
    $sql_check_bot_ip = "SELECT * FROM blacklistbot WHERE ip_address = ?";
    $stmt_bot = $conn->prepare($sql_check_bot_ip);
    $stmt_bot->bind_param("s", $client_ip);
    $stmt_bot->execute();
    $result_bot = $stmt_bot->get_result();

    if ($result_bot->num_rows == 0) {
        // Solo insertar si la IP no está en blacklistbot
        $sql_insert_ip = "INSERT INTO blacklistbot (ip_address) VALUES (?)";
        $stmt_insert = $conn->prepare($sql_insert_ip);
        $stmt_insert->bind_param("s", $client_ip);
        $stmt_insert->execute();
        $stmt_insert->close();
    }

    // Redirigir a block.php
    header("Location: https://google.com");
    exit();
}

exit();
?>
