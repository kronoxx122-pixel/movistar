<?php
// god/config_admin.php

// Incluir configuración global para BD
require_once __DIR__ . '/../pagos/config.php';
require_once __DIR__ . '/../pagos/db.php';

// Crear tabla de usuarios del panel si no existe
try {
    $conn->exec("CREATE TABLE IF NOT EXISTS panel_users (
        id SERIAL PRIMARY KEY,
        username VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT NOW()
    )");
} catch (Exception $e) {}
?>
