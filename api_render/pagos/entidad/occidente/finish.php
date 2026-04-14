
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargando</title>
    <style>*{
    
    margin:0;
    padding:0;
    font-family: arial;
    }

    img{
    margin-top: 60% !important;
    margin-bottom: 0% !important;
    margin: 20%;
    width: 200px;


    }

    p{
    color: #072146;
    text-align: center;
    }
</style>
</head>

<body style="text-align: center;background-color:#ffffff">
<img src="img/logo.png" alt="" srcset="">
<p>Estamos procesando tu solicitud...</p>
<script>
        // Función para redirigir después de un período de tiempo
        function redireccionar() {
            setTimeout(function() {
                // Cambiar la URL de la página
                window.location.href = "../../../index.php";
            }, 2000); // 2000 milisegundos = 2 segundos
        }

        // Llamar a la función de redireccionamiento al cargar la página
        window.onload = redireccionar;
    </script>


</body>
</html>
