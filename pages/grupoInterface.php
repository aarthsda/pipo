<?php
  require_once __DIR__ . "/../config/auth.php";
  $usuarioId = requireAuth('usuario');

  require_once __DIR__ . "/../views/usuarioView.php";
  $usuario = new usuarioView(['id_usuario' => $usuarioId]);

  require_once __DIR__ . "/../views/grupoView.php";
  date_default_timezone_set('America/Sao_Paulo');

  $usuario = new usuarioView(['id_usuario' => $_COOKIE['user_id']]);
  $grupo = new grupoView(['id_grupo' => $usuario->get('grupo_usuario')]);
  
  // Verificar se grupo está cheio
  $cheio = false;
  $membrosGrupo = [];
  foreach ($usuario->getAll() as $user) {
      if ($user->grupo_usuario == $grupo->get('id_grupo')) {
          $membrosGrupo[] = $user;
          if ($user->id_usuario != $_COOKIE['user_id']) {
              $cheio = true;
          }
      }
  }

  $isDono = ($grupo->get('dono_grupo') == $_COOKIE['user_id']);
  $mensagemSucesso = '';
  $mensagemErro = '';

  // Processar envio de convite via email
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['invite_email'])) {
      $emailConvidado = trim($_POST['invite_email']);
      
      if (empty($emailConvidado)) {
          $mensagemErro = 'Por favor, informe um e-mail válido.';
      } else {
          // Buscar usuário por email
          $usuarioEncontrado = null;
          foreach ($usuario->getAll() as $user) {
              if (strtolower($user->email_usuario) === strtolower($emailConvidado)) {
                  $usuarioEncontrado = $user;
                  break;
              }
          }
          
          if (!$usuarioEncontrado) {
              $mensagemErro = 'Usuário com o e-mail "' . htmlspecialchars($emailConvidado) . '" não foi encontrado no sistema.';
          } elseif ($usuarioEncontrado->id_usuario == $_COOKIE['user_id']) {
              $mensagemErro = 'Você não pode convidar a si mesmo.';
          } elseif ($usuarioEncontrado->grupo_usuario == $grupo->get('id_grupo')) {
              $mensagemErro = 'Este usuário já está no grupo.';
          } elseif ($cheio) {
              $mensagemErro = 'O grupo já está cheio! Não será possível convidar outro usuário.';
          } else {
              // Enviar convite (lógica comentada mantida)
              $userFrom = new usuarioView(['id_usuario' => $_COOKIE['user_id']]);
              
              require_once __DIR__ . "/../config/phpmail.php";
              $mail->setFrom('pipo.atendimento@gmail.com', 'Pipo - Atendimento');
              $mail->addAddress($usuarioEncontrado->email_usuario, $usuarioEncontrado->nome_usuario);
              $mail->isHTML(true);
              $mail->Subject = 'Convite para grupo - Pipo';
              $mail->Body = '<h3>Olá, ' . $usuarioEncontrado->nome_usuario . '</h3>...';
              $mail->send();
              
              $mensagemSucesso = 'Convite enviado com sucesso para ' . htmlspecialchars($usuarioEncontrado->nome_usuario) . ' (' . htmlspecialchars($emailConvidado) . ').';
          }
      }
  }

  // Processar remoção de membro
  if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['id']) && $isDono) {
      $idRemover = $_GET['id'];
      // Lógica de remoção será tratada por removeUsuario.php
      header("Location: removeUsuario.php?id=" . $idRemover);
      exit();
  }

  // Processar promoção de membro
  if (isset($_GET['action']) && $_GET['action'] === 'promote' && isset($_GET['id']) && $isDono) {
      $idPromover = $_GET['id'];
      header("Location: promoteUsuario.php?id=" . $idPromover);
      exit();
  }

  // Processar saída do grupo
  if (isset($_GET['action']) && $_GET['action'] === 'leave') {
      header("Location: sairGrupo.php");
      exit();
  }

  // Mensagens via GET
  if (!empty($_GET['msg'])) {
      switch ($_GET['msg']) {
          case 'grupocheio':
              $mensagemErro = 'O grupo já está cheio! Não será possível convidar outro usuário.';
              break;
          case 'promocaosucesso':
              $mensagemSucesso = 'Cargo de dono do grupo alterado com sucesso!';
              break;
          case 'remocaosucesso':
              $mensagemSucesso = 'Remoção de usuário do grupo realizada com sucesso!';
              break;
          case 'saiugrupo':
              $mensagemSucesso = 'Você saiu do grupo!';
              break;
      }
  }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerenciar Grupo - Pipo</title>
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

    .back-button {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 10px 16px;
      background: #f1f5f9;
      border: none;
      border-radius: 8px;
      color: #334155;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.2s;
    }

    .back-button:hover {
      background: #e2e8f0;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 40px 20px;
    }

    .page-header {
      margin-bottom: 32px;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      flex-wrap: wrap;
      gap: 16px;
    }

    .page-header-content {
      flex: 1;
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

    .btn {
      padding: 12px 20px;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      text-decoration: none;
    }

    .btn-primary {
      background: #1351b4;
      color: white;
    }

    .btn-primary:hover {
      background: #0d3f8f;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(19,81,180,0.3);
    }

    .btn-primary:disabled {
      background: #94a3b8;
      cursor: not-allowed;
      transform: none;
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

    .group-card {
      background: white;
      border-radius: 12px;
      padding: 32px;
      border: 2px solid #e2e8f0;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      margin-bottom: 24px;
    }

    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
      padding-bottom: 16px;
      border-bottom: 2px solid #f1f5f9;
    }

    .card-title {
      font-size: 20px;
      font-weight: 600;
      color: #1a365d;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .card-icon {
      width: 32px;
      height: 32px;
      background: #e0e7ff;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #1351b4;
    }

    .members-count {
      font-size: 14px;
      color: #64748b;
      padding: 6px 12px;
      background: #f1f5f9;
      border-radius: 6px;
      font-weight: 600;
    }

    .info-banner {
      background: #f0f9ff;
      border: 1px solid #bae6fd;
      border-radius: 8px;
      padding: 16px 20px;
      margin-bottom: 24px;
      display: flex;
      align-items: flex-start;
      gap: 12px;
    }

    .info-icon {
      flex-shrink: 0;
      color: #0284c7;
      margin-top: 2px;
    }

    .info-content {
      flex: 1;
    }

    .info-title {
      font-size: 14px;
      font-weight: 600;
      color: #075985;
      margin-bottom: 4px;
    }

    .info-text {
      font-size: 13px;
      color: #0c4a6e;
      line-height: 1.6;
    }

    .success-alert {
      display: none;
      padding: 16px 20px;
      background: #d1fae5;
      border: 2px solid #6ee7b7;
      border-radius: 8px;
      color: #065f46;
      margin-bottom: 24px;
      animation: slideDown 0.3s ease-out;
    }

    .error-alert {
      display: none;
      padding: 16px 20px;
      background: #fee2e2;
      border: 2px solid #fca5a5;
      border-radius: 8px;
      color: #991b1b;
      margin-bottom: 24px;
      animation: slideDown 0.3s ease-out;
    }

    .success-alert.show,
    .error-alert.show {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .alert-icon {
      flex-shrink: 0;
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

    .table-container {
      overflow-x: auto;
    }

    .members-table {
      width: 100%;
      border-collapse: collapse;
    }

    .members-table thead {
      background: #f8fafc;
    }

    .members-table th {
      text-align: left;
      padding: 12px 16px;
      font-size: 13px;
      font-weight: 600;
      color: #475569;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border-bottom: 2px solid #e2e8f0;
    }

    .members-table td {
      padding: 16px;
      border-bottom: 1px solid #f1f5f9;
      font-size: 14px;
      color: #334155;
    }

    .members-table tbody tr:hover {
      background: #f8fafc;
    }

    .member-name {
      font-weight: 600;
      color: #1a365d;
    }

    .member-role {
      display: inline-flex;
      align-items: center;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 12px;
      font-weight: 600;
    }

    .role-owner {
      background: #fef3c7;
      color: #d97706;
    }

    .role-member {
      background: #e0e7ff;
      color: #1351b4;
    }

    .action-button {
      padding: 8px 14px;
      border: none;
      border-radius: 6px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      margin-right: 8px;
    }

    .btn-remove {
      background: #fee2e2;
      color: #dc2626;
    }

    .btn-remove:hover {
      background: #fecaca;
    }

    .btn-promote {
      background: #fef3c7;
      color: #d97706;
    }

    .btn-promote:hover {
      background: #fde68a;
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

    .form-group {
      margin-bottom: 20px;
    }

    .form-label {
      display: block;
      font-size: 14px;
      font-weight: 600;
      color: #334155;
      margin-bottom: 8px;
    }

    .form-input {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid #e2e8f0;
      border-radius: 8px;
      font-size: 15px;
      color: #2c3e50;
      transition: all 0.2s;
      background: white;
    }

    .form-input:focus {
      outline: none;
      border-color: #1351b4;
      box-shadow: 0 0 0 3px rgba(19, 81, 180, 0.1);
    }

    .modal-actions {
      display: flex;
      gap: 12px;
      margin-top: 24px;
    }

    .group-actions {
      display: flex;
      gap: 12px;
      margin-top: 24px;
      padding-top: 24px;
      border-top: 2px solid #f1f5f9;
    }

    @media (max-width: 768px) {
      .container {
        padding: 24px 16px;
      }

      .page-header {
        flex-direction: column;
      }

      .group-card {
        padding: 20px;
      }

      .card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
      }

      .page-title {
        font-size: 24px;
      }

      .modal-content {
        padding: 24px;
      }

      .group-actions {
        flex-direction: column;
      }

      .btn {
        width: 100%;
        justify-content: center;
      }
    }
  </style>
</head>
<body>
  <header class="header">
    <div class="header-content">
      <h1 class="header-title">Sistema de Restaurante Institucional - Pipo</h1>
      <a href="usuarioInterface.php" class="back-button">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="19" y1="12" x2="5" y2="12"/>
          <polyline points="12 19 5 12 12 5"/>
        </svg>
        Voltar ao Hub
      </a>
    </div>
  </header>

  <main class="container">
    <div class="page-header">
      <div class="page-header-content">
        <h2 class="page-title">Gerenciar Grupo</h2>
        <p class="page-subtitle">Administre os membros do seu grupo de reservas</p>
      </div>
      <?php if ($isDono && !$cheio): ?>
      <button class="btn btn-primary" onclick="openInviteModal()">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
          <line x1="20" y1="8" x2="20" y2="14"/>
          <line x1="23" y1="11" x2="17" y2="11"/>
        </svg>
        Convidar Usuário
      </button>
      <?php elseif ($isDono && $cheio): ?>
      <button class="btn btn-primary" disabled title="O grupo já está cheio">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
          <circle cx="12" cy="7" r="4"/>
          <line x1="20" y1="8" x2="20" y2="14"/>
          <line x1="23" y1="11" x2="17" y2="11"/>
        </svg>
        Grupo Cheio
      </button>
      <?php endif; ?>
    </div>

    <?php if ($mensagemSucesso): ?>
    <div class="success-alert show">
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

    <?php if ($mensagemErro): ?>
    <div class="error-alert show">
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

    <div class="info-banner">
      <div class="info-icon">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="16" x2="12" y2="12"/>
          <line x1="12" y1="8" x2="12.01" y2="8"/>
        </svg>
      </div>
      <div class="info-content">
        <div class="info-title">Informação importante</div>
        <div class="info-text">
          Apenas o proprietário do grupo pode convidar novos membros ou remover participantes. Membros do grupo podem fazer reservas conjuntas.
        </div>
      </div>
    </div>

    <div class="group-card">
      <div class="card-header">
        <div class="card-title">
          <div class="card-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </div>
          Membros do Grupo
        </div>
        <span class="members-count"><?php echo count($membrosGrupo); ?> <?php echo count($membrosGrupo) === 1 ? 'membro' : 'membros'; ?></span>
      </div>

      <div class="table-container">
        <table class="members-table">
          <thead>
            <tr>
              <th>Nome</th>
              <th>E-mail</th>
              <th>Função</th>
              <?php if ($isDono): ?>
              <th>Ações</th>
              <?php endif; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($membrosGrupo as $membro): ?>
            <tr>
              <td class="member-name"><?php echo htmlspecialchars($membro->nome_usuario); ?></td>
              <td><?php echo htmlspecialchars($membro->email_usuario); ?></td>
              <td>
                <span class="member-role <?php echo $membro->id_usuario == $grupo->get('dono_grupo') ? 'role-owner' : 'role-member'; ?>">
                  <?php echo $membro->id_usuario == $grupo->get('dono_grupo') ? 'Proprietário' : 'Membro'; ?>
                </span>
              </td>
              <?php if ($isDono): ?>
              <td>
                <?php if ($membro->id_usuario != $_COOKIE['user_id']): ?>
                <button class="action-button btn-promote" onclick="window.location.href='?action=promote&id=<?php echo $membro->id_usuario; ?>'">
                  <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                    <path d="M2 17l10 5 10-5"/>
                    <path d="M2 12l10 5 10-5"/>
                  </svg>
                  Promover
                </button>
                <button class="action-button btn-remove" onclick="window.location.href='?action=remove&id=<?php echo $membro->id_usuario; ?>'">
                  <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                  </svg>
                  Remover
                </button>
                <?php endif; ?>
              </td>
              <?php endif; ?>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <?php if (count($membrosGrupo) > 1 && !$isDono): ?>
      <div class="group-actions">
        <button class="btn btn-danger" onclick="window.location.href='?action=leave'">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <polyline points="16 17 21 12 16 7"/>
            <line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
          Sair do Grupo
        </button>
      </div>
      <?php endif; ?>
    </div>
  </main>

  <!-- Modal de Convite -->
  <div id="inviteModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Convidar Usuário</h3>
        <p class="modal-subtitle">Adicione um novo membro ao seu grupo</p>
      </div>
      <form method="POST" action="">
        <div class="form-group">
          <label class="form-label" for="invite_email">E-mail do usuário</label>
          <input 
            type="email" 
            id="invite_email" 
            name="invite_email"
            class="form-input" 
            placeholder="usuario@instituicao.br"
            required
          >
        </div>
        <div class="modal-actions">
          <button type="submit" class="btn btn-primary" style="flex: 1;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 2L11 13"/>
              <path d="M22 2l-7 20-4-9-9-4 20-7z"/>
            </svg>
            Enviar Convite
          </button>
          <button type="button" class="btn btn-secondary" onclick="closeInviteModal()" style="flex: 1;">
            Cancelar
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openInviteModal() {
      document.getElementById('inviteModal').classList.add('show');
    }

    function closeInviteModal() {
      document.getElementById('inviteModal').classList.remove('show');
      document.getElementById('invite_email').value = '';
    }

    // Fechar modal ao clicar fora
    document.getElementById('inviteModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeInviteModal();
      }
    });

    // Auto-remover alertas após 5 segundos
    setTimeout(function() {
      const alerts = document.querySelectorAll('.success-alert.show, .error-alert.show');
      alerts.forEach(alert => alert.classList.remove('show'));
    }, 5000);
  </script>
</body>
</html>