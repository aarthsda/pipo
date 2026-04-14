<?php
  // Verificação de autenticação
  if (empty($_COOKIE) || $_COOKIE['user_type'] != 'usuario') {
    header("Location: hub.php?msg=acessoproibido");
    exit();
  }

  require_once __DIR__ . "/../views/usuarioView.php";
  
  $usuario = new usuarioView(['id_usuario' => $_COOKIE['user_id']]);
  
  if (!$usuario->verifyPassword($_COOKIE['user_password'])) {
    setcookie("user_type", 'guest', time() + 3600, "/");
    setcookie("user_id", '', time() + 3600, "/");
    setcookie("user_password", '', time() + 3600, "/");

    header("Location: hub.php");
    exit();
  }

  // Carregar dados das reservas
  require_once __DIR__ . "/../views/reservaView.php";
  require_once __DIR__ . "/../views/grupoView.php";
  require_once __DIR__ . "/../views/filaView.php";

  $grupo = new grupoView(['id_grupo' => $usuario->get('grupo_usuario')]);
  $reservaView = new reservaView();
  
  date_default_timezone_set('America/Rio_Branco');
  
  $reservas_ativas = [];
  $eh_dono = ($grupo->get('dono_grupo') == $_COOKIE['user_id']);
  
  if ($reservaView->getAll()) {
    foreach ($reservaView->getAll(['id_grupo_reserva' => $grupo->get('id_grupo'), 'status_reserva' => 'ativa']) as $reserva) {
      $fila = new filaView(['id_fila' => $reserva->id_fila_reserva]);
      
      // Verificar se pode cancelar (dono do grupo + até 2h antes - 11:30)
      $pode_cancelar = $eh_dono && (new DateTime(date('Y-m-d H:i')) < new DateTime($reserva->data_reserva . ' 11:30'));
      
      // Formatar data para exibição (DD/MM/YYYY)
      $data_formatada = date('d/m/Y', strtotime($reserva->data_reserva));
      
      $reservas_ativas[] = [
        'id' => $reserva->id_reserva,
        'date' => $data_formatada,
        'type' => $reserva->tipo_reserva,
        'dish' => $reserva->prato_prioritario_reserva,
        'position' => $reserva->posicao_reserva,
        'canCancel' => $pode_cancelar
      ];
    }
  }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gerenciar Reservas - Sistema de Restaurante</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
      background: #f5f7fa;
      min-height: 100vh;
      line-height: 1.6;
      color: #2c3e50;
    }

    .header {
      background: white;
      border-bottom: 2px solid #e2e8f0;
      padding: 20px 0;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .header-content {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .header-title {
      font-size: 20px;
      font-weight: 600;
      color: #1a365d;
    }

    .header-actions {
      display: flex;
      gap: 12px;
    }

    .back-button {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 10px 16px;
      background: #f1f5f9;
      border: none;
      border-radius: 8px;
      color: #334155;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.2s;
    }

    .back-button:hover {
      background: #e2e8f0;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 40px 20px;
    }

    .page-header {
      margin-bottom: 32px;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      flex-wrap: wrap;
      gap: 16px;
    }

    .page-header-content {
      flex: 1;
    }

    .page-title {
      font-size: 28px;
      font-weight: 700;
      color: #1a365d;
      margin-bottom: 8px;
    }

    .page-subtitle {
      font-size: 15px;
      color: #64748b;
    }

    .btn {
      padding: 12px 20px;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      text-decoration: none;
    }

    .btn-primary {
      background: #1351b4;
      color: white;
    }

    .btn-primary:hover {
      background: #0d3f8f;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(19,81,180,0.3);
    }

    .reservations-card {
      background: white;
      border-radius: 12px;
      padding: 32px;
      border: 2px solid #e2e8f0;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      margin-bottom: 24px;
    }

    .card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 24px;
      padding-bottom: 16px;
      border-bottom: 2px solid #f1f5f9;
    }

    .card-title {
      font-size: 20px;
      font-weight: 600;
      color: #1a365d;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .card-icon {
      width: 32px;
      height: 32px;
      background: #e0e7ff;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #1351b4;
    }

    .reservations-count {
      font-size: 14px;
      color: #64748b;
      padding: 6px 12px;
      background: #f1f5f9;
      border-radius: 6px;
      font-weight: 600;
    }

    .table-container {
      overflow-x: auto;
    }

    .reservations-table {
      width: 100%;
      border-collapse: collapse;
    }

    .reservations-table thead {
      background: #f8fafc;
    }

    .reservations-table th {
      text-align: left;
      padding: 12px 16px;
      font-size: 13px;
      font-weight: 600;
      color: #475569;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border-bottom: 2px solid #e2e8f0;
    }

    .reservations-table td {
      padding: 16px;
      border-bottom: 1px solid #f1f5f9;
      font-size: 14px;
      color: #334155;
    }

    .reservations-table tbody tr:hover {
      background: #f8fafc;
    }

    .reservation-date {
      font-weight: 600;
      color: #1a365d;
    }

    .reservation-type {
      display: inline-flex;
      align-items: center;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 12px;
      font-weight: 600;
    }

    .type-common {
      background: #e0e7ff;
      color: #1351b4;
    }

    .type-priority {
      background: #fef3c7;
      color: #d97706;
    }

    .queue-position {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-weight: 600;
      color: #059669;
    }

    .action-button {
      padding: 8px 14px;
      border: none;
      border-radius: 6px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }

    .btn-cancel {
      background: #fee2e2;
      color: #dc2626;
    }

    .btn-cancel:hover {
      background: #fecaca;
    }

    .btn-cancel:disabled {
      background: #f1f5f9;
      color: #94a3b8;
      cursor: not-allowed;
    }

    .empty-state {
      text-align: center;
      padding: 60px 20px;
    }

    .empty-icon {
      width: 80px;
      height: 80px;
      margin: 0 auto 20px;
      background: #f1f5f9;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #94a3b8;
    }

    .empty-title {
      font-size: 20px;
      font-weight: 600;
      color: #475569;
      margin-bottom: 8px;
    }

    .empty-text {
      font-size: 14px;
      color: #64748b;
      margin-bottom: 24px;
    }

    .info-banner {
      background: #f0f9ff;
      border: 1px solid #bae6fd;
      border-radius: 8px;
      padding: 16px 20px;
      margin-bottom: 24px;
      display: flex;
      align-items: flex-start;
      gap: 12px;
    }

    .info-icon {
      flex-shrink: 0;
      color: #0284c7;
      margin-top: 2px;
    }

    .info-content {
      flex: 1;
    }

    .info-title {
      font-size: 14px;
      font-weight: 600;
      color: #075985;
      margin-bottom: 4px;
    }

    .info-text {
      font-size: 13px;
      color: #0c4a6e;
      line-height: 1.6;
    }

    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1000;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .modal.show {
      display: flex;
    }

    .modal-content {
      background: white;
      border-radius: 12px;
      padding: 32px;
      max-width: 500px;
      width: 100%;
      box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
    }

    .modal-header {
      margin-bottom: 20px;
    }

    .modal-title {
      font-size: 20px;
      font-weight: 700;
      color: #1a365d;
      margin-bottom: 8px;
    }

    .modal-subtitle {
      font-size: 14px;
      color: #64748b;
    }

    .modal-actions {
      display: flex;
      gap: 12px;
      margin-top: 24px;
    }

    .btn-danger {
      background: #dc2626;
      color: white;
      flex: 1;
    }

    .btn-danger:hover {
      background: #b91c1c;
    }

    .btn-secondary {
      background: #f1f5f9;
      color: #334155;
      flex: 1;
    }

    .btn-secondary:hover {
      background: #e2e8f0;
    }

    .alert {
      padding: 16px 20px;
      border-radius: 8px;
      margin-bottom: 24px;
      display: flex;
      align-items: flex-start;
      gap: 12px;
      font-size: 14px;
      line-height: 1.6;
    }

    .alert-success {
      background: #dcfce7;
      border: 1px solid #86efac;
      color: #166534;
    }

    .alert-error {
      background: #fee2e2;
      border: 1px solid #fca5a5;
      color: #991b1b;
    }

    @media (max-width: 768px) {
      .container {
        padding: 24px 16px;
      }

      .page-header {
        flex-direction: column;
      }

      .reservations-card {
        padding: 20px;
      }

      .card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
      }

      .page-title {
        font-size: 24px;
      }

      .table-container {
        margin: 0 -20px;
        padding: 0 20px;
      }

      .reservations-table th,
      .reservations-table td {
        padding: 12px 8px;
        font-size: 12px;
      }

      .modal-content {
        padding: 24px;
      }

      .header-actions {
        flex-direction: column;
        width: 100%;
      }

      .back-button,
      .btn-primary {
        width: 100%;
        justify-content: center;
      }
    }
  </style>
