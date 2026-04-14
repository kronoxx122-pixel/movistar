<?php
$config = require __DIR__ . '/config.php';

try {
    $db_host = $config['db_host'];
    $db_name = $config['db_name'];
    $db_user = $config['db_user'];
    $db_pass = $config['db_pass'];
    $db_port = $config['db_port'];

    // Determinar DSN y opciones
    if (getenv('DATABASE_URL') || getenv('PGSSLMODE') === 'require') {
        // Extraer endpoint ID (primer segmento del dominio de Neon) para soportar SNI en clientes antiguos (como XAMPP)
        $endpointId = explode('.', $db_host)[0];
        $dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name;sslmode=require;options='endpoint=$endpointId'";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
    } else {
        // Fallback genérico a MySql (local)
        $dsn = "mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
    }

    $conn = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>
