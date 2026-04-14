<?php
ini_set('display_errors', 0);
header('Content-Type: application/json');
require_once dirname(__DIR__) . '/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo json_encode(['error' => 'No ID']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT estado FROM pse WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        echo json_encode(['estado' => (int)$row['estado']]);
    } else {
        echo json_encode(['error' => 'Not found']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'DB error']);
}
?>
