<?php
// Verificar autenticação
if (empty($_COOKIE) || $_COOKIE['user_type'] != 'usuario') {
  header("Location: hub.php?msg=acessoproibido");
  exit();
}

require_once __DIR__ . "/../views/usuarioView.php";

$usuario = new usuarioView(['id_usuario' => $_COOKIE['user_id']]);

if (!$usuario->verifyPassword($_COOKIE['user_password'])) {
  setcookie("user_type", 'guest', time() + 3600, "/");
  setcookie("user_id", '', time() + 3600, "/");
  setcookie("user_password", '', time() + 3600, "/");

  header("Location: hub.php");
  exit();
}

// Processar logout
if (!empty($_POST) && isset($_POST['valor']) && $_POST['valor'] == 'sair') {
  setcookie("user_type", 'guest', time() + 3600, "/");
  setcookie("user_id", '', time() + 3600, "/");
  setcookie("user_password", '', time() + 3600, "/");

  header("Location: hub.php?msg=sairdaconta");
  exit();
}

// Mensagens de feedback
$mensagemSucesso = '';
$mensagemInfo = '';
$mensagemErro = '';
$tipoMensagem = '';

if (!empty($_GET['msg'])) {
  switch ($_GET['msg']) {
    case 'conviteaceitado':
      $mensagemSucesso = 'Convite aceito! Agora você faz parte de um grupo!';
      $tipoMensagem = 'success';
      break;
    case 'conviteexpirado':
      $mensagemErro = 'Convite expirado! Não será mais possível aceitar esse convite.';
      $tipoMensagem = 'error';
      break;
    case 'grupocheio':
      $mensagemErro = 'O grupo já está cheio! Não será possível convidar outro usuário.';
      $tipoMensagem = 'error';
      break;
    case 'naoedono':
      $mensagemErro = 'Impossível convidar usuários! Você não é o dono do grupo.';
      $tipoMensagem = 'error';
      break;
    case 'reconhecimentocadastrosucesso':
      $mensagemSucesso = 'Biometria cadastrada com sucesso! Agora você pode confirmar a reserva usando apenas seu rosto.';
      $tipoMensagem = 'success';

      require_once __DIR__ . "/../config/phpmail.php";

      $mail->setFrom('pipo.atendimento@gmail.com', 'Pipo - Atendimento');
      $mail->addAddress($usuario->get('email_usuario'), $usuario->get('nome_usuario'));

      $mail->isHTML(true);
      $mail->Subject = 'Biometria facial cadastrada com sucesso! - Pipo';
      $mail->Body    = '<h3>Olá, ' . $usuario->get('nome_usuario') . '</h3><p>Temos uma ótima notícia!</p>

<p>Sua <strong>biometria facial</strong> foi cadastrada com sucesso no sistema <strong>Pipo</strong>.</p>

<p>A partir de agora, você poderá <strong>confirmar suas reservas e almoços de forma muito mais rápida e prática</strong>, utilizando apenas o reconhecimento facial — sem precisar digitar senha ou realizar confirmações manuais.</p>

<p>É simples, seguro e pensado para tornar sua experiência no restaurante ainda melhor!</p>

<p>Você pode acessar sua conta no site do Pipo para visualizar ou atualizar suas informações biométricas sempre que quiser.</p>
<p>Atenciosamente,<br><strong>Equipe Pipo</strong></p>
<p><em>Esta é uma mensagem automática — por favor, não responda diretamente a este e-mail.</em></p>';

      $mail->send();
      break;
  }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel do Usuário - Sistema de Restaurante</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
      background: #f5f7fa;
      min-height: 100vh;
      line-height: 1.6;
      color: #2c3e50;
    }

    .header {
      background: white;
      border-bottom: 2px solid #e2e8f0;
      padding: 20px 0;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .header-content {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .header-title {
      font-size: 20px;
      font-weight: 600;
      color: #1a365d;
    }

    .header-user {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #e0e7ff;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #1351b4;
      font-weight: 600;
      font-size: 16px;
    }

    .user-info {
      display: flex;
      flex-direction: column;
    }

    .user-name {
      font-size: 14px;
      font-weight: 600;
      color: #1a365d;
    }

    .user-email {
      font-size: 12px;
      color: #64748b;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 40px 20px;
    }

    .page-header {
      margin-bottom: 32px;
    }

    .page-title {
      font-size: 28px;
      font-weight: 700;
      color: #1a365d;
      margin-bottom: 8px;
    }

    .page-subtitle {
      font-size: 15px;
      color: #64748b;
    }

    .alert {
      padding: 16px 20px;
      border-radius: 8px;
      margin-bottom: 24px;
      display: flex;
      align-items: flex-start;
      gap: 12px;
      animation: slideDown 0.3s ease-out;
    }

    .alert-success {
      background: #d1fae5;
      border-left: 4px solid #059669;
      color: #065f46;
    }

    .alert-error {
      background: #fee2e2;
      border-left: 4px solid #dc2626;
      color: #991b1b;
    }

    .alert-icon {
      flex-shrink: 0;
      margin-top: 2px;
    }

    .alert-content {
      flex: 1;
    }

    .alert-title {
      font-weight: 600;
      margin-bottom: 4px;
      font-size: 14px;
    }

    .alert-text {
      font-size: 14px;
      line-height: 1.5;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 24px;
      margin-bottom: 40px;
    }

    .dashboard-card {
      background: white;
      border-radius: 12px;
      padding: 28px;
      border: 2px solid #e2e8f0;
      transition: all 0.3s ease;
      cursor: pointer;
      text-decoration: none;
      display: block;
    }

    .dashboard-card:hover {
      border-color: #1351b4;
      box-shadow: 0 4px 12px rgba(19,81,180,0.12);
      transform: translateY(-2px);
    }

    .card-icon {
      width: 48px;
      height: 48px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 16px;
    }

    .card-icon.primary {
      background: #e0e7ff;
      color: #1351b4;
    }

    .card-icon.success {
      background: #d1fae5;
      color: #059669;
    }

    .card-icon.warning {
      background: #fef3c7;
      color: #d97706;
    }

    .card-icon.purple {
      background: #f3e8ff;
      color: #7c3aed;
    }

    .card-icon.red {
      background: #fee2e2;
      color: #dc2626;
    }

    .card-title {
      font-size: 18px;
      font-weight: 600;
      color: #1a365d;
      margin-bottom: 8px;
    }

    .card-description {
      font-size: 14px;
      color: #64748b;
      line-height: 1.5;
    }

    .info-section {
      background: white;
      border-radius: 12px;
      padding: 32px;
      border: 2px solid #e2e8f0;
      margin-bottom: 24px;
    }

    .info-title {
      font-size: 20px;
      font-weight: 600;
      color: #1a365d;
      margin-bottom: 16px;
      padding-bottom: 12px;
      border-bottom: 2px solid #f1f5f9;
    }

    .info-text {
      font-size: 15px;
      color: #475569;
      line-height: 1.7;
      margin-bottom: 12px;
    }

    .alert-info {
      background: #e0f2fe;
      border-left: 4px solid #0284c7;
      color: #075985;
    }

    .quick-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-top: 24px;
    }

    .stat-box {
      background: #f8fafc;
      padding: 20px;
      border-radius: 10px;
      border: 1px solid #e2e8f0;
    }

    .stat-label {
      font-size: 13px;
      color: #64748b;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 8px;
    }

    .stat-value {
      font-size: 24px;
      font-weight: 700;
      color: #1a365d;
    }

    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1000;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .modal.show {
      display: flex;
    }

    .modal-content {
      background: white;
      border-radius: 12px;
      padding: 32px;
      max-width: 500px;
      width: 100%;
      box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
    }

    .modal-header {
      margin-bottom: 20px;
    }

    .modal-title {
      font-size: 20px;
      font-weight: 700;
      color: #1a365d;
      margin-bottom: 8px;
    }

    .modal-subtitle {
      font-size: 14px;
      color: #64748b;
    }

    .modal-actions {
      display: flex;
      gap: 12px;
      margin-top: 24px;
    }

    .btn {
      padding: 14px 24px;
      border: none;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      text-decoration: none;
      flex: 1;
    }

    .btn-danger {
      background: #dc2626;
      color: white;
    }

    .btn-danger:hover {
      background: #b91c1c;
    }

    .btn-secondary {
      background: #f1f5f9;
      color: #334155;
    }

    .btn-secondary:hover {
      background: #e2e8f0;
    }

    @media (max-width: 768px) {
      .header-content {
        flex-direction: column;
        gap: 16px;
        align-items: flex-start;
      }

      .dashboard-grid {
        grid-template-columns: 1fr;
      }

      .page-title {
        font-size: 24px;
      }

      .info-section {
        padding: 24px;
      }

      .quick-stats {
        grid-template-columns: 1fr;
      }

      .user-info {
        display: none;
      }

      .modal-content {
        padding: 24px;
      }

      .modal-actions {
        flex-direction: column;
      }
    }

    @media (max-width: 480px) {
      .container {
        padding: 24px 16px;
      }

      .dashboard-card {
        padding: 20px;
      }
    }
  </style>
</head>
<body>
  <header class="header">
    <div class="header-content">
      <h1 class="header-title">Sistema de Restaurante Institucional</h1>
      <div class="header-user">
        <div class="user-avatar" id="userAvatar"><?php echo strtoupper(substr($usuario->get('nome_usuario'), 0, 1)); ?></div>
        <div class="user-info">
          <span class="user-name" id="userName"><?php echo htmlspecialchars($usuario->get('nome_usuario')); ?></span>
          <span class="user-email" id="userEmail"><?php echo htmlspecialchars($usuario->get('email_usuario')); ?></span>
        </div>
      </div>
    </div>
  </header>

  <main class="container">
    <div class="page-header">
      <h2 class="page-title">Painel do Usuário</h2>
      <p class="page-subtitle">Gerencie suas reservas, perfil e configurações do sistema</p>
    </div>

    <?php if ($tipoMensagem == 'success' && !empty($mensagemSucesso)): ?>
    <div class="alert alert-success">
      <div class="alert-icon">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
          <polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
      </div>
      <div class="alert-content">
        <div class="alert-title">Sucesso!</div>
        <div class="alert-text"><?php echo htmlspecialchars($mensagemSucesso); ?></div>
      </div>
    </div>
    <?php endif; ?>

    <?php if ($tipoMensagem == 'error' && !empty($mensagemErro)): ?>
    <div class="alert alert-error">
      <div class="alert-icon">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="8" x2="12" y2="12"/>
          <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
      </div>
      <div class="alert-content">
        <div class="alert-title">Erro</div>
        <div class="alert-text"><?php echo htmlspecialchars($mensagemErro); ?></div>
      </div>
    </div>
    <?php endif; ?>

    <div class="dashboard-grid">
      <a href="http://localhost/pipo/_Reconhecimento/frontend/index.php" class="dashboard-card">
        <div class="card-icon primary">
          <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2a10 10 0 0 1 10 10c0 4.42-2.87 8.17-6.84 9.5-.31.1-.66-.02-.84-.3l-2.32-3.63"/>
            <path d="M12 2a10 10 0 0 0-10 10c0 4.42 2.87 8.17 6.84 9.5.31.1.66-.02.84-.3l2.32-3.63"/>
            <circle cx="12" cy="12" r="3"/>
          </svg>
        </div>
        <h3 class="card-title">Cadastrar Reconhecimento Facial</h3>
        <p class="card-description">Configure o reconhecimento facial para acesso rápido ao restaurante</p>
      </a>

      <a href="alterUsuario.php" class="dashboard-card">
        <div class="card-icon success">
          <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
        </div>
        <h3 class="card-title">Editar Perfil</h3>
        <p class="card-description">Atualize suas informações pessoais, e-mail e senha de acesso</p>
      </a>

      <a href="filaInterface.php" class="dashboard-card">
        <div class="card-icon warning">
          <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
        </div>
        <h3 class="card-title">Gerenciar Reservas</h3>
        <p class="card-description">Visualize, cancele ou modifique suas reservas de refeições</p>
      </a>

      <a href="grupoInterface.php" class="dashboard-card">
        <div class="card-icon purple">
          <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
        </div>
        <h3 class="card-title">Gerenciar Grupo</h3>
        <p class="card-description">Administre os membros do seu grupo de reservas</p>
      </a>

      <a href="#" class="dashboard-card" onclick="openLogoutModal(event)">
        <div class="card-icon red">
          <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <polyline points="16 17 21 12 16 7"/>
            <line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
        </div>
        <h3 class="card-title">Sair do Sistema</h3>
        <p class="card-description">Encerrar sua sessão e retornar à página inicial</p>
      </a>
    </div>

    <div class="info-section">
      <h3 class="info-title">Bem-vindo ao Sistema</h3>
      <p class="info-text">
        Este é o painel central de gerenciamento do usuário. A partir daqui, você pode acessar todas as funcionalidades disponíveis para realizar reservas de refeições, gerenciar seu perfil e configurar suas preferências.
      </p>
      <p class="info-text">
        Utilize os cartões acima para navegar entre as diferentes seções do sistema. Todas as operações são seguras e suas informações estão protegidas conforme as políticas de privacidade institucionais.
      </p>

      <div class="alert alert-info">
        <div class="alert-icon">
          <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="16" x2="12" y2="12"/>
            <line x1="12" y1="8" x2="12.01" y2="8"/>
          </svg>
        </div>
        <div class="alert-content">
          <div class="alert-title">Dica importante</div>
          <div class="alert-text">
            Realize suas reservas com antecedência para garantir sua vaga. O sistema permite reservas com até 7 dias de antecedência.
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Modal de Confirmação de Logout -->
  <div id="logoutModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Sair do Sistema</h3>
        <p class="modal-subtitle">Tem certeza que deseja encerrar sua sessão?</p>
      </div>
      <div style="background: #f0f9ff; padding: 12px; border-radius: 6px; margin: 16px 0;">
        <p style="font-size: 13px; color: #0c4a6e; line-height: 1.6;">
          <strong>Informação:</strong> Você será desconectado do sistema e precisará fazer login novamente para acessar suas funcionalidades.
        </p>
      </div>
      <form id="logoutForm" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="hidden" name="valor" value="sair">
        <div class="modal-actions">
          <button type="submit" class="btn btn-danger">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <polyline points="16 17 21 12 16 7"/>
              <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Sim, Sair do Sistema
          </button>
          <button type="button" class="btn btn-secondary" onclick="closeLogoutModal()">
            Cancelar
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Abrir modal de logout
    function openLogoutModal(event) {
      event.preventDefault();
      document.getElementById('logoutModal').classList.add('show');
    }

    // Fechar modal de logout
    function closeLogoutModal() {
      document.getElementById('logoutModal').classList.remove('show');
    }

    // Fechar modal ao clicar fora
    document.getElementById('logoutModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeLogoutModal();
      }
    });

    <?php if ($tipoMensagem): ?>
    // Auto-redirecionar após 5 segundos se houver mensagem
    setTimeout(function() {
      window.location.href = 'usuarioInterface.php';
    }, 5000);
    <?php endif; ?>
  </script>
</body>
</html>