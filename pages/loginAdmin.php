<?php
require_once __DIR__ . "/../views/adminView.php";
require_once __DIR__ . "/../config/phpmail.php";
    require_once __DIR__ . "/../config/auth.php";

$loginError = '';
$loginSuccess = false;

// Verificar se há mensagem de sucesso de redefinição de senha
if (!empty($_GET['msg']) && $_GET['msg'] == 'senha_alterada') {
    $loginSuccess = true;
}

// Processar formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email']) && !empty($_POST['password'])) {
    
    $email = trim($_POST['email']);
    $senha = $_POST['password'];
    
    // Buscar admin no banco
    $admin = new adminView(['email_admin' => $email]);
    
    if (!empty($admin->getAtributtes())) {
        // Verificar senha
        if ($admin->verifyPassword($senha)) {
            // Configurar cookies de sessão
            loginUser('admin', (int) $admin->get('id_admin'));

            // Enviar email de alerta (opcional - comentado)
            
            $mail->setFrom('pipo.atendimento@gmail.com', 'Pipo - Atendimento');
            $mail->addAddress($admin->get('email_admin'), $admin->get('nome_admin'));
            
            date_default_timezone_set('America/Rio_Branco');
            
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'Desconhecido';
            if ($ip === '::1') {
                $ip = '127.0.0.1';
            }
            
            $mail->isHTML(true);
            $mail->Subject = 'Alerta de login realizado - Pipo';
            $mail->Body = '<h3>Olá, ' . $admin->get('nome_admin') . '</h3>...';
            $mail->send();
            
            
            // Redirecionar para interface admin
            header("Location: adminInterface.php");
            exit();
            
        } else {
            $loginError = 'Senha incorreta.';
        }
    } else {
        $loginError = 'Email não encontrado no sistema.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Sistema de Administração de Restaurante - Governo Federal">
  <meta name="theme-color" content="#1351B4">
  <title>Login - Sistema de Restaurante</title>
  
  <!-- CSS -->
  <link rel="stylesheet" href="css/reset.css">
  <link rel="stylesheet" href="css/variables.css">
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/footer.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/pages/login.css">
  
  <!-- Favicon (opcional) -->
  <link rel="icon" type="image/svg+xml" href="assets/favicon.svg">
</head>
<body>
  
  <!-- Container Principal de Login -->
  <div class="login-container">
    
    <!-- Card de Login -->
    <div class="login-card">
      
      <!-- Cabeçalho com Logo -->
      <div class="login-header">
        <div class="login-logo">
          <img
            src="./assets/icons/logo-pipo.svg"
            alt="Sistema Pipo"
            class="login-logo-icon"
          />
        </div>
        <h1 class="login-title">Pipo Sistema</h1>
        <p class="login-subtitle">Para Restaurantes Institucionais</p>
      </div>

      <!-- Formulário de Login -->
      <form id="loginForm" class="login-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" novalidate>
        
        <!-- Campo Email -->
        <div class="form-group">
          <label for="email" class="form-label form-label-required">Email</label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            class="form-input" 
            placeholder="seu.email@restaurante.gov.br"
            autocomplete="email"
            required
            aria-required="true"
            aria-describedby="emailError"
            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
          >
          <span id="emailError" class="form-error" role="alert"></span>
        </div>

        <!-- Campo Senha -->
        <div class="form-group">
          <label for="password" class="form-label form-label-required">Senha</label>
          <div class="password-input-wrapper">
            <input 
              type="password" 
              id="password" 
              name="password" 
              class="form-input" 
              placeholder="••••••••"
              autocomplete="current-password"
              required
              aria-required="true"
              aria-describedby="passwordError"
            >
            <button 
              type="button" 
              class="password-toggle" 
              id="togglePassword"
              aria-label="Mostrar senha"
            >
              <svg class="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
              <svg class="eye-off-icon hidden" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                <line x1="2" x2="22" y1="2" y2="22"/>
              </svg>
            </button>
          </div>
          <span id="passwordError" class="form-error" role="alert"></span>
        </div>

        <div class="forgot-password">
  <a href="forgetSenhaAdmin.php" class="forgot-password-link">
    Esqueci minha senha
  </a>
</div>


        <!-- Mensagem de Sucesso -->
        <?php if ($loginSuccess): ?>
        <div class="alert alert-success" role="alert">
          Senha alterada com sucesso! Faça login com sua nova senha.
        </div>
        <?php endif; ?>

        <!-- Mensagem de Erro do PHP -->
        <?php if (!empty($loginError)): ?>
        <div id="loginError" class="alert alert-error" role="alert">
          <?php echo htmlspecialchars($loginError); ?>
        </div>
        <?php endif; ?>

        <!-- Mensagem de Erro do JavaScript -->
        <div id="jsLoginError" class="alert alert-error hidden" role="alert"></div>

        <!-- Botão de Submit -->
        <button type="submit" class="btn btn-primary btn-block btn-lg" id="submitBtn">
          <span class="btn-text">Entrar no Sistema</span>
          <span class="spinner spinner-sm hidden" id="loginSpinner"></span>
        </button>

      </form>

      <!-- Footer do Card -->
      

    </div>

    <!-- Informações Institucionais -->
    <div class="login-info">
      <div class="login-info-item">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
          <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
        <span>Conexão segura</span>
      </div>
      <div class="login-info-item">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
          <path d="m9 12 2 2 4-4"/>
        </svg>
        <span>Sistema oficial</span>
      </div>
      <div class="login-info-item">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"/>
          <polyline points="12 6 12 12 16 14"/>
        </svg>
        <span>Disponível 24/7</span>
      </div>
    </div>

  </div>

  <footer class="system-footer">
    <div class="footer-container">
  
      <div class="footer-brand">
        <span class="footer-system-name">Sistema de Gestão | PIPO</span>
        <span class="footer-copy">© 2026 · Todos os direitos reservados</span>
      </div>
  
      <nav class="footer-nav">
        <a href="usuarioInterface.php" class="footer-link">Dashboard</a>
        <a href="usuarioInterface.php" class="footer-link">Pratos</a>
        <a href="usuarioInterface.php" class="footer-link">Cardápios</a>
        <a href="usuarioInterface.php" class="footer-link">Filas</a>
        <a href="usuarioInterface.php" class="footer-link">Acessos</a>
        <a href="usuarioInterface.php" class="footer-link">Perfil</a>
      </nav>
  
      <div class="footer-meta">
        <span class="footer-version">Versão 1.0.0</span>
      </div>
  
    </div>
  </footer>
  
  <!-- JavaScript -->
  <script src="js/pages/login.js"></script>

</body>
</html> 