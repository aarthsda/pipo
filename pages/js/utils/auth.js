/**
 * ==========================================================================
 * AUTH UTILITY - Autenticação e gerenciamento de sessão
 * ==========================================================================
 */

const Auth = {
  /**
   * Chave da sessão no localStorage
   */
  SESSION_KEY: 'session',

  /**
   * Tempo de expiração da sessão em milissegundos (2 horas)
   */
  SESSION_TIMEOUT: 2 * 60 * 60 * 1000,

  /**
   * Realiza login do usuário
   * @param {string} email - Email do usuário
   * @param {string} password - Senha do usuário
   * @param {boolean} rememberMe - Se deve manter a sessão
   * @returns {Object} - {success: boolean, message: string, user: Object}
   */
  login(email, password, rememberMe = false) {
    // Busca o usuário administrador
    const adminUser = Storage.get('adminUser');
    
    if (!adminUser) {
      return {
        success: false,
        message: 'Sistema não inicializado corretamente',
        user: null
      };
    }

    // Valida credenciais
    if (email !== adminUser.email) {
      return {
        success: false,
        message: 'Email não encontrado',
        user: null
      };
    }

    // Verifica senha (hash simples - em produção usar bcrypt)
    const hashedPassword = btoa(password);
    if (hashedPassword !== adminUser.password) {
      return {
        success: false,
        message: 'Senha incorreta',
        user: null
      };
    }

    // Cria sessão
    const session = {
      user: {
        id: adminUser.id,
        email: adminUser.email,
        name: adminUser.name,
        role: adminUser.role
      },
      loginTime: new Date().toISOString(),
      expiresAt: new Date(Date.now() + this.SESSION_TIMEOUT).toISOString(),
      rememberMe: rememberMe
    };

    // Salva sessão
    Storage.set(this.SESSION_KEY, session);

    // Log de auditoria
    this.logActivity('login', adminUser.id);

    return {
      success: true,
      message: 'Login realizado com sucesso',
      user: session.user
    };
  },

  /**
   * Realiza logout do usuário
   * @returns {boolean} - Sucesso da operação
   */
  logout() {
    const session = this.getSession();
    
    if (session) {
      // Log de auditoria
      this.logActivity('logout', session.user.id);
    }

    // Remove sessão
    Storage.remove(this.SESSION_KEY);
    
    return true;
  },

  /**
   * Retorna a sessão atual
   * @returns {Object|null} - Sessão atual ou null
   */
  getSession() {
    const session = Storage.get(this.SESSION_KEY);
    
    if (!session) return null;

    // Verifica se a sessão expirou
    if (!session.rememberMe && new Date(session.expiresAt) < new Date()) {
      this.logout();
      return null;
    }

    return session;
  },

  /**
   * Retorna o usuário atual da sessão
   * @returns {Object|null} - Usuário atual ou null
   */
  getCurrentUser() {
    const session = this.getSession();
    return session ? session.user : null;
  },

  /**
   * Verifica se o usuário está autenticado
   * @returns {boolean} - True se autenticado
   */
  isAuthenticated() {
    return this.getSession() !== null;
  },

  /**
   * Verifica se o usuário tem permissão (role)
   * @param {string} requiredRole - Role necessária
   * @returns {boolean} - True se tem permissão
   */
  hasRole(requiredRole) {
    const user = this.getCurrentUser();
    return user && user.role === requiredRole;
  },

  /**
   * Atualiza a senha do usuário
   * @param {string} currentPassword - Senha atual
   * @param {string} newPassword - Nova senha
   * @returns {Object} - {success: boolean, message: string}
   */
  updatePassword(currentPassword, newPassword) {
    const adminUser = Storage.get('adminUser');
    const hashedCurrentPassword = btoa(currentPassword);

    // Verifica senha atual
    if (hashedCurrentPassword !== adminUser.password) {
      return {
        success: false,
        message: 'Senha atual incorreta'
      };
    }

    // Atualiza senha
    adminUser.password = btoa(newPassword);
    adminUser.updatedAt = new Date().toISOString();
    Storage.set('adminUser', adminUser);

    // Atualiza sessão
    const session = this.getSession();
    if (session) {
      Storage.set(this.SESSION_KEY, session);
    }

    // Log de auditoria
    this.logActivity('password_change', adminUser.id);

    return {
      success: true,
      message: 'Senha atualizada com sucesso'
    };
  },

  /**
   * Atualiza informações do perfil
   * @param {Object} updates - Dados a serem atualizados
   * @returns {Object} - {success: boolean, message: string, user: Object}
   */
  updateProfile(updates) {
    const adminUser = Storage.get('adminUser');
    
    // Atualiza apenas campos permitidos
    const allowedFields = ['name', 'email'];
    const filteredUpdates = {};
    
    allowedFields.forEach(field => {
      if (updates[field] !== undefined) {
        filteredUpdates[field] = updates[field];
      }
    });

    // Aplica atualizações
    Object.assign(adminUser, filteredUpdates);
    adminUser.updatedAt = new Date().toISOString();
    Storage.set('adminUser', adminUser);

    // Atualiza sessão
    const session = this.getSession();
    if (session) {
      Object.assign(session.user, filteredUpdates);
      Storage.set(this.SESSION_KEY, session);
    }

    // Log de auditoria
    this.logActivity('profile_update', adminUser.id);

    return {
      success: true,
      message: 'Perfil atualizado com sucesso',
      user: session.user
    };
  },

  /**
   * Exclui a conta do usuário
   * @returns {Object} - {success: boolean, message: string}
   */
  deleteAccount() {
    const session = this.getSession();
    
    if (!session) {
      return {
        success: false,
        message: 'Usuário não autenticado'
      };
    }

    // Log de auditoria
    this.logActivity('account_deleted', session.user.id);

    // Remove todos os dados
    Storage.clear();

    return {
      success: true,
      message: 'Conta excluída com sucesso'
    };
  },

  /**
   * Renova a sessão (atualiza tempo de expiração)
   */
  renewSession() {
    const session = this.getSession();
    
    if (session && !session.rememberMe) {
      session.expiresAt = new Date(Date.now() + this.SESSION_TIMEOUT).toISOString();
      Storage.set(this.SESSION_KEY, session);
    }
  },

  /**
   * Registra atividade do usuário para auditoria
   * @param {string} action - Ação realizada
   * @param {string} userId - ID do usuário
   */
  logActivity(action, userId) {
    const logs = Storage.get('activityLogs', []);
    
    logs.push({
      id: DataInitializer.generateId(),
      userId: userId,
      action: action,
      timestamp: new Date().toISOString(),
      userAgent: navigator.userAgent
    });

    // Mantém apenas os últimos 100 logs
    if (logs.length > 100) {
      logs.shift();
    }

    Storage.set('activityLogs', logs);
  },

  /**
   * Retorna logs de atividade
   * @param {number} limit - Quantidade de logs
   * @returns {Array} - Array de logs
   */
  getActivityLogs(limit = 50) {
    const logs = Storage.get('activityLogs', []);
    return logs.slice(-limit).reverse();
  },

  /**
   * Protege uma página (redireciona se não autenticado)
   * @param {string} redirectUrl - URL para redirecionamento
   */
  requireAuth(redirectUrl = '/index.html') {
    if (!this.isAuthenticated()) {
      window.location.href = redirectUrl;
    }
  },

  /**
   * Redireciona se já estiver autenticado (para página de login)
   * @param {string} redirectUrl - URL para redirecionamento
   */
  redirectIfAuthenticated(redirectUrl = 'pages/dashboard.html') {
    if (this.isAuthenticated()) {
      window.location.href = redirectUrl;
    }
  }
};

/**
 * ==========================================================================
 * AUTO-RENOVAÇÃO DE SESSÃO
 * ==========================================================================
 */

// Renova a sessão a cada 5 minutos se o usuário estiver ativo
if (typeof window !== 'undefined') {
  let inactivityTimer;

  // Renova sessão ao detectar atividade
  const renewOnActivity = () => {
    if (Auth.isAuthenticated()) {
      Auth.renewSession();
      
      // Reseta timer de inatividade
      clearTimeout(inactivityTimer);
      
      // Desloga após 30 minutos de inatividade
      inactivityTimer = setTimeout(() => {
        if (Auth.isAuthenticated()) {
          Auth.logout();
          alert('Sua sessão expirou por inatividade. Faça login novamente.');
          window.location.href = '/index.html';
        }
      }, 30 * 60 * 1000);
    }
  };

  // Eventos que indicam atividade do usuário
  ['mousedown', 'keydown', 'scroll', 'touchstart'].forEach(event => {
    document.addEventListener(event, renewOnActivity, { passive: true });
  });

  // Primeira renovação ao carregar
  renewOnActivity();
}