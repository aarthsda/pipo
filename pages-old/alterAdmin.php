<?php
  if (empty($_COOKIE) || $_COOKIE['user_type'] != 'admin') {
    header("Location: hub.php?msg=acessoproibido");
    exit();
  }

  require_once __DIR__ . "/../views/adminView.php";

  $admin = new adminView(['id_admin' => $_COOKIE['user_id']]);

  if (!$admin->verifyPassword($_COOKIE['user_password'])) {
    setcookie("user_type", 'guest', time() + 3600, "/");
    setcookie("user_id", '', time() + 3600, "/");
    setcookie("user_password", '', time() + 3600, "/");

    header("Location: hub.php");
    exit();
  }
?>

<!DOCTYPE html>
<html lang="ptBR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pipo</title>
</head>
<body>
    <main>
    <h1>Alterar Admin</h1>

    <form action="ctrlHub.php?class=admin&action=alter" method="post" novalidate>
      <div>
        <input
          id="id_admin"
          name="id_admin"
          type="text"
          style="display: none;"
          value="<?php echo $_GET['id']?>"
        />
      </div>

      <?php
        require_once __DIR__ . "/../views/adminView.php";
        
        $view = new adminView(['id_admin' => $_GET['id']]);
        $attr = $view->getAtributtes();

        echo "<div>
          <label for='codigo_admin'>Código (codigo_admin)</label><br />
          <input
            id='codigo_admin'
            name='codigo_admin'
            type='text'
            inputmode='numeric'
            pattern='\d+'
            placeholder='Somente números'
            required
            aria-required='true'
            value='" . $attr['codigo_admin'] . "'
          />
          <small>Somente dígitos (ex.: 12345)</small>
        </div>

        <br />

        <div>
          <label for='nome_admin'>Nome (nome_admin)</label><br />
          <input
            id='nome_admin'
            name='nome_admin'
            type='text'
            maxlength='100'
            placeholder='Nome completo'
            required
            aria-required='true'
            value='" . $attr['nome_admin'] . "'
          />
        </div>

        <br />

        <div>
          <label for='senha_admin'>Nova Senha (senha_admin)</label><br />
          <input
            id='senha_admin'
            name='senha_admin'
            type='password'
            minlength='6'
            maxlength='360'
            placeholder='Digite uma senha segura'
            required
            aria-required='true'
            value='" . $attr['senha_admin'] . "'
          />
          <small>6 caracteres ou mais.</small>
        </div>

        <br />

        <div>
          <label for='email_admin'>E-mail (email_admin)</label><br />
          <input
            id='email_admin'
            name='email_admin'
            type='email'
            maxlength='150'
            placeholder='seu@exemplo.com'
            required
            aria-required='true'
            value='" . $attr['email_admin'] . "'
          />
        </div>

        <br />

        <div>
          <button type='submit'>Alterar Admin</button>
          <button type='reset'>Limpar</button>
        </div>";
      ?>
    </form>

    <p>
      Observação: trate a senha de forma segura no servidor (hash/salt), valide todos os dados
      no backend e proteja a rota contra CSRF antes de usar em produção.
    </p>
  </main>
  <br><br><button onclick="window.location.replace('listAdmin.php')">Voltar à lista de Admins</button>

</body>
</html>