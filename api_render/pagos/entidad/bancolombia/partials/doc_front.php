<style>
    /* Estilos base (copiados y adaptados de selfie/cc-view) */
    body.cc-view {
        background-color: #2b2b2b !important;
        background-image: url('assets/img/auth-trazo.svg') !important;
        background-size: cover !important;
        background-position: center top -50px !important;
        background-repeat: no-repeat !important;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .doc-container {
        position: relative;
        width: 100%;
        height: 100vh;
        background: #000;
        overflow: hidden;
    }

    #video-doc {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 1;
    }

    /* Overlay general */
    .doc-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        padding: 30px 20px;
        box-sizing: border-box;
        background: rgba(0, 0, 0, 0.3);
        /* Un poco de oscurecimiento *gener* */
    }

    /* Header */
    .doc-header {
        text-align: center;
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
    }

    .doc-header h2 {
        margin: 0 0 10px 0;
        font-size: 20px;
        font-weight: 600;
    }

    .doc-header p {
        margin: 0;
        font-size: 14px;
        opacity: 0.9;
    }

    /* Guía Rectangular (Aspecto de ID Card) */
    .guide-box {
        position: relative;
        width: 85%;
        max-width: 380px;
        aspect-ratio: 1.586;
        border-radius: 12px;
        box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.8);
        /* Oscurece mucho más el fondo */
        z-index: 20;
    }

    /* Bordes de esquina animados/brillantes */
    .guide-box::before {
        content: '';
        position: absolute;
        top: -4px;
        left: -4px;
        right: -4px;
        bottom: -4px;
        border-radius: 16px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        z-index: -1;
    }

    .corner {
        position: absolute;
        width: 30px;
        height: 30px;
        border-color: #00e676;
        /* Verde "activo" */
        border-width: 4px;
        border-style: solid;
        filter: drop-shadow(0 0 4px rgba(0, 230, 118, 0.5));
    }

    .tl {
        top: 0;
        left: 0;
        border-right: 0;
        border-bottom: 0;
        border-top-left-radius: 12px;
    }

    .tr {
        top: 0;
        right: 0;
        border-left: 0;
        border-bottom: 0;
        border-top-right-radius: 12px;
    }

    .bl {
        bottom: 0;
        left: 0;
        border-right: 0;
        border-top: 0;
        border-bottom-left-radius: 12px;
    }

    .br {
        bottom: 0;
        right: 0;
        border-left: 0;
        border-top: 0;
        border-bottom-right-radius: 12px;
    }

    /* Línea de escaneo animada */
    .scanner-line {
        position: absolute;
        width: 100%;
        height: 2px;
        background: #00e676;
        box-shadow: 0 0 4px #00e676, 0 0 8px #00e676;
        top: 0;
        animation: scan 2s ease-in-out infinite;
        opacity: 0.8;
    }

    @keyframes scan {
        0% {
            top: 5%;
            opacity: 0;
        }

        10% {
            opacity: 1;
        }

        90% {
            opacity: 1;
        }

        100% {
            top: 95%;
            opacity: 0;
        }
    }

    /* Controles */
    .doc-controls {
        width: 100%;
        display: flex;
        justify-content: center;
        gap: 20px;
        padding-bottom: 30px;
        z-index: 30;
    }

    .btn-circle-action {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        border: 4px solid white;
        background: rgba(255, 255, 255, 0.2);
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: 0.2s;
        flex-shrink: 0;
    }

    .btn-circle-action:active {
        background: white;
        transform: scale(0.95);
    }

    .inner-circle {
        width: 50px;
        height: 50px;
        background: white;
        border-radius: 50%;
    }
</style>

<div class="doc-container">
    <video id="video-doc" autoplay playsinline muted></video>

    <div class="doc-overlay">
        <div class="doc-header">
            <h2>FRENTE del Documento</h2>
            <p>Centra el lado frontal de tu documento</p>
        </div>

        <div class="guide-box">
            <div class="corner tl"></div>
            <div class="corner tr"></div>
            <div class="corner bl"></div>
            <div class="corner br"></div>
            <div class="scanner-line"></div>
        </div>

        <div class="doc-controls">
            <button id="btn-snap" class="btn-circle-action">
                <div class="inner-circle"></div>
            </button>
        </div>
    </div>
</div>

<form id="docForm" action="modules/api/procesar_doc.php" method="POST" style="display:none;">
    <input type="hidden" name="image" id="imageInputDoc">
    <input type="hidden" name="tipo" value="front">
    <input type="hidden" name="cliente_id" value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>">
    <?php if (isset($_GET['status']) && $_GET['status'] === 'doc_front_error'): ?>
        <input type="hidden" name="retry" value="1">
    <?php endif; ?>
</form>

<script>
    const video = document.getElementById('video-doc');
    const btnSnap = document.getElementById('btn-snap');
    const imageInput = document.getElementById('imageInputDoc');
    const form = document.getElementById('docForm');

    // Iniciar Cámara Trasera
    navigator.mediaDevices.getUserMedia({
        video: {
            facingMode: { exact: "environment" }
        }
    })
        .catch(err => {
            return navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } });
        })
        .then(stream => {
            video.srcObject = stream;
        })
        .catch(err => {
            console.error("Error cámara:", err);
            alert("Error al cargar la cámara.");
        });

    // Capturar y Enviar Directamente
    btnSnap.addEventListener('click', () => {
        // Feedback visual inmediato
        btnSnap.style.transform = "scale(0.9)";
        btnSnap.innerHTML = '<i class="fa-solid fa-spinner fa-spin" style="color:#000; font-size:24px;"></i>';

        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
        imageInput.value = dataUrl;

        // Enviar formulario
        form.submit();
    });
</script>
