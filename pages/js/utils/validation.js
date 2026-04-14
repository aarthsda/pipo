/**
 * ==========================================================================
 * VALIDATION UTILITY - Validações de formulários e dados
 * ==========================================================================
 */

const Validator = {
  /**
   * Valida formato de email
   * @param {string} email - Email a ser validado
   * @returns {boolean} - True se válido
   */
  isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  },

  /**
   * Valida senha forte
   * @param {string} password - Senha a ser validada
   * @returns {Object} - {valid: boolean, errors: Array}
   */
  isValidPassword(password) {
    const errors = [];
    
    if (password.length < 8) {
      errors.push('A senha deve ter no mínimo 8 caracteres');
    }
    
    if (!/[A-Z]/.test(password)) {
      errors.push('A senha deve conter pelo menos uma letra maiúscula');
    }
    
    if (!/[a-z]/.test(password)) {
      errors.push('A senha deve conter pelo menos uma letra minúscula');
    }
    
    if (!/[0-9]/.test(password)) {
      errors.push('A senha deve conter pelo menos um número');
    }
    
    if (!/[!@#$%^&*]/.test(password)) {
      errors.push('A senha deve conter pelo menos um caractere especial (!@#$%^&*)');
    }
    
    return {
      valid: errors.length === 0,
      errors: errors
    };
  },

  /**
   * Valida telefone brasileiro
   * @param {string} phone - Telefone a ser validado
   * @returns {boolean} - True se válido
   */
  isValidPhone(phone) {
    // Remove caracteres não numéricos
    const cleaned = phone.replace(/\D/g, '');
    
    // Verifica se tem 10 ou 11 dígitos (com ou sem DDD)
    if (cleaned.length < 10 || cleaned.length > 11) {
      return false;
    }
    
    // Formato: (99) 99999-9999 ou (99) 9999-9999
    const phoneRegex = /^\(?[1-9]{2}\)?\s?9?[0-9]{4}-?[0-9]{4}$/;
    return phoneRegex.test(phone);
  },

  /**
   * Valida se campo está vazio
   * @param {string} value - Valor a ser validado
   * @returns {boolean} - True se não vazio
   */
  isNotEmpty(value) {
    return value !== null && value !== undefined && value.trim() !== '';
  },

  /**
   * Valida tamanho mínimo de string
   * @param {string} value - Valor a ser validado
   * @param {number} min - Tamanho mínimo
   * @returns {boolean} - True se válido
   */
  minLength(value, min) {
    return value && value.length >= min;
  },

  /**
   * Valida tamanho máximo de string
   * @param {string} value - Valor a ser validado
   * @param {number} max - Tamanho máximo
   * @returns {boolean} - True se válido
   */
  maxLength(value, max) {
    return value && value.length <= max;
  },

  /**
   * Valida se número está em um range
   * @param {number} value - Valor a ser validado
   * @param {number} min - Valor mínimo
   * @param {number} max - Valor máximo
   * @returns {boolean} - True se válido
   */
  inRange(value, min, max) {
    const num = Number(value);
    return !isNaN(num) && num >= min && num <= max;
  },

  /**
   * Valida se é um número válido
   * @param {*} value - Valor a ser validado
   * @returns {boolean} - True se é número
   */
  isNumber(value) {
    return !isNaN(value) && !isNaN(parseFloat(value));
  },

  /**
   * Valida data futura
   * @param {string} date - Data no formato ISO ou compatível
   * @returns {boolean} - True se é futura
   */
  isFutureDate(date) {
    const inputDate = new Date(date);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return inputDate > today;
  },

  /**
   * Valida se uma data é anterior a outra
   * @param {string} date1 - Primeira data
   * @param {string} date2 - Segunda data
   * @returns {boolean} - True se date1 < date2
   */
  isDateBefore(date1, date2) {
    return new Date(date1) < new Date(date2);
  },

  /**
   * Valida URL
   * @param {string} url - URL a ser validada
   * @returns {boolean} - True se válida
   */
  isValidURL(url) {
    try {
      new URL(url);
      return true;
    } catch {
      return false;
    }
  },

  /**
   * Valida arquivo de imagem
   * @param {File} file - Arquivo a ser validado
   * @param {number} maxSizeMB - Tamanho máximo em MB
   * @returns {Object} - {valid: boolean, error: string}
   */
  isValidImage(file, maxSizeMB = 5) {
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    
    if (!file) {
      return { valid: false, error: 'Nenhum arquivo selecionado' };
    }
    
    if (!validTypes.includes(file.type)) {
      return { valid: false, error: 'Formato de imagem inválido. Use JPG, PNG, GIF ou WebP' };
    }
    
    const maxSize = maxSizeMB * 1024 * 1024; // Converte MB para bytes
    if (file.size > maxSize) {
      return { valid: false, error: `Arquivo muito grande. Tamanho máximo: ${maxSizeMB}MB` };
    }
    
    return { valid: true, error: null };
  }
};

/**
 * ==========================================================================
 * FORM VALIDATOR - Validação de formulários HTML
 * ==========================================================================
 */

class FormValidator {
  constructor(formElement) {
    this.form = formElement;
    this.errors = {};
  }

