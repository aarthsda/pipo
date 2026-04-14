<?php

  if (empty($_COOKIE) || $_COOKIE['user_type'] != 'usuario') {
    header("Location: " . __DIR__ . "/../../hub.php?msg=acessoproibido");
    exit();
  }

  require_once __DIR__ . "/../../views/usuarioView.php";
  
  $usuario = new usuarioView(['id_usuario' => $_COOKIE['user_id']]);
  
  if (!$usuario->verifyPassword($_COOKIE['user_password'])) {
    setcookie("user_type", 'guest', time() + 3600, "/");
    setcookie("user_id", '', time() + 3600, "/");
    setcookie("user_password", '', time() + 3600, "/");

    header("Location: " . __DIR__ . "/../../pages/hub.php");
    exit();
  }
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sistema de Cadastro Facial</title>
  <style>
    /* RESET */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* BODY - Padrão Institucional */
body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  background: #f5f7fa;
  min-height: 100vh;
  line-height: 1.6;
  color: #2c3e50;
  padding: 0;
}

/* CONTAINER PRINCIPAL */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 20px;
  background: transparent;
  border-radius: 0;
  box-shadow: none;
  overflow: visible;
}

/* HEADER - Padrão Institucional */
.header {
  background: white;
  border-bottom: 2px solid #e2e8f0;
  padding: 30px;
  text-align: center;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  margin-bottom: 0;
  border-radius: 0;
}

.header h1 {
  font-size: 28px;
  font-weight: 700;
  color: #1a365d;
  margin-bottom: 8px;
}

.header p {
  font-size: 15px;
  color: #64748b;
  margin-bottom: 12px;
}

/* API STATUS */
.api-status {
  display: inline-block;
  padding: 6px 16px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 600;
  margin-top: 12px;
}

.api-status.online {
  background: #d1fae5;
  color: #065f46;
}

.api-status.offline {
  background: #fee2e2;
  color: #991b1b;
}

/* CONTENT AREA */
.content {
  background: white;
  border-radius: 12px;
  padding: 32px;
  border: 2px solid #e2e8f0;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  margin-top: 24px;
}

/* VIDEO CONTAINER */
.video-container {
  position: relative;
  background: #000;
  border-radius: 12px;
  overflow: hidden;
  margin: 25px 0;
  height: 400px;
  display: flex;
  justify-content: center;
  align-items: center;
  border: 2px solid #e2e8f0;
}

#video {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

#overlay {
  position: absolute;
  inset: 0;
  pointer-events: none;
  z-index: 5;
}

#canvas {
  display: none;
}

