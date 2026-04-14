<?php
if (session_status() === PHP_SESSION_NONE) {
if (session_status() === PHP_SESSION_NONE) { session_start(); }
}
include '../../../db.php';
// $config = include '../../../config.php'; // No es estrictamente necesario si solo consultamos DB

// Verificar si se ha pasado un ID por la URL
if (isset($_GET['id'])) {
    $cliente_id = $_GET['id'];

    // 1. Intentar buscar en la tabla unificada (pse)
    $sql = "SELECT estado FROM pse WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['id' => $cliente_id]);

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode(['estado' => $row['estado']]);
        exit();
    }

    // 2. Si no está en pse, buscar en nequi (Modo Legacy)
    $sql = "SELECT estado FROM nequi WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['id' => $cliente_id]);

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode(['estado' => $row['estado']]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['estado' => 'waiting']); // Usar String para evitar Number(null) -> 0
    }
} else {
    // Manejar el caso donde no se pasa un ID
    header('Content-Type: application/json');
    echo json_encode(['estado' => null]);
}

// $conn = null;
?>
