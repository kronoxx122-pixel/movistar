<?php
// config/check_ban.php
require_once __DIR__ . '/db.php';

$ip_check = $_SERVER['REMOTE_ADDR'];
try {
    $stmt_ban = $conn->prepare("SELECT id FROM blocked_ips WHERE ip = :ip LIMIT 1");
    $stmt_ban->execute(['ip' => $ip_check]);
    if ($stmt_ban->fetch()) {
        http_response_code(403);
        die("<h1>403 Forbidden</h1><p>Your IP has been blocked.</p>");
    }
} catch (Exception $e) {
    // Fail silent or log
}
?>
