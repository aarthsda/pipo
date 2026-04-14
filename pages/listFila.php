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

  // Buscar dados para renderização
  require_once __DIR__ . "/../views/filaView.php";
  require_once __DIR__ . "/../views/cardapioView.php";

  $filaView = new filaView();
  $filas = $filaView->getAll();

  $cardapioView = new cardapioView();
  $cardapios = $cardapioView->getAll();

  date_default_timezone_set('America/Rio_Branco');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Gestão de Filas - Sistema de Restaurante">
  <title>Gestão de Filas - Sistema de Restaurante</title>
  
  <!-- CSS -->
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/pages/queues.css">
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
            <a href="listFila.php" class="sidebar-menu-link active">
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
          <h1 class="page-title">Gestão de Filas</h1>
          <p class="page-subtitle">Agende e acompanhe filas em tempo real</p>
        </div>
        <div class="page-header-actions">
          <button class="btn btn-success" id="openQueueBtn" onclick="window.location.href='confirmarAlmoco.php'">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M5 12h14"/>
              <path d="m12 5 7 7-7 7"/>
            </svg>
            <span>Abrir Fila</span>
          </button>
          <button class="btn btn-primary" id="openModalBtn">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" x2="12" y1="5" y2="19"/>
              <line x1="5" x2="19" y1="12" y2="12"/>
            </svg>
            <span>Nova Fila</span>
          </button>
        </div>
      </header>

      <!-- Mensagens de Feedback -->
      <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-<?php echo $_GET['msg'] === 'cadastrorealizado' ? 'success' : 'error'; ?>">
          <?php
            $messages = [
              'cadastrorealizado' => 'Fila cadastrada com sucesso!',
              'erro' => 'Erro ao processar requisição. Tente novamente.'
            ];
            echo $messages[$_GET['msg']] ?? 'Operação realizada.';
          ?>
        </div>
      <?php endif; ?>

      <!-- Filtros e Tabs -->
      <section class="filters-section">
        <div class="tabs">
          <div class="tabs-list">
            <button class="tabs-item active" data-status="all">Todas</button>
            <button class="tabs-item" data-status="ativa">Agendadas</button>
            <button class="tabs-item" data-status="em_andamento">Em Andamento</button>
            <button class="tabs-item" data-status="finalizada">Finalizadas</button>
          </div>
        </div>
      </section>

      <!-- Lista de Filas -->
      <section class="queues-section">
        <div class="queues-list" id="queuesList">
          <!-- Filas renderizadas pelo JavaScript -->
        </div>

        <!-- Estado Vazio -->
        <div class="empty-state hidden" id="emptyState">
          <div class="empty-state-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"/>
              <polyline points="12 6 12 12 16 14"/>
            </svg>
          </div>
          <h3 class="empty-state-title">Nenhuma fila cadastrada</h3>
          <p class="empty-state-text">Crie sua primeira fila de atendimento</p>
        </div>
      </section>

    </main>

  </div>

  <!-- Modal de Cadastro (aponta para createFila.php) -->
  <div class="modal-overlay hidden" id="modalOverlay">
    <div class="modal" id="queueModal">
      
      <div class="modal-header">
        <h2 class="modal-title" id="modalTitle">Nova Fila</h2>
        <button class="modal-close" id="closeModalBtn" aria-label="Fechar modal">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" x2="6" y1="6" y2="18"/>
            <line x1="6" x2="18" y1="6" y2="18"/>
          </svg>
        </button>
      </div>

      <form id="queueForm" class="modal-body" action="ctrlHub.php?class=fila&action=create" method="POST" novalidate>
        
        <!-- Data da Fila -->
        <div class="form-section">
          <h4 class="form-section-title">📅 Data da Fila</h4>
          <p class="form-section-help">Define a data em que a fila será realizada</p>
          
          <div class="form-group">
            <label for="data_fila" class="form-label form-label-required">Data da Fila</label>
            <input 
              type="date" 
              id="data_fila" 
              name="data_fila" 
              class="form-input"
              value="<?php echo date('Y-m-d'); ?>"
              required
            >
            <span id="dataFilaError" class="form-error"></span>
          </div>
        </div>

        <div class="form-divider"></div>

        <!-- Configurações da Fila -->
        <div class="form-section">
          <h4 class="form-section-title">⚙️ Configurações da Fila</h4>
          
          <div class="form-group">
            <label for="cardapio_fila" class="form-label form-label-required">Cardápio</label>
            <select 
              id="cardapio_fila" 
              name="cardapio_fila" 
              class="form-select"
              required
            >
              <option value="">Selecione um cardápio</option>
              <?php foreach ($cardapios as $cardapio): ?>
                <option value="<?php echo $cardapio->id_cardapio; ?>">
                  <?php echo htmlspecialchars($cardapio->nome_cardapio); ?>
                </option>
              <?php endforeach; ?>
            </select>
            <span id="menuIdError" class="form-error"></span>
          </div>

          <div class="form-group">
            <label for="capacidade_maxima_fila" class="form-label form-label-required">Capacidade Total de Pessoas</label>
            <input 
              type="number" 
              id="capacidade_maxima_fila" 
              name="capacidade_maxima_fila" 
              class="form-input"
              placeholder="Ex: 100"
              value="100"
              required
              min="1"
              max="9999"
            >
            <span class="form-help">Número máximo de pessoas que podem ser atendidas</span>
            <span id="totalCapacityError" class="form-error"></span>
          </div>

          <!-- Campos ocultos necessários pelo backend -->
          <input type="hidden" name="status_fila" value="ativa">
          <input type="hidden" name="reservas_em_fila" value="0">
        </div>

      </form>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="cancelBtn">Cancelar</button>
        <button type="submit" form="queueForm" class="btn btn-primary" id="submitBtn">
          <span id="submitBtnText">Cadastrar Fila</span>
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
    // Dados das filas vindos do PHP
    window.filasData = <?php echo json_encode($filas); ?>;
    window.cardapiosData = <?php echo json_encode($cardapios); ?>;
  </script>

  <!-- JavaScript -->
  <script src="js/pages/queues.js"></script>

</body>
</html>