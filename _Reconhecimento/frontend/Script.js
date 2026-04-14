// ------- Estado -------
let stream = null;
let capturedImageData = null;
let faceDescriptor = null;
let modelsLoaded = false;

const apiBaseUrl = '/api'; // simples, mesmo host

// ------- DOM -------
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const ctx = canvas.getContext('2d');
const overlay = document.getElementById('overlay');
const ctxOverlay = overlay.getContext('2d');

const status = document.getElementById('status');
const startCameraBtn = document.getElementById('startCamera');
const captureBtn = document.getElementById('capture');
const registerBtn = document.getElementById('register');
const resetBtn = document.getElementById('reset');
const alertBox = document.getElementById('alert');
const capturedImage = document.getElementById('capturedImage');
const capturedImageContainer = document.getElementById('capturedImageContainer');

// ------- Funções -------
function showAlert(msg, type='success') {
    alertBox.textContent = msg;
    alertBox.style.display = 'block';
    alertBox.style.color = type === 'error' ? 'red' : 'green';
    setTimeout(() => alertBox.style.display='none', 4000);
}

async function loadModels() {
    const MODEL_URL = 'https://justadudewhohacks.github.io/face-api.js/models';
    await faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL);
    await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
    await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
    modelsLoaded = true;
    showAlert('Modelos carregados!', 'success');
}

async function startCamera() {
    if (!modelsLoaded) { showAlert('Carregue os modelos primeiro!', 'error'); return; }
    stream = await navigator.mediaDevices.getUserMedia({ video: true });
    video.srcObject = stream;
    await new Promise(r=>video.onloadedmetadata=r);
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    overlay.width = video.videoWidth;
    overlay.height = video.videoHeight;
    captureBtn.disabled = false;
    startCameraBtn.textContent = '🛑 Parar Câmera';
    startCameraBtn.onclick = stopCamera;
    detectFace();
}

function stopCamera() {
    if (stream) stream.getTracks().forEach(t=>t.stop());
    video.srcObject = null;
    stream = null;
    captureBtn.disabled = true;
    startCameraBtn.textContent = '📷 Iniciar Câmera';
    startCameraBtn.onclick = startCamera;
    ctxOverlay.clearRect(0,0,overlay.width,overlay.height);
}

async function detectFace() {
    if (!video.srcObject) return;
    const detections = await faceapi.detectAllFaces(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks();
    ctxOverlay.clearRect(0,0,overlay.width,overlay.height);
    if (detections.length) faceapi.draw.drawDetections(overlay, detections);
    requestAnimationFrame(detectFace);
}

async function captureImage() {
    const nome = document.getElementById('nome').value.trim();
    const matricula = document.getElementById('matricula').value.trim();
    if (!nome || !matricula) { showAlert('Preencha nome e matrícula!', 'error'); return; }

    const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();
    if (!detection) { showAlert('Nenhum rosto detectado!', 'error'); return; }

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0,0,canvas.width,canvas.height);
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
    if (!capturedImageData || !faceDescriptor) { showAlert('Capture o rosto primeiro!', 'error'); return; }
    const senha = gerarSenha(nome, matricula);

    const usuario = { nome, matricula, senha, faceDescriptor, imagem: capturedImageData };
    try {
        const res = await fetch(`${apiBaseUrl}/cadastrar`, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(usuario) });
        const data = await res.json();
        if (res.ok) { showAlert('Cadastro feito! Senha: '+senha, 'success'); resetForm(); }
        else showAlert(data.erro || 'Erro no cadastro', 'error');
    } catch(e) { showAlert('Erro de conexão!', 'error'); }
}

function resetForm() {
    document.getElementById('nome').value='';
    document.getElementById('matricula').value='';
    capturedImageData=null;
    faceDescriptor=null;
    capturedImageContainer.style.display='none';
    registerBtn.disabled=true;
    stopCamera();
}

// ------- Eventos -------
startCameraBtn.onclick = startCamera;
captureBtn.onclick = captureImage;
registerBtn.onclick = cadastrarUsuario;
resetBtn.onclick = resetForm;

// Carrega modelos na inicialização
loadModels();