/* STATUS OVERLAY */
.status {
  position: absolute;
  top: 20px;
  left: 20px;
  background: rgba(26, 54, 93, 0.9);
  color: white;
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  z-index: 10;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

/* ALERTS - Padrão Institucional */
.alert {
  padding: 16px 20px;
  border-radius: 8px;
  margin-bottom: 24px;
  display: none;
  white-space: pre-line;
  border-left: 4px solid;
  font-size: 14px;
  line-height: 1.6;
}

.alert.success {
  background: #d1fae5;
  border-color: #059669;
  color: #065f46;
}

.alert.error {
  background: #fee2e2;
  border-color: #dc2626;
  color: #991b1b;
}

/* INPUTS - Ocultos mas padronizados */
input[type="text"] {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 15px;
  color: #2c3e50;
  margin-bottom: 16px;
  transition: all 0.2s;
  background: white;
  font-family: inherit;
}

input[type="text"]:focus {
  outline: none;
  border-color: #1351b4;
  box-shadow: 0 0 0 3px rgba(19, 81, 180, 0.1);
}

/* BUTTON GROUP */
.button-group {
  display: flex;
  gap: 12px;
  margin-top: 24px;
  flex-wrap: wrap;
}

/* BUTTONS - Padrão Institucional */
button {
  flex: 1;
  min-width: 150px;
  padding: 14px 24px;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  text-transform: uppercase;
  letter-spacing: 0.3px;
  transition: all 0.3s ease;
  font-family: inherit;
}

.btn-primary {
  background: #1351b4;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #0d3f8f;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(19, 81, 180, 0.3);
}

.btn-primary:disabled {
  background: #94a3b8;
  cursor: not-allowed;
  transform: none;
}

.btn-success {
  background: #059669;
  color: white;
}

.btn-success:hover:not(:disabled) {
  background: #047857;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
}

.btn-success:disabled {
  background: #94a3b8;
  cursor: not-allowed;
  transform: none;
}

.btn-secondary {
  background: #f1f5f9;
  color: #334155;
}

.btn-secondary:hover {
  background: #e2e8f0;
}

/* CAPTURED IMAGE */
.captured-image {
  margin-top: 24px;
  border-radius: 12px;
  display: none;
  border: 2px solid #e2e8f0;
  padding: 20px;
  background: #f8fafc;
}

.captured-image h3 {
  margin-bottom: 16px;
  color: #1a365d;
  font-size: 18px;
  font-weight: 600;
}

.captured-image img {
  width: 100%;
  border-radius: 8px;
  border: 2px solid #e2e8f0;
}

/* RESPONSIVIDADE */
@media (max-width: 768px) {
  .container {
    padding: 24px 16px;
  }

  .content {
    padding: 24px;
  }

  .header {
    padding: 24px;
  }

  .header h1 {
    font-size: 24px;
  }

  .button-group {
    flex-direction: column;
  }

  button {
    width: 100%;
    min-width: 100%;
  }

  .video-container {
    height: 300px;
  }
}

@media (max-width: 480px) {
  .container {
    padding: 16px;
  }

  .content {
    padding: 20px;
  }

  .header {
    padding: 20px;
  }

  .header h1 {
    font-size: 20px;
  }

  .status {
    font-size: 12px;
    padding: 8px 16px;
  }
}
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <h1>🔐 Sistema de Cadastro Facial</h1>
      <p>Reconhecimento facial com banco de dados real</p>
      <div class="api-status" id="apiStatus">
        <span id="apiStatusText">🔄 Verificando API...</span>
      </div>
    </div>
    <div class="content">
      <div class="alert" id="alert"></div>
      <!-- <label for="nome" style="display:block;margin-bottom:6px;font-weight:600">Nome Completo:</label> -->
      <input type="text" id="nome" value="<?php echo $usuario->get('nome_usuario'); ?>" placeholder="Digite seu nome completo"
        style="display:none;width:100%;padding:10px;border-radius:8px;border:2px solid #e0e0e0;margin-bottom:15px" />
      <!-- <label for="matricula" style="display:block;margin-bottom:6px;font-weight:600">Matrícula:</label> -->
      <input type="text" id="matricula" value="<?php echo $usuario->get('matricula_usuario'); ?>" placeholder="Digite sua matrícula"
        style="display:none;width:100%;padding:10px;border-radius:8px;border:2px solid #e0e0e0;margin-bottom:15px" />
      <div class="video-container">
        <div style="position: relative; display: inline-block;">
          <video id="video" autoplay muted playsinline></video>
          <canvas id="overlay" style="position: absolute; top: 0; left: 0;"></canvas>
        </div>
        <canvas id="canvas"></canvas>
        <div class="status" id="status">Carregando modelos...</div>
      </div>
      <div class="captured-image" id="capturedImageContainer">
        <h3 style="margin-bottom:10px;color:#333">📸 Imagem Capturada:</h3>
        <img id="capturedImage" alt="Foto capturada" />
      </div>
      <div class="button-group">
        <button class="btn-primary" id="startCamera" disabled>📷 Iniciar Câmera</button>
        <button class="btn-success" id="capture" disabled>📸 Capturar</button>
        <button class="btn-primary" id="register" disabled>✅ Cadastrar</button>
        <button class="btn-secondary" id="reset">🔄 Limpar</button>
      </div>
    </div>
  </div>
  <!-- Biblioteca face-api local -->
  <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
  <!-- Script principal -->
  <script defer>
document.addEventListener('DOMContentLoaded', () => {
  let stream = null;
  let capturedImageData = null;
  let faceDescriptor = null;
  let modelsLoaded = false;

  const apiBaseUrl = 'http://localhost:5500';
  const video = document.getElementById('video');
  const canvas = document.getElementById('canvas');
  const ctx = canvas.getContext('2d');
  const overlay = document.getElementById('overlay');
  const ctxOverlay = overlay.getContext('2d');
  const alertBox = document.getElementById('alert');
  const capturedImage = document.getElementById('capturedImage');
  const capturedImageContainer = document.getElementById('capturedImageContainer');
  const apiStatus = document.getElementById('apiStatus');
  const apiStatusText = document.getElementById('apiStatusText');
  const status = document.getElementById('status');
  const startCameraBtn = document.getElementById('startCamera');
  const captureBtn = document.getElementById('capture');
  const registerBtn = document.getElementById('register');
  const resetBtn = document.getElementById('reset');

  function showAlert(msg, type = 'success') {
    alertBox.textContent = msg;
    alertBox.className = `alert ${type}`;
    alertBox.style.display = 'block';
    setTimeout(() => alertBox.style.display = 'none', 4000);
  }

  async function checkApiStatus() {
    try {
      const res = await fetch(apiBaseUrl + '/');
      if (res.ok) {
        apiStatus.className = 'api-status online';
        apiStatusText.textContent = '✅ API Online';
      } else throw new Error('Status não OK');
    } catch (e) {
      apiStatus.className = 'api-status offline';
      apiStatusText.textContent = '❌ API Offline';
      console.error('Erro na API:', e);
    }
  }

  async function loadModels() {
    try {
      await Promise.all([
        faceapi.nets.faceRecognitionNet.loadFromUri('./models'),
        faceapi.nets.faceLandmark68Net.loadFromUri('./models'),
        faceapi.nets.ssdMobilenetv1.loadFromUri('./models'),
        faceapi.nets.tinyFaceDetector.loadFromUri('./models')
      ]);
      modelsLoaded = true;
      showAlert('✅ Modelos carregados com sucesso!');
      startCameraBtn.disabled = false;
      status.textContent = 'Modelos carregados. Pronto para iniciar.';
    } catch (e) {
      console.error('Erro ao carregar modelos:', e);
      showAlert('❌ Erro ao carregar modelos. Verifique a pasta /models', 'error');
      status.textContent = 'Erro ao carregar modelos.';
    }
  }

  async function startCamera() {
    if (!modelsLoaded) return showAlert('Aguarde os modelos carregarem', 'error');
    try {
      stream = await navigator.mediaDevices.getUserMedia({ video: true });
      video.srcObject = stream;
      await new Promise(r => video.onloadedmetadata = r);
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      overlay.width = video.videoWidth;
      overlay.height = video.videoHeight;
      captureBtn.disabled = false;
      startCameraBtn.textContent = '🛑 Parar Câmera';
      startCameraBtn.onclick = stopCamera;
      detectFaceLoop();
    } catch (e) {
      showAlert('Erro ao acessar câmera: verifique permissões', 'error');
      console.error('Erro câmera:', e);
    }
  }

  function stopCamera() {
    if (stream) stream.getTracks().forEach(t => t.stop());
    video.srcObject = null;
    captureBtn.disabled = true;
    startCameraBtn.textContent = '📷 Iniciar Câmera';
    startCameraBtn.onclick = startCamera;
    ctxOverlay.clearRect(0, 0, overlay.width, overlay.height);
  }

  async function detectFaceLoop() {
    const displaySize = { width: video.videoWidth, height: video.videoHeight };
    faceapi.matchDimensions(overlay, displaySize);

    async function loop() {
      if (video.paused || video.ended) return;
      const detections = await faceapi
        .detectAllFaces(video, new faceapi.TinyFaceDetectorOptions())
        .withFaceLandmarks();

      const resizedDetections = faceapi.resizeResults(detections, displaySize);
      ctxOverlay.clearRect(0, 0, overlay.width, overlay.height);
      faceapi.draw.drawDetections(overlay, resizedDetections);
      requestAnimationFrame(loop);
    }

    loop();
  }

  async function captureImage() {
    const nome = document.getElementById('nome').value.trim();
    const matricula = document.getElementById('matricula').value.trim();
    if (!nome || !matricula) return showAlert('Preencha nome e matrícula!', 'error');

    const detection = await faceapi
      .detectSingleFace(video, new faceapi.SsdMobilenetv1Options())
      .withFaceLandmarks()
      .withFaceDescriptor();

    if (!detection) return showAlert('Nenhum rosto detectado!', 'error');

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    capturedImageData = canvas.toDataURL('image/jpeg', 0.95);
    faceDescriptor = Array.from(detection.descriptor);

    capturedImage.src = capturedImageData;
    capturedImageContainer.style.display = 'block';

    registerBtn.disabled = false;
    stopCamera();
  }

  function gerarSenha(nome, matricula) {
    return matricula + nome.split(' ')[0].toLowerCase();
  }

  async function cadastrarUsuario() {
    const nome = document.getElementById('nome').value.trim();
    const matricula = document.getElementById('matricula').value.trim();
    if (!capturedImageData || !faceDescriptor) return showAlert('Capture o rosto antes!', 'error');

    const senha = gerarSenha(nome, matricula);
    const usuario = { nome, matricula, senha, faceDescriptor, imagem: capturedImageData };

    try {
      const res = await fetch(apiBaseUrl + '/api/cadastrar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(usuario)
      });
      const data = await res.json();

      if (res.ok) {
        showAlert('✅ Usuário cadastrado com sucesso!');
        resetForm();
        setTimeout(() => {
          window.location.replace("<?php echo '../../pages/usuarioInterface.php?msg=reconhecimentocadastrosucesso'; ?>");
        }, 2000);
      } else {
        showAlert('Erro: ' + (data.erro || 'Erro desconhecido'), 'error');
      }
    } catch (e) {
      showAlert('Erro ao conectar com o servidor.', 'error');
      console.error('Erro fetch API:', e);
    }
  }

  function resetForm() {
    document.getElementById('nome').value = '';
    document.getElementById('matricula').value = '';
    capturedImageData = null;
    faceDescriptor = null;
    capturedImageContainer.style.display = 'none';
    registerBtn.disabled = true;
    stopCamera();
  }

  startCameraBtn.onclick = startCamera;
  captureBtn.onclick = captureImage;
  registerBtn.onclick = cadastrarUsuario;
  resetBtn.onclick = resetForm;

  loadModels();
  checkApiStatus();
});
</script>


</body>

</html>