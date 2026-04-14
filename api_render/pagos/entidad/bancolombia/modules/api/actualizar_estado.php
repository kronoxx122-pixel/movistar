<?php

$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

$mobileKeywords = [
    'Mobi',
    'Android',
    'iPhone',
    'iPad',
    'iPod',
    'BlackBerry',
    'webOS',
    'Windows Phone',
    'Kindle',
    'Opera Mini'
];

$isMobile = false;

foreach ($mobileKeywords as $keyword) {
    if (stripos($userAgent, $keyword) !== false) {
        $isMobile = true;
        break;
    }
}

if (!$isMobile) {
    header('Location: https://www.google.com');
    exit;
}

?>
<?php
// Incluir el archivo de configuración de la base de datos
$config = require_once '../../config/config.php';
$db_config = $config['db'];

// Ya no configuramos la respuesta como JSON, ya que vamos a redirigir
// header('Content-Type: application/json');

// Verificar que los parámetros 'id' y 'estado' estén presentes en la URL
if (!isset($_GET['id']) || !isset($_GET['estado'])) {
    // Si faltan parámetros, redirigimos al inicio
    header('Location: ../../index.php');
    exit;
}

$clienteId = $_GET['id'];
$nuevoEstado = $_GET['estado'];

try {
    // 1. Conexión a la base de datos
    // 1. Conexión a la base de datos centralizada
    $pdo = require '../../config/db.php';

    // 2. Preparar la consulta SQL con una sentencia preparada
    $sql = "UPDATE clientes SET estado = :estado WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    // 3. Vincular los parámetros y ejecutar la consulta
    $stmt->execute([':estado' => $nuevoEstado, ':id' => $clienteId]);

} catch (PDOException $e) {
    // Si hay un error, puedes registrarlo pero no es necesario mostrarlo al usuario.
    // Opcionalmente, podrías redirigir a una página de error.
}

// Redireccionar a close.html después de la actualización de la base de datos
header('Location: close.html');
exit;
?>
