<?php
// Processar formulário se houver POST
$mensagemSucesso = '';
$mensagemErro = '';

if (!empty($_POST)) {
    require_once __DIR__ . "/../config/phpmail.php";
    require_once __DIR__ . "/../views/usuarioView.php";

    $usuario = new usuarioView(['email_usuario' => $_POST['email_usuario']]);

    if (!empty($usuario->getAtributtes())) {
        $mail->setFrom('pipo.atendimento@gmail.com', 'Pipo - Atendimento');
        $mail->addAddress($usuario->get('email_usuario'), $usuario->get('nome_usuario'));

        $mail->isHTML(true);
        $mail->Subject = 'Recuperar acesso a sua conta - Pipo';
        $mail->Body    = '<h2>Recuperação de senha - Usuário</h2><h3>Olá, ' . $usuario->get('nome_usuario') . '</h3><p>Recebemos uma solicitação para redefinir a senha da sua conta na <strong>Pipo</strong>.</p>
<p>Para continuar o processo e criar uma nova senha, acesse o link abaixo:</p>
<a href="http://localhost/pipo/pages/redefinirSenhaUsuario.php?id=' . $usuario->get('id_usuario') . 
'">Clique Aqui</a><p>Se você não solicitou essa recuperação, pode ignorar este e-mail com segurança. Sua conta permanecerá protegida.</p>
<p>Atenciosamente,<br><strong>Equipe Pipo</strong></p>
<p><em>Esta é uma mensagem automática — por favor, não responda diretamente a este e-mail.</em></p>';

        $mail->send();

        $mensagemSucesso = 'E-mail enviado com sucesso! Verifique sua caixa de entrada e siga as instruções.';
    }
    else {
        $mensagemErro = 'Este e-mail não está cadastrado no sistema.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperar Senha - Sistema de Restaurante</title>
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
    }

    .header {
      text-align: center;
      margin-bottom: 32px;
    }

    .header-title {
      font-size: 28px;
      font-weight: 700;
      color: #1a365d;
      margin-bottom: 8px;
    }

    .header-subtitle {
      font-size: 15px;
      color: #64748b;
      font-weight: 400;
    }

    .recovery-card {
      background: white;
      border-radius: 12px;
      padding: 40px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      border: 2px solid #e2e8f0;
    }

    .card-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 24px;
      padding-bottom: 20px;
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

    .info-box {
      background: #f0f9ff;
      border: 1px solid #bae6fd;
      border-radius: 8px;
      padding: 16px;
      margin-bottom: 24px;
    }

    .info-box-text {
      font-size: 14px;
      color: #0c4a6e;
      line-height: 1.6;
    }

    .form-group {
      margin-bottom: 24px;
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
    }

    .form-input:focus {
      outline: none;
      border-color: #1351b4;
      box-shadow: 0 0 0 3px rgba(19, 81, 180, 0.1);
    }

    .form-hint {
      font-size: 13px;
      color: #64748b;
      margin-top: 6px;
      display: flex;
      align-items: flex-start;
      gap: 6px;
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
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .btn-primary {
      background: #1351b4;
      color: white;
      margin-bottom: 12px;
    }

    .btn-primary:hover {
      background: #0d3f8f;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(19,81,180,0.3);
    }

    .btn-secondary {
      background: #f1f5f9;
      color: #334155;
    }

    .btn-secondary:hover {
      background: #e2e8f0;
    }

    .success-message {
      display: none;
      padding: 16px 20px;
      background: #d1fae5;
      border: 2px solid #6ee7b7;
      border-radius: 8px;
      color: #065f46;
      margin-bottom: 24px;
      animation: slideDown 0.3s ease-out;
    }

    .success-message.show {
      display: flex;
      align-items: flex-start;
      gap: 12px;
    }

    .error-message {
      display: none;
      padding: 16px 20px;
      background: #fee2e2;
      border: 2px solid #fca5a5;
      border-radius: 8px;
      color: #991b1b;
      margin-bottom: 24px;
      animation: slideDown 0.3s ease-out;
    }

    .error-message.show {
      display: flex;
      align-items: flex-start;
      gap: 12px;
    }

    .message-icon {
      flex-shrink: 0;
      margin-top: 2px;
    }

    .message-content {
      flex: 1;
    }

    .message-title {
      font-weight: 600;
      margin-bottom: 4px;
      font-size: 14px;
    }

    .message-text {
      font-size: 14px;
      line-height: 1.5;
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

    .footer {
      text-align: center;
      margin-top: 24px;
      padding-top: 24px;
    }

    .footer-text {
      font-size: 14px;
      color: #64748b;
    }

    @media (max-width: 480px) {
      .recovery-card {
        padding: 28px 24px;
      }

      .header-title {
        font-size: 24px;
      }

      .card-title {
        font-size: 18px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <header class="header">
      <h1 class="header-title">Recuperar Senha</h1>
      <p class="header-subtitle">Sistema de Restaurante Institucional</p>
    </header>

    <div class="recovery-card">
      <!-- Mensagem de sucesso do PHP -->
      <div id="successMessage" class="success-message <?php echo !empty($mensagemSucesso) ? 'show' : ''; ?>">
        <div class="message-icon">
          <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
          </svg>
        </div>
        <div class="message-content">
          <div class="message-title">E-mail enviado com sucesso</div>
          <div class="message-text"><?php echo !empty($mensagemSucesso) ? $mensagemSucesso : 'Você recebeu um link para redefinição de senha em seu e-mail. Verifique sua caixa de entrada e siga as instruções.'; ?></div>
        </div>
      </div>

      <!-- Mensagem de erro do PHP -->
      <div id="errorMessage" class="error-message <?php echo !empty($mensagemErro) ? 'show' : ''; ?>">
        <div class="message-icon">
          <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
          </svg>
        </div>
        <div class="message-content">
          <div class="message-title">E-mail não encontrado</div>
          <div class="message-text"><?php echo $mensagemErro; ?></div>
        </div>
      </div>

      <div class="card-header">
        <div class="card-icon">
          <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
          </svg>
        </div>
        <h2 class="card-title">Esqueci minha senha</h2>
      </div>

      <div class="info-box">
        <p class="info-box-text">
          Informe o e-mail cadastrado no sistema. Enviaremos um link de recuperação para que você possa redefinir sua senha de acesso.
        </p>
      </div>

      <form id="recoveryForm" method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <div class="form-group">
          <label class="form-label" for="userEmail">E-mail institucional</label>
          <input 
            type="email" 
            id="userEmail" 
            name="email_usuario"
            class="form-input" 
            placeholder="seu.email@instituicao.br"
            required
          >
          <p class="form-hint">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"/>
              <line x1="12" y1="16" x2="12" y2="12"/>
              <line x1="12" y1="8" x2="12.01" y2="8"/>
            </svg>
            Digite o e-mail que você utiliza para acessar o sistema
          </p>
        </div>

        <button type="submit" class="btn btn-primary">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 2L11 13"/>
            <path d="M22 2l-7 20-4-9-9-4 20-7z"/>
          </svg>
          Enviar link de recuperação
        </button>

        <a href="hub.php" class="btn btn-secondary">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"/>
            <polyline points="12 19 5 12 12 5"/>
          </svg>
          Voltar para o login
        </a>
      </form>
    </div>

    <footer class="footer">
      <p class="footer-text">Sistema de Gestão de Restaurante Institucional © 2026</p>
    </footer>
  </div>

  <script>
    // Scroll automático para mensagens quando a página carrega
    window.addEventListener('DOMContentLoaded', function() {
      const successMessage = document.getElementById('successMessage');
      const errorMessage = document.getElementById('errorMessage');
      
      if (successMessage.classList.contains('show') || errorMessage.classList.contains('show')) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    });
  </script>
</body>
</html>