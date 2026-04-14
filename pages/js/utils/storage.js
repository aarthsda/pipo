/**
 * ==========================================================================
 * STORAGE UTILITY - Gerenciamento de dados no localStorage
 * ==========================================================================
 */

const Storage = {
  /**
   * Salva dados no localStorage
   * @param {string} key - Chave para armazenamento
   * @param {*} value - Valor a ser armazenado (será convertido para JSON)
   * @returns {boolean} - Sucesso da operação
   */
  set(key, value) {
    try {
      const serializedValue = JSON.stringify(value);
      localStorage.setItem(key, serializedValue);
      return true;
    } catch (error) {
      console.error('Erro ao salvar no storage:', error);
      return false;
    }
  },

  /**
   * Recupera dados do localStorage
   * @param {string} key - Chave para recuperação
   * @param {*} defaultValue - Valor padrão caso a chave não exista
   * @returns {*} - Valor armazenado ou valor padrão
   */
  get(key, defaultValue = null) {
    try {
      const item = localStorage.getItem(key);
      return item ? JSON.parse(item) : defaultValue;
    } catch (error) {
      console.error('Erro ao recuperar do storage:', error);
      return defaultValue;
    }
  },

  /**
   * Remove um item do localStorage
   * @param {string} key - Chave para remoção
   * @returns {boolean} - Sucesso da operação
   */
  remove(key) {
    try {
      localStorage.removeItem(key);
      return true;
    } catch (error) {
      console.error('Erro ao remover do storage:', error);
      return false;
    }
  },

  /**
   * Limpa todo o localStorage
   * @returns {boolean} - Sucesso da operação
   */
  clear() {
    try {
      localStorage.clear();
      return true;
    } catch (error) {
      console.error('Erro ao limpar storage:', error);
      return false;
    }
  },

  /**
   * Verifica se uma chave existe no localStorage
   * @param {string} key - Chave para verificação
   * @returns {boolean} - True se existe, false caso contrário
   */
  has(key) {
    return localStorage.getItem(key) !== null;
  },

  /**
   * Retorna todas as chaves armazenadas
   * @returns {Array<string>} - Array com todas as chaves
   */
  keys() {
    return Object.keys(localStorage);
  },

  /**
   * Retorna o tamanho do localStorage em bytes (aproximado)
   * @returns {number} - Tamanho em bytes
   */
  size() {
    let total = 0;
    for (let key in localStorage) {
      if (localStorage.hasOwnProperty(key)) {
        total += localStorage[key].length + key.length;
      }
    }
    return total;
  }
};

/**
 * ==========================================================================
 * INICIALIZAÇÃO DOS DADOS PADRÃO DO SISTEMA
 * ==========================================================================
 */

const DataInitializer = {
  /**
   * Inicializa todos os dados necessários do sistema
   */
  init() {
    this.initAdminUser();
    this.initCollections();
  },

  /**
   * Cria o usuário administrador padrão se não existir
   */
  initAdminUser() {
    if (!Storage.has('adminUser')) {
      const defaultAdmin = {
        id: this.generateId(),
        email: 'admin@restaurante.gov.br',
        password: this.hashPassword('Admin@2025'),
        name: 'Administrador',
        role: 'admin',
        createdAt: new Date().toISOString(),
        updatedAt: new Date().toISOString()
      };
      Storage.set('adminUser', defaultAdmin);
      console.log('✅ Usuário administrador padrão criado');
    }
  },

  /**
   * Inicializa as coleções vazias se não existirem
   */
  initCollections() {
    const collections = ['dishes', 'menus', 'queues', 'accessUsers'];
    
    collections.forEach(collection => {
      if (!Storage.has(collection)) {
        Storage.set(collection, []);
        console.log(`✅ Coleção '${collection}' inicializada`);
      }
    });
  },

  /**
   * Gera um ID único
   * @returns {string} - ID único gerado
   */
  generateId() {
    return '_' + Math.random().toString(36).substr(2, 9) + Date.now().toString(36);
  },

  /**
   * Hash simples de senha (em produção, usar bcrypt ou similar)
   * @param {string} password - Senha em texto plano
   * @returns {string} - Senha com hash
   */
  hashPassword(password) {
    return btoa(password); // Base64 - apenas para demonstração
  },

  /**
   * Reseta o sistema para os dados padrão
   */
  reset() {
    if (confirm('⚠️ ATENÇÃO: Todos os dados serão perdidos. Deseja continuar?')) {
      Storage.clear();
      this.init();
      console.log('✅ Sistema resetado para configurações padrão');
      return true;
    }
    return false;
  }
};

