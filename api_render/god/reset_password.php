<?php
// god/reset_password.php
require_once __DIR__ . '/config_admin.php';

try {
    // Vaciar la tabla de usuarios para forzar el instalador inicial
    $conn->exec("TRUNCATE TABLE panel_users RESTART IDENTITY CASCADE");
    
    echo "<h1>Panel GodEye restablecido correctamente</h1>";
    echo "<p>Se han eliminado todos los usuarios de administración.</p>";
    echo "<p><strong>Paso siguiente:</strong> Entra a la ruta del panel de control de tu web (ej. <code>/god/</code>) y el sistema te pedirá crear una nueva cuenta de administrador.</p>";
    echo "<p style='color:red;'><strong>IMPORTANTE:</strong> Por seguridad, elimina este archivo (<code>reset_password.php</code>) después de crear tu usuario.</p>";
} catch (Exception $e) {
    echo "<h1>Error al restablecer</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
