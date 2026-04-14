<?php
// Incluir el archivo de conexión a la base de datos y configuración
include '../../../db.php';
$config = include '../../../config.php';

// Clave de seguridad para validar solicitudes
$security_key = $config['security_key']; // Usar clave de config global

// Verificar los parámetros enviados
if (isset($_GET['id'], $_GET['estado'], $_GET['key']) && $_GET['key'] === $security_key) {
    $id = intval($_GET['id']);
    $estado = intval($_GET['estado']);

    // Actualizar el estado en la base de datos 'nequi' (no 'data')
    // Usando PDO
    $sql = "UPDATE nequi SET estado = :estado WHERE id = :id";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        if ($stmt->execute(['estado' => $estado, 'id' => $id])) {
            // Confirmación directa sin redirecciones, evita problemas de caché de end.php antiguo
            echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Estado Actualizado</title></head>";
            echo "<body style='display:flex;justify-content:center;align-items:center;height:100vh;font-family:Arial,sans-serif;background-color:#f4f4f4;'>";
            echo "<div style='text-align:center;background:#fff;padding:40px;border-radius:10px;box-shadow:0 4px 6px rgba(0,0,0,0.1);'>";
            echo "<h1 style='color:#4CAF50;'>✅ ¡Estado Actualizado!</h1>";
            echo "<p style='color:#333;font-size:18px;'>El estado (#$estado) del paciente (#$id) en Nequi se cambió exitosamente.</p>";
            echo "<p style='color:#777;font-size:14px;margin-top:20px;'>Ya puedes cerrar esta pestaña y volver a Telegram.</p>";
            echo "</div></body></html>";
            exit();
        } else {
            error_log("Error al actualizar estado Nequi: " . print_r($stmt->errorInfo(), true));
            echo "Error al actualizar el estado.";
        }
    } else {
        echo "Error al preparar la consulta.";
    }
} else {
    // Mensaje para solicitudes inválidas o no autorizadas con depuración temporal
    http_response_code(400);
    echo "<h1>Debug Info</h1>";
    echo "Acceso no autorizado o parámetros inválidos.<br><br>";
    echo "Parámetros recibidos en GET:<br>";
    echo "<pre>" . print_r($_GET, true) . "</pre>";
    echo "Llave esperada: " . htmlspecialchars($security_key) . "<br>";
    echo "Request URI: " . htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'N/A') . "<br>";
}
?>
