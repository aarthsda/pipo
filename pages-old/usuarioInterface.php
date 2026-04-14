<?php


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
?>

<!DOCTYPE html>
<html lang="PT-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pipo</title>
</head>
<body>
    <h1>Hub do Usuário</h1>

    <p>Olá, <strong><?php echo $usuario->get('nome_usuario')?></strong>.</p><br>

    <a href="http://localhost/pipo/_Reconhecimento/frontend/index.php">Cadastrar reconhecimento facial</a><br>
    <a href="alterUsuario.php">Alterar perfil</a><br>
    <a href="filaInterface.php">Organizar reserva</a><br>
    <a href="grupoInterface.php">Organizar grupo</a><br>
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

      if (!empty($_GET['msg'])) {
        switch ($_GET['msg']) {
          case 'conviteaceitado':
              echo "<br><strong style='color: green;'>Convite aceito! Agora você faz parte de um grupo!</strong>";
            break;
          case 'conviteexpirado':
              echo "<br><strong style='color: red;'>Convite expirado! Não será mais possível aceitar esse convite.</strong>";
            break;
          case 'grupocheio':
              echo "<br><strong style='color: red;'>O grupo ja esta cheio! Não será possível convidar outro usuário.</strong>";
            break;
          case 'naoedono':
            echo "<br><strong style='color: red;'>Impossível convidar usuários! Você não é o dono do grupo.</strong>";
            break;
          case 'reconhecimentocadastrosucesso':
            echo "<br><strong style='color: green;'>Biometria cadastrada com sucesso! Agora você pode confirmar a reserva usando apenas seu rosto.</strong>";

            require_once __DIR__ . "/../config/phpmail.php";

            $mail->setFrom('pipo.atendimento@gmail.com', 'Pipo - Atendimento');
            $mail->addAddress($usuario->get('email_usuario'), $usuario->get('nome_usuario'));
    
            $mail->isHTML(true);
            $mail->Subject = 'Biometria facial cadastrada com sucesso! - Pipo';
            $mail->Body    = '<h3>Olá, ' . $usuario->get('nome_usuario') . '</h3><p>Temos uma ótima notícia!</p>

<p>Sua <strong>biometria facial</strong> foi cadastrada com sucesso no sistema <strong>Pipo</strong>.</p>

<p>A partir de agora, você poderá <strong>confirmar suas reservas e almoços de forma muito mais rápida e prática</strong>, utilizando apenas o reconhecimento facial — sem precisar digitar senha ou realizar confirmações manuais.</p>

<p>É simples, seguro e pensado para tornar sua experiência no restaurante ainda melhor!</p>

<p>Você pode acessar sua conta no site do Pipo para visualizar ou atualizar suas informações biométricas sempre que quiser.</p>
<p>Atenciosamente,<br><strong>Equipe Pipo</strong></p>
<p><em>Esta é uma mensagem automática — por favor, não responda diretamente a este e-mail.</em></p>';
    
            $mail->send();

            break;
        }
        header( "refresh:5;url=usuarioInterface.php" );
      }
    ?>
</body>
</html>