  /**
   * Valida todos os campos do formulário
   * @returns {boolean} - True se todos os campos são válidos
   */
  validate() {
    this.errors = {};
    const inputs = this.form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
      const value = input.value;
      const name = input.name;
      const type = input.type;
      
      // Validação de campos obrigatórios
      if (input.hasAttribute('required') && !Validator.isNotEmpty(value)) {
        this.addError(name, 'Este campo é obrigatório');
        return;
      }
      
      // Se o campo está vazio e não é obrigatório, pula validações
      if (!Validator.isNotEmpty(value)) return;
      
      // Validações específicas por tipo
      switch (type) {
        case 'email':
          if (!Validator.isValidEmail(value)) {
            this.addError(name, 'Email inválido');
          }
          break;
          
        case 'tel':
          if (!Validator.isValidPhone(value)) {
            this.addError(name, 'Telefone inválido');
          }
          break;
          
        case 'number':
          const min = input.getAttribute('min');
          const max = input.getAttribute('max');
          
          if (!Validator.isNumber(value)) {
            this.addError(name, 'Valor deve ser um número');
          } else if (min !== null && Number(value) < Number(min)) {
            this.addError(name, `Valor mínimo: ${min}`);
          } else if (max !== null && Number(value) > Number(max)) {
            this.addError(name, `Valor máximo: ${max}`);
          }
          break;
          
        case 'password':
          const minLength = input.getAttribute('minlength') || 8;
          if (value.length < minLength) {
            this.addError(name, `Senha deve ter no mínimo ${minLength} caracteres`);
          }
          break;
          
        case 'date':
        case 'datetime-local':
          if (input.hasAttribute('data-future') && !Validator.isFutureDate(value)) {
            this.addError(name, 'Data deve ser futura');
          }
          break;
      }
      
      // Validações de comprimento
      const minlength = input.getAttribute('minlength');
      const maxlength = input.getAttribute('maxlength');
      
      if (minlength && !Validator.minLength(value, Number(minlength))) {
        this.addError(name, `Mínimo de ${minlength} caracteres`);
      }
      
      if (maxlength && !Validator.maxLength(value, Number(maxlength))) {
        this.addError(name, `Máximo de ${maxlength} caracteres`);
      }
    });
    
    return Object.keys(this.errors).length === 0;
  }

  /**
   * Adiciona um erro de validação
   * @param {string} fieldName - Nome do campo
   * @param {string} message - Mensagem de erro
   */
  addError(fieldName, message) {
    if (!this.errors[fieldName]) {
      this.errors[fieldName] = [];
    }
    this.errors[fieldName].push(message);
  }

  /**
   * Retorna todos os erros
   * @returns {Object} - Objeto com erros por campo
   */
  getErrors() {
    return this.errors;
  }

  /**
   * Retorna erros de um campo específico
   * @param {string} fieldName - Nome do campo
   * @returns {Array} - Array de erros
   */
  getFieldErrors(fieldName) {
    return this.errors[fieldName] || [];
  }

  /**
   * Exibe erros no formulário
   */
  displayErrors() {
    // Remove erros anteriores
    this.clearErrors();
    
    // Adiciona novos erros
    Object.keys(this.errors).forEach(fieldName => {
      const input = this.form.querySelector(`[name="${fieldName}"]`);
      if (!input) return;
      
      // Adiciona classe de erro ao input
      input.classList.add('is-invalid');
      
      // Adiciona animação de shake
      input.classList.add('shake');
      setTimeout(() => input.classList.remove('shake'), 400);
      
      // Exibe mensagem de erro
      const errorElement = document.getElementById(`${fieldName}Error`);
      if (errorElement) {
        errorElement.textContent = this.errors[fieldName][0];
      }
    });
  }

  /**
   * Limpa todos os erros visuais do formulário
   */
  clearErrors() {
    const inputs = this.form.querySelectorAll('.is-invalid');
    inputs.forEach(input => input.classList.remove('is-invalid'));
    
    const errorElements = this.form.querySelectorAll('.form-error');
    errorElements.forEach(el => el.textContent = '');
  }

  /**
   * Valida um campo específico em tempo real
   * @param {HTMLElement} input - Elemento input
   */
  validateField(input) {
    const tempErrors = this.errors;
    this.errors = {};
    
    // Simula validação apenas deste campo
    const value = input.value;
    const name = input.name;
    const type = input.type;
    
    if (input.hasAttribute('required') && !Validator.isNotEmpty(value)) {
      this.addError(name, 'Este campo é obrigatório');
    } else if (type === 'email' && value && !Validator.isValidEmail(value)) {
      this.addError(name, 'Email inválido');
    } else if (type === 'tel' && value && !Validator.isValidPhone(value)) {
      this.addError(name, 'Telefone inválido');
    }
    
    // Exibe ou remove erro do campo
    if (this.errors[name]) {
      input.classList.add('is-invalid');
      const errorElement = document.getElementById(`${name}Error`);
      if (errorElement) {
        errorElement.textContent = this.errors[name][0];
      }
    } else {
      input.classList.remove('is-invalid');
      const errorElement = document.getElementById(`${name}Error`);
      if (errorElement) {
        errorElement.textContent = '';
      }
    }
    
    // Restaura erros temporários
    this.errors = tempErrors;
  }
}