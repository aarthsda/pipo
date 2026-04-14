<?php
  // Verificação de acesso administrativo
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

  // Buscar todos os cardápios do banco de dados
  require_once __DIR__ . "/../views/cardapioView.php";
  require_once __DIR__ . "/../views/pratoView.php";
  
  $cardapioView = new cardapioView();
  $cardapios = $cardapioView->getAll();
  
  // Buscar todos os pratos disponíveis
  $pratoView = new pratoView();
  $pratos = $pratoView->getAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Gestão de Cardápios - Sistema de Restaurante">
  <title>Gestão de Cardápios - Sistema de Restaurante</title>
  
  <!-- CSS -->
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/pages/menus.css">
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
            <a href="listCardapio.php" class="sidebar-menu-link active">
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
          <div class="sidebar-user-avatar" id="userAvatar">
            <?php echo strtoupper(substr($admin->get('nome_admin'), 0, 1)); ?>
          </div>
          <div class="sidebar-user-info">
            <p class="sidebar-user-name" id="userName"><?php echo $admin->get('nome_admin'); ?></p>
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
          <h1 class="page-title">Gestão de Cardápios</h1>
          <p class="page-subtitle">Crie e gerencie cardápios associando pratos</p>
        </div>
        <div class="page-header-actions">
          <button class="btn btn-primary" id="openModalBtn">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" x2="12" y1="5" y2="19"/>
              <line x1="5" x2="19" y1="12" y2="12"/>
            </svg>
            <span>Novo Cardápio</span>
          </button>
        </div>
      </header>

      <!-- Barra de Busca -->
      <section class="filters-section">
        <div class="search-container">
          <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.3-4.3"/>
          </svg>
          <input 
            type="text" 
            class="search-input" 
            id="searchInput" 
            placeholder="Buscar cardápios por nome..."
            autocomplete="off"
          >
        </div>
      </section>

      <!-- Lista de Cardápios -->
      <section class="menus-section">
        <div class="menus-list" id="menusList">
          <?php if (!empty($cardapios)): ?>
            <?php foreach ($cardapios as $cardapio): ?>
              <?php
                // Buscar informações dos pratos do cardápio
                $pratoA = new pratoView(['id_prato' => $cardapio->prato_A_cardapio]);
                $pratoB = new pratoView(['id_prato' => $cardapio->prato_B_cardapio]);
              ?>
              <article class="menu-card" data-menu-name="<?php echo htmlspecialchars($cardapio->nome_cardapio); ?>">
                <div class="menu-card-header">
                  <div>
                    <h3 class="menu-card-title"><?php echo htmlspecialchars($cardapio->nome_cardapio); ?></h3>
                    <p class="menu-card-subtitle">2 pratos cadastrados</p>
                  </div>
                  <div class="menu-card-actions">
                    <button class="btn-icon" onclick="editMenu(<?php echo $cardapio->id_cardapio; ?>)" title="Editar cardápio">
                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                        <path d="m15 5 4 4"/>
                      </svg>
                    </button>
                    <button class="btn-icon btn-icon-danger" onclick="confirmDelete(<?php echo $cardapio->id_cardapio; ?>, '<?php echo htmlspecialchars($cardapio->nome_cardapio); ?>')" title="Excluir cardápio">
                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="m19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                      </svg>
                    </button>
                  </div>
                </div>
                <div class="menu-card-dishes">
                  <?php if ($pratoA->exists()): ?>
                    <?php
                      require_once __DIR__ . "/../views/uploadView.php";
                      $uploadA = new uploadView(['id_upload' => $pratoA->get('foto_prato')]);
                      $imagemA = $uploadA->exists() ? $uploadA->get('path_upload') : '../assets/images/default-dish.jpg';
                    ?>
                    <div class="menu-dish-item">
                      <div class="menu-dish-image">
                        <img src="<?php echo htmlspecialchars($imagemA); ?>" alt="<?php echo htmlspecialchars($pratoA->get('nome_prato')); ?>">
                      </div>
                      <div class="menu-dish-info">
                        <p class="menu-dish-name"><?php echo htmlspecialchars($pratoA->get('nome_prato')); ?></p>
                        <span class="menu-dish-badge">Prato A</span>
                      </div>
                    </div>
                  <?php endif; ?>
                  
                  <?php if ($pratoB->exists()): ?>
                    <?php
                      $uploadB = new uploadView(['id_upload' => $pratoB->get('foto_prato')]);
                      $imagemB = $uploadB->exists() ? $uploadB->get('path_upload') : '../assets/images/default-dish.jpg';
                    ?>
                    <div class="menu-dish-item">
                      <div class="menu-dish-image">
                        <img src="<?php echo htmlspecialchars($imagemB); ?>" alt="<?php echo htmlspecialchars($pratoB->get('nome_prato')); ?>">
                      </div>
                      <div class="menu-dish-info">
                        <p class="menu-dish-name"><?php echo htmlspecialchars($pratoB->get('nome_prato')); ?></p>
                        <span class="menu-dish-badge">Prato B</span>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
              </article>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- Estado Vazio -->
        <div class="empty-state <?php echo empty($cardapios) ? '' : 'hidden'; ?>" id="emptyState">
          <div class="empty-state-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
            </svg>
          </div>
          <h3 class="empty-state-title">Nenhum cardápio cadastrado</h3>
          <p class="empty-state-text">Comece criando seu primeiro cardápio</p>
        </div>

        <!-- Nenhum Resultado -->
        <div class="empty-state hidden" id="noResults">
          <div class="empty-state-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"/>
              <path d="m21 21-4.3-4.3"/>
            </svg>
          </div>
          <h3 class="empty-state-title">Nenhum cardápio encontrado</h3>
          <p class="empty-state-text">Tente buscar com outros termos</p>
        </div>
      </section>

    </main>

  </div>

  <!-- Modal de Cadastro/Edição -->
  <div class="modal-overlay hidden" id="modalOverlay">
    <div class="modal modal-large" id="menuModal">
      
      <div class="modal-header">
        <h2 class="modal-title" id="modalTitle">Novo Cardápio</h2>
        <button class="modal-close" id="closeModalBtn" aria-label="Fechar modal">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" x2="6" y1="6" y2="18"/>
            <line x1="6" x2="18" y1="6" y2="18"/>
          </svg>
        </button>
      </div>

      <form id="menuForm" action="ctrlHub.php?class=cardapio&action=create" method="post" class="modal-body" novalidate>
        
        <!-- Nome do Cardápio -->
        <div class="form-group">
          <label for="menuName" class="form-label form-label-required">Nome do Cardápio</label>
          <input 
            type="text" 
            id="menuName" 
            name="nome_cardapio" 
            class="form-input"
            placeholder="Ex: Cardápio Executivo"
            required
            minlength="3"
            maxlength="100"
          >
          <span id="menuNameError" class="form-error"></span>
        </div>

        <!-- Seleção de Pratos -->
        <div class="form-group">
          <label class="form-label form-label-required">Selecionar Pratos</label>
          <p class="form-help mb-3">Selecione 2 pratos para o cardápio (Prato A e Prato B)</p>
          
          <?php if (empty($pratos)): ?>
            <!-- Nenhum prato cadastrado -->
            <div class="alert alert-warning" id="noDishesAlert">
              <strong>⚠️ Atenção:</strong> Nenhum prato cadastrado. 
              <a href="dishes.php" class="text-primary font-semibold">Cadastre pratos primeiro</a>.
            </div>
          <?php else: ?>
            <!-- Prato A -->
            <div class="form-group">
              <label for="pratoA" class="form-label form-label-required">Prato A</label>
              <select 
                id="pratoA" 
                name="prato_A_cardapio" 
                class="form-input"
                required
              >
                <option value="">Selecione o Prato A</option>
                <?php foreach ($pratos as $prato): ?>
                  <option value="<?php echo $prato->id_prato; ?>">
                    <?php echo htmlspecialchars($prato->nome_prato); ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <span id="pratoAError" class="form-error"></span>
            </div>

            <!-- Prato B -->
            <div class="form-group">
              <label for="pratoB" class="form-label form-label-required">Prato B</label>
              <select 
                id="pratoB" 
                name="prato_B_cardapio" 
                class="form-input"
                required
              >
                <option value="">Selecione o Prato B</option>
                <?php foreach ($pratos as $prato): ?>
                  <option value="<?php echo $prato->id_prato; ?>">
                    <?php echo htmlspecialchars($prato->nome_prato); ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <span id="pratoBError" class="form-error"></span>
            </div>
          <?php endif; ?>

          <span id="dishesError" class="form-error"></span>
        </div>

      </form>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="cancelBtn">Cancelar</button>
        <button type="submit" form="menuForm" class="btn btn-primary" id="submitBtn" <?php echo empty($pratos) ? 'disabled' : ''; ?>>
          <span id="submitBtnText">Cadastrar Cardápio</span>
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
        <a href="dashboard.html" class="footer-link">Dashboard</a>
        <a href="dishes.php" class="footer-link">Pratos</a>
        <a href="menus.php" class="footer-link">Cardápios</a>
        <a href="queues.html" class="footer-link">Filas</a>
        <a href="access.html" class="footer-link">Acessos</a>
        <a href="profile.html" class="footer-link">Perfil</a>
      </nav>
  
      <div class="footer-meta">
        <span class="footer-version">Versão 1.0.0</span>
      </div>
  
    </div>
  </footer>

  <!-- JavaScript NOVO -->
  <script src="js/pages/menus.js"></script>

</body>
</html>