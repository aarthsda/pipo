<?php
  // ========== VALIDAÇÃO DE SESSÃO ==========
  require_once __DIR__ . "/../config/auth.php";

  $auth = $_SESSION['auth'] ?? null;
  if (!$auth || empty($auth['type']) || empty($auth['id'])) {
    header("Location: hub.php?msg=acessoproibido");
    exit();
  }

  if ($auth['type'] === 'admin') {
    $id = $_GET['id'];
    $submitValue = 'Voltar à lista de Usuários';
    require_once __DIR__ . "/../views/adminView.php";
    $admin = new adminView(['id_admin' => (int) $auth['id']]);
  }
  else if ($auth['type'] === 'usuario') {
    $id = (int) $auth['id'];
    $submitValue = 'Voltar ao Hub';
    require_once __DIR__ . "/../views/usuarioView.php";
    $usuario = new usuarioView(['id_usuario' => (int) $auth['id']]);
  }
  else {
    header("Location: hub.php?msg=acessoproibido");
    exit();
  }

  // ========== CARREGAR DADOS DO USUÁRIO ==========
  require_once __DIR__ . "/../views/usuarioView.php";
  $view = new usuarioView(['id_usuario' => $id]);
  $attr = $view->getAtributtes();

  // Verificar se há mensagem de sucesso
  $showSuccess = isset($_GET['msg']) && $_GET['msg'] == 'success';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Perfil - Sistema de Restaurante</title>
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
      max-width: 900px;
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
      max-width: 900px;
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

    .profile-card {
      background: white;
      border-radius: 12px;
      padding: 40px;
      border: 2px solid #e2e8f0;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .profile-header {
      display: flex;
      align-items: center;
      gap: 24px;
      margin-bottom: 40px;
      padding-bottom: 32px;
      border-bottom: 2px solid #f1f5f9;
    }

    .profile-avatar {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 32px;
      font-weight: 700;
      flex-shrink: 0;
    }

    .profile-info {
      flex: 1;
    }

    .profile-name {
      font-size: 24px;
      font-weight: 700;
      color: #1a365d;
      margin-bottom: 4px;
    }

    .profile-role {
      font-size: 14px;
      color: #64748b;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .role-badge {
      display: inline-flex;
      align-items: center;
      padding: 4px 10px;
      background: #e0e7ff;
      color: #1351b4;
      border-radius: 6px;
      font-size: 12px;
      font-weight: 600;
      margin-top: 8px;
    }

    .form-section {
      margin-bottom: 32px;
    }

    .section-title {
      font-size: 18px;
      font-weight: 600;
      color: #1a365d;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .section-icon {
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #1351b4;
    }

    .form-group {
      margin-bottom: 24px;
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

    .form-input:disabled {
      background: #f8fafc;
      color: #94a3b8;
      cursor: not-allowed;
    }

    .form-hint {
      font-size: 13px;
      color: #64748b;
      margin-top: 6px;
      display: flex;
      align-items: flex-start;
      gap: 6px;
    }

    .password-toggle {
      position: relative;
    }

    .toggle-button {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #64748b;
      cursor: pointer;
      padding: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .toggle-button:hover {
      color: #1351b4;
    }

    .form-actions {
      display: flex;
      gap: 12px;
      margin-top: 32px;
      padding-top: 32px;
      border-top: 2px solid #f1f5f9;
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
    }

    .btn-primary {
      background: #1351b4;
      color: white;
      flex: 1;
    }

    .btn-primary:hover {
      background: #0d3f8f;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(19,81,180,0.3);
    }

    .btn-secondary {
      background: #f1f5f9;
      color: #334155;
      flex: 1;
    }

    .btn-secondary:hover {
      background: #e2e8f0;
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

    .success-alert.show {
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

    .info-box {
      background: #f0f9ff;
      border: 1px solid #bae6fd;
      border-radius: 8px;
      padding: 16px;
      margin-bottom: 24px;
    }

    .info-box-title {
      font-size: 14px;
      font-weight: 600;
      color: #075985;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .info-box-text {
      font-size: 13px;
      color: #0c4a6e;
      line-height: 1.6;
    }

    @media (max-width: 768px) {
      .container {
        padding: 24px 16px;
      }

      .profile-card {
        padding: 24px;
      }

      .profile-header {
        flex-direction: column;
        text-align: center;
      }

      .page-title {
        font-size: 24px;
      }

      .form-actions {
        flex-direction: column;
      }

      .btn {
        width: 100%;
      }
    }

    @media (max-width: 480px) {
      .profile-avatar {
        width: 64px;
        height: 64px;
        font-size: 24px;
      }

      .profile-name {
        font-size: 20px;
      }
    }
  </style>
</head>
<body>
  <header class="header">
    <div class="header-content">
      <h1 class="header-title">Sistema de Restaurante Institucional</h1>
      <a href="<?php echo $_COOKIE['user_type'] == 'admin' ? 'listUsuario.php' : 'hub.php'; ?>" class="back-button">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="19" y1="12" x2="5" y2="12"/>
          <polyline points="12 19 5 12 12 5"/>
        </svg>
        Voltar
      </a>
    </div>
  </header>

  <main class="container">
    <div class="page-header">
      <h2 class="page-title">Editar Perfil</h2>
      <p class="page-subtitle">Atualize suas informações pessoais e credenciais de acesso</p>
    </div>

    <div class="profile-card">
      <!-- Alerta de Sucesso -->
      <div id="successAlert" class="success-alert <?php echo $showSuccess ? 'show' : ''; ?>">
        <div class="alert-icon">
          <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
          </svg>
        </div>
        <div class="alert-content">
          <div class="alert-title">Perfil atualizado com sucesso</div>
          <div class="alert-text">Suas informações foram salvas e já estão disponíveis no sistema.</div>
        </div>
      </div>

      <!-- Cabeçalho do Perfil -->
      <div class="profile-header">
        <div class="profile-avatar" id="profileAvatar">
          <?php echo strtoupper(substr($attr['nome_usuario'], 0, 1)); ?>
        </div>
        <div class="profile-info">
          <h3 class="profile-name" id="profileName"><?php echo htmlspecialchars($attr['nome_usuario']); ?></h3>
          <p class="profile-role">Membro desde <span id="memberSince">Janeiro de 2026</span></p>
          <span class="role-badge">
            <?php echo $attr['categoria_usuario'] == 'prioritario' ? 'Usuário Prioritário' : 'Usuário Ativo'; ?>
          </span>
        </div>
      </div>

      <!-- Formulário -->
      <form id="profileForm" action="ctrlHub.php?class=usuario&action=alter" method="post" novalidate>
        
        <!-- ID do usuário (hidden) -->
        <input type="hidden" id="id_usuario" name="id_usuario" value="<?php echo $id; ?>">

        <!-- Informações Pessoais -->
        <div class="form-section">
          <h3 class="section-title">
            <span class="section-icon">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
              </svg>
            </span>
            Informações Pessoais
          </h3>

          <?php if ($_COOKIE['user_type'] == 'admin'): ?>
          <div class="form-group">
            <label class="form-label" for="nome_usuario">Nome Completo</label>
            <input 
              type="text" 
              id="nome_usuario" 
              name="nome_usuario"
              class="form-input" 
              value="<?php echo htmlspecialchars($attr['nome_usuario']); ?>"
              placeholder="Nome completo do usuário"
              required
            >
            <p class="form-hint">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="16" x2="12" y2="12"/>
                <line x1="12" y1="8" x2="12.01" y2="8"/>
              </svg>
              Este nome será exibido nas reservas e comunicações
            </p>
          </div>
          <?php endif; ?>

          <div class="form-group">
            <label class="form-label" for="email_usuario">E-mail Institucional</label>
            <input 
              type="email" 
              id="email_usuario" 
              name="email_usuario"
              class="form-input" 
              value="<?php echo htmlspecialchars($attr['email_usuario']); ?>"
              placeholder="email@instituicao.br"
              required
            >
            <p class="form-hint">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="16" x2="12" y2="12"/>
                <line x1="12" y1="8" x2="12.01" y2="8"/>
              </svg>
              Usado para login e notificações do sistema
            </p>
          </div>

          <?php if ($_COOKIE['user_type'] == 'admin'): ?>
          <div class="form-group">
            <label class="form-label" for="matricula_usuario">Matrícula</label>
            <input 
              type="text" 
              id="matricula_usuario" 
              name="matricula_usuario"
              class="form-input" 
              value="<?php echo htmlspecialchars($attr['matricula_usuario']); ?>"
              placeholder="Matrícula institucional"
            >
          </div>

          <div class="form-group">
            <label class="form-label" for="categoria_usuario">Categoria do Usuário</label>
            <select id="categoria_usuario" name="categoria_usuario" class="form-input">
              <option value="comum" <?php echo $attr['categoria_usuario'] == 'comum' ? 'selected' : ''; ?>>Comum</option>
              <option value="prioritario" <?php echo $attr['categoria_usuario'] == 'prioritario' ? 'selected' : ''; ?>>Prioritário</option>
            </select>
          </div>
          <?php endif; ?>
        </div>

        <!-- Segurança -->
        <div class="form-section">
          <h3 class="section-title">
            <span class="section-icon">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
              </svg>
            </span>
            Senha
          </h3>

          <div class="info-box">
            <div class="info-box-title">
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="16" x2="12" y2="12"/>
                <line x1="12" y1="8" x2="12.01" y2="8"/>
              </svg>
              Requisitos de Senha
            </div>
            <p class="info-box-text">
              A senha deve ter no mínimo 8 caracteres. Deixe em branco para manter a senha atual.
            </p>
          </div>

          <div class="form-group">
            <label class="form-label" for="senha_usuario">Nova Senha (deixe em branco para não alterar)</label>
            <div class="password-toggle">
              <input 
                type="password" 
                id="senha_usuario" 
                name="senha_usuario"
                class="form-input" 
                placeholder="Digite a nova senha (opcional)"
                minlength="8"
              >
              <button type="button" class="toggle-button" onclick="togglePassword('senha_usuario')">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Ações do Formulário -->
        <div class="form-actions">
          <button type="submit" class="btn btn-primary">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
              <polyline points="17 21 17 13 7 13 7 21"/>
              <polyline points="7 3 7 8 15 8"/>
            </svg>
            Salvar Alterações
          </button>
          <a href="<?php echo $_COOKIE['user_type'] == 'admin' ? 'listUsuario.php' : 'hub.php'; ?>" class="btn btn-secondary">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="18" y1="6" x2="6" y2="18"/>
              <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
            Cancelar
          </a>
        </div>
      </form>
    </div>
  </main>

  <script>
    // Toggle de visibilidade de senha
    function togglePassword(fieldId) {
      const field = document.getElementById(fieldId);
      field.type = field.type === 'password' ? 'text' : 'password';
    }

    // Validação do formulário
    document.getElementById('profileForm').addEventListener('submit', function(e) {
      const senha = document.getElementById('senha_usuario').value;
      
      // Validar senha apenas se foi preenchida
      if (senha && senha.length < 8) {
        e.preventDefault();
        alert('A senha deve ter no mínimo 8 caracteres.');
        return false;
      }

      return true;
    });

    // Esconder alerta de sucesso após 5 segundos
    <?php if ($showSuccess): ?>
    setTimeout(function() {
      document.getElementById('successAlert').classList.remove('show');
    }, 5000);
    <?php endif; ?>
  </script>
</body>
</html>