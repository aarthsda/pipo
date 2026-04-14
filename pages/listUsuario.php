<?php
  // Verificação de autenticação de administrador
  if (empty($_COOKIE) || $_COOKIE['user_type'] != 'admin') {
    header("Location: hub.php?msg=acessoproibido");
    exit();
  }

  require_once __DIR__ . "/../views/adminView.php";

  $admin = new adminView(['id_admin' => $_COOKIE['user_id']]);

  if (!$admin->verifyPassword($_COOKIE['user_password'])) {
    setcookie("user_type", 'guest', time() + 3600, "/");
    setcookie("user_id", '', time() + 3600, "/");
    setcookie("user_password", '', time() + 3600, "/");

    header("Location: hub.php");
    exit();
  }

  // Buscar usuários do banco
  require_once __DIR__ . "/../views/usuarioView.php";

  $usuarioView = new usuarioView();
  $usuarios = $usuarioView->getAll();

  date_default_timezone_set('America/Rio_Branco');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Gestão de Acessos - Sistema de Restaurante">
  <title>Gestão de Acessos - Sistema de Restaurante</title>
  
  <!-- CSS -->
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/pages/access.css">
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
            <a href="listUsuario.php" class="sidebar-menu-link active">
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

      <div class="sidebar-footer">
        <div class="sidebar-user">
          <div class="sidebar-user-avatar"><?php echo strtoupper(substr($admin->get('nome_admin'), 0, 1)); ?></div>
          <div class="sidebar-user-info">
            <p class="sidebar-user-name"><?php echo htmlspecialchars($admin->get('nome_admin')); ?></p>
            <p class="sidebar-user-role">Administrador</p>
          </div>
        </div>
        <button class="sidebar-logout" id="logoutBtn">
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
          <h1 class="page-title">Gestão de Acessos</h1>
          <p class="page-subtitle">Gerencie usuários que acessarão o sistema de consulta</p>
        </div>
        <div class="page-header-actions">
          <button class="btn btn-primary" id="openModalBtn">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" x2="12" y1="5" y2="19"/>
              <line x1="5" x2="19" y1="12" y2="12"/>
            </svg>
            <span>Novo Usuário</span>
          </button>
        </div>
      </header>

      <!-- Mensagens de Feedback -->
      <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-<?php echo $_GET['msg'] === 'cadastrorealizado' ? 'success' : 'error'; ?>">
          <?php
            $messages = [
              'cadastrorealizado' => 'Usuário cadastrado com sucesso!',
              'erro' => 'Erro ao processar requisição. Tente novamente.'
            ];
            echo $messages[$_GET['msg']] ?? 'Operação realizada.';
          ?>
        </div>
      <?php endif; ?>

      <!-- Info Box -->
      <div class="info-box">
        <div class="info-box-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <path d="M12 16v-4"/>
            <path d="M12 8h.01"/>
          </svg>
        </div>
        <div class="info-box-content">
          <p><strong>ℹ️ Importante:</strong> Estes usuários NÃO têm acesso ao painel administrativo. Eles poderão apenas consultar filas e cardápios no sistema de visualização.</p>
        </div>
      </div>

      <!-- Filtros e Stats -->
      <section class="filters-section">
        <div class="access-stats">
          <div class="access-stat-card">
            <div class="access-stat-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
              </svg>
            </div>
            <div class="access-stat-info">
              <div class="access-stat-value" id="totalUsers">0</div>
              <div class="access-stat-label">Total de Usuários</div>
            </div>
          </div>
          <div class="access-stat-card priority">
            <div class="access-stat-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
              </svg>
            </div>
            <div class="access-stat-info">
              <div class="access-stat-value" id="priorityUsers">0</div>
              <div class="access-stat-label">Usuários Prioritários</div>
            </div>
          </div>
        </div>

        <div class="search-and-filters">
          <div class="search-container">
            <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"/>
              <path d="m21 21-4.3-4.3"/>
            </svg>
            <input 
              type="text" 
              class="search-input" 
              id="searchInput" 
              placeholder="Buscar por nome, email ou matrícula..."
            >
          </div>
          <div class="filter-buttons">
            <button class="filter-btn active" data-filter="all">Todos</button>
            <button class="filter-btn" data-filter="prioritario">Prioritários</button>
            <button class="filter-btn" data-filter="comum">Normais</button>
          </div>
        </div>
      </section>

      <!-- Tabela de Usuários -->
      <section class="users-section">
        <div class="table-container">
          <table class="table" id="usersTable">
            <thead>
              <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Matrícula</th>
                <th>Tipo</th>
                <th class="text-center">Ações</th>
              </tr>
            </thead>
            <tbody id="usersTableBody">
              <!-- Linhas renderizadas pelo JavaScript -->
            </tbody>
          </table>
        </div>

        <!-- Estado Vazio -->
        <div class="empty-state hidden" id="emptyState">
          <div class="empty-state-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
              <circle cx="9" cy="7" r="4"/>
              <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </div>
          <h3 class="empty-state-title">Nenhum usuário cadastrado</h3>
          <p class="empty-state-text">Comece adicionando usuários individualmente</p>
        </div>

        <!-- Sem Resultados -->
        <div class="empty-state hidden" id="noResults">
          <h3 class="empty-state-title">Nenhum usuário encontrado</h3>
          <p class="empty-state-text">Tente ajustar os filtros de busca</p>
        </div>
      </section>

    </main>

  </div>

  <!-- Modal de Cadastro -->
  <div class="modal-overlay hidden" id="modalOverlay">
    <div class="modal" id="userModal">
      
      <div class="modal-header">
        <h2 class="modal-title" id="modalTitle">Novo Usuário</h2>
        <button class="modal-close" id="closeModalBtn">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" x2="6" y1="6" y2="18"/>
            <line x1="6" x2="18" y1="6" y2="18"/>
          </svg>
        </button>
      </div>

      <form id="userForm" class="modal-body" action="ctrlHub.php?class=usuario&action=create" method="POST" novalidate>
        
        <div class="form-group">
          <label for="nome_usuario" class="form-label form-label-required">Nome Completo</label>
          <input 
            type="text" 
            id="nome_usuario" 
            name="nome_usuario" 
            class="form-input"
            placeholder="Ex: João da Silva"
            required
            minlength="3"
          >
          <span id="userNameError" class="form-error"></span>
        </div>

        <div class="form-group">
          <label for="email_usuario" class="form-label form-label-required">Email</label>
          <input 
            type="email" 
            id="email_usuario" 
            name="email_usuario" 
            class="form-input"
            placeholder="usuario@email.com"
            required
          >
          <span id="userEmailError" class="form-error"></span>
        </div>

        <div class="form-group">
          <label for="matricula_usuario" class="form-label form-label-required">Matrícula</label>
          <input 
            type="text" 
            id="matricula_usuario" 
            name="matricula_usuario" 
            class="form-input"
            placeholder="Ex: 2024001"
            required
          >
          <span class="form-help">A matrícula será utilizada como senha inicial</span>
          <span id="userMatriculaError" class="form-error"></span>
        </div>

        <div class="form-group">
          <label for="categoria_usuario" class="form-label form-label-required">Categoria</label>
          <select 
            id="categoria_usuario" 
            name="categoria_usuario" 
            class="form-select"
            required
          >
            <option value="comum">Comum</option>
            <option value="prioritario">Prioritário</option>
          </select>
          <span class="form-help">Usuários prioritários têm atendimento preferencial nas filas</span>
          <span id="userCategoriaError" class="form-error"></span>
        </div>

        <!-- Campo oculto para senha (será preenchido via JS) -->
        <input type="hidden" id="senha_usuario" name="senha_usuario">

        <div class="alert alert-warning" id="passwordAlert">
          <strong>🔒 Senha Automática:</strong> A senha inicial será a matrícula do usuário. Recomende que ele altere após o primeiro acesso.
        </div>

      </form>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="cancelBtn">Cancelar</button>
        <button type="submit" form="userForm" class="btn btn-primary" id="submitBtn">
          <span id="submitBtnText">Cadastrar Usuário</span>
        </button>
      </div>

    </div>
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

  <!-- Dados do PHP para JavaScript -->
  <script>
    // Dados dos usuários vindos do PHP
    window.usuariosData = <?php echo json_encode($usuarios); ?>;
  </script>

  <!-- JavaScript -->
  <script src="js/pages/access.js"></script>

</body>
</html>