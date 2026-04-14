<?php
// god/api_debug.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
require_once __DIR__ . '/../pagos/db.php';

try {
    echo "--- TESTING PSE ---\n";
    $stmt = $conn->query("SELECT * FROM pse LIMIT 2");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($rows);

    echo "--- TESTING NEQUI ---\n";
    $stmt = $conn->query("SELECT * FROM nequi LIMIT 2");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($rows);

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
