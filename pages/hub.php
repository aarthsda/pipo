<?php
    if (!empty($_GET['msg'])) {
      if ($_GET['msg'] == 'logout') {
        setcookie("user_type", 'guest', time() + 3600, "/");
        setcookie("user_id", '', time() + 3600, "/");
        setcookie("user_password", '', time() + 3600, "/");
      }
    }
    if (isset($_COOKIE['user_type'])) {
        switch ($_COOKIE['user_type']) {
            case 'usuario':
                header("Location: usuarioInterface.php");
                break;
            case 'admin':
                header("Location: adminInterface.php");
                break;
        }
    }
    else {
        setcookie("user_type", 'guest', time() + 3600, "/");
    }

    require_once __DIR__ . "/../views/filaView.php";

    $fila = new filaView;
    $hideGuestCard = false;
    
    if (empty($fila->getAll(['status_fila' => 'ativa']))) {
        $hideGuestCard = true;
    }

    // Variáveis para os pratos (caso existam filas ativas)
    $pratoOptions = '';
    $idFila = '';
    
    if (!$hideGuestCard) {
        require_once __DIR__ . "/../views/cardapioView.php";
        require_once __DIR__ . "/../views/pratoView.php";

        date_default_timezone_set('America/Rio_Branco');

        $hoursNow = new Datetime(date('H:i'));
        $templateAlmoco = new Datetime('11:30');

        $dataFila = new Datetime(date('Y-m-d'));
        $dateTomorrow = new Datetime(date('Y-m-d'));
        $dateTomorrow = $dateTomorrow->modify('+1 day');
        
        if ($hoursNow > $templateAlmoco) {
            $filaView = new filaView(['data_fila' => $dateTomorrow->format('Y-m-d')]);
            $dataProxima = null;
            if (!$filaView->exists()) {
                foreach($filaView->getAll(['status_fila' => 'ativa']) as $fila) {
                    $dataFila = new Datetime($fila->data_fila);

                    $interval = $dataFila->diff(new Datetime(date('Y-m-d')));
                    $interval = (int) $interval->format('%h');

                    if ($interval < $dataProxima) {
                        $dataProxima = $interval;
                        $dataFila = $fila->data_fila;
                    }
                }
            }
            else {
                $dataFila = $filaView->get('data_fila');
            }
        }
        
        $fila = new filaView(['data_fila' => $dataFila->format('Y-m-d')]);
        $cardapio = new cardapioView(['id_cardapio' => $fila->get('cardapio_fila')]);
        $pratoA = new pratoView(['id_prato' => $cardapio->get('prato_A_cardapio')]);
        $pratoB = new pratoView(['id_prato' => $cardapio->get('prato_B_cardapio')]);

        $pratoOptions .= "<option value='" . $pratoA->get('id_prato') . "'>" . $pratoA->get('nome_prato') . "</option>";
        $pratoOptions .= "<option value='" . $pratoB->get('id_prato') . "'>" . $pratoB->get('nome_prato') . "</option>";
        
        $idFila = $fila->get('id_fila');
    }

    // Processar mensagens
    $alertMessage = '';
    $alertType = '';
    
    if(isset($_GET['msg'])) {
        switch ($_GET['msg']) {
            case 'senhaincorreta':
                $alertMessage = 'Senha incorreta.';
                $alertType = 'error';
                break;
            case 'cadastrorealizado':
                $alertMessage = 'Cadastro do almoço realizado!';
                $alertType = 'success';
                break;
            case 'usuarioinexistente':
                $alertMessage = 'O E-mail informado não existe no nosso banco de dados.';
                $alertType = 'error';
                break;
            case 'acessoproibido':
                $alertMessage = 'Você não tem permissão para acessar essa página.';
                $alertType = 'error';
                break;
            case 'sairdaconta':
                $alertMessage = 'Você saiu da conta com sucesso!';
                $alertType = 'success';
                break;
            case 'naodono':
                $alertMessage = 'Somente o dono do grupo que você participa pode realizar reservas.';
                $alertType = 'error';
                break;
            case 'foradehora':
                $alertMessage = 'A área de confirmação de almoço só pode ser acessada após 11:30 (GMT-5)';
                $alertType = 'error';
                break;
            case 'jatemreserva':
                $alertMessage = 'Você, ou seu grupo, já possui uma reserva nessa fila.';
                $alertType = 'error';
                break;
            case 'pratoindisponivel':
                $alertMessage = 'Este prato está indisponível. Por favor, escolha outro.';
                $alertType = 'error';
                break;
            case 'jafoitodos':
                $alertMessage = 'Todos já confirmaram seu almoço!';
                $alertType = 'success';
                break;
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistema de Restaurante - Acesso</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
      background: linear-gradient(135deg, #f5f7fa 0%, #e8eef5 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      line-height: 1.6;
      color: #2c3e50;
    }

    .container {
      width: 100%;
      max-width: 1200px;
      margin: 0 auto;
    }

    .header {
      text-align: center;
      margin-bottom: 48px;
    }

    .header-title {
      font-size: 32px;
      font-weight: 700;
      color: #1a365d;
      margin-bottom: 8px;
    }

    .header-subtitle {
      font-size: 16px;
      color: #64748b;
      font-weight: 400;
    }

    .alert {
      padding: 16px 20px;
      border-radius: 8px;
      margin-bottom: 24px;
      font-size: 15px;
      font-weight: 500;
      text-align: center;
    }

    .alert-success {
      background: #d1fae5;
      border: 1px solid #6ee7b7;
      color: #065f46;
    }

    .alert-error {
      background: #fee2e2;
      border: 1px solid #fca5a5;
      color: #991b1b;
    }

    .access-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 24px;
      margin-bottom: 32px;
    }

    .access-card {
      background: white;
      border-radius: 12px;
      padding: 32px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      border: 2px solid #e2e8f0;
      transition: all 0.3s ease;
    }

    .access-card:hover {
      border-color: #1351b4;
      box-shadow: 0 4px 12px rgba(19,81,180,0.15);
    }

    .card-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 24px;
      padding-bottom: 16px;
      border-bottom: 2px solid #f1f5f9;
    }

    .card-icon {
      width: 40px;
      height: 40px;
      background: #e0e7ff;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #1351b4;
    }

    .card-title {
      font-size: 20px;
      font-weight: 600;
      color: #1a365d;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-label {
      display: block;
      font-size: 14px;
      font-weight: 500;
      color: #334155;
      margin-bottom: 8px;
    }

    .form-input,
    .form-select {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid #e2e8f0;
      border-radius: 8px;
      font-size: 15px;
      color: #2c3e50;
      transition: border-color 0.2s;
      background: white;
    }

    .form-input:focus,
    .form-select:focus {
      outline: none;
      border-color: #1351b4;
    }

    .btn {
      width: 100%;
      padding: 14px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
      text-align: center;
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

    .btn-secondary {
      background: #f1f5f9;
      color: #334155;
      margin-top: 12px;
    }

    .btn-secondary:hover {
      background: #e2e8f0;
    }

    .footer {
      text-align: center;
      margin-top: 48px;
      padding-top: 24px;
      border-top: 1px solid #e2e8f0;
    }

    .footer-text {
      font-size: 14px;
      color: #64748b;
    }

    .card-note {
      font-size: 13px;
      color: #64748b;
      margin-top: 12px;
      text-align: center;
    }

    .card-unavailable {
      padding: 16px;
      background: #f1f5f9;
      border-radius: 8px;
      text-align: center;
      color: #64748b;
      font-size: 14px;
    }

    @media (max-width: 768px) {
      .access-grid {
        grid-template-columns: 1fr;
      }

      .header-title {
        font-size: 24px;
      }

      .access-card {
        padding: 24px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <header class="header">
      <h1 class="header-title">Sistema de Restaurante Institucional</h1>
      <p class="header-subtitle">Acesso ao sistema de reservas e gestão de refeições</p>
    </header>

    <?php if ($alertMessage): ?>
    <div class="alert alert-<?php echo $alertType; ?>">
      <?php echo $alertMessage; ?>
    </div>
    <?php endif; ?>

    <div class="access-grid">
      <!-- Admin Login Card -->
      <div class="access-card">
        <div class="card-header">
          <div class="card-icon">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 2L2 7l10 5 10-5-10-5z"/>
              <path d="M2 17l10 5 10-5"/>
              <path d="M2 12l10 5 10-5"/>
            </svg>
          </div>
          <h2 class="card-title">Acesso Administrativo</h2>
        </div>
        <button class="btn btn-primary" onclick="window.location.href='loginAdmin.php'">
          Entrar como Administrador
        </button>
        <p class="card-note">
          Acesso restrito a administradores do sistema
        </p>
      </div>

      <!-- User Login Card -->
      <div class="access-card">
        <div class="card-header">
          <div class="card-icon">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
              <circle cx="12" cy="7" r="4"/>
            </svg>
          </div>
          <h2 class="card-title">Acesso de Usuário</h2>
        </div>
        <button class="btn btn-primary" onclick="window.location.href='loginUsuario.php'">
          Entrar como Usuário
        </button>
        <p class="card-note">
          Acesso para usuários cadastrados no sistema
        </p>
      </div>

      <!-- Guest Reservation Card -->
      <div class="access-card">
        <div class="card-header">
          <div class="card-icon">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/>
              <path d="M7 2v20"/>
              <path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3v7"/>
            </svg>
          </div>
          <h2 class="card-title">Reserva sem Cadastro</h2>
        </div>
        
        <?php if ($hideGuestCard): ?>
        <div class="card-unavailable">
          Não há filas ativas no momento. Reservas indisponíveis.
        </div>
        <?php else: ?>
        <form action="ctrlHub.php?class=reserva&action=create" method="post" novalidate>
          <div class="form-group">
            <label class="form-label" for="email_usuario">E-mail</label>
            <input type="email" id="email_usuario" name="email_usuario" class="form-input" placeholder="seu.email@exemplo.com" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="senha_usuario">Senha temporária</label>
            <input type="password" id="senha_usuario" name="senha_usuario" class="form-input" placeholder="••••••••" required>
          </div>
          <div class="form-group">
            <label class="form-label" for="prato_prioritario">Prato prioritário</label>
            <select id="prato_prioritario" name="prato_prioritario" class="form-select" required>
              <option value="">Selecione uma opção</option>
              <?php echo $pratoOptions; ?>
            </select>
          </div>
          <input type="hidden" name="id_fila" value="<?php echo $idFila; ?>">
          <button type="submit" class="btn btn-primary">Fazer Reserva</button>
        </form>
        <?php endif; ?>
      </div>
    </div>

    <footer class="footer">
      <p class="footer-text">Sistema de Gestão de Restaurante Institucional © 2026</p>
      <p class="footer-text" style="margin-top: 8px; font-size: 13px;">
        Para suporte técnico, entre em contato com a administração
      </p>
    </footer>
  </div>

  <?php if ($alertMessage): ?>
  <script>
    setTimeout(function() {
      window.location.href = 'hub.php';
    }, 5000);
  </script>
  <?php endif; ?>
</body>
</html>