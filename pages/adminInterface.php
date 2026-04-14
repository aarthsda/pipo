<?php
// ============================================
// DASHBOARD.PHP - VERIFICAÇÃO E AUTENTICAÇÃO
// ============================================

// Verificar se o usuário está autenticado
if (empty($_COOKIE) || $_COOKIE['user_type'] != 'admin') {
    header("Location: hub.php?msg=acessoproibido");
    exit();
}

require_once __DIR__ . "/../views/adminView.php";

// Buscar dados do admin logado
$admin = new adminView(['id_admin' => $_COOKIE['user_id']]);

// Verificar se a senha do cookie ainda é válida
if (!$admin->verifyPassword($_COOKIE['user_password'])) {
    setcookie("user_type", 'guest', time() + 3600, "/");
    setcookie("user_id", '', time() + 3600, "/");
    setcookie("user_password", '', time() + 3600, "/");

    header("Location: hub.php");
    exit();
}

// Processar logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    setcookie("user_type", 'guest', time() + 3600, "/");
    setcookie("user_id", '', time() + 3600, "/");
    setcookie("user_password", '', time() + 3600, "/");

    header("Location: hub.php?msg=logout");
    exit();
}

// Pegar nome do admin para exibir
$nomeAdmin = $admin->get('nome_admin') ?? 'Administrador';
$primeiraLetra = strtoupper(substr($nomeAdmin, 0, 1));

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Dashboard - Sistema de Administração de Restaurante">
  <meta name="theme-color" content="#1351B4">
  <title>Dashboard - Sistema de Restaurante</title>
  
  <!-- CSS -->
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/pages/dashboard.css">
</head>
<body>

  <!-- Layout Principal -->
  <div class="main-layout">

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      
      <!-- Header da Sidebar -->
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

      <!-- Navegação -->
      <nav class="sidebar-nav">
        <ul class="sidebar-menu">
          <li class="sidebar-menu-item">
            <a href="adminInterface.php" class="sidebar-menu-link active">
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
                  <polyline points="10 9 9 9 8 9"/>
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
            <a href="alterAdmin.php" class="sidebar-menu-link">
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

      <!-- Footer da Sidebar -->
      <div class="sidebar-footer">
        <div class="sidebar-user">
          <div class="sidebar-user-avatar" id="userAvatar"><?php echo $primeiraLetra; ?></div>
          <div class="sidebar-user-info">
            <p class="sidebar-user-name" id="userName"><?php echo htmlspecialchars($nomeAdmin); ?></p>
            <p class="sidebar-user-role">Administrador</p>
          </div>
        </div>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="logoutForm">
          <input type="hidden" name="logout" value="1">
          <button type="submit" class="sidebar-logout" id="logoutBtn">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
              <polyline points="16 17 21 12 16 7"/>
              <line x1="21" x2="9" y1="12" y2="12"/>
            </svg>
            <span>Sair</span>
          </button>
        </form>
      </div>

    </aside>

    <!-- Botão Toggle Mobile -->
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Abrir menu">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="4" x2="20" y1="12" y2="12"/>
        <line x1="4" x2="20" y1="6" y2="6"/>
        <line x1="4" x2="20" y1="18" y2="18"/>
      </svg>
    </button>

    <!-- Overlay Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Conteúdo Principal -->
    <main class="main-content">

      <!-- Header da Página -->
      <header class="page-header">
        <div>
          <h1 class="page-title">Dashboard</h1>
          <p class="page-subtitle">Visão geral do sistema de restaurante</p>
        </div>
        <div class="page-header-actions">
          <span class="page-date" id="currentDate"></span>
        </div>
      </header>

      <!-- Cards de Estatísticas -->
      <section class="stats-section">
        <div class="stats-grid">
          
          <!-- Card: Pratos -->
          <div class="stats-card">
            <div class="stats-card-content">
              <div class="stats-card-info">
                <p class="stats-card-title">Pratos Cadastrados</p>
                <p class="stats-card-value" id="dishesCount">0</p>
              </div>
              <div class="stats-card-icon" style="background-color: var(--color-primary-lighter); color: var(--color-primary);">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/>
                  <path d="M7 2v20"/>
                  <path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/>
                </svg>
              </div>
            </div>
          </div>

          <!-- Card: Cardápios -->
          <div class="stats-card">
            <div class="stats-card-content">
              <div class="stats-card-info">
                <p class="stats-card-title">Cardápios Ativos</p>
                <p class="stats-card-value" id="menusCount">0</p>
              </div>
              <div class="stats-card-icon" style="background-color: var(--color-success-light); color: var(--color-success);">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                  <polyline points="14 2 14 8 20 8"/>
                </svg>
              </div>
            </div>
          </div>

          <!-- Card: Filas -->
          <div class="stats-card">
            <div class="stats-card-content">
              <div class="stats-card-info">
                <p class="stats-card-title">Filas Agendadas</p>
                <p class="stats-card-value" id="queuesCount">0</p>
              </div>
              <div class="stats-card-icon" style="background-color: var(--color-warning-light); color: #8B6914;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"/>
                  <polyline points="12 6 12 12 16 14"/>
                </svg>
              </div>
            </div>
          </div>

          <!-- Card: Usuários de Acesso -->
          <div class="stats-card">
            <div class="stats-card-content">
              <div class="stats-card-info">
                <p class="stats-card-title">Usuários de Acesso</p>
                <p class="stats-card-value" id="usersCount">0</p>
              </div>
              <div class="stats-card-icon" style="background-color: #f3e8ff; color: #7c3aed;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                  <circle cx="9" cy="7" r="4"/>
                  <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
              </div>
            </div>
          </div>

        </div>
      </section>

      <!-- Ações Rápidas -->
      <section class="quick-actions-section">
        <h2 class="section-title">Ações Rápidas</h2>
        <div class="quick-actions-grid">
          <a href="listPrato.php" class="quick-action-card">
            <div class="quick-action-icon" style="background-color: var(--color-primary-lighter); color: var(--color-primary);">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" x2="12" y1="5" y2="19"/>
                <line x1="5" x2="19" y1="12" y2="12"/>
              </svg>
            </div>
            <h3 class="quick-action-title">Cadastrar Prato</h3>
            <p class="quick-action-description">Adicione novos pratos ao sistema</p>
          </a>

          <a href="listCardapio.php" class="quick-action-card">
            <div class="quick-action-icon" style="background-color: var(--color-success-light); color: var(--color-success);">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" x2="12" y1="5" y2="19"/>
                <line x1="5" x2="19" y1="12" y2="12"/>
              </svg>
            </div>
            <h3 class="quick-action-title">Criar Cardápio</h3>
            <p class="quick-action-description">Monte um novo cardápio</p>
          </a>

          <a href="listFila.php" class="quick-action-card">
            <div class="quick-action-icon" style="background-color: var(--color-warning-light); color: #8B6914;">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" x2="12" y1="5" y2="19"/>
                <line x1="5" x2="19" y1="12" y2="12"/>
              </svg>
            </div>
            <h3 class="quick-action-title">Agendar Fila</h3>
            <p class="quick-action-description">Crie uma nova fila agendada</p>
          </a>

          <a href="listUsuario.php" class="quick-action-card">
            <div class="quick-action-icon" style="background-color: #f3e8ff; color: #7c3aed;">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" x2="12" y1="5" y2="19"/>
                <line x1="5" x2="19" y1="12" y2="12"/>
              </svg>
            </div>
            <h3 class="quick-action-title">Gerenciar Acessos</h3>
            <p class="quick-action-description">Administre usuários do sistema</p>
          </a>
        </div>
      </section>

      <!-- Informações do Sistema -->
      <section class="system-info-section">
        <div class="card">
          <div class="card-body">
            <h2 class="section-title">Bem-vindo ao Sistema de Restaurante</h2>
            <p class="text-gray-600">
              Utilize o menu lateral para navegar entre os módulos de gestão. 
              Todos os dados são validados automaticamente para garantir a integridade do sistema.
            </p>
            <div class="mt-4">
              <div class="alert alert-info">
                <strong>💡 Dica:</strong> Use as ações rápidas acima para acessar rapidamente as funcionalidades mais utilizadas.
              </div>
            </div>
          </div>
        </div>
      </section>

    </main>

  </div>

  <footer class="system-footer">
    <div class="footer-container">
  
      <div class="footer-brand">
        <span class="footer-system-name">Sistema de Gestão | PIPO</span>
        <span class="footer-copy">© 2026 · Todos os direitos reservados</span>
      </div>
  
      <nav class="footer-nav">
        <a href="dashboard.php" class="footer-link">Dashboard</a>
        <a href="dishes.php" class="footer-link">Pratos</a>
        <a href="menus.php" class="footer-link">Cardápios</a>
        <a href="queues.php" class="footer-link">Filas</a>
        <a href="access.php" class="footer-link">Acessos</a>
        <a href="profile.php" class="footer-link">Perfil</a>
      </nav>
  
      <div class="footer-meta">
        <span class="footer-version">Versão 1.0.0</span>
      </div>
  
    </div>
  </footer>

  <!-- JavaScript -->
  <script src="../js/pages/dashboard.js"></script>

</body>
</html>