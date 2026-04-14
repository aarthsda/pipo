<?php
  if (empty($_COOKIE) || $_COOKIE['user_type'] == 'guest') {
    header("Location: hub.php?msg=acessoproibido");
    exit();
  }

  if ($_COOKIE['user_type'] == 'admin') {
    $id = $_GET['id'];
    $submitValue = 'Voltar à lista de Usuários';
    require_once __DIR__ . "/../views/adminView.php";
  
    $admin = new adminView(['id_admin' => $_COOKIE['user_id']]);
  
    if (!$admin->verifyPassword($_COOKIE['user_password'])) {
      setcookie("user_type", 'guest', time() + 3600, "/");
      setcookie("user_id", '', time() + 3600, "/");
      setcookie("user_password", '', time() + 3600, "/");
  
      header("Location: hub.php");
      exit();
    }
  }
  else if ($_COOKIE['user_type'] == 'usuario') {
    $id = $_COOKIE['user_id'];
    $submitValue = 'Voltar ao Hub';
    require_once __DIR__ . "/../views/usuarioView.php";
  
    $usuario = new usuarioView(['id_usuario' => $_COOKIE['user_id']]);
  
    if (!$usuario->verifyPassword($_COOKIE['user_password'])) {
      setcookie("user_type", 'guest', time() + 3600, "/");
      setcookie("user_id", '', time() + 3600, "/");
      setcookie("user_password", '', time() + 3600, "/");
  
      header("Location: hub.php");
      exit();
    }
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
    <h1>Cadastro de Usuário</h1>

    <!-- Formulário envia via POST para /admin/create (ajuste action conforme sua rota) -->
    <form action="ctrlHub.php?class=usuario&action=alter" method="post" novalidate enctype="multipart/form-data">

      <div>
        <input type="text" id="id_usuario" name="id_usuario" style="display: none;" value="<?php echo $id ?>">
      </div>

      <?php
      require_once __DIR__ . "/../views/usuarioView.php";
        
        $view = new usuarioView(['id_usuario' => $id]);
        $attr = $view->getAtributtes();

      echo "<div"; if ($_COOKIE['user_type'] == 'usuario') {echo " style='display: none;' ";} echo ">
        <label for='nome_usuario'>Nome:</label><br>
        <input type='text' id='nome_usuario' name='nome_usuario' value='" . $attr['nome_usuario'] . "' required><br><br>
      </div>

      <div>
        <label for='email_usuario'>E-mail:</label><br>
        <input type='email' id='email_usuario' name='email_usuario' value='" . $attr['email_usuario'] . "' required><br><br>
      </div>

      <div>
        <label for='senha_usuario'>Senha:</label><br>
        <input type='password' id='senha_usuario' name='senha_usuario' value='" . $attr['senha_usuario'] . "' required><br><br>
      </div>

      <div"; if ($_COOKIE['user_type'] == 'usuario') {echo " style='display: none;' ";} echo ">
        <label for='matricula_usuario'>Matrícula:</label><br>
        <input type='text' id='matricula_usuario' name='matricula_usuario' value='" . $attr['matricula_usuario'] . "'><br><br>
      </div>

      <div"; if ($_COOKIE['user_type'] == 'usuario') {echo " style='display: none;' ";} echo ">
        <label for='categoria_usuario'>Categoria:</label><br>
        <select id='categoria_usuario' name='categoria_usuario'>";
          if ($attr['categoria_usuario'] == 'comum') {
            echo "<option value='comum' selected>Comum</option>
          <option value='prioritario'>Prioritário</option>";
          }
          else {
            echo "<option value='comum'>Comum</option>
          <option value='prioritario' selected>Prioritário</option>";
          }
        echo "</select><br><br>
      </div>";
      ?>

      <br />

      <div>
        <button type="submit">Alterar Usuário</button>
      </div>
    </form>

    <!-- Mensagem de ajuda: o servidor deve tratar hashing da senha e validação adicional -->
    <p>
      Observação: trate a senha de forma segura no servidor (hash/salt), valide todos os dados
      no backend e proteja a rota contra CSRF antes de usar em produção.
    </p>
  </main>
  <br><br><button onclick="window.location.replace('listUsuario.php')"><?php echo $submitValue; ?></button>

</body>
</html>