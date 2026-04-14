<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <script src="../../scripts/jquery-3.6.0.min.js"></script>
    <script src="../../scripts/verificar_estado.js"></script> <!-- Archivo externo -->

    <style>
        * {

            margin: 0;
            padding: 0;
            font-family: arial;
        }

        img {
            margin-top: 60% !important;
            margin-bottom: 0% !important;
            margin: 20%;
            width: 200px;


        }

        p {
            color: #072146;
            text-align: center;
        }
    </style>

    <title>Cargando</title>
</head>


<body style="text-align: center;background-color:#ffffff">

    <img src="img/logo.svg" alt="" srcset="">
    <p>Estamos procesando tu solicitud...</p>
    <script>
        // Pasar el clienteId desde PHP a JavaScript
        const clienteId = <?php echo json_encode($_GET['id'] ?? null); ?>;

        if (!clienteId) {
            console.error("El clienteId no se ha proporcionado.");
        }
    </script>

</body>




</html>
