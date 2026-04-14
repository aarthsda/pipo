// ========================================
// DISHES.JS - JAVASCRIPT INTEGRADO
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
  const dishForm = document.getElementById('dishForm');
  const searchInput = document.getElementById('searchInput');
  const dishesGrid = document.getElementById('dishesGrid');
  const emptyState = document.getElementById('emptyState');
  const noResults = document.getElementById('noResults');
  
  // Campos do formulário
  const dishName = document.getElementById('dishName');
  const dishDescription = document.getElementById('dishDescription');
  const dishQuantity = document.getElementById('dishQuantity');
  const dishImage = document.getElementById('dishImage');
  const charCount = document.getElementById('charCount');
  const imagePreview = document.getElementById('imagePreview');
  const previewImg = document.getElementById('previewImg');
  const imageUploadLabel = document.getElementById('imageUploadLabel');
  const removeImageBtn = document.getElementById('removeImageBtn');

  // Elementos de erro
  const dishNameError = document.getElementById('dishNameError');
  const dishDescriptionError = document.getElementById('dishDescriptionError');
  const dishQuantityError = document.getElementById('dishQuantityError');
  const dishImageError = document.getElementById('dishImageError');

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
  // CONTADOR DE CARACTERES
  // ========================================
  dishDescription.addEventListener('input', function() {
    const count = this.value.length;
    charCount.textContent = count;
    
    if (count > 500) {
      charCount.style.color = 'var(--color-error)';
    } else {
      charCount.style.color = '';
    }
  });

  // ========================================
  // PREVIEW DE IMAGEM
  // ========================================
  dishImage.addEventListener('change', function(e) {
    const file = e.target.files[0];
    
    if (!file) return;

    // Validar tamanho (5MB)
    if (file.size > 5 * 1024 * 1024) {
      showError(dishImageError, 'A imagem deve ter no máximo 5MB');
      this.value = '';
      return;
    }

    // Validar tipo
    if (!file.type.startsWith('image/')) {
      showError(dishImageError, 'Apenas imagens são permitidas');
      this.value = '';
      return;
    }

    // Mostrar preview
    const reader = new FileReader();
    reader.onload = function(e) {
      previewImg.src = e.target.result;
      imagePreview.classList.remove('hidden');
      imageUploadLabel.classList.add('hidden');
    };
    reader.readAsDataURL(file);
    
    clearError(dishImageError);
  });

  // Remover imagem
  removeImageBtn.addEventListener('click', function() {
    dishImage.value = '';
    imagePreview.classList.add('hidden');
    imageUploadLabel.classList.remove('hidden');
    previewImg.src = '';
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
    element.textContent = message;
    element.style.display = 'block';
  }

  function clearError(element) {
    element.textContent = '';
    element.style.display = 'none';
  }

  // Validação em tempo real
  dishName.addEventListener('blur', function() {
    validateField(this, dishNameError, [
      {
        test: (v) => v.length > 0,
        message: 'O nome do prato é obrigatório'
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

  dishDescription.addEventListener('blur', function() {
    validateField(this, dishDescriptionError, [
      {
        test: (v) => v.length > 0,
        message: 'A descrição é obrigatória'
      },
      {
        test: (v) => v.length <= 500,
        message: 'A descrição deve ter no máximo 500 caracteres'
      }
    ]);
  });

  dishQuantity.addEventListener('blur', function() {
    validateField(this, dishQuantityError, [
      {
        test: (v) => v.length > 0,
        message: 'A quantidade é obrigatória'
      },
      {
        test: (v) => parseInt(v) >= 1,
        message: 'A quantidade deve ser no mínimo 1'
      },
      {
        test: (v) => parseInt(v) <= 9999,
        message: 'A quantidade deve ser no máximo 9999'
      }
    ]);
  });

  // ========================================
  // SUBMIT DO FORMULÁRIO
  // ========================================
  dishForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validar todos os campos
    const isNameValid = validateField(dishName, dishNameError, [
      {test: (v) => v.length > 0, message: 'O nome do prato é obrigatório'},
      {test: (v) => v.length >= 3, message: 'O nome deve ter no mínimo 3 caracteres'},
      {test: (v) => v.length <= 100, message: 'O nome deve ter no máximo 100 caracteres'}
    ]);

    const isDescriptionValid = validateField(dishDescription, dishDescriptionError, [
      {test: (v) => v.length > 0, message: 'A descrição é obrigatória'},
      {test: (v) => v.length <= 500, message: 'A descrição deve ter no máximo 500 caracteres'}
    ]);

    const isQuantityValid = validateField(dishQuantity, dishQuantityError, [
      {test: (v) => v.length > 0, message: 'A quantidade é obrigatória'},
      {test: (v) => parseInt(v) >= 1, message: 'A quantidade deve ser no mínimo 1'},
      {test: (v) => parseInt(v) <= 9999, message: 'A quantidade deve ser no máximo 9999'}
    ]);

    const isImageValid = dishImage.files.length > 0;
    if (!isImageValid) {
      showError(dishImageError, 'A imagem do prato é obrigatória');
    } else {
      clearError(dishImageError);
    }

    // Se todos os campos forem válidos, envia o formulário
    if (isNameValid && isDescriptionValid && isQuantityValid && isImageValid) {
      this.submit();
    } else {
      // Focar no primeiro campo com erro
      if (!isNameValid) dishName.focus();
      else if (!isDescriptionValid) dishDescription.focus();
      else if (!isQuantityValid) dishQuantity.focus();
      else if (!isImageValid) dishImage.focus();
    }
  });

  // ========================================
  // RESET DO FORMULÁRIO
  // ========================================
  function resetForm() {
    dishForm.reset();
    charCount.textContent = '0';
    imagePreview.classList.add('hidden');
    imageUploadLabel.classList.remove('hidden');
    previewImg.src = '';
    
    // Limpar erros
    clearError(dishNameError);
    clearError(dishDescriptionError);
    clearError(dishQuantityError);
    clearError(dishImageError);
  }

  // ========================================
  // BUSCA DE PRATOS
  // ========================================
  searchInput.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase().trim();
    const dishCards = dishesGrid.querySelectorAll('.dish-card');
    let visibleCount = 0;

    dishCards.forEach(function(card) {
      const dishName = card.getAttribute('data-dish-name').toLowerCase();
      
      if (dishName.includes(searchTerm)) {
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
      if (dishCards.length === 0) {
        emptyState.classList.remove('hidden');
      } else {
        emptyState.classList.add('hidden');
      }
    }
  });

})();

// ========================================
// FUNÇÕES GLOBAIS (chamadas pelo PHP)
// ========================================

function editDish(id) {
  window.location.href = 'alterPrato.php?id=' + id;
}

function confirmDelete(id, name) {
  if (confirm('Deseja realmente excluir o prato "' + name + '"?\n\nEsta ação não pode ser desfeita.')) {
    window.location.href = 'ctrlHub.php?class=prato&action=delete&id=' + id;
  }
}