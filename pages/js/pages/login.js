// ============================================
// LOGIN.JS - VALIDAÇÃO E INTERATIVIDADE
// Sistema Pipo - Restaurante Institucional
// ============================================

(function() {
  'use strict';

  // ============================================
  // ELEMENTOS DO DOM
  // ============================================
  const loginForm = document.getElementById('loginForm');
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  const emailError = document.getElementById('emailError');
  const passwordError = document.getElementById('passwordError');
  const jsLoginError = document.getElementById('jsLoginError');
  const submitBtn = document.getElementById('submitBtn');
  const btnText = submitBtn.querySelector('.btn-text');
  const spinner = document.getElementById('loginSpinner');
  const togglePassword = document.getElementById('togglePassword');
  const eyeIcon = togglePassword.querySelector('.eye-icon');
  const eyeOffIcon = togglePassword.querySelector('.eye-off-icon');

  // ============================================
  // FUNÇÕES DE VALIDAÇÃO
  // ============================================

  /**
   * Valida formato de email
   */
  function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  /**
   * Valida campo de email
   */
  function validateEmail() {
    const email = emailInput.value.trim();
    
    // Limpar erro anterior
    emailError.textContent = '';
    emailInput.classList.remove('form-input-error');
    
    if (email === '') {
      emailError.textContent = 'O email é obrigatório.';
      emailInput.classList.add('form-input-error');
      return false;
    }
    
    if (!isValidEmail(email)) {
      emailError.textContent = 'Digite um email válido.';
      emailInput.classList.add('form-input-error');
      return false;
    }
    
    return true;
  }

  /**
   * Valida campo de senha
   */
  function validatePassword() {
    const password = passwordInput.value;
    
    // Limpar erro anterior
    passwordError.textContent = '';
    passwordInput.classList.remove('form-input-error');
    
    if (password === '') {
      passwordError.textContent = 'A senha é obrigatória.';
      passwordInput.classList.add('form-input-error');
      return false;
    }
    
    if (password.length < 6) {
      passwordError.textContent = 'A senha deve ter pelo menos 6 caracteres.';
      passwordInput.classList.add('form-input-error');
      return false;
    }
    
    return true;
  }

  /**
   * Limpa mensagem de erro geral
   */
  function clearGeneralError() {
    jsLoginError.textContent = '';
    jsLoginError.classList.add('hidden');
  }

  /**
   * Exibe mensagem de erro geral
   */
  function showGeneralError(message) {
    jsLoginError.textContent = message;
    jsLoginError.classList.remove('hidden');
  }

  /**
   * Mostra estado de loading no botão
   */
  function showLoading() {
    submitBtn.disabled = true;
    btnText.style.opacity = '0';
    spinner.classList.remove('hidden');
  }

  /**
   * Remove estado de loading do botão
   */
  function hideLoading() {
    submitBtn.disabled = false;
    btnText.style.opacity = '1';
    spinner.classList.add('hidden');
  }

  // ============================================
  // TOGGLE DE VISUALIZAÇÃO DE SENHA
  // ============================================
  
  togglePassword.addEventListener('click', function() {
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    
    // Alternar ícones
    if (type === 'text') {
      eyeIcon.classList.add('hidden');
      eyeOffIcon.classList.remove('hidden');
      togglePassword.setAttribute('aria-label', 'Ocultar senha');
    } else {
      eyeIcon.classList.remove('hidden');
      eyeOffIcon.classList.add('hidden');
      togglePassword.setAttribute('aria-label', 'Mostrar senha');
    }
  });

  // ============================================
  // VALIDAÇÃO EM TEMPO REAL
  // ============================================
  
  // Validar email ao sair do campo
  emailInput.addEventListener('blur', function() {
    validateEmail();
  });

  // Limpar erro ao digitar
  emailInput.addEventListener('input', function() {
    if (emailError.textContent !== '') {
      emailError.textContent = '';
      emailInput.classList.remove('form-input-error');
    }
    clearGeneralError();
  });

  // Validar senha ao sair do campo
  passwordInput.addEventListener('blur', function() {
    validatePassword();
  });

  // Limpar erro ao digitar
  passwordInput.addEventListener('input', function() {
    if (passwordError.textContent !== '') {
      passwordError.textContent = '';
      passwordInput.classList.remove('form-input-error');
    }
    clearGeneralError();
  });

  // ============================================
  // SUBMIT DO FORMULÁRIO
  // ============================================
  
  loginForm.addEventListener('submit', function(e) {
    // Limpar mensagens de erro
    clearGeneralError();
    
    // Validar campos
    const isEmailValid = validateEmail();
    const isPasswordValid = validatePassword();
    
    // Se algum campo for inválido, prevenir submit
    if (!isEmailValid || !isPasswordValid) {
      e.preventDefault();
      
      // Focar no primeiro campo com erro
      if (!isEmailValid) {
        emailInput.focus();
      } else if (!isPasswordValid) {
        passwordInput.focus();
      }
      
      return false;
    }
    
    // Mostrar loading
    showLoading();
    
    // Permitir o submit normal do formulário (PHP processará)
    return true;
  });

  // ============================================
  // INICIALIZAÇÃO
  // ============================================
  
  // Focar no campo email ao carregar
  if (emailInput && !emailInput.value) {
    emailInput.focus();
  }

  // Limpar mensagens de erro do PHP após 5 segundos
  const phpLoginError = document.getElementById('loginError');
  if (phpLoginError && !phpLoginError.classList.contains('hidden')) {
    setTimeout(function() {
      phpLoginError.style.transition = 'opacity 0.5s';
      phpLoginError.style.opacity = '0';
      setTimeout(function() {
        phpLoginError.classList.add('hidden');
      }, 500);
    }, 5000);
  }

})();