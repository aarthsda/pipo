<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reconhecimento Facial em Tempo Real</title>

  <style>
    /* RESET */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
      background: #f5f7fa;
      min-height: 100vh;
      color: #2c3e50;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    header {
      background: white;
      border-bottom: 2px solid #e2e8f0;
      width: 100%;
      padding: 30px 20px;
      text-align: center;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    h1 {
      font-size: 28px;
      font-weight: 700;
      color: #1a365d;
    }

    .video-wrapper {
      position: relative;
      margin-top: 40px;
      width: 640px;
      max-width: 90%;
      background: white;
      border-radius: 12px;
      padding: 32px;
      border: 2px solid #e2e8f0;
    }

    /* VÍDEO */
    video {
      width: 100%;
      border-radius: 12px;
      border: 2px solid #e2e8f0;
      background: #000;
      position: relative;
      z-index: 1;
    }

    /* CANVAS (SOMENTE DESENHO) */
    canvas {
      width: 100%;
      position: absolute;
      top: 32px;
      left: 32px;
      pointer-events: none;
      background: transparent; /* 🔑 CORREÇÃO PRINCIPAL */
      z-index: 2;
    }

    .info {
      margin-top: 24px;
      font-size: 15px;
      background: #f8fafc;
      padding: 16px 24px;
      border-radius: 8px;
      border: 2px solid #e2e8f0;
      font-weight: 600;
      text-align: center;
    }

    .status {
      margin-top: 16px;
      font-weight: 600;
      font-size: 14px;
      text-align: center;
      padding: 12px 20px;
      background: #e0e7ff;
      border-radius: 8px;
      border: 1px solid #c7d2fe;
    }

    form {
      display: none;
    }
  </style>
</head>

<body>

<header>
  <h1>🔍 Reconhecimento Facial em Tempo Real</h1>
</header>

<div class="video-wrapper">
  <video id="video" autoplay muted playsinline width="640" height="480"></video>
  <canvas id="overlay" width="640" height="480"></canvas>

  <div class="info" id="info">Aguardando rosto...</div>
  <div class="status" id="status">Carregando modelos...</div>
</div>

<form action="http://localhost/pipo/pages/confirmarAlmoco.php" id="postForm" method="post">
  <input type="text" name="matricula_usuario" id="matricula_usuario">
</form>

<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<script>
  const apiBaseUrl = 'http://localhost:5500';

  const video = document.getElementById('video');
  const canvas = document.getElementById('overlay');
  const ctx = canvas.getContext('2d');
  const info = document.getElementById('info');
  const status = document.getElementById('status');

  let lastDescriptor = null;

  async function loadModels() {
    await Promise.all([
      faceapi.nets.faceRecognitionNet.loadFromUri('./models'),
      faceapi.nets.faceLandmark68Net.loadFromUri('./models'),
      faceapi.nets.ssdMobilenetv1.loadFromUri('./models')
    ]);

    status.textContent = '✅ Modelos carregados. Iniciando câmera...';
    startCamera();
  }

  async function startCamera() {
    const stream = await navigator.mediaDevices.getUserMedia({
      video: {
        width: { ideal: 640 },
        height: { ideal: 480 },
        facingMode: "user"
      }
    });

    video.srcObject = stream;
    await video.play();
    recognizeLoop();
  }

  async function recognizeLoop() {
    const detection = await faceapi
      .detectSingleFace(video, new faceapi.SsdMobilenetv1Options())
      .withFaceLandmarks()
      .withFaceDescriptor();

    ctx.clearRect(0, 0, canvas.width, canvas.height);

    if (detection) {
      const { box } = detection.detection;
      const descriptor = Array.from(detection.descriptor);

      ctx.strokeStyle = '#00FF00';
      ctx.lineWidth = 2;
      ctx.strokeRect(box.x, box.y, box.width, box.height);

      if (!lastDescriptor || calcularDistancia(descriptor, lastDescriptor) > 0.05) {
        lastDescriptor = descriptor;
        reconhecerUsuario(descriptor);
      }
    } else {
      info.textContent = 'Nenhum rosto detectado...';
    }

    requestAnimationFrame(recognizeLoop);
  }

  async function reconhecerUsuario(faceDescriptor) {
    const res = await fetch(apiBaseUrl + '/api/reconhecer', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ faceDescriptor })
    });

    const data = await res.json();

    if (document.getElementById('matricula_usuario').value == '') {
      if (data.reconhecido) {
        info.textContent = '✅ Rosto reconhecido';
        document.getElementById('matricula_usuario').value = data.usuario.matricula;
  
        setTimeout(() => {
          document.getElementById('postForm').submit();
        }, 3000);
      } else {
        info.textContent = '❌ Rosto não reconhecido';
      }
    }
  }

  function calcularDistancia(a, b) {
    let soma = 0;
    for (let i = 0; i < a.length; i++) {
      soma += (a[i] - b[i]) ** 2;
    }
    return Math.sqrt(soma);
  }

  loadModels();
</script>

</body>
</html>
