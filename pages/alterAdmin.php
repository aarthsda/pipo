<?php
  require_once __DIR__ . "/../config/auth.php";
  $adminId = requireAuth('admin');

  require_once __DIR__ . "/../views/adminView.php";

  // Buscar dados do admin logado
  $admin = new adminView(['id_admin' => $adminId]);

  // Pegar atributos do admin
  $attr = $admin->getAtributtes();
  $primeiraLetra = strtoupper(substr($attr['nome_admin'], 0, 1));
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Perfil do Administrador - Sistema de Restaurante">
  <title>Meu Perfil - Sistema de Restaurante</title>
  
  <!-- CSS -->
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/pages/profile.css">
</head>
<body>

  <div class="main-layout">

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <div class="sidebar-logo">
          <div class="sidebar-logo-icon">
            <img src="assets/icons/logo-pipo.svg" alt="Sistema Pipo" />
          </div>
          <div class="sidebar-logo-text">
            <h2 class="sidebar-logo-title">Sistema Pipo</h2>
            <p class="sidebar-logo-subtitle">Administração</p>
          </div>
        </div>
      </div>

      <nav class="sidebar-nav">
        <ul class="sidebar-menu">
          <li class="sidebar-menu-item">
            <a href="adminInterface.php" class="sidebar-menu-link">
              <span class="sidebar-menu-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect width="7" height="9" x="3" y="3" rx="1"/>
                  <rect width="7" height="5" x="14" y="3" rx="1"/>
                  <rect width="7" height="9" x="14" y="12" rx="1"/>
                  <rect width="7" height="5" x="3" y="16" rx="1"/>
                </svg>
              </span>
              <span class="sidebar-menu-text">Dashboard</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="listPrato.php" class="sidebar-menu-link">
              <span class="sidebar-menu-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/>
                  <path d="M7 2v20"/>
                  <path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/>
                </svg>
              </span>
              <span class="sidebar-menu-text">Pratos</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="listCardapio.php" class="sidebar-menu-link">
              <span class="sidebar-menu-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                  <polyline points="14 2 14 8 20 8"/>
                  <line x1="16" x2="8" y1="13" y2="13"/>
                  <line x1="16" x2="8" y1="17" y2="17"/>
                </svg>
              </span>
              <span class="sidebar-menu-text">Cardápios</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="listFila.php" class="sidebar-menu-link">
              <span class="sidebar-menu-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"/>
                  <polyline points="12 6 12 12 16 14"/>
                </svg>
              </span>
              <span class="sidebar-menu-text">Filas</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="listUsuario.php" class="sidebar-menu-link">
              <span class="sidebar-menu-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                  <circle cx="9" cy="7" r="4"/>
                  <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
              </span>
              <span class="sidebar-menu-text">Acessos</span>
            </a>
          </li>
          <li class="sidebar-menu-item">
            <a href="alterAdmin.php" class="sidebar-menu-link active">
              <span class="sidebar-menu-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                  <circle cx="12" cy="7" r="4"/>
                </svg>
              </span>
              <span class="sidebar-menu-text">Perfil</span>
            </a>
          </li>
        </ul>
      </nav>

      <div class="sidebar-footer">
        <div class="sidebar-user">
          <div class="sidebar-user-avatar"><?php echo $primeiraLetra; ?></div>
          <div class="sidebar-user-info">
            <p class="sidebar-user-name"><?php echo htmlspecialchars($attr['nome_admin']); ?></p>
            <p class="sidebar-user-role">Administrador</p>
          </div>
        </div>
        <button class="sidebar-logout" onclick="window.location.href='adminInterface.php'">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
            <polyline points="16 17 21 12 16 7"/>
            <line x1="21" x2="9" y1="12" y2="12"/>
          </svg>
          <span>Sair</span>
        </button>
      </div>
    </aside>

    <button class="sidebar-toggle" id="sidebarToggle">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="4" x2="20" y1="12" y2="12"/>
        <line x1="4" x2="20" y1="6" y2="6"/>
        <line x1="4" x2="20" y1="18" y2="18"/>
      </svg>
    </button>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Conteúdo Principal -->
    <main class="main-content">

      <!-- Header -->
      <header class="page-header">
        <div>
          <h1 class="page-title">Meu Perfil</h1>
          <p class="page-subtitle">Gerencie suas informações e configurações de conta</p>
        </div>
      </header>

      <!-- Grid de Cards -->
      <div class="profile-grid">

        <!-- Card: Avatar e Informações -->
        <section class="profile-card profile-info-card">
          <div class="profile-avatar-container">
            <div class="profile-avatar"><?php echo $primeiraLetra; ?></div>
            <div class="profile-avatar-badge">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
              </svg>
            </div>
          </div>
          <h2 class="profile-name"><?php echo htmlspecialchars($attr['nome_admin']); ?></h2>
          <p class="profile-role">Administrador do Sistema</p>
          <p class="profile-email"><?php echo htmlspecialchars($attr['email_admin']); ?></p>
          <p class="text-gray-600" style="margin-top: 8px;">
            <strong>Código:</strong> <?php echo htmlspecialchars($attr['codigo_admin']); ?>
          </p>
        </section>

        <!-- Card: Informações Pessoais -->
        <section class="profile-card">
          <div class="card-header">
            <h3 class="card-title">Informações Pessoais</h3>
          </div>
          <form action="ctrlHub.php?class=admin&action=alter" method="post" id="profileForm" class="card-body" novalidate>
            
            <input type="hidden" name="id_admin" value="<?php echo $attr['id_admin']; ?>">
            
            <div class="form-group">
              <label for="codigo_admin" class="form-label form-label-required">Código</label>
              <input 
                type="text" 
                id="codigo_admin" 
                name="codigo_admin" 
                class="form-input"
                value="<?php echo htmlspecialchars($attr['codigo_admin']); ?>"
                inputmode="numeric"
                pattern="\d+"
                required
              >
              <span class="form-help">Somente números</span>
              <span id="codigoError" class="form-error"></span>
            </div>

            <div class="form-group">
              <label for="nome_admin" class="form-label form-label-required">Nome Completo</label>
              <input 
                type="text" 
                id="nome_admin" 
                name="nome_admin" 
                class="form-input"
                value="<?php echo htmlspecialchars($attr['nome_admin']); ?>"
                required
                minlength="3"
                maxlength="100"
              >
              <span id="nomeError" class="form-error"></span>
            </div>

            <div class="form-group">
              <label for="email_admin" class="form-label form-label-required">Email</label>
              <input 
                type="email" 
                id="email_admin" 
                name="email_admin" 
                class="form-input"
                value="<?php echo htmlspecialchars($attr['email_admin']); ?>"
                required
                maxlength="150"
              >
              <span id="emailError" class="form-error"></span>
            </div>

            <div class="alert alert-success hidden" id="profileSuccess">
              ✅ Informações atualizadas com sucesso!
            </div>

            <button type="submit" class="btn btn-primary btn-block" id="saveProfileBtn">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
              <span>Salvar Alterações</span>
            </button>

          </form>
        </section>

        <!-- Card: Segurança -->
        <section class="profile-card">
          <div class="card-header">
            <h3 class="card-title">Segurança da Conta</h3>
          </div>
          <form action="ctrlHub.php?class=admin&action=alter" method="post" id="passwordForm" class="card-body" novalidate>
            
            <input type="hidden" name="id_admin" value="<?php echo $attr['id_admin']; ?>">
            <input type="hidden" name="codigo_admin" value="<?php echo htmlspecialchars($attr['codigo_admin']); ?>">
            <input type="hidden" name="nome_admin" value="<?php echo htmlspecialchars($attr['nome_admin']); ?>">
            <input type="hidden" name="email_admin" value="<?php echo htmlspecialchars($attr['email_admin']); ?>">
            
            <div class="form-group">
              <label for="senha_admin" class="form-label form-label-required">Nova Senha</label>
              <div class="password-input-wrapper">
                <input 
                  type="password" 
                  id="senha_admin" 
                  name="senha_admin" 
                  class="form-input"
                  required
                  minlength="6"
                  maxlength="360"
                  placeholder="Digite uma nova senha"
                >
                <button type="button" class="password-toggle" data-target="senha_admin">
                  <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                  <svg class="eye-off-icon hidden" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                    <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                    <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                    <line x1="2" x2="22" y1="2" y2="22"/>
                  </svg>
                </button>
              </div>
              <span class="form-help">Mínimo de 6 caracteres. Deixe em branco para manter a senha atual.</span>
              <span id="senhaError" class="form-error"></span>
            </div>

            <div class="form-group">
              <label for="confirmar_senha" class="form-label form-label-required">Confirmar Nova Senha</label>
              <div class="password-input-wrapper">
                <input 
                  type="password" 
                  id="confirmar_senha" 
                  name="confirmar_senha" 
                  class="form-input"
                  required
                  placeholder="Confirme a nova senha"
                >
                <button type="button" class="password-toggle" data-target="confirmar_senha">
                  <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                    <circle cx="12" cy="12" r="3"/>
                  </svg>
                  <svg class="eye-off-icon hidden" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                    <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                    <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                    <line x1="2" x2="22" y1="2" y2="22"/>
                  </svg>
                </button>
              </div>
              <span id="confirmarSenhaError" class="form-error"></span>
            </div>

            <div class="alert alert-success hidden" id="passwordSuccess">
              ✅ Senha alterada com sucesso!
            </div>

            <button type="submit" class="btn btn-primary btn-block" id="changePasswordBtn">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
              </svg>
              <span>Alterar Senha</span>
            </button>

          </form>
        </section>

      </div>

    </main>

  </div>

  <footer class="system-footer">
    <div class="footer-container">
  
      <div class="footer-brand">
        <span class="footer-system-name">Sistema de Gestão | PIPO</span>
        <span class="footer-copy">© 2026 · Todos os direitos reservados</span>
      </div>
  
      <nav class="footer-nav">
        <a href="dashboard.html" class="footer-link">Dashboard</a>
        <a href="menus.html" class="footer-link">Pratos</a>
        <a href="menus.html" class="footer-link">Cardápios</a>
        <a href="queues.html" class="footer-link">Filas</a>
        <a href="access.html" class="footer-link">Acessos</a>
        <a href="profile.php" class="footer-link">Perfil</a>
      </nav>
  
      <div class="footer-meta">
        <span class="footer-version">Versão 1.0.0</span>
      </div>
  
    </div>
  </footer>

  <!-- JavaScript -->
  <script src="js/pages/profile.js"></script>

</body>
</html>