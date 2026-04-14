// ============================================
// DASHBOARD.JS - INTERATIVIDADE E INTERFACE
// Sistema Pipo - Restaurante Institucional
// ============================================

(function() {
  'use strict';

  // ============================================
  // ELEMENTOS DO DOM
  // ============================================
  const sidebar = document.getElementById('sidebar');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebarOverlay = document.getElementById('sidebarOverlay');
  const logoutBtn = document.getElementById('logoutBtn');
  const logoutForm = document.getElementById('logoutForm');
  const currentDateElement = document.getElementById('currentDate');

  // ============================================
  // SIDEBAR - TOGGLE MOBILE
  // ============================================

  /**
   * Abre a sidebar no mobile
   */
  function openSidebar() {
    sidebar.classList.add('sidebar-open');
    sidebarOverlay.classList.add('sidebar-overlay-active');
    document.body.style.overflow = 'hidden';
    sidebarToggle.setAttribute('aria-label', 'Fechar menu');
  }

  /**
   * Fecha a sidebar no mobile
   */
  function closeSidebar() {
    sidebar.classList.remove('sidebar-open');
    sidebarOverlay.classList.remove('sidebar-overlay-active');
    document.body.style.overflow = '';
    sidebarToggle.setAttribute('aria-label', 'Abrir menu');
  }

  /**
   * Toggle da sidebar
   */
  function toggleSidebar() {
    if (sidebar.classList.contains('sidebar-open')) {
      closeSidebar();
    } else {
      openSidebar();
    }
  }

  // Event listeners para sidebar
  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', toggleSidebar);
  }

  if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', closeSidebar);
  }

  // Fechar sidebar ao pressionar ESC
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && sidebar.classList.contains('sidebar-open')) {
      closeSidebar();
    }
  });

  // ============================================
  // DATA E HORA ATUAL
  // ============================================

  /**
   * Formata data no padrão brasileiro
   */
  function formatarData() {
    const meses = [
      'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
      'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
    ];
    
    const diasSemana = [
      'Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira',
      'Quinta-feira', 'Sexta-feira', 'Sábado'
    ];

    const agora = new Date();
    const diaSemana = diasSemana[agora.getDay()];
    const dia = agora.getDate();
    const mes = meses[agora.getMonth()];
    const ano = agora.getFullYear();

    return `${diaSemana}, ${dia} de ${mes} de ${ano}`;
  }

  /**
   * Atualiza a data no header
   */
  function atualizarData() {
    if (currentDateElement) {
      currentDateElement.textContent = formatarData();
    }
  }

  // ============================================
  // LOGOUT - CONFIRMAÇÃO
  // ============================================

  /**
   * Confirma logout antes de enviar o formulário
   */
  function confirmarLogout(e) {
    const confirmacao = confirm('Tem certeza que deseja sair do sistema?');
    
    if (!confirmacao) {
      e.preventDefault();
      return false;
    }
    
    return true;
  }

  // Event listener para logout
  if (logoutForm) {
    logoutForm.addEventListener('submit', confirmarLogout);
  }

  // ============================================
  // ESTATÍSTICAS - BUSCAR DO BACKEND (FUTURO)
  // ============================================

  /**
   * Busca estatísticas do sistema
   * TODO: Implementar quando houver endpoint PHP
   */
  function buscarEstatisticas() {
    // Por enquanto, deixar em 0
    // Futuramente, fazer fetch para PHP que retorna JSON com contagens
    
    const dishesCount = document.getElementById('dishesCount');
    const menusCount = document.getElementById('menusCount');
    const queuesCount = document.getElementById('queuesCount');
    const usersCount = document.getElementById('usersCount');

    // Placeholder - será substituído por dados reais do PHP
    if (dishesCount) dishesCount.textContent = '0';
    if (menusCount) menusCount.textContent = '0';
    if (queuesCount) queuesCount.textContent = '0';
    if (usersCount) usersCount.textContent = '0';

    /*
    // EXEMPLO DE COMO SERÁ IMPLEMENTADO NO FUTURO:
    
    fetch('../api/get-stats.php')
      .then(response => response.json())
      .then(data => {
        if (dishesCount) dishesCount.textContent = data.pratos || '0';
        if (menusCount) menusCount.textContent = data.cardapios || '0';
        if (queuesCount) queuesCount.textContent = data.filas || '0';
        if (usersCount) usersCount.textContent = data.usuarios || '0';
      })
      .catch(error => {
        console.error('Erro ao buscar estatísticas:', error);
      });
    */
  }

  // ============================================
  // ANIMAÇÕES DE ENTRADA
  // ============================================

  /**
   * Adiciona animação fade-in aos cards
   */
  function animarCards() {
    const cards = document.querySelectorAll('.stats-card, .quick-action-card');
    
    cards.forEach((card, index) => {
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)';
      card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
      
      setTimeout(() => {
        card.style.opacity = '1';
        card.style.transform = 'translateY(0)';
      }, index * 100);
    });
  }

  // ============================================
  // RESPONSIVIDADE
  // ============================================

  /**
   * Fecha sidebar ao redimensionar para desktop
   */
  function handleResize() {
    if (window.innerWidth > 1024 && sidebar.classList.contains('sidebar-open')) {
      closeSidebar();
    }
  }

  // Event listener para resize
  window.addEventListener('resize', handleResize);

  // ============================================
  // MENSAGENS DE SISTEMA
  // ============================================

  /**
   * Verifica e exibe mensagens da URL
   */
  function verificarMensagens() {
    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');

    if (msg === 'logout') {
      // Usuário fez logout com sucesso
      // Redirecionar para index se necessário
      window.location.href = '../index.php';
    }
  }

  // ============================================
  // INICIALIZAÇÃO
  // ============================================

  /**
   * Inicializa o dashboard
   */
  function init() {
    // Atualizar data
    atualizarData();
    
    // Buscar estatísticas
    buscarEstatisticas();
    
    // Animar cards
    animarCards();
    
    // Verificar mensagens
    verificarMensagens();
    
    // Atualizar data a cada minuto
    setInterval(atualizarData, 60000);
  }

  // ============================================
  // EXECUTAR AO CARREGAR
  // ============================================

  // Aguardar carregamento completo
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();