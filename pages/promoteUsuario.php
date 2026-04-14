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
    <?php
        require_once __DIR__ . "/../views/grupoView.php";
        require_once __DIR__ . "/../controllers/grupoCtrl.php";
        require_once __DIR__ . "/../views/usuarioView.php";
        require_once __DIR__ . "/../controllers/usuarioCtrl.php";
        require_once __DIR__ . "/../config/phpmail.php";

        date_default_timezone_set('America/Rio_Branco');

        $usuarioDono = new usuarioView(['id_usuario' => $_COOKIE['user_id']]);

        $usuario = new usuarioView(['id_usuario' => $_GET['id']]);
        $grupo = new grupoView(['id_grupo' => $usuario->get('grupo_usuario')]);

        $grupoCtrl = new grupoCtrl($grupo->getAtributtes());

        $grupoCtrl->setData(['dono_grupo' => $_GET['id']]);

        $grupoCtrl->store();

        $mail->setFrom('pipo.atendimento@gmail.com', 'Pipo - Atendimento');
        $mail->addAddress($usuario->get('email_usuario'), $usuario->get('nome_usuario'));
        $mail->isHTML(true);
        $mail->Subject = 'Promovido a dono do grupo - Pipo';
        $mail->Body    = '<h3>Olá, ' . $usuario->get('nome_usuario') . '</h3>
        <p>Temos uma novidade!</p>
        <p>O usuário <em>' . $usuarioDono->get('nome_usuario') . '</em> transferiu a propriedade do grupo de almoço para você.</p>
        <p>A partir de agora, você é o novo dono do grupo e será responsável por:</p>
        <p><strong>Realizar a reserva do almoço,</strong></p>
        <p><strong>Escolher o prato prioritário,</strong> e</p>
        <p><strong>Garantir que o grupo esteja confirmado dentro do prazo.</strong></p>
        <p>Para isso, acesse o site do Pipo, entre na sua conta e organize os detalhes da sua reserva.</p>
        <p>Em caso de dúvidas ou problemas, entre em contato com o suporte da Pipo.</p>
        <p>Atenciosamente,<br><strong>Equipe Pipo</strong></p>
        <p><em>Esta é uma mensagem automática — por favor, não responda diretamente a este e-mail.</em></p>';

        $mail->send();

        header("Location: grupoInterface.php?msg=promocaosucesso");
    ?>
</body>
</html>