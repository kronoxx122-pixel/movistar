<?php
header('Content-Type: application/json');

// 1. CARGAR CONFIGURACIÓN GLOBAL
$config = require '../../config/config.php';

if (!$config || !is_array($config)) {
    echo json_encode(['error' => 'Error de configuración']);
    exit();
}

try {
    $pdo = require '../../config/db.php';
} catch (Exception $e) {
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit();
}

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'No se proporcionó ID de cliente']);
    exit();
}

$clienteId = $_GET['id'];

try {
    // Usar tabla 'pse' en lugar de 'clientes'
    $sql = "SELECT estado FROM pse WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $clienteId]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        echo json_encode(['estado' => $cliente['estado']]);
    } else {
        echo json_encode(['error' => 'Cliente no encontrado']);
    }

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la consulta']);
}
?>
