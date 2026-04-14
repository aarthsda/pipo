<?php
    setcookie("user_type", 'guest', time() + 3600, "/");
    setcookie("user_id", '', time() + 3600, "/");
    setcookie("user_password", '', time() + 3600, "/");

    require_once __DIR__ . "/../controllers/!allCtrl.php";
    require_once __DIR__ . "/../views/!allView.php";
    require_once __DIR__ . "/../config/phpmail.php";

    date_default_timezone_set('America/Rio_Branco');
    
    if (!isset($_COOKIE['time'])) {
        setcookie("time", date('Y-m-d H:i'), time() + 3600, "/");
        
        $reserva = new reservaView;
        $reservas = $reserva->getAll(['tipo_reserva' => 'reserva', 'status_reserva' => 'ativa']);
        $prioritarios = $reserva->getAll(['tipo_reserva' => 'prioritario', 'status_reserva' => 'ativa']);
        $comuns = $reserva->getAll(['tipo_reserva' => 'comum', 'status_reserva' => 'ativa']);

        if (!empty($prioritarios) || !empty($comuns)) {
            if (!empty($prioritarios)) {
                $first = $prioritarios[0]->id_reserva;
            }
            else {
                $first = $comuns[0]->id_reserva;
            }
        }
        else if (!empty($reservas)) {
            $first = $reservas[0]->id_reserva;
        }
        else {
            setcookie("time", '', time() - 3600, "/");
            setcookie("first", '', time() - 3600, "/");
            header('Location: hub.php?msg=jafoitodos');
            exit();
        }

        setcookie("first", $first, time() + 3600, "/");

        $reserva = new reservaView(['id_reserva' => $first]);
        $grupo = new grupoView(['id_grupo' => $reserva->get('id_grupo_reserva')]);
        $usuario = new usuarioView;
    }

    $late = new DateTime($_COOKIE['time'] ?? date('Y-m-d'));
    $now = new DateTime(date('Y-m-d H:i'));
    $diferenca = $late->diff($now);
    $minutos = ($diferenca->days * 24 * 60) + ($diferenca->h * 60) + $diferenca->i;

    if ($minutos > 2 || ($minutos == 2 && $diferenca->s > 0)) {
        var_dump($reservaCtrl);
        $reserva = new reservaView(['id_reserva' => 1]);
        $reservaCtrl = new reservaCtrl($reserva->getAtributtes());

        $reservaCtrl->setData(['tipo_reserva' => 'reserva']);
        $reservaCtrl->store();

        setcookie("time", '', time() - 3600, "/");
        // header("Refresh:0");
    }

    $mensagemSucesso = '';
    $mensagemErro = '';

    if (!empty($_POST)) {
        echo 'a';
        var_dump($_POST);
        if (empty($_POST['matricula_usuario'])) {
            $usuarioDono = new usuarioView(['matricula_usuario' => $_POST['matricula_usuario']]);
    
            if ($usuarioDono->exists()) {
                if (!$usuarioDono->verifyPassword($_POST['senha_usuario'])) {
                    $mensagemErro = 'Senha incorreta.';
                }
            }
            else {
                $mensagemErro = 'O E-mail informado não existe no nosso banco de dados.';
            }
        }
        else {
            $usuarioDono = new usuarioView(['matricula_usuario' => $_POST['matricula_usuario']]);
        }
        
        if (empty($mensagemErro)) {
            $reserva = new reservaView(['id_reserva' => $_COOKIE['first']]);
            // var_dump($usuarioDono);
            if ($usuarioDono->get('grupo_usuario') != $reserva->get('id_grupo_reserva')) {
                $mensagemErro = 'Ainda não chegou a sua vez de confirmar sua reserva.';
            }
            else {
                $grupoView = new grupoView(['id_grupo' => $usuarioDono->get('grupo_usuario')]);
        
                if ($grupoView->get('dono_grupo') != $usuarioDono->get('id_usuario')) {
                    $mensagemErro = 'Somente o dono do grupo que você participa pode realizar reservas.';
                }
                else {
                    $reserva = new reservaView(['id_grupo_reserva' => $grupoView->get('id_grupo')]);

                    if (!$reserva->exists()) {
                        $mensagemErro = 'Você não possui uma reserva pro dia de hoje.';
                    }
                    else {
                        $fila = new filaView(['id_fila' => $reserva->get('id_fila_reserva')]);

                        if ($fila->get('data_fila') != date('Y-m-d')) {
                            $mensagemErro = 'Você não possui uma reserva pro dia de hoje.';
                        }
                        else {
                            $reservaCtrl = new reservaCtrl($reserva->getAtributtes());
                            $reservaCtrl->setData(['status_reserva' => 'concluida']);
                            $reservaCtrl->store();
                            setcookie("time", '', time() - 3600, "/");

                            foreach ($usuarioDono->getAll(['grupo_usuario' => $usuarioDono->get('grupo_usuario')]) as $user) {
                                $mail->setFrom('pipo.atendimento@gmail.com', 'Pipo - Atendimento');
                                $mail->addAddress($user->email_usuario, $user->nome_usuario);
                            
                                $mail->isHTML(true);
                                $mail->Subject = 'Sua reserva foi confirmada com sucesso! - Pipo';
                                $mail->Body    = '<h3>Olá, ' . $user->nome_usuario . '</h3>
                                <p>Temos uma ótima notícia! Sua <strong>reserva de almoço foi confirmada com sucesso</strong> no sistema <strong>Pipo</strong>.</p>
                                <p>Aqui estão os detalhes da confirmação:</p>
                                <ul>
                                <li><strong>Data do almoço:</strong> ' . date('d/m/Y') . '</li>
                                <li><strong>Confirmado por:</strong> ' . $usuarioDono->get('nome_usuario') . '</li>
                                <li><strong>Hora da confirmação:</strong> ' . date('H:i') . ' (GMT-5)</li>
                                </ul>
                                <p>Agora é só aproveitar o almoço com o seu prato favorito!</p>
                                <p>Atenciosamente,<br><strong>Equipe Pipo</strong></p>
                                <p><em>Esta é uma mensagem automática – por favor, não responda diretamente a este e-mail.</em></p>';
                            
                                $mail->send();
                            }
                    
                            $mensagemSucesso = 'Reserva confirmada com sucesso! Tenha um bom almoço!';
                        }
                    }
                }
            }
        }
    }
    else {
        var_dump($usuarioDono);
    }

    if (!empty($_GET['msg'])) {
        switch ($_GET['msg']) {
            case 'senhaincorreta':
                $mensagemErro = 'Senha incorreta.';
                break;
            case 'usuarioinexistente':
                $mensagemErro = 'O E-mail informado não existe no nosso banco de dados.';
                break;
            case 'naodono':
                $mensagemErro = 'Somente o dono do grupo que você participa pode realizar reservas.';
                break;
            case 'confirmada':
                $mensagemSucesso = 'Reserva confirmada com sucesso! Tenha um bom almoço!';
                break;
            case 'naoreserva':
                $mensagemErro = 'Você não possui uma reserva pro dia de hoje.';
                break;
            case 'furou':
                $mensagemErro = 'Ainda não chegou a sua vez de confirmar sua reserva.';
                break;
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Almoço - Sistema de Restaurante</title>
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
            text-align: center;
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

        .success-alert {
            display: none;
            padding: 16px 20px;
            background: #d1fae5;
            border: 2px solid #6ee7b7;
            border-radius: 8px;
            color: #065f46;
            margin-bottom: 24px;
            animation: slideDown 0.3s ease-out;
        }

        .error-alert {
            display: none;
            padding: 16px 20px;
            background: #fee2e2;
            border: 2px solid #fca5a5;
            border-radius: 8px;
            color: #991b1b;
            margin-bottom: 24px;
            animation: slideDown 0.3s ease-out;
        }

        .success-alert.show,
        .error-alert.show {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-icon {
            flex-shrink: 0;
        }

        .alert-content {
            flex: 1;
        }

        .alert-title {
            font-weight: 600;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .alert-text {
            font-size: 14px;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .timer-banner {
            background: #fef3c7;
            border: 2px solid #fde047;
            border-radius: 12px;
            padding: 20px 24px;
            margin-bottom: 32px;
            text-align: center;
        }

        .timer-title {
            font-size: 16px;
            font-weight: 600;
            color: #92400e;
            margin-bottom: 8px;
        }

        .timer-display {
            font-size: 32px;
            font-weight: 700;
            color: #d97706;
            font-family: 'Courier New', monospace;
        }

        .methods-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .method-card {
            background: white;
            border-radius: 12px;
            padding: 32px;
            border: 2px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .method-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #f1f5f9;
        }

        .method-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .method-icon.primary {
            background: #e0e7ff;
            color: #1351b4;
        }

        .method-icon.success {
            background: #d1fae5;
            color: #059669;
        }

        .method-title {
            font-size: 20px;
            font-weight: 600;
            color: #1a365d;
        }

        .method-description {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 15px;
            color: #2c3e50;
            transition: all 0.2s;
            background: white;
            font-family: inherit;
        }

        .form-input:focus {
            outline: none;
            border-color: #1351b4;
            box-shadow: 0 0 0 3px rgba(19, 81, 180, 0.1);
        }

        .btn {
            width: 100%;
            padding: 14px 24px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
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
            box-shadow: 0 4px 12px rgba(19, 81, 180, 0.3);
        }

        .btn-success {
            background: #059669;
            color: white;
        }

        .btn-success:hover {
            background: #047857;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
        }

        .info-section {
            background: white;
            border-radius: 12px;
            padding: 32px;
            border: 2px solid #e2e8f0;
        }

        .info-title {
            font-size: 20px;
            font-weight: 600;
            color: #1a365d;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f1f5f9;
        }

        .info-text {
            font-size: 15px;
            color: #475569;
            line-height: 1.7;
            margin-bottom: 12px;
        }

        @media (max-width: 768px) {
            .methods-grid {
                grid-template-columns: 1fr;
            }

            .container {
                padding: 24px 16px;
            }

            .page-title {
                font-size: 24px;
            }

            .method-card {
                padding: 24px;
            }

            .timer-display {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <h1 class="header-title">Sistema de Restaurante Institucional - Pipo</h1>
            <a href="hub.php" class="back-button">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"/>
                    <polyline points="12 19 5 12 12 5"/>
                </svg>
                Voltar ao Hub
            </a>
        </div>
    </header>

    <main class="container">
        <div class="page-header">
            <h2 class="page-title">Confirmar Almoço</h2>
            <p class="page-subtitle">Escolha um dos métodos abaixo para confirmar sua reserva</p>
        </div>

        <?php if ($mensagemSucesso): ?>
        <div class="success-alert show">
            <div class="alert-icon">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div class="alert-content">
                <div class="alert-title">Sucesso!</div>
                <div class="alert-text"><?php echo htmlspecialchars($mensagemSucesso); ?></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($mensagemErro): ?>
        <div class="error-alert show">
            <div class="alert-icon">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <div class="alert-content">
                <div class="alert-title">Erro</div>
                <div class="alert-text"><?php echo htmlspecialchars($mensagemErro); ?></div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isset($_COOKIE['time'])): ?>
        <div class="timer-banner">
            <div class="timer-title">⏱️ Tempo Restante para Confirmação</div>
            <div class="timer-display" id="timerDisplay">02:00</div>
        </div>
        <?php endif; ?>

        <div class="methods-grid">
            <div class="method-card">
                <div class="method-header">
                    <div class="method-icon primary">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2a10 10 0 0 1 10 10c0 4.42-2.87 8.17-6.84 9.5-.31.1-.66-.02-.84-.3l-2.32-3.63"/>
                            <path d="M12 2a10 10 0 0 0-10 10c0 4.42 2.87 8.17 6.84 9.5.31.1.66-.02.84-.3l2.32-3.63"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="method-title">Reconhecimento Facial</h3>
                    </div>
                </div>
                <p class="method-description">
                    Confirme sua reserva de forma rápida e segura usando reconhecimento facial biométrico. Basta posicionar seu rosto na câmera.
                </p>
                <a href="http://localhost/pipo/_Reconhecimento/frontend/reconhecimento.php" class="btn btn-primary">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                        <circle cx="12" cy="13" r="4"/>
                    </svg>
                    Confirmar com Biometria
                </a>
            </div>

            <div class="method-card">
                <div class="method-header">
                    <div class="method-icon success">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="method-title">Login com E-mail</h3>
                    </div>
                </div>
                <p class="method-description">
                    Confirme sua reserva informando seu e-mail e senha cadastrados no sistema.
                </p>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                    <div class="form-group">
                        <label class="form-label" for="email_usuario">E-mail</label>
                        <input 
                            type="email" 
                            id="email_usuario" 
                            name="email_usuario" 
                            class="form-input" 
                            placeholder="seu.email@instituicao.br"
                            required
                        >
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="senha_usuario">Senha</label>
                        <input 
                            type="password" 
                            id="senha_usuario" 
                            name="senha_usuario" 
                            class="form-input" 
                            placeholder="Digite sua senha"
                            required
                        >
                    </div>
                    <button type="submit" class="btn btn-success">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        Confirmar Almoço
                    </button>
                </form>
            </div>
        </div>

        <div class="info-section">
            <h3 class="info-title">Informações Importantes</h3>
            <p class="info-text">
                <strong>Atenção:</strong> Você tem apenas 2 minutos para confirmar sua reserva após ser chamado. Caso o tempo se esgote, sua reserva será automaticamente realocada para o final da fila.
            </p>
            <p class="info-text">
                <strong>Dica:</strong> Recomendamos o uso do reconhecimento facial para uma confirmação mais rápida e prática. Certifique-se de que seu rosto esteja cadastrado no sistema.
            </p>
            <p class="info-text">
                Apenas o proprietário do grupo pode confirmar a reserva. Todos os membros do grupo serão notificados por e-mail após a confirmação.
            </p>
        </div>
    </main>

    <script>
        <?php if (isset($_COOKIE['time'])): ?>
        // Timer de contagem regressiva
        const startTime = new Date('<?php echo $_COOKIE['time']; ?>').getTime();
        const timerDisplay = document.getElementById('timerDisplay');

        function updateTimer() {
            const now = new Date().getTime();
            const elapsed = now - startTime;
            const remaining = (2 * 60 * 1000) - elapsed; // 2 minutos em milissegundos

            if (remaining <= 0) {
                timerDisplay.textContent = '00:00';
                timerDisplay.style.color = '#dc2626';
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
                return;
            }

            const minutes = Math.floor(remaining / (60 * 1000));
            const seconds = Math.floor((remaining % (60 * 1000)) / 1000);

            timerDisplay.textContent = 
                String(minutes).padStart(2, '0') + ':' + 
                String(seconds).padStart(2, '0');

            // Alerta visual quando faltam 30 segundos
            if (remaining < 30000) {
                timerDisplay.style.color = '#dc2626';
            }

            requestAnimationFrame(updateTimer);
        }

        updateTimer();
        <?php endif; ?>

        // Auto-remover alertas após 5 segundos
        setTimeout(function() {
            const alerts = document.querySelectorAll('.success-alert.show, .error-alert.show');
            alerts.forEach(alert => alert.classList.remove('show'));
        }, 5000);

        <?php if (!empty($_GET['msg'])): ?>
        // Redirecionar após 5 segundos quando houver mensagem GET
        setTimeout(function() {
            window.location.href = 'confirmarAlmoco.php';
        }, 5000);
        <?php endif; ?>
    </script>
</body>
</html>