// ========================================
// MENUS.JS - JAVASCRIPT INTEGRADO
// SEM LOCALSTORAGE - APENAS VALIDAÇÕES
// ========================================

(function() {
  'use strict';

  // ========================================
  // ELEMENTOS DO DOM
  // ========================================
  const modal = document.getElementById('modalOverlay');
  const openModalBtn = document.getElementById('openModalBtn');
  const closeModalBtn = document.getElementById('closeModalBtn');
  const cancelBtn = document.getElementById('cancelBtn');
  const menuForm = document.getElementById('menuForm');
  const searchInput = document.getElementById('searchInput');
  const menusList = document.getElementById('menusList');
  const emptyState = document.getElementById('emptyState');
  const noResults = document.getElementById('noResults');
  
  // Campos do formulário
  const menuName = document.getElementById('menuName');
  const pratoA = document.getElementById('pratoA');
  const pratoB = document.getElementById('pratoB');
  
  // Elementos de erro
  const menuNameError = document.getElementById('menuNameError');
  const pratoAError = document.getElementById('pratoAError');
  const pratoBError = document.getElementById('pratoBError');
  const dishesError = document.getElementById('dishesError');

  // Sidebar
  const sidebar = document.getElementById('sidebar');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebarOverlay = document.getElementById('sidebarOverlay');
  const logoutBtn = document.getElementById('logoutBtn');

  // ========================================
  // MODAL - ABRIR/FECHAR
  // ========================================
  function openModal() {
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    resetForm();
  }

  function closeModal() {
    modal.classList.add('hidden');
    document.body.style.overflow = '';
    resetForm();
  }

  openModalBtn.addEventListener('click', openModal);
  closeModalBtn.addEventListener('click', closeModal);
  cancelBtn.addEventListener('click', closeModal);
  
  modal.addEventListener('click', function(e) {
    if (e.target === modal) {
      closeModal();
    }
  });

  // ========================================
  // SIDEBAR - TOGGLE MOBILE
  // ========================================
  sidebarToggle.addEventListener('click', function() {
    sidebar.classList.toggle('active');
    sidebarOverlay.classList.toggle('active');
  });

  sidebarOverlay.addEventListener('click', function() {
    sidebar.classList.remove('active');
    sidebarOverlay.classList.remove('active');
  });

  // ========================================
  // LOGOUT
  // ========================================
  logoutBtn.addEventListener('click', function() {
    if (confirm('Deseja realmente sair do sistema?')) {
      window.location.href = 'hub.php?action=logout';
    }
  });

  // ========================================
  // VALIDAÇÃO DO FORMULÁRIO
  // ========================================
  function validateField(field, errorElement, validations) {
    const value = field.value.trim();
    
    for (let validation of validations) {
      if (!validation.test(value)) {
        showError(errorElement, validation.message);
        return false;
      }
    }
    
    clearError(errorElement);
    return true;
  }

  function showError(element, message) {
    if (element) {
      element.textContent = message;
      element.style.display = 'block';
    }
  }

  function clearError(element) {
    if (element) {
      element.textContent = '';
      element.style.display = 'none';
    }
  }

  // Validação em tempo real
  if (menuName) {
    menuName.addEventListener('blur', function() {
      validateField(this, menuNameError, [
        {
          test: (v) => v.length > 0,
          message: 'O nome do cardápio é obrigatório'
        },
        {
          test: (v) => v.length >= 3,
          message: 'O nome deve ter no mínimo 3 caracteres'
        },
        {
          test: (v) => v.length <= 100,
          message: 'O nome deve ter no máximo 100 caracteres'
        }
      ]);
    });
  }

  // Validação dos selects de pratos
  if (pratoA) {
    pratoA.addEventListener('change', function() {
      if (this.value === '') {
        showError(pratoAError, 'Selecione o Prato A');
      } else {
        clearError(pratoAError);
        
        // Verificar se Prato A e B são diferentes
        if (pratoB && pratoB.value && this.value === pratoB.value) {
          showError(dishesError, 'Os pratos A e B não podem ser iguais');
        } else {
          clearError(dishesError);
        }
      }
    });
  }

  if (pratoB) {
    pratoB.addEventListener('change', function() {
      if (this.value === '') {
        showError(pratoBError, 'Selecione o Prato B');
      } else {
        clearError(pratoBError);
        
        // Verificar se Prato A e B são diferentes
        if (pratoA && pratoA.value && this.value === pratoA.value) {
          showError(dishesError, 'Os pratos A e B não podem ser iguais');
        } else {
          clearError(dishesError);
        }
      }
    });
  }

  // ========================================
  // SUBMIT DO FORMULÁRIO
  // ========================================
  if (menuForm) {
    menuForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Validar nome do cardápio
      const isNameValid = validateField(menuName, menuNameError, [
        {test: (v) => v.length > 0, message: 'O nome do cardápio é obrigatório'},
        {test: (v) => v.length >= 3, message: 'O nome deve ter no mínimo 3 caracteres'},
        {test: (v) => v.length <= 100, message: 'O nome deve ter no máximo 100 caracteres'}
      ]);

      // Validar Prato A
      let isPratoAValid = true;
      if (pratoA) {
        if (pratoA.value === '') {
          showError(pratoAError, 'Selecione o Prato A');
          isPratoAValid = false;
        } else {
          clearError(pratoAError);
        }
      }

      // Validar Prato B
      let isPratoBValid = true;
      if (pratoB) {
        if (pratoB.value === '') {
          showError(pratoBError, 'Selecione o Prato B');
          isPratoBValid = false;
        } else {
          clearError(pratoBError);
        }
      }

      // Verificar se os pratos são diferentes
      let areDishesValid = true;
      if (pratoA && pratoB && pratoA.value && pratoB.value) {
        if (pratoA.value === pratoB.value) {
          showError(dishesError, 'Os pratos A e B não podem ser iguais');
          areDishesValid = false;
        } else {
          clearError(dishesError);
        }
      }

      // Se todos os campos forem válidos, envia o formulário
      if (isNameValid && isPratoAValid && isPratoBValid && areDishesValid) {
        this.submit();
      } else {
        // Focar no primeiro campo com erro
        if (!isNameValid) menuName.focus();
        else if (!isPratoAValid) pratoA.focus();
        else if (!isPratoBValid) pratoB.focus();
      }
    });
  }

  // ========================================
  // RESET DO FORMULÁRIO
  // ========================================
  function resetForm() {
    if (menuForm) {
      menuForm.reset();
    }
    
    // Limpar erros
    clearError(menuNameError);
    clearError(pratoAError);
    clearError(pratoBError);
    clearError(dishesError);
  }

  // ========================================
  // BUSCA DE CARDÁPIOS
  // ========================================
  if (searchInput && menusList) {
    searchInput.addEventListener('input', function() {
      const searchTerm = this.value.toLowerCase().trim();
      const menuCards = menusList.querySelectorAll('.menu-card');
      let visibleCount = 0;

      menuCards.forEach(function(card) {
        const menuName = card.getAttribute('data-menu-name').toLowerCase();
        
        if (menuName.includes(searchTerm)) {
          card.style.display = '';
          visibleCount++;
        } else {
          card.style.display = 'none';
        }
      });

      // Mostrar/ocultar mensagens de estado
      if (visibleCount === 0 && searchTerm !== '') {
        noResults.classList.remove('hidden');
        emptyState.classList.add('hidden');
      } else {
        noResults.classList.add('hidden');
        if (menuCards.length === 0) {
          emptyState.classList.remove('hidden');
        } else {
          emptyState.classList.add('hidden');
        }
      }
    });
  }

})();

// ========================================
// FUNÇÕES GLOBAIS (chamadas pelo PHP)
// ========================================

function editMenu(id) {
  window.location.href = 'alterCardapio.php?id=' + id;
}

function confirmDelete(id, name) {
  if (confirm('Deseja realmente excluir o cardápio "' + name + '"?\n\nEsta ação não pode ser desfeita.')) {
    window.location.href = 'ctrlHub.php?class=cardapio&action=delete&id=' + id;
  }
}