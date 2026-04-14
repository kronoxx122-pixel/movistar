<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (isset($_GET['id'])) { $_SESSION['cliente_id'] = intval($_GET['id']); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirigiendo...</title>
    <script>
        window.location.href = "bbva.php" + window.location.search;
    </script>
</head>
<body>
</body>
</html>