</head>
<body>
  <header class="header">
    <div class="header-content">
      <h1 class="header-title">Sistema de Restaurante Institucional</h1>
      <div class="header-actions">
        <a href="usuarioInterface.php" class="back-button">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12 19 5 12 12 5"/>
          </svg>
          Voltar
        </a>
      </div>
    </div>
  </header>

  <main class="container">
    <?php
    // Exibir mensagens de feedback
    if (!empty($_GET['msg'])) {
      $mensagem = '';
      $tipo = 'error';
      
      switch ($_GET['msg']) {
        case 'naodono':
          $mensagem = 'Somente o dono do grupo que você participa pode realizar reservas.';
          break;
        case 'jatemreserva':
          $mensagem = 'Você, ou seu grupo, já possui uma reserva nessa fila.';
          break;
        case 'reservacancelada':
          $mensagem = 'Reserva cancelada com sucesso!';
          $tipo = 'success';
          break;
        case 'pratoindisponivel':
          $mensagem = 'Este prato não está mais disponível. Por favor, escolha outro.';
          break;
      }
      
      if ($mensagem) {
        echo "<div class='alert alert-$tipo'><strong>" . htmlspecialchars($mensagem) . "</strong></div>";
        echo "<script>setTimeout(function(){ window.location.href='manage-reservation.php'; }, 5000);</script>";
      }
    }
    ?>

    <div class="page-header">
      <div class="page-header-content">
        <h2 class="page-title">Gerenciar Reservas</h2>
        <p class="page-subtitle">Visualize e gerencie todas as suas reservas de refeições</p>
      </div>
      <?php if ($eh_dono): ?>
      <a href="reservarFila.php" class="btn btn-primary">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"/>
          <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Nova Reserva
      </a>
      <?php endif; ?>
    </div>

    <div class="info-banner">
      <div class="info-icon">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/>
          <line x1="12" y1="16" x2="12" y2="12"/>
          <line x1="12" y1="8" x2="12.01" y2="8"/>
        </svg>
      </div>
      <div class="info-content">
        <div class="info-title">Informação importante</div>
        <div class="info-text">
          Apenas o proprietário do grupo pode cancelar reservas. Cancelamentos devem ser feitos com no mínimo 2 horas de antecedência (até 11:30 do dia da reserva).
        </div>
      </div>
    </div>

    <div class="reservations-card">
      <div class="card-header">
        <div class="card-title">
          <div class="card-icon">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
              <line x1="16" y1="2" x2="16" y2="6"/>
              <line x1="8" y1="2" x2="8" y2="6"/>
              <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
          </div>
          Minhas Reservas
        </div>
        <span class="reservations-count" id="reservationsCount">
          <?php echo count($reservas_ativas) . ' ' . (count($reservas_ativas) === 1 ? 'reserva' : 'reservas'); ?>
        </span>
      </div>

      <div id="reservationsContent">
        <?php if (empty($reservas_ativas)): ?>
          <div class="empty-state">
            <div class="empty-icon">
              <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
              </svg>
            </div>
            <h3 class="empty-title">Nenhuma reserva encontrada</h3>
            <p class="empty-text">Você ainda não possui reservas ativas. <?php echo $eh_dono ? 'Clique no botão acima para fazer sua primeira reserva.' : 'Aguarde o dono do grupo realizar uma reserva.'; ?></p>
            <?php if ($eh_dono): ?>
            <a href="reservarFila.php" class="btn btn-primary">Fazer Reserva</a>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <div class="table-container">
            <table class="reservations-table">
              <thead>
                <tr>
                  <th>Data</th>
                  <th>Tipo</th>
                  <th>Prato Prioritário</th>
                  <th>Posição na Fila</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($reservas_ativas as $reserva): ?>
                <tr>
                  <td class="reservation-date"><?php echo htmlspecialchars($reserva['date']); ?></td>
                  <td>
                    <span class="reservation-type <?php echo $reserva['type'] === 'Comum' ? 'type-common' : 'type-priority'; ?>">
                      <?php echo htmlspecialchars($reserva['type']); ?>
                    </span>
                  </td>
                  <td><?php echo htmlspecialchars($reserva['dish']); ?></td>
                  <td>
                    <span class="queue-position">
                      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                      </svg>
                      <?php echo htmlspecialchars($reserva['position']); ?>º
                    </span>
                  </td>
                  <td>
                    <button 
                      class="action-button btn-cancel" 
                      onclick="openCancelModal(<?php echo $reserva['id']; ?>)"
                      <?php echo !$reserva['canCancel'] ? 'disabled' : ''; ?>
                      title="<?php echo !$reserva['canCancel'] ? 'Apenas o proprietário do grupo pode cancelar ou o prazo expirou' : 'Cancelar reserva'; ?>"
                    >
                      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" y1="9" x2="9" y2="15"/>
                        <line x1="9" y1="9" x2="15" y2="15"/>
                      </svg>
                      Cancelar
                    </button>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div style="text-align: center; margin-top: 24px;">
      <a href="outrasReservas.php" class="btn btn-secondary" style="display: inline-flex;">
        Ver outras reservas
      </a>
    </div>
  </main>

  <!-- Modal de Confirmação -->
  <div id="cancelModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Confirmar Cancelamento</h3>
        <p class="modal-subtitle">Tem certeza que deseja cancelar esta reserva?</p>
      </div>
      <div style="background: #fef3c7; padding: 12px; border-radius: 6px; margin: 16px 0;">
        <p style="font-size: 13px; color: #92400e; line-height: 1.6;">
          <strong>Atenção:</strong> Esta ação não pode ser desfeita. Você precisará fazer uma nova reserva caso deseje garantir uma refeição neste dia.
        </p>
      </div>
      <div class="modal-actions">
        <button class="btn btn-danger" onclick="confirmCancel()">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="15" y1="9" x2="9" y2="15"/>
            <line x1="9" y1="9" x2="15" y2="15"/>
          </svg>
          Sim, Cancelar Reserva
        </button>
        <button class="btn btn-secondary" onclick="closeModal()">
          Não, Manter Reserva
        </button>
      </div>
    </div>
  </div>

  <script>
    let currentReservationId = null;

    // Abrir modal de cancelamento
    function openCancelModal(id) {
      currentReservationId = id;
      document.getElementById('cancelModal').classList.add('show');
    }

    // Fechar modal
    function closeModal() {
      document.getElementById('cancelModal').classList.remove('show');
      currentReservationId = null;
    }

    // Confirmar cancelamento - redireciona para o PHP
    function confirmCancel() {
      if (currentReservationId) {
        window.location.href = 'cancelReserva.php?id=' + currentReservationId;
      }
    }

    // Fechar modal ao clicar fora
    document.getElementById('cancelModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeModal();
      }
    });
  </script>
</body>
</html>