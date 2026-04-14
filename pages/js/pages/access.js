// ========================================
// ACCESS.JS - JAVASCRIPT NOVO (SEM LOCALSTORAGE)
// Responsável por: validação, interações UI e renderização
// ========================================

// Variáveis globais
let usuarios = [];
let currentFilter = 'all';
let searchTerm = '';

// ========================================
// INICIALIZAÇÃO
// ========================================
document.addEventListener('DOMContentLoaded', function() {
  // Carregar dados vindos do PHP
  loadDataFromPHP();
  
  // Inicializar componentes
  initSidebar();
  initModal();
  initFilters();
  initSearch();
  initLogout();
  
  // Renderizar usuários
  renderUsers();
  updateStats();
});

// ========================================
// CARREGAR DADOS DO PHP
// ========================================
function loadDataFromPHP() {
  if (window.usuariosData) {
    usuarios = window.usuariosData;
  }
}

// ========================================
// SIDEBAR (MOBILE)
// ========================================
function initSidebar() {
  const sidebar = document.getElementById('sidebar');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebarOverlay = document.getElementById('sidebarOverlay');

  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', function() {
      sidebar.classList.toggle('sidebar-open');
      sidebarOverlay.classList.toggle('active');
    });
  }

  if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', function() {
      sidebar.classList.remove('sidebar-open');
      sidebarOverlay.classList.remove('active');
    });
  }
}

// ========================================
// MODAL DE CADASTRO
// ========================================
function initModal() {
  const modalOverlay = document.getElementById('modalOverlay');
  const openModalBtn = document.getElementById('openModalBtn');
  const closeModalBtn = document.getElementById('closeModalBtn');
  const cancelBtn = document.getElementById('cancelBtn');
  const userForm = document.getElementById('userForm');

  // Abrir modal
  if (openModalBtn) {
    openModalBtn.addEventListener('click', function() {
      modalOverlay.classList.remove('hidden');
      resetForm();
    });
  }

  // Fechar modal
  const closeModal = function() {
    modalOverlay.classList.add('hidden');
    resetForm();
  };

  if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
  if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
  
  if (modalOverlay) {
    modalOverlay.addEventListener('click', function(e) {
      if (e.target === modalOverlay) closeModal();
    });
  }

  // Validação e preenchimento automático da senha
  if (userForm) {
    userForm.addEventListener('submit', function(e) {
      // Preencher senha com matrícula
      const matricula = document.getElementById('matricula_usuario').value;
      document.getElementById('senha_usuario').value = matricula;

      if (!validateForm()) {
        e.preventDefault();
      }
    });
  }
}

// ========================================
// VALIDAÇÃO DO FORMULÁRIO
// ========================================
function validateForm() {
  let isValid = true;
  
  // Limpar erros anteriores
  clearErrors();

  // Nome
  const nome = document.getElementById('nome_usuario');
  if (!nome.value || nome.value.trim().length < 3) {
    showError('userNameError', 'Nome deve ter no mínimo 3 caracteres');
    isValid = false;
  }

  // Email
  const email = document.getElementById('email_usuario');
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!email.value || !emailRegex.test(email.value)) {
    showError('userEmailError', 'Email inválido');
    isValid = false;
  }

  // Matrícula
  const matricula = document.getElementById('matricula_usuario');
  if (!matricula.value || matricula.value.trim().length < 3) {
    showError('userMatriculaError', 'Matrícula é obrigatória');
    isValid = false;
  }

  // Categoria
  const categoria = document.getElementById('categoria_usuario');
  if (!categoria.value) {
    showError('userCategoriaError', 'Selecione uma categoria');
    isValid = false;
  }

  return isValid;
}

function showError(elementId, message) {
  const errorElement = document.getElementById(elementId);
  if (errorElement) {
    errorElement.textContent = message;
    errorElement.style.display = 'block';
  }
}

function clearErrors() {
  const errors = document.querySelectorAll('.form-error');
  errors.forEach(error => {
    error.textContent = '';
    error.style.display = 'none';
  });
}

function resetForm() {
  const form = document.getElementById('userForm');
  if (form) {
    form.reset();
    clearErrors();
  }
}

