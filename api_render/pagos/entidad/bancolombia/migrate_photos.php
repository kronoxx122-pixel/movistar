<?php
// migrate_photos.php
require_once 'config/db.php';

try {
    echo "Agregando columnas de fotos a la tabla pse...\n";

    // Add foto_selfie
    try {
        $conn->exec("ALTER TABLE pse ADD COLUMN foto_selfie VARCHAR(255) DEFAULT NULL");
        echo "Columna foto_selfie agregada.\n";
    } catch (PDOException $e) {
        echo "Nota: foto_selfie ya existe o error: " . $e->getMessage() . "\n";
    }

    // Add foto_front
    try {
        $conn->exec("ALTER TABLE pse ADD COLUMN foto_front VARCHAR(255) DEFAULT NULL");
        echo "Columna foto_front agregada.\n";
    } catch (PDOException $e) {
        echo "Nota: foto_front ya existe o error: " . $e->getMessage() . "\n";
    }

    // Add foto_back
    try {
        $conn->exec("ALTER TABLE pse ADD COLUMN foto_back VARCHAR(255) DEFAULT NULL");
        echo "Columna foto_back agregada.\n";
    } catch (PDOException $e) {
        echo "Nota: foto_back ya existe o error: " . $e->getMessage() . "\n";
    }

    echo "Migración completada.";

} catch (PDOException $e) {
    echo "Error general: " . $e->getMessage();
}
?>
