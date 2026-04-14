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
<html lang="PT-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pipo</title>
</head>
<body>
    <h1>Hub do Administrador</h1>

    <p>Olá, <strong><?php echo $admin->get('nome_admin')?></strong>.</p><br>

    <a href="listAdmin.php">Admins</a><br>
    <a href="listCardapio.php">Cardápios</a><br>
    <a href="listFila.php">Filas</a><br>
    <a href="listPrato.php">Pratos</a><br>
    <a href="listUsuario.php">Usuários</a><br>
    <a href="confirmarAlmoco.php">Área de confirmação de almoço</a><br>
    <br>

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
      <input type="text" name="valor" value="sair" style="display: none;">
      <input type="submit" value="Sair da conta">
    </form>

    <?php
      if (!empty($_POST)) {
        setcookie("user_type", 'guest', time() + 3600, "/");
        setcookie("user_id", '', time() + 3600, "/");
        setcookie("user_password", '', time() + 3600, "/");

        header("Location: hub.php?msg=sairdaconta");
        exit();
      }
    ?>
</body>
</html>