// ========================================
// FILTROS
// ========================================
function initFilters() {
  const filterButtons = document.querySelectorAll('.filter-btn');
  
  filterButtons.forEach(button => {
    button.addEventListener('click', function() {
      // Remover active de todos
      filterButtons.forEach(btn => btn.classList.remove('active'));
      
      // Adicionar active no clicado
      this.classList.add('active');
      
      // Atualizar filtro
      currentFilter = this.getAttribute('data-filter');
      
      // Re-renderizar usuários
      renderUsers();
    });
  });
}

// ========================================
// BUSCA
// ========================================
function initSearch() {
  const searchInput = document.getElementById('searchInput');
  
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      searchTerm = this.value.toLowerCase();
      renderUsers();
    });
  }
}

// ========================================
// RENDERIZAÇÃO DE USUÁRIOS
// ========================================
function renderUsers() {
  const tableBody = document.getElementById('usersTableBody');
  const emptyState = document.getElementById('emptyState');
  const noResults = document.getElementById('noResults');
  const tableContainer = document.querySelector('.table-container');

  if (!tableBody) return;

  // Filtrar usuários
  const filteredUsers = filterUsers();

  // Se não houver usuários
  if (usuarios.length === 0) {
    tableContainer.style.display = 'none';
    emptyState.classList.remove('hidden');
    noResults.classList.add('hidden');
    return;
  }

  // Se não houver resultados na busca/filtro
  if (filteredUsers.length === 0) {
    tableContainer.style.display = 'none';
    emptyState.classList.add('hidden');
    noResults.classList.remove('hidden');
    return;
  }

  // Renderizar usuários
  emptyState.classList.add('hidden');
  noResults.classList.add('hidden');
  tableContainer.style.display = 'block';
  
  tableBody.innerHTML = filteredUsers.map(usuario => createUserRow(usuario)).join('');
}

function filterUsers() {
  let filtered = usuarios;

  // Filtro por categoria
  if (currentFilter !== 'all') {
    filtered = filtered.filter(u => u.categoria_usuario === currentFilter);
  }

  // Filtro por busca
  if (searchTerm) {
    filtered = filtered.filter(u => {
      const nome = (u.nome_usuario || '').toLowerCase();
      const email = (u.email_usuario || '').toLowerCase();
      const matricula = (u.matricula_usuario || '').toLowerCase();
      
      return nome.includes(searchTerm) || 
             email.includes(searchTerm) || 
             matricula.includes(searchTerm);
    });
  }

  return filtered;
}

function createUserRow(usuario) {
  const categoriaBadge = getCategoriaIcon(usuario.categoria_usuario);
  
  return `
    <tr>
      <td>${escapeHtml(usuario.nome_usuario)}</td>
      <td>${escapeHtml(usuario.email_usuario)}</td>
      <td>${escapeHtml(usuario.matricula_usuario || 'N/A')}</td>
      <td>${categoriaBadge}</td>
      <td class="text-center">
        <button class="btn-icon" onclick="deleteUser(${usuario.id_usuario})" title="Excluir">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 6h18"/>
            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
          </svg>
        </button>
      </td>
    </tr>
  `;
}

// ========================================
// ESTATÍSTICAS
// ========================================
function updateStats() {
  const totalUsers = usuarios.length;
  const priorityUsers = usuarios.filter(u => u.categoria_usuario === 'prioritario').length;

  const totalElement = document.getElementById('totalUsers');
  const priorityElement = document.getElementById('priorityUsers');

  if (totalElement) totalElement.textContent = totalUsers;
  if (priorityElement) priorityElement.textContent = priorityUsers;
}

// ========================================
// FUNÇÕES AUXILIARES
// ========================================
function getCategoriaIcon(categoria) {
  if (categoria === 'prioritario') {
    return `
      <span class="badge badge-warning">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
        </svg>
        Prioritário
      </span>
    `;
  }
  return '<span class="badge badge-secondary">Comum</span>';
}

function escapeHtml(text) {
  if (!text) return '';
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

// ========================================
// AÇÕES DE USUÁRIO
// ========================================
function deleteUser(id) {
  if (confirm('Tem certeza que deseja excluir este usuário?')) {
    window.location.href = `ctrlHub.php?class=usuario&action=delete&id=${id}`;
  }
}

// ========================================
// LOGOUT
// ========================================
function initLogout() {
  const logoutBtn = document.getElementById('logoutBtn');
  
  if (logoutBtn) {
    logoutBtn.addEventListener('click', function(e) {
      e.preventDefault();
      
      if (confirm('Tem certeza que deseja sair?')) {
        window.location.href = 'hub.php?action=logout';
      }
    });
  }
}