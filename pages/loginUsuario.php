<?php
    require_once __DIR__ . "/../views/usuarioView.php";
    require_once __DIR__ . "/../config/phpmail.php";

    $alertMessage = '';
    $alertType = '';

    if(!empty($_GET['msg']) && $_GET['msg']) {
        $alertMessage = 'Senha alterada com sucesso!';
        $alertType = 'success';
    }

    if (!empty($_POST)) {
        $usuario = new usuarioView(['email_usuario' => $_POST['email_usuario']]);
        
        if (!empty($usuario->getAtributtes())) {
            if ($usuario->verifyPassword($_POST['senha_usuario'])) {
                $time = time() + (10 * 365 * 24 * 60 * 60);
                
                setcookie("user_type", 'usuario', $time, "/");
                setcookie("user_id", $usuario->get('id_usuario'), $time, "/");
                setcookie("user_password", $_POST['senha_usuario'], $time, "/");
                
                header("Location: usuarioInterface.php");

                $mail->setFrom('pipo.atendimento@gmail.com', 'Pipo - Atendimento');
                $mail->addAddress($usuario->get('email_usuario'), $usuario->get('nome_usuario'));

                date_default_timezone_set('America/Rio_Branco');

                function detectOS($ua) {
                    $os = 'Desconhecido';
                    $oses = [
                        'Windows NT 10.0' => 'Windows 10',
                        'Windows NT 6.3'  => 'Windows 8.1',
                        'Windows NT 6.2'  => 'Windows 8',
                        'Windows NT 6.1'  => 'Windows 7',
                        'Windows NT 6.0'  => 'Windows Vista',
                        'Windows NT 5.1'  => 'Windows XP',
                        'Macintosh'       => 'macOS',
                        'Android'         => 'Android',
                        'iPhone'          => 'iOS',
                        'iPad'            => 'iPadOS',
                        'Linux'           => 'Linux'
                    ];
                    foreach ($oses as $pattern => $name) {
                        if (stripos($ua, $pattern) !== false) {
                            return $name;
                        }
                    }
                    return $os;
                }

                function detectBrowser($ua) {
                    $browsers = [
                        'Edge'      => 'Edge',
                        'Edg'       => 'Edge (Chromium)',
                        'OPR'       => 'Opera',
                        'Chrome'    => 'Google Chrome',
                        'CriOS'     => 'Google Chrome (iOS)',
                        'Firefox'   => 'Mozilla Firefox',
                        'Safari'    => 'Safari',
                        'MSIE'      => 'Internet Explorer',
                        'Trident'   => 'Internet Explorer'
                    ];
                    foreach ($browsers as $pattern => $name) {
                        if (stripos($ua, $pattern) !== false) {
                            return $name;
                        }
                    }
                    return 'Desconhecido';
                }

                $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
                $os = detectOS($ua);
                $browser = detectBrowser($ua);

                $ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconhecido';
                if ($ip === '::1') {
                    $ip = '127.0.0.1';
                }
        
                $mail->isHTML(true);
                $mail->Subject = 'Alerta de login realizado - Pipo';
                $mail->Body    = '<h3>Olá, ' . $usuario->get('nome_usuario') . '</h3><p>Detectamos um novo acesso à sua conta no sistema <strong>Pipo</strong>.</p>
                <p><strong>Data e hora:</strong> ' . date("d/m/Y H:i") . ' (GMT-5)</p>
                <p><strong>Endereço IP:</strong> ' . $ip . '</p>
                <p><strong>Dispositivo:</strong> ' . $os . ' - ' . $browser . '</p>
                <p>Se foi você quem realizou o login, não é necessário fazer nada.</p>
                <p>Se <strong>não reconhece este acesso</strong>, recomendamos que altere sua senha imediatamente para proteger sua conta:</p>
                <a href="http://localhost/pipo/pages/redefinirSenhaUsuario.php?id=' . $usuario->get('id_usuario') . '">Alterar minha senha</a>
                <p>Em caso de dúvidas ou problemas, entre em contato com o suporte da Pipo.</p>
                <p>Atenciosamente,<br><strong>Equipe Pipo</strong></p>
                <p><em>Esta é uma mensagem automática — por favor, não responda diretamente a este e-mail.</em></p>';
        
                $mail->send();
            }
            else {
                $alertMessage = 'Senha incorreta.';
                $alertType = 'error';
            }
        }
        else {
            $alertMessage = 'A(s) informação(ões) não existe(m) no nosso banco de dados.';
            $alertType = 'error';
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login de Usuário - Sistema de Restaurante</title>
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
            max-width: 480px;
            margin: 0 auto;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #1351b4;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 24px;
            transition: color 0.2s;
        }

        .back-button:hover {
            color: #0d3f8f;
        }

        .login-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.07), 0 1px 3px rgba(0,0,0,0.06);
            border: 2px solid #e2e8f0;
        }

        .card-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .card-icon {
            width: 64px;
            height: 64px;
            background: #e0e7ff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1351b4;
            margin: 0 auto 16px;
        }

        .card-title {
            font-size: 24px;
            font-weight: 700;
            color: #1a365d;
            margin-bottom: 8px;
        }

        .card-subtitle {
            font-size: 14px;
            color: #64748b;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
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

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 15px;
            color: #2c3e50;
            transition: border-color 0.2s;
            background: white;
        }

        .form-input:focus {
            outline: none;
            border-color: #1351b4;
        }

        .form-link {
            display: inline-block;
            font-size: 14px;
            color: #1351b4;
            text-decoration: none;
            margin-top: 8px;
            font-weight: 500;
        }

        .form-link:hover {
            color: #0d3f8f;
            text-decoration: underline;
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
        }

        .btn-primary {
            background: #1351b4;
            color: white;
            margin-top: 8px;
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

        .divider {
            text-align: center;
            margin: 24px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #e2e8f0;
        }

        .divider-text {
            background: white;
            padding: 0 12px;
            font-size: 13px;
            color: #64748b;
            position: relative;
            z-index: 1;
        }

        @media (max-width: 768px) {
            .login-card {
                padding: 32px 24px;
            }

            .card-title {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="hub.php" class="back-button">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Voltar ao Hub
        </a>

        <div class="login-card">
            <div class="card-header">
                <div class="card-icon">
                    <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <h1 class="card-title">Login de Usuário</h1>
                <p class="card-subtitle">Acesse sua conta no sistema</p>
            </div>

            <?php if ($alertMessage): ?>
            <div class="alert alert-<?php echo $alertType; ?>">
                <?php echo $alertMessage; ?>
            </div>
            <?php endif; ?>

            <form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post" novalidate>
                <div class="form-group">
                    <label class="form-label" for="email_usuario">E-mail institucional</label>
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
                        placeholder="••••••••"
                        required
                    >
                    <a href="forgetSenhaUsuario.php" class="form-link">Esqueci minha senha</a>
                </div>

                <button type="submit" class="btn btn-primary">
                    Realizar Login
                </button>
            </form>

            <div class="divider">
                <span class="divider-text">ou</span>
            </div>

            <button type="button" class="btn btn-secondary" onclick="window.location.href='hub.php'">
                Voltar ao Hub
            </button>
        </div>
    </div>
</body>
</html>