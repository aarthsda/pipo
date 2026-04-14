// ========================================
// QUEUES.JS - JAVASCRIPT NOVO (SEM LOCALSTORAGE)
// Responsável por: validação, interações UI e renderização
// ========================================

// Variáveis globais
let filas = [];
let cardapios = [];
let currentFilter = 'all';

// ========================================
// INICIALIZAÇÃO
// ========================================
document.addEventListener('DOMContentLoaded', function() {
  // Carregar dados vindos do PHP
  loadDataFromPHP();
  
  // Inicializar componentes
  initSidebar();
  initModal();
  initTabs();
  initLogout();
  
  // Renderizar filas
  renderQueues();
});

// ========================================
// CARREGAR DADOS DO PHP
// ========================================
function loadDataFromPHP() {
  // Dados injetados pelo PHP via <script>
  if (window.filasData) {
    filas = window.filasData;
  }
  if (window.cardapiosData) {
    cardapios = window.cardapiosData;
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
  const queueForm = document.getElementById('queueForm');

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

  // Validação do formulário antes de enviar
  if (queueForm) {
    queueForm.addEventListener('submit', function(e) {
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

  // Data da fila
  const dataFila = document.getElementById('data_fila');
  if (!dataFila.value) {
    showError('dataFilaError', 'Data da fila é obrigatória');
    isValid = false;
  }

  // Cardápio
  const menu = document.getElementById('cardapio_fila');
  if (!menu.value) {
    showError('menuIdError', 'Selecione um cardápio');
    isValid = false;
  }

  // Capacidade
  const capacity = document.getElementById('capacidade_maxima_fila');
  if (!capacity.value || capacity.value < 1) {
    showError('totalCapacityError', 'Capacidade deve ser no mínimo 1');
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
  const form = document.getElementById('queueForm');
  if (form) {
    form.reset();
    clearErrors();
  }
}

// ========================================
// TABS (FILTROS)
// ========================================
function initTabs() {
  const tabButtons = document.querySelectorAll('.tabs-item');
  
  tabButtons.forEach(button => {
    button.addEventListener('click', function() {
      // Remover active de todos
      tabButtons.forEach(btn => btn.classList.remove('active'));
      
      // Adicionar active no clicado
      this.classList.add('active');
      
      // Atualizar filtro
      currentFilter = this.getAttribute('data-status');
      
      // Re-renderizar filas
      renderQueues();
    });
  });
}

// ========================================
// RENDERIZAÇÃO DE FILAS
// ========================================
function renderQueues() {
  const queuesList = document.getElementById('queuesList');
  const emptyState = document.getElementById('emptyState');

  if (!queuesList) return;

  // Filtrar filas
  const filteredQueues = filterQueues();

  // Se não houver filas
  if (filteredQueues.length === 0) {
    queuesList.innerHTML = '';
    emptyState.classList.remove('hidden');
    return;
  }

  // Renderizar filas
  emptyState.classList.add('hidden');
  queuesList.innerHTML = filteredQueues.map(fila => createQueueCard(fila)).join('');
}

function filterQueues() {
  if (currentFilter === 'all') {
    return filas;
  }
  
  return filas.filter(fila => fila.status_fila === currentFilter);
}

function createQueueCard(fila) {
  const date = formatDate(fila.data_fila);
  const status = getStatusBadge(fila.status_fila);
  const cardapio = getCardapioName(fila.cardapio_fila);
  const ocupacao = calculateOcupacao(fila);

  return `
    <div class="queue-card" data-queue-id="${fila.id_fila}">
      <div class="queue-card-header">
        <div class="queue-card-title">
          <h3>Fila - ${date}</h3>
          ${status}
        </div>
        <div class="queue-card-actions">
          <button class="btn-icon" onclick="viewQueue(${fila.id_fila})" title="Visualizar">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
              <circle cx="12" cy="12" r="3"/>
            </svg>
          </button>
          <button class="btn-icon" onclick="deleteQueue(${fila.id_fila})" title="Excluir">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 6h18"/>
              <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
              <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
            </svg>
          </button>
        </div>
      </div>

      <div class="queue-card-body">
        <div class="queue-card-info">
          <div class="info-item">
            <span class="info-label">Cardápio:</span>
            <span class="info-value">${cardapio}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Capacidade:</span>
            <span class="info-value">${fila.capacidade_maxima_fila} pessoas</span>
          </div>
          <div class="info-item">
            <span class="info-label">Ocupação:</span>
            <span class="info-value">${ocupacao}</span>
          </div>
        </div>
      </div>
    </div>
  `;
}

// ========================================
// FUNÇÕES AUXILIARES
// ========================================
function formatDate(dateString) {
  if (!dateString) return 'Data não definida';
  
  const date = new Date(dateString + 'T00:00:00');
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  
  return `${day}/${month}/${year}`;
}

function getStatusBadge(status) {
  const statusMap = {
    'ativa': { label: 'Agendada', class: 'badge-scheduled' },
    'em_andamento': { label: 'Em Andamento', class: 'badge-active' },
    'finalizada': { label: 'Finalizada', class: 'badge-finished' }
  };

  const statusInfo = statusMap[status] || { label: 'Desconhecido', class: 'badge-scheduled' };
  
  return `<span class="badge ${statusInfo.class}">${statusInfo.label}</span>`;
}

function getCardapioName(cardapioId) {
  const cardapio = cardapios.find(c => c.id_cardapio == cardapioId);
  return cardapio ? cardapio.nome_cardapio : 'Não definido';
}

function calculateOcupacao(fila) {
  const ocupadas = parseInt(fila.reservas_em_fila) || 0;
  const total = parseInt(fila.capacidade_maxima_fila) || 1;
  const percentual = Math.round((ocupadas / total) * 100);
  
  return `${ocupadas}/${total} (${percentual}%)`;
}

// ========================================
// AÇÕES DE FILA
// ========================================
function viewQueue(id) {
  // Redirecionar para página de detalhes (a ser implementada)
  window.location.href = `viewFila.php?id=${id}`;
}

function deleteQueue(id) {
  if (confirm('Tem certeza que deseja excluir esta fila?')) {
    window.location.href = `ctrlHub.php?class=fila&action=delete&id=${id}`;
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
        // Limpar cookies via PHP
        window.location.href = 'hub.php?action=logout';
      }
    });
  }
}