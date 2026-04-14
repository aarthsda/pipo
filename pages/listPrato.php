<?php
  // Verificação de acesso administrativo
  require_once __DIR__ . "/../config/auth.php";
  $adminId = requireAuth('admin');

  require_once __DIR__ . "/../views/adminView.php";
  $admin = new adminView(['id_admin' => $adminId]);

  // Buscar todos os pratos do banco de dados
  require_once __DIR__ . "/../views/pratoView.php";
  $pratoView = new pratoView();
  $pratos = $pratoView->getAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Gestão de Pratos - Sistema de Restaurante">
  <title>Gestão de Pratos - Sistema de Restaurante</title>
  
  <!-- CSS -->
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/pages/dishes.css">
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
            <a href="listPrato.php" class="sidebar-menu-link active">
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
          <h1 class="page-title">Gestão de Pratos</h1>
          <p class="page-subtitle">Cadastre e gerencie os pratos disponíveis</p>
        </div>
        <div class="page-header-actions">
          <button class="btn btn-primary" id="openModalBtn">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" x2="12" y1="5" y2="19"/>
              <line x1="5" x2="19" y1="12" y2="12"/>
            </svg>
            <span>Novo Prato</span>
          </button>
        </div>
      </header>

      <!-- Barra de Busca e Filtros -->
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
            placeholder="Buscar pratos por nome..."
            autocomplete="off"
          >
        </div>
      </section>

      <!-- Grid de Pratos -->
      <section class="dishes-section">
        <div class="dishes-grid" id="dishesGrid">
          <?php if (!empty($pratos)): ?>
            <?php foreach ($pratos as $prato): ?>
              <?php
                // Buscar informações da imagem
                require_once __DIR__ . "/../views/uploadView.php";
                $uploadView = new uploadView(['id_upload' => $prato->foto_prato]);
                $imagemPath = $uploadView->exists() ? $uploadView->get('path_upload') : '../assets/images/default-dish.jpg';
              ?>
              <article class="dish-card" data-dish-name="<?php echo htmlspecialchars($prato->nome_prato); ?>">
                <div class="dish-card-image">
                  <img src="<?php echo htmlspecialchars($imagemPath); ?>" alt="<?php echo htmlspecialchars($prato->nome_prato); ?>">
                </div>
                <div class="dish-card-content">
                  <h3 class="dish-card-title"><?php echo htmlspecialchars($prato->nome_prato); ?></h3>
                  <p class="dish-card-description"><?php echo htmlspecialchars($prato->descricao_prato); ?></p>
                  <div class="dish-card-info">
                    <span class="dish-card-quantity">
                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 7h-9"/>
                        <path d="M14 17H5"/>
                        <circle cx="17" cy="17" r="3"/>
                        <circle cx="7" cy="7" r="3"/>
                      </svg>
                      <?php echo $prato->quantidade_disponivel_prato; ?> disponíveis
                    </span>
                  </div>
                </div>
                <div class="dish-card-actions">
                  <button class="btn-icon" onclick="editDish(<?php echo $prato->id_prato; ?>)" title="Editar prato">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                      <path d="m15 5 4 4"/>
                    </svg>
                  </button>
                  <button class="btn-icon btn-icon-danger" onclick="confirmDelete(<?php echo $prato->id_prato; ?>, '<?php echo htmlspecialchars($prato->nome_prato); ?>')" title="Excluir prato">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="3 6 5 6 21 6"/>
                      <path d="m19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                    </svg>
                  </button>
                </div>
              </article>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- Estado Vazio -->
        <div class="empty-state <?php echo empty($pratos) ? '' : 'hidden'; ?>" id="emptyState">
          <div class="empty-state-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/>
              <path d="M7 2v20"/>
              <path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/>
            </svg>
          </div>
          <h3 class="empty-state-title">Nenhum prato cadastrado</h3>
          <p class="empty-state-text">Comece cadastrando seu primeiro prato clicando no botão acima</p>
        </div>

        <!-- Nenhum Resultado de Busca -->
        <div class="empty-state hidden" id="noResults">
          <div class="empty-state-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="11" cy="11" r="8"/>
              <path d="m21 21-4.3-4.3"/>
            </svg>
          </div>
          <h3 class="empty-state-title">Nenhum prato encontrado</h3>
          <p class="empty-state-text">Tente buscar com outros termos</p>
        </div>
      </section>

    </main>

  </div>

  <!-- Modal de Cadastro -->
  <div class="modal-overlay hidden" id="modalOverlay">
    <div class="modal" id="dishModal">
      
      <div class="modal-header">
        <h2 class="modal-title" id="modalTitle">Novo Prato</h2>
        <button class="modal-close" id="closeModalBtn" aria-label="Fechar modal">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" x2="6" y1="6" y2="18"/>
            <line x1="6" x2="18" y1="6" y2="18"/>
          </svg>
        </button>
      </div>

      <form id="dishForm" action="ctrlHub.php?class=prato&action=create" method="post" enctype="multipart/form-data" class="modal-body" novalidate>
        
        <!-- Data do prato (hidden) -->
        <input type="hidden" name="data_prato" value="<?php date_default_timezone_set('America/Rio_Branco'); echo date('Y-m-d'); ?>">

        <!-- Nome do Prato -->
        <div class="form-group">
          <label for="dishName" class="form-label form-label-required">Nome do Prato</label>
          <input 
            type="text" 
            id="dishName" 
            name="nome_prato" 
            class="form-input"
            placeholder="Ex: Feijoada Completa"
            required
            minlength="3"
            maxlength="100"
          >
          <span id="dishNameError" class="form-error"></span>
        </div>

        <!-- Descrição -->
        <div class="form-group">
          <label for="dishDescription" class="form-label form-label-required">Descrição</label>
          <textarea 
            id="dishDescription" 
            name="descricao_prato" 
            class="form-textarea"
            placeholder="Descreva o prato..."
            required
            maxlength="500"
            rows="4"
          ></textarea>
          <span class="form-help">
            <span id="charCount">0</span>/500 caracteres
          </span>
          <span id="dishDescriptionError" class="form-error"></span>
        </div>

        <!-- Quantidade Disponível -->
        <div class="form-group">
          <label for="dishQuantity" class="form-label form-label-required">Quantidade Disponível</label>
          <input 
            type="number" 
            id="dishQuantity" 
            name="quantidade_disponivel_prato" 
            class="form-input"
            placeholder="0"
            required
            min="1"
            max="9999"
          >
          <span id="dishQuantityError" class="form-error"></span>
        </div>

        <!-- Upload de Imagem -->
        <div class="form-group">
          <label for="dishImage" class="form-label form-label-required">Imagem do Prato</label>
          <div class="image-upload-container">
            <input 
              type="file" 
              id="dishImage" 
              name="foto_prato" 
              accept="image/*"
              class="image-upload-input"
              required
            >
            <label for="dishImage" class="image-upload-label" id="imageUploadLabel">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                <circle cx="9" cy="9" r="2"/>
                <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
              </svg>
              <span class="image-upload-text">Clique para selecionar uma imagem</span>
              <span class="image-upload-hint">JPG, PNG ou GIF (máx. 5MB)</span>
            </label>
            <div class="image-preview hidden" id="imagePreview">
              <img id="previewImg" src="" alt="Preview">
              <button type="button" class="image-remove-btn" id="removeImageBtn">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="3 6 5 6 21 6"/>
                  <path d="m19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                </svg>
              </button>
            </div>
          </div>
          <span id="dishImageError" class="form-error"></span>
        </div>

      </form>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" id="cancelBtn">Cancelar</button>
        <button type="submit" form="dishForm" class="btn btn-primary" id="submitBtn">
          <span id="submitBtnText">Cadastrar Prato</span>
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
        <a href="menus.html" class="footer-link">Cardápios</a>
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
  <script src="js/pages/dishes.js"></script>

</body>
</html>