// profile.js - Validação e interações da página de perfil

document.addEventListener('DOMContentLoaded', function() {
  
  // ===== SIDEBAR =====
  const sidebar = document.getElementById('sidebar');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebarOverlay = document.getElementById('sidebarOverlay');

  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', function() {
      sidebar.classList.toggle('active');
      sidebarOverlay.classList.toggle('active');
    });
  }

  if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', function() {
      sidebar.classList.remove('active');
      sidebarOverlay.classList.remove('active');
    });
  }

  // ===== FORMULÁRIO DE INFORMAÇÕES PESSOAIS =====
  const profileForm = document.getElementById('profileForm');
  
  if (profileForm) {
    profileForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Limpar erros anteriores
      clearErrors();
      
      const codigo = document.getElementById('codigo_admin');
      const nome = document.getElementById('nome_admin');
      const email = document.getElementById('email_admin');
      
      let isValid = true;
      
      // Validar código (somente números)
      if (!codigo.value.trim()) {
        showError('codigoError', 'O código é obrigatório');
        isValid = false;
      } else if (!/^\d+$/.test(codigo.value.trim())) {
        showError('codigoError', 'O código deve conter apenas números');
        isValid = false;
      }
      
      // Validar nome
      if (!nome.value.trim()) {
        showError('nomeError', 'O nome é obrigatório');
        isValid = false;
      } else if (nome.value.trim().length < 3) {
        showError('nomeError', 'O nome deve ter no mínimo 3 caracteres');
        isValid = false;
      }
      
      // Validar email
      if (!email.value.trim()) {
        showError('emailError', 'O email é obrigatório');
        isValid = false;
      } else if (!isValidEmail(email.value.trim())) {
        showError('emailError', 'Digite um email válido');
        isValid = false;
      }
      
      if (isValid) {
        profileForm.submit();
      }
    });
  }
  
  // ===== FORMULÁRIO DE SENHA =====
  const passwordForm = document.getElementById('passwordForm');
  
  if (passwordForm) {
    passwordForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Limpar erros anteriores
      clearPasswordErrors();
      
      const senha = document.getElementById('senha_admin');
      const confirmar = document.getElementById('confirmar_senha');
      
      let isValid = true;
      
      // Validar senha
      if (!senha.value) {
        showError('senhaError', 'A senha é obrigatória');
        isValid = false;
      } else if (senha.value.length < 6) {
        showError('senhaError', 'A senha deve ter no mínimo 6 caracteres');
        isValid = false;
      }
      
      // Validar confirmação
      if (!confirmar.value) {
        showError('confirmarSenhaError', 'Confirme a nova senha');
        isValid = false;
      } else if (senha.value !== confirmar.value) {
        showError('confirmarSenhaError', 'As senhas não coincidem');
        isValid = false;
      }
      
      if (isValid) {
        passwordForm.submit();
      }
    });
  }
  
  // ===== TOGGLE DE SENHA =====
  const passwordToggles = document.querySelectorAll('.password-toggle');
  
  passwordToggles.forEach(function(toggle) {
    toggle.addEventListener('click', function() {
      const targetId = this.getAttribute('data-target');
      const input = document.getElementById(targetId);
      const eyeIcon = this.querySelector('.eye-icon');
      const eyeOffIcon = this.querySelector('.eye-off-icon');
      
      if (input.type === 'password') {
        input.type = 'text';
        eyeIcon.classList.add('hidden');
        eyeOffIcon.classList.remove('hidden');
      } else {
        input.type = 'password';
        eyeIcon.classList.remove('hidden');
        eyeOffIcon.classList.add('hidden');
      }
    });
  });
  
  // ===== FUNÇÕES AUXILIARES =====
  
  function showError(elementId, message) {
    const errorElement = document.getElementById(elementId);
    if (errorElement) {
      errorElement.textContent = message;
      errorElement.style.display = 'block';
    }
  }
  
  function clearErrors() {
    const errors = ['codigoError', 'nomeError', 'emailError'];
    errors.forEach(function(id) {
      const el = document.getElementById(id);
      if (el) {
        el.textContent = '';
        el.style.display = 'none';
      }
    });
  }
  
  function clearPasswordErrors() {
    const errors = ['senhaError', 'confirmarSenhaError'];
    errors.forEach(function(id) {
      const el = document.getElementById(id);
      if (el) {
        el.textContent = '';
        el.style.display = 'none';
      }
    });
  }
  
  function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
  }
  
});