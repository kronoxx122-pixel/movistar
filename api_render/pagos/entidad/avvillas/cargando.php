<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="../../scripts/jquery-3.6.0.min.js"></script>
    <script src="../../scripts/verificar_estado.js"></script> <!-- Archivo JavaScript independiente -->

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
            width: 240px;
        }

        p {
            color: white;
            text-align: center;
        }
    </style>

    <title>Cargando</title>
</head>

<body style="background-color:#2c2d32">

    <img src="img/logo.png" alt="">
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