/**
 * ==========================================================================
 * HELPERS PARA MANIPULAÇÃO DE COLEÇÕES
 * ==========================================================================
 */

const Collections = {
  /**
   * Retorna todos os itens de uma coleção
   * @param {string} collection - Nome da coleção
   * @returns {Array} - Array com todos os itens
   */
  getAll(collection) {
    return Storage.get(collection, []);
  },

  /**
   * Retorna um item específico por ID
   * @param {string} collection - Nome da coleção
   * @param {string} id - ID do item
   * @returns {Object|null} - Item encontrado ou null
   */
  getById(collection, id) {
    const items = this.getAll(collection);
    return items.find(item => item.id === id) || null;
  },

  /**
   * Adiciona um novo item à coleção
   * @param {string} collection - Nome da coleção
   * @param {Object} item - Item a ser adicionado
   * @returns {Object} - Item adicionado com ID
   */
  add(collection, item) {
    const items = this.getAll(collection);
    const newItem = {
      id: DataInitializer.generateId(),
      ...item,
      createdAt: new Date().toISOString(),
      updatedAt: new Date().toISOString()
    };
    items.push(newItem);
    Storage.set(collection, items);
    return newItem;
  },

  /**
   * Atualiza um item existente
   * @param {string} collection - Nome da coleção
   * @param {string} id - ID do item
   * @param {Object} updates - Dados a serem atualizados
   * @returns {Object|null} - Item atualizado ou null
   */
  update(collection, id, updates) {
    const items = this.getAll(collection);
    const index = items.findIndex(item => item.id === id);
    
    if (index === -1) return null;
    
    items[index] = {
      ...items[index],
      ...updates,
      id: items[index].id, // Garante que o ID não seja alterado
      createdAt: items[index].createdAt, // Mantém a data de criação
      updatedAt: new Date().toISOString()
    };
    
    Storage.set(collection, items);
    return items[index];
  },

  /**
   * Remove um item da coleção
   * @param {string} collection - Nome da coleção
   * @param {string} id - ID do item
   * @returns {boolean} - Sucesso da operação
   */
  remove(collection, id) {
    const items = this.getAll(collection);
    const filteredItems = items.filter(item => item.id !== id);
    
    if (filteredItems.length === items.length) return false;
    
    Storage.set(collection, filteredItems);
    return true;
  },

  /**
   * Remove múltiplos itens da coleção
   * @param {string} collection - Nome da coleção
   * @param {Array<string>} ids - Array de IDs
   * @returns {number} - Quantidade de itens removidos
   */
  removeMany(collection, ids) {
    const items = this.getAll(collection);
    const filteredItems = items.filter(item => !ids.includes(item.id));
    const removedCount = items.length - filteredItems.length;
    
    if (removedCount > 0) {
      Storage.set(collection, filteredItems);
    }
    
    return removedCount;
  },

  /**
   * Busca itens por critério
   * @param {string} collection - Nome da coleção
   * @param {Function} predicate - Função de filtro
   * @returns {Array} - Itens que atendem ao critério
   */
  find(collection, predicate) {
    const items = this.getAll(collection);
    return items.filter(predicate);
  },

  /**
   * Conta itens na coleção
   * @param {string} collection - Nome da coleção
   * @returns {number} - Quantidade de itens
   */
  count(collection) {
    return this.getAll(collection).length;
  }
};

// Inicializa os dados ao carregar o script
if (typeof window !== 'undefined') {
  DataInitializer.init();